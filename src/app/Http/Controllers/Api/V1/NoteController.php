<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\NoteRequest;
use App\Http\Resources\UniversalDTO;
use App\Interfaces\Api\V1\NoteInterface;
use App\Interfaces\Repository\NoteRepositoryInterface;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class NoteController extends Controller implements NoteInterface{

    private NoteRepositoryInterface $noteRepository;

    public function __construct(NoteRepositoryInterface $noteRepository){
        $this->noteRepository = $noteRepository;
    }

    //
    /**
     * @OA\Get(
     *     path="/notes",
     *     summary="Notes list",
     *     operationId="notes.list",
     *     tags={"Notes"},
     *     description="View notes information",
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         required=false,
     *         description="Note offset",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Note limit",
     *         @OA\Schema(
     *             type="number"
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
    public function list(NoteRequest $request): UniversalDTO{
        $repoAll = $this->noteRepository->getAllNotes();
        if($request->has('offset')){
            $offset = $request->offset;
            $limit = $request->has('limit') ? $request->limit : 5;
            $repo = $this->noteRepository->getNotes($offset, $limit);
        }else{
            $repo = $repoAll;
        }
        return (new UniversalDTO(['total' => $repoAll->count(), 'items' => $repo->toArray()]))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Get(
     *     path="/notes/{id}",
     *     summary="Read note",
     *     operationId="notes.read",
     *     tags={"Notes"},
     *     description="Read note",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Note id",
     *         @OA\Schema(
     *             type="string"
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
    public function read(NoteRequest $request): UniversalDTO{
        return (new UniversalDTO($this->noteRepository->getById($request->id)))->additional(['success'=>true, 'status'=>200]);
    }

    /**
     * @OA\Post(
     *     path="/notes",
     *     summary="Create note",
     *     operationId="notes.create",
     *     tags={"Notes"},
     *     description="Create note",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"title", "note"},
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="Title note",
     *                 ),
     *                 @OA\Property(
     *                     property="note",
     *                     type="string",
     *                     description="Note",
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     enum={"draft","public","archive"},
     *                     default="draft",
     *                     description="Status note",
     *                 ),
     *                 @OA\Property(
     *                     property="category_id",
     *                     type="number",
     *                     description="Category ID",
     *                 ),
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
    public function create(NoteRequest $request): UniversalDTO{
        $note = new Note();
        $note->title = $request->title;
        $note->note = $request->note;
        $note->status = $request->status;

        $note->user_id = Auth::id();
        if($request->has('category_id')){
            $note->category_id = $request->category_id;
        }
        $note->created_at = time();
        $note->updated_at = time();

        if($note->save()){
            $message = __('note.success_created');
        }else{
            $message = __('note.error_create_note');
        }
        return (new UniversalDTO([$message]))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Put(
     *     path="/notes/{id}",
     *     summary="Update note",
     *     operationId="notes.update",
     *     tags={"Notes"},
     *     description="Update note",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Note id",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"title", "note"},
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="Title note",
     *                 ),
     *                 @OA\Property(
     *                     property="note",
     *                     type="string",
     *                     description="Note",
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     enum={"draft","public","archive"},
     *                     default="draft",
     *                     description="Status note",
     *                 ),
     *                 @OA\Property(
     *                     property="category_id",
     *                     type="number",
     *                     description="Category ID",
     *                 ),
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
    public function update(NoteRequest $request): UniversalDTO{
        $note = Note::whereId($request->id)->whereUserId(Auth::id())->first();
        if($note != null){
            $note->title = $request->title;
            $note->note = $request->note;
            $note->updated_at = time();
            $note->status = $request->status;
            if($request->has('category_id' && $request->category_id != '')){
                $note->category_id = $request->category_id;
            }
            if($note->save()){
                $message = __('note.success_updated');
            } else {
                $message = __('note.error_update_category');
            }
        }else{
            $message = __('note.error_incorrect_id', ['id' => $request->id]);
        }
        return (new UniversalDTO([$message]))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Delete(
     *     path="/notes/{id}",
     *     summary="Delete note",
     *     operationId="notes.delete",
     *     tags={"Notes"},
     *     description="Delete note",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Note id",
     *         @OA\Schema(
     *             type="string"
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
    public function delete(NoteRequest $request): UniversalDTO{
        $note = Note::whereId($request->id)->whereUserId(Auth::id());
        if($note->count() > 0){
            if($note->delete()){
                $message = __('note.success_deleted');
            }else{
                $message = __('note.error_delete_note', ['id' => $request->id]);
            }
        }else{
            $message = __('note.error_incorrect_id', ['id' => $request->id]);
        }
        return (new UniversalDTO([$message]))->additional(['success' => true, 'status' => 200]);
    }
}
