<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\TownHallCommunication;

class AdminDashboardController extends Controller
{


    public function index()
    {
        if (!Auth::user()->hasPermission('access_admin_dashboard')) {
            abort(403, 'Unauthorized');
        }
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
