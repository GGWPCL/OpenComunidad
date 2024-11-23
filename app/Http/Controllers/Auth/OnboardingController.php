<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Community;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function index()
    {
        $communities = Community::all(['id', 'name', 'slug']);
        if (Auth::user()->onboarding && Auth::user()->communities()->exists()) {
            return redirect()->route('dashboard');
        }

        return inertia('Auth/Onboarding', [
            'auth' => Auth::user(),
            'communities' => $communities,
        ]);
    }

    public function complete(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'community_id' => 'required|exists:communities,id',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->onboarding = true;
        $user->save();

        if (!$user->communities()->where('community_id', $request->community_id)->exists()) {
            $user->communities()->attach($request->community_id, ['is_neighbor' => true]);
        }

        return redirect()->route('dashboard');
    }
}
