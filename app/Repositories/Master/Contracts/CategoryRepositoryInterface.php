<?php

namespace App\Repositories\Master\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function all(): Collection;
    public function findOrFail(int $id): Category;
    public function create(array $data): Category;
    public function update(Category $category, array $data): Category;
    public function delete(Category $category): void;
    public function getByServiceId(int $serviceId): Collection;
}
