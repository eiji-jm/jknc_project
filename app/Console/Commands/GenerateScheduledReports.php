<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectSowReport;
use Illuminate\Console\Command;

class GenerateScheduledReports extends Command
{
    protected $signature = 'reports:generate-scheduled';
    protected $description = 'Auto-generate SOW and RSAT reports by schedule.';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $generatedCount = 0;

        Project::query()->with(['sows' => fn ($query) => $query->latest(), 'starts' => fn ($query) => $query->latest()])->chunkById(100, function ($projects) use ($today, &$generatedCount): void {
            foreach ($projects as $project) {
                $metadata = (array) ($project->metadata ?? []);
                $generatedCount += $this->handleSowSchedule($project, $metadata, $today);
                $generatedCount += $this->handleRsatSchedule($project, $metadata, $today);
            }
        });

        $this->info("Scheduled report generation complete. Generated {$generatedCount} report(s).");

        return self::SUCCESS;
    }

    private function handleSowSchedule(Project $project, array $metadata, $today): int
    {
        $config = (array) data_get($metadata, 'auto_report.sow', []);
        if (! $this->shouldGenerateToday($config, $today)) {
            return 0;
        }

        $sow = $project->sows->first();
        if (! $sow) {
            return 0;
        }

        $summary = $this->buildStatusSummary($sow->within_scope_items ?? [], $sow->out_of_scope_items ?? []);
        ProjectSowReport::query()->create([
            'project_id' => $project->id,
            'version_number' => $sow->version_number,
            'date_prepared' => $today->toDateString(),
            'within_scope_items' => $sow->within_scope_items ?? [],
            'out_of_scope_items' => $sow->out_of_scope_items ?? [],
            'status_summary' => $summary,
            'project_completion_percentage' => $this->completionPercentage($summary),
            'key_issues' => null,
            'recommendations' => null,
            'way_forward' => null,
            'client_confirmation_name' => $sow->client_confirmation_name ?: $project->client_confirmation_name,
            'internal_approval' => array_merge((array) ($sow->internal_approval ?? []), [
                '__meta' => [
                    'generated' => true,
                    'generated_at' => now()->toDateTimeString(),
                    'source' => 'auto_scheduler_sow',
                ],
            ]),
        ]);

        data_set($metadata, 'auto_report.sow.last_generated_on', $today->toDateString());
        $project->forceFill(['metadata' => $metadata])->save();

        return 1;
    }

    private function handleRsatSchedule(Project $project, array $metadata, $today): int
    {
        $config = (array) data_get($metadata, 'auto_report.rsat', []);
        if (! $this->shouldGenerateToday($config, $today)) {
            return 0;
        }

        $isRegular = str_contains(strtolower((string) $project->engagement_type), 'regular');
        if (! $isRegular) {
            return 0;
        }

        $rsat = $project->starts->first();
        if (! $rsat) {
            return 0;
        }

        $withinRows = collect($rsat->engagement_requirements ?? [])
            ->map(fn (array $row): array => [
                'service' => trim((string) ($row['purpose'] ?? '')),
                'activity_output' => trim((string) ($row['requirement'] ?? '')),
                'frequency' => trim((string) ($row['notes'] ?? '')),
                'reminder_lead_time' => trim((string) ($row['timeline'] ?? '')),
                'deadline' => trim((string) ($row['submitted_to'] ?? '')),
                'status' => trim((string) ($row['status'] ?? 'open')),
            ])
            ->filter(fn (array $row): bool => collect($row)->contains(fn ($value) => $value !== ''))
            ->values()
            ->all();

        ProjectSowReport::query()->create([
            'project_id' => $project->id,
            'report_number' => ProjectSowReport::generateNextRsatCode(),
            'date_prepared' => $today->toDateString(),
            'within_scope_items' => $withinRows,
            'out_of_scope_items' => [],
            'client_confirmation_name' => $project->client_name,
            'internal_approval' => [
                '__meta' => [
                    'generated' => true,
                    'generated_at' => now()->toDateTimeString(),
                    'source' => 'auto_scheduler_rsat',
                ],
            ],
        ]);

        data_set($metadata, 'auto_report.rsat.last_generated_on', $today->toDateString());
        $project->forceFill(['metadata' => $metadata])->save();

        return 1;
    }

    private function shouldGenerateToday(array $config, $today): bool
    {
        if (! (bool) ($config['enabled'] ?? false)) {
            return false;
        }

        $dayOfMonth = (int) ($config['day_of_month'] ?? 0);
        if ($dayOfMonth < 1 || $dayOfMonth > 31) {
            return false;
        }

        $lastGeneratedOn = (string) ($config['last_generated_on'] ?? '');
        if ($lastGeneratedOn === $today->toDateString()) {
            return false;
        }

        $lastDayOfMonth = (int) $today->copy()->endOfMonth()->format('j');
        $runDay = min($dayOfMonth, $lastDayOfMonth);

        return (int) $today->format('j') === $runDay;
    }

    private function buildStatusSummary(array $withinScope, array $outOfScope): array
    {
        $rows = collect(array_merge($withinScope, $outOfScope))
            ->filter(fn ($row) => is_array($row) && trim((string) ($row['main_task_description'] ?? '')) !== '')
            ->values();

        $statusGroups = $rows->groupBy(fn ($row) => (string) ($row['status'] ?? 'open'));

        return [
            'total_main_tasks' => $rows->count(),
            'open' => ($statusGroups['open'] ?? collect())->count(),
            'in_progress' => ($statusGroups['in_progress'] ?? collect())->count(),
            'delayed' => ($statusGroups['delayed'] ?? collect())->count(),
            'completed' => ($statusGroups['completed'] ?? collect())->count(),
            'on_hold' => ($statusGroups['on_hold'] ?? collect())->count(),
        ];
    }

    private function completionPercentage(array $summary): float
    {
        $total = (int) ($summary['total_main_tasks'] ?? 0);
        if ($total <= 0) {
            return 0.0;
        }

        $completed = (int) ($summary['completed'] ?? 0);

        return round(($completed / $total) * 100, 2);
    }
}
