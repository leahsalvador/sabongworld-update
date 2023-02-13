<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class IsPlayer
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $list = ['master-agent-player', 'sub-agent-player', 'gold-agent-player', 'silver-agent-player', 'bronze-agent-player'];
        $list = ['master-agent-player', 'sub-agent-player', 'gold-agent-player', 'silver-agent-player'];

        if (Auth::user() && in_array(Auth::user()->user_level, $list)) {
            return $next($request);
        } else {
            abort(403);
        }
    }
}
