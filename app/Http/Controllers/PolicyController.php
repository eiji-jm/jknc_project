<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PolicyController extends Controller
{
    public function index()
    {
        $policies = Policy::where('workflow_status', 'Accepted')
            ->latest()
            ->get();

        return view('policies.policies', compact('policies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:50',
            'effectivity_date' => 'nullable|date',
            'prepared_by' => 'nullable|string|max:255',
            'reviewed_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            if (!$file->isValid()) {
                return back()
                    ->withErrors(['attachment' => 'The attachment failed to upload.'])
                    ->withInput();
            }

            $validated['attachment'] = $file->store('policy_attachments', 'public');
        }

        $policy = Policy::create([
            'code' => null,
            'policy' => $validated['policy'] ?? null,
            'version' => $validated['version'] ?? '1.0',
            'effectivity_date' => $validated['effectivity_date'] ?? null,
            'prepared_by' => $validated['prepared_by'] ?? (Auth::user()->name ?? 'System Admin'),
            'reviewed_by' => $validated['reviewed_by'] ?? null,
            'approved_by' => $validated['approved_by'] ?? null,
            'classification' => $validated['classification'] ?? 'Internal Use',
            'description' => $validated['description'] ?? null,
            'attachment' => $validated['attachment'] ?? null,
            'approval_status' => 'Pending',
            'workflow_status' => 'Submitted',
            'submitted_by' => Auth::id(),
        ]);

        $policy->code = 'POL-' . str_pad((string) $policy->id, 5, '0', STR_PAD_LEFT);
        $policy->save();

        return redirect()
            ->route('policies.index')
            ->with('success', 'Policy submitted for admin review.');
    }
    public function submitted(Request $request)
    {
        if (!Auth::user()->hasPermission('approve_policies')) {
            abort(403, 'Unauthorized');
        }

        $query = Policy::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('policy', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('prepared_by', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('workflow_status', $request->status);
        }

        $policies = $query->latest()->paginate(10)->withQueryString();

        return view('admin.policies-dashboard', compact('policies'));
    }

    public function approve($id)
    {
        if (!Auth::user()->hasPermission('approve_policies')) {
            abort(403, 'Unauthorized');
        }

        $policy = Policy::findOrFail($id);

        $policy->update([
            'approval_status' => 'Approved',
            'workflow_status' => 'Accepted',
            'approved_by_user_id' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Policy approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('approve_policies')) {
            abort(403, 'Unauthorized');
        }

        $policy = Policy::findOrFail($id);

        $policy->update([
            'approval_status' => 'Rejected',
            'workflow_status' => 'Reverted',
            'approved_by_user_id' => Auth::id(),
            'approved_at' => now(),
            'review_note' => $request->input('review_note'),
        ]);

        return redirect()->back()->with('success', 'Policy rejected successfully.');
    }
    public function revise(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('approve_policies')) {
            abort(403, 'Unauthorized');
        }

        $policy = Policy::findOrFail($id);

        $policy->update([
            'approval_status' => 'Needs Revision',
            'workflow_status' => 'Reverted',
            'approved_by_user_id' => Auth::id(),
            'approved_at' => now(),
            'review_note' => $request->input('review_note'),
        ]);

        return redirect()->back()->with('success', 'Policy marked for revision.');
    }

    public function showAdmin($id)
    {
        if (!Auth::user()->hasPermission('approve_policies')) {
            abort(403, 'Unauthorized');
        }

        $policy = Policy::findOrFail($id);

        return view('admin.policy-show', compact('policy'));
    }

    public function show($id)
    {
        $policy = Policy::findOrFail($id);

        return view('policies.show', compact('policy'));
    }

    public function edit($id)
    {
        $policy = Policy::findOrFail($id);

        return view('policies.edit', compact('policy'));
    }
}
