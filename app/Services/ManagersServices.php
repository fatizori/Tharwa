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
use App\Models\Manager;
use App\Models\User;
use function GuzzleHttp\Promise\exception_for;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagersServices
{
    /**
     * Find the banker by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findById($id){
        $banker = Manager::find($id);
        return $banker;
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
            $manager = $user->manager();
            foreach ((array)$data as $key => $value) {
                if (null == $value) {
                    unset($data[$key]);
                }
            }
            if(!empty($data)){
                $manager->update($data);
            }
            // log information
            dispatch(new LogJob($user->email,'','Manager had changed his personal
            data',8,LogJob::SUCCESS_STATUS));
            // show the exception message
            DB::commit();
            return response()->json(['message' => 'info changées avec succes'], 200);
        }catch (\Exception $e){
            DB::rollback();
            // log information
            dispatch(new LogJob($user->email,'',$e->getMessage(),8,LogJob::FAILED_STATUS));
            // show the exception message
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}