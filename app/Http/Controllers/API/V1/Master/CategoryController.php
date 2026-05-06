<?php

namespace App\Http\Controllers\API\V1\Master;

use App\Core\Standards\ApiResponseLibrary;
use App\Core\Standards\ResponseStatus;
use App\DTO\Master\CategoryDTO;
use App\DTO\Master\SubCategoryDTO;
use App\Http\Controllers\Controller;
use App\Requests\API\V1\Master\Category\CreateCategoryRequest;
use App\Requests\API\V1\Master\Category\UpdateCategoryRequest;
use App\Services\Master\CategoryService;
use App\Services\Master\SubCategoryService;
use App\Transformers\Master\CategoryTransformer;
use App\Transformers\Master\SubCategoryTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Exception;

class CategoryController extends Controller
{
    use ApiResponseLibrary;

    public function __construct(
        protected CategoryService $service,
        protected SubCategoryService $subCategoryService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $categories = $this->service->list();

            return $this->respondWithSuccess(
                data: CategoryTransformer::collection($categories),
                message: 'Categories fetched successfully',
                code: ResponseStatus::OK
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Failed to retrieve categories',
                detail: $e->getMessage(),
                httpStatus: 500,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }
    }

    public function listCategoriesByService(string $serviceId): JsonResponse
    {
        try {
            $decryptedServiceId = decrypt_id($serviceId);
            if (!$decryptedServiceId) {
                return $this->respondWithProblem(title: 'Invalid Service ID', detail: 'The provided service ID is invalid.', httpStatus: 400);
            }

            $categories = $this->service->listByService($decryptedServiceId);

            return $this->respondWithSuccess(
                data: CategoryTransformer::collection($categories),
                message: 'Categories fetched successfully',
                code: ResponseStatus::OK
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Failed to retrieve categories',
                detail: $e->getMessage(),
                httpStatus: 500,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }
    }

    public function storeCategory(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $dto = CategoryDTO::fromRequest($request);

            $category = $this->service->create($dto);

            return $this->respondWithSuccess(
                data: CategoryTransformer::transform($category),
                message: 'Category created successfully',
                code: ResponseStatus::OK
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Failed to create category',
                detail: $e->getMessage(),
                httpStatus: 500,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $decryptedId = decrypt_id($id);

            if (!$decryptedId) {
                return $this->respondWithProblem(
                    title: 'Invalid ID',
                    detail: 'The provided category ID is invalid or corrupted.',
                    httpStatus: 400,
                    errorCode: 'ELRS-VAL-INVALIDID'
                );
            }

            $category = $this->service->find($decryptedId);

            return $this->respondWithSuccess(
                data: CategoryTransformer::transform($category),
                message: 'Category fetched successfully',
                code: ResponseStatus::OK
            );
        } catch (ModelNotFoundException $e) {
            return $this->respondWithProblem(
                title: 'Category not found',
                detail: $e->getMessage(),
                httpStatus: 404,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Failed to retrieve category',
                detail: $e->getMessage(),
                httpStatus: 500,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }
    }

    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        try {
            $decryptedId = decrypt_id($id);

            if (!$decryptedId) {
                return $this->respondWithProblem(
                    title: 'Invalid ID',
                    detail: 'The provided category ID is invalid or corrupted.',
                    httpStatus: 400,
                    errorCode: 'ELRS-VAL-INVALIDID'
                );
            }

            $dto = CategoryDTO::fromRequest($request);

            $category = $this->service->update($decryptedId, $dto);

            return $this->respondWithSuccess(
                data: CategoryTransformer::transform($category),
                message: 'Category updated successfully',
                code: ResponseStatus::OK
            );
        } catch (ModelNotFoundException $e) {
            return $this->respondWithProblem(
                title: 'Category not found',
                detail: $e->getMessage(),
                httpStatus: 404,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Failed to update category',
                detail: $e->getMessage(),
                httpStatus: 500,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }
    }

    public function deleteCategory(string $id): JsonResponse
    {
        try {
            $decryptedId = decrypt_id($id);

            if (!$decryptedId) {
                return $this->respondWithProblem(
                    title: 'Invalid ID',
                    detail: 'The provided category ID is invalid or corrupted.',
                    httpStatus: 400,
                    errorCode: 'ELRS-VAL-INVALIDID'
                );
            }

            $this->service->delete($decryptedId);

            return $this->respondWithSuccess(
                data: null,
                message: 'Category deleted successfully',
                code: ResponseStatus::OK
            );
        } catch (ModelNotFoundException $e) {
            return $this->respondWithProblem(
                title: 'Category not found',
                detail: $e->getMessage(),
                httpStatus: 404,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(
                title: 'Failed to delete category',
                detail: $e->getMessage(),
                httpStatus: 500,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }
    }

    /*
     * |--------------------------------------------------------------------------
     * | SubCategory Methods
     * |--------------------------------------------------------------------------
     */

    public function indexlistSubCategories(): JsonResponse
    {
        try {
            $subcategories = $this->subCategoryService->list();

            return $this->respondWithSuccess(
                data: SubCategoryTransformer::collection($subcategories),
                message: 'Subcategories fetched successfully',
                code: ResponseStatus::OK
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(title: 'Failed to retrieve subcategories', detail: $e->getMessage(), httpStatus: 500);
        }
    }

    public function listSubCategories(string $categoryId): JsonResponse
    {
        try {
            $decryptedCategoryId = decrypt_id($categoryId);
            if (!$decryptedCategoryId) {
                return $this->respondWithProblem(title: 'Invalid Category ID', detail: 'The provided category ID is invalid.', httpStatus: 400);
            }

            $subcategories = $this->subCategoryService->listByCategory($decryptedCategoryId);

            return $this->respondWithSuccess(
                data: SubCategoryTransformer::collection($subcategories),
                message: 'Subcategories fetched successfully',
                code: ResponseStatus::OK
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(title: 'Failed to retrieve subcategories', detail: $e->getMessage(), httpStatus: 500);
        }
    }

    public function storeSubCategory(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $dto = SubCategoryDTO::fromRequest($request);
            $subcategory = $this->subCategoryService->create($dto);

            return $this->respondWithSuccess(
                data: SubCategoryTransformer::transform($subcategory),
                message: 'Subcategory created successfully',
                code: ResponseStatus::OK
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(title: 'Failed to create subcategory', detail: $e->getMessage(), httpStatus: 500);
        }
    }

    public function deleteSubCategory(string $id): JsonResponse
    {
        try {
            $decryptedId = decrypt_id($id);
            if (!$decryptedId) {
                return $this->respondWithProblem(title: 'Invalid ID', detail: 'The provided subcategory ID is invalid.', httpStatus: 400);
            }

            $this->subCategoryService->delete($decryptedId);

            return $this->respondWithSuccess(
                data: null,
                message: 'Subcategory deleted successfully',
                code: ResponseStatus::OK
            );
        } catch (Exception $e) {
            return $this->respondWithProblem(title: 'Failed to delete subcategory', detail: $e->getMessage(), httpStatus: 500);
        }
    }
}
