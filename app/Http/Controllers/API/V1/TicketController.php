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
            // if (!auth()->check()) {
            //     return $this->respondWithProblem(
            //         title: 'Unauthorized',
            //         detail: 'Your session has expired. Please log in again.',
            //         httpStatus: 401,
            //         errorCode: 'ELRS-VAL-INVALIDID'
            //     );
            // }

            $dto = TicketDTO::fromRequest($request);

            $ticket = $this->service->create($dto);

            return $this->respondWithSuccess(
                data: TicketTransformer::transform($ticket),
                message: 'Ticket created successfully',
                code: ResponseStatus::CREATED
            );
        } catch (Exception $e) {
            \Log::error('Ticket creation failed: ' . $e->getMessage());
            return $this->respondWithProblem(
                title: 'Submission Failed',
                detail: 'We could not process your ticket at this time. Please try again later.',
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

    public function assign($id, Request $request): JsonResponse
    {
        try {
            $id = decrypt_id($id);
            $validated = $request->validate([
                'developer_id' => 'required|exists:users,id',
            ]);

            $ticket = $this->service->assign($id, $validated['developer_id']);

            return $this->respondWithSuccess(
                data: TicketTransformer::transform($ticket),
                message: 'Ticket assigned successfully'
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Assignment Failed',
                detail: $e->getMessage(),
                httpStatus: 500
            );
        }
    }

    public function changeStatus($id, Request $request): JsonResponse
    {
        try {
            $id = decrypt_id($id);
            $validated = $request->validate([
                'status_id' => 'required|exists:statuses,id',
                'remark' => 'nullable|string',
            ]);

            $ticket = $this->service->changeStatus($id, $validated['status_id'], $validated['remark'] ?? null);

            return $this->respondWithSuccess(
                data: TicketTransformer::transform($ticket),
                message: 'Status updated successfully'
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Status Update Failed',
                detail: $e->getMessage(),
                httpStatus: 500
            );
        }
    }

    public function close($id, Request $request): JsonResponse
    {
        try {
            $id = decrypt_id($id);
            $remark = $request->input('remark');

            $ticket = $this->service->close($id, $remark);

            return $this->respondWithSuccess(
                data: TicketTransformer::transform($ticket),
                message: 'Ticket closed successfully'
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Failed to close ticket',
                detail: $e->getMessage(),
                httpStatus: 500
            );
        }
    }
}

