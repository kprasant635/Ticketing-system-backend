<?php

namespace App\Http\Controllers\API\V1\Master;

use App\DTO\Master\PriorityDTO;
use App\Http\Controllers\Controller;
use App\Requests\API\V1\Master\Priority\CreatePriorityRequest;
use App\Requests\API\V1\Master\Priority\UpdatePriorityRequest;
use App\Services\Master\PriorityService;
use App\Traits\ApiResponseTrait;
use App\Transformers\Master\PriorityTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PriorityController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected PriorityService $service
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            $priorities = $this->service->list();

            return $this->success(
                PriorityTransformer::collection($priorities)
            );
        } catch (Exception $e) {
            return $this->serverError('Failed to retrieve priorities: ' . $e->getMessage());
        }
    }

    public function store(CreatePriorityRequest $request): JsonResponse
    {
        try {
            $dto = PriorityDTO::fromRequest($request);

            $priority = $this->service->create($dto);

            return $this->success(
                PriorityTransformer::transform($priority),
                201
            );
        } catch (Exception $e) {
            return $this->serverError('Failed to create priority: ' . $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $priority = $this->service->find($id);

            return $this->success(
                PriorityTransformer::transform($priority)
            );
        } catch (ModelNotFoundException) {
            return $this->notFound('Priority not found');
        } catch (Exception $e) {
            return $this->serverError('Failed to retrieve priority: ' . $e->getMessage());
        }
    }

    public function update(UpdatePriorityRequest $request, int $id): JsonResponse
    {
        try {
            $dto = PriorityDTO::fromRequest($request);

            $priority = $this->service->update($id, $dto);

            return $this->success(
                PriorityTransformer::transform($priority)
            );
        } catch (ModelNotFoundException) {
            return $this->notFound('Priority not found');
        } catch (Exception $e) {
            return $this->serverError('Failed to update priority: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->success([
                'message' => 'Priority deleted successfully'
            ]);
        } catch (ModelNotFoundException) {
            return $this->notFound('Priority not found');
        } catch (Exception $e) {
            return $this->serverError('Failed to delete priority: ' . $e->getMessage());
        }
    }
}
