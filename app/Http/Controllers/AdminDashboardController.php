<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\TownHallCommunication;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        if (
            !Auth::user()->hasPermission('access_admin_dashboard') &&
            !Auth::user()->hasPermission('approve_townhall')
        ) {
            abort(403, 'Unauthorized');
        }

        $communications = TownHallCommunication::with(['uploader', 'approver'])
            ->latest()
            ->paginate(10);

        $pendingCount = $this->townHallCountByStatus('Pending');
        $approvedCount = $this->townHallCountByStatus('Approved');
        $rejectedCount = $this->townHallCountByStatus('Rejected');
        $revisionCount = $this->townHallCountByStatus('Needs Revision');
        $expiredCount = Schema::hasColumn('townhall_communications', 'is_archived')
            ? TownHallCommunication::where('is_archived', true)->count()
            : 0;

        return view('admin.admin-dashboard', compact(
            'communications',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'revisionCount',
            'expiredCount'
        ));
    }

    private function townHallCountByStatus(string $status): int
    {
        $query = TownHallCommunication::where('approval_status', $status);

        if (Schema::hasColumn('townhall_communications', 'is_archived')) {
            $query->where('is_archived', false);
        }

        return $query->count();
    }
}
