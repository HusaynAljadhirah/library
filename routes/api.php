<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Librarian\AuthorController;
use App\Http\Controllers\Librarian\BookController;
use App\Http\Controllers\Librarian\CategoryController;
use App\Http\Controllers\Librarian\BorrowController;
use App\Http\Controllers\Admin\UserController;




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('roles', RoleController::class);
    Route::post('/roles/assign/{userId}', [RoleController::class, 'assignRole']);
    Route::post('/roles/restore/{id}', [RoleController::class, 'restore']);

    Route::apiResource('users', UserController::class);
    Route::post('/users/restore/{id}', [UserController::class, 'restore']);
});

Route::middleware(['auth:sanctum', 'role:librarian'])->group(function () {
   Route::apiResource('authors', AuthorController::class);
    Route::post('/authors/restore/{id}', [AuthorController::class, 'restore']);

    Route::apiResource('books', BookController::class);
    Route::post('/books/restore/{id}', [BookController::class, 'restore']);
    
    Route::apiResource('categories', CategoryController::class);
    Route::post('/categories/restore/{id}', [CategoryController::class, 'restore']);
    
    Route::apiResource('borrows', BorrowController::class);
    Route::post('/borrows/restore/{id}', [BorrowController::class, 'restore']);
});




