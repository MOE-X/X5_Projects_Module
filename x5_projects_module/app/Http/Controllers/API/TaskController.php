<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->userRole->name === 'admin') {
            // Admin: fetch all tasks
            $tasks = Task::all();
        } else {
            // Regular user: fetch only their tasks
            $project = $user->projects->first;
            $tasks = $project->tasks;
        }

        return response()->json($tasks);
    }

    public function show($id)
    {
        $user = Auth::user();
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $project = $task->project;
        $users = $project->users;
        if ($user->userRole->name === 'admin' || $users->contains($user)) {
            // Admin or user in the project: show task details
            return response()->json($task);
        } else {
            // Unauthorized access
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    }

    public function store(Request $request)
    {

        // Logic to create a new task
        $validatedData = $request->validate([
            'name' => 'required|string|min:3',
            'description' => 'required|string|min:3',
            'task_type_id' => 'required|exists:task_types,id',
            'video_link' => 'nullable|url',
            'project_id' => 'required|exists:projects,id',
            'task_status_id' => 'required|exists:task_statuses,id',
            'due_date' => 'required|date',
            'result' => 'nullable|string'
        ]);

        $user = Auth::user();

        if ($user->userRole->name === 'admin') {
            // Admin: Create a task for any project
            $task = Task::create($validatedData);
        } else {
            // Regular user: fetch only their tasks
            return response()->json(['message' => 'Unauthorized'], 403);
        }


        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        // Logic to update a task
        $validatedData = $request->validate([
            'name' => 'nullable|string|min:3',
            'description' => 'nullable|string|min:3',
            'task_type_id' => 'nullable|exists:task_types,id',
            'video_link' => 'nullable|url',
            'project_id' => 'nullable|exists:projects,id',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'due_date' => 'nullable|date',
            'result' => 'nullable|string'
        ]);

        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        if ($user->userRole->name != 'admin'){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->update($validatedData);
        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        if ($user->userRole->name != 'admin'){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}