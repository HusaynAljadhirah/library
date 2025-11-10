<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
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
    public function store(StoreAuthorRequest $request)
    {
        $validated = $request->validated();

        // photo soon

        $author = Author::create($validated);
         return new AuthorResource($author);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $author = Author::findOrFail($id);
        return response()->json($author);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, string $id)
    {
        $author = Author::findOrFail($id);

        $validated = $request->validated();

        // if ($request->hasFile('photo')) {
        //     $path = $request->file('photo')->store('authors', 'public');
        //     $validated['photo'] = $path;
        // }

        $author->update($validated);

        return new AuthorResource($author);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $author = Author::findOrFail($id);
        $author->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Author deleted successfully',
        ]);
    }

    public function restore(string $id)
{
    // Find the author including soft-deleted ones
    $author = Author::withTrashed()->findOrFail($id);

    // Restore the record
    $author->restore();

    return response()->json([
        'status'  => 'success',
        'message' => 'Author restored successfully',
        'author'  => $author,
    ]);
}

}
