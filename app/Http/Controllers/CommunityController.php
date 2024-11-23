<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use App\Models\Community;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
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
        $isMember = false;

        if (auth()->check()) {
            $isMember = $community->users()
                ->where('user_id', auth()->id())
                ->exists();
        }

        return Inertia::render('Communities/Show', [
            'community' => [
                'name' => $community->name,
                'isMember' => $isMember,
                'logo' => $community->logo?->url,
                'banner' => $community->banner?->url,
            ]
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
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('community-logos', 'r2');
            $file = File::create([
                'name' => $request->file('logo')->getClientOriginalName(),
                'mime' => $request->file('logo')->getMimeType(),
                'url' => Storage::disk('r2')->url($path),
                'metadata' => [
                    'size' => $request->file('logo')->getSize(),
                    'path' => $path,
                ],
            ]);
            $community->logo_id = $file->id;
        }

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('community-banners', 'r2');
            $file = File::create([
                'name' => $request->file('banner')->getClientOriginalName(),
                'mime' => $request->file('banner')->getMimeType(),
                'url' => Storage::disk('r2')->url($path),
                'metadata' => [
                    'size' => $request->file('banner')->getSize(),
                    'path' => $path,
                ],
            ]);
            $community->banner_id = $file->id;
        }

        $community->name = $request->name;
        $community->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community)
    {
        //
    }
}
