<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class PostController extends Controller
{
    /**
     *  Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $posts = Post::with('user')->get();

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     * @param CreatePostRequest $request
     * @return JsonResource
     * @throws Throwable
     */
    public function store(CreatePostRequest $request): JsonResource
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $post = Post::create($validated);

        return $post->toResource();
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return  JsonResource
     * @throws Throwable
     */
    public function show(Post $post): JsonResource
    {
        return $post->toResource();
    }

    /**
     * Update the specified resource in storage.
     * @param UpdatePostRequest $request
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException|Throwable
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validated();
        $post->updateOrFail($validated);

        return response()->json(['message' => 'Operation complete successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->deleteOrFail();

        return response()->json(['message' => 'Operation complete successfully.']);
    }
}
