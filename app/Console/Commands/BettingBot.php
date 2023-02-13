<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Psy\debug;


class BettingBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'betting:bot {loop=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is auto betting script. This bot script automaticaly bet on running game.';

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
        $current = Carbon::now();
        echo $log = "\r\n......................Cron Start AT {$current}...................................\r\n";
        $counter = intval($this->argument('loop'));
        // Starting clock time in seconds
        $start_time = microtime(true);
        echo $log .= ".............................Start Auto betting on MERON..........................................\r\n";

        $botIds = config('settings.bots.meron') . "," . config('settings.bots.wala');
        echo $log .= "Bot IDS: {$botIds} \r\n";
        if (empty($botIds)) {
            echo $log .= "Bot player did not define yet.";
            return false;
        }
        $botIds = explode(",", $botIds);
        $users = DB::table('users AS u')
            ->join('wallets AS w', 'u.id', '=', 'w.user_id')
            ->select('u.id', 'w.points', 'u.username')
            ->whereIn('u.id', $botIds)
            ->where('u.status', 'activated')
            ->get();

        //echo $log .= "Bot Users: {$users} \r\n";

        $min = intval(config('settings.bots.betting.min'));
        $max = intval(config('settings.bots.betting.max'));
        $side = 'heads';
        for ($i = 0; $i < $counter; $i++) {
            echo $log .= "\r\n .........................................Outer Loop Start {$i}.................................................. \r\n";
            foreach ($users as $user) {
                $game = DB::table('game_rounds')->where('status', 'open')->whereRaw('created_at >= CURRENT_DATE')->orderBy('updated_at', 'DESC')->first();
                if ($game) {
                    echo $log .= "\r\n game#{$game->id} \r\n Round#{$game->round}..........\r\n";

                    echo $log .= "\r\n\r\n....................................Start Betting.........................................\r\n\r\n";
                    switch ($side) {
                        case 'heads':
                            $side = 'tails';
                            break;
                        case 'tails':
                            $side = 'heads';
                            break;
                    }
                    $amount = rand($min, $max);
                    $bet = [
                        "player_id" => $user->id,
                        "game_rounds_id" => $game->id,
                        "side" => $side,
                        "amount" => $amount,
                        "status" => "ongoing",
                        "current_points" => $user->points,
                        'created_at' => Carbon::now()
                    ];
                    echo $log .= "Bot Player: \r\n ID: {$user->id} \r\n Username: {$user->username} \r\n Amount: {$amount} \r\n";
                    $isExist = DB::table('betting_histories')->where([
                        ["game_rounds_id", "=", $game->id],
                        ["player_id", "=", $user->id],
                        ["side", "=", "heads"],
                        ["status", "=", "ongoing"],
                    ])
                        ->select('id', 'player_id', 'amount')
                        ->first();
                    //echo $log .= "Betting Exist: " . json_encode($isExist) . "  \r\n";
                    if ($isExist) {
                        echo $log .= "Betting Exist: Update on existing betting  \r\n";
                        $betting = DB::table('betting_histories')
                            ->where([
                                ['id', '=', $isExist->id]
                            ])
                            ->update([
                                "amount" => $amount + $isExist->amount,
                                "current_points" => $user->points,
                                'updated_at' => Carbon::now()
                            ]);
                    } else {
                        $betting = DB::table('betting_histories')->insert($bet);
                    }
                    //echo $log .= "Betting Data: " . json_encode($bet) . " \r\n Betting: {$betting} \r\n";
                    $updateWallet = DB::table('wallets')->where('user_id', $user->id)->update(["points" => ($user->points - $amount)]);
                    //echo $log .= "Wallets Upadate: {$updateWallet} \r\n";

                    //usleep(1900000);
                } else {
                    echo $log .= "\r\n .........................................No Game for betting.................................................. \r\n";
                    //sleep(2);
                }
            }

            echo $log .= "\r\n .........................................Betting User Loop End.................................................. \r\n";
        }
        // End clock time in seconds
        $end_time = microtime(true);
        // Calculate script execution time
        $execution_time = ($end_time - $start_time);
        echo $log .= "Execution time of script is {$execution_time} sec \r\n";
        echo $log .= "\r\n .............................END Auto betting on MERON..........................................\r\n";
        //Log::info($log);
    }
}
