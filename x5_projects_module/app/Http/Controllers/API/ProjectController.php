<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display all projects (Admins & Students).
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::paginate(10);

        return response()->json($projects, 200);
    }

    /**
     * View a specific project (Admins & Students).
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return response()->json($project, 200);
    }

    /**
     * Store a new project (Admins & Students).
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validatedData = $request->validate([
            'name'             => 'required|string|max:255|unique:projects,name',
            'description'      => 'nullable|string',
            'project_topic_id' => 'required|exists:project_topics,id',
            'start_date'       => 'required|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'production_link'  => 'nullable|string|max:255',
            'web_github_link'  => 'nullable|string|max:255',
            'mobile_github_link' => 'nullable|string|max:255',
            'logo'             => 'nullable|string|max:255',
            'role_id'          => 'required|exists:roles,id',
	        'is_open'          => 'required|boolean' 
        ]);

        $validatedData['user_id'] = $request->user()->id;

        $project = Project::create($validatedData);
 	
	// Automatically enroll the student into the project with the selected role
        $project->users()->attach($request->user()->id, ['role_id' => $validatedData['role_id']]);

        return response()->json([
            'message' => 'Project created successfully.',
            'data'    => $project,
        ], 201);
    }

    /**
     * Update an existing project (Admins & Assigned Students).
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validatedData = $request->validate([
            'name'         => 'sometimes|required|string|max:255|unique:projects,name,' . $project->id,
            'description'  => 'nullable|string',
            'start_date'   => 'sometimes|required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'production_link'  => 'nullable|string|max:255',
            'web_github_link'  => 'nullable|string|max:255',
            'mobile_github_link' => 'nullable|string|max:255',
            'logo'         => 'nullable|string|max:255',
 	    'is_open'      => 'sometimes|required|boolean'
        ]);

        $project->update($validatedData);

        return response()->json([
            'message' => 'Project updated successfully.',
            'data'    => $project,
        ], 200);
    }

    /**
     * Delete a project (Admin Only).
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(['message' => 'Project deleted successfully.'], 200);
    }
}