<?php

namespace App\Repositories\Master;

use App\Models\Status;
use App\Repositories\Master\Contracts\StatusRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StatusRepository implements StatusRepositoryInterface
{
    public function all(): Collection
    {
        return Status::all();
    }

    public function findOrFail(int $id): Status
    {
        return Status::findOrFail($id);
    }

    public function create(array $data): Status
    {
        return Status::create($data);
    }

    public function update(Status $status, array $data): Status
    {
        $status->update($data);

        return $status->fresh();
    }

    public function delete(Status $status): void
    {
        $status->delete();
    }
}
