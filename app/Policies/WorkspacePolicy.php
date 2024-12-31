<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkspacePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Workspace  $workspace
     * @return mixed
     */
    public function view(User $user, Workspace $workspace)
    {
        return $user->id === $workspace->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Workspace  $workspace
     * @return mixed
     */
    public function update(User $user, Workspace $workspace)
    {
        return $user->id === $workspace->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Workspace  $workspace
     * @return mixed
     */
    public function delete(User $user, Workspace $workspace)
    {
        return $user->id === $workspace->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Workspace  $workspace
     * @return mixed
     */
    public function restore(User $user, Workspace $workspace)
    {
        return $user->id === $workspace->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Workspace  $workspace
     * @return mixed
     */
    public function forceDelete(User $user, Workspace $workspace)
    {
        return $user->id === $workspace->user_id;
    }

    /**
     * Determine whether the user can manage roles for the workspace.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Workspace  $workspace
     * @return mixed
     */
    public function isOwner(User $user, Workspace $workspace)
    {
        return $user->id === $workspace->user_id;
    }

    /**
     * Determine whether the user can manage roles for the workspace.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Workspace  $workspace
     * @return mixed
     */
    public function manageRoles(User $user)
    {
        $userRole = $user->roles()->first();
        return $userRole && in_array($userRole->name, ['Project Manager', 'System Analyst']);
    }
}
