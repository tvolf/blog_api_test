<?php

namespace Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommentDeleteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Post $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->post = Post::factory()->create(['user_id' => $this->user->id]);
        $this->comment = Comment::factory()->create(['user_id' => $this->user->id, 'post_id' => $this->post->id]);
    }

    /**
     * @param $post
     * @param $comment
     * @return string
     */
    private function getRoute($post, $comment): string
    {
        return route('post.comments.destroy', ['post' => $post, 'comment' => $comment]);
    }

    #[Test]
    public function delete_comment_successfully(): void
    {
        $route = $this->getRoute($this->post, $this->comment);

        $this->actingAs($this->user)
            ->deleteJson($route)
            ->assertExactJson(['message' => __('statuses.ok')])
            ->assertStatus(200);
    }

    #[Test]
    public function trying_to_delete_comment_with_improper_id_is_failed(): void
    {
        $route = $this->getRoute($this->post->id, 9999);

        $this->actingAs($this->user)
            ->deleteJson($route)
            ->assertStatus(404);
    }

    #[Test]
    public function foreign_user_is_not_allowed_to_destroy_comment(): void
    {
        $route = $this->getRoute($this->post->id, $this->comment->id);

        $this->comment->update(['user_id' => User::factory()->create()->id]);

        $this->actingAs($this->user)
            ->deleteJson($route)
            ->assertStatus(403);
    }
}
