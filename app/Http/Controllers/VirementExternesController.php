<?php
/**
 * Created by PhpStorm.
 * User: Mezerreg
 * Date: 14/05/2018
 * Time: 13:48
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\VirementExternesServices;
use App\Services\AccountsServices;
use  App\Jobs\LogJob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VirementExternesController extends Controller
{
    const max_amount_justif = 200000;

    private $virementExterneService;
    private $accountService;

    /**
     * VirementInternesController constructor.
     */
    public function __construct()
    {
        $this->virementExterneService = new VirementExternesServices();
        $this->accountService = new AccountsServices();

    }

    /*===================================================================================================================================*/
    /**
     * Get all the externe transfer
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVirementExternes(){
        $virements = $this->virementExterneService->getVirementExternes();
        return response()->json($virements, 200);

    }

    /*===================================================================================================================================*/

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function externeTransfer(Request $request){
        //Validation of data
        $rules = [

            'num_acc_receiver'=> 'required | integer',
            'code_bnk_receiver'=> 'required ',
            'code_curr_receiver'=> 'required ',
            'name'=> 'required ',
            'amount_virement'=>'required | numeric',
            'justif' => 'image|mimes:jpeg,png,jpg,bmp|max:2048'

        ];
        $data=$request->json()->all();
        $user = $request->user();
        if(emptyArray($data)){
            $data = $request->input();
        }

        $data['justif'] = $request->file('justif');
        $amount = $data['amount_virement'];

        $validator = Validator::make($data, $rules);
        if ($amount < 0 && !$validator->passes()) {
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }
        //amount > 200000
        if ($amount > self::max_amount_justif && is_null($data['justif'])) {
            return   response()->json(['message' => 'Justification required'], 403);
        }


        DB::beginTransaction();
        // Get sender account
        $senderAccount = $this->accountService->findCurrentAccountByUserId($user->id);
        $sender = User::find($senderAccount->id_customer);
        //amount < 200000
        if ($amount <= self::max_amount_justif) {
            try {
                $virement = $this->virementExterneService->createExterneExchange($senderAccount, $data, $amount);

                $new_sender_balance = $senderAccount->balance - $data['amount_virement'];
                if($new_sender_balance < 0){
                    //log
                    dispatch(new LogJob($user->id, $data['name'], 'Virement non effectué (montant insuffisant)', 16,
                        LogJob::FAILED_STATUS));
                    return response(json_encode(['message' => 'montant insuffisant']), 422);
                }

                $this->$this->writeToXml($virement,$sender);


                // Send email to sender
              /*  $this->virementInterneService->sendJustifNotifMAil($user->email,$data['name'],'accepté');*/

            } catch (\Exception $exception) {
                //log
                DB::rollback();
                dispatch(new LogJob($user->id, $data['name'], 'Virement non effectué (erreur serveur)', 16,
                    LogJob::FAILED_STATUS));
                return response(json_encode(['message' => $exception->getMessage()]), 500);

            }
        } elseif ($amount > self::max_amount_justif) {
            try {
                //add exchange

                $virement = $this->virementExterneService->createExterneExchangeJustif($senderAccount, $data, $amount);
                //add justif
                $this->virementExterneService->addJustif($data['justif'],$user->id,$virement->id);
            } catch (\Exception $exception) {
                DB::rollback();
                dispatch(new LogJob($user->id, $data['name'], 'Virement non effectué (erreur serveur)', 16,
                    LogJob::FAILED_STATUS));
                return response(json_encode(['message' => $exception->getMessage()]), 500);
            }
        }
        //log
        dispatch(new LogJob($user->id,$data['name'],'Virement effectué',16,
            LogJob::SUCCESS_STATUS));
        DB::commit();
        return response(json_encode(['message'=>'virement effectué']),201);
    }

    /*===================================================================================================================================*/

    /**
     * @param Request $request
     * @param $id_justif
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateTransfer(Request $request,$id_justif){
        //Validation of data
        $user = $request->user();
        $rules = [
            'operation'=>'required | integer | between:1,2'
        ];
        $data= $request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }
        $operation = $data['operation'];
        $justif = $this->virementExterneService->getJustifById($id_justif);
        if (is_null($justif)){
            dispatch(new LogJob($user->email, null, 'justif inexistant', 18,LogJob::FAILED_STATUS));
            return   response()->json(['message' => 'justif non trouvé'], 404);
        }
        $id_transfert = $justif->id_vrm;
        $transfert = $this->virementExterneService->getTransferById($id_transfert);
        if (is_null($transfert)){
            dispatch(new LogJob($user->email, $id_transfert, 'justif inexistant', 18,LogJob::FAILED_STATUS));
            return   response()->json(['message' => 'virement non trouvé'], 404);
        }
        $banker_id = $request->user()->id;

        $senderAccount = $this->accountService->findSenderCurrentAccountByTransfer($transfert);
        $sender = User::find($senderAccount->id_customer);


        try {
            DB::beginTransaction();
            // Refuse justif
            if($operation == 2 && $justif->status == 0 && $transfert->status == 0){
                $this->virementExterneService->refuseJustif($justif->id,$banker_id);
                $this->virementExterneService->refuseTranfer($transfert);
                dispatch(new LogJob($user->email, $id_transfert, 'virement refusé', 18,LogJob::SUCCESS_STATUS));
                // Send email to sender
               // $this->virementExterneService->sendJustifNotifMAil($sender->email,$receiverAccount->id,'refusé');
                DB::commit();
                return   response()->json(['message' => 'justificatif refusé'], 200);
            }else if ($operation == 1 && $justif->status == 0 && $transfert->status == 0){
                // Accept justif
                $this->virementExterneService->acceptJustif($justif->id,$banker_id);
                $this->virementExterneService->acceptTranfer($transfert);
                $new_sender_balance = $senderAccount->balance - $transfert->amount_vir;
                if($new_sender_balance < 0){
                    //log
                    DB::rollback();
                    dispatch(new LogJob($senderAccount->id_customer,'', 'Virement non effectué (montant insuffisant)', 18,
                        LogJob::FAILED_STATUS));
                    return response(json_encode(['message' => 'montant insuffisant']), 206);
                }



                //write the externe transfer
                $this->writeToXml($transfert,$sender);
                dispatch(new LogJob($user->email, $id_transfert, 'virement validé', 17,LogJob::SUCCESS_STATUS));


                DB::commit();
                return   response()->json(['message' => 'justificatif accepté, virement validé'], 200);
            }else if ( 0 != $transfert->status){
                // Virement is already valide
                // Refuse justif
                $this->virementExterneService->refuseJustif($justif->id,$banker_id);
                dispatch(new LogJob($user->email, $id_transfert, 'virement refusé', 18,LogJob::SUCCESS_STATUS));
                DB::commit();
                return   response()->json(['message' => 'Virement est déja valide'], 422);
            }else if ( 0 != $justif->status){
                // Justif is already valide
                // Refuse justif
                $this->virementExterneService->refuseJustif($justif->id,$banker_id);
                dispatch(new LogJob($user->email, $id_transfert, 'virement refusé', 18,LogJob::SUCCESS_STATUS));
                DB::commit();
                return response()->json(['message' => 'Justif est déja valide'], 422);
            }
        }catch (\Exception $exception) {
            DB::rollback();
            dispatch(new LogJob($user->email, $id_transfert, 'virement non traité (erreur serveur)', 17,LogJob::FAILED_STATUS));
            return response()->json(['message' => $exception->getMessage()], 500);
        }

    }

    /*===================================================================================================================================*/
    public function writeToXml($transfert,$sender){

        $doc = new \DOMDocument();
        $doc->formatOutput = true;

            $virement = $doc->createElement( "virement" );
            $doc->appendChild( $virement );


            $numero = $doc->createElement( "numero" );
            $transfer_date = $transfert->created_at;
            $date = explode('-',$transfer_date);
            $y = $date[0];
            $m = $date[1];
            $d = substr($date[2],0,2);
            $h = substr($date[2],3,2);
            $mi = substr($date[2],6,2);
            $num = $transfert->code_bnk.$transfert->num_acc.$transfert->code_curr.$transfert->code_bnk_ext.$transfert->num_acc_ext.$transfert->code_curr
                   .$y.$m.$d.$h.$mi;
            $numero->appendChild(
                $doc->createTextNode($num )
            );

            $date = $doc->createElement( "date" );
            $date->appendChild(
                $doc->createTextNode( $y.$m.$d.$h.$mi )
            );
            $virement->appendChild( $numero );
            $virement->appendChild( $date );

            //the sender
            $titulaire = $doc->createElement( "titulaire" );

            $nomSender = $doc->createElement( "nom" );
            $nomSender->appendChild(
                $doc->createTextNode( $sender->name )
            );

            $banqueSender = $doc->createElement( "banque" );
            $banqueSender->appendChild(
                $doc->createTextNode( $transfert->code_bnk )
            );

            $compteSender = $doc->createElement( "compte" );
            $compteSender->appendChild(
                $doc->createTextNode( $transfert->code_bnk.$transfert->num_acc.$transfert->code_curr )
            );

             $titulaire->appendChild( $nomSender );
             $titulaire->appendChild( $banqueSender );
             $titulaire->appendChild( $compteSender );

             $virement->appendChild( $titulaire );

             //the receiver

            $destinataire = $doc->createElement( "destinataire" );

            $nomReceiver = $doc->createElement( "nom" );
            $nomReceiver->appendChild(
                $doc->createTextNode( $transfert->name_ext )
            );

            $banqueReceiver = $doc->createElement( "banque" );
           $banqueReceiver->appendChild(
                $doc->createTextNode( $transfert->code_bnk_ext )
            );

            $compteReceiver = $doc->createElement( "compte" );
           $compteReceiver->appendChild(
                $doc->createTextNode( $transfert->code_bnk_ext.$transfert->num_acc_ext.$transfert->code_curr )
            );

            $destinataire->appendChild( $nomReceiver );
            $destinataire->appendChild( $banqueReceiver );
            $destinataire->appendChild( $compteReceiver );

            $virement->appendChild( $destinataire );

        $montant = $doc->createElement( "montant" );
        $montant->appendChild(
            $doc->createTextNode( $transfert->amount_vir )
        );

        $motif = $doc->createElement( "motif" );
        $motif->appendChild(
            $doc->createTextNode( "motif" )
        );
        $virement->appendChild( $montant );
        $virement->appendChild( $motif );
            $doc->save("./XML_file/".$transfert->code_bnk.$transfert->num_acc."Vers".$transfert->code_bnk_ext.$transfert->num_acc_ext.".xml");
        }


    /*===================================================================================================================================*/

    public function executeTransfer(){


    }

}