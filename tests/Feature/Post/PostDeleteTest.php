<?php

namespace Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostDeleteTest extends TestCase
{
    use DatabaseMigrations;

    private User $user;
    private Post $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->post = Post::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * @param $id
     * @return string
     */
    private function getRoute($id): string
    {
        return route('posts.destroy', $id);
    }

    #[Test]
    public function delete_post_successfully(): void
    {
        $route = $this->getRoute($this->post->id);

        $this->actingAs($this->user)
            ->deleteJson($route)
            ->assertExactJson(['message' => 'Operation complete successfully.'])
            ->assertStatus(200);
    }

    #[Test]
    public function trying_to_delete_post_with_improper_id_is_failed(): void
    {
        $route = $this->getRoute(9999);

        $this->actingAs($this->user)
            ->deleteJson($route)
            ->assertStatus(404);
    }

    #[Test]
    public function foreign_user_is_not_allowed_to_destroy_post(): void
    {
        $route = $this->getRoute($this->post->id);

        $this->post->user_id = User::factory()->create()->id;
        $this->post->save();

        $this->actingAs($this->user)
            ->deleteJson($route)
            ->assertStatus(403);
    }
}
