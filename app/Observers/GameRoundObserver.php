<?php

namespace App\Observers;

use App\Models\GameRound;
use App\Events\GameStatusChange;
class GameRoundObserver
{
    /**
     * Handle the GameRound "created" event.
     *
     * @param  \App\Models\GameRound  $gameRound
     * @return void
     */
    public function created(GameRound $gameRound)
    {
        //
        GameStatusChange::dispatch($gameRound);
    }

    /**
     * Handle the GameRound "updated" event.
     *
     * @param  \App\Models\GameRound  $gameRound
     * @return void
     */
    public function updated(GameRound $gameRound)
    {
        //
        GameStatusChange::dispatch($gameRound);
    }

    /**
     * Handle the GameRound "deleted" event.
     *
     * @param  \App\Models\GameRound  $gameRound
     * @return void
     */
    public function deleted(GameRound $gameRound)
    {
        //
    }

    /**
     * Handle the GameRound "restored" event.
     *
     * @param  \App\Models\GameRound  $gameRound
     * @return void
     */
    public function restored(GameRound $gameRound)
    {
        //
    }

    /**
     * Handle the GameRound "force deleted" event.
     *
     * @param  \App\Models\GameRound  $gameRound
     * @return void
     */
    public function forceDeleted(GameRound $gameRound)
    {
        //
    }
}
