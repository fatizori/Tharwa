<?php namespace App\Http\Middleware;
class CorsMiddleware {
    /**
     * This middleware is used to allow cross origin routes
     *
     * @param $request
     * @param \Closure $next
     * @return mixed
     */

    //add an array of Routes to skip this check (Create because we have Guzzlehttp routes
    //that don't work
    private $openRoutesPrefixes = ['oauth'];

    public function handle($request, \Closure $next)
    {
        $path = 'x'.$request->path();
        foreach ($this->openRoutesPrefixes as $prefix) {
            if (strpos($path , $prefix) == true ) {
                return $next($request);
            }
        }

        $response = $next($request);
        $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers, Accept, Content-Type, Authorization'));
        $response->header('Access-Control-Allow-Origin', '*');

        return $response;

    }
}