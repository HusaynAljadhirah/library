<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\QueryBuilders\UserQueryBuilder;
use App\Http\Requests\UserIndexRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  
public function index(UserIndexRequest $request)
{
    // Using UserQueryBuilder to handle filtering and sorting
    $users = UserQueryBuilder::build()->paginate(10);
    return UserResource::collection($users);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        $defaultRole = Role::where('name', 'user')->firstOrFail();
        $validated['role_id'] = $defaultRole->id;
        
        $user = User::create($validated);
        
        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // pass??
        $user = User::findOrFail($id);

        $validated = $request->validated();

        $user->update($validated);

        return new UserResource($user);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return new UserResource($user);

    }

    public function restore(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return new UserResource($user);

    }
}
