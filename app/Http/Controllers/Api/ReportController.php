<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function coursesReport()
    {
        try {
            $data = $this->reportService->getCoursesReport();

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function assignmentsReport()
    {
        try {
            $data = $this->reportService->getAssignmentsReport();

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function studentReport($id)
    {
        try {
            $data = $this->reportService->getStudentReport($id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getMessage() === 'Err user is not student' ? 403 : 404);
        }
    }
}