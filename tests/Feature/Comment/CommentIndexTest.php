<?php

namespace Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommentIndexTest extends TestCase
{
    use DatabaseMigrations;


    private Post $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->post = Post::factory()->create();
        $this->comment = Comment::factory(4)->create(['post_id' => $this->post->id]);
    }


    /**
     * @param $postId
     * @return string
     */
    private function getRoute($postId): string
    {
        return route('post.comments.index', ['post' => $postId]);
    }

    #[Test]
    public function show_all_comments_for_post_successfully(): void
    {
        $route = $this->getRoute($this->post);

        $this->getJson($route)
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }
}
