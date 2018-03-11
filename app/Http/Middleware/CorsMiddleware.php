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

        header('Access-Control-Allow-Origin: *');

        // ALLOW OPTIONS METHOD
        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin, Authorization'
        ];
        if($request->isMethod('OPTIONS')) {
            return response('OK', 200, $headers);

        }
            $response = $next($request);
            foreach($headers as $key => $value){
                $response->header($key, $value);
        }
        return $response;
    }
}