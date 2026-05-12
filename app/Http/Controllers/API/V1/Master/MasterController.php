<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Core\Standards\ApiResponseLibrary;
use App\Core\Standards\ResponseStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Master\DTO\CategoryDTO;
use App\Modules\Master\DTO\MappingDTO;
use App\Modules\Master\DTO\ServiceDTO;
use App\Modules\Master\Requests\AssignTeamLeadRequest;
use App\Modules\Master\Requests\StoreCategoryRequest;
use App\Modules\Master\Requests\StoreServiceRequest;
use App\Modules\Master\Requests\StoreTeamStructureRequest;
use App\Modules\Master\Services\MasterService;
use App\Modules\Master\Transformers\MasterTransformer;
use App\Traits\UpsApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MasterController extends Controller
{
    use ApiResponseLibrary, UpsApiTrait;

    public function __construct(
        protected MasterService $service
    ) {}

    /*
     * |--------------------------------------------------------------------------
     * | Service Master
     * |--------------------------------------------------------------------------
     */

    public function keyclock_Callback(Request $request)
    {
        $code = $request->query('code');
        $KEYCLOAK_BASE = 'https://elrs-auth.assam.gov.in/keycloak/realms/elrs-sso';
        $CLIENT_ID = 'elrs-ticketing-system';
        $CLIENT_SECRET = 'jVZm6mIsoEzdogErFc8Xfd6N9DnHZhj9';
        $REDIRECT_URI = 'http://127.0.0.1:8000/api/v1/master/ticket-callback-url';
        $FRONTEND_URL = 'http://127.0.0.1:3501';

        if (!$code) {
            return redirect($FRONTEND_URL . '/unauthorized?error=' . urlencode('The provided code is invalid or corrupted.'));
        }

        try {
            // 🔄 STEP 1: Exchange code → token
            $response = Http::asForm()->post(
                $KEYCLOAK_BASE . '/protocol/openid-connect/token',
                [
                    'grant_type' => 'authorization_code',
                    'client_id' => $CLIENT_ID,
                    'client_secret' => $CLIENT_SECRET,
                    'code' => $code,
                    'redirect_uri' => $REDIRECT_URI,
                ]
            );

            if (!$response->successful()) {
                return redirect($FRONTEND_URL . '/unauthorized?error=' . urlencode('The provided token is invalid or corrupted.'));
            }

            $data = $response->json();
            $accessToken = $data['access_token'];
            $idToken = $data['id_token'] ?? null;

            // 🔍 STEP 2: Decode token → get Keycloak user ID
            $payload = json_decode(base64_decode(explode('.', $accessToken)[1]), true);

            $keycloakUserId = $payload['sub'] ?? null;

            if (!$keycloakUserId) {
                return redirect($FRONTEND_URL . '/unauthorized?error=' . urlencode('The provided user ID is invalid or corrupted.'));
            }

            // \Log::info('Keycloak User ID: ' . $keycloakUserId);

            // 🚀 STEP 3: Call UPS API
            $upsResponse = $this->upsPost('dev/token', [
                'keycloak_user_id' => $keycloakUserId
            ]);

            if (!$upsResponse->successful()) {
                return redirect($FRONTEND_URL . '/unauthorized?error=' . urlencode('The provided UPS API failed.'));
            }

            $upsData = $upsResponse->json();

            $accessToken = $upsData['access_token'];

            $payload_ups = json_decode(base64_decode(explode('.', $accessToken)[1]), true);
            $ups_user_id = $payload_ups['sub'];

            // 🚀 STEP 3: Sync user from UPS
            $user = $this->syncUpsUser($accessToken, $ups_user_id);

            if (!$user) {
                return redirect($FRONTEND_URL . '/unauthorized?error=' . urlencode('Failed to sync user from UPS.'));
            }

            // Extract roles from the synced user model
            $roles = json_decode($user->role_name, true) ?? [];

            if (empty($roles)) {
                return redirect($FRONTEND_URL . '/unauthorized?error=no_role_assigned');
            }

            $priority = ['superadmin', 'admin', 'adc', 'teamlead', 'developer', 'lra', 'applicant'];
            $primaryRole = null;

            foreach ($priority as $p) {
                if (in_array($p, $roles)) {
                    $primaryRole = $p;
                    break;
                }
            }

            // Determine the frontend path based on the role_code
            $redirectPath = match ($primaryRole) {
                'superadmin', 'admin' => '/superadmin-dashboard',
                'teamlead' => '/teamlead-dashboard',
                'developer' => '/developer-dashboard',
                'applicant', 'lra', 'adc' => '/applicant-dashboard',
                default => '/my-dashboard'
            };

            // 🔁 STEP 5: Redirect to frontend dashboard and pass tokens securely via temporary Cookies
            return redirect($FRONTEND_URL . $redirectPath)
                ->cookie('ups_access_token', $accessToken, 5, '/', null, false, false)
                ->cookie('ups_id_token', $idToken, 5, '/', null, false, false)
                ->cookie('ups_user_name', $user->name ?? 'User', 5, '/', null, false, false)
                ->cookie('ups_role', $primaryRole, 5, '/', null, false, false);

            /*
             * Example UPS response:
             * {
             *   "access_token": "...",
             *   "user_uuid": "...",
             *   "employee_code": "...",
             *   "full_name": "..."
             * }
             */

            // 💾 STEP 4: Store (optional)
            // session([
            //     'access_token' => $upsData['access_token'] ?? null,
            //     'user_uuid' => $upsData['user_uuid'] ?? null,
            //     'employee_code' => $upsData['employee_code'] ?? null,
            //     'full_name' => $upsData['full_name'] ?? null,
            //     'keycloak_user_id' => $keycloakUserId
            // ]);

            // 🔁 STEP 5: Redirect to frontend with token (optional)
            // return redirect($FRONTEND_URL . '?token=' . $upsData['access_token']);
        } catch (\Exception $e) {
            return redirect($FRONTEND_URL . '/unauthorized?error=' . urlencode($e->getMessage()));
        }
    }

    public function storeService(StoreServiceRequest $request)
    {
        $dto = ServiceDTO::fromRequest($request);
        $service = $this->service->createService($dto);

        return $this->respondWithSuccess(
            data: MasterTransformer::service($service),
            message: 'Service stored successfully',
            code: ResponseStatus::CREATED
        );
    }

    public function getService()
    {
        $services = $this->service->getService();

        return $this->respondWithSuccess(
            data: $services->map(fn($service) => MasterTransformer::service($service)),
            message: 'Services fetched successfully',
            code: ResponseStatus::OK
        );
    }

    /*
     * |--------------------------------------------------------------------------
     * | Category Master
     * |--------------------------------------------------------------------------
     */

    // public function storeCategory(StoreCategoryRequest $request)
    // {
    //     $dto = CategoryDTO::fromRequest($request);

    //     $category = $this->service->createCategory($dto);

    //     return response()->json(
    //         MasterTransformer::category($category)
    //     );
    // }

    /*
     * |--------------------------------------------------------------------------
     * | Category → Team Lead Mapping
     * |--------------------------------------------------------------------------
     */

    // public function assignTeamLead(AssignTeamLeadRequest $request)
    // {
    //     $dto = MappingDTO::fromRequest($request);

    //     $data = $this->service->assignTeamLead($dto);

    //     return response()->json(
    //         MasterTransformer::mapping($data)
    //     );
    // }
    public function deleteService($id)
    {
        $decryptedId = decrypt_id($id);

        if (!$decryptedId) {
            return $this->respondWithProblem(
                title: 'Invalid ID',
                detail: 'The provided service ID is invalid or corrupted.',
                httpStatus: 400,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }

        $this->service->deleteService($decryptedId);

        return $this->respondWithSuccess(
            data: null,
            message: 'Service deleted successfully',
            code: ResponseStatus::OK
        );
    }

    // Teamlead structure

    public function getTeamLeadStructure()
    {
        $teamLeadStructure = $this->service->getTeamLeadStructure();

        return $this->respondWithSuccess(
            data: $teamLeadStructure,
            message: 'Teamlead structure fetched successfully',
            code: ResponseStatus::OK
        );
    }

    public function getTeamLeadList()
    {
        $teamLeadList = $this->service->getTeamLeadList();

        return $this->respondWithSuccess(
            data: $teamLeadList,
            message: 'Teamlead list fetched successfully',
            code: ResponseStatus::OK
        );
    }

    public function getDeveloperList(Request $request)
    {
        $user = auth()->user();
        $roles = json_decode($user->role_name, true) ?? [];

        $teamLeadId = null;

        // 1. Check if an explicit team_lead_id is provided (for Admins)
        if ($request->has('team_lead_id')) {
            $teamLeadId = decrypt_id($request->query('team_lead_id'));
        }

        // 2. If no ID in query, and user is a Team Lead (but NOT an admin), auto-filter by their ID
        if (!$teamLeadId && in_array('teamlead', $roles) && !in_array('admin', $roles) && !in_array('superadmin', $roles)) {
            $teamLeadId = $user->id;
        }

        $developerList = $this->service->getDeveloperList($teamLeadId);

        return $this->respondWithSuccess(
            data: $developerList,
            message: 'Developer list fetched successfully',
            code: ResponseStatus::OK
        );
    }

    public function getapplicantList()
    {
        $applicantList = $this->service->getapplicantList();

        return $this->respondWithSuccess(
            data: $applicantList,
            message: 'Applicant list fetched successfully',
            code: ResponseStatus::OK
        );
    }

    public function deleteUser($id)
    {
        $decryptedId = decrypt_id($id);

        if (!$decryptedId) {
            return $this->respondWithProblem(
                title: 'Invalid ID',
                detail: 'The provided user ID is invalid or corrupted.',
                httpStatus: 400,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }

        $this->service->deleteUser($decryptedId);

        return $this->respondWithSuccess(
            data: null,
            message: 'User deleted successfully',
            code: ResponseStatus::OK
        );
    }

    public function updateUserStatus(Request $request, $id)
    {
        $decryptedId = decrypt_id($id);

        if (!$decryptedId) {
            return $this->respondWithProblem(
                title: 'Invalid ID',
                detail: 'The provided user ID is invalid or corrupted.',
                httpStatus: 400,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }

        $status = $request->input('status');
        if (!in_array($status, ['active', 'inactive'])) {
            return $this->respondWithProblem(
                title: 'Invalid Status',
                detail: 'Status must be either active or inactive.',
                httpStatus: 400,
                errorCode: 'ELRS-VAL-INVALIDSTATUS'
            );
        }

        $user = $this->service->updateUserStatus($decryptedId, $status);

        if (!$user) {
            return $this->respondWithProblem(
                title: 'User Not Found',
                detail: 'The requested user could not be found.',
                httpStatus: 404,
                errorCode: 'ELRS-VAL-NOTFOUND'
            );
        }

        return $this->respondWithSuccess(
            data: null,
            message: 'User status updated successfully',
            code: ResponseStatus::OK
        );
    }

    public function storeTeamStructure(StoreTeamStructureRequest $request)
    {
        $teamStructure = $this->service->storeTeamStructure($request);

        if (isset($teamStructure['status']) && $teamStructure['status'] === 'error') {
            return $this->respondWithProblem(
                title: 'Team structure store failed',
                detail: $teamStructure['message'],
                httpStatus: 400,
                errorCode: 'ELRS-VAL-STORFAILED'
            );
        }

        return $this->respondWithSuccess(
            data: $teamStructure,
            message: 'Team structure created successfully',
            code: ResponseStatus::CREATED
        );
    }

    public function deleteTeamStructure($categoryId, $teamLeadId)
    {
        if (!$categoryId || !$teamLeadId) {
            return $this->respondWithProblem(
                title: 'Invalid IDs',
                detail: 'The provided category or team lead ID is invalid or corrupted.',
                httpStatus: 400,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }

        $result = $this->service->deleteTeamStructure($categoryId, $teamLeadId);

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->respondWithProblem(
                title: 'Team structure deletion failed',
                detail: $result['message'],
                httpStatus: 400,
                errorCode: 'ELRS-VAL-DELETEFAILED'
            );
        }

        return $this->respondWithSuccess(
            data: null,
            message: 'Team structure deleted successfully',
            code: ResponseStatus::OK
        );
    }
}
