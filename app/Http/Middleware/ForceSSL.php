<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Laravel\Lumen\Http\Redirector;

class ForceSSL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \InvalidArgumentException
     */


    public function handle($request, Closure $next)
    {
        //TODO change from prod to local
        if (!$request->secure())  {
            //$request->setTrustedProxies([ $request->getClientIp() ]);

            return redirect()->to($request->getRequestUri(), 301, $request->header(), true);
        }

        return $next($request);
    }

}
