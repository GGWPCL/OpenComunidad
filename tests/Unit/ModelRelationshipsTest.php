<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Community;
use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use App\Models\UserCommunity;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    public function test_model_relationships(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $community = Community::factory()->create([
            'name' => 'Test Community',
        ]);
        $user->communities()->attach($community, [
            'is_admin' => true,
            'is_manager' => true,
            'is_neighbor' => true,
        ]);
        $community->users()->attach($user2, [
            'is_admin' => false,
            'is_manager' => false,
            'is_neighbor' => true,
        ]);

        $category = Category::factory()->create([
            'display_name' => 'Test Category',
            'internal_name' => 'test_category',
        ]);

        $post = Post::factory()->create([
            'author_id' => $user->id,
            'community_id' => $community->id,
            'category_id' => $category->id,
            'original_title' => 'Original Title',
            'mutated_title' => 'Mutated Title',
            'original_content' => 'Original Content',
            'mutated_content' => 'Mutated Content',
        ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'author_id' => $user2->id,
            'original_content' => 'Original Comment',
            'mutated_content' => 'Mutated Comment',
        ]);

        $follow = Follow::factory()->create([
            'user_id' => $user2->id,
            'post_id' => $post->id,
        ]);

        $this->assertInstanceOf(Community::class, $user->communities->first());
        $this->assertInstanceOf(User::class, $community->users->first());

        $this->assertInstanceOf(Post::class, $user->posts->first());
        $this->assertInstanceOf(Post::class, $community->posts->first());

        $this->assertInstanceOf(Category::class, $post->category);
        $this->assertInstanceOf(User::class, $post->author);
        $this->assertInstanceOf(Community::class, $post->community);
        $this->assertInstanceOf(Comment::class, $post->comments->first());

        $this->assertInstanceOf(Post::class, $comment->post);
        $this->assertInstanceOf(User::class, $comment->author);
        $this->assertInstanceOf(Comment::class, $user2->comments->first());
        $this->assertNull($user->comments->first());

        $this->assertInstanceOf(User::class, $follow->user);
        $this->assertInstanceOf(Post::class, $follow->post);
        $this->assertInstanceOf(Post::class, $user2->followedPosts->first());
        $this->assertNull($user->followedPosts->first());
    }
}
