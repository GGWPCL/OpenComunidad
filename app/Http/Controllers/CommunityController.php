<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use App\Models\Category;
use App\Models\Community;
use App\Models\Post;
use Inertia\Inertia;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommunityRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community)
    {
        $categories = Category::select('display_name as name', 'internal_name', 'icon')->get();
        $selectedCategory = request()->query('category');

        $postsQuery = Post::select(
            'posts.id',
            'posts.mutated_title as title',
            'posts.mutated_content as content',
            'categories.display_name as category',
            'users.name as author'
        )
            ->selectRaw('(SELECT COUNT(*) FROM up_votes WHERE up_votes.post_id = posts.id) as votes')
            ->selectRaw('(SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) as comments')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->join('users', 'posts.author_id', '=', 'users.id')
            ->where('posts.community_id', $community->id)
            ->addSelect('posts.created_at');

        if ($selectedCategory) {
            $postsQuery->where('categories.internal_name', $selectedCategory);
        }

        $posts = $postsQuery->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'category' => $post->category,
                'author' => $post->author,
                'votes' => (int) $post->votes,
                'comments' => (int) $post->comments,
                'createdAt' => $post->created_at->diffForHumans()
            ];
        });

        return Inertia::render('Communities/Show', [
            'community' => [
                'name' => $community->name,
            ],
            'categories' => $categories,
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Community $community)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunityRequest $request, Community $community)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community)
    {
        //
    }
}
