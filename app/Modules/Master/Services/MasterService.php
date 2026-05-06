<?php

namespace App\Modules\Master\Services;

use App\Modules\Master\Repositories\MasterRepository;

class MasterService
{
    public function __construct(
        protected MasterRepository $repo
    ) {}

    public function createService($dto)
    {
        return $this->repo->storeService($dto);
    }

    public function getService()
    {
        return $this->repo->getService();
    }

    public function createCategory($dto)
    {
        return $this->repo->storeCategory($dto);
    }

    public function assignTeamLead($dto)
    {
        return $this->repo->assignTeamLead($dto);
    }

    public function deleteService($id)
    {
        return $this->repo->deleteService($id);
    }

    public function getTeamLeadStructure()
    {
        return $this->repo->getTeamLeadStructuredetails();
    }

    public function getTeamLeadList()
    {
        return $this->repo->getTeamLeadList();
    }

    public function getDeveloperList()
    {
        return $this->repo->getDeveloperList();
    }

    public function getapplicantList()
    {
        return $this->repo->getapplicantList();
    }

    public function deleteUser($id)
    {
        return $this->repo->deleteUser($id);
    }

    public function updateUserStatus($id, $status)
    {
        return $this->repo->updateUserStatus($id, $status);
    }

    public function storeTeamStructure($dto)
    {
        return $this->repo->storeTeamStructure($dto);
    }

    public function deleteTeamStructure($categoryId, $teamLeadId)
    {
        return $this->repo->deleteTeamStructure($categoryId, $teamLeadId);
    }
}
