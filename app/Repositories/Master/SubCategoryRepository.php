<?php

namespace App\Repositories\Master;

use App\Models\SubCategory;
use App\Repositories\Master\Contracts\SubCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    public function all(): Collection
    {
        return SubCategory::with('category.service')->get();
    }

    public function findOrFail(int $id): SubCategory
    {
        return SubCategory::findOrFail($id);
    }

    public function create(array $data): SubCategory
    {
        return SubCategory::create($data);
    }

    public function update(SubCategory $subcategory, array $data): SubCategory
    {
        $subcategory->update($data);
        return $subcategory;
    }

    public function delete(SubCategory $subcategory): void
    {
        $subcategory->delete();
    }

    public function getByCategoryId(int $categoryId): Collection
    {
        return SubCategory::with('category.service')->where('category_id', $categoryId)->where('status', true)->get();
    }
}
