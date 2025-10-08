<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Course;

class AssignmentService
{
    public function getAssignmentsByCourse($courseId)
    {
        return Assignment::where('course_id', $courseId)
            ->with('course:id,name,lecturer_id')
            ->withCount('submissions')
            ->orderBy('deadline', 'asc')
            ->get();
    }

    public function createAssignment(array $data, $lecturerId)
    {
        $course = Course::findOrFail($data['course_id']);
        
        if ($course->lecturer_id !== $lecturerId) {
            throw new \Exception('Hak akses ditolak!');
        }

        $assignment = Assignment::create([
            'course_id' => $data['course_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline'],
        ]);

        return $assignment->load('course:id,name');
    }

    public function getAssignmentById($id)
    {
        return Assignment::with(['course:id,name,lecturer_id', 'submissions.student:id,name,email'])
            ->withCount('submissions')
            ->findOrFail($id);
    }

    public function getLecturerAssignments($lecturerId)
    {
        return Assignment::whereHas('course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })
        ->with(['course:id,name', 'submissions.student:id,name,email'])
        ->withCount([
            'submissions',
            'submissions as graded_submissions_count' => function ($query) {
                $query->whereNotNull('score');
            },
            'submissions as ungraded_submissions_count' => function ($query) {
                $query->whereNull('score');
            }
        ])
        ->orderBy('deadline', 'desc')
        ->get();
    }

    public function getLecturerAssignmentDetail($id, $lecturerId)
    {
        $assignment = Assignment::whereHas('course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })
        ->with(['course:id,name', 'submissions.student:id,name,email'])
        ->withCount([
            'submissions',
            'submissions as graded_submissions_count' => function ($query) {
                $query->whereNotNull('score');
            },
            'submissions as ungraded_submissions_count' => function ($query) {
                $query->whereNull('score');
            }
        ])
        ->findOrFail($id);

        return $assignment;
    }

    public function updateAssignment($id, array $data, $lecturerId)
    {
        $assignment = Assignment::with('course')->findOrFail($id);

        if ($assignment->course->lecturer_id !== $lecturerId) {
            throw new \Exception('Hak akses ditolak');
        }

        $assignment->update($data);

        return $assignment->load('course:id,name');
    }

    public function deleteAssignment($id, $lecturerId)
    {
        $assignment = Assignment::with('course')->findOrFail($id);

        if ($assignment->course->lecturer_id !== $lecturerId) {
            throw new \Exception('Hak akses ditolak!');
        }

        $assignment->delete();

        return true;
    }
}