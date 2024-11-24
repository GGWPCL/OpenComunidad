<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Community;
use App\Models\User;
use App\Models\Post;
use App\Models\Poll;
use App\Models\PollOption;
use App\Services\ContentModerationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the ContentModerationService
        $this->mock(ContentModerationService::class, function ($mock) {
            $mock->shouldReceive('moderateContent')
                ->andReturnArg(0); // Return the input unchanged
        });
    }

    public function test_store_creates_post_with_poll_and_options(): void
    {
        // Create necessary test data
        $user = User::factory()->create();
        $community = Community::factory()->create([
            'name' => 'Test Community',
            'slug' => 'test-community',
        ]);
        $category = Category::factory()->create([
            'internal_name' => 'test-category',
            'display_name' => 'Test Category',
        ]);

        // Add user to community
        $community->users()->attach($user, [
            'is_admin' => true,
            'is_manager' => true,
            'is_neighbor' => true,
        ]);

        $deadline = Carbon::now()->addDays(7);

        $postData = [
            'original_title' => 'Test Post Title',
            'original_content' => 'Test Post Content',
            'category_id' => $category->id,
            'poll' => [
                'question' => 'What is your favorite color?',
                'deadline' => $deadline->format('Y-m-d H:i:s'),
                'options' => [
                    ['text' => 'Red'],
                    ['text' => 'Blue'],
                    ['text' => 'Green']
                ]
            ]
        ];

        // Make the request
        $response = $this->actingAs($user)
            ->post(route('posts.store', ['community' => $community->slug]), $postData);

        // Assert the post was created
        $this->assertDatabaseHas('posts', [
            'original_title' => 'Test Post Title',
            'mutated_title' => 'Test Post Title',
            'original_content' => 'Test Post Content',
            'mutated_content' => 'Test Post Content',
            'category_id' => $category->id,
            'community_id' => $community->id,
            'author_id' => $user->id,
        ]);

        // Get the created post and verify relationships
        $post = Post::latest()->first();

        // Assert poll was created
        $this->assertDatabaseHas('polls', [
            'post_id' => $post->id,
            'original_title' => 'What is your favorite color?',
            'mutated_title' => 'What is your favorite color?',
            'deadline' => $deadline->format('Y-m-d H:i:s'),
        ]);

        // Assert poll options were created
        $poll = Poll::where('post_id', $post->id)->first();
        $this->assertCount(3, $poll->options);

        // Assert each option was created
        foreach (['Red', 'Blue', 'Green'] as $optionText) {
            $this->assertDatabaseHas('poll_options', [
                'poll_id' => $poll->id,
                'original_title' => $optionText,
                'mutated_title' => $optionText,
            ]);
        }

        // Assert relationships
        $this->assertInstanceOf(Poll::class, $post->poll);
        $this->assertInstanceOf(PollOption::class, $post->poll->options->first());

        // Assert redirect
        $response->assertRedirect(route('communities.show', [
            'community' => $community->slug,
            'category' => $category->internal_name,
        ]));
    }
}
