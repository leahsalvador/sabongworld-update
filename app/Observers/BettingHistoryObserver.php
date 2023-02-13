<?php

namespace App\Observers;

use App\Models\BettingHistory;
use App\Events\GameStatusChange;
class BettingHistoryObserver
{
    /**
     * Handle the BettingHistory "created" event.
     *
     * @param  \App\Models\BettingHistory  $bettingHistory
     * @return void
     */
    public function created(BettingHistory $bettingHistory)
    {
        GameStatusChange::dispatch($bettingHistory);
        Log::emergency('create trigger');
    }

    /**
     * Handle the BettingHistory "updated" event.
     *
     * @param  \App\Models\BettingHistory  $bettingHistory
     * @return void
     */
    public function updated(BettingHistory $bettingHistory)
    {
        GameStatusChange::dispatch($bettingHistory);
        Log::emergency('update trigger');
    }

    /**
     * Handle the BettingHistory "deleted" event.
     *
     * @param  \App\Models\BettingHistory  $bettingHistory
     * @return void
     */
    public function deleted(BettingHistory $bettingHistory)
    {
        //
    }

    /**
     * Handle the BettingHistory "restored" event.
     *
     * @param  \App\Models\BettingHistory  $bettingHistory
     * @return void
     */
    public function restored(BettingHistory $bettingHistory)
    {
        //
    }

    /**
     * Handle the BettingHistory "force deleted" event.
     *
     * @param  \App\Models\BettingHistory  $bettingHistory
     * @return void
     */
    public function forceDeleted(BettingHistory $bettingHistory)
    {
        //
    }
}
