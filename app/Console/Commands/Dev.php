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
        $model = ListItem::query()->where('id', '019c9a5f-2a1b-71aa-bd3e-3802596eb33b')->first();
        print_r($model);
        return 0;
    }
}
