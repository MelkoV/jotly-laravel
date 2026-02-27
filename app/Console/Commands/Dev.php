<?php

namespace App\Console\Commands;

use App\Data\List\ListData;
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
        $v = DB::table('list_items')
            ->join('lists', 'lists.id', '=', 'list_items.list_id')
            ->select('lists.type')
            ->where('list_items.id', '019c9e92-96e0-7268-a830-15227cf2269b')
            ->value('type');
        print_r($v);
        return 0;
    }
}
