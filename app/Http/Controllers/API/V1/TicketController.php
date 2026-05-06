<?php

namespace App\Http\Controllers\API\V1;

use App\Core\Standards\ApiResponseLibrary;
use App\Core\Standards\ResponseStatus;
use App\DTO\TicketDTO;
use App\Http\Controllers\Controller;
use App\Requests\API\V1\Ticket\CreateTicketRequest;
use App\Requests\API\V1\Ticket\UpdateTicketRequest;
use App\Services\TicketService;
use App\Traits\ApiResponseTrait;
use App\Transformers\TicketTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Validator;

class TicketController extends Controller
{
    use ApiResponseLibrary;

    public function __construct(
        protected TicketService $service
    ) {}

    public function index(): JsonResponse
    {
        try {
            $tickets = $this->service->list();

            return $this->respondWithSuccess(
                data: TicketTransformer::collection($tickets),
                message: 'Tickets fetched successfully',
                code: ResponseStatus::OK
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Failed to retrieve tickets',
                detail: $e->getMessage(),
                httpStatus: 500,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }
    }

    public function store(CreateTicketRequest $request): JsonResponse
    {
        try {
            $dto = TicketDTO::fromRequest($request);

            $ticket = $this->service->create($dto);

            return $this->respondWithSuccess(
                data: TicketTransformer::transform($ticket),
                message: 'Ticket created successfully',
                code: ResponseStatus::CREATED
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Failed to create ticket',
                detail: $e->getMessage(),
                httpStatus: 500,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }
    }

    public function addAttachments($id, Request $request): JsonResponse
    {
        try {
            $validated = Validator::make($request->all(), [
                'files' => 'required',
                'files.*' => 'file|max:10240',
            ]);

            if ($validated->fails()) {
                return $this->respondWithProblem(
                    title: 'Validation error',
                    detail: $validated->errors()->first(),
                    httpStatus: 422,
                    errorCode: 'ELRS-VAL-INVALIDID'
                );
            }

            $files = $validated->validated()['files'];

            if ($files && !is_array($files)) {
                $files = [$files];
            }
            $id = decrypt_id($id);
            $ticket = $this->service->find($id);
            // Reusing service logic by passing files directly or via a dummy DTO
            // For simplicity, we'll add a specific method to service or just handle here if small
            $ticket = $this->service->addFiles($id, $files);

            return $this->respondWithSuccess(
                data: TicketTransformer::transform($ticket),
                message: 'Files uploaded successfully',
                code: ResponseStatus::OK
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Failed to upload files',
                detail: $e->getMessage(),
                httpStatus: 500
            );
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $ticket = $this->service->find($id);

            return $this->success(
                TicketTransformer::transform($ticket)
            );
        } catch (ModelNotFoundException) {
            return $this->notFound('Ticket not found');
        } catch (Exception $e) {
            return $this->serverError('Failed to retrieve ticket: ' . $e->getMessage());
        }
    }

    public function update(UpdateTicketRequest $request, int $id): JsonResponse
    {
        try {
            $dto = TicketDTO::fromRequest($request);

            $ticket = $this->service->update($id, $dto);

            return $this->success(
                TicketTransformer::transform($ticket)
            );
        } catch (ModelNotFoundException) {
            return $this->notFound('Ticket not found');
        } catch (Exception $e) {
            return $this->serverError('Failed to update ticket: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            // Check if ticket exists first
            $this->service->find($id);

            $this->service->delete($id);

            return $this->success([
                'message' => 'Ticket deleted successfully'
            ]);
        } catch (ModelNotFoundException) {
            return $this->notFound('Ticket not found');
        } catch (Exception $e) {
            return $this->serverError('Failed to delete ticket: ' . $e->getMessage());
        }
    }
}
