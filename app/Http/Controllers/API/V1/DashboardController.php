<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Core\Standards\ApiResponseLibrary;
use App\Core\Standards\ResponseStatus;

class DashboardController extends Controller
{
    use ApiResponseLibrary;

    public function superadmin()
    {
        $stats = [
            'totalUsers' => 150,
            'activeUsers' => 120,
            'suspendedUsers' => 5,
            'totalRoles' => 8,
            'logsToday' => 342,
            'uptime' => '99.9%',
            'serverLoad' => 45,
            'memoryUsage' => 60,
            'subadmins' => 10,
            'projectcordinators' => 5,
            'teamleads' => 20,
            'developers' => 100,
        ];

        return $this->respondWithSuccess(
            data: $stats,
            message: 'SuperAdmin Dashboard Stats fetched successfully',
            code: ResponseStatus::OK
        );
    }
}
