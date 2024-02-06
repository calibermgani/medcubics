<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Input;
use Auth;
use Response;
use Request;
use Validator;
use Lang;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Models\ClinicalSpecialtiesModel as Clinical;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\IcdCategoryModel as IcdCategory;

class AdvancedSearchIcdCptApiController extends Controller {
	public function getAdvancedSearchApi($request='')
	{
		$get_cpt_API = DBConnectionController::getUserAPIIds('imo_cpt'); 
		$get_icd_API = DBConnectionController::getUserAPIIds('imo_icd'); 
		
		if($request != '' && ($get_cpt_API ==1 or $get_icd_API==1)){
			$request = Request::all();
			/* getting the values from form blade  */
			$keyword = $request['search_keyword'];
			$search_for = "imo_".$request['search_for'];  
			
			/* getting the values in db */
			$advanced_api = ApiConfig::where('api_for',$search_for)->where('api_status','Active')->first(); 

			
			if($advanced_api)
			{
				$result      =   $this->getAdvancedResult($advanced_api,$keyword); /* Get Advanced Response [Line 57] */ 
				if (preg_match("/Request Denied/", $result, $matches)) 
				{
					$error_message = explode(":",$result);
					return Response::json(array('status'=>'error','data'=>$error_message[1]));		
				}
				else
				{
					$array = json_decode(json_encode((array)simplexml_load_string($result)),1); /* XML Res To Array Conversion */
					/* Getting Advanced results */
					if(isset($array['item'])){
						
						/* Single into multi array Conversion */
						if (count($array['item']) ===1) { 
							$array['item'][0][]=$array['item']; 
							$array['item']=$array['item'][0];
						}
						if($search_for == "imo_cpt"){
							$icd_cpt_column = ['CPT / HCPCS','Description','Last Updated'];
						}
						elseif($search_for == "imo_icd"){
							$icd_cpt_column = ['Code','Short Description','Last Updated'];
						}
						$icd_cpt_result = '';
						foreach($array['item'] as $get_val)
						{	
							if(	@$get_val['@attributes']['CLINICAL_SPECIALTIES'] != "" || @$get_val['@attributes']['CATEGORIES'] != ""){
								/// Get clinical speciality description by id ///
								$v_con = explode(',',@$get_val['@attributes']['CLINICAL_SPECIALTIES']);
								$resultc = Clinical::whereIn('specialty_IT_lexical_code',$v_con)->pluck('description')->all();
								$get_val['@attributes']['clinical_spl_content'] = implode(', ',$resultc);
								/// Get clinical speciality categories by text_code ///
								$v_con = explode(',',@$get_val['@attributes']['CATEGORIES']);
								$resultct = IcdCategory::whereIn('text_code',$v_con)->pluck('description','text_code')->all();
								$get_val['@attributes']['categories'] = implode(', ',$resultct);
							}
							$icd_cpt_result[] =$get_val['@attributes'];
						}
						return Response::json(array('status'=>'success', 'message'=>'Results received successfully','data'=>compact('icd_cpt_result', 'icd_cpt_column','search_for')));	
					}
					else
					{	
						return Response::json(array('status'=>'error','data'=>Lang::get("common.validation.empty_record_msg")));					
					}
				}
			}
			else
			{	
				return Response::json(array('status'=>'error','data'=>Lang::get("common.validation.empty_record_msg")));					
			}
		}
		else
		{
			return Response::json(array('status'=>'error','data'=>Lang::get("common.validation.api_alert_msg")));					
		}
	}
	
	/* getImoResult Start */
	public function getAdvancedResult($advanced_api,$keyword){
		$url = $advanced_api->url;
		$user_credential = $advanced_api->usps_user_id;
		$host = $advanced_api->host;
		$port = $advanced_api->port;
		$soap_request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
		<Execute xmlns="http://www.e-imo.com/">
		<Value>search^500|5|1|2^'.$keyword.'^'.$user_credential.'</Value>
		<Host>'.$host.'</Host>
		<Port>'.$port.'</Port>
		</Execute>
		</soap:Body>
		</soap:Envelope>';
		$soap_do = curl_init();
		curl_setopt($soap_do, CURLOPT_URL,$url );
		curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($soap_do, CURLOPT_POST, true );
		curl_setopt($soap_do, CURLOPT_POSTFIELDS,$soap_request);
		curl_setopt($soap_do, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		$result = curl_exec($soap_do); 
		$result = html_entity_decode($result);   
		$result = str_replace('</ExecuteResult></ExecuteResponse></soap:Body></soap:Envelope>','',$result);
		$result = str_replace('<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><ExecuteResponse xmlns="http://www.e-imo.com/"><ExecuteResult>','',$result);
		return $result;
	}
	/* getImoResult End */
	
	
}
