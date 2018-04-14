<?php

namespace App\Http\Middleware;

use App\Models\Banker;
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
        $user = $request->user();
        if(! $user->hasRole($role)){
            return response('Unauthorized.'.$role, 403);
        }
        //Verify whether the banker is blocked
        if($user->getRole() == 'banker' && !Banker::find($user->id)->is_active){
            return response('Unauthorized.'.$role, 403);
        }
        return $next($request);
    }
}
