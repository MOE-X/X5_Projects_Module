<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Batch;
use App\Models\Course;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(){
        $this->authorize('viewAny', User::class);

        $studentRoleId = UserRole::where('name', 'student')->value('id');

        $users = User::with('userRole')
                    ->where('is_active', true)
                    ->where('user_role_id', $studentRoleId)
                    ->paginate(10);

        return response()->json($users, 200);
    } 

    /**
     * Store a newly created user.
     * Only admins are allowed to create users.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validatedData = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users',
            'password'        => 'required|string|min:6|max:32',
            'password_confirmation' => 'required|same:password',
            'phone'           => 'required|string|max:20',
            'dob'             => 'required|date',
            'gender_id'       => 'required|exists:genders,id',
            'user_role_id'    => 'required|exists:user_roles,id',
        ]);
        
        $validatedData['password'] = bcrypt($validatedData['password']);
        $user = new User($validatedData);
        $user->save();
        return response()->json([
            'message' => 'User created successfully.',
            'data'    => $user
        ], 201);
    }

    /**
     * Display the specified user.
     * Admins can view any user; students can only view their own record.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return response()->json($user, 200);
    }


    /**
     * Update the specified user.
     * Admins can update any record; students can update only their own profile.
     */
    public function update(Request $request, User $user){
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name'            => 'sometimes|required|string|max:255',
            'email'           => ['sometimes','required','email', Rule::unique('users')->ignore($user->id)],
            'password'        => 'sometimes|nullable|string|min:6|max:32',
            'phone'           => 'sometimes|required|string|max:20',
            'dob'             => 'sometimes|required|date',
            'gender_id'       => 'sometimes|required|exists:genders,id',
            'user_role_id'    => 'sometimes|required|exists:user_roles,id',
            'github_link'     => 'sometimes|nullable|url|string',
            'linkedin_link'   => 'sometimes|nullable|url|string',
            'is_completed'    => 'sometimes|boolean',
            'is_active'       => 'sometimes|boolean',
        ]);

        // Prevent students from changing their role
        if ($request->user()->userRole->name !== 'admin') {
            unset($validated['user_role_id']);
        }

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully.',
            'data'    => $user->fresh(),
        ], 200);
    }



    /**
     * Remove the specified user.
     * Only admins are allowed to delete user records.
     */
    public function destroy(User $user){
        $this->authorize('delete', $user);
        
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.'], 200);
    }

    
}
