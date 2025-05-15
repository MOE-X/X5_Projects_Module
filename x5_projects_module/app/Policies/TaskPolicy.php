<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any tasks.
     * Both admins and students have access, though students will have their view limited in the controller.
     */
    public function viewAny(User $user)
    {
        return in_array($user->userRole->name, ['admin', 'student']);
    }

    /**
     * Determine whether the user can view the specific task.
     *
     * Admins can view all tasks.
     * Students can view the task only if they are assigned to the task's project.
     */
    public function view(User $user, Task $task)
    {
        if ($user->userRole->name === 'admin') {
            return true;
        }

        // Check if the user is a student and is assigned to the task's project
        return $task->project->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create tasks.
     * Only admins can create tasks.
     */
    public function create(User $user)
    {
        return $user->userRole->name === 'admin';
    }

    /**
     * Determine whether the user can update a task.
     * Only admins are allowed to update tasks.
     */
    public function update(User $user, Task $task)
    {
        return $user->userRole->name === 'admin';
    }

    /**
     * Determine whether the user can delete a task.
     * Only admins can delete tasks.
     */
    public function delete(User $user, Task $task)
    {
        return $user->userRole->name === 'admin';
    }
}
