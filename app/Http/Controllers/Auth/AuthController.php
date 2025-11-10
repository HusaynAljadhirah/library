<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Http\Resources\LoginResource;
use App\Http\Resources\RegisterResource;



class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        // Validate incoming request
        $credentials = $request->validated();

        // Attempt authentication
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return new LoginResource(['user' => $user, 'token' => $token]);
    }

     public function register(RegisterRequest $request)
    {
        // Validate input
        $validated = $request->validated();

        $defaultRole = Role::where('name', 'user')->firstOrFail();

        // Create user with UUID
       $user = User::create($validated + [
            'id' => (string) Str::uuid(),
            'role_id' => $defaultRole->id,
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return new RegisterResource(['user' => $user, 'token' => $token]);
        
    }

    /**
     * Logout user
     */
    
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully',
        ]);
    }
}
