<?php
namespace App\Services;

use App\Models\User;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;


class PushNotificationService
{
    public function sendPushStream($userId){
        $user = User::find($userId);
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['message' => 'Hello world!']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = $user->fcm_token;
        $downstreamResponse = FCM::sendTo($token, $option, null, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        //return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();

        //return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:errror) - in production you should remove from your database the tokens
    }

    /**
     * @param array $dataToSend
     * @param User $user
     * @return boolean
     * @throws \LaravelFCM\Message\InvalidOptionException
     */
    public function sendDataNotif(array $dataToSend, User $user){
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(
            $dataToSend
        );
        $data = $dataBuilder->build();
        $token = $user->fcm_token;
        // Send
        $downstreamResponse = FCM::sendTo($token, $option, null, $data);
        return $downstreamResponse->numberSuccess();
    }

    public function sendToTopic(){

    }
}