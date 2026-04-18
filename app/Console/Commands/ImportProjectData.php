<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImportProjectData extends Command
{
    protected $signature = 'project-data:import
        {--path=database/data-export : Directory containing exported JSON files}
        {--fresh : Run migrate:fresh before importing data}
        {--seed : Run db:seed after import completes}';

    protected $description = 'Import exported project data into the local database.';

    /**
     * Tables that are environment-specific or should not be restored from exports.
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
        $importPath = base_path($this->option('path'));
        $manifestPath = $importPath.DIRECTORY_SEPARATOR.'manifest.json';

        if (! File::exists($manifestPath)) {
            $this->error(sprintf('Manifest not found at %s. Run project-data:export first or pull the exported files.', $manifestPath));

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--force' => true]);
        } else {
            $this->call('migrate', ['--force' => true]);
        }

        $manifest = json_decode(File::get($manifestPath), true);
        $tables = collect($manifest['tables'] ?? [])
            ->map(function ($entry) {
                $sourceTable = (string) ($entry['table'] ?? '');
                $localTable = $this->normalizeTableName($sourceTable);

                return [
                    'source' => $sourceTable,
                    'local' => $localTable,
                ];
            })
            ->filter(fn (array $entry) => $entry['source'] !== '' && $entry['local'] !== '')
            ->reject(fn (array $entry) => in_array($entry['local'], $this->defaultExcludedTables, true))
            ->filter()
            ->values()
            ->all();

        if ($tables === []) {
            $this->warn('Manifest does not contain any tables to import.');

            return self::SUCCESS;
        }

        Schema::disableForeignKeyConstraints();

        try {
            foreach ($tables as $table) {
                $sourceTable = $table['source'];
                $localTable = $table['local'];

                if (! Schema::hasTable($localTable)) {
                    $this->warn(sprintf('Skipping %s because the local table %s does not exist.', $sourceTable, $localTable));

                    continue;
                }

                $tableFile = $importPath.DIRECTORY_SEPARATOR.$sourceTable.'.json';

                if (! File::exists($tableFile)) {
                    $this->warn(sprintf('Skipping %s because %s was not found.', $sourceTable, $tableFile));

                    continue;
                }

                $rows = json_decode(File::get($tableFile), true) ?? [];

                DB::table($localTable)->truncate();

                foreach (array_chunk($rows, 200) as $chunk) {
                    if ($chunk !== []) {
                        DB::table($localTable)->insert($chunk);
                    }
                }

                $this->line(sprintf('Imported %s into %s (%d row%s)', $sourceTable, $localTable, count($rows), count($rows) === 1 ? '' : 's'));
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        if ($this->option('seed')) {
            $this->call('db:seed', ['--force' => true]);
        }

        $this->info('Project data import completed.');

        return self::SUCCESS;
    }

    protected function normalizeTableName(string $table): string
    {
        $segments = array_values(array_filter(explode('.', $table)));

        return $segments === [] ? '' : (string) end($segments);
    }
}
