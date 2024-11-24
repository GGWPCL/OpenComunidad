<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Models\Community;
use App\Http\Controllers\CommentController;
use App\Utils\MediaProcessor;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'communities' => Community::with(['logo', 'banner'])->get()->each(function ($community) {
            MediaProcessor::processMediaUrls($community);
        }),
    ]);
});

Route::get('/communities/{community:slug}', [CommunityController::class, 'show'])->name('communities.show');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');


Route::middleware(['auth', 'onboarding.complete'])->group(function () {
    Route::get('/dashboard', function () {
        $userCommunities = auth()->user()->communities()
            ->with(['logo', 'banner'])
            ->get()
            ->each(function ($community) {
                MediaProcessor::processMediaUrls($community);
            });

        $otherCommunities = Community::whereDoesntHave('users', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with(['logo', 'banner'])
            ->get()
            ->each(function ($community) {
                MediaProcessor::processMediaUrls($community);
                $community->view_only = true;
            });

        return Inertia::render('Dashboard', [
            'userCommunities' => $userCommunities,
            'otherCommunities' => $otherCommunities,
            'isAdmin' => auth()->user()->communities()->wherePivot('is_admin', true)->exists(),
        ]);
    })->name('dashboard');

    Route::post('/communities/{community}', [CommunityController::class, 'update'])
        ->name('communities.update');

    Route::post('/posts/{post}/up-vote', [PostController::class, 'upVote'])->name('posts.up_vote');
    Route::post('/posts/{post}/follow', [PostController::class, 'follow'])->name('posts.follow');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/posts/preflight', [PostController::class, 'preflight'])->name('posts.preflight');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/communities/{community}/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/communities/{community}/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
});

require_once __DIR__ . '/auth.php';
