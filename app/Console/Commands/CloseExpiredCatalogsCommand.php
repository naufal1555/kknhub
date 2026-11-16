<?php

namespace App\Console\Commands;

use App\Models\OprecCatalog;
use Illuminate\Console\Command;

class CloseExpiredCatalogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalogs:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menutup secara otomatis seluruh katalog OPREC yang telah melewati batas waktu deadline.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        $affected = OprecCatalog::where('status', 'published')
            ->where('deadline', '<=', $now)
            ->update(['status' => 'closed']);

        $this->info("Berhasil menutup {$affected} katalog OPREC yang telah melewati batas deadline ({$now->toDateTimeString()}).");

        return Command::SUCCESS;
    }
}
