<?php
namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\BadResponseException;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class LoginsController extends Controller {

    public $errorMessage= 'The credentials not found in our database.';

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            return response()->json(['message' => $validator->errors()->all()], 400);
        }

        $credentials = [
            'username' => $request->json()->get('email'),
            'password' => $request->json()->get('password'),
        ];

        if (! $this->checkUser($credentials)) {
            return $this->sendFailedLoginResponse();
        }

        return $this->access('password', $credentials);
    }

    /**
     * Check the given user credentials.
     *
     * @param $credentials
     * @return boolean
     */
    protected function checkUser(&$credentials)
    {
        $user = User::whereEmail($credentials['username'])->first();


        if (! is_null($user) && Hash::check($credentials['password'], $user->password)) {
            //get the role of the user
            $role=$user->getRole();
            $credentials['scope']=$role;
            return true;
        }

        return false;
    }

    /**
     * Get the failed login response instance.
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse()
    {
        return response()->json([
            'message' => $this->errorMessage,
        ], 401);
    }


    /**
     * Send request to the laravel passport.
     *
     * @param  string $grantType
     * @param  array $data
     * @return void
     * @throws \InvalidArgumentException
     */
    private function access($grantType, array $data = [])
    {
        try {

            //Get client secrets
            $config = app()->make('config');
            $secrets = $config->get('oauth.secrets');

            $data = array_merge([
                'client_id' => $secrets['client_id'],
                'client_secret' => $secrets['client_secret'],
                'grant_type' => $grantType,
            ], $data);

             $http = new Client();

             $guzzleResponse = $http->post($config->get('app.url').'/oauth/token', [
                 'form_params' => $data,
             ]);

         } catch(BadResponseException $e) {
             $guzzleResponse = $e->getResponse();
         }

         $response = json_decode($guzzleResponse->getBody());

         if (property_exists($response, 'access_token')) {
             $cookie = app()->make('cookie');

             $cookie->queue('refresh_token',
                 $response->refresh_token,
                 3600, // expiration, should be moved to a config file
                 null,
                 null,
                 false,
                 true // HttpOnly
             );

             $response = [
                 'token_type'    => $response->token_type,
                 'expires_in'    => $response->expires_in,
                 'access_token'   => $response->access_token,
             ];
         }

         $response = response()->json($response);
         $response->setStatusCode($guzzleResponse->getStatusCode());

         $headers = $guzzleResponse->getHeaders();
         foreach($headers as $headerType => $headerValue) {
             $response->header($headerType, $headerValue);
         }

         return $response;
        }

    /**
     * Handle a logout request to the application. needs an authorisation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \RuntimeException
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

       // Auth::guard('api')->logout();
       // $request->session()->flush();
       // $request->session()->regenerate();

        $json = [
            'message' => 'You are Logged out',
        ];
        return response()->json($json, '200');
    }


}
