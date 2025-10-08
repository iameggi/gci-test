<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AssignmentService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    protected $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        try {
            $assignments = $this->assignmentService->getAssignmentsByCourse($request->course_id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $assignments,
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
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:now',
        ]);

        try {
            $assignment = $this->assignmentService->createAssignment(
                $validated,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $assignment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    public function show($id)
    {
        try {
            $assignment = $this->assignmentService->getAssignmentById($id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $assignment,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Not Found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'deadline' => 'sometimes|required|date',
        ]);

        try {
            $assignment = $this->assignmentService->updateAssignment(
                $id,
                $validated,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $assignment,
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
            $this->assignmentService->deleteAssignment($id, $request->user()->id);

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


    public function myAssignments(Request $request)
    {
        try {
            $assignments = $this->assignmentService->getLecturerAssignments($request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $assignments,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function myAssignmentDetail(Request $request, $id)
    {
        try {
            $assignment = $this->assignmentService->getLecturerAssignmentDetail($id, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $assignment,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 404);
        }
    }
}