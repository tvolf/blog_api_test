<?php

namespace Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommentUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Post $post;
    private Comment $comment;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->post = Post::factory()->create();
        $this->comment = Comment::factory()->create(['user_id' => $this->user->id, 'post_id' => $this->post->id]);
    }

    /**
     * @param $post
     * @param $comment
     * @return string
     */
    private function getRoute($post, $comment): string
    {
        return route('post.comments.update', ['post' => $post, 'comment' => $comment]);
    }

    #[Test]
    public function non_authenticated_user_is_not_allowed_to_update_comments(): void
    {
        $route = $this->getRoute($this->post, $this->comment);

        $this->putJson($route, Comment::factory()->make()->toArray())
             ->assertStatus(401);

        $this->post->refresh();
    }

    #[Test]
    public function update_comment_successfully(): void
    {
        $route = $this->getRoute($this->post, $this->comment);

        $this->actingAs($this->user)
            ->putJson($route, Comment::factory()->make()->toArray())
            ->assertJsonStructure(['data' => ['id', 'body', 'user_id', 'post_id']])
            ->assertStatus(200);
    }

    #[Test]
    public function update_comment_with_invalid_data_failed(): void
    {
        $route = $this->getRoute($this->post, $this->comment);

        $data = Comment::factory()->make()->toArray();
        unset($data['body']);

        $this->actingAs($this->user)
            ->putJson($route, $data)
            ->assertJsonValidationErrors(['body'])
            ->assertStatus(422);
    }
}
