<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 28-03-2018
 * Time: 13:45
 */

namespace App\Services;
use App\Jobs\LogJob;
use App\Models\Banker;
use App\Models\User;
use function GuzzleHttp\Promise\exception_for;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankersServices
{
    /**
     * Find the banker by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findById($id){
        $banker = Banker::find($id);
        return $banker;
    }

    /**
     * Create a new banker
     * @param $data
     * @param $user_id
     * @param $id_manager
     * @param $photo
     */
    public function create($data,$user_id,$id_manager,$photo){
        $banker = new Banker();
        $banker->name = strip_tags($data['name']);
        $banker->firstname = strip_tags($data['firstname']);
        $banker->address = strip_tags($data['address']);
        $banker->id = $user_id;
        $banker->photo = $photo;
        $banker->id_creator = $id_manager;  // the id of the manager who create the banker account
        $banker->save();
    }

    /**
     * Update the banker avatar
     * @param $id
     * @param $photo
     */
    public function updatePhoto($id,$photo){
        $this->findById($id)->update(['photo'=>$photo]);
    }

    /**
     * Update the banker avatar
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInfo(Request $request){
        $user = $request->user();
        $data = $request->json()->all();
        try {
            DB::beginTransaction();
            if (isset($data['email'])) {
                $user->update(['email' => $data['email']]);
                unset($data['email']);
            }
            if (isset($data['phone_number'])) {
                $user->update(['phone_number' => $data['phone_number']]);
                unset($data['phone_number']);
            }
            $banker = $user->banker();
            foreach ((array)$data as $key => $value) {
                if (null == $value) {
                    unset($data[$key]);
                }
            }
            if(!empty($data)){
                $banker->update($data);
            }
            // log information
            dispatch(new LogJob($user->email,'','Banker had changed his personal
            data',5,LogJob::SUCCESS_STATUS));
            // show the exception message
            DB::commit();
            return response()->json(['message' => 'info changées avec succes'], 200);
        }catch (\Exception $e){
            DB::rollback();
            // log information
            dispatch(new LogJob($user->email,'',$e->getMessage(),5,LogJob::FAILED_STATUS));
            // show the exception message
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param $manager_email
     * @param $id_banker
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function setBankerInvalide($manager_email,$id_banker){
        $banker = Banker::find($id_banker);
        $banker_email = User::find($id_banker)->email;
        if (! $banker){
            return response(json_encode(['message' => 'banquier intouvable']),404);
        }
        try{
            if($banker->is_active){
                // block banker
                $banker->update(['is_active' => false]);
                // log information
                dispatch(new LogJob($manager_email,$banker_email,'Banker blocked',6,
                    LogJob::SUCCESS_STATUS));
            }

            return response(json_encode(['message' => 'Banquier a été bloqué']),200);

        }catch (Exception $exception){
            // log information
            dispatch(new LogJob($manager_email,$banker_email,$exception->getMessage(),6,
                LogJob::FAILED_STATUS));
            return response(json_encode(['message' => $exception->getMessage()]),500);
        }
    }


}