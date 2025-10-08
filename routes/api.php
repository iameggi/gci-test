<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\DiscussionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/courses', [CourseController::class, 'index']);
    Route::middleware('role:dosen')->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{id}', [CourseController::class, 'update']);
        Route::delete('/courses/{id}', [CourseController::class, 'delete']);
        Route::get('/courses/me', [CourseController::class, 'myCourses']);
        Route::get('/courses/me/{id}', [CourseController::class, 'myCourseDetail']);

        Route::post('/materials', [MaterialController::class, 'store']);
        Route::delete('/materials/{id}', [MaterialController::class, 'delete']);

        Route::post('/assignments', [AssignmentController::class, 'store']);
        Route::put('/assignments/{id}', [AssignmentController::class, 'update']);
        Route::delete('/assignments/{id}', [AssignmentController::class, 'delete']);
        Route::get('/assignments/my-assignments', [AssignmentController::class, 'myAssignments']); 
        Route::get('/assignments/my-assignments/{id}', [AssignmentController::class, 'myAssignmentDetail']);
        Route::post('/submissions/{id}/grade', [SubmissionController::class, 'grade']);
    });

        Route::middleware('role:mahasiswa')->group(function () {
        Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
        Route::get('/courses/enrolled', [CourseController::class, 'enrolledCourses']);

        Route::post('/submissions', [SubmissionController::class, 'store']);
        Route::get('/submissions/me', [SubmissionController::class, 'mySubmissions']);

    });
        Route::get('/materials', [MaterialController::class, 'index']);
        Route::get('/materials/{id}/download', [MaterialController::class, 'download']);
        Route::get('/materials', [MaterialController::class, 'index']);
        Route::get('/materials/{id}/download', [MaterialController::class, 'download']);
        Route::get('/assignments', [AssignmentController::class, 'index']);
        Route::get('/assignments/{id}', [AssignmentController::class, 'show']);
        Route::get('/submissions', [SubmissionController::class, 'index']);

        Route::get('/discussions', [DiscussionController::class, 'index']);
        Route::get('/discussions/{id}', [DiscussionController::class, 'show']);
        Route::post('/discussions', [DiscussionController::class, 'store']);
        Route::put('/discussions/{id}', [DiscussionController::class, 'update']);
        Route::delete('/discussions/{id}', [DiscussionController::class, 'delete']);
        Route::post('/discussions/{id}/replies', [DiscussionController::class, 'reply']);
        Route::put('/replies/{id}', [DiscussionController::class, 'updateReply']);
        Route::delete('/replies/{id}', [DiscussionController::class, 'deleteReply']);


});