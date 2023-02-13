<?php

namespace App\Http\Livewire;

use App\Models\GameRound;
use Livewire\Component;

class RoundHistory extends Component
{
    public function render()
    {
        //        DB::enableQueryLog();
        $all_game = GameRound::select('round', 'winner', 'status')
            ->whereNotIn('status', ['open', 'upcoming'])
            ->whereRaw('created_at >= CURRENT_DATE')
            ->orderBy('created_at', 'asc')
            ->get();

        /* echo "<pre>";
         dd(DB::getQueryLog());
         echo "</pre>";*/
        $drawCounts = $all_game->where('winner', 'draw')->count();
        $cancelCounts = $all_game->where('status', 'cancelled')->count();
        $meronCounts = $all_game->where('winner', 'heads')->where('status', '<>', 'cancelled')->count();
        $walaCounts = $all_game->where('winner', 'tails')->where('status', '<>', 'cancelled')->count();
        $data = [
            'logs' => $all_game,
            'walaCounts' => $walaCounts,
            'drawCounts' => $drawCounts,
            'meronCounts' => $meronCounts,
            'cancelCounts' => $cancelCounts,
        ];
        return view('livewire.round-history', $data);
    }
}
