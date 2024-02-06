<?php

namespace App\Http\Controllers\HL7;

use App\Http\Controllers\Controller;
use Auth;
use App;
use DB;
use View;
use Input;
use Config;
use Session;
use Exception;
use Request;
use Response;
use Redirect;
use Validator;
use Log;

class HL7Controller extends Controller {

   

    public function __construct() {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
		$path_medcubic = public_path() . '/';
        $local_path = $path_medcubic . 'media/hl7/';
		if (!file_exists($local_path)) {
			mkdir($local_path, 0777, true);
		}
		$filename = '';'sample.txt';
		$filepath = $local_path.$filename;
		//$file_content = file($filepath);
		$file_content = "MSH|^~\&|Nexus|NEXUS CLINICAL|RCM|RCM|20170509142406||ADT^A08|20170509142406|P|2.3.1
EVN|A08|20170509142406^S
PID|1||29488|59|Livneh^Uzi^Mid||19751112000000|M||White|123 NoPlace Street^Suite 3^Narnia^^99999||(650)123-4444^Home^^uzi@yopmail.com^^650^1234444^^Home~(650)378-3782^Cell^^uzi@yopmail.com^^650^3783782^^Cell|(650)123-5555^Work^^uzi@yopmail.com^^650^1235555^^Work||L||342343|8987|||Declined To Specifiy||||||||Y
PD1||||1234^Jones^Smith
PV1|1||||||1234^Jones^Smith|10^Provos^Amelia
GT1|1||ABRAHAM^Zeineddar||123 NoPlace Street^Suite 3^Narnia^^99999|(555)555-5555^Home^^^^555^5555555^^Home|||||  
IN1|1|111222333||Operating Engineers|123 NoPlace Street^Suite 3^Narnia^CA^99999||(626)356-1004^Benefits^^^^626^3561004^^Benefits|3433||||20151014000000|20171109000000|||Livneh^Uzi^Mid|Self|19751112000000|123 NoPlace Street^Suite 3^Narnia^^99999||||||||||||||||||||||||M";
		$file_content =  explode('^',$file_content);
		$patientArr = $insuranceArr = []; 
		
		foreach ($file_content as $key => $file_line) {
			echo $file_line ; echo "<br>"; 
			
			if($key == 'PID'){
				$patientTempInfo = explode('|',$file_line);
				
				echo "<pre>";print_r($patientTempInfo);
			}
			if($key == 'PV1'){
				$ProviderTempInfo = explode('|',$file_line);
				
				echo "<pre>";print_r($ProviderTempInfo);
			}
			
			if($key == 'GT1'){
				$guraterTempInfo = explode('|',$file_line);
				echo "<pre>";print_r($guraterTempInfo);
			}
			
			if($key == 'IN1'){
				$insuranceTempInfo = explode('|',$file_line);
				
				echo "<pre>";print_r($insuranceTempInfo);
			}
			
			if($key == 'SCH'){
				$schedulerTempInfo = explode('|',$file_line);
				
				echo "<pre>";print_r($schedulerTempInfo);
			}
			
			if($key == 'AIG'){
				$apptTempInfo = explode('|',$file_line);
				
				echo "<pre>";print_r($apptTempInfo);
			}
			
		}
    }

    

}
