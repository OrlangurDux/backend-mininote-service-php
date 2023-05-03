<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\UniversalDTO;
use App\Interfaces\Api\V1\CategoryInterface;
use App\Interfaces\Repository\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class CategoryController extends Controller implements CategoryInterface {
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository){
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Categories list",
     *     operationId="categories.list",
     *     tags={"Categories"},
     *     description="View categories information",
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
    public function list(): UniversalDTO{
        $repo = $this->categoryRepository->getAllUserCategory();
        return (new UniversalDTO(['total' => $repo->count(), 'items' => $repo->toArray()]))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     summary="Create category",
     *     operationId="categories.create",
     *     tags={"Categories"},
     *     description="Create category note",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name category",
     *                 ),
     *                 @OA\Property(
     *                     property="parent_id",
     *                     type="string",
     *                     description="Parent id child category",
     *                 ),
     *                 @OA\Property(
     *                     property="sort",
     *                     type="number",
     *                     description="Sort category",
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
    public function create(CategoryRequest $request): UniversalDTO{
        $category = new Category();
        $category->name = $request->name;
        if($request->has('sort') && $request->sort != '') {
            $category->sort = $request->sort;
        }
        if($request->has('parent_id' && $request->parent_id != '')){
            $category->parent_id = $request->parent_id;
        }
        $category->user_id = Auth::id();
        if($category->save()){
            $message = __('category.success_created');
        }else{
            $message = __('category.error_create_category');
        }
        return (new UniversalDTO([$message]))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     summary="Read category",
     *     operationId="categories.read",
     *     tags={"Categories"},
     *     description="Read category note",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category id",
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
    public function read(CategoryRequest $request): UniversalDTO{
        return (new UniversalDTO($this->categoryRepository->getById($request->id)))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     summary="Delete category",
     *     operationId="categories.delete",
     *     tags={"Categories"},
     *     description="Delete category note",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category id",
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
    public function delete(CategoryRequest $request): UniversalDTO{
        $category = Category::whereId($request->id)->whereUserId(Auth::id());
        if($category->count() > 0){
            if($category->delete()){
                $message = __('category.success_deleted');
            }else{
                $message = __('category.error_remove_category', ['id' => $request->id]);
            }
        }else{
            $message = __('category.error_incorrect_id', ['id' => $request->id]);
        }
        return (new UniversalDTO([$message]))->additional(['success' => true, 'status' => 200]);
    }

    /**
     * @OA\Put(
     *     path="/categories/{id}",
     *     summary="Update category",
     *     operationId="categories.update",
     *     tags={"Categories"},
     *     description="Update category note",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category id",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name category",
     *                 ),
     *                 @OA\Property(
     *                     property="parent_id",
     *                     type="string",
     *                     description="Parent id child category",
     *                 ),
     *                 @OA\Property(
     *                     property="sort",
     *                     type="number",
     *                     description="Sort category",
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
    public function update(CategoryRequest $request): UniversalDTO{
        $category = Category::whereId($request->id)->whereUserId(Auth::id())->first();
        if($category != null){
            $category->name = $request->name;
            if($request->has('sort') && $request->sort != '') {
                $category->sort = $request->sort;
            }
            if($request->has('parent_id' && $request->parent_id != '')){
                $category->parent_id = $request->parent_id;
            }
            if($category->save()){
                $message = __('category.success_updated');
            } else {
                $message = __('category.error_update_category');
            }
        }else{
            $message = __('category.error_incorrect_id', ['id' => $request->id]);
        }
        return (new UniversalDTO([$message]))->additional(['success' => true, 'status' => 200]);
    }
}
