<?php
namespace App\Interfaces\Api\V1;

use App\Http\Requests\Api\V1\CategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\UniversalDTO;

interface CategoryInterface {
    public function list(): UniversalDTO;
    public function create(CategoryRequest $request): UniversalDTO;
    public function read(CategoryRequest $request): UniversalDTO;
    public function delete(CategoryRequest $request): UniversalDTO;
    public function update(CategoryRequest $request): UniversalDTO;
}
