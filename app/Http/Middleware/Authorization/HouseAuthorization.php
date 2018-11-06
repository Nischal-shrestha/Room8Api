<?php

namespace App\Http\Middleware\Authorization;

use Closure;
use Illuminate\Support\Facades\Auth;

class HouseAuthorization
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
        if ($request->route()->hasParameter('house')) {
            $house = $request->route()->parameter('house');
            if (in_array($house->toArray(), $user->houses->toArray())) {
                return $next($request); // Authorized
            }
            return abort(403); // Unauthorized

        }
        return $next($request);
    }
}
