<?php

namespace App\Http\Controllers\API;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a paginated list of tasks.
     * 
     * Admins see all tasks; students see only those tasks whose project
     * includes them as a member.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);
        
        $user = $request->user();
        if ($user->userRole->name === 'admin') {
            $tasks = Task::paginate(10);
        } else {
            // For students: filter tasks based on projects in which they are enrolled.
            $tasks = Task::whereHas('project.users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->paginate(10);
        }
        
        return response()->json($tasks, 200);
    }

    /**
     * Display a specific task.
     */
    public function show(Task $task, Request $request)
    {
        $this->authorize('view', $task);
        return response()->json($task, 200);
    }

    /**
     * Create a new task.
     * Only admins can perform this action.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Task::class);

        $validatedData = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'required|string',
            'task_type_id'   => 'required|exists:task_types,id',
            'video_link'     => 'nullable|string|max:255',
            'project_id'     => 'required|exists:projects,id',
            'task_status_id' => 'required|exists:task_statuses,id',
            'due_date'       => 'required|date',
            'result'         => 'nullable|string',
        ]);

        $task = Task::create($validatedData);

        return response()->json([
            'message' => 'Task created successfully.',
            'data'    => $task,
        ], 201);
    }

    /**
     * Update an existing task.
     * Only admins are allowed to update tasks.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validatedData = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'description'    => 'sometimes|required|string',
            'task_type_id'   => 'sometimes|required|exists:task_types,id',
            'video_link'     => 'nullable|string|max:255',
            'task_status_id' => 'sometimes|required|exists:task_statuses,id',
            'due_date'       => 'sometimes|required|date',
            'result'         => 'nullable|string',
        ]);

        $task->update($validatedData);

        return response()->json([
            'message' => 'Task updated successfully.',
            'data'    => $task,
        ], 200);
    }

    /**
     * Delete a task.
     * Only admins are allowed to delete tasks.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully.'], 200);
    }
}
