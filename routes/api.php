<?php

use App\Http\Controllers\API\V1\Master\CategoryController;
use App\Http\Controllers\API\V1\Master\MasterController;
use App\Http\Controllers\API\V1\Master\PriorityController;
use App\Http\Controllers\API\V1\Master\StatusController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\DashboardController;
use App\Http\Controllers\API\V1\QueryController;
use App\Http\Controllers\API\V1\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    // ─── Public Routes ──────────────────────────────────────────────────────────
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('master/ticket-callback-url', [MasterController::class, 'keyclock_Callback']);

    // ─── SuperAdmin / Admin Routes ──────────────────────────────────────────────
    Route::middleware('ups.role:superadmin,admin')->group(function () {
        Route::prefix('master')->group(function () {
            Route::post('service', [MasterController::class, 'storeService']);
            Route::delete('service-delete/{id}', [MasterController::class, 'deleteService']);

            Route::post('category', [CategoryController::class, 'storeCategory']);
            Route::delete('category-delete/{id}', [CategoryController::class, 'deleteCategory']);

            Route::get('subcategories/list', [CategoryController::class, 'indexlistSubCategories']);
            Route::post('subcategory', [CategoryController::class, 'storeSubCategory']);
            Route::delete('subcategory-delete/{id}', [CategoryController::class, 'deleteSubCategory']);

            Route::get('team-lead-list', [MasterController::class, 'getTeamLeadList']);
            Route::get('developer-list', [MasterController::class, 'getDeveloperList']);
            Route::get('applicant-list', [MasterController::class, 'getapplicantList']);

            Route::delete('user-delete/{id}', [MasterController::class, 'deleteUser']);
            Route::post('user-status/{id}', [MasterController::class, 'updateUserStatus']);

            // team-structure management
            Route::post('team-structure-store', [MasterController::class, 'storeTeamStructure']);
            Route::post('assign-teamlead', [MasterController::class, 'assignTeamLead']);
            Route::delete('delete-team-structure/{categoryId}/{teamLeadId}', [MasterController::class, 'deleteTeamStructure']);
        });
    });

    // ─── Admin & Team Lead Shared Routes ────────────────────────────────────────
    Route::middleware('ups.role:superadmin,admin,teamlead')->group(function () {
        Route::get('master/get-teamlead-structure', [MasterController::class, 'getTeamLeadStructure']);
    });

    // ─── Authenticated Routes (Common Master Data) ──────────────────────────────
    Route::middleware('ups.role')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);

        // Dashboards
        Route::get('dashboard/superadmin', [DashboardController::class, 'superadmin']);
        Route::get('dashboard/applicant', [DashboardController::class, 'applicant']);
        Route::get('dashboard/developer', [DashboardController::class, 'developer']);
        Route::get('dashboard/teamlead', [DashboardController::class, 'teamlead']);

        // Master Data (Read-only for all authenticated users)
        Route::prefix('master')->group(function () {
            Route::get('service-get', [MasterController::class, 'getService']);
            Route::get('categories', [CategoryController::class, 'index']);
            Route::get('list-categories-by-service/{serviceId}', [CategoryController::class, 'listCategoriesByService'])->where('serviceId', '.*');
            Route::get('subcategories/{categoryId}', [CategoryController::class, 'listSubCategories'])->where('categoryId', '.*');
        });

        Route::apiResource('priorities', PriorityController::class);
        Route::apiResource('statuses', StatusController::class);
    });

    // ─── Team Lead Routes ──────────────────────────────────────────────────────
    Route::middleware('ups.role:teamlead,superadmin,admin')->group(function () {
        Route::get('tickets/team-lead-tickets', [TicketController::class, 'teamLeadTickets']);
        Route::get('developer-list', [MasterController::class, 'getDeveloperList']);
        Route::post('tickets/{id}/assign', [TicketController::class, 'assign']);
        Route::post('tickets/{id}/close', [TicketController::class, 'close']);
    });

    // ─── Developer Routes ───────────────────────────────────────────────────────
    Route::middleware('ups.role:developer,superadmin,admin')->group(function () {
        Route::post('tickets/{id}/status', [TicketController::class, 'changeStatus']);
        Route::post('queries/{id}/reply', [QueryController::class, 'reply']);
    });

    // ─── Applicant / LRA / ADC Routes ──────────────────────────────────────────
    Route::middleware('ups.role:applicant,lra,adc,superadmin,admin')->group(function () {
        Route::post('tickets', [TicketController::class, 'store']);
        Route::post('tickets/{id}/attachments', [TicketController::class, 'addAttachments']);
        Route::post('tickets/{id}/query', [QueryController::class, 'store']);
    });

    // ─── General Ticket Access ──────────────────────────────────────────────────
    Route::middleware('ups.role')->group(function () {
        Route::get('tickets', [TicketController::class, 'index']);
        Route::get('tickets/{id}', [TicketController::class, 'show']);
    });
});

// });
