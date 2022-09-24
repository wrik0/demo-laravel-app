<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneStaleTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanctum:prune-stale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale tokens';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $stale = DB::table('personal_access_tokens')
            ->select('id')
            ->where('last_used_at', '<', now()->subDays(7))
            ->get();
        $count = $stale->count();
        DB::table('personal_access_tokens')
            ->select('id')
            ->where('last_used_at', '<', now()->subDays(7))
            ->delete();
        $this->info("{$count} token(s) removed.");
        return 0;
    }
}
