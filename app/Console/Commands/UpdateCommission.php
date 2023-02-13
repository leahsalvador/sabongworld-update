<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commission:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user commission based on commission table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table("wallets")->update([
            'comission' => 0
        ]);
        $commissions = DB::select('SELECT c.to as user_id, SUM(amount) as amount FROM `commissions` AS c WHERE c.to IN(SELECT user_id FROM wallets) GROUP BY c.to');
        foreach ($commissions as $commission) {
            $updated = DB::table("wallets")->where('user_id', $commission->user_id)->update([
                'comission' => $commission->amount
            ]);
        }
        return 0;
    }
}
