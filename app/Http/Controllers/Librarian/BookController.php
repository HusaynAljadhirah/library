<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\UpdateBookResource;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Support\Str;
use App\Http\Resources\BookResource;
use App\QueryBuilders\BookQueryBuilder;
use App\Services\FileStorageService;


class BookController extends Controller
{

    protected string $pdfDisk = 'local';
    protected string $imageDisk = 'public';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $books = BookQueryBuilder::build()->paginate(10);
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request, FileStorageService $storage)
    {
        $validated = $request->validated();

        // Require a PDF upload and store it
        $storedPdf = $storage->storePdfFor('book', $request->file('pdf'), null, $this->pdfDisk);
        $validated['pdf_path'] = $storedPdf['path'];
        $validated['pdf_size'] = $storedPdf['size'];

        // Handle cover image upload if provided
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $storage->storePhotoFor('book', $request->file('cover_image'));
        }

        // Merge cover image handled above; pdf_path/pdf_size already set

        $book = Book::create($validated);

        return new BookResource($book);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    $book = Book::findOrFail($id);
    return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, string $id, FileStorageService $storage)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $storage->storePhotoFor('book', $request->file('cover_image'), $book->cover_image);
        }

        $book->update($validated);

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Book deleted successfully',
        ]);
    }

    /**
     * Restore the specified resource from soft deletion.
     */   
    public function restore(string $id)
    {
        $book = Book::withTrashed()->findOrFail($id);
        $book->restore();
        return response()->json([
            'status'  => 'success',
            'message' => 'Book restored successfully',
            'book'    => $book,
        ]);
    }
}
