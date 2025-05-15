<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectTopic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectTopicController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        // Logic to get all project topics
        $this ->authorize('viewany', ProjectTopic::class);
        $topics = ProjectTopic::all();

        if ($topics->isEmpty()) {
            return response()->json(['message' => 'No project topics found'], 404);
        }
        return response()->json([
            'message' => 'Project topics',
            'data' => $topics
        ], 200);
    }

    public function show($id)
    {
        // Logic to get a specific project topic
        $this ->authorize('view', ProjectTopic::class);
        $topic = ProjectTopic::find($id);
        if (!$topic) {
            return response()->json(['message' => 'Project topic not found'], 404);
        }
        return response()->json([
            'message' => 'Project topic',
            'data' => $topic
        ], 200);
    }

    public function store(Request $request)
    {
        // Logic to create a new project topic

        $this ->authorize('create', ProjectTopic::class);
        $validatedData = $request->validate([
            'name' => 'required|string|min:3',
        ]);
        $existingTopic = ProjectTopic::where('name', $validatedData['name'])->first();
        if ($existingTopic) {
            return response()->json(['message' => 'Project topic already exists'], 409);
        }

        $topic = ProjectTopic::create($validatedData);

        return response()->json([
            'message' => 'Project topic created successfully',
            'data' => $topic
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Logic to update a project topic
        $this ->authorize('update', ProjectTopic::class);
        $validatedData = $request->validate([
            'name' => 'required|string|min:3',
        ]);

        $topic = ProjectTopic::find($id);
        if (!$topic) {
            return response()->json(['message' => 'Project topic not found'], 404);
        }
        
        $topic->update($validatedData);

        return response()->json([
            'message' => 'Project topic updated successfully',
            'data' => $topic
        ], 200);
    }

    public function destroy($id)
    {
        // Logic to delete a project topic
        $this ->authorize('delete', ProjectTopic::class);
        $topic = ProjectTopic::find($id);
        if (!$topic) {
            return response()->json(['message' => 'Project topic not found'], 404);
        }

        $topic->delete();

        return response()->json([
            'message' => 'Project topic deleted successfully'
        ], 200);
    }
}
