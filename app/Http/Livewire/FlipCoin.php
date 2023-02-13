<?php

namespace App\Http\Livewire;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\BettingHistory;
use App\Models\GameRound;
use Carbon\Carbon;
class FlipCoin extends Component
{
    public function render()
    {   
 
        $user = Auth::user();
        // $wallet = $user->wallet;
        // $betting_histories = $user->bettingHistory;
        // $game_rounds = GameRound::orderBy('created_at','desc')->get();
        $game_rounds_current = GameRound::orderBy('created_at','desc')->first();
 
        if($game_rounds_current){
            $heads = $user->totalHeadBet($game_rounds_current->id)->sum('amount');
            $tails = $user->totalTailsBet($game_rounds_current->id)->sum('amount');
        }else{
            $heads = 0;
            $tails = 0;
        }
        // $all_game = GameRound::select('round','winner')->where('status','!=','open')->whereDate('created_at',now())->orderBy('id','asc')->get();
        $betting_histories_current = ['heads'=>$heads,'tails'=>$tails];


        $time = strtotime(Carbon::now());

        if($game_rounds_current){
            $to = date_add($game_rounds_current->created_at,date_interval_create_from_date_string("50 seconds"));
            $secondsLeft = strtotime($to) - $time;
            if ( in_array($game_rounds_current->status,['final-bet','open']) && $game_rounds_current->winner == 'none' ){
                $this->emit('refresh-bet');
            }
            if (gmdate("i:s",  $secondsLeft) < '00:02') {
                $this->emit('refresh-bet');
                sleep(1.5);
            }
            $this->emit('play-audio', ['winner'=>$game_rounds_current->winner,'coin1'=>$game_rounds_current->coin1,'coin2'=>$game_rounds_current->coin2]);
        }else{
            $date = date_create(Carbon::now());
            date_time_set($date, 8, 01);
            $secondsLeft =  strtotime(date_format($date, 'Y-m-d H:i:s')) - $time;
        }
        $days = floor($secondsLeft / 60*60*24);
        $hours = floor(($secondsLeft - $days*60*60*24) / 60*60);
//         $now = date('F d, Y h:i:s A', $time);
        $time = strtotime(Carbon::now()->setTimezone('Asia/Singapore'));
        $data = [
            'player' => $user,
            // 'wallet' => $wallet,
            // 'betting_histories' => $betting_histories,
            // 'game_rounds' => $game_rounds,
            'game_rounds_current' => $game_rounds_current,
            'betting_histories_current' => $betting_histories_current,
            // 'logs'=>$all_game,
            'count_down' => gmdate("i:s",  $secondsLeft),
            'now' =>$time
        ];

        // if ($game_rounds_current->winner != 'none') {
        // }
        return view('livewire.flip-coin', $data);
    }
}
