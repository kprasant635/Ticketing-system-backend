<?php

namespace App\Services\Master;

use App\DTO\Master\StatusDTO;
use App\Models\Status;
use App\Repositories\Master\Contracts\StatusRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StatusService
{
    public function __construct(
        protected StatusRepositoryInterface $repository
    ) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): Status
    {
        return $this->repository->findOrFail($id);
    }

    public function create(StatusDTO $dto): Status
    {
        return $this->repository->create([
            'status_name' => $dto->status_name,
        ]);
    }

    public function update(int $id, StatusDTO $dto): Status
    {
        $status = $this->repository->findOrFail($id);

        return $this->repository->update($status, [
            'status_name' => $dto->status_name,
        ]);
    }

    public function delete(int $id): void
    {
        $status = $this->repository->findOrFail($id);

        $this->repository->delete($status);
    }
}
