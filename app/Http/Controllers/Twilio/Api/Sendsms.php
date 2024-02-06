<?php
namespace App\Http\Controllers\Twilio\api;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonApiController ;
use Carbon\Carbon;

use Twilio\Rest\Client;



class Sendsms extends Controller{
    
    public function  connectSmsCretential(){
        try{
        CommonApiController::setSmsCretential('twilio_sms');
        
        Twilio::sms('+919943865925', 'HI');
       // Twilio::call('+919943865925',"http://demo.twilio.com/docs/voice.xml");
        }catch(Excption $e){
            dd($e);
        }
    }
  public  function checkTwiApi(){
            $to_number = '+919943865925';
            $sid = 'ACad4526896be5e47782b609aa5b62a328';
            $token = 'f74bdece1d33bb505995e6d45276c0eb';
            $client = new Client($sid, $token);
            ////// make a call
          /*  $dd = $client->messages->create(
            '+919943865925',
            array(
                // A Twilio phone number you purchased at twilio.com/console
                'from' => '+14159430994',
                // the body of the text message you'd like to send
                'body' => 'Hey Jenny! Good luck on the bar exam!',
                'statusCallback' => "http://requestb.in/1234abcd"
                )
            );*/
            
         // Loop over the list of messages and echo a property for each one
         /*     $params = array('to' => $to_number);
                $result = $client->messages->read($params);
                $allsms = array();
                $temp = [];
                foreach ($result as $message) {
                  $temp['sid']  = $message->sid ; 
                  $dcdate= $message->dateCreated->format('Y-m-d H:i:s');
                  $dsdate  = $message->dateSent->format('Y-m-d H:i:s');
                  $temp['dateCreated'] =  $dcdate;
                  $temp['dateSent'] = $dsdate;
                  $temp['status'] = $message->status;
                  $temp['msg'] = $message->body;
                  array_push($allsms, $temp);
                }
                dd($allsms);*/
                
        // robo Call 
                /*$call = $client->calls->create(
                    "+919943865925", "+14159430994",
                    array("url" => "http://demo.twilio.com/docs/voice.xml")
                );

                echo $call->sid;
                */
         //call list
                $calls = $client->calls->read(
                array("to" => $to_number)
                );
                $allCalls = [];
                $temp = [];
                foreach ($calls as $call) {
                  $temp['sid'] =  $call->sid;
                  $temp['startTime'] =  $call->startTime->format('Y-m-d H:i:s');
                  $temp['dateCreated'] =  $call->dateCreated->format('Y-m-d H:i:s');
                  $temp['startTime'] = $call->startTime->format('Y-m-d H:i:s');
                  $temp['endTime'] =  $call->endTime->format('Y-m-d H:i:s');
                  $temp['status'] = $call->status;
                  $temp['price'] = $call->price;
                   array_push($allCalls, $temp);
                }
                dd($allCalls);

    }
    
}
    
