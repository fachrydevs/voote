<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vote;
use App\Models\AuditLog;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'admin']);
    // }

    public function statistics() {
        $totalUsers = User::count();
        $totalElections = Election::count();
        $totalVotes = Vote::count();
        $activeElections = Election::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())->count();


            $votesPerDay = Vote::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Users per role
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get();
        
        // Top 5 elections by vote count
        $topElections = Election::withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'votes_count']);
        
        return response()->json([
            'total_users' => $totalUsers,
            'total_elections' => $totalElections,
            'total_votes' => $totalVotes,
            'active_elections' => $activeElections,
            'votes_per_day' => $votesPerDay,
            'users_by_role' => $usersByRole,
            'top_elections' => $topElections,
        ]);
    }

    public function auditLogs(Request $request) {
        $query = AuditLog::with('user')->latest();

        if ($request->has('email')) {
            $query->where('email', 'like', "%{$request->email}%");
        }
        
        // Filter by activity type
        if ($request->has('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }
        
        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $auditLogs = $query->paginate(15);
        
        return response()->json($auditLogs);
    
    }

    public function recentActivity()
    {
        $recentLogs = AuditLog::with('user')
            ->latest()
            ->limit(10)
            ->get();
            
        return response()->json($recentLogs);
    }
}
