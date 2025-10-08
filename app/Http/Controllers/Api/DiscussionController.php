<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DiscussionService;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    protected $discussionService;

    public function __construct(DiscussionService $discussionService)
    {
        $this->discussionService = $discussionService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        try {
            $discussions = $this->discussionService->getDiscussionsByCourse($request->course_id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $discussions,
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
            'content' => 'required|string',
        ]);

        try {
            $discussion = $this->discussionService->createDiscussion(
                $validated,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $discussion,
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
            $discussion = $this->discussionService->getDiscussionById($id);

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $discussion,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Err not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        try {
            $discussion = $this->discussionService->updateDiscussion(
                $id,
                $validated['content'],
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $discussion,
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
            $this->discussionService->deleteDiscussion($id, $request->user()->id);

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


    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        try {
            $reply = $this->discussionService->createReply(
                $id,
                $validated['content'],
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $reply,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    public function updateReply(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        try {
            $reply = $this->discussionService->updateReply(
                $id,
                $validated['content'],
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $reply,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    public function deleteReply(Request $request, $id)
    {
        try {
            $this->discussionService->deleteReply($id, $request->user()->id);

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