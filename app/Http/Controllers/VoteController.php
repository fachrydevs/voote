<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Election;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

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
    public function store(Request $request, Election $election)
    {
        //
        if (!$election->isActive()) {
            return response()->json([
                'message' => 'Election is not active or has ended'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $candidate = Candidate::find($request->candidate_id);
        if($candidate->election_id !== $election->id) {
            return response()->json([
                'message' => 'Candidate does not belong to this election'
            ], 400);
        }

        $userId = Auth::user()->id;

        $hasVoted = Vote::where('user_id', $userId)
            ->where('election_id', $election->id)
            ->exists();

            if($hasVoted) {
                return response()->json([
                    'message' => 'You have already voted for this election'
                ], 400);
            }

            $vote = Vote::create([
                'user_id' => $userId,
                'election_id' => $election->id,
                'candidate_id' => $request->candidate_id,
            ]);

            $this->auditService->log('Vote Created', "Vote cast in election : {$election->title}", null, $vote->toArray());

            return response()->json([
               'message' => 'Vote cast successfully'
            ], 201);
    }

    public function checkStatus(Election $election) {
        $user = Auth::user();

        $hasVoted = Vote::where('user_id', $user->id)
            ->where('election_id', $election->id)
            ->exists();

            return response()->json([
                'hasVoted' => $hasVoted,
                'election_active' => $election->isActive(),
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vote $vote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vote $vote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vote $vote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vote $vote)
    {
        //
    }
}
