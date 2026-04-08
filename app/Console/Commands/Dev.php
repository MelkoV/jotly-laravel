<?php

namespace App\Console\Commands;

use App\Data\List\ListData;
use App\Models\ListItem;
use App\Models\Lists;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Dev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        DB::select('');
        return 0;
    }
}
