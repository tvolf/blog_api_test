<?php

namespace Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostUpdateTest extends TestCase
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
        return route('posts.update', $id);
    }

    #[Test]
    public function update_post_successfully(): void
    {
        $route = $this->getRoute($this->post->id);

        $data = [
            'body' => 'Some new body',
            'title' => 'Some new title',
        ];

        $this->actingAs($this->user)
            ->putJson($route, $data)
            ->assertExactJson(['message' => 'Operation complete successfully.'])
            ->assertStatus(200);
    }

    #[Test]
    public function update_post_with_invalid_data_failed(): void
    {
        $route = $this->getRoute($this->post->id);

        $data = [
            'body' => '',
            'title' => 'Some new title',
        ];

        $this->actingAs($this->user)
            ->putJson($route, $data)
            ->assertJsonValidationErrors(['body'])
            ->assertStatus(422);
    }

    #[Test]
    public function foreign_user_is_not_allowed_to_update_post(): void
    {
        $route = $this->getRoute($this->post->id);

        $this->post->user_id = User::factory()->create()->id;
        $this->post->save();

        $data = [
            'body' => 'Some new body',
            'title' => 'Some new title',
        ];

        $this->actingAs($this->user)
            ->putJson($route, $data)
            ->assertStatus(403);
    }
}
