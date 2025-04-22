<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;
    /**
     * List all roles (Admin Only).
     */
    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::paginate(10);

        return response()->json($roles, 200);
    }

    /**
     * Store a new role (Admin Only).
     */
    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create($validatedData);

        return response()->json([
            'message' => 'Role created successfully.',
            'data'    => $role,
        ], 201);
    }

    /**
     * View a specific role (Admin Only).
     */
    public function show(Role $role)
    {
        $this->authorize('view', $role);

        return response()->json($role, 200);
    }

    /**
     * Update a role (Admin Only).
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update($validatedData);

        return response()->json([
            'message' => 'Role updated successfully.',
            'data'    => $role,
        ], 200);
    }

    /**
     * Delete a role (Admin Only).
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully.'], 200);
    }
}
