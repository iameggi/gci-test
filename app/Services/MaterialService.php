<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

class MaterialService
{
    public function getMaterialsByCourse($courseId)
    {
        return Material::where('course_id', $courseId)
            ->with('course:id,name,lecturer_id')
            ->get();
    }

    public function uploadMaterial(array $data, $file, $lecturerId)
    {
        $course = Course::findOrFail($data['course_id']);
        
        if ($course->lecturer_id !== $lecturerId) {
            throw new \Exception('Hak akses ditolak!');
        }

        $directory = 'materials';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $fileName = $this->generateFileName($file);
        $filePath = $file->storeAs($directory, $fileName, 'public');

        $material = Material::create([
            'course_id' => $data['course_id'],
            'title' => $data['title'],
            'file_path' => $filePath,
        ]);

        return $material->load('course:id,name');
    }

    public function getMaterialById($id)
    {
        return Material::with('course:id,name,lecturer_id')->findOrFail($id);
    }

    public function deleteMaterial($id, $lecturerId)
    {
        $material = Material::with('course')->findOrFail($id);

        if ($material->course->lecturer_id !== $lecturerId) {
            throw new \Exception('Hak akses ditolak!');
        }

        if (Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return true;
    }

    public function getDownloadPath($id, $userId)
    {
        $material = Material::with('course')->findOrFail($id);

        $course = $material->course;
        $isLecturer = $course->lecturer_id === $userId;
        $isEnrolled = $course->students()->where('user_id', $userId)->exists();

        if (!$isLecturer && !$isEnrolled) {
            throw new \Exception('Hak akses ditolak!');
        }

        if (!Storage::disk('public')->exists($material->file_path)) {
            throw new \Exception('File not found');
        }

        return [
            'path' => storage_path('app/public/' . $material->file_path),
            'name' => basename($material->file_path),
            'material' => $material,
        ];
    }

    private function generateFileName($file)
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $cleanName = preg_replace('/[^A-Za-z0-9_-]/', '_', $originalName);

        return time() . '_' . $cleanName . '.' . $extension;
    }
}