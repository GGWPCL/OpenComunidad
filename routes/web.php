<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Models\Community;
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'communities' => Community::all(),
    ]);
});

Route::get('/communities/{community:slug}', [CommunityController::class, 'show'])->name('communities.show');
Route::post('/posts/{post}/up-vote', [PostController::class, 'upVote'])->name('posts.up_vote');


Route::middleware(['auth', 'onboarding.complete'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'userCommunities' => auth()->user()->communities()->get()
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
});

require __DIR__ . '/auth.php';
