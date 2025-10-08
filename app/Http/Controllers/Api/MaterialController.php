<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MaterialService;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    protected $materialService;

    public function __construct(MaterialService $materialService)
    {
        $this->materialService = $materialService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        try {
            $materials = $this->materialService->getMaterialsByCourse($request->course_id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $materials,
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
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip|max:10240', 
        ]);

        try {
            $material = $this->materialService->uploadMaterial(
                $validated,
                $request->file('file'),
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $material,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    public function download(Request $request, $id)
    {
        try {
            $downloadData = $this->materialService->getDownloadPath($id, $request->user()->id);

            return response()->download(
                $downloadData['path'],
                $downloadData['name']
            );
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
            $this->materialService->deleteMaterial($id, $request->user()->id);

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
}