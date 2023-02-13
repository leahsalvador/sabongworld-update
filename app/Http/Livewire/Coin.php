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

class Coin extends Component
{
    public function render()
    { $user = Auth::user();
        $game_rounds_current = GameRound::orderBy('created_at','desc')->first();
        $winner = '';

        $time = strtotime(Carbon::now());
        if($game_rounds_current){
                
            // if($game_rounds_current->winner == 'draw' ){
            //     usleep(5 * 1000000);
            //     $this->emit('play-draw', "draw");
            //     $winner = '';
            // }else if ($game_rounds_current->winner == 'heads'){
            //     usleep(5 * 1000000);
            //     $this->emit('play-heads', "heads");
            //    $winner = '';
            // }else if ($game_rounds_current->winner == 'tails'){
            //     usleep(5 * 1000000);
            //     $this->emit('play-tails', "tails");
            //    $winner = '';
            // }else {
            //     $winner = '';
            // }
            $to = date_add($game_rounds_current->created_at,date_interval_create_from_date_string("50 seconds"));
            $secondsLeft = strtotime($to) - $time;
        }else{
            $date = date_create(now());

            date_time_set($date, 8, 00);
            $secondsLeft =  strtotime(date_format($date, 'Y-m-d H:i:s')) - $time;
        }
        $days = floor($secondsLeft / 60*60*24);
        $hours = floor(($secondsLeft - $days*60*60*24) / 60*60);
        $data = [
            'winner' => $winner,
            'count_down' => gmdate("i:s",  $secondsLeft),
            'game_rounds_current' => $game_rounds_current
        ];
        return view('livewire.coin', $data);
    }

}
