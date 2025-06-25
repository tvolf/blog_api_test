<?php

namespace Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostCreateTest extends TestCase
{
    use DatabaseMigrations;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function non_authenticated_user_is_not_allowed_to_create_posts(): void
    {
        $route = route('posts.store');

        $oldPostsCount = Post::count();

        $this->postJson($route, Post::factory()->make()->toArray())
             ->assertStatus(401);

        $this->assertEquals($oldPostsCount, Post::count());
    }

    #[Test]
    public function create_post_successfully(): void
    {
        $route = route('posts.store');

        $oldPostsCount = Post::count();

        $this->actingAs($this->user)
            ->postJson($route, Post::factory()->make()->toArray())
            ->assertJsonStructure(['data' => ['id', 'title', 'body']])
            ->assertStatus(201);

        $this->assertEquals($oldPostsCount + 1, Post::count());
    }

    #[Test]
    public function create_post_with_invalid_data_failed(): void
    {
        $route = route('posts.store');

        $oldPostsCount = Post::count();

        $data = Post::factory()->make()->toArray();
        unset($data['body']);

        $this->actingAs($this->user)
            ->postJson($route, $data)
            ->assertJsonValidationErrors(['body'])
            ->assertStatus(422);

        $this->assertEquals($oldPostsCount, Post::count());
    }
}
