<?php

namespace App\Repositories\Master\Contracts;

use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Collection;

interface SubCategoryRepositoryInterface
{
    public function all(): Collection;
    public function findOrFail(int $id): SubCategory;
    public function create(array $data): SubCategory;
    public function update(SubCategory $subcategory, array $data): SubCategory;
    public function delete(SubCategory $subcategory): void;
    public function getByCategoryId(int $categoryId): Collection;
}
