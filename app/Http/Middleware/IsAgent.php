<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use Illuminate\Auth\Middleware\Authenticate as Middleware;

class IsAgent
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
        $agentList = ['master-agent', 'sub-agent', 'gold-agent', 'silver-agent', 'bronze-agent'];
        $agentList = ['master-agent', 'sub-agent', 'gold-agent', 'silver-agent'];
        if (Auth::user() && in_array(Auth::user()->user_level, $agentList)) {
            return $next($request);
        } else {
            abort(403);
        }
    }
}
