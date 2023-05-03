<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserRequest;
use App\Http\Resources\UniversalDTO;
use App\Models\User;
use App\Traits\DecodeContentTrait;
use Carbon\Carbon;
use App\Interfaces\Api\V1\UserInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;
use Illuminate\Support\Str;

class UserController extends Controller implements UserInterface{
    use DecodeContentTrait;
    //
    /**
     * @OA\Post(
     *     path="/users/register",
     *     summary="Register user account",
     *     operationId="register",
     *     tags={"User"},
     *     description="Registration user by email and password",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email", "password", "password_confirmation"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Password",
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     description="Password confirmation",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UniversalDTO")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function register(UserRequest $request): UniversalDTO{
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->created_at = time();
        $user->updated_at = time();
        $user->save();
        return (new UniversalDTO($user))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Post(
     *     path="/users/login",
     *     summary="Login user account",
     *     operationId="login",
     *     tags={"User"},
     *     description="Authorization user account by email and password",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Password",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UniversalDTO")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function login(UserRequest $request): UniversalDTO{
        $credentials = $request->only(['email','password']);
        if(!$token = Auth::attempt($credentials)){
            return (new UniversalDTO([]))->additional([
                'success' => false,
                'status' => 401,
                'error' => [
                    'code' => 20,
                    'message' => 'Unauthorized'
                ]
                ]);
        }
        User::whereId(Auth::id())->update(['authorized_at' => Carbon::now()]);
        return (new UniversalDTO([]))->additional([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }

    /**
     * @OA\Post(
     *     path="/users/forgot",
     *     summary="Forgot user password",
     *     operationId="forgot",
     *     tags={"User"},
     *     description="Recovery user password by email",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Email",
     *                 ),
     *                 @OA\Property(
     *                     property="restore_token",
     *                     type="string",
     *                     description="Restore Token",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="New password",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UniversalDTO")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function forgot(UserRequest $request): UniversalDTO{
        if(
            ($request->get("email") != "" && $request->get("restore_token") != "")||
            ($request->get("email") != "" && $request->get("restore_token") != "" && $request->get("password") != "")
        ){
            $restoreToken = $request->restore_token;
            $password = $request->password;
            if($password == null){
                throw new \RuntimeException(__('user.error_password_not_empty'), 422);
            }
            User::whereRestoreToken($restoreToken)->update(['password' => Hash::make($password), 'restore_token' => '']);
            $message = 'Password update';
        }elseif($request->has("email") && $request->email != '') {
            $restoreToken = Str::random(32);
            User::whereEmail($request->email)->update(['restore_token' => $restoreToken]);
            $data['restoreToken'] = $restoreToken;
            Mail::send('emails.restore', $data, function($message) use ($request){
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->to($request->email);
            });
            $message = __('user.message_restore_token_send');
        }else{
            throw new \RuntimeException(__('user.error_fields_not_empty'), 422);
        }
        return (new UniversalDTO([$message]))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Get(
     *     path="/users/profile",
     *     summary="User profile",
     *     operationId="profile.read",
     *     tags={"User"},
     *     description="View user profile information",
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UniversalDTO")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function readProfile(UserRequest $request): UniversalDTO{
        return (new UniversalDTO(auth()->user()))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Put(
     *     path="/users/profile",
     *     summary="Update user account",
     *     operationId="profile.update",
     *     tags={"User"},
     *     description="Update user account",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name",
     *                 ),
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="file",
     *                     description="User avatar",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UniversalDTO")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function updateProfile(UserRequest $request): UniversalDTO{
        $user = User::find(auth()->id());
        if($request->hasFile("avatar")){
            $file = $request->file("avatar")[0];
            $fileName = $file->getClientOriginalName();
            $pathAvatar = auth()->id().'/'.$fileName;
            Storage::disk('avatar')->put($pathAvatar, file_get_contents($file));
            $user->avatar = $pathAvatar;
        }
        if($request->has("name")){
            $user->name = $request->name;
        }
        $user->save();
        return (new UniversalDTO([__('user.message_user_updated')]))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Delete(
     *     path="/users/profile",
     *     summary="Delete user account",
     *     operationId="profile.delete",
     *     tags={"User"},
     *     description="Delete user account",
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UniversalDTO")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function deleteProfile(UserRequest $request): UniversalDTO{
        return (new UniversalDTO([__('user.message_user_deleted')]))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Post(
     *     path="/users/password",
     *     summary="Update password user account",
     *     operationId="password.update",
     *     tags={"User"},
     *     description="Update password user account",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"password", "password_confirmation"},
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Password",
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     description="Password confirmation",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UniversalDTO")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function updatePassword(UserRequest $request): UniversalDTO{
        $user = User::find(auth()->id());
        $user->password = Hash::make($request->password);
        $user->save();
        return (new UniversalDTO([__('user.message_password_updated')]))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Post(
     *     path="/users/check",
     *     summary="Check exists user account",
     *     operationId="check",
     *     tags={"User"},
     *     description="Check exists user account by email",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Email",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UniversalDTO")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function checkByEmail(UserRequest $request): UniversalDTO{
        return (new UniversalDTO([__('user.message_email_check', ['email' => $request->email])]))->additional(['success' => true, 'status' => 200]);
    }
}
