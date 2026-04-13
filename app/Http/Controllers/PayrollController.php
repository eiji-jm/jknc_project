<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::query();

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('department') && $request->department !== 'All') {
            $query->where('department', $request->department);
        }

        $payrolls = $query
            ->orderByDesc('pay_date')
            ->orderByDesc('id')
            ->get();

        $departments = Payroll::query()
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        return view('human-capital.payroll', compact('payrolls', 'departments'));
    }

    public function create()
    {
        return redirect()->route('payroll.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_name'   => ['required', 'string', 'max:255'],
            'employee_id'     => ['nullable', 'string', 'max:255'],
            'department'      => ['nullable', 'string', 'max:255'],
            'payroll_period'  => ['required', 'string', 'max:255'],
            'pay_date'        => ['required', 'date'],
            'basic_pay'       => ['required', 'numeric', 'min:0'],
            'allowance'       => ['nullable', 'numeric', 'min:0'],
            'deductions'      => ['nullable', 'numeric', 'min:0'],
            'status'          => ['required', 'string', 'max:50'],
        ]);

        $basicPay = (float) $validated['basic_pay'];
        $allowance = (float) ($validated['allowance'] ?? 0);
        $deductions = (float) ($validated['deductions'] ?? 0);

        Payroll::create([
            'employee_name'  => $validated['employee_name'],
            'employee_id'    => $validated['employee_id'] ?? null,
            'department'     => $validated['department'] ?? null,
            'payroll_period' => $validated['payroll_period'],
            'pay_date'       => $validated['pay_date'],
            'basic_pay'      => $basicPay,
            'allowance'      => $allowance,
            'deductions'     => $deductions,
            'net_pay'        => ($basicPay + $allowance) - $deductions,
            'status'         => $validated['status'],
            'prepared_by'    => Auth::user()->name ?? 'System',
        ]);

        return redirect()
            ->route('payroll.index')
            ->with('success', 'Payroll record created successfully.');
    }

    public function show(Payroll $payroll)
    {
        return response()->json($payroll);
    }

    public function edit(Payroll $payroll)
    {
        return redirect()->route('payroll.index');
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'employee_name'   => ['required', 'string', 'max:255'],
            'employee_id'     => ['nullable', 'string', 'max:255'],
            'department'      => ['nullable', 'string', 'max:255'],
            'payroll_period'  => ['required', 'string', 'max:255'],
            'pay_date'        => ['required', 'date'],
            'basic_pay'       => ['required', 'numeric', 'min:0'],
            'allowance'       => ['nullable', 'numeric', 'min:0'],
            'deductions'      => ['nullable', 'numeric', 'min:0'],
            'status'          => ['required', 'string', 'max:50'],
        ]);

        $basicPay = (float) $validated['basic_pay'];
        $allowance = (float) ($validated['allowance'] ?? 0);
        $deductions = (float) ($validated['deductions'] ?? 0);

        $payroll->update([
            'employee_name'  => $validated['employee_name'],
            'employee_id'    => $validated['employee_id'] ?? null,
            'department'     => $validated['department'] ?? null,
            'payroll_period' => $validated['payroll_period'],
            'pay_date'       => $validated['pay_date'],
            'basic_pay'      => $basicPay,
            'allowance'      => $allowance,
            'deductions'     => $deductions,
            'net_pay'        => ($basicPay + $allowance) - $deductions,
            'status'         => $validated['status'],
        ]);

        return redirect()
            ->route('payroll.index')
            ->with('success', 'Payroll record updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()
            ->route('payroll.index')
            ->with('success', 'Payroll record deleted successfully.');
    }
}