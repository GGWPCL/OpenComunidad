<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowPostRequest;
use App\Models\Category;
use App\Http\Requests\UpVotePostRequest;
use App\Models\Community;
use App\Models\Poll;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\ContentModerationService;
use DateTime;

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

            'poll' => 'nullable|array',
            'poll_question' => 'required_with:poll|string|max:255',
            // 'poll_deadline' => 'required_with:poll|date', // TODO: get from request
            'poll_options' => 'required_with:poll|array|min:2|max:6',
            'poll_options.*' => 'required_with:poll|string|max:255',
        ]);

        // Extract post data
        $postData = [
            'original_title' => $validated['original_title'],
            'mutated_title' => $this->contentModerationService->moderateContent($validated['original_title']) ?? $validated['original_title'], // TODO: how to handle this?
            'original_content' => $validated['original_content'],
            'mutated_content' => $this->contentModerationService->moderateContent($validated['original_content']) ?? $validated['original_content'], // TODO: how to handle this?
            'category_id' => $validated['category_id'],
            'author_id' => $request->user()->id,
            'community_id' => Community::where('slug', $request->community)->firstOrFail()->id,
        ];

        $post = Post::create($postData);

        if (isset($validated['poll_question'])) {
            $pollData = [
                'post_id' => $post->id,
                'original_content' => $validated['poll_question'],
                'mutated_content' => $this->contentModerationService->moderateContent($validated['poll_question']) ?? $validated['poll_question'], // TODO: how to handle this?
                'deadline' => new DateTime('+7 days'), // TODO: get from request
            ];
            $poll = Poll::create($pollData);

            foreach ($validated['poll_options'] ?? [] as $option_title) {
                $poll->options()->create([
                    'original_title' => $option_title,
                    'mutated_title' => $this->contentModerationService->moderateContent($option_title) ?? $option_title, // TODO: how to handle this?
                ]);
            }
        }

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
        // {
        //     question: "¿Qué día prefieren para la próxima reunión de vecinos?",
        //     options: [
        //         { id: 1, text: "Sábado por la mañana", votes: 12 },
        //         { id: 2, text: "Sábado por la tarde", votes: 8 },
        //         { id: 3, text: "Domingo por la mañana", votes: 15 },
        //         { id: 4, text: "Domingo por la tarde", votes: 5 },
        //     ],
        //     total_votes: 40,
        //     closed: false,
        //     ends_at: "2024-11-30T23:59:59"
        // };
        $pollData = null;
        if ($post->poll) {
            $pollData = [
                'question' => $post->poll->mutated_content,
                'options' => $post->poll->options->map(fn($option) => [
                    'id' => $option->id,
                    'text' => $option->mutated_title,
                    'votes' => $option->votes?->count(),
                ]),
                'total_votes' => $post->poll->options->sum('votes'),
                'closed' => $post->poll->deadline < now(),
                'deadline' => $post->poll->deadline,
            ];
        }

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
            'poll' => $pollData,
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
