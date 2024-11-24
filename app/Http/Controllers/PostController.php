<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowPostRequest;
use App\Models\Category;
use App\Http\Requests\UpVotePostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\ContentModerationService;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected $contentModerationService;

    public function __construct(ContentModerationService $contentModerationService)
    {
        $this->contentModerationService = $contentModerationService;
    }

    public function create(Request $request, string $community)
    {
        return Inertia::render('Posts/Create', [
            'categories' => Category::all(['id', 'display_name', 'internal_name']),
            'community' => $community,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'original_title' => 'required|string|max:255',
            'original_content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $validated['mutated_title'] = $validated['original_title'];
        $validated['mutated_content'] = $this->contentModerationService->moderateContent($validated['original_content']);
        $validated['author_id'] = $request->user()->id;
        $validated['community_id'] = \App\Models\Community::where('slug', $request->community)->firstOrFail()->id;

        $post = Post::create($validated);

        return redirect()->route('communities.show', [
            'community' => $request->community,
            'category' => $post->category->internal_name,
        ]);
    }

    public function upVote(UpVotePostRequest $request, Post $post)
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return redirect()->route('login');
        }

        $validated = $request->validated();
        $shouldUpVote = $validated['shouldUpVote'];

        $upVote = $user->upVotePost($post, $shouldUpVote);

        return redirect()->back()->with(
            'isUpVoted',
            (bool) $upVote,
        );
    }

    public function follow(FollowPostRequest $request, Post $post)
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return redirect()->route('login');
        }

        $validated = $request->validated();
        $shouldFollow = $validated['shouldFollow'];

        $follow = $user->followPost($post, $shouldFollow);

        return redirect()->back()->with(
            'isFollowed',
            (bool) $follow,
        );
    }

    public function show(Post $post)
    {
        $user = Auth::user();
        $isAdmin = false;

        if ($user) {
            $isAdmin = $post->community->users()
                ->where('user_id', $user->id)
                ->where('is_admin', true)
                ->exists();
        }



        $postData = [
            'id' => $post->id,
            'title' => $post->mutated_title,
            'content' => $post->mutated_content,
            'category' => $post->category?->display_name,
            'author' => $post->author?->id,
            'votes' => (int) $post->upVotedBy()?->count(),
            'isUpVoted' => $user instanceof User && $user->upVotedPosts()?->where('post_id', $post->id)->exists(),
            'isFollowed' => $user instanceof User && $user->followedPosts()?->where('post_id', $post->id)->exists(),
            'createdAt' => $post->created_at->diffForHumans()
        ];

        $comments = $post->comments->sortBy('created_at')->map(fn($comment) => [
            'id' => $comment->id,
            'author' => $comment->author?->id,
            'content' => $comment->mutated_content,
            'createdAt' => $comment->created_at->diffForHumans(),
        ])
            ->values()
            ->all();

        return Inertia::render('Posts/Show', [
            'post' => $postData,
            'comments' => $comments,
            'community' => [
                'name' => $post->community->name,
                'slug' => $post->community->slug,
                'isAdmin' => $isAdmin,
            ]
        ]);
    }

    public function preflight(Request $request)
    {
        $content = $request->input('content');

        $result = $this->contentModerationService->preflight($content);

        logger()->info($result);

        if (!$result) {
            return response()->json([
                'ready' => 0,
                'message' => 'No se pudo validar el contenido. Por favor, inténtalo de nuevo.'
            ]);
        }

        // Parse the JSON string returned from the service
        $preflightResult = json_decode($result, true);

        return response()->json($preflightResult);
    }
}
