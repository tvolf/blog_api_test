<?php

namespace Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommentCreateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Post $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->post = Post::factory()->create();
    }

    /**
     * @param $postId
     * @return string
     */
    private function getRoute($postId): string
    {
        return route('post.comments.store', ['post' => $postId]);
    }

    #[Test]
    public function non_authenticated_user_is_not_allowed_to_create_comments(): void
    {
        $route = $this->getRoute($this->post->id);

        $oldCommentsCount = $this->post->comments()->count();

        $this->postJson($route, Comment::factory()->make()->toArray())
             ->assertStatus(401);

        $this->post->refresh();
        $this->assertEquals($oldCommentsCount, $this->post->comments()->count());
    }

    #[Test]
    public function create_comment_successfully(): void
    {
        $route = $this->getRoute($this->post->id);

        $oldCommentsCount = $this->post->comments()->count();

        $this->actingAs($this->user)
            ->postJson($route, Comment::factory()->make()->toArray())
            ->assertJsonStructure(['data' => ['id', 'body', 'user']])
            ->assertStatus(201);

        $this->post->load('comments');
        $this->assertEquals($oldCommentsCount + 1, $this->post->comments()->count());
    }

    #[Test]
    public function create_comment_with_invalid_data_failed(): void
    {
        $route = $this->getRoute($this->post->id);

        $oldCommentsCount = $this->post->comments()->count();

        $data = Comment::factory()->make()->toArray();
        unset($data['body']);

        $this->actingAs($this->user)
            ->postJson($route, $data)
            ->assertJsonValidationErrors(['body'])
            ->assertStatus(422);

        $this->assertEquals($oldCommentsCount, $this->post->comments()->count());
    }
}
