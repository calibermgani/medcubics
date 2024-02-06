<?php

namespace App\Http\Controllers\Twilio\api;

use App\Http\Controllers\Controller;
use App\Models\CommunicationInfo;
use Request;
use Twilio\Twiml;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use App\Models\Patients\Patient as Patient;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Traits\ClaimUtil;
use App\Models\Practice;
use App\Models\Insurance;
use App\Models\Employer as Employer;
use App\Models\Facility as Facility;
use View;
use Session;
use DB;
use Config;
use Auth;

/**
|--------------------------------------------------------------------------
| PaymentV1ApiController
| @author Manikandan Duraisamy - CD019
|--------------------------------------------------------------------------
|
 */
class TwilioApi extends Controller
{

    private $accountSid;
    private $authToken;
    private $appSid;
    private $callerId;
    use ClaimUtil;

    public function __construct()
    {
        //madcubics credentials;
        $this->accountSid = 'ACc6875e0f5dd06b6d07438f33eac5c19a';
        $this->authToken = 'e74bbf47b11f5e3bcb88e104c70029bf';
        $this->appSid = 'APad4337e99bc86085e4dc32943224049c';
        $this->callerId = '+19104151899';
        $this->countryCode = Config::get('siteconfigs.twilioActiveCountryCode');
    }

    public function TestCredentials()
    {
        try {
            $client = new Client('AC07cefba93e24c33fb66e059cfbe42691', '5f929a6f678c48392ea3e511266be373');
            $number = $client->incomingPhoneNumbers->create(
                array(
                    "voiceUrl" => "https://demo.twilio.com/docs/voice.xml",
                    "phoneNumber" => "+15005550006"
                )
            );
            $sid = $number->sid;
            $array = array(
                "sid" => $sid,
                "status" => "success",
                "error" => "",
                "message" => "Account Activated",
                'url' => "https://demo.twilio.com");
            return $array;
        } catch (TwilioException $e) {
            $temp[] = array();
            $temp['status'] = "Failed";
            $temp['error'] = $e->getMessage();
            $temp['url'] = "https://demo.twilio.com";
            return $temp;
        }
    }


    public function getTwilioToken($tonum)
    {
        $capability = new ClientToken($this->accountSid, $this->authToken);
        $capability->allowClientOutgoing($this->appSid);
        $token = $capability->generateToken();
        $toNum = '' . $this->countryCode . '' . base64_decode($tonum);
        header('Content-Type: application/json');
        echo json_encode(array(
            'identity' => '',
            'token' => $token,
            'toNum' => $toNum,
        ));
    }

    public function sendSms()
    {
        try {
            $request = Request::all();
            $toNum = '' . $this->countryCode . '' . $request['phone_number'];
            $content = $request['content'];
            $client = new Client($this->accountSid, $this->authToken);
            $dd = $client->messages->create(
                $toNum, array(
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => $this->callerId,
                    // the body of the text message you'd like to send
                    'body' => $content,
                    // 'statusCallback' => "https://requestb.in/1234abcd"
                )
            );
            return "scccess";
        } catch (\Exception $e) {
//            $temp[] = array();
//                $temp['status'] = false;
//                $temp['error'] = $e->getMessage();;
//          
            return false;
        }
    }
    public  function  callHistory($to_number,$firstRecord){
        $client = new Client($this->accountSid, $this->authToken);
        $calls = $client->calls->read(
            array("to" => $to_number)
        );
        $allCalls = [];
        $temp = [];
            foreach ($calls as $call){
                //dd($call);
                $temp['sid'] = $call->sid;
                $temp['startTime'] = $call->startTime->format('Y-m-d H:i:s');
                $temp['dateCreated'] = $call->dateCreated->format('Y-m-d');
                $temp['startTime'] = $call->startTime->format('H:i:s');
                $temp['endTime'] = $call->endTime->format('H:i:s');
                $temp['endDT'] = $call->endTime->format('Y-m-d H:i:s');
                $newStatus = str_replace('-', ' ', $call->status);
                $temp['status'] = ucwords($newStatus);
                $temp['duration'] = $call->duration;
                $temp['direction'] = $call->direction;
                $temp['price'] = $call->price;
                array_push($allCalls, $temp);
                if($firstRecord) {
                    break;
                }
            }
        return $allCalls;
    }

