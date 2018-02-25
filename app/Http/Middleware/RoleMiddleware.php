<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class RoleMiddleware used to check the role of the user (client, banker, manager)
 *to allow access to specifics routes for each one
 * @package App\Http\Middleware
 */

class RoleMiddleware
{
    public function __construct()
    {
        //
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $role
     * @return mixed
     */
    public function handle($request, Closure $next,$role = null)
    {
        if(!$request->user()->hasRole($role)){
            return response('Unauthorized.'.$role, 401);
        }
        return $next($request);
    }
}
