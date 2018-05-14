<?php
namespace App\Http\Controllers;


use App\Models\Banker;
use App\Models\User;
use App\Services\BankersServices;
use App\Services\UsersServices;
use Illuminate\Http\Request;


class BankersController extends Controller
{
    private $bankerService;

    public function __construct()
    {
        $this->bankerService = new BankersServices();
    }

    public function index(){

        //get the list of bankers
        $bankers  = Banker::join('users', 'users.id', '=', 'bankers.id')
           ->select('users.id', 'name','firstname','address','email','phone_number','photo')
            ->get();

        //if no banker exists in the database
        if(!$bankers){
            return response()->json(['message' => 'No banker was found'], 404);
        }
        return response()->json($bankers, 200);
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request,$id){
        $user = $request->user();
        $banker = $this->bankerService->findById($id);
        if(!$banker){
            return response()->json(['message' => "The banker with {$id} doesn't exist"], 404);
        }
        $needed_banker = array();
        if ($user->hasRole('banker')){
            $keys = ['id', 'name','firstname','photo'];
            foreach ($keys as $key) {
                $needed_banker[$key] = $banker[$key];
            }
        }elseif ($user->hasRole('manager')){
            $needed_banker = $banker->toArray();
            $user = $banker->user()->getAttributes();
            $keys = ['email','phone_number'];
            foreach ($keys as $key) {
                $needed_banker[$key] = $user[$key];
            }
        }
        $needed_banker['photo'] = FilesController::generateNameImageMinUser($banker['id'],$banker['photo']);
        return response()->json($needed_banker, 200);
    }



    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        $banquier = Bnaquier::find($id);
        if(!$banquier){
            return response()->json(['message' => "The banker with {$id} doesn't exist"], 404);
        }
        $banquier->delete();
        return response()->json(['message' =>"The banker with  id {$id} has been deleted"], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeInfo(Request $request){
        //Validate input
        $rules = [
            'name' => 'alpha',
            'firstname'=> 'alpha',
            'address' => '',
            'email' => 'email | filled',
            'phone_number' => 'numeric | filled | min:12',
        ];
        $data = $request->json()->all();

        if(! $this->validateData($data,$rules)){
            return response()->json(['message' => 'invalid input data'], 400);
        }

        //call for update infos
        return $this->bankerService->updateInfo($request);
    }

    /**
     * @param Request $request
     * @param $id_banker
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function blockBanker(Request $request,$id_banker){
        //call to block banker
        return $this->bankerService->setBankerInvalide($request->user()->email , $id_banker);
    }
}