<?php

namespace App\Http\Controllers;

use App\Models\SalesMarketingEarner;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class SalesMarketingEarnerController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermission('access_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        $earners = SalesMarketingEarner::latest()->get();

        return view('sales-marketing.earners.index', compact('earners'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('create_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'source_type' => ['required', 'string', 'max:50'],
            'source_id' => ['nullable'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        // 🔥 AUTO-FILL LOGIC
        if ($request->source_type === 'contact' && $request->source_id) {
            $contact = Contact::find($request->source_id);

            if ($contact) {
                $validated['full_name'] = trim(($contact->first_name ?? '') . ' ' . ($contact->last_name ?? ''));
                $validated['email'] = $contact->email ?? null;
                $validated['mobile_number'] = $contact->phone ?? null;
            }
        }

        if ($request->source_type === 'employee' && $request->source_id) {
            $employee = User::find($request->source_id);

            if ($employee) {
                $validated['full_name'] = $employee->name;
                $validated['email'] = $employee->email;
            }
        }

        // 🔒 MANUAL REQUIRED
        if ($request->source_type === 'manual' && empty($validated['full_name'])) {
            return back()->withErrors(['full_name' => 'Full name is required for manual entry']);
        }

        $validated['created_by'] = auth()->id();

        SalesMarketingEarner::create($validated);

        return redirect()
            ->route('sales-marketing.earners.index')
            ->with('success', 'Commission earner added successfully.');
    }

    public function show(SalesMarketingEarner $earner)
    {
        if (!auth()->user()->hasPermission('access_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        return view('sales-marketing.earners.show', compact('earner'));
    }

    public function update(Request $request, SalesMarketingEarner $earner)
    {
        if (!auth()->user()->hasPermission('create_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'source_type' => ['required', 'string', 'max:50'],
            'source_id' => ['nullable'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        // 🔥 AUTO-FILL ON UPDATE ALSO
        if ($request->source_type === 'contact' && $request->source_id) {
            $contact = Contact::find($request->source_id);

            if ($contact) {
                $validated['full_name'] = trim(($contact->first_name ?? '') . ' ' . ($contact->last_name ?? ''));
                $validated['email'] = $contact->email ?? null;
                $validated['mobile_number'] = $contact->phone ?? null;
            }
        }

        if ($request->source_type === 'employee' && $request->source_id) {
            $employee = User::find($request->source_id);

            if ($employee) {
                $validated['full_name'] = $employee->name;
                $validated['email'] = $employee->email;
            }
        }

        $earner->update($validated);

        return redirect()
            ->route('sales-marketing.earners.index')
            ->with('success', 'Commission earner updated successfully.');
    }

    public function destroy(SalesMarketingEarner $earner)
    {
        if (!auth()->user()->hasPermission('create_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        $earner->delete();

        return redirect()
            ->route('sales-marketing.earners.index')
            ->with('success', 'Commission earner deleted successfully.');
    }
}