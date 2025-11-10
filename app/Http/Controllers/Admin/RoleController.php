<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Resources\RoleResource;

class RoleController extends Controller
{
    /**
     * List all roles (admin only)
     */
    public function index()
    {
        return response()->json(Role::paginate(10));
    }

    /**
     * Create a new role (admin only)
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        $role = Role::create($validated);

        return new RoleResource($role);
    }

    /**
     * Assign a role to a user (admin only)
     */
    public function assignRole(AssignRoleRequest $request, $userId)
    {
        $validated = $request->validated();

        $user = User::findOrFail($userId);
        $user->role_id = $validated['role_id'];
       
        return new RoleResource($user->role);
    }

    /**
     * Delete a role (admin only)
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Role deleted successfully',
        ]);
    }
}
