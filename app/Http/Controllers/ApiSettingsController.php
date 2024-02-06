<?php namespace App\Http\Controllers;

use App\Http\Requests;
use Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Twilio\Api\TwilioApi;
use App\Speciality as Speciality;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Medcubics\ClearingHouse as ClearingHouse;
use App\Http\Helpers\Helpers;
use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use Session;
use View;
use Config;
use SSH;
class ApiSettingsController extends Api\ApiSettingsApiController
{
	
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'apisettings' );  
		View::share ( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	/*** Listing the code end ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$practiceApiList = $api_response_data->data->practiceApiList;
		$apilist 		 = $api_response_data->data->apilist;
		$maincat_api  = $api_response_data->data->maincat_api;
		$apilist_arr 	 = json_decode(json_encode($apilist), True);
		return view('practice/apisettings/view',  compact('practiceApiList','apilist_arr','maincat_api'));
	}
	/*** Listing the code end ***/
	
	/*** Store the code start ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('apisettings')->with('success', $api_response_data->message);
		}
	}
        public function getApiList()
	{
                $chek = new TwilioApi();
                $twilioCC = $chek->TestCredentials();
                $usps = $this->testUspsCredentials();
                $npi_data =  $this->npiCheck('1295794428');
                $clearing_house = $this->testClearingHouse();
                $ipfinderUsage = $this->ipfinderUsage();
              // dd($ipfinderUsage);
                $api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$practiceApiList = $api_response_data->data->practiceApiList;
		$apilist  = $api_response_data->data->apilist;
		$maincat_api  = $api_response_data->data->maincat_api;
		$apilist_arr 	 = json_decode(json_encode($apilist), True);
                return view('practice/apisettings/apilist',compact('practiceApiList','apilist_arr','maincat_api','twilioCC','usps','npi_data','ipfinderUsage','clearing_house'));
		
	}
        
        public function ipfinderUsage() {
            
            $url = "https://ipfind.co/usage?auth=78af4dfc-7fd6-4064-ae75-981e4f3e7aad";
            
            $responseData = Helpers::GetIpAddress($url);  
              if(!empty($ip_find_api->error)) {
              return array('status'=>'failed','error'=>$ip_find_api->error, 'message'=>$ip_find_api->error,'data'=>$data,"url"=>$url);

            } else{
                if($responseData->remaining <50){
                     return array('status'=>'success','error'=>'','message'=>'please activate the premium account. Hit limet less then 50 ','data'=>$responseData,"url"=>$url);
                }else if($responseData->remaining == 0){
                     return array('status'=>'failed','error'=>'','message'=>'hit limit overdrawn','data'=>$responseData,"url"=>$url);
                }else{
                     return array('status'=>'success','error'=>'', 'message'=>'Account Activated','data'=>$responseData,"url"=>$url);
                }
            }
            
        }


        public function testUspsCredentials(){
            $get_practiceAPI = DBConnectionController::getUserAPIIds('address');
            $address_api = ApiConfig::where('api_for','address')->where('api_status','Active')->first();
                                $address2 =  "123 6th St";
				$city = "Melbourne";
				$state = "FL"; 
				$zip5 = "";
				$zip4 = "";
				$check_either = "city";
				$url = $address_api->url;
				$msg = '<AddressValidateRequest USERID="'.$address_api->usps_user_id.'">';
				$msg .= '<IncludeOptionalElements>true</IncludeOptionalElements>';
				$msg .= '<ReturnCarrierRoute>true</ReturnCarrierRoute>';
				$msg .= '<Address ID="0">';
				$msg .= '<FirmName />';
				$msg .= '<Address1 />';
				$msg .= '<Address2>'.$address2.'</Address2>';

				if($check_either == 'city' || $check_either == 'state')
				{
					$msg .= '<City>'.$city.'</City>';
					$msg .= '<State>'.$state.'</State>';
					$msg .= '<Zip5></Zip5>';
				}

				if($check_either == 'zip5' || $check_either == 'zip4') {
					$msg .= '<City></City>';
					$msg .= '<State></State>';
					$msg .= '<Zip5>'.$zip5.'</Zip5>';
				}
				if($check_either == 'address') {
					$msg .= '<City>'.$city.'</City>';
					$msg .= '<State>'.$state.'</State>';
					$msg .= '<Zip5>'.$zip5.'</Zip5>';
				}

				$msg .= '<Zip4></Zip4>';
				$msg .= '</Address>';
				$msg .= '</AddressValidateRequest>';

				$newurl = $url.urlencode($msg);
                                
				$contents = file_get_contents($newurl);
				$xml = $newurl;
				$parser = xml_parser_create();
				$fp = fopen($xml, 'r');
				$xmldata = fread($fp, 4096);
				xml_parse_into_struct($parser,$xmldata,$values);
				xml_parser_free($parser);
				$xml=simplexml_load_string($xmldata);

				if(!$xml->Address->Error && !$xml->Number)
				{
					$data['address1'] = $xml->Address->Address2;
					$data['address2'] = $xml->Address->Address1;
					$data['city'] = $xml->Address->City;
					$data['state'] = $xml->Address->State;
					$data['zip5'] = $xml->Address->Zip5;
					$data['zip4'] = $xml->Address->Zip4;	
					return array('status'=>'success', 'message'=>'Account Activated','data'=>$data,"url"=>$url);
				} else {
					$message = 'Erro found';
					if($xml->Address->Error)
						$message = $xml->Address->Error->Description;
					else if($xml->Number)
						$message = $xml->Description;

					return array('status'=>'error', 'message'=>$message,'data'=>'',"url"=>$ur);
				}
        }
        
        public function npiCheck($npi)
	{ 
            
		$get_practiceAPI = DBConnectionController::getUserAPIIds('npi');
		$address_api = ApiConfig::where('api_for','npi')->where('api_status','Active')->first();
                $url = '';
		if($address_api && $get_practiceAPI ==1)
		{
			$npi = $npi;
                        $is_provider = "yes";
			$url = $address_api->url.$npi.'&skip=&pretty=on';
			$curl = curl_init();

			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $url,
				CURLOPT_SSL_VERIFYPEER => false
			));

			// Send the request & save response to $resp
			$resp = curl_exec($curl);

			if(!curl_exec($curl)){
				die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
			} 

			// Close request to clear up some resources
			curl_close($curl);

			$result_array = json_decode($resp);
                      // dd($result_array);
			if(isset($result_array->Errors))
			{
				$data['npi_details']['is_valid_npi'] = 'No';
				$data['npi_details']['npi_error_message'] = $result_array->Errors->number;
				return array('status'=>'error', 'message'=>$result_array->Errors->number,'data'=>$data,'url'=>$url);

			} else if($result_array->result_count == 1)
			{
					
				
				$data['npi_details']['is_valid_npi'] = 'Yes';	
                                $data['npi_details']["url"] = $url;
				$data['npi_details']['npi_error_message'] = '';
                                $data['npi_details']['message'] = 'Account Activated';
                                
				if($is_provider == 'yes') 
				{
					$data['provider']['enumeration_type'] = @$result_array->results[0]->enumeration_type;	
				   /* if(@$result_array->results[0]->enumeration_type == 'NPI-2')
						$data['provider']['provider_types_id'] = 5;
					else
						$data['provider']['provider_types_id'] = 5;*/
					$data['provider']['address_1'] = @$result_array->results[0]->addresses[0]->address_1;	
					$data['provider']['address_2'] = @$result_array->results[0]->addresses[0]->address_2;
					$data['provider']['city'] = @$result_array->results[0]->addresses[0]->city;	
					$data['provider']['state'] = @$result_array->results[0]->addresses[0]->state;	
					$data['provider']['zipcode5'] = substr(@$result_array->results[0]->addresses[0]->postal_code,0,5);
									$data['provider']['zipcode4'] = substr(@$result_array->results[0]->addresses[0]->postal_code,5,4);
					$data['provider']['phone'] = '('.preg_replace('/-/', ') ',@$result_array->results[0]->addresses[0]->telephone_number, 1);                                        
					$data['provider']['fax'] = '('.preg_replace('/-/', ') ', @$result_array->results[0]->addresses[0]->fax_number, 1);
									if(@$result_array->results[0]->enumeration_type == 'NPI-2')
										$data['provider']['provider_degrees_id'] = @$result_array->results[0]->basic->authorized_official_credential;
									else
										$data['provider']['provider_degrees_id'] = @$result_array->results[0]->basic->credential;
									
					$data['provider']['first_name'] = @$result_array->results[0]->basic->first_name;	
					$data['provider']['last_name'] = @$result_array->results[0]->basic->last_name;
					$data['provider']['middle_name'] = @$result_array->results[0]->basic->middle_name;
					$data['provider']['organization_name'] = @$result_array->results[0]->basic->organization_name;

					if(@$result_array->results[0]->basic->gender == 'M')
						$data['provider']['gender_m'] = 'Male';
					else
						$data['provider']['gender_m'] = '';
					if(@$result_array->results[0]->basic->gender == 'F')
						$data['provider']['gender_f'] = 'Female';
					else
						$data['provider']['gender_f'] = '';

					if(@$result_array->results[0]->identifiers)
						$data['provider']['medicareptan'] = @$result_array->results[0]->identifiers[0]->identifier;	
					else
						$data['provider']['medicareptan'] = '';
						
                                    }
                                    
			
                                    
			
			
				return array('status'=>'success', 'message'=>'Account Activated','data'=>$data,'url'=>$url);
			} else {
				$data['npi_details']['is_valid_npi'] = 'No';
				$data['npi_details']['npi_error_message'] = 'Not found';
				return array('status'=>'error', 'message'=>'not_found','data'=>$data,'url'=>$url);
			}			
		} else {
			array('status'=>'error', 'message'=>'no_validation','data'=>'','url'=>$url);
		}
	}
        
        
        
            public function testClearingHouse(){
                try{
		$ftp_server = '';
		$clearing_house_details = ClearingHouse::where('status','Active')->where('practice_id',Session::get('practice_dbid'))->first();
                $error_code = '';
		$file_count = 0;
		if(count($clearing_house_details) > 0){
			$ftp_server = $clearing_house_details->ftp_address;
			$ftp_username = $clearing_house_details->ftp_user_id;
                        
			$ftp_password = $clearing_house_details->ftp_password;
			$ftp_port = $clearing_house_details->ftp_port;

			$destination_file = $clearing_house_details->edi_report_folder;

			if (!function_exists("ssh2_connect"))
				$error_code = 'Function ssh2_connect not found, you cannot use ssh2 here';

			else if (!$connection = ssh2_connect($ftp_server, $ftp_port))
				$error_code = 'Connection cannot be made to clearing house. Please contact administrator';

			else if (!ssh2_auth_password($connection, $ftp_username, $ftp_password))
				$error_code = 'Connection cannot be made to clearing house. Please contact administrator';

			else if (!$stream = ssh2_sftp($connection))
				$error_code = 'Connection cannot be made to clearing house. Please contact administrator';

			else if (!$dir = opendir("ssh2.sftp://".intval($stream)."/{$destination_file}/./"))
				$error_code = 'ssh2.sftp://'.$stream.$destination_file.'Could not open the directory';

			//$files = array();
			
//			while (false !== ($file = readdir($dir)))
//			{
//				if ($file == "." || $file == "..")
//					continue;
//					$filename = $file;
//					$local_file = $local_path.$filename;
//					$data_arr = array();
//					$check_count = 0;
//					if(!file_exists($local_path.$filename)){
//						$file_count++;
//						@fopen($local_path . $file, 'w');
//						$file_content  = file_get_contents("ssh2.sftp://".intval($stream)."/{$destination_file}/".$file);
//						$myerafile = fopen($local_path.$file,"w+");
//						fwrite($myerafile,$file_content);
//
//						$file_full_content = explode('~',$file_content);
//						
//						
//						$symb_check = implode('',$file_full_content);
//						$first_segment = $file_full_content[0];
//						if(count(explode('|',$symb_check)) > 1){
//							$separate = "|";
//						}else if(count(explode('*',$symb_check)) > 1){
//							$separate = "*";
//						}
//						
//						foreach($file_full_content as $key=>$segment){
//							if(substr($segment,0,3) == 'ST'.$separate){
//								$check_count++;
//							}
//							
//							if(substr($segment,0,4) == 'TRN'.$separate){
//								$temp = explode($separate,$segment);
//								$data_arr[$check_count]['check_no'] = $temp[2];
//							}
//							if(substr($segment,0,4) == 'BPR'.$separate){
//								$temp = explode($separate,$segment);
//								if(!empty($temp[16]))
//									$data_arr[$check_count]['check_date'] = date('Y-m-d',strtotime($temp[16]));
//								$data_arr[$check_count]['check_paid_amount'] = $temp[2];
//							}
//							if(substr($segment,0,4) == 'CLP'.$separate){
//								$temp = explode($separate,$segment);
//								if(!empty($temp[3]))
//									$data_arr[$check_count]['check_amount'] = $temp[3];
//							}
//							if(substr($segment,0,4) == 'PER'.$separate){
//								$temp = explode($separate,$segment);
//								$insurance_id = Insurance::where('insurance_name',$temp[2])->pluck('id')->first();
//								if($insurance_id == '')
//									$data_arr[$check_count]['insurance_id'] = 1;
//								else
//									$data_arr[$check_count]['insurance_id'] = $insurance_id;
//							}
//							if(substr($segment,0,6) == 'N1'.$separate.'PE'.$separate){
//								$temp = explode($separate,$segment);
//								$data_arr[$check_count]['provider_npi_id'] = $temp[4];
//							}
//							if($check_count != 0){
//								$data_arr[$check_count]['receive_date'] = date("Y-m-d", filemtime("ssh2.sftp://".intval($stream)."/{$destination_file}/".$file));
//								$data_arr[$check_count]['pdf_name'] = $filename;
//							}
//							
//						}
//						foreach($data_arr as $single_cheque_data_arr){
//							$created = Eras::create($single_cheque_data_arr);
//						}
//						
//						
//					}
//				}
				
		}else{
			$error_code = 'You have no clearing house setup. Please contact administrator';
		}
		if($file_count == 0 && $error_code == ''){
			$error_code = 'There are no EOB / ERA 835 downloads available. Do try after sometime';
                }
                if($error_code == ''){
                    $temp[] = array();
                     $temp['status'] = "success";
                     $temp['message'] = "";
                     $temp['error'] = "";
                     $temp['url'] = $ftp_server;
                     return  $temp;
                }else{
                    $temp[] = array();
                     $temp['status'] = "failed";
                     $temp['message'] = "$error_code";
                     $temp['error'] =$error_code;
                     $temp['url'] = $ftp_server;
                     return  $temp;
                }

                }catch(\Exception $e) {
                       return $e;
                }
            }
}