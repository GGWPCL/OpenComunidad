<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\UpVotePostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PostController extends Controller
{
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
        $validated['mutated_content'] = $validated['original_content'];
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
}
