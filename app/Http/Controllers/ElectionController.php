<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ElectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $auditService;
    public function __construct(AuditService $auditService) {
        // $this->middleware('admin')->expect(['index', 'show']);
        $this->auditService = $auditService;
    }

    public function index()
    {
        //
        $elections = Election::with(['creator', 'candidates'])->paginate('10');

        return response()->json($elections);
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' =>'boolean',
            'candidates' => 'required|array|min:1',
            'candidates.*.name' => 'required|string|max:255',
            'candidates.*.description' => 'required|string',
            'candidates.*.image' => 'nullable|string',
            'candidates.*.order_number' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            $election = Election::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->is_active,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->candidates as $candidateData) {
                $candidate = new Candidate([
                    'name' => $candidateData['name'],
                    'description' => $candidateData['description'],
                    'image' => $candidateData['image'] ?? null,
                    'order_number' => $candidateData['order_number']
                ]);
                $election->candidates()->save($candidate);
            }

            DB::commit();

            $this->auditService->log(
                'Election Created', 
                "Election created with " . count($request->candidates) . " candidates: {$election->title}", 
                null, 
                $election->load('candidates')->toArray()
            );

            return response()->json([
                'message' => 'Election and candidates created successfully',
                'election' => $election->load('candidates')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create election and candidates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Election $election)
    {
        //
        $election->load(['creator', 'candidates']);
        return response()->json($election);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Election $election)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Election $election)
    {
        $validator = Validator::make($request->all(), [
            "title" => "string|max:255",
            "description" => "string",
            "start_date" => "date",
            "end_date" => "date|after:start_date",
            "is_active" => "boolean"
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        DB::beginTransaction();
        try {
            $oldData = $election->toArray();
    
            $election->update($request->only(['title', 'description', 'start_date', 'end_date', 'is_active']));
    
            DB::commit();
    
            $this->auditService->log(
                'Election Updated', 
                "Election updated: {$election->title}", 
                $oldData, 
                $election->toArray()
            );
    
            return response()->json([
                'message' => 'Election updated successfully',
                'election' => $election
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update election',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Election $election)
    {
        //
        $electionData = $election->toArray();
        $electionTitle = $election->title;

        $election->delete();

        $this->auditService->log('Election Deleted', "Election deleted : {$electionTitle}", $electionData, null);

        return response()->json([
           'message' => 'Election deleted successfully'
        ], 200);
    }

    public function results(Election $election) {
        if (!Auth::user()->is_admin && $election->end_date > now()) {
            return response()->json(['message' => 'Election results not availbale yet'], 403);
        }

        $candidates = $election->candidates()->withCount('votes')->get();

        $totalVotes = $election->votes()->count();

        $results = [
            'total_votes' => $totalVotes,
            'candidates' => $candidates->map(function ($candidate) use ($totalVotes) {
                $percentage = $totalVotes > 0 ? ($candidate->votes_count / $totalVotes) * 100 : 0;

                return [
                    'id' => $candidate->id,
                    'name' => $candidate->name,
                    'votes' => $candidate->votes_count,
                    'percentage' => round($percentage, 2),
                ];
            }),
        ];

        return response()->json($results);
    }

}
