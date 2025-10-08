<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubmissionService;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    protected $submissionService;

    public function __construct(SubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
        ]);

        try {
            $submissions = $this->submissionService->getSubmissionsByAssignment($request->assignment_id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $submissions,
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
            'assignment_id' => 'required|exists:assignments,id',
            'file' => 'required|file|mimes:pdf,doc,docx,zip,rar|max:10240',
        ]);

        try {
            $submission = $this->submissionService->submitAssignment(
                $validated,
                $request->file('file'),
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $submission,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function grade(Request $request, $id)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
        ]);

        try {
            $submission = $this->submissionService->gradeSubmission(
                $id,
                $validated['score'],
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $submission,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    public function mySubmissions(Request $request)
    {
        try {
            $submissions = $this->submissionService->getStudentSubmissions($request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $submissions,
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