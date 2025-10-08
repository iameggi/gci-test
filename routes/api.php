<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\MaterialController;


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
    });

        Route::middleware('role:mahasiswa')->group(function () {
        Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
        Route::get('/courses/enrolled', [CourseController::class, 'enrolledCourses']);

    });
        Route::get('/materials', [MaterialController::class, 'index']);
        Route::get('/materials/{id}/download', [MaterialController::class, 'download']);


});