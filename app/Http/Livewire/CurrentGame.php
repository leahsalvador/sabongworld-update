<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\GameRound;
use Carbon\Carbon;

class CurrentGame extends Component
{
    public function render()
    {
//        DB::enableQueryLog();
        $game_rounds = GameRound::whereIn('status', ['open', 'upcoming', 'ongoing', 'final-bet', 'closed', 'done', 'cancelled', 'draw','undo'])
            ->whereRaw('created_at >= CURRENT_DATE')
            ->orderBy('updated_at', 'DESC')->get();
        /*dd(DB::getQueryLog());
        dd($game_rounds);*/
        $data = ['game_rounds' => $game_rounds];
        return view('livewire.current-game', $data);
    }
}
