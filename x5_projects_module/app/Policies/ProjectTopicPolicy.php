<?php

namespace App\Policies;

use App\Models\ProjectTopic;
use App\Models\User;

class ProjectTopicPolicy
{
    /**
     * Determine whether the user can view any project topics.
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view a specific project topic.
     */
    public function view(User $user, ProjectTopic $projectTopic)
    {
       return true;
    }

    /**
     * Determine whether the user can create a project topic.
     */
    public function create(User $user)
    {
        // Example: Allow only admins or users with a specific role
         return $user->userRole->name === 'admin';
    }

    /**
     * Determine whether the user can update a project topic.
     */
    public function update(User $user, ProjectTopic $projectTopic)
    {
        return $user->userRole->name === 'admin';
    }

    /**
     * Determine whether the user can delete a project topic.
     */
    public function delete(User $user, ProjectTopic $projectTopic)
    {
        return $user->userRole->name === 'admin';
    }
}