<?php

namespace App\Http\Controllers\API\V1\Master;

use App\DTO\Master\StatusDTO;
use App\Http\Controllers\Controller;
use App\Requests\API\V1\Master\Status\CreateStatusRequest;
use App\Requests\API\V1\Master\Status\UpdateStatusRequest;
use App\Services\Master\StatusService;
use App\Traits\ApiResponseTrait;
use App\Transformers\Master\StatusTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class StatusController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected StatusService $service
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            $statuses = $this->service->list();

            return $this->success(
                StatusTransformer::collection($statuses)
            );
        } catch (Exception $e) {
            return $this->serverError('Failed to retrieve statuses: ' . $e->getMessage());
        }
    }

    public function store(CreateStatusRequest $request): JsonResponse
    {
        try {
            $dto = StatusDTO::fromRequest($request);

            $status = $this->service->create($dto);

            return $this->success(
                StatusTransformer::transform($status),
                201
            );
        } catch (Exception $e) {
            return $this->serverError('Failed to create status: ' . $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $status = $this->service->find($id);

            return $this->success(
                StatusTransformer::transform($status)
            );
        } catch (ModelNotFoundException) {
            return $this->notFound('Status not found');
        } catch (Exception $e) {
            return $this->serverError('Failed to retrieve status: ' . $e->getMessage());
        }
    }

    public function update(UpdateStatusRequest $request, int $id): JsonResponse
    {
        try {
            $dto = StatusDTO::fromRequest($request);

            $status = $this->service->update($id, $dto);

            return $this->success(
                StatusTransformer::transform($status)
            );
        } catch (ModelNotFoundException) {
            return $this->notFound('Status not found');
        } catch (Exception $e) {
            return $this->serverError('Failed to update status: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->success([
                'message' => 'Status deleted successfully'
            ]);
        } catch (ModelNotFoundException) {
            return $this->notFound('Status not found');
        } catch (Exception $e) {
            return $this->serverError('Failed to delete status: ' . $e->getMessage());
        }
    }
}
