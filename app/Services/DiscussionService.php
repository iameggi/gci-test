<?php

namespace App\Services;

use App\Models\Discussion;
use App\Models\Reply;
use App\Models\Course;

class DiscussionService
{
    public function getDiscussionsByCourse($courseId)
    {
        return Discussion::where('course_id', $courseId)
            ->with(['user:id,name,email,role', 'course:id,name'])
            ->withCount('replies')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createDiscussion(array $data, $userId)
    {
        $course = Course::findOrFail($data['course_id']);

        $isLecturer = $course->lecturer_id === $userId;
        $isEnrolled = $course->students()->where('user_id', $userId)->exists();

        if (!$isLecturer && !$isEnrolled) {
            throw new \Exception('Untuk membuat discussion baru anda harus bergabung dengan course ini');
        }

        $discussion = Discussion::create([
            'course_id' => $data['course_id'],
            'user_id' => $userId,
            'content' => $data['content'],
        ]);

        return $discussion->load(['user:id,name,email,role', 'course:id,name']);
    }

    public function getDiscussionById($id)
    {
        return Discussion::with([
            'user:id,name,email,role',
            'course:id,name',
            'replies.user:id,name,email,role'
        ])
        ->withCount('replies')
        ->findOrFail($id);
    }

    public function createReply($discussionId, $content, $userId)
    {
        $discussion = Discussion::with('course')->findOrFail($discussionId);

        $isLecturer = $discussion->course->lecturer_id === $userId;
        $isEnrolled = $discussion->course->students()->where('user_id', $userId)->exists();

        if (!$isLecturer && !$isEnrolled) {
            throw new \Exception('Untuk membalas anda harus bergabung dengan course ini');
        }

        $reply = Reply::create([
            'discussion_id' => $discussionId,
            'user_id' => $userId,
            'content' => $content,
        ]);

        return $reply->load('user:id,name,email,role');
    }

    public function deleteDiscussion($id, $userId)
    {
        $discussion = Discussion::findOrFail($id);

        if ($discussion->user_id !== $userId) {
            throw new \Exception('Hak akses ditolak');
        }

        $discussion->delete();

        return true;
    }

    public function deleteReply($id, $userId)
    {
        $reply = Reply::findOrFail($id);

        if ($reply->user_id !== $userId) {
            throw new \Exception('Hak akses ditolak');
        }

        $reply->delete();

        return true;
    }

    public function updateDiscussion($id, $content, $userId)
    {
        $discussion = Discussion::findOrFail($id);

        if ($discussion->user_id !== $userId) {
            throw new \Exception('Hak akses ditolak');
        }

        $discussion->update(['content' => $content]);

        return $discussion->load(['user:id,name,email,role', 'course:id,name']);
    }

    public function updateReply($id, $content, $userId)
    {
        $reply = Reply::findOrFail($id);

        if ($reply->user_id !== $userId) {
            throw new \Exception('Hak akses ditolak');
        }

        $reply->update(['content' => $content]);

        return $reply->load('user:id,name,email,role');
    }
}