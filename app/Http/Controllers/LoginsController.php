<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\BadResponseException;
use App\Models\User;

//Added for mail
use App\Mail\AuthConfirmationMail;
use Illuminate\Support\Facades\Mail;

class LoginsController extends Controller {

    public $errorMessage= 'Vos informations non trouvées dans notre base de donnée.';

    //************************ The first step of Authentication ************************//

    /**
     * Handle a first login request to the application to send auth code.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function sendCodeLogin(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
            'channel' => 'required | integer | between:0,1',
        ];

       $data=$request->json()->all();

       if(!$this->validateData($data,$rules)) {
           return response()->json(['message' => 'invalid input data'], 400);
       }

        $channel=$data['channel'];
        unset($data['channel']);
        $user=$this->checkUser($data);

        if (! $user) {
            return $this->sendFailedLoginResponse();
        }

        //Generate the auth number
        $nonce=$this->generateNonce();

        DB::beginTransaction();

        //Set the nonce and the creation time of the nonce
        $user->update(['nonce_auth' => $nonce,
                        'expire_date_nonce' => Carbon::now()->addHours(1)->toDateTimeString()]);

        if (0 == $channel) {
            //Send the auth nonce via email
            $response = $this -> sendAuthentificationCodeMAil($data['email'],$nonce);

        }else {        // $channel == 1
            //Send the auth nonce via SMS
             $response = $this-> sendAuthentificationCodeSMS($user['phone_number'],$nonce);
        }

        if ($response->status() == 500){
            DB::rollback();
        }else{
            DB::commit();
        }
        return $response;
    }

    /**
     * Check the given user credentials.
     *
     * @param $credentials
     * @return boolean
     */
    protected function checkUser($credentials)
    {
        $user = User::whereEmail($credentials['email'])->first();

        if (! is_null($user) && Hash::check($credentials['password'], $user->password)) {
            return $user;
        }
        return false;
    }

    /**
     * This added function for sending auth mail
     *
     * @param $email
     * @param $auth_code
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function sendAuthentificationCodeMAil($email,$auth_code)
    {
       try{
           $response='Consultez vos emails SVP';

           Mail::to($email)
               ->send(new AuthConfirmationMail($email,$auth_code));

              return response()->json(['message' => $response], 200);

       }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * This added function for sending auth SMS message
     *
     * @param $phone_number
     * @param $auth_code
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function sendAuthentificationCodeSMS($phone_number,$auth_code)
    {
        try{
            $response='Consultez vos messages SMS SVP';

            $nexmo = app(\Nexmo\Client::class);

            $nexmo->message()->send([
                'to'   => $phone_number,
                'from' => config('nexmo.tharwa_phone'),
                'text' => 'THARWA code: '.$auth_code.' Valide pour 1 heure'
            ]);

            return response()->json(['message' => $response], 200);

        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * Generate a nonce of 4 digits
     * @return string
     */
    public function generateNonce(){

        $nonce=random_int(0,9999);

        return sprintf('%04u', $nonce);
    }

    //************************ The second step of Authentication ******************//

    /**
     * Handle a second login request to the application to send auth token.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     * @throws \InvalidArgumentException
     */
    public function login(Request $request){
        $rules = [
            'email' => 'required',
            'password' => 'required',
            'nonce' => 'required |alpha_dash| max:4'
        ];

        $data = $request->json()->all();

        if (!$this->validateData($data,$rules)) {
            return response()->json(['message' => 'invalid input data'], 400);
        }

        $user = $this->checkNonceUser($data['email'],$data['password'],$data['nonce']);

        if (!$user){
            return $this->sendFailedLoginResponse();
        }

        return $this->access('password', $user, $data['password']);

        //The user is authenticated
        $reponse = $this->access('password', $data, $role);
    }

    /**
     * Check the given user auth nonce.
     *
     * @param $email
     * @param $password
     * @param $nonce
     * @return boolean
     */
    protected function checkNonceUser($email,$password,$nonce)
    {
        $user = User::whereEmail($email)->first();
        if (!is_null($user)
            && Hash::check($password, $user->password)
            && $user->nonce_auth == $nonce
            && $user->expire_date_nonce > Carbon::now()->toDateTimeString()){
            return $user;

        }
        return false;
    }


    /**
     * Send request to the laravel passport.
     *
     * @param  string $grantType
     * @param $user
     * @param $password
     * @return void
     * @throws \InvalidArgumentException
     */
    private function access($grantType,  $user , $password)
    {
        try {

            //Get client secrets
            $config = app()->make('config');
            $secrets = $config->get('oauth.secrets');

            $credentials['username']=$user['email'];
            $credentials['password']=$password;

            $credentials = array_merge([
                'client_id' => $secrets['client_id'],
                'client_secret' => $secrets['client_secret'],
                'grant_type' => $grantType,
                'scope' => $user->getRole()
            ], $credentials);

            $http = new Client();

            $guzzleResponse = $http->post($config->get('app.url').'/oauth/token', [
                'form_params' => $credentials,
            ]);

        } catch(BadResponseException $e) {
            $guzzleResponse = $e->getResponse();
        }

        $response = json_decode($guzzleResponse->getBody());

        if (property_exists($response, 'access_token')) {
            //TODO to erase if there is no refresh token
//            $cookie = app()->make('cookie');
//
//            $cookie->queue('refresh_token',
//                $response->refresh_token,
//                3600, // expiration, should be moved to a config file
//                null,
//                null,
//                false,
//                true // HttpOnly
//            );

            $response = [
                'user_id' => $user->id,
                'user_type' => $user->role,
                'access_token'   => $response->token_type.' '.$response->access_token,
                'expires_in'    => $response->expires_in,
            ];
        }

        $response = response()->json($response);

        //TODO to erase if there is no problems
        /*$response->setStatusCode($guzzleResponse->getStatusCode());

        $headers = $guzzleResponse->getHeaders();
        foreach($headers as $headerType => $headerValue) {
            $response->header($headerType, $headerValue);
        }*/

        return $response;
    }


    //***************************** General methodes ******************************//
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


    //****************************    Logout    ******************************//
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