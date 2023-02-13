<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DeclareWinner extends Component
{
    public function render()
    {
        $winner = DB::table('winner_notify')->select('*')->get();
        return view('livewire.declare-winner', ['winner' => $winner]);
    }
}
