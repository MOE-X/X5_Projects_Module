<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        $this->authorize('viewAny', Course::class);
        $courses = Course::paginate(10);
        return response()->json($courses, 200);
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Course::class);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $course = Course::create($validatedData);

        return response()->json([
            'message' => 'Course created successfully.',
            'data' => $course,
        ], 201);
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $this->authorize('view', $course);
        return response()->json($course, 200);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course)
    {
        $this ->authorize('update', $course);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        
        ]);

        $course->update($validatedData);

        return response()->json([
            'message' => 'Course updated successfully.',
            'data' => $course,
        ], 200);
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course)
    {
        $this ->authorize('delete', $course);
        // Check if the course is associated with any batches
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully.',
        ], 200);
    }
}