<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollBenefit;
use App\Models\PayrollDeduction;
use App\Models\PayrollHoliday;
use App\Models\PayrollLevel;
use App\Models\PayrollPeriod;
use App\Models\PayrollSummary;
use App\Models\PayrollSummaryItem;
use App\Models\PayrollAllowance;
use App\Models\EmployeePayrollProfile;
use App\Models\SalaryGrade;
use App\Services\PayrollCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index()
    {
        return view('human-capital.payroll', [
            'salaryGrades' => SalaryGrade::latest()->get(),
            'payrollLevels' => PayrollLevel::with('salaryGrade')->latest()->get(),
            'benefits' => PayrollBenefit::latest()->get(),
            'allowances' => PayrollAllowance::latest()->get(),
            'deductions' => PayrollDeduction::latest()->get(),
            'holidays' => PayrollHoliday::orderBy('holiday_date')->get(),
            'periods' => PayrollPeriod::latest()->get(),
            'employees' => Employee::orderBy('last_name')->orderBy('first_name')->get(),
            'profiles' => EmployeePayrollProfile::with(['employee', 'payrollLevel.salaryGrade'])->latest()->get(),
            'summaries' => PayrollSummary::with(['employee', 'period', 'payrollLevel.salaryGrade'])->latest()->get(),
        ]);
    }

    public function storeSalaryGrade(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'applicable_daily_rate' => ['required', 'numeric', 'min:0'],
        ]);

        SalaryGrade::create($validated);

        return back()->with('success', 'Salary grade added successfully.');
    }

    public function storePayrollLevel(Request $request)
    {
        $validated = $request->validate([
            'salary_grade_id' => ['required', 'exists:salary_grades,id'],
            'level_name' => ['required', 'string', 'max:100'],
            'computation_type' => ['required', 'in:monthly,daily'],
            'work_schedule' => ['nullable', 'in:every_day,no_sunday,no_sat_sun'],
            'hours_per_day' => ['required', 'numeric', 'min:1'],
        ]);

        PayrollLevel::create($validated);

        return back()->with('success', 'Payroll level added successfully.');
    }

    public function storeBenefit(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        PayrollBenefit::create($validated);

        return back()->with('success', 'Benefit added successfully.');
    }

    public function storeAllowance(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        PayrollAllowance::create($validated);

        return back()->with('success', 'Allowance added successfully.');
    }

    public function storeDeduction(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        PayrollDeduction::create($validated);

        return back()->with('success', 'Deduction added successfully.');
    }

    public function storeHoliday(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'holiday_date' => ['required', 'date'],
            'holiday_type' => ['required', 'in:regular,special'],
            'multiplier' => ['required', 'numeric', 'min:0'],
        ]);

        PayrollHoliday::create($validated);

        return back()->with('success', 'Holiday added successfully.');
    }

    public function storePayrollPeriod(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'payroll_date' => ['required', 'date'],
            'pay_date' => ['required', 'date'],
            'status' => ['required', 'in:draft,open,processed'],
        ]);

        PayrollPeriod::create($validated);

        return back()->with('success', 'Payroll period added successfully.');
    }

    public function storeEmployeeProfile(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'payroll_level_id' => ['required', 'exists:payroll_levels,id'],
            'basic_salary_override' => ['nullable', 'numeric', 'min:0'],
            'night_differential_enabled' => ['nullable', 'boolean'],
        ]);

        $validated['night_differential_enabled'] = $request->boolean('night_differential_enabled');

        EmployeePayrollProfile::updateOrCreate(
            ['employee_id' => $validated['employee_id']],
            $validated
        );

        return back()->with('success', 'Employee payroll profile saved successfully.');
    }

    public function generateSummary(Request $request, PayrollCalculator $calculator)
    {
        $validated = $request->validate([
            'payroll_period_id' => ['required', 'exists:payroll_periods,id'],
        ]);

        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);

        $profiles = EmployeePayrollProfile::with(['employee', 'payrollLevel.salaryGrade'])->get();

        DB::transaction(function () use ($profiles, $period, $calculator) {
            foreach ($profiles as $profile) {
                if (!$profile->employee || !$profile->payrollLevel || !$profile->payrollLevel->salaryGrade) {
                    continue;
                }

                $computed = $calculator->compute($profile, $period);

                $summary = PayrollSummary::updateOrCreate(
                    [
                        'employee_id' => $profile->employee_id,
                        'payroll_period_id' => $period->id,
                    ],
                    [
                        'payroll_level_id' => $profile->payroll_level_id,
                        'computation_type' => $computed['computation_type'],
                        'gross_pay' => $computed['gross_pay'],
                        'total_benefits' => $computed['total_benefits'],
                        'total_allowances' => $computed['total_allowances'],
                        'total_deductions' => $computed['total_deductions'],
                        'night_differential_amount' => $computed['night_differential_amount'],
                        'holiday_pay_amount' => $computed['holiday_pay_amount'],
                        'net_pay' => $computed['net_pay'],
                        'breakdown_json' => $computed['breakdown'],
                        'status' => 'generated',
                    ]
                );

                PayrollSummaryItem::where('payroll_summary_id', $summary->id)->delete();

                foreach ($computed['items'] as $item) {
                    PayrollSummaryItem::create([
                        'payroll_summary_id' => $summary->id,
                        'item_type' => $item['item_type'],
                        'category' => $item['category'],
                        'name' => $item['name'],
                        'amount' => $item['amount'],
                        'meta_json' => $item['meta_json'] ?? null,
                    ]);
                }
            }
        });

        return back()->with('success', 'Payroll summaries generated successfully.');
    }

    public function showPayslip(PayrollSummary $summary)
    {
        $summary->load([
            'employee',
            'period',
            'payrollLevel.salaryGrade',
            'items',
        ]);

        return view('human-capital.payroll-payslip', compact('summary'));
    }
}