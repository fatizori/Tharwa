<?php
namespace App\Http\Controllers;


use App\Services\NotificationsService;
use Illuminate\Http\Request;

class NotificationsController extends  Controller
{
    private $notificationsService;

    public function __construct()
    {
        $this->notificationsService = new NotificationsService();
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getNotifNumber(Request $request){
        return json_encode($this->notificationsService->findNotifNumber($request));
    }
}