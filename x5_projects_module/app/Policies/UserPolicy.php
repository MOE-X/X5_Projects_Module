<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Batch;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    
    /**
     * This "before" method runs before all other checks.
     * If the user is an admin, grant full access.
     */
    public function before(User $user, $ability)
    {
        if ($user->userRole->name === 'admin') {
            return true; // Admin is allowed to perform any action.
        }
    }

    /**
     * Determine whether the user can view any users.
     * Only admins can view all users.
     */
    public function viewAny(User $user)
    {
        return false;
    }
    /**
     * Determine whether the user can view the given model.
     * A student can only view their own record.
     */
    public function view(User $user, User $model)
    {
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create a new user.
     * Only admins can create users.
     * 
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the given model.
     * A student can only update their own profile.
     */
    public function update(User $user, User $model)
    {
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the given model.
     * Only an admin can delete users. Since the "before" method grants admins
     * blanket access, students here cannot delete.
     */
    public function delete(User $user, User $model)
    {
        return false;
    }


    public function enrollstudent()
    {
        return false;
    }


    /**
    * Users can view their own enrolled courses, admins can view any user’s courses.
    */
    public function viewEnrolledCourses(User $user, User $model)
    {
        return $user->id === $model->id;
    }

}
