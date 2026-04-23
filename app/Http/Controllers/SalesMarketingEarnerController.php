<?php

namespace App\Http\Controllers;

use App\Models\SalesMarketingEarner;
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
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
        ]);

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
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
        ]);

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