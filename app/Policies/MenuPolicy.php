<?php

namespace App\Policies;

use App\Models\User;

class MenuPolicy
{
    /**
     * Check if user can view dashboard
     *
     * @param User $user
     * @return bool
     */
    public function viewDashboard(User $user)
    {
        return true; // Everyone can view dashboard
    }

    /**
     * Check if user can view master data
     *
     * @param User $user
     * @return bool
     */
    public function viewMasterData(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Check if user can view branches
     *
     * @param User $user
     * @return bool
     */
    public function viewBranches(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager']) || $user->hasPermissionTo('view-branches');
    }

    /**
     * Check if user can view CRM
     *
     * @param User $user
     * @return bool
     */
    public function viewCrm(User $user)
    {
        return $user->hasAnyRole(['admin', 'sales', 'manager']);
    }

    /**
     * Check if user can view WQS
     *
     * @param User $user
     * @return bool
     */
    public function viewWqs(User $user)
    {
        return $user->hasAnyRole(['admin', 'warehouse', 'manager']);
    }

    /**
     * Check if user can view SCM
     *
     * @param User $user
     * @return bool
     */
    public function viewScm(User $user)
    {
        return $user->hasAnyRole(['admin', 'logistics', 'manager']);
    }

    /**
     * Check if user can view ACT
     *
     * @param User $user
     * @return bool
     */
    public function viewAct(User $user)
    {
        return $user->hasAnyRole(['admin', 'accounting', 'manager']);
    }

    /**
     * Check if user can view FIN
     *
     * @param User $user
     * @return bool
     */
    public function viewFin(User $user)
    {
        return $user->hasAnyRole(['admin', 'finance', 'manager']);
    }

    /**
     * Check if user can manage settings
     *
     * @param User $user
     * @return bool
     */
    public function manageUsers(User $user)
    {
        return $user->hasAnyRole(['admin']) || $user->hasPermissionTo('manage-users');
    }

    public function manageRoles(User $user)
    {
        return $user->hasAnyRole(['admin']) || $user->hasPermissionTo('manage-roles');
    }

    public function managePermissions(User $user)
    {
        return $user->hasAnyRole(['admin']) || $user->hasPermissionTo('manage-permissions');
    }
}