<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\TownHallCommunication;

class AdminDashboardController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('access_admin_dashboard')) {
            abort(403, 'Unauthorized');
        }

        $communications = TownHallCommunication::latest()->paginate(10);

        $pendingCount = TownHallCommunication::where('approval_status', 'Pending')
            ->where('is_archived', false)
            ->count();

        $approvedCount = TownHallCommunication::where('approval_status', 'Approved')
            ->where('is_archived', false)
            ->count();

        $rejectedCount = TownHallCommunication::where('approval_status', 'Rejected')
            ->where('is_archived', false)
            ->count();

        $revisionCount = TownHallCommunication::where('approval_status', 'Needs Revision')
            ->where('is_archived', false)
            ->count();

        $expiredCount = TownHallCommunication::where('is_archived', true)->count();

        return view('admin.admin-dashboard', compact(
            'communications',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'revisionCount',
            'expiredCount'
        ));
    }
}
