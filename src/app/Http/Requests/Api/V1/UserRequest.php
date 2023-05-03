<?php

namespace App\Http\Requests\Api\V1;

use Anik\Form\FormRequest;
use App\Rules\RestoreTokenRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

//use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array {
        $as = Arr::get($this->route()[1], 'as');
        return match($as){
            'register' => [
                'email'     => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed'
            ],
            'login' => [
                'email' => 'required|email|exists:users,email',
                'password' => 'required'
            ],
            'forgot', 'check' => [
                'email' => 'email|exists:users,email',
                'restore_token' => [new RestoreTokenRule]
            ],
            'password.update' => [
                'password' => 'required|min:6|confirmed'
            ],
            default => []
        };
    }

    protected function errorResponse(): ?JsonResponse {
        return response()->json([
            'success' => false,
            'status' => $this->statusCode(),
            'error' => [
                    'code' => 10,
                    'message' => $this->validator->errors()->messages(),
                ],
            'data' => []
        ], $this->statusCode());
    }
}
