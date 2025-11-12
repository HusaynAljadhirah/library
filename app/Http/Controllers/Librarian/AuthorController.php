<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;
use App\QueryBuilders\AuthorQueryBuilder;
use App\Services\FileStorageService;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authors = AuthorQueryBuilder::paginate($request, 10);
        return AuthorResource::collection($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request, FileStorageService $storage)
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            $validated['photo'] = $storage->storePhotoFor('author', $request->file('photo'));
        }

        $author = Author::create($validated);
        return new AuthorResource($author);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $author = Author::findOrFail($id);
        return new AuthorResource($author);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, string $id, FileStorageService $storage)
    {
        $author = Author::findOrFail($id);

        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            $validated['photo'] = $storage->storePhotoFor('author', $request->file('photo'), $author->photo);
        }

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
