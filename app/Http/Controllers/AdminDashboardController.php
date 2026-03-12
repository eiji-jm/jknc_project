<?php

namespace App\Http\Controllers;

use App\Models\TownHallCommunication;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $communications = TownHallCommunication::latest()->paginate(10);

        $pendingCount = TownHallCommunication::where('approval_status', 'Pending')->count();
        $approvedCount = TownHallCommunication::where('approval_status', 'Approved')->count();
        $rejectedCount = TownHallCommunication::where('approval_status', 'Rejected')->count();
        $revisionCount = TownHallCommunication::where('approval_status', 'Needs Revision')->count();

        return view('admin.admin-dashboard', compact(
            'communications',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'revisionCount'
        ));
    }
}
