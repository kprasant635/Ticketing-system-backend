<?php

namespace App\Modules\Master\Repositories;

use App\Models\Category;
use App\Models\CategoryTeamLead;
use App\Models\Service;
use App\Models\TeamLeadDeveloper;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MasterRepository
{
    public function storeService($dto)
    {
        return Service::create([
            'service_name' => trim($dto->service_name),
            'description' => trim($dto->description),
            'status' => $dto->status
        ]);
    }

    public function getService()
    {
        return Service::withCount('categories')->get();
    }

    public function storeCategory($dto)
    {
        return Category::create([
            'service_id' => $dto->service_id,
            'category_name' => $dto->category_name
        ]);
    }

    public function assignTeamLead($dto)
    {
        return CategoryTeamLead::create([
            'category_id' => $dto->category_id,
            'teamlead_id' => $dto->teamlead_id
        ]);
    }

    public function deleteService($id)
    {
        return Service::destroy($id);
    }

    public function getTeamLeadStructuredetails()
    {
        return DB::table('category_team_lead as ctl')
            ->join('categories as c', 'c.id', '=', 'ctl.category_id')
            ->join('services as s', 's.id', '=', 'c.service_id')
            ->join('users as tl', function ($join) {
                $join
                    ->on('tl.id', '=', 'ctl.team_lead_id')
                    ->where('tl.role_name', 'teamlead');
            })
            ->leftJoin('team_lead_developer as tld', 'tld.team_lead_id', '=', 'tl.id')
            ->leftJoin('users as d', function ($join) {
                $join
                    ->on('d.id', '=', 'tld.developer_id')
                    ->where('d.role_name', 'developer');
            })
            ->select(
                'c.id as categoryId',
                'c.name as categoryName',
                's.id as serviceId',
                's.service_name as serviceName',
                'tl.id as teamLeadId',
                'tl.name as teamLeadName',
                'tl.email as teamLeadEmail',
                DB::raw("json_agg(json_build_object('_id', d.id, 'name', d.name, 'email', d.email)) as developers")
            )
            ->groupBy('c.id', 'c.name', 's.id', 's.service_name', 'tl.id', 'tl.name', 'tl.email')
            ->orderBy('c.id')
            ->get();
    }

    public function getTeamLeadList()
    {
        return User::where(function ($query) {
            $query
                ->where('role_name', 'teamlead')
                ->orWhere('role_name', 'like', '%teamlead%');
        })
            ->select('id', 'name', 'email', 'employee_code', 'designation', 'status', 'created_at', 'updated_at')
            ->get()
            ->map(function ($user) {
                $data = $user->toArray();
                $data['id'] = encrypt_id($user->id);
                return $data;
            });
    }

    public function getDeveloperList()
    {
        return User::where(function ($query) {
            $query
                ->where('role_name', 'developer')
                ->orWhere('role_name', 'like', '%developer%');
        })
            ->select('id', 'name', 'email', 'employee_code', 'designation', 'status', 'created_at', 'updated_at')
            ->get()
            ->map(function ($user) {
                $data = $user->toArray();
                $data['id'] = encrypt_id($user->id);
                return $data;
            });
    }

    public function getapplicantList()
    {
        return User::where(function ($query) {
            $query
                ->whereIn('role_name', ['adc', 'applicant', 'co', 'lra', 'dc'])
                ->orWhere('role_name', 'like', '%adc%')
                ->orWhere('role_name', 'like', '%applicant%')
                ->orWhere('role_name', 'like', '%co%')
                ->orWhere('role_name', 'like', '%lra%')
                ->orWhere('role_name', 'like', '%dc%');
        })
            ->select('id', 'name', 'email', 'employee_code', 'designation', 'status', 'created_at', 'updated_at')
            ->get()
            ->map(function ($user) {
                $data = $user->toArray();
                $data['id'] = encrypt_id($user->id);
                return $data;
            });
    }

    public function deleteUser($id)
    {
        return User::destroy($id);
    }

    public function updateUserStatus($id, $status)
    {
        $user = User::find($id);
        if ($user) {
            $user->status = $status;
            $user->save();
            return $user;
        }
        return null;
    }

    public function storeTeamStructure($dto)
    {
        DB::beginTransaction();

        try {
            // 🔓 Decrypt IDs
            $categoryId = decrypt_id($dto->categoryId);
            $teamLeadId = decrypt_id($dto->teamLeadId);

            // 1️⃣ Insert into category_team_lead
            $existingMapping = CategoryTeamLead::where('category_id', $categoryId)
                ->where('team_lead_id', $teamLeadId)
                ->first();

            if ($existingMapping) {
                // Update existing record instead of inserting
                $existingMapping->update([
                    'updated_at' => now(),
                ]);
            } else {
                CategoryTeamLead::create([
                    'category_id' => $categoryId,
                    'team_lead_id' => $teamLeadId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2️⃣ Insert developers mapping
            // First, remove existing developer mappings for this team lead to prevent duplicates
            TeamLeadDeveloper::where('team_lead_id', $teamLeadId)->delete();

            $developerData = [];

            foreach ($dto->developers as $dev) {
                $developerId = decrypt_id($dev['_id']);

                $developerData[] = [
                    'team_lead_id' => $teamLeadId,
                    'developer_id' => $developerId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            TeamLeadDeveloper::insert($developerData);

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Mapping stored successfully',
                'category_name' => Category::where('id', $categoryId)->first()->name
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteTeamStructure($categoryId, $teamLeadId)
    {
        DB::beginTransaction();

        try {
            // 1. Delete mapping from category_team_lead
            CategoryTeamLead::where('category_id', $categoryId)
                ->where('team_lead_id', $teamLeadId)
                ->delete();

            // 2. Delete developer mappings for this lead
            TeamLeadDeveloper::where('team_lead_id', $teamLeadId)->delete();

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Team structure deleted successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
