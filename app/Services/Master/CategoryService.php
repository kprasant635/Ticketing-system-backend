<?php

namespace App\Services\Master;

use App\DTO\Master\CategoryDTO;
use App\Models\Category;
use App\Repositories\Master\Contracts\CategoryRepositoryInterface;
use App\Repositories\Master\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $repository
    ) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): Category
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CategoryDTO $dto): Category
    {
        return $this->repository->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'service_id' => $dto->service_id,
            'status' => $dto->status === 'active' ? true : false,
        ]);
    }

    public function update(int $id, CategoryDTO $dto): Category
    {
        $category = $this->repository->findOrFail($id);

        return $this->repository->update($category, [
            'name' => $dto->name,
            'description' => $dto->description,
        ]);
    }

    public function delete(int $id): void
    {
        $category = $this->repository->findOrFail($id);

        $this->repository->delete($category);
    }

    public function listByService(int $serviceId): Collection
    {
        return $this->repository->getByServiceId($serviceId);
    }
}
