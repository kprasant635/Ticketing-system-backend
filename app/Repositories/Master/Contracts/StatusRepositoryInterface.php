<?php

namespace App\Repositories\Master\Contracts;

use App\Models\Status;
use Illuminate\Database\Eloquent\Collection;

interface StatusRepositoryInterface
{
    public function all(): Collection;
    public function findOrFail(int $id): Status;
    public function create(array $data): Status;
    public function update(Status $status, array $data): Status;
    public function delete(Status $status): void;
}
