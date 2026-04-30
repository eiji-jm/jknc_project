<?php

namespace App\Services;

use App\Models\EmployeePayrollProfile;
use App\Models\PayrollAllowance;
use App\Models\PayrollBenefit;
use App\Models\PayrollDeduction;
use App\Models\PayrollHoliday;
use App\Models\PayrollPeriod;
use Carbon\Carbon;

class PayrollCalculator
{
    public function compute(EmployeePayrollProfile $profile, PayrollPeriod $period): array
    {
        $level = $profile->payrollLevel;
        $grade = $level->salaryGrade;

        $monthlyBasic = round((float) ($profile->basic_salary_override ?: $grade->monthly_basic_pay), 2);
        $dailyRate = round((float) $grade->applicable_daily_rate, 2);
        $hourlyRate = round((float) ($grade->hourly_rate ?: ($dailyRate / max((float) $level->hours_per_day, 1))), 4);

        $holidays = PayrollHoliday::query()
            ->where('salary_grade_id', $grade->id)
            ->where('payroll_level_id', $level->id)
            ->whereBetween('holiday_date', [$period->period_start, $period->period_end])
            ->get();

        $workDays = $this->countWorkDays(
            $period->period_start,
            $period->period_end,
            $level->work_schedule_label ?: $level->work_schedule,
            $holidays->pluck('holiday_date')->all()
        );

        $grossPay = round($dailyRate * $workDays, 2);

        if ($grossPay <= 0 && $level->computation_type === 'monthly') {
            $grossPay = round($monthlyBasic / 2, 2);
        }

        $benefits = PayrollBenefit::query()
            ->where('salary_grade_id', $grade->id)
            ->where('payroll_level_id', $level->id)
            ->where('is_active', true)
            ->get();

        $allowances = PayrollAllowance::query()
            ->where('salary_grade_id', $grade->id)
            ->where('payroll_level_id', $level->id)
            ->where('is_active', true)
            ->get();

        $deductions = PayrollDeduction::query()
            ->where('salary_grade_id', $grade->id)
            ->where('payroll_level_id', $level->id)
            ->where('is_active', true)
            ->get();

        $totalBenefits = round((float) $benefits->sum('value'), 2);
        $totalAllowances = round((float) $allowances->sum('value'), 2);
        $totalDeductions = round((float) $deductions->sum('value'), 2);
        $holidayPayAmount = round((float) $holidays->sum('holiday_value'), 2);
        $nightDifferentialAmount = 0.00;
        $netPay = round($grossPay + $totalBenefits + $totalAllowances + $holidayPayAmount - $totalDeductions, 2);

        $items = [];

        $items[] = [
            'item_type' => 'earning',
            'category' => 'basic_pay',
            'name' => 'Basic Pay',
            'amount' => $grossPay,
            'meta_json' => [
                'computation_type' => $level->computation_type,
                'work_days' => $workDays,
                'daily_rate' => $dailyRate,
                'hourly_rate' => $hourlyRate,
            ],
        ];

        foreach ($benefits as $item) {
            $items[] = $this->buildItem('earning', 'benefit', $item->name, (float) $item->value, $item->type, $item->rate);
        }

        foreach ($allowances as $item) {
            $items[] = $this->buildItem('earning', 'allowance', $item->name, (float) $item->value, $item->type, $item->rate);
        }

        foreach ($holidays as $item) {
            $items[] = [
                'item_type' => 'earning',
                'category' => 'holiday',
                'name' => $item->name,
                'amount' => (float) $item->holiday_value,
                'meta_json' => [
                    'holiday_date' => optional($item->holiday_date)->format('Y-m-d'),
                    'holiday_type' => $item->holiday_category ?: $item->holiday_type,
                    'percentage' => (float) $item->percentage,
                ],
            ];
        }

        foreach ($deductions as $item) {
            $items[] = $this->buildItem('deduction', 'deduction', $item->name, (float) $item->value, $item->type, $item->rate);
        }

        return [
            'computation_type' => $level->computation_type,
            'gross_pay' => $grossPay,
            'total_benefits' => $totalBenefits,
            'total_allowances' => $totalAllowances,
            'total_deductions' => $totalDeductions,
            'night_differential_amount' => $nightDifferentialAmount,
            'holiday_pay_amount' => $holidayPayAmount,
            'net_pay' => $netPay,
            'breakdown' => [
                'monthly_basic_pay' => $monthlyBasic,
                'daily_rate' => $dailyRate,
                'hourly_rate' => $hourlyRate,
                'work_days' => $workDays,
            ],
            'items' => $items,
        ];
    }

    private function buildItem(string $itemType, string $category, string $name, float $amount, string $type, ?float $rate): array
    {
        return [
            'item_type' => $itemType,
            'category' => $category,
            'name' => $name,
            'amount' => round($amount, 2),
            'meta_json' => [
                'type' => $type,
                'rate' => $rate,
            ],
        ];
    }

    private function countWorkDays(Carbon $start, Carbon $end, ?string $schedule, array $holidayDates): int
    {
        $count = 0;
        $cursor = $start->copy()->startOfDay();
        $lastDay = $end->copy()->startOfDay();
        $holidayLookup = collect($holidayDates)
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->flip();

        while ($cursor->lte($lastDay)) {
            $dayOfWeek = $cursor->dayOfWeek;
            $isSaturday = $dayOfWeek === Carbon::SATURDAY;
            $isSunday = $dayOfWeek === Carbon::SUNDAY;
            $isHoliday = $holidayLookup->has($cursor->format('Y-m-d'));

            $skip = match ($schedule) {
                'no_saturday' => $isSaturday,
                'no_sat_sun', 'does_not_work_on_saturday_and_sunday' => $isSaturday || $isSunday,
                'no_sat_sun_holidays' => $isSaturday || $isSunday || $isHoliday,
                'no_sunday' => $isSunday,
                default => false,
            };

            if (! $skip) {
                $count++;
            }

            $cursor->addDay();
        }

        return $count;
    }
}
