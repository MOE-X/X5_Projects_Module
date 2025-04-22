<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Batch;
use App\Models\Course;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(){
    $this->authorize('viewAny', User::class);

    $studentRoleId = UserRole::where('name', 'student')->value('id');

    $users = User::with('userRole')
                 ->where('is_active', true)
                 ->where('user_role_id', $studentRoleId)
                 ->paginate(10);

    return response()->json($users, 200);
} 

    /**
     * Store a newly created user.
     * Only admins are allowed to create users.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validatedData = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users',
            'password'        => 'required|string|min:6|max:32',
            'password_confirmation' => 'required|same:password',
            'phone'           => 'required|string|max:20',
            'dob'             => 'required|date',
            'gender_id'       => 'required|exists:genders,id',
            'user_role_id'    => 'required|exists:user_roles,id',
        ]);
        
        $validatedData['password'] = bcrypt($validatedData['password']);
        $user = new User($validatedData);
        $user->save();
        return response()->json([
            'message' => 'User created successfully.',
            'data'    => $user
        ], 201);
    }

    /**
     * Display the specified user.
     * Admins can view any user; students can only view their own record.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return response()->json($user, 200);
    }


    /**
     * Update the specified user.
     * Admins can update any record; students can update only their own profile.
     */
    public function update(Request $request, User $user)
{
    $this->authorize('update', $user);

    $validated = $request->validate([
        'name'            => 'sometimes|required|string|max:255',
        'email'           => ['sometimes','required','email', Rule::unique('users')->ignore($user->id)],
        'password'        => 'sometimes|nullable|string|min:6|max:32',
        'phone'           => 'sometimes|required|string|max:20',
        'dob'             => 'sometimes|required|date',
        'gender_id'       => 'sometimes|required|exists:genders,id',
        'user_role_id'    => 'sometimes|required|exists:user_roles,id',
        'github_link'     => 'sometimes|nullable|url|string',
        'linkedin_link'   => 'sometimes|nullable|url|string',
        'is_completed'    => 'sometimes|boolean',
        'is_active'       => 'sometimes|boolean',
    ]);

    // Prevent students from changing their role
    if ($request->user()->userRole->name !== 'admin') {
        unset($validated['user_role_id']);
    }

    if (!empty($validated['password'])) {
        $validated['password'] = bcrypt($validated['password']);
    } else {
        unset($validated['password']);
    }

    $user->update($validated);

    return response()->json([
        'message' => 'User updated successfully.',
        'data'    => $user->fresh(),
    ], 200);
}



    /**
     * Remove the specified user.
     * Only admins are allowed to delete user records.
     */
   public function destroy(User $user)
{
    $this->authorize('delete', $user);
    
    $user->delete();
    return response()->json(['message' => 'User deleted successfully.'], 200);
}

    /**
     * Self-enrollment endpoint for students.
     * A student can enroll in a batch and select multiple courses.
     */
    public function enroll(Request $request){

    $currentUser = Auth::user();
    
    if ($currentUser->userRole->name !== 'student') {
        return response()->json(['message' => 'Only students can enroll.'], 403);
    }
    
    $validatedData = $request->validate([
        'batch_id'    => 'required|exists:batches,id',
        'course_ids'  => 'required|array',
        'course_ids.*'=> 'exists:courses,id'
    ]);
    
    $batchId   = $validatedData['batch_id'];
    $courseIds = $validatedData['course_ids'];
    
    // Loop through each course and add it to the pivot table without detaching existing courses.
    foreach ($courseIds as $courseId) {
        $currentUser->batches()->syncWithoutDetaching([
            $batchId => ['course_id' => $courseId]
        ]);
    }
    
    return response()->json([
        'message' => 'Courses added successfully to the enrollment.'
    ], 200);
}


    /**
     * Enrollment endpoint for admins to enroll a student.
     */
    public function enrollStudent(Request $request)
    {
        $currentUser = Auth::user();
        
        if ($currentUser->userRole->name !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
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
        $student->batches()->syncWithoutDetaching([
            $batchId => ['course_id' => $courseId]
        ]);
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
        $this->authorize('detachCourse', [$batch, $course]);
    } else {
        // For admins, manually check that the target student actually has this course enrollment.
        $exists = DB::table('batch_course_user')
                    ->where('user_id', $targetStudentId)
                    ->where('batch_id', $batch->id)
                    ->where('course_id', $course->id)
                    ->exists();
        if (!$exists) {
            return response()->json(['message' => 'No matching course enrollment found.'], 404);
        }
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
        $this->authorize('detachStudentFromBatch', [$batch]);
    } else {
        // Admins have access via 'before', but we still check if the enrollment exists.
        $exists = DB::table('batch_course_user')
                    ->where('user_id', $targetStudentId)
                    ->where('batch_id', $batch->id)
                    ->exists();

        if (!$exists) {
            return response()->json(['message' => 'No matching batch enrollment found.'], 404);
        }
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
