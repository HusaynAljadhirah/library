<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Admin\RoleController;

// Route::middleware('auth:sanctum')->group(function () {

//     Route::get('/admin', function () {
//         return response()->json(['message' => 'Welcome Admin']);
//     })->middleware('role:admin');

//     Route::get('/librarian', function () {
//         return response()->json(['message' => 'Welcome Librarian']);
//     })->middleware('role:librarian');

//     Route::get('/user', function () {
//         return response()->json(['message' => 'Welcome User']);
//     })->middleware('role:user');
// });



Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('roles', RoleController::class);
});

Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'role' => $user->role->name,
    ]);
});
