<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\Assignment;
use Illuminate\Support\Facades\Storage;

class SubmissionService
{
    public function getSubmissionsByAssignment($assignmentId)
    {
        return Submission::where('assignment_id', $assignmentId)
            ->with(['student:id,name,email', 'assignment:id,title,deadline'])
            ->get();
    }

    public function submitAssignment(array $data, $file, $studentId)
    {
        $assignment = Assignment::with('course.students')->findOrFail($data['assignment_id']);

        $isEnrolled = $assignment->course->students()->where('user_id', $studentId)->exists();
        
        if (!$isEnrolled) {
            throw new \Exception('Anda belum enroll di course ini');
        }

        $existingSubmission = Submission::where('assignment_id', $data['assignment_id'])
            ->where('student_id', $studentId)
            ->first();

        if ($existingSubmission) {
            throw new \Exception('Anda telah submit ke course ini');
        }

        $directory = 'submissions';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $fileName = $this->generateFileName($file, $studentId);
        $filePath = $file->storeAs($directory, $fileName, 'public');

        $submission = Submission::create([
            'assignment_id' => $data['assignment_id'],
            'student_id' => $studentId,
            'file_path' => $filePath,
        ]);

        return $submission->load(['student:id,name,email', 'assignment:id,title,deadline']);
    }

    public function gradeSubmission($id, $score, $lecturerId)
    {
        $submission = Submission::with('assignment.course')->findOrFail($id);

        if ($submission->assignment->course->lecturer_id !== $lecturerId) {
            throw new \Exception('Hak akses ditolak');
        }

        $submission->update(['score' => $score]);

        return $submission->load(['student:id,name,email', 'assignment:id,title']);
    }

    public function getStudentSubmissions($studentId, $courseId = null)
    {
        $query = Submission::where('student_id', $studentId)
            ->with(['assignment.course:id,name']);

        if ($courseId) {
            $query->whereHas('assignment', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        return $query->get();
    }

    private function generateFileName($file, $studentId)
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        $fileName = preg_replace('/[^A-Za-z0-9_-]/', '_', $originalName);
        
        return time() . '_student' . $studentId . '_' . $fileName . '.' . $extension;
    }
}