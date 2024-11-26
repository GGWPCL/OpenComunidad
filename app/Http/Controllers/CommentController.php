<?php

namespace App\Http\Controllers;

use App\Events\PostUpdated;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\ContentModerationService;

class CommentController extends Controller
{

    public function __construct(
        private ContentModerationService $contentModerationService,
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, Post $post)
    {
        $validated = $request->validated();
        $validated['mutated_content'] = $this->contentModerationService->moderateContent(
            $validated['original_content'],
            ContentModerationService::INPUT_TYPE_CONTENT,
            $post->category->internal_name,
        );
        $validated['author_id'] = $request->user()->id;
        $validated['post_id'] = $post->id;

        Comment::create($validated);
        PostUpdated::dispatch(PostUpdated::TYPE_POST_COMMENTED, $post);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
