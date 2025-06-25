<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Post $post
     * @return AnonymousResourceCollection
     */
    public function index(Post $post): AnonymousResourceCollection
    {
        return CommentResource::collection($post->comments()->with(['user'])->get());
    }

    /**
     * Store a newly created resource in storage.
     * @param CreateCommentRequest $request
     * @param Post $post
     * @return CommentResource
     */
    public function store(CreateCommentRequest $request, Post $post): CommentResource
    {
        $comment = $post->comments()->create([
            'body' => $request->input('body'),
            'user_id' => auth()->id()
        ]);
        $comment->load('user');

        return new CommentResource($comment);
    }

    /**
     * Display the specified resource.
     * @param Post $post
     * @param Comment $comment
     * @return JsonResource
     * @throws Throwable
     */
    public function show(Post $post, Comment $comment):  JsonResource
    {
        return $comment->toResource();
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateCommentRequest $request
     * @param Post $post
     * @param Comment $comment
     * @return JsonResource
     * @throws AuthorizationException
     */
    public function update(UpdateCommentRequest $request, Post $post, Comment $comment): JsonResource
    {
        $this->authorize('update', $comment);

        $validated = $request->validated();
        $comment->update($validated);
        return new CommentResource($comment);
    }

    /**
     * Remove the specified resource from storage.
     * @param Post $post
     * @param Comment $comment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Post $post, Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();
        return  response()->json(['message' => __('statuses.ok')]);
    }
}
