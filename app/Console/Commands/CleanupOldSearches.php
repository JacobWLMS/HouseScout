<?php

namespace App\Console\Commands;

use App\Models\PropertySearch;
use Illuminate\Console\Command;

class CleanupOldSearches extends Command
{
    protected $signature = 'app:cleanup-old-searches';

    protected $description = 'Purge property search records older than the configured retention period';

    public function handle(): int
    {
        $days = config('housescout.search.cleanup_after_days', 90);

        $deleted = PropertySearch::query()
            ->where('searched_at', '<', now()->subDays($days))
            ->delete();

        $this->info("Deleted {$deleted} property search records older than {$days} days.");

        return self::SUCCESS;
    }
}
