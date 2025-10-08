<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportService
{
   
    public function getCoursesReport()
    {
        $courses = Course::withCount('students')
            ->with('lecturer:id,name,email')
            ->orderBy('students_count', 'desc')
            ->get()
            ->map(function ($course) {
                return [
                    'course_id' => $course->id,
                    'course_name' => $course->name,
                    'lecturer_name' => $course->lecturer->name,
                    'lecturer_email' => $course->lecturer->email,
                    'total_students' => $course->students_count,
                ];
            });

        return [
            'total_courses' => $courses->count(),
            'total_enrollments' => $courses->sum('total_students'),
            'courses' => $courses,
        ];
    }

  
    public function getAssignmentsReport()
    {
        $assignments = Assignment::withCount([
                'submissions as total_submissions',
                'submissions as graded_submissions' => function ($query) {
                    $query->whereNotNull('score');
                },
                'submissions as ungraded_submissions' => function ($query) {
                    $query->whereNull('score');
                },
            ])
            ->with('course:id,name,lecturer_id', 'course.lecturer:id,name,email')
            ->get()
            ->map(function ($assignment) {
                $gradedPercentage = $assignment->total_submissions > 0
                    ? round(($assignment->graded_submissions / $assignment->total_submissions) * 100, 2)
                    : 0;

                return [
                    'assignment_id' => $assignment->id,
                    'assignment_title' => $assignment->title,
                    'course_name' => $assignment->course->name,
                    'lecturer_name' => $assignment->course->lecturer->name,
                    'deadline' => $assignment->deadline->format('Y-m-d H:i:s'),
                    'total_submissions' => $assignment->total_submissions,
                    'graded_submissions' => $assignment->graded_submissions,
                    'ungraded_submissions' => $assignment->ungraded_submissions,
                    'graded_percentage' => $gradedPercentage,
                ];
            });

        return [
            'total_assignments' => $assignments->count(),
            'total_submissions' => $assignments->sum('total_submissions'),
            'total_graded' => $assignments->sum('graded_submissions'),
            'total_ungraded' => $assignments->sum('ungraded_submissions'),
            'assignments' => $assignments,
        ];
    }

    public function getStudentReport($studentId)
    {
        $student = User::findOrFail($studentId);

        if (!$student->isMahasiswa()) {
            throw new \Exception('User bukan mahasiswa');
        }

        $enrolledCourses = $student->courses()
            ->withCount('assignments')
            ->get();

        $submissions = Submission::where('student_id', $studentId)
            ->with(['assignment.course:id,name', 'assignment:id,course_id,title,deadline'])
            ->get();

        $totalSubmissions = $submissions->count();
        $gradedSubmissions = $submissions->whereNotNull('score')->count();
        $ungradedSubmissions = $submissions->whereNull('score')->count();
        
        $scores = $submissions->whereNotNull('score')->pluck('score');
        $averageScore = $scores->isNotEmpty() ? round($scores->avg(), 2) : 0;
        $highestScore = $scores->isNotEmpty() ? $scores->max() : 0;
        $lowestScore = $scores->isNotEmpty() ? $scores->min() : 0;

        $totalAssignments = Assignment::whereIn('course_id', $enrolledCourses->pluck('id'))->count();
        $notSubmitted = $totalAssignments - $totalSubmissions;

        $submissionsByCourse = $submissions->groupBy('assignment.course.name')->map(function ($courseSubmissions, $courseName) {
            $courseScores = $courseSubmissions->whereNotNull('score')->pluck('score');
            
            return [
                'course_name' => $courseName,
                'total_submissions' => $courseSubmissions->count(),
                'graded' => $courseSubmissions->whereNotNull('score')->count(),
                'ungraded' => $courseSubmissions->whereNull('score')->count(),
                'average_score' => $courseScores->isNotEmpty() ? round($courseScores->avg(), 2) : 0,
                'submissions' => $courseSubmissions->map(function ($submission) {
                    return [
                        'assignment_title' => $submission->assignment->title,
                        'deadline' => $submission->assignment->deadline->format('Y-m-d H:i:s'),
                        'submitted_at' => $submission->created_at->format('Y-m-d H:i:s'),
                        'score' => $submission->score,
                        'status' => $submission->score !== null ? 'Sudah Dinilai' : 'Belum Dinilai',
                    ];
                })->values(),
            ];
        })->values();

        return [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ],
            'summary' => [
                'total_courses_enrolled' => $enrolledCourses->count(),
                'total_assignments_available' => $totalAssignments,
                'total_submissions' => $totalSubmissions,
                'graded_submissions' => $gradedSubmissions,
                'ungraded_submissions' => $ungradedSubmissions,
                'not_submitted' => $notSubmitted,
                'average_score' => $averageScore,
                'highest_score' => $highestScore,
                'lowest_score' => $lowestScore,
            ],
            'courses' => $submissionsByCourse,
        ];
    }
}