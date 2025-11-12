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

        // Determine PDF path/size: use provided pdf_path or store uploaded pdf
        $pdfPath = $validated['pdf_path'] ?? null;
        $pdfSize = $validated['pdf_size'] ?? null;
        if (!$pdfPath && $request->hasFile('pdf')) {
            $storedPdf = $storage->storePdf($request->file('pdf'), $this->pdfDisk, 'books/pdfs');
            $pdfPath = $storedPdf['path'];
            $pdfSize = $storedPdf['size'];
        }

        // Handle cover image upload if provided
        if ($request->hasFile('cover_image')) {
            $coverPath = $storage->storeImage($request->file('cover_image'), $this->imageDisk, 'books/covers');
            $validated['cover_image'] = $coverPath;
        }

        // Merge computed pdf_path/pdf_size into payload
        if ($pdfPath) {
            $validated['pdf_path'] = $pdfPath;
        }
        if ($pdfSize) {
            $validated['pdf_size'] = $pdfSize;
        }

        $book = Book::create($validated);
        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);

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
    public function update(UpdateBookRequest $request, string $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validated();

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
