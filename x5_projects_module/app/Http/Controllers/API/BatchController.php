<?php

namespace App\Http\Controllers\API;
use App\Models\Batch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BatchController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the batches.
     */

    public function index()
    {
        $this->authorize('viewAny', Batch::class);
        $batches = Batch::paginate(10);
        return response()->json($batches, 200);
    }
    /**
     * Store a newly created batch in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Batch::class);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'required|boolean',
        ]);

        $batch = Batch::create($validatedData);

        return response()->json([
            'message' => 'Batch created successfully.',
            'data' => $batch,
        ], 201);
    }

    /**
     * Display the specified batch.
     */
    public function show(Batch $batch)
    {
        $this->authorize('view', $batch);
        return response()->json($batch, 200);
    }

    /**
     * Update the specified batch in storage.
     */
    public function update(Request $request, Batch $batch)
    {
        $this->authorize('update', $batch);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'required|boolean',
        ]);

        $batch->update($validatedData);

        return response()->json([
            'message' => 'Batch updated successfully.',
            'data' => $batch,
        ], 200);
    }

    /**
     * Remove the specified batch from storage.
     */
    public function destroy(Batch $batch)
    {
        $this->authorize('delete', $batch);
        $batch->delete();

        return response()->json([
            'message' => 'Batch deleted successfully.',
        ], 200);
    }

}