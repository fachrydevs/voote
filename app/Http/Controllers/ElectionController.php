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
        $elections = Election::all();
        return response()->json($elections);
       
    }
    public function getActiveElections() {
        $elections = Election::where('is_active', true)
            ->with(['creator', 'candidates'])
            ->paginate(10);

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
        try {
            DB::beginTransaction();
            
            // Create the election first
            $election = Election::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->is_active
            ]);
    
            // Then handle the candidates
            if ($request->has('candidates')) {
                // 2. Gunakan Transaksi Database
                // Ini memastikan semua kandidat berhasil disimpan, atau tidak sama sekali.
                DB::beginTransaction();
                try {
                    foreach ($request->candidates as $index => $candidateData) {
                        $imageUrl = null;
            
                        // Cek apakah ada file gambar untuk kandidat ini
                        if ($request->hasFile("candidates.{$index}.image")) {
                            $image = $request->file("candidates.{$index}.image");
                            
                            // 3. Simpan file ke disk 'public'
                            // Laravel akan otomatis generate nama unik. Path ini akan disimpan di database.
                            // Contoh path yang tersimpan: 'candidates/asdf89asdf98asdf.jpg'
                            $imagePath = $image->store('candidates', 'public');
                            
                            // Simpan path relatif ini ke database
                            $imageUrl = $imagePath;
                        }
            
                        $candidate = new Candidate([
                            'name'         => $candidateData['name'],
                            'description'  => $candidateData['description'],
                            'order_number' => $candidateData['order_number'],
                            'image'        => $imageUrl,
                            'election_id'  => $election->id,
                        ]);
                        
                        // Simpan kandidat
                        $candidate->save(); // Atau bisa juga: $election->candidates()->save($candidate);
                    }
                    
                    // Jika semua berhasil, commit transaksi
                    DB::commit();
            
                } catch (\Exception $e) {
                    // Jika terjadi error, batalkan semua yang sudah disimpan
                    DB::rollBack();
                    
                    // Kembalikan response error
                    return response()->json([
                        'message' => 'Terjadi kesalahan saat menyimpan kandidat.',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
            DB::commit();
    
            return response()->json([
                'message' => 'Election created successfully',
                'data' => $election->load('candidates')
            ], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create election',
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
