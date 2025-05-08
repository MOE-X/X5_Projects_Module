<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\ProjectTopicController;
use App\Http\Controllers\API\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/project-topics', [ProjectTopicController::class, 'index']);
Route::get('/project-topics/{id}', [ProjectTopicController::class, 'show']);
Route::post('/project-topics', [ProjectTopicController::class, 'store']);
Route::put('/project-topics/{id}', [ProjectTopicController::class, 'update']);
Route::delete('/project-topics/{id}', [ProjectTopicController::class, 'destroy']);



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
    
    // Authenticated user actions
    Route::post('/logout', [AuthController::class, 'logout']);

    // General user management
    Route::get('/users', [UserController::class, 'index']);// Admin only
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('can:view,user'); // Admin or self
    Route::post('/users', [UserController::class, 'store']); // Admin only
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('can:update,user'); // Admin or self
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('can:delete,user'); // Admin only
    

    // Student self-enroll
    Route::post('/enroll', [UserController::class, 'enroll']); // Student only

    // Admin enrolls student
    Route::post('/enroll-student', [UserController::class, 'enrollStudent']); // Admin only

    // Detach course from user
    Route::delete('/detach-course/{batch}/{course}', [UserController::class, 'detachCourse']);// Admin or self

    // Detach student from batch
    Route::delete('/detach-student-from-batch/{batch}', [UserController::class, 'detachStudentFromBatch']); // Admin or self

    // View enrolled courses
    Route::get('/users/{user}/courses', [UserController::class, 'viewEnrolledCourses']);

    // Roles (Admin-Only)
    Route::get('/roles', [RoleController::class, 'index'])->middleware('can:viewAny,role'); 
    Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('can:view,role');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('can:create,role'); 
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('can:update,role');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('can:delete,role');

     // Batches (Admin-Only)
     Route::get('/batches', [BatchController::class, 'index'])->middleware('can:viewAny,batch'); 
     Route::get('/batches/{batch}', [BatchController::class, 'show'])->middleware('can:view,batch');
     Route::post('/batches', [BatchController::class, 'store'])->middleware('can:create,batch'); 
     Route::put('/batches/{batch}', [BatchController::class, 'update'])->middleware('can:update,batch');
     Route::delete('/batches/{batch}', [BatchController::class, 'destroy'])->middleware('can:delete,batch');
 
     // Courses (Admin-Only)
     Route::get('/courses', [Coursecontroller::class, 'index'])->middleware('can:viewAny,course'); 
     Route::get('/courses/{course}', [CourseController::class, 'show'])->middleware('can:view,course');
     Route::post('/courses', [CourseController::class, 'store'])->middleware('can:create,course'); 
     Route::put('/courses/{course}', [CourseController::class, 'update'])->middleware('can:update,course');
     Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->middleware('can:delete,course');
});
