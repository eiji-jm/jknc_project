<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ExportProjectData extends Command
{
    protected $signature = 'project-data:export
        {--path=database/data-export : Directory where JSON exports will be stored}
        {--only=* : Export only these tables}
        {--except=* : Exclude these tables from export}';

    protected $description = 'Export shareable project data into JSON files for teammate imports.';

    /**
     * Tables that are environment-specific or rebuilt elsewhere.
     *
     * @var array<int, string>
     */
    protected array $defaultExcludedTables = [
        'cache',
        'cache_locks',
        'failed_jobs',
        'job_batches',
        'jobs',
        'migrations',
        'password_reset_tokens',
        'sessions',
    ];

    public function handle(): int
    {
        $tables = $this->resolveTables();

        if ($tables === []) {
            $this->warn('No tables matched the export options.');

            return self::SUCCESS;
        }

        $exportPath = base_path($this->option('path'));

        File::ensureDirectoryExists($exportPath);

        $manifest = [
            'exported_at' => now()->toIso8601String(),
            'connection' => config('database.default'),
            'tables' => [],
        ];

        foreach ($tables as $table) {
            $rows = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();

            File::put(
                $exportPath.DIRECTORY_SEPARATOR.$table.'.json',
                json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );

            $manifest['tables'][] = [
                'table' => $table,
                'rows' => count($rows),
            ];

            $this->line(sprintf('Exported %s (%d row%s)', $table, count($rows), count($rows) === 1 ? '' : 's'));
        }

        File::put(
            $exportPath.DIRECTORY_SEPARATOR.'manifest.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->info(sprintf('Project data exported to %s', $exportPath));

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    protected function resolveTables(): array
    {
        $availableTables = collect(Schema::getTableListing())
            ->map(fn ($table) => (string) $table)
            ->sort()
            ->values();

        $only = collect($this->option('only'))
            ->filter()
            ->map(fn ($table) => (string) $table);

        if ($only->isNotEmpty()) {
            return $availableTables
                ->filter(fn ($table) => $only->contains($table))
                ->values()
                ->all();
        }

        $excludedTables = collect($this->defaultExcludedTables)
            ->merge($this->option('except'))
            ->filter()
            ->map(fn ($table) => (string) $table)
            ->unique();

        return $availableTables
            ->reject(fn ($table) => $excludedTables->contains($table))
            ->values()
            ->all();
    }
}
