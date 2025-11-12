<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\QueryBuilders\RoleQueryBuilder;

class RoleController extends Controller
{
    /**
     * List all roles (admin only)
     */
    public function index(Request $request)
    {
        $roles = RoleQueryBuilder::build()->paginate(10);
        return RoleResource::collection($roles);
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
    $user->save();
    return new UserResource($user->load('role'));
    }

    /**
     * Delete a role (admin only)
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json([
            'message' => 'Role deleted successfully.'
        ]);
    }

    public function restore(string $id)
    {
        $role = Role::withTrashed()->findOrFail($id);
        $role->restore();
        return new RoleResource($role);

    }
}