    public function messagesList($to_number)
    {
        $params = array('to' => $to_number);
        $client = new Client($this->accountSid, $this->authToken);
        $result = $client->messages->read($params);
        $allsms = array();
        $temp = [];
        foreach ($result as $message) {
            $temp['sid'] = $message->sid;
            $dcdate = $message->dateCreated->format('Y-m-d');
            $dcdateTime = $message->dateCreated->format('H:i:s');
            $dsdate = $message->dateSent->format('Y-m-d H:i:s');
            $temp['dateCreated'] = $dcdate;
            $temp['dcdateTime'] = $dcdateTime;
            $temp['dateSent'] = $dsdate;
            $temp['status'] = ucwords($message->status);
            $temp['msg'] = $message->body;
            array_push($allsms, $temp);
        }
        return $allsms;
    }




    public function phoneNumLoookup($phoneNum)
    {
        try {
            $client = new Client($this->accountSid, $this->authToken);
            $number = $client->lookups
                ->phoneNumbers($phoneNum)
                ->fetch(
                    array("type" => "carrier")
                );
            if ($number->carrier) {
                $carrier = $number->carrier;
                $carrier['status'] = true;
                return $carrier;
            }
        } catch (\Exception $e) {
            $temp[] = array();
            $temp['status'] = false;
            $temp['error'] = $e->getMessage();
            return $temp;
        }
    }

