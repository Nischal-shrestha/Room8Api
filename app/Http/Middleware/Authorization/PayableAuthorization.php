<?php

namespace App\Http\Middleware\Authorization;

use Closure;
use Illuminate\Support\Facades\Auth;

class PayableAuthorization
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
        if ($request->route()->hasParameter('payable')) {
            $payable = $request->route()->parameter('payable');
            if ($payable->payer_id == $user->id || $payable->receiver_id == $user->id) {
                return $next($request); // Authorized
            }
            return abort(403); // Unauthorized

        }
        return $next($request);
    }
}
