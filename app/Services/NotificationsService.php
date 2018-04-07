<?php
namespace App\Services;


use \Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationsService
{
    /**
     * @param Request $request
     * @return array
     */
    public function findNotifNumber(Request $request)
    {
        $user = $request->user();
        $userRole = $user->getRole();
        $response = array();
        switch ($userRole) {
            case 'banker':
                $notifications = DB::table('notifications')
                    ->where([['is_valid', 0], ['id_user', -1]])->get()->all();
                {
                    $response['active'] = $response['unblock'] = $response['justif_v'] = $response['justif_o'] = 0;
                    foreach ($notifications as $notification) {
                        switch ($notification->type) {
                            case 0:
                                {
                                    // New Account Activation Demands
                                    $response['active']++;
                                }
                                break;
                            case 1:
                                {
                                    // Unblock Account Demands
                                    $response['unblock']++;
                                }
                                break;
                            case 2:
                                {
                                    // Accept justif virm Demands
                                    $response['justif_v']++;
                                }
                                break;
                            case 3:
                                {
                                    // Accept justif order-virm Demands
                                    $response['justif_o']++;
                                }
                                break;
                            default;
                        }
                    }
                }
                break;
            case 'customer':
                $notifications = DB::table('notifications')
                    ->where([['is_valid','=', 0], ['id_user','=', $user->id]])->get()->all();
                {
                    $response['accepted'] = $response['blocked'] = $response['unblocked']
                        = $response['virm_local'] = $response['virm_to'] = $response['virm_from']
                        = $response['commission'] = 0;
                    foreach ($notifications as $notification) {
                        switch ($notification->type) {
                            case 4:
                                {
                                    $response['accepted']++;
                                }
                                break;
                            case 5:
                                {
                                    $response['blocked']++;
                                }
                                break;
                            case 6:
                                {
                                    $response['unblocked']++;
                                }
                                break;
                            case 7:
                                {
                                    $response['virm_local']++;
                                }
                                break;
                            case 8:
                                {
                                    $response['virm_to']++;
                                }
                                break;
                            case 9:
                                {
                                    $response['virm_from']++;
                                }
                                break;
                            case 10:
                                {
                                    $response['commission']++;
                                }
                                break;
                            default;
                        }
                        break;
                    }
                }
        }
        return $response;
    }
}