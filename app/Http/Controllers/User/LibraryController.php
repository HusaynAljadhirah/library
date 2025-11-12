<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BorrowResource;
use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\BorrowService;
use App\Services\FileStorageService;

class LibraryController extends Controller
{
    public function requestAccess(Request $request, Book $book, BorrowService $borrows)
    {
        $borrow = $borrows->getOrCreateActiveBorrow($request->user(), $book);
        return (new BorrowResource($borrow))
            ->response()
            ->setStatusCode(201);
    }


    // Stream or download the book's PDF if the user has access (borrowed and not returned)
    public function view(Request $request, Book $book, FileStorageService $storage)
    {
        return $storage->streamPdf($book->pdf_path, 'local');
    }
}