    // $type = 'patient / provider / employer / facility'
    public function callsList($tonum, $userId, $type='patient')
    {    \Log::info($tonum);
        try {
            $practice_id = Session::get('practice_dbid');
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_id);

            /*
             $practice = Practice::where('id', $id)->first();
            return [@$practice->avatar_name, @$practice->avatar_ext];
            */
            $userId = Helpers::getEncodeAndDecodeOfId($userId, 'decode');
            if ($userId){
                switch($type) {
                    case 'patient':
                        $patient = patient::where('id', $userId)->first();
                       // \Log::info($patient);
                        $filename = @$patient->avatar_name . '.' . @$patient->avatar_ext;
                        $img_details = [];
                        $img_details['module_name'] = 'patient';
                        $img_details['file_name'] = $filename;
                        $img_details['practice_name'] = md5('P' . $practice_id);
                        $img_details['need_url'] = 'yes';
                        $img_details['alt'] = 'patient-image';
                        $image_tag = Helpers::checkAndGetAvatar($img_details);
                        $patientName = Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
                        break;

                    case 'employer':
                        $employer = Employer::where('id',$userId)->first();
                       // \Log::info($employer);
                      //  $filename = @$employer->avatar_name . '.' . @$patient->avatar_ext;
                        $img_details = [];
                        $img_details['module_name'] = 'patient';
                        $img_details['file_name'] = '';
                        $img_details['practice_name'] = md5('P' . $practice_id);
                        $img_details['need_url'] = 'yes';
                        $img_details['alt'] = 'patient-image';
                        $image_tag = Helpers::checkAndGetAvatar($img_details); 
                        $patientName = $employer->employer_name;           
                        break;

                    case 'facility':
                        $facility = Facility::where('id',$userId)->first();
                       // \Log::info($facility);
                      //  $filename = @$employer->avatar_name . '.' . @$patient->avatar_ext;
                        $img_details = [];
                        $img_details['module_name'] = 'patient';
                        $img_details['file_name'] = '';
                        $img_details['practice_name'] = md5('P' . $practice_id);
                        $img_details['need_url'] = 'yes';
                        $img_details['alt'] = 'patient-image';
                        $image_tag = Helpers::checkAndGetAvatar($img_details); 
                        $patientName = $employer->facility_name;          
                        break;

                   case 'insurance':
                        $insurance = Insurance::where('id', $userId)->first(); //\Log::info($insurance);
                        $filename = @$insurance->avatar_name . '.' . @$insurance->avatar_ext;
                        $img_details = [];
                        $img_details['module_name'] = 'practice';
                        $img_details['file_name'] = $filename;
                        $img_details['practice_name'] = md5('P' . $practice_id);
                        $img_details['need_url'] = 'yes';
                        $img_details['alt'] = 'patient-image';
                        $image_tag = Helpers::checkAndGetAvatar($img_details);
                        $patientName = $practice->practice_name;           
                        break;         

                    case 'practice':     
                        $practice = Practice::where('id', $userId)->first(); // \Log::info($practice);
                        $filename = @$practice->avatar_name . '.' . @$practice->avatar_ext;
                        $img_details = [];
                        $img_details['module_name'] = 'practice';
                        $img_details['file_name'] = $filename;
                        $img_details['practice_name'] = md5('P' . $practice_id);
                        $img_details['need_url'] = 'yes';
                        $img_details['alt'] = 'patient-image';
                        $image_tag = Helpers::checkAndGetAvatar($img_details);
                        $patientName = $practice->practice_name;
                        break;
                }
            } else {
                $image_tag = '';
                $patientName = '';
            }
          \Log::info($tonum);
            $toNum = '' . $this->countryCode . '' . base64_decode($tonum);
            $numberCheck = $this->phoneNumLoookup($toNum);
            if ($numberCheck['status']) {  
                $allCalls = $this->callHistory($toNum,false);
                $messageList = $this->messagesList($toNum);
                View::share('phone_number', base64_decode($tonum));
                return view('layouts/twilio', compact('allCalls', 'messageList', 'image_tag', 'patientName'));
            } else {
                return "invalid";
            }
        } catch (\Exception $e) {
            $this->showErrorResponse("callsList", $e);
        }
    }

    public function connectTheCall()
    {
        $_REQUEST = Request::all();
        $response = new Twiml;
        // get the phone number from the page request parameters, if given
        if (isset($_REQUEST['To']) && strlen($_REQUEST['To']) > 0) {
            $number = htmlspecialchars($_REQUEST['To']);
            $dial = $response->dial(array('callerId' => $this->callerId));
            if (preg_match("/^[\d\+\-\(\) ]+$/", $number)) {
                $dial->number($number);
            } else {
                $dial->client($number);
            }
        } else {
            $response->say("Thanks for calling.!");
        }
        header('Content-Type: text/xml');
        echo $response;
    }
    public  function  createCallLogHistory(){
        try {
            $_request = Request::all();
            $userId = Helpers::getEncodeAndDecodeOfId($_request['userId'], 'decode');
            $toNum = $_request['toNum'];
            $practice_id = Session::get('practice_dbid');
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_id);
            $newCommunicationInfo = array(
                "sid"=>@$_request["sid"],
                "com_prvider"=>@$_request["com_prvider"],
                "from"=>$this->callerId,
                "to"=>$toNum,
                "direction"=>@$_request["direction"],
                "patient_id"=>$userId,
                "com_provider"=>'Twilio',
                "claim_id"=>@$_request["claim_id"],
                "com_type"=>@$_request["com_type"],
                "start_time"=>@$_request["start_time"],
                "duration"=>@$_request["duration"],
                "status"=>@$_request["status"],
                "cost"=>@$_request["cost"]
              //"created_by"=>@$created_by
            );
            foreach ($newCommunicationInfo as $key => $value){
                    if(empty($value)){
                        $newCommunicationInfo[$key] = '';
                    }
            }
            $resultSet = CommunicationInfo::updateOrCreate(['id'=>$_request['rowID']],$newCommunicationInfo);
            if(isset($resultSet)){
                return json_encode([
                    "status" => 'success',
                    "li_id" => $resultSet->id
                ]);
            }else{
                return json_encode([
                    "status" => 'error',
                    "li_id" => 0
                ]);
            }
        } catch (\Exception $e) {
            $this->showErrorResponse("createUpdateLogHistory", $e);
        }
    }
    public  function  updateCallLogHistory(){
        try {
            $_request = Request::all();
            $userId = Helpers::getEncodeAndDecodeOfId($_request['userId'], 'decode');
            $toNum = $_request['toNum'];
            $practice_id = Session::get('practice_dbid');
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_id);
            $latestCall = $this->callHistory($toNum,true)[0];
            $newCommunicationInfo = array(
                "sid"=>$latestCall["sid"],
                "from"=>$this->callerId,
                //"to"=>$latestCall['to'],
                "direction"=>$latestCall["direction"],
                "patient_id"=>$userId,
                "start_time"=>$latestCall["startTime"],
                "duration"=>$latestCall["duration"],
                "status"=>$latestCall["status"],
                "cost"=>$latestCall["price"],
                "created_by"=> Auth::user()->id
            );

            $resultSet = CommunicationInfo::updateOrCreate(['id'=>$_request['rowID']],$newCommunicationInfo);

            if(isset($resultSet)){
                return json_encode([
                    "status" => 'success',
                    "li_id" => $resultSet->id
                ]);
            }else{
                return json_encode([
                    "status" => 'error',
                    "li_id" => 0
                ]);
            }
        } catch (\Exception $e) {
            $this->showErrorResponse("createUpdateLogHistory", $e);
            return json_encode([
                "status" => 'error',
                "li_id" => 0
            ]);
        }
    }
}
