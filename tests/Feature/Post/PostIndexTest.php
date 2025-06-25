<?php

namespace Feature\Post;

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostIndexTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function show_all_posts_successfully(): void
    {
        $route = route('posts.index');
        Post::factory()->count(5)->create();

        $this->getJson($route)
            ->assertOk()
            ->assertJsonCount(5, 'data');
    }
}
