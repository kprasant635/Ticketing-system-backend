<?php

namespace App\Repositories\Master\Contracts;

use App\Models\Priority;
use Illuminate\Database\Eloquent\Collection;

interface PriorityRepositoryInterface
{
    public function all(): Collection;
    public function findOrFail(int $id): Priority;
    public function create(array $data): Priority;
    public function update(Priority $priority, array $data): Priority;
    public function delete(Priority $priority): void;
}
