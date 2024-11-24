<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Models\Community;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'communities' => Community::all(),
    ]);
});

Route::get('/communities/{community:slug}', [CommunityController::class, 'show'])->name('communities.show');

Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::post('/posts/{post}/up-vote', [PostController::class, 'upVote'])->name('posts.up_vote');
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');


Route::middleware(['auth', 'onboarding.complete'])->group(function () {
    Route::get('/dashboard', function () {
        $userCommunities = auth()->user()->communities()->with('logo', 'banner')->get();

        $userCommunities = $userCommunities->map(function ($community) {
            if ($community->logo) {
                $path = parse_url($community->logo->url, PHP_URL_PATH);
                $community->logo->url = 'https://storage.opencomunidad.cl' . $path;
            }
            if ($community->banner) {
                $path = parse_url($community->banner->url, PHP_URL_PATH);
                $community->banner->url = 'https://storage.opencomunidad.cl' . $path;
            }
            return $community;
        });

        return Inertia::render('Dashboard', [
            'userCommunities' => $userCommunities,
            'isAdmin' => auth()->user()->communities()->wherePivot('is_admin', true)->exists(),
        ]);
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/communities/{community}/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/communities/{community}/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');

    Route::post('/communities/{community}', [CommunityController::class, 'update'])
        ->name('communities.update');
});

require __DIR__ . '/auth.php';
