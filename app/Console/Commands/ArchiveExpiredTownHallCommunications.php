<?php

namespace App\Console\Commands;

use App\Models\TownHallCommunication;
use Illuminate\Console\Command;

class ArchiveExpiredTownHallCommunications extends Command
{
    protected $signature = 'townhall:archive-expired';
    protected $description = 'Archive expired Town Hall communications automatically';

    public function handle(): int
    {
        $now = now();

        $communications = TownHallCommunication::where('is_archived', false)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->get();

        foreach ($communications as $communication) {
            $communication->update([
                'is_archived' => true,
                'archived_at' => $now,
            ]);
        }

        $this->info('Expired Town Hall communications archived.');

        return self::SUCCESS;
    }
}
