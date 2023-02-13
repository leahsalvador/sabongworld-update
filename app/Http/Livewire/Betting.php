<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\BettingHistory;
use App\Models\GameRound;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\SiteSettings;

class Betting extends Component
{

    protected $listeners = ['refresh-bet' => 'refresh'];
    public $game_id;

    public function refresh()
    {
    }

    public function render()
    {

        $user = Auth::user();
        $wallet = $user->wallet;
        $betting_histories = $user->bettingHistory;
        $game_rounds = GameRound::orderBy('round', 'asc')->get();
        $game_rounds_current = GameRound::whereIn('status', ['upcoming', 'open', 'closed', 'cancelled', 'draw', 'undo'])
            ->whereRaw('created_at >= CURRENT_DATE')
            ->orderBy('updated_at', 'DESC')->first();
        $amtHeads = 0;
        $amtTails = 0;
        if ($game_rounds_current) {
            $heads = $user->totalHeadBet($game_rounds_current->id)->sum('amount');
            $tails = $user->totalTailsBet($game_rounds_current->id)->sum('amount');
            $countHeads = BettingHistory::where("game_rounds_id", $game_rounds_current->id)->where("side", "heads")->sum('amount');
            $countTails = BettingHistory::where("game_rounds_id", $game_rounds_current->id)->where("side", "tails")->sum('amount');
            $amtHeads = BettingHistory::where("game_rounds_id", $game_rounds_current->id)->where("side", "heads")->count();
            $amtTails = BettingHistory::where("game_rounds_id", $game_rounds_current->id)->where("side", "tails")->count();
            $time = strtotime(Carbon::now());
            $to = date_add($game_rounds_current->created_at, date_interval_create_from_date_string("50 seconds"));
            $secondsLeft = strtotime($to) - $time;
            if (gmdate("i:s", $secondsLeft) > '00:45' && $game_rounds_current->status == 'open') {
                //sleep(1);
            }
        } else {
            $date = date_create(Carbon::now());
            date_time_set($date, 8, 00);
            $time = strtotime(Carbon::now());
            $secondsLeft = strtotime(date_format($date, 'Y-m-d H:i:s')) - $time;
            $heads = 0;
            $tails = 0;
            $countHeads = 0;
            $countTails = 0;
        }
//         if(@$game_rounds_current->head_payout > 0){
        if ($amtHeads > 0 && $amtTails > 0) {
            if ($countTails > 0) {
                $payout_tails = (($countHeads + $countTails) / $countTails) * config('settings.conversion_rate') * 100;

            } else {
                $payout_tails = 0;
            }
            if ($countHeads > 0) {
                $payout_heads = (($countHeads + $countTails) / $countHeads) * config('settings.conversion_rate') * 100;

            } else {
                $payout_heads = 0;
            }
        } else {
            $payout_heads = 0;
            $payout_tails = 0;
        }
        $days = floor($secondsLeft / 60 * 60 * 24);
        $hours = floor(($secondsLeft - $days * 60 * 60 * 24) / 60 * 60);
        GameRound::whereIn('status', ['open'])->where('id', $this->game_id)->update([
            'head_payout' => $payout_heads,
            'tails_payout' => $payout_tails,
            'total_bet_heads' => $countHeads,
            'total_bet_tails' => $countTails,
        ]);

        $all_game = GameRound::select('round', 'winner')->whereIn('status', ['done', 'cancelled'])->orderBy('id', 'asc')->get();
        $betting_histories_current = ['heads' => $heads, 'tails' => $tails, 'payout_heads' => $payout_heads, 'payout_tails' => $payout_tails, 'countHeads' => $countHeads, 'countTails' => $countTails];

        $image1 = SiteSettings::where('id', 11)->first();

        $data = [
            'player' => $user,
            'wallet' => $wallet,
            'betting_histories' => $betting_histories,
            'game_rounds' => $game_rounds,
            'game_rounds_current' => $game_rounds_current,
            'betting_histories_current' => $betting_histories_current,
            'logs' => $all_game,
            'count_down' => gmdate("i:s", $secondsLeft),
            'image1' => $image1->value,
            'now' => strtotime(Carbon::now()),
            'minimum_bet' => config('settings.minimum_bet'),
        ];

        return view('livewire.betting', $data);
    }
}
