<?php
namespace App\Interfaces\Repository;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface {
    public function getAllCategory(): Collection;
    public function getAllUserCategory(): Collection;
    public function getById(int $id, bool $checkPermission): Category;
}
