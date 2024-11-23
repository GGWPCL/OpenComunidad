<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Post;
use App\Models\Community;
use App\Models\Category;
use App\Models\Follow;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_follow_behavior(): void
    {
        // Create test user and required relationships
        $user = User::factory()->create();
        $community = Community::factory()->create([
            'name' => 'Test Community'
        ]);
        $category = Category::factory()->create([
            'display_name' => 'Test Category',
            'internal_name' => 'test_category'
        ]);

        // Create a test post
        $post = Post::factory()->create([
            'community_id' => $community->id,
            'category_id' => $category->id,
            'author_id' => $user->id,
            'original_title' => 'Test Title',
            'mutated_title' => 'Test Title',
            'original_content' => 'Test Content',
            'mutated_content' => 'Test Content'
        ]);

        // Test following a post
        $follow = $user->followPost($post, true);

        $this->assertInstanceOf(Follow::class, $follow);
        $this->assertTrue($user->followedPosts->contains($post));
        $this->assertEquals(1, $user->followedPosts->count());
        $this->assertEquals($user->id, $follow->user_id);
        $this->assertEquals($post->id, $follow->post_id);

        // Test following the same post again (should return existing follow)
        $duplicateFollow = $user->followPost($post, true);
        $this->assertEquals($follow->id, $duplicateFollow->id);
        $this->assertEquals(1, $user->followedPosts()->count());

        // Test unfollowing a post
        $result = $user->followPost($post, false);
        $this->assertNull($result);
        $this->assertFalse($user->followedPosts->contains($post));
        $this->assertEquals(0, $user->followedPosts()->count());

        // Test unfollowing an already unfollowed post
        $result = $user->followPost($post, false);
        $this->assertNull($result);
        $this->assertEquals(0, $user->followedPosts()->count());
    }
}
