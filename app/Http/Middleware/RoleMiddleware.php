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
     * @param $role1
     * @param null $role2
     * @return mixed
     */
    public function handle($request, Closure $next,$role1= null ,$role2 = null)
    {
        $user = $request->user();
        $roles = [
                    'role1' => $role1,
                    'role2' => $role2
        ];
        $actualRole = $user->getRole();
        if(! in_array($actualRole,$roles,false)){
            return response('Unauthorized.'.$actualRole, 403);
        }
        //Verify whether the banker is blocked
        if($user->getRole() == 'banker' && !Banker::find($user->id)->is_active){
            return response('Unauthorized.'.$actualRole, 403);
        }
        return $next($request);
    }
}
