<?php
namespace App\Http\Controllers;


use App\Services\LoginServices;
use Illuminate\Http\Request;




class LoginsController extends Controller {

    private $loginServices ;

    /**
     * LoginsController constructor.
     */
    public function __construct()
    {
        $this->loginServices = new LoginServices();
    }


    //************************ The first step of Authentication ************************//

    /**
     * Handle a first login request to the application to send auth code.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function askCodeLogin(Request $request)
    {
        $rules = [
            'email' => 'required | email',
            'password' => 'required',
            'channel' => 'required | integer | between:0,1',
        ];

        //TODO  THIS just for tests
        return response()->json(['message' => 'Consultez votre email'], 200);
//      $data=$request->json()->all();
//
//       if(!$this->validateData($data,$rules)) {
//           return response()->json(['message' => 'invalid input data'], 400);
//       }
//
//       return $this->loginServices->sendCodeLogin($data);

    }

    /**
     * Handle a second login request to the application to send auth token.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     * @throws \InvalidArgumentException
     */
    public function login(Request $request){
        $rules = [
            'email' => 'required | email',
            'password' => 'required',
            'nonce' => 'required |alpha_dash| max:4'
        ];

        $data = $request->json()->all();

        if (!$this->validateData($data,$rules)) {
            return response()->json(['message' => 'invalid input data'], 400);
        }

        return $this->loginServices->checkCodeLogin($data);
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
        return $this->loginServices->revokeToken($request);
    }
}