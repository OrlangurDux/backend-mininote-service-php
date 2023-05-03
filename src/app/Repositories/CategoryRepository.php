<?php
namespace App\Repositories;

use App\Interfaces\Repository\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CategoryRepository implements CategoryRepositoryInterface {
    /**
     * @return Collection
     */
    public function getAllCategory(): Collection{
        return Category::all();
    }

    /**
     * @return Collection
     */
    public function getAllUserCategory(): Collection{
        return Category::whereUserId(Auth::id())->orderBy('sort')->get();
    }

    /**
     * @param int $id
     * @param bool $checkPermission
     * @return Category
     */
    public function getById(int $id, bool $checkPermission = true): Category{
        if($checkPermission){
            $collection = Category::whereId($id)->whereUserId(Auth::id())->first();
        }else{
            $collection = Category::whereId($id)->first();
        }
        return $collection;
    }
}
