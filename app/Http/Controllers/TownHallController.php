<?php

namespace App\Http\Controllers;

use App\Models\TownHallCommunication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TownHallAcknowledgement;
use Carbon\Carbon;
use App\Models\User;


class TownHallController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('access_townhall')) {
            abort(403, 'Unauthorized');
        }

        $query = TownHallCommunication::latest();

        if (!Auth::user()->hasPermission('approve_townhall')) {
            $query->where('approval_status', 'Approved');
        }

        $communications = $query->paginate(10);

        return view('townhall.townhall', compact('communications'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create_townhall')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'communication_date' => ['nullable', 'date'],
            'department_stakeholder' => ['nullable', 'string', 'max:255'],
            'to_for' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'in:High,Low'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'cc' => ['nullable', 'string', 'max:255'],
            'additional' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx', 'max:5120'],
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            if (!$file->isValid()) {
                return back()
                    ->withErrors(['attachment' => 'The attachment failed to upload.'])
                    ->withInput();
            }

            $validated['attachment'] = $file->store('townhall_attachments', 'public');
        }

        $validated['from_name'] = Auth::user()->name;
        $validated['priority'] = $request->priority ?? 'Low';
        $validated['created_by'] = Auth::id();
        $validated['approval_status'] = 'Pending';

        $communication = TownHallCommunication::create($validated);

        $communication->ref_no = 'TH-' . str_pad((string) $communication->id, 5, '0', STR_PAD_LEFT);
        $communication->save();

        return redirect()
            ->route('townhall')
            ->with('success', 'Town Hall communication created successfully.');
    }

    public function show($id)
    {
        if (!Auth::user()->hasPermission('access_townhall')) {
            abort(403, 'Unauthorized');
        }

        $communication = TownHallCommunication::findOrFail($id);

        if (
            !Auth::user()->hasPermission('approve_townhall') &&
            $communication->approval_status !== 'Approved'
        ) {
            abort(403, 'Unauthorized');
        }

        // attachment type (your existing)
        $attachmentType = null;
        if ($communication->attachment) {
            $ext = strtolower(pathinfo($communication->attachment, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'])) {
                $attachmentType = 'image';
            } elseif ($ext === 'pdf') {
                $attachmentType = 'pdf';
            } else {
                $attachmentType = 'file';
            }
        }

        // ✅ ACKNOWLEDGEMENT DATA
        $employees = User::where('role', 'Employee')->get();

        $acknowledgedUserIds = TownHallAcknowledgement::where('townhall_communication_id', $id)
            ->pluck('user_id')
            ->toArray();

        $acknowledgedUsers = $employees->whereIn('id', $acknowledgedUserIds);
        $notAcknowledgedUsers = $employees->whereNotIn('id', $acknowledgedUserIds);

        $totalEmployees = $employees->count();
        $ackCount = count($acknowledgedUserIds);

        $progress = $totalEmployees > 0
            ? round(($ackCount / $totalEmployees) * 100)
            : 0;

        // existing flags
        $hasAcknowledged = $communication->hasBeenAcknowledgedBy(Auth::id());
        $requiresAcknowledgement = Auth::user()->role === 'Employee'
            && $communication->approval_status === 'Approved';

        return view('townhall.show', compact(
            'communication',
            'attachmentType',
            'hasAcknowledged',
            'requiresAcknowledgement',

            // NEW
            'acknowledgedUsers',
            'notAcknowledgedUsers',
            'progress',
            'totalEmployees',
            'ackCount'
        ));
    }

    public function approve(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('approve_townhall')) {
            abort(403, 'Unauthorized');
        }

        $communication = TownHallCommunication::findOrFail($id);

        $communication->update([
            'approval_status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
            'approval_notes' => $request->input('approval_notes'),
        ]);

        return redirect()->back()->with('success', 'Communication approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('approve_townhall')) {
            abort(403, 'Unauthorized');
        }

        $communication = TownHallCommunication::findOrFail($id);

        $communication->update([
            'approval_status' => 'Rejected',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
            'approval_notes' => $request->input('approval_notes'),
        ]);

        return redirect()->back()->with('success', 'Communication rejected successfully.');
    }

    public function revise(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('approve_townhall')) {
            abort(403, 'Unauthorized');
        }

        $communication = TownHallCommunication::findOrFail($id);

        $communication->update([
            'approval_status' => 'Needs Revision',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
            'approval_notes' => $request->input('approval_notes'),
        ]);

        return redirect()->back()->with('success', 'Communication marked for revision.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('create_townhall')) {
            abort(403, 'Unauthorized');
        }

        $communication = TownHallCommunication::findOrFail($id);

        if ($communication->attachment && Storage::disk('public')->exists($communication->attachment)) {
            Storage::disk('public')->delete($communication->attachment);
        }

        $communication->delete();

        return redirect()
            ->route('townhall')
            ->with('success', 'Communication deleted successfully.');
    }

    public function acknowledge($id)
    {
        if (!Auth::user()->hasPermission('access_townhall')) {
            abort(403, 'Unauthorized');
        }

        $communication = TownHallCommunication::findOrFail($id);

        // Only approved communications can be acknowledged
        if ($communication->approval_status !== 'Approved') {
            abort(403, 'This communication is not available for acknowledgment.');
        }

        // Only employees need acknowledgment
        if (Auth::user()->role !== 'Employee') {
            return redirect()->back()->with('success', 'Acknowledgment is not required for your role.');
        }

        TownHallAcknowledgement::updateOrCreate(
            [
                'townhall_communication_id' => $communication->id,
                'user_id' => Auth::id(),
            ],
            [
                'acknowledged_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Communication acknowledged successfully.');
    }
}
