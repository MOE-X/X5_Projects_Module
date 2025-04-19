<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectTopic;

class ProjectTopicController extends Controller
{
    public function index()
    {
        // Logic to get all project topics
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
