<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Batch;

class BatchPolicy
{
    /**
     * Determine whether the user can view any batches.
     */
    public function viewAny(User $user)
    {
        return $user->userRole->name === 'admin';
    }

    /**
     * Determine whether the user can view a specific batch.
     */
    public function view(User $user, Batch $batch)
    {
        return $user->userRole->name === 'admin' || $user->batches->contains($batch);
    }

    /**
     * Determine whether the user can create a batch.
     */
    public function create(User $user)
    {
        return $user->userRole->name === 'admin';
    }

    /**
     * Determine whether the user can update a batch.
     */
    public function update(User $user, Batch $batch)
    {
        return $user->userRole->name === 'admin';
    }

    /**
     * Determine whether the user can delete a batch.
     */
    public function delete(User $user, Batch $batch)
    {
        return $user->userRole->name === 'admin';
    }
    

    /**
     * Determine whether the user can toggle the active status of a batch.
     */
    public function toggleActive(User $user, Batch $batch)
    {
        return $user->userRole->name === 'admin';
    }
}