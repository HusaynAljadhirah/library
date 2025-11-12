<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Borrow;
use App\Http\Resources\BorrowResource;
use App\Http\Requests\StoreBorrowRequest;
use App\Http\Requests\UpdateBorrowRequest;
use App\QueryBuilders\BorrowQueryBuilder;


class BorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
     {

        $borrows = BorrowQueryBuilder::build()->paginate(10);
        return BorrowResource::collection($borrows);

     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowRequest $request)
    {
        $validated = $request->validated();

        $borrow = Borrow::create($validated);
        return new BorrowResource($borrow);   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $borrow = Borrow::findOrFail($id);
        return response()->json($borrow);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBorrowRequest $request, string $id)
    {
        $borrow = Borrow::findOrFail($id);

        $validated = $request->validated();

        $borrow->update($validated);

        return new BorrowResource($borrow);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $borrow = Borrow::findOrFail($id);
        $borrow->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Borrow record deleted successfully',
        ], 200);
    }

    //recover soft deleted borrow record
    public function restore(string $id)
    {
        $borrow = Borrow::withTrashed()->findOrFail($id);
        $borrow->restore();
        return response()->json([
            'status'  => 'success',
            'message' => 'Borrow record restored successfully',
            'borrow'    => $borrow,
        ], 200);
    }
}
