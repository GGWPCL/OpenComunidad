<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Post;
use App\Models\Community;
use App\Models\Category;
use App\Models\UpVote;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpVoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_upvote_behavior(): void
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

        // Test upvoting a post
        $upvote = $user->upvotePost($post, true);

        $this->assertInstanceOf(UpVote::class, $upvote);
        $this->assertTrue($user->upvotedPosts->contains($post));
        $this->assertEquals(1, $user->upvotedPosts->count());
        $this->assertEquals($user->id, $upvote->user_id);
        $this->assertEquals($post->id, $upvote->post_id);

        // Test upvoting the same post again (should return existing upvote)
        $duplicateUpvote = $user->upvotePost($post, true);
        $this->assertEquals($upvote->id, $duplicateUpvote->id);
        $this->assertEquals(1, $user->upvotedPosts()->count());

        // Test removing an upvote from a post
        $result = $user->upvotePost($post, false);
        $this->assertNull($result);
        $this->assertFalse($user->upvotedPosts->contains($post));
        $this->assertEquals(0, $user->upvotedPosts()->count());

        // Test removing an upvote from an already non-upvoted post
        $result = $user->upvotePost($post, false);
        $this->assertNull($result);
        $this->assertEquals(0, $user->upvotedPosts()->count());
    }
}
