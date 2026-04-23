<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Division;
use App\Models\Office;
use App\Models\OrganizationalAddress;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganizationalController extends Controller
{
    public function index()
    {
        $addresses = OrganizationalAddress::latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'business_address' => $item->business_address,
            ];
        });

        $offices = Office::latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'office_name' => $item->office_name,
                'office_address' => $item->office_address,
                'office_head' => $item->office_head,
            ];
        });

        $branches = Branch::with('office')->latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'office_id' => $item->office_id,
                'office_name' => $item->office?->office_name,
                'branch_name' => $item->branch_name,
                'branch_address' => $item->branch_address,
                'branch_head' => $item->branch_head,
            ];
        });

        $departments = Department::with(['office', 'branch'])->latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'office_id' => $item->office_id,
                'branch_id' => $item->branch_id,
                'office_name' => $item->office?->office_name,
                'branch_name' => $item->branch?->branch_name,
                'department_name' => $item->department_name,
                'department_address' => $item->department_address,
                'department_head' => $item->department_head,
            ];
        });

        $divisions = Division::with(['office', 'branch', 'department'])->latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'office_id' => $item->office_id,
                'branch_id' => $item->branch_id,
                'department_id' => $item->department_id,
                'office_name' => $item->office?->office_name,
                'branch_name' => $item->branch?->branch_name,
                'department_name' => $item->department?->department_name,
                'division_name' => $item->division_name,
                'division_address' => $item->division_address,
                'division_head' => $item->division_head,
            ];
        });

        $units = Unit::with(['office', 'branch', 'department', 'division'])->latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'office_id' => $item->office_id,
                'branch_id' => $item->branch_id,
                'department_id' => $item->department_id,
                'division_id' => $item->division_id,
                'office_name' => $item->office?->office_name,
                'branch_name' => $item->branch?->branch_name,
                'department_name' => $item->department?->department_name,
                'division_name' => $item->division?->division_name,
                'unit_name' => $item->unit_name,
                'unit_address' => $item->unit_address,
                'unit_head' => $item->unit_head,
            ];
        });

        return view('human-capital.organizational', [
            'addresses' => $addresses,
            'offices' => $offices,
            'branches' => $branches,
            'departments' => $departments,
            'divisions' => $divisions,
            'units' => $units,
            'officeOptions' => Office::orderBy('office_name')->get(['id', 'office_name']),
            'branchOptions' => Branch::orderBy('branch_name')->get(['id', 'branch_name']),
            'departmentOptions' => Department::orderBy('department_name')->get(['id', 'department_name']),
            'divisionOptions' => Division::orderBy('division_name')->get(['id', 'division_name']),
        ]);
    }

    public function store(Request $request)
    {
        $type = $request->input('type');

        if (!in_array($type, ['address', 'office', 'branch', 'department', 'division', 'unit'])) {
            return response()->json([
                'message' => 'Invalid type.',
            ], 422);
        }

        switch ($type) {
            case 'address':
                $validated = $request->validate([
                    'business_address' => ['required', 'string'],
                ]);

                $record = OrganizationalAddress::create($validated);

                return response()->json([
                    'message' => 'Business address saved successfully.',
                    'record' => [
                        'id' => $record->id,
                        'business_address' => $record->business_address,
                    ],
                ]);

            case 'office':
                $validated = $request->validate([
                    'office_name' => ['required', 'string', 'max:255'],
                    'office_address' => ['required', 'string'],
                    'office_head' => ['required', 'string', 'max:255'],
                ]);

                $record = Office::create($validated);

                return response()->json([
                    'message' => 'Office saved successfully.',
                    'record' => [
                        'id' => $record->id,
                        'office_name' => $record->office_name,
                        'office_address' => $record->office_address,
                        'office_head' => $record->office_head,
                    ],
                ]);

            case 'branch':
                $validated = $request->validate([
                    'office_id' => ['required', Rule::exists('offices', 'id')],
                    'branch_name' => ['required', 'string', 'max:255'],
                    'branch_address' => ['required', 'string'],
                    'branch_head' => ['required', 'string', 'max:255'],
                ]);

                $record = Branch::create($validated)->load('office');

                return response()->json([
                    'message' => 'Branch saved successfully.',
                    'record' => [
                        'id' => $record->id,
                        'office_id' => $record->office_id,
                        'office_name' => $record->office?->office_name,
                        'branch_name' => $record->branch_name,
                        'branch_address' => $record->branch_address,
                        'branch_head' => $record->branch_head,
                    ],
                ]);

            case 'department':
                $validated = $request->validate([
                    'office_id' => ['required', Rule::exists('offices', 'id')],
                    'branch_id' => ['required', Rule::exists('branches', 'id')],
                    'department_name' => ['required', 'string', 'max:255'],
                    'department_address' => ['required', 'string'],
                    'department_head' => ['required', 'string', 'max:255'],
                ]);

                $record = Department::create($validated)->load(['office', 'branch']);

                return response()->json([
                    'message' => 'Department saved successfully.',
                    'record' => [
                        'id' => $record->id,
                        'office_id' => $record->office_id,
                        'branch_id' => $record->branch_id,
                        'office_name' => $record->office?->office_name,
                        'branch_name' => $record->branch?->branch_name,
                        'department_name' => $record->department_name,
                        'department_address' => $record->department_address,
                        'department_head' => $record->department_head,
                    ],
                ]);

            case 'division':
                $validated = $request->validate([
                    'office_id' => ['required', Rule::exists('offices', 'id')],
                    'branch_id' => ['required', Rule::exists('branches', 'id')],
                    'department_id' => ['required', Rule::exists('departments', 'id')],
                    'division_name' => ['required', 'string', 'max:255'],
                    'division_address' => ['required', 'string'],
                    'division_head' => ['required', 'string', 'max:255'],
                ]);

                $record = Division::create($validated)->load(['office', 'branch', 'department']);

                return response()->json([
                    'message' => 'Division saved successfully.',
                    'record' => [
                        'id' => $record->id,
                        'office_id' => $record->office_id,
                        'branch_id' => $record->branch_id,
                        'department_id' => $record->department_id,
                        'office_name' => $record->office?->office_name,
                        'branch_name' => $record->branch?->branch_name,
                        'department_name' => $record->department?->department_name,
                        'division_name' => $record->division_name,
                        'division_address' => $record->division_address,
                        'division_head' => $record->division_head,
                    ],
                ]);

            case 'unit':
                $validated = $request->validate([
                    'office_id' => ['required', Rule::exists('offices', 'id')],
                    'branch_id' => ['required', Rule::exists('branches', 'id')],
                    'department_id' => ['required', Rule::exists('departments', 'id')],
                    'division_id' => ['required', Rule::exists('divisions', 'id')],
                    'unit_name' => ['required', 'string', 'max:255'],
                    'unit_address' => ['required', 'string'],
                    'unit_head' => ['required', 'string', 'max:255'],
                ]);

                $record = Unit::create($validated)->load(['office', 'branch', 'department', 'division']);

                return response()->json([
                    'message' => 'Unit saved successfully.',
                    'record' => [
                        'id' => $record->id,
                        'office_id' => $record->office_id,
                        'branch_id' => $record->branch_id,
                        'department_id' => $record->department_id,
                        'division_id' => $record->division_id,
                        'office_name' => $record->office?->office_name,
                        'branch_name' => $record->branch?->branch_name,
                        'department_name' => $record->department?->department_name,
                        'division_name' => $record->division?->division_name,
                        'unit_name' => $record->unit_name,
                        'unit_address' => $record->unit_address,
                        'unit_head' => $record->unit_head,
                    ],
                ]);
        }

        return response()->json([
            'message' => 'Unable to save record.',
        ], 422);
    }
}