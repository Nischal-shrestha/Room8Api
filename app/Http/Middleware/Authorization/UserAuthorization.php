<?php

namespace App\Http\Middleware\Authorization;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($request->route()->hasParameter('user')) {
            $reqUser = $request->route()->parameter('user');
            if ($reqUser->id == $user->id) {
                return $next($request); // Authorized
            }
            return abort(403); // Unauthorized

        }

        return $next($request);
    }
}
