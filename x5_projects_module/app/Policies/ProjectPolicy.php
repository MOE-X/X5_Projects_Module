<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any projects.
     */
    public function viewAny(User $user)
    {
        // Example: Allow only admins to view all projects
        return in_array($user->userRole->name, ['admin', 'student']);
    }

    /**
     * Determine whether the user can view a specific project.
     */
    public function view(User $user, Project $project)
    {
        // Example: Allow if the user is an admin or the owner of the project
        return in_array($user->userRole->name, ['admin', 'student']);
    }

    /**
     * Determine whether the user can create a project.
     */
    public function create(User $user)
    {
       return in_array($user->userRole->name, ['admin', 'student']);
    }

    /**
     * Determine whether the user can update a project.
     */
    public function update(User $user, Project $project)
    {
        // Example: Allow if the user is an admin or the owner of the project
        return $user->userRole->name === 'admin' || $user->id === $project->user_id;
    }

    /**
     * Determine whether the user can delete a project.
     */
    public function delete(User $user, Project $project)
    {
        // Example: Allow if the user is an admin or the owner of the project
        return $user->userRole->name === 'admin' || $user->id === $project->user_id;
    }
}