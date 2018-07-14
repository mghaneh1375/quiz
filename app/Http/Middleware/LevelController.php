<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LevelController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $level
     * @return mixed
     */
    public function handle($request, Closure $next, $level)
    {
        if(Auth::user()->role > $level) {
            return Redirect::route('home')->send();
        }
        return $next($request);
    }
}
