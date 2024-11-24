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

    public function test_up_vote_behavior(): void
    {
        // Create test user and required relationships
        $user = User::factory()->create();
        $community = Community::factory()->create([
            'name' => 'Test Community',
            'slug' => 'test-community-upvote',
        ]);
        $category = Category::factory()->create([
            'display_name' => 'Test Category',
            'internal_name' => 'test_category_upvote'
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
        $upVote = $user->upVotePost($post, true);

        $this->assertInstanceOf(UpVote::class, $upVote);
        $this->assertTrue($user->upVotedPosts->contains($post));
        $this->assertEquals(1, $user->upVotedPosts->count());
        $this->assertEquals($user->id, $upVote->user_id);
        $this->assertEquals($post->id, $upVote->post_id);

        // Test upvoting the same post again (should return existing upVote)
        $duplicateUpvote = $user->upVotePost($post, true);
        $this->assertEquals($upVote->id, $duplicateUpvote->id);
        $this->assertEquals(1, $user->upVotedPosts()->count());

        // Test removing an upVote from a post
        $result = $user->upVotePost($post, false);
        $this->assertNull($result);
        $this->assertFalse($user->upVotedPosts->contains($post));
        $this->assertEquals(0, $user->upVotedPosts()->count());

        // Test removing an upVote from an already non-upVoted post
        $result = $user->upVotePost($post, false);
        $this->assertNull($result);
        $this->assertEquals(0, $user->upVotedPosts()->count());
    }
}
