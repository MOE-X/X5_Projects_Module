<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EnrollmentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Enrollment endpoint for admins to enroll a student.
     */
    public function enrollStudent(Request $request)
    {
        $currentUser = Auth::user();
        $this->authorize('enrollStudent', User::class);
        
        $validatedData = $request->validate([
            'student_id'  => 'required|exists:users,id',
            'batch_id'    => 'required|exists:batches,id',
            'course_ids'  => 'required|array',
            'course_ids.*'=> 'exists:courses,id'
        ]);
        
        $student = User::findOrFail($validatedData['student_id']);
        if ($student->userRole->name !== 'student') {
            return response()->json(['message' => 'The specified user is not a student.'], 400);
        }
        
        $batchId = $validatedData['batch_id'];
        $courseIds = $validatedData['course_ids'];
        
       foreach ($courseIds as $courseId) {
            $student->batches()->attach($batchId, ['course_id' => $courseId]);
        }         
        return response()->json(['message' => 'Student enrolled successfully in the batch and selected courses.'], 200);
    }

    public function detachCourse(Request $request, Batch $batch, Course $course)
    {
        $currentUser = Auth::user();
        
        // For admins, require a student_id to specify whose enrollment to remove.
        if ($currentUser->userRole->name === 'admin') {
            $validated = $request->validate([
                'student_id' => 'required|exists:users,id'
            ]);
            $studentId = $validated['student_id'];
        }


        // Determine the target student:
        $targetStudentId = $currentUser->userRole->name === 'admin'
            ? $studentId
            : $currentUser->id;
        
        // For a student, authorize using the policy.
        if ($currentUser->userRole->name !== 'admin') {
             $exists = $currentUser->userRole->name === 'student'
                && DB::table('batch_course_user')
                ->where('user_id', $targetStudentId)
                ->where('batch_id', $batch->id)
                ->where('course_id', $course->id)
                ->exists();
        } else {
            // For admins, manually check that the target student actually has this course enrollment.
            $exists = DB::table('batch_course_user')
                        ->where('user_id', $targetStudentId)
                        ->where('batch_id', $batch->id)
                        ->where('course_id', $course->id)
                        ->exists();
           
        }
        if (!$exists) {
            return response()->json(['message' => 'No matching course enrollment found.'], 404);
        }
        // Proceed with deleting the enrollment from the pivot table.
        $deleted = DB::table('batch_course_user')
                    ->where('user_id', $targetStudentId)
                    ->where('batch_id', $batch->id)
                    ->where('course_id', $course->id)
                    ->delete();
        
        if ($deleted) {
            return response()->json(['message' => 'Course detached successfully.'], 200);
        }
        
        return response()->json(['message' => 'Error detaching course.'], 500);
    } 

    public function detachStudentFromBatch(Request $request, Batch $batch)
    {
        $currentUser =Auth::user();
        
        // If an admin is performing the action, require a student_id.
        if ($currentUser->userRole->name === 'admin') {
            $validated = $request->validate([
                'student_id' => 'required|exists:users,id'
            ]);

            $studentId = $validated['student_id'];
        }

        $targetStudentId = $currentUser->userRole->name === 'admin'
            ? $studentId
            : $currentUser->id;

        if ($currentUser->userRole->name !== 'admin') {
           $exists = $currentUser->userRole->name === 'student'
                && DB::table('batch_course_user')
                ->where('user_id', $targetStudentId)
                ->where('batch_id', $batch->id)
                ->exists();
        } else {
            // Admins have access via 'before', but we still check if the enrollment exists.
            $exists = DB::table('batch_course_user')
                        ->where('user_id', $targetStudentId)
                        ->where('batch_id', $batch->id)
                        ->exists();
   
        }
        if (!$exists) {
                return response()->json(['message' => 'No matching batch enrollment found.'], 404);
        }

        DB::table('batch_course_user')
        ->where('user_id', $targetStudentId)
        ->where('batch_id', $batch->id)
        ->delete();

        return response()->json(['message' => 'Student detached from batch successfully.'], 200);
    }

    public function viewEnrolledCourses(Request $request, User $user)
    {
        $this->authorize('viewEnrolledCourses', $user); 

        $courses = $user->courses()
            ->whereHas('batches', function ($query) {
                $query->where('is_active', true);
            })
            ->get();

        return response()->json([
            'message' => 'Enrolled courses retrieved successfully.',
            'data'    => $courses,
        ], 200);
    }

    /**
     * Enroll a student in a project with a specified role.
     * If an admin is enrolling a student, 'student_id' must be provided.
     * Students can only enroll themselves.
     */
    public function enrollInProject(Request $request, Project $project)
    {
        // Prevent enrollment if project is closed
        if (!$project->is_open) {
            return response()->json(['message' => 'Enrollment is closed for this project.'], 403);
        }

        // Validate role selection
        $validatedData = $request->validate([
            'role_id' => 'required|integer|exists:roles,id'
        ]);

        $currentUser = Auth::user();

        // Determine target student
        if ($currentUser->userRole->name === 'admin') {
            $adminData = $request->validate([
                'student_id' => 'required|exists:users,id'
            ]);
            $student = User::findOrFail($adminData['student_id']);
        } else {
            $student = $currentUser;
        }

        $roleId = $validatedData['role_id'];

        // Prevent duplicate role assignments
        $exists = DB::table('project_role_user')
            ->where('project_id', $project->id)
            ->where('user_id', $student->id)
            ->where('role_id', $roleId)
            ->exists();
            
        if ($exists) {
            return response()->json([
                'message' => 'The student already has this role assigned in the project.'
            ], 400);
        }

        // Attach role to student
        $project->users()->attach($student->id, ['role_id' => $roleId]);

        return response()->json([
            'message' => 'Student enrolled in project successfully with the specified role.'
        ], 200);
    }

    /**
     * Remove a specific role from a student in a project.
     * Admins can remove any student's role, while students can remove only their own role.
     */
    public function detachRoleFromProject(Request $request, Project $project)
    {
        $currentUser = Auth::user();

        // Validate role selection
        $validated = $request->validate([
            'role_id' => 'required|integer|exists:roles,id'
        ]);
        $roleId = $validated['role_id'];

        // Determine target student
        if ($currentUser->userRole->name === 'admin') {
            $adminData = $request->validate([
                'student_id' => 'required|exists:users,id'
            ]);
            $studentId = $adminData['student_id'];
        } else {
            $studentId = $currentUser->id;
        }

        // Check if student has the specified role in the project
        $exists = DB::table('project_role_user')
            ->where('project_id', $project->id)
            ->where('user_id', $studentId)
            ->where('role_id', $roleId)
            ->exists();

        if (!$exists) {
            return response()->json([
                'message' => 'No enrollment found for the specified role in this project.'
            ], 404);
        }

        // Remove specific role from pivot table
        DB::table('project_role_user')
            ->where('project_id', $project->id)
            ->where('user_id', $studentId)
            ->where('role_id', $roleId)
            ->delete();

        return response()->json([
            'message' => 'Role removed successfully from the project.'
        ], 200);
    }
}
