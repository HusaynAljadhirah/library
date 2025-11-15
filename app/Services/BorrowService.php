<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;

class BorrowService
{
    public function hasActiveBorrow(User $user, Book $book): bool
    {
        return Borrow::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();
    }

    public function getOrCreateActiveBorrow(User $user, Book $book): Borrow
    {
        $existing = Borrow::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->whereNull('returned_at')
            ->first();

        if ($existing) {
            return $existing;
        }

        return Borrow::create([
            'user_id'     => $user->id,
            'book_id'     => $book->id,
            'status'      => 'pending',  // Changed to pending for approval
            'borrowed_at' => now(),
            'due_at'      => now()->addDays(14),
        ]);
    }
}
