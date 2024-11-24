<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use App\Models\Category;
use App\Models\Community;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;


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
        $community = Community::with(['logo', 'banner'])->find($community->id);
        $this->processMediaUrls($community);
        $user = Auth::user();
        $isMember = false;
        if ($user) {
            $isMember = $community->users()
                ->where('user_id', $user->id)
                ->exists();
        }

        $categories = Category::select('display_name as name', 'internal_name', 'icon', 'description')->get();
        $selectedCategory = request()->query('category');

        $postsQuery = Post::where('community_id', $community->id);
        if ($selectedCategory) {
            $postsQuery->whereHas('category', function ($query) use ($selectedCategory) {
                $query->where('internal_name', $selectedCategory);
            });
        }

        $posts = $postsQuery->get()
            ->map(function (Post $post) use ($user) {
                return [
                    'id' => $post->id,
                    'title' => $post->mutated_title,
                    'content' => $post->mutated_content,
                    'category' => $post->category?->display_name,
                    'author' => $post->author?->id,
                    'comments' => (int) $post->comments?->count(),
                    'votes' => (int) $post->upVotedBy()?->count(),
                    'isUpVoted' => $user instanceof User && $user->upVotedPosts()?->where('post_id', $post->id)->exists(),
                    'createdAt' => $post->created_at->diffForHumans()
                ];
            })
            ->values()
            ->all();

        return Inertia::render('Communities/Show', [
            'community' => [
                'name' => $community->name,
                'slug' => $community->slug,
                'isMember' => $isMember,
                'logo' => $community->logo,
                'banner' => $community->banner,
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
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:communities,slug,' . $community->id,
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',
        ]);
        foreach (['logo', 'banner'] as $fileType) {
            if ($request->hasFile($fileType)) {
                $file = $this->handleFileUpload(
                    $request->file($fileType),
                    "community-{$fileType}s"
                );
                $validated["{$fileType}_id"] = $file->id;
            }
        }

        $community->update($validated);

        return redirect()->back()->with('success', 'Community updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community)
    {
        //
    }

    /**
     * Handle file upload and return the created File model
     */
    private function handleFileUpload($uploadedFile, string $directory): File
    {
        $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
        $file = $uploadedFile->storePubliclyAs($directory, $fileName, 'r2');

        return File::create([
            'path' => $file,
            'mime' => $uploadedFile->getMimeType(),
            'name' => (string) \Illuminate\Support\Str::uuid(),
            'url' => Storage::disk('r2')->url($file),
        ]);
    }

    private function processMediaUrls($community)
    {
        $baseUrl = 'https://storage.opencomunidad.cl';

        foreach (['logo', 'banner'] as $mediaType) {
            if ($community->{$mediaType}) {
                $path = parse_url($community->{$mediaType}->url, PHP_URL_PATH);
                $community->{$mediaType}->url = $baseUrl . $path;
            }
        }
    }
}
