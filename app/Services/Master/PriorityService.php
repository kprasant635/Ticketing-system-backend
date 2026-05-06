<?php

namespace App\Services\Master;

use App\DTO\Master\PriorityDTO;
use App\Models\Priority;
use App\Repositories\Master\Contracts\PriorityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PriorityService
{
    public function __construct(
        protected PriorityRepositoryInterface $repository
    ) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): Priority
    {
        return $this->repository->findOrFail($id);
    }

    public function create(PriorityDTO $dto): Priority
    {
        return $this->repository->create([
            'priority_name' => $dto->priority_name,
            'sla_hours'     => $dto->sla_hours,
        ]);
    }

    public function update(int $id, PriorityDTO $dto): Priority
    {
        $priority = $this->repository->findOrFail($id);

        return $this->repository->update($priority, [
            'priority_name' => $dto->priority_name,
            'sla_hours'     => $dto->sla_hours,
        ]);
    }

    public function delete(int $id): void
    {
        $priority = $this->repository->findOrFail($id);

        $this->repository->delete($priority);
    }
}
