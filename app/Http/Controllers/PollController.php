<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePollRequest;
use App\Http\Requests\UpdatePollRequest;
use App\Models\Poll;
use App\Models\PollOption;

class PollController extends Controller
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
     * Display the specified resource.
     */
    public function show(Poll $poll)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Poll $poll)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Poll $poll)
    {
        //
    }

    public function vote(PollOption $pollOption)
    {
        $user = auth()->user();
        $poll = $pollOption->poll;

        // Check if poll is still open
        if ($poll->deadline < now()) {
            return response()->json([
                'message' => 'Esta encuesta está cerrada'
            ], 403);
        }

        // Check if user has already voted in this poll
        $hasVoted = $poll->options()
            ->whereHas('voters', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->exists();

        if ($hasVoted) {
            return response()->json([
                'message' => 'Ya has votado en esta encuesta'
            ], 403);
        }

        // Create the vote
        $pollOption->voters()->attach($user->id);

        return response()->json([
            'success' => true,
            'total_votes' => $poll->options->sum(function ($option) {
                return $option->voters()->count();
            }),
            'options' => $poll->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'votes' => $option->voters()->count()
                ];
            })
        ]);
    }
}
