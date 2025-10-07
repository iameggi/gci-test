<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function getAllCourses()
    {
        return Course::with(['lecturer:id,name,email', 'students:id,name,email'])
            ->get();
    }

    public function getCourseById($id)
    {
        return Course::with(['lecturer:id,name,email', 'students:id,name,email'])
            ->findOrFail($id);
    }

    public function createCourse(array $data, $lecturerId)
    {
        return Course::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'lecturer_id' => $lecturerId,
        ]);
    }

    public function updateCourse($id, array $data, $lecturerId)
    {
        $course = Course::findOrFail($id);

        if ($course->lecturer_id !== $lecturerId) {
            throw new \Exception('Hak akses ditolak!');
        }

        $course->update([
            'name' => $data['name'] ?? $course->name,
            'description' => $data['description'] ?? $course->description,
        ]);

        return $course;
    }

    public function deleteCourse($id, $lecturerId)
    {
        $course = Course::findOrFail($id);

        if ($course->lecturer_id !== $lecturerId) {
            throw new \Exception('Hak akses ditolak!');
        }

        $course->delete();

        return true;
    }

    public function enrollStudent($courseId, $studentId)
    {
        $course = Course::findOrFail($courseId);

        if ($course->students()->where('user_id', $studentId)->exists()) {
            throw new \Exception('Anda telah enroll di course ini');
        }

        $course->students()->attach($studentId);

        return $course->load('students:id,name,email');
    }


    public function getLecturerCourses($lecturerId)
    {
        return Course::where('lecturer_id', $lecturerId)
            ->withCount('students') // Hitung jumlah mahasiswa
            ->with(['students:id,name,email'])
            ->get();
    }

    public function getLecturerCourseDetail($courseId, $lecturerId)
    {
        $course = Course::where('id', $courseId)
            ->where('lecturer_id', $lecturerId)
            ->withCount('students')
            ->with(['students:id,name,email'])
            ->firstOrFail();

        return $course;
    }


    public function getStudentCourses($studentId)
    {
        return Course::whereHas('students', function ($query) use ($studentId) {
            $query->where('user_id', $studentId);
        })
        ->with(['lecturer:id,name,email'])
        ->withCount('students')
        ->get();
    }
}