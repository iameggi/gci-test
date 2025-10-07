<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }


    public function index()
    {
        try {
            $courses = $this->courseService->getAllCourses();

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $courses,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $course = $this->courseService->createCourse(
                $validated,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $course->load('lecturer:id,name,email'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $course = $this->courseService->updateCourse(
                $id,
                $validated,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $course->load('lecturer:id,name,email'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $this->courseService->deleteCourse($id, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    public function enroll(Request $request, $id)
    {
        try {
            $course = $this->courseService->enrollStudent($id, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $course,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }


    public function myCourses(Request $request)
    {
        try {
            $courses = $this->courseService->getLecturerCourses($request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $courses,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function myCourseDetail(Request $request, $id)
    {
        try {
            $course = $this->courseService->getLecturerCourseDetail($id, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $course,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function enrolledCourses(Request $request)
    {
        try {
            $courses = $this->courseService->getStudentCourses($request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $courses,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}