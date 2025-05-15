<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\BatchController;
use App\Http\Controllers\API\EnrollmentController;
use App\Http\Controllers\API\ProjectTopicController;

use App\Http\Controllers\API\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



//Routes for guest users
Route::middleware('guest')->group(function(){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

    Route::get('/project-topics', [ProjectTopicController::class, 'index']);
    Route::get('/project-topics/{id}', [ProjectTopicController::class, 'show']);
    Route::post('/project-topics', [ProjectTopicController::class, 'store']);
    Route::put('/project-topics/{id}', [ProjectTopicController::class, 'update']);
    Route::delete('/project-topics/{id}', [ProjectTopicController::class, 'destroy']);
    
    // Authenticated user actions
    Route::post('/logout', [AuthController::class, 'logout']);

    // Users
    Route::get('/users', [UserController::class, 'index']);// Admin only
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('can:view,user'); // Admin or self
    Route::post('/users', [UserController::class, 'store']); // Admin only
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('can:update,user'); // Admin or self
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('can:delete,user'); // Admin only


    // Admin enrolls student
    Route::post('/enroll-student', [EnrollmentController::class, 'enrollStudent']); // Admin only

    // Detach course from user
    Route::delete('/detach-course/{batch}/{course}', [EnrollmentController::class, 'detachCourse']);// Admin or self

    // Detach student from batch
    Route::delete('/detach-student-from-batch/{batch}', [EnrollmentController::class, 'detachStudentFromBatch']); // Admin or self

    // View enrolled courses
    Route::get('/users/{user}/courses', [EnrollmentController::class, 'viewEnrolledCourses']);

    // Project Enrollment (Students & Admins)
    Route::post('/projects/{project}/enroll', [EnrollmentController::class, 'enrollInProject']); 
    Route::delete('/projects/{project}/detach', [EnrollmentController::class, 'detachRoleFromProject']);


    // Roles (Admin-Only)
    Route::get('/roles', [RoleController::class, 'index']); 
    Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('can:view,role');
    Route::post('/roles', [RoleController::class, 'store']); 
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('can:update,role');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('can:delete,role');

     // Batches (Admin-Only)
     Route::get('/batches', [BatchController::class, 'index']); 
     Route::get('/batches/{batch}', [BatchController::class, 'show'])->middleware('can:view,batch');
     Route::post('/batches', [BatchController::class, 'store']); 
     Route::put('/batches/{batch}', [BatchController::class, 'update'])->middleware('can:update,batch');
     Route::delete('/batches/{batch}', [BatchController::class, 'destroy'])->middleware('can:delete,batch');
 
     // Courses (Admin-Only)
     Route::get('/courses', [CourseController::class, 'index']); 
     Route::get('/courses/{course}', [CourseController::class, 'show'])->middleware('can:view,course');
     Route::post('/courses', [CourseController::class, 'store']); 
     Route::put('/courses/{course}', [CourseController::class, 'update'])->middleware('can:update,course');
     Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->middleware('can:delete,course');
});
