<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'roles' => $user ? $user->communities()
                    ->select(['communities.id', 'is_admin', 'is_manager', 'is_neighbor'])
                    ->get()
                    ->mapWithKeys(function ($community) {
                        return [$community->id => [
                            'is_admin' => (bool) $community->pivot->is_admin,
                            'is_manager' => (bool) $community->pivot->is_manager,
                            'is_neighbor' => (bool) $community->pivot->is_neighbor,
                        ]];
                    })->values()->all() : null,
            ],
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
            ]
        ]);
    }
}
