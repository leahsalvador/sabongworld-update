<?php

namespace App\Providers;

use App\Models\GameRound;
use App\Observers\GameRoundObserver;
use Illuminate\Support\ServiceProvider;
use App\Events\GameStatusChange;
use App\Events\BettingHistoryChanges;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    protected $listen = [
        'App\Events\BettingHistoryChanges' => [
            'App\Listeners\BettingHistoryChanges',
        ],
    ];
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // GameRound::observe(GameRoundObserver::class);
        if(env('FORCE_HTTPS',false)) { // Default value should be false for local server
            URL::forceScheme('https');
        }
    }
}
