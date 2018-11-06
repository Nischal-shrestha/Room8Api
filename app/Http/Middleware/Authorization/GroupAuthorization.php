<?php

namespace App\Http\Middleware\Authorization;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Group;

class GroupAuthorization
{
    /**
     * Handle an incoming request.\
     * Check if the authenticated user is in the group they are trying to access
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($request->route()->hasParameter('group')) {
            $group = $request->route()->parameter('group');
            if (in_array($group->toArray(), $user->groups->toArray())) {
                return $next($request); // Authorized
            }
            return abort(403); // Unauthorized
        }

        //no param
        return $next($request);
    }
}
