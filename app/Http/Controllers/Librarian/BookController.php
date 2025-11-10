<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Support\Str;

class BookController extends Controller
{

    protected string $pdfDisk = 's3';
    protected string $imageDisk = 'public';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return AuthorResource::collection(Author::paginate(10));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();

        $pdf = $request->file('pdf');
        $pdfPath = $pdf->storeAs('books/pdfs', Str::uuid() . '.' . $pdf->getClientOriginalExtension(), $this->pdfDisk);
       
        
       $coverPath = null;
        if ($request->hasFile('cover_image')) { 
            $coverImage = $request->file('cover_image');
            $coverPath = $coverImage->storeAs('books/covers', Str::uuid() . '.' . $coverImage->getClientOriginalExtension(), $this->imageDisk);
        }

        $book = Book::create($validated + [
            'pdf_path' => $pdfPath,
            'cover_image_path' => $coverPath,
            'pages' => $validated['pages'] ?? null,
            'pdf_size' => $pdf->getSize(),
        ]);
        

        return new BookResource($book);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::findOrFail($id);
        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, string $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validated();

        $book->update($validated);

        return new UpdateBookRequest($book);
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
