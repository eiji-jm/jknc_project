<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\SalesMarketingEarner;
use App\Models\SalesMarketingIda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesMarketingIdaController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermission('access_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        $idas = SalesMarketingIda::with(['allocations.earner'])
            ->latest()
            ->get();

        $deals = Deal::orderBy('deal_code')->get()->map(function ($deal) {
            $clientName = trim(implode(' ', array_filter([
                $deal->first_name,
                $deal->middle_name,
                $deal->last_name,
            ])));

            return [
                'id' => $deal->id,
                'deal_code' => $deal->deal_code,
                'client_name' => $clientName ?: ($deal->deal_name ?? ''),
                'business_name' => $deal->company_name,
                'service_area' => $deal->service_area,
                'product_engagement_structure' => $deal->engagement_type,
                'deal_value' => $deal->total_estimated_engagement_value ?? 0,
            ];
        });

        $earners = SalesMarketingEarner::orderBy('full_name')->get();

        return view('sales-marketing.ida.index', compact('idas', 'deals', 'earners'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('create_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'deal_id' => ['nullable', 'exists:deals,id'],
            'condeal_ref_no' => ['nullable', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'service_area' => ['nullable', 'string', 'max:255'],
            'product_engagement_structure' => ['nullable', 'string', 'max:255'],
            'deal_value' => ['nullable', 'numeric'],

            'allocations' => ['nullable', 'array'],
            'allocations.*.earner_id' => ['nullable', 'exists:sales_marketing_earners,id'],
            'allocations.*.role' => ['nullable', 'string', 'max:255'],
            'allocations.*.commission_category' => ['nullable', 'string', 'max:255'],
            'allocations.*.commission_type' => ['nullable', 'string', 'max:255'],
            'allocations.*.commission_rate' => ['nullable', 'numeric'],
            'allocations.*.commission_amount' => ['nullable', 'numeric'],
            'allocations.*.status' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $ida = SalesMarketingIda::create([
                'deal_id' => $validated['deal_id'] ?? null,
                'condeal_ref_no' => $validated['condeal_ref_no'] ?? null,
                'client_name' => $validated['client_name'] ?? null,
                'business_name' => $validated['business_name'] ?? null,
                'service_area' => $validated['service_area'] ?? null,
                'product_engagement_structure' => $validated['product_engagement_structure'] ?? null,
                'deal_value' => $validated['deal_value'] ?? 0,
                'workflow_status' => 'Uploaded',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['allocations'] ?? [] as $allocation) {
                if (
                    empty($allocation['earner_id']) &&
                    empty($allocation['role']) &&
                    empty($allocation['commission_category']) &&
                    empty($allocation['commission_type']) &&
                    empty($allocation['commission_rate']) &&
                    empty($allocation['commission_amount'])
                ) {
                    continue;
                }

                $ida->allocations()->create([
                    'earner_id' => $allocation['earner_id'] ?? null,
                    'role' => $allocation['role'] ?? null,
                    'commission_category' => $allocation['commission_category'] ?? null,
                    'commission_type' => $allocation['commission_type'] ?? null,
                    'commission_rate' => $allocation['commission_rate'] ?? 0,
                    'commission_amount' => $allocation['commission_amount'] ?? 0,
                    'status' => $allocation['status'] ?? 'Pending',
                ]);
            }
        });

        return redirect()
            ->route('sales-marketing.ida.index')
            ->with('success', 'IDA record created successfully.');
    }

    public function show(SalesMarketingIda $ida)
    {
        if (!auth()->user()->hasPermission('access_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        $ida->load(['deal', 'allocations.earner']);

        return view('sales-marketing.ida.show', compact('ida'));
    }
}