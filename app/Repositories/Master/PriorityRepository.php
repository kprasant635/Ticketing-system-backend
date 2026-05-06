<?php

namespace App\Repositories\Master;

use App\Models\Priority;
use App\Repositories\Master\Contracts\PriorityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PriorityRepository implements PriorityRepositoryInterface
{
    public function all(): Collection
    {
        return Priority::all();
    }

    public function findOrFail(int $id): Priority
    {
        return Priority::findOrFail($id);
    }

    public function create(array $data): Priority
    {
        return Priority::create($data);
    }

    public function update(Priority $priority, array $data): Priority
    {
        $priority->update($data);

        return $priority->fresh();
    }

    public function delete(Priority $priority): void
    {
        $priority->delete();
    }
}
