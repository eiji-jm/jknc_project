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
            ->pluck('table')
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
                if (! Schema::hasTable($table)) {
                    $this->warn(sprintf('Skipping %s because the table does not exist locally.', $table));

                    continue;
                }

                $rows = json_decode(File::get($importPath.DIRECTORY_SEPARATOR.$table.'.json'), true) ?? [];

                DB::table($table)->truncate();

                foreach (array_chunk($rows, 200) as $chunk) {
                    if ($chunk !== []) {
                        DB::table($table)->insert($chunk);
                    }
                }

                $this->line(sprintf('Imported %s (%d row%s)', $table, count($rows), count($rows) === 1 ? '' : 's'));
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
}
