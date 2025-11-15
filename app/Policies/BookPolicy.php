<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;

class BookPolicy
{
    public function view(User $user, Book $book): bool
    {
        return Borrow::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['approved', 'borrowed'])
            ->whereNull('returned_at')
            ->exists();
    }
}
