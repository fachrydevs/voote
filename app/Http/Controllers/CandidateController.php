<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AuditService;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Illuminate\Support\Facades\Validator;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        // $this->middleware('admin')->expect(['index','show']);
        $this->auditService = $auditService;
    }

    public function index(Election $election)
    {
        //
        $candidates = $election->candidates()->paginate(10);
        return response()->json($candidates);
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
        $validator = Validator::make($request->all(), [
            'name' =>'required|string|max:255',
            'description' =>'required|string',
            'image' => 'nullable|string',
            'order_number' =>'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json([
               'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            DB::beginTransaction();
            $candidate = new Candidate([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $request->image?? null,
                'order_number' => $request->order_number ?? ($election->candidates()->max('order_number') + 1),
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('candidates', 'public');
                $candidate->image = $request->file('image')->getClientOriginalName();
                $candidate->image_path = $imagePath;
            }

             
            $election->candidates()->save($candidate);
            $this->auditService->log(
                'Candidate Created',
                "Candidate created for election {$election->title}: {$candidate->name}",
                null,
                $candidate->fresh()->toArray()
            );

            DB::commit();
            return response()->json([
               'message' => 'Candidate created successfully',
                'candidate' => $candidate->fresh(),
            ], 201);


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
               'message' => 'Failed to create candidate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Election $election, Candidate $candidate)
    {
        //
        if ($candidate->election_id !== $election->id) {
            return response()->json([
                'message' => 'Candidate not found in this election',
            ], 404);
        }


       try {
        $candidate->load('election','votes');

        return response()->json($candidate);
       } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to retrieve candidate',
            'error' => $e->getMessage()
        ], 500);
       }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Candidate $candidate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Election $election, Candidate $candidate)
    {
        if ($candidate->election_id !== $election->id) {
            return response()->json([
                'message' => 'Candidate not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'order_number' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $oldData = $candidate->toArray();
            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'order_number' => $request->order_number,
            ];

            if ($request->hasFile('image')) {
                if ($candidate->image_path) {
                    FacadesStorage::disk('public')->delete($candidate->image_path);
                }

                $imagePath = $request->file('image')->store('candidates', 'public');
                $updateData['image'] = $request->file('image')->getClientOriginalName();
                $updateData['image_path'] = $imagePath;
            }

            $candidate->update($updateData);

            $this->auditService->log(
                'Candidate Update',
                "Candidate Updated: {$candidate->name} for election {$election->title}",
                $oldData,
                $candidate->fresh()->toArray()
            );

            DB::commit();

            return response()->json([
                'message' => 'Candidate updated successfully',
                'candidate' => $candidate->fresh(),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to update candidate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Election $election, Candidate $candidate)
    {
        if ($candidate->election_id !== $election->id) {
            return response()->json([
                'message' => 'Candidate not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $candidateData = $candidate->toArray();
            $candidateName = $candidate->name;

            if ($candidate->image_path) {
                FacadesStorage::disk('public')->delete($candidate->image_path);
            }

            $candidate->delete();

            $this->auditService->log(
                'Candidate Deleted',
                "Candidate deleted from election {$election->title}: {$candidateName}",
                $candidateData,
                null
            );

            DB::commit();

            return response()->json([
                'message' => 'Candidate deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete candidate',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
