<?php


require_once __DIR__.'/../vendor/autoload.php';


try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__) . '/'
);

 $app->withFacades();

 $app->withEloquent();


// Register the facade
$app->withFacades(true, [
    Swap\Laravel\Facades\Swap::class => 'Swap',
    \LaravelFCM\Facades\FCM::class => 'FCM',
    \LaravelFCM\Facades\FCMGroup::class => 'FCMGroup'
]);



/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Cookie\QueueingFactory::class,
    'cookie'
);

// Register all config files
$app->configure('app');
$app->configure('auth');
$app->configure('oauth');
//this configuration for mail
$app->configure('services');
$app->configure('mail');
//this configuration for sms
$app->configure('nexmo');
//database conf
$app->configure('database');
// Load the configuration
$app->configure('swap');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

 $app->middleware([
    //App\Http\Middleware\ExampleMiddleware::class,
    App\Http\Middleware\CorsMiddleware::class,
 ]);

$app->routeMiddleware([
     'auth' => App\Http\Middleware\Authenticate::class,
     'role' => App\Http\Middleware\RoleMiddleware::class,
    'force_ssl' => App\Http\Middleware\ForceSSL::class,
 ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

 $app->register(App\Providers\AppServiceProvider::class);
 $app->register(App\Providers\AuthServiceProvider::class);
 $app->register(App\Providers\EventServiceProvider::class);
 $app->register(App\Providers\CatchAllOptionsRequestsProvider::class);
// register original one and Lumen adapter
  $app->register(Laravel\Passport\PassportServiceProvider::class);
  $app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
// register 'registered routes' service provider
  $app->register(Appzcoder\LumenRoutesList\RoutesCommandServiceProvider::class);
  $app->register(Illuminate\Cookie\CookieServiceProvider::class);
  $app->register(\Illuminate\Mail\MailServiceProvider::class);
// register additional laravel commands Flipbox
  $app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
// register the SMS provider
  $app->register(Nexmo\Laravel\NexmoServiceProvider::class);
// register FCM service
  $app->register(LaravelFCM\FCMServiceProvider::class);



// Register the service provider
$app->register(Swap\Laravel\SwapServiceProvider::class);
Dusterio\LumenPassport\LumenPassport::routes($app);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../routes/web.php';
    require __DIR__.'/../routes/auth.php';
});

return $app;
