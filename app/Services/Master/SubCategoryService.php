<?php

namespace App\Services\Master;

use App\DTO\Master\SubCategoryDTO;
use App\Models\SubCategory;
use App\Repositories\Master\Contracts\SubCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubCategoryService
{
    public function __construct(
        protected SubCategoryRepositoryInterface $repository
    ) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function listByCategory(int $categoryId): Collection
    {
        return $this->repository->getByCategoryId($categoryId);
    }

    public function find(int $id): SubCategory
    {
        return $this->repository->findOrFail($id);
    }

    public function create(SubCategoryDTO $dto): SubCategory
    {
        return $this->repository->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'category_id' => decrypt_id($dto->categoryId),
            'status' => $dto->status == 1 ? true : false,
        ]);
    }

    public function update(int $id, SubCategoryDTO $dto): SubCategory
    {
        $subcategory = $this->repository->findOrFail($id);

        return $this->repository->update($subcategory, [
            'name' => $dto->name,
            'description' => $dto->description,
            'status' => $dto->status == 1 ? true : false,
        ]);
    }

    public function delete(int $id): void
    {
        $subcategory = $this->repository->findOrFail($id);
        $this->repository->delete($subcategory);
    }
}
