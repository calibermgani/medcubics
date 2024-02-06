<?php namespace App\Http\Controllers\Patients\Api;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Patients\PatientInsurance;
use App\Models\Patients\PatientEligibility;
use App\Models\EdiEligibility as EdiEligibility;
use App\Models\PatientEligibilityWaystar as PatientEligibilityWaystar;
use App\Models\EdiEligibilityDemo as EdiEligibilityDemo;
use App\Models\EdiEligibilityInsurance as EdiEligibilityInsurance;
use App\Models\EdiEligibilityContact_detail as EdiEligibilityContact_detail;
use App\Models\EdiEligibilityInsuranceSpPhysician as EdiEligibilityInsuranceSpPhysician;
use App\Models\EdiEligibilityMedicare as EdiEligibilityMedicare;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Patients\Patient;

use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Provider;
use App\Models\Insurance;
use Response;
use App\Models\Template;
use Request;
use Validator;
use Config;
use Auth;
use App;
use PDF;
use Session;
use Lang;
use DB;
class PatientEligibilityApiController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getindexApi($patient_id = '',$export='')
	{
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id)) 
		{
			$patient_insurances = Insurance::has('patient_insurance')
									->whereHas('patient_insurance', function($q) use($patient_id){ 
										$q->where('patient_id',$patient_id); 
									})->pluck('insurance_name','id')->all();

			$benefit_verification = [];
			/*$benefit_verification = PatientEligibility::with('provider','provider.degrees','provider.provider_types','facility','facility.facility_address','facility.pos_details','facility.speciality_details','template','insurance_details','user')->where('patients_id', $patient_id)->where('is_edi_atatched',"!=", 1)->get();
			$eligibility = PatientEligibility::with('insurance_details','user','get_eligibilityinfo')->where('patients_id', $patient_id)->where('is_edi_atatched', 1)->get();*/
			if(Session::get('practice_dbid') != 40)
				$eligibility = PatientEligibility::with('provider','provider.degrees','provider.provider_types','facility','facility.facility_address','facility.pos_details','facility.speciality_details','template','insurance_details','user')->where('patients_id', $patient_id)->get();
			else
				$eligibility = PatientEligibilityWaystar::with('insurance_details')->where('patient_id', $patient_id)->get();
			//dd($eligibility);
			return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_insurances','eligibility','benefit_verification')));
		}
		else 
		{
			return Response::json(array('status' => 'failure', 'message' =>Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}
	
	public static function get_policy_id($insuranceid,$patientid)
	{
		return $patient_insurances = PatientInsurance::with('insurance_details')->where('patient_id', $patientid)->where('insurance_id', $insuranceid)->orderBy('id', 'DESC')->first()->policy_id;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreateApi($patient_id, $template_id)
	{
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode'); 
		$template_id = Helpers::getEncodeAndDecodeOfId($template_id,'decode'); 
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id)) 
		{
			$patient_insurances = PatientInsurance::with('insurance_details')->where('patient_id', $patient_id)->get();
			$insurance_details = array();
			if(!empty($patient_insurances)) 
			{
				foreach($patient_insurances as $patient_insurance) 
				{
					$insurance_details[$patient_insurance->insurance_details->id] = $patient_insurance->insurance_details->insurance_name;			
				}
			}
			$bvtemplateid = Config::get('siteconfigs.templatetype.benefit_verifications'); 
			$templates = Template::where('id',$template_id)->first();
			// /$templates = array_flip($templates);
			return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance_details', 'templates', 'tab_insurance', 'patient_detail')));
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' =>Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function getstoreApi($id,$request = '')
	{
		$patient_id = Helpers::getEncodeAndDecodeOfId($id,'decode'); 
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id)) 
		{
			$request = Request::all();
			$request['dos_from']= date("Y-m-d",strtotime($request['dos_from']));	
			$request['dos_to']= date("Y-m-d",strtotime($request['dos_to']));	
			$validator = Validator::make($request, PatientEligibility::$rules, PatientEligibility::messages());
			if ($validator->fails())
			{  
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{ 		
				$request['template_id'] = Helpers::getEncodeAndDecodeOfId($request['template_id'],'decode'); 
				$eligibility = PatientEligibility::create($request);
				if($eligibility->id) {
					$pdf = App::make('dompdf.wrapper');
					$pdf->loadHTML($request['content']);
					$default_view = Config::get('siteconfigs.production.defult_production');
					if(App::environment() == $default_view)
						$path = $_SERVER['DOCUMENT_ROOT'].'/medcubic/';
					else
						$path = public_path().'/';		 
					if (!file_exists($path.'/media/patienteligibility/'.$eligibility->patients_id)) {
						mkdir($path.'/media/patienteligibility/'.$eligibility->patients_id, 0777, true);
					}
					$path = $path.'/media/patienteligibility/'.$eligibility->patients_id.'/benifitverification_'.$request['dos_from'].'_'.$eligibility->id.'.pdf';
					$path_pdf = 'media/patienteligibility/'.$eligibility->patients_id.'/benifitverification_'.$request['dos_from'].'_'.$eligibility->id.'.pdf';
					$filename_pdf = 'benifitverification_'.$request['dos_from'].'_'.$eligibility->id.'.pdf';
					$eligibility->bv_filename = $filename_pdf;				
					$eligibility->bv_file_path = $path_pdf;
					$pdf->save($path);
				}
				$user = Auth::user ()->id;
				$eligibility->created_by = $user;
				$eligibility->is_manual_atatched = '1';
				$eligibility->save ();
				$eligibility_id = Helpers::getEncodeAndDecodeOfId($eligibility->id,'encode'); 
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$eligibility_id));	
							
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' =>Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getshowApi($patient_id,$eligibility_id)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($eligibility_id,'decode');
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode'); 
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id)) 
		{	
			if (PatientEligibility::where('id', $id)->count() && is_numeric($id)) 
			{
				$eligibility  = PatientEligibility::with('provider','provider.degrees','provider.provider_types','facility','facility.facility_address','facility.pos_details','facility.speciality_details','template','insurance_details','user','userupdate')->where('id', $id)->first();
				$eligibility->id = Helpers::getEncodeAndDecodeOfId($id,'encode');
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('eligibility')));	
			} 
			else 
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' =>Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function geteditApi($patient_id,$elibility_id)
	{		
		$id = Helpers::getEncodeAndDecodeOfId($elibility_id,'decode');
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode'); 
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id)) 
		{	
			if (PatientEligibility::where('id', $id)->count() && is_numeric($id)) 
			{
				$eligibility = PatientEligibility::findOrFail($id);
				$bvtemplateid = Config::get('siteconfigs.templatetype.benefit_verifications'); 
				$templates = Template::where('template_type_id',$bvtemplateid)->pluck('id', 'name')->all();
				$templates = array_flip($templates);
				$patient_insurances = PatientInsurance::with('insurance_details')->where('patient_id', $patient_id)->get();
				$insurance_details = array();
				if(!empty($patient_insurances)) 
				{
					foreach($patient_insurances as $patient_insurance) 
					{
						$insurance_details[$patient_insurance->insurance_details->id] = $patient_insurance->insurance_details->insurance_name;			
					}
				}
				$eligibility->id = Helpers::getEncodeAndDecodeOfId($id,'encode');
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('eligibility', 'insurance_details', 'templates')));
			} 
			else 
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getupdateApi($patientid,$elibility_id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($elibility_id,'decode');
		$patientid = Helpers::getEncodeAndDecodeOfId($patientid,'decode');
		
		if(Patient::where('id', $patientid)->count()>0 && is_numeric($patientid)) 
		{	
			if (PatientEligibility::where('id', $id)->count() && is_numeric($id)) 
			{
				$request = Request::all();
				$request['dos_from']= date("Y-m-d",strtotime($request['dos_from'])); 
				$request['dos_to']= date("Y-m-d",strtotime($request['dos_to'])); 
				$validator = Validator::make($request, PatientEligibility::$rules, PatientEligibility::messages());
				if ($validator->fails())
				{  
					$errors = $validator->errors();
					return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
				}
				else
				{
					$eligibility = PatientEligibility::findOrFail($id);
					$request['template_id'] = Helpers::getEncodeAndDecodeOfId($request['template_id'],'decode'); 
					$default_view = Config::get('siteconfigs.production.defult_production');
					if(App::environment() == $default_view)
						$path_medcubic = $_SERVER['DOCUMENT_ROOT'].'/medcubic/';
					else
						$path_medcubic = public_path().'/';
					
					if($eligibility->id) 
					{
						$path = $path_medcubic.'/media/patienteligibility/'.$patientid.'/'.$eligibility->bv_filename;
						$request['bv_filename'] = $eligibility->bv_filename;				
						$request['bv_file_path'] = $path;
						if (file_exists($path)) 
						{
							unlink($path);					
						}		
					}
					$request['is_manual_atatched'] = '1';
					if($eligibility->update($request)) {
						$pdf = App::make('dompdf.wrapper');
						$pdf->loadHTML($request['content']);
						$pdf->save($path);
					}
					$patient_id = $eligibility->patients_id;
					$eligibility->updated_by = Auth::user()->id;
					$eligibility->save();
					
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>compact('patient_id')));
				}
			} 
			else 
			{
				return Response::json(array('status'=>'failure_nodetail', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDeleteApi($patient_id, $eligibility_id)
	{
		$patient_id  = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		$eligibility_id  = Helpers::getEncodeAndDecodeOfId($eligibility_id,'decode');
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id)) 
		{	
			
			if ( is_numeric($eligibility_id)&& PatientEligibility::where('id', $eligibility_id)->count()) 
			{
				$default_view = Config::get('siteconfigs.production.defult_production');
				if(App::environment() == $default_view)
					$path_medcubic = $_SERVER['DOCUMENT_ROOT'].'/medcubic/';
				else
					$path_medcubic = public_path().'/';
				$eligibility = PatientEligibility::findOrFail($eligibility_id);
				if($eligibility->id) 
				{
					$path = $path_medcubic.'/media/patienteligibility/'.$patient_id.'/'.$eligibility->bv_filename;
					$request['bv_filename'] = $eligibility->bv_filename;				
					$request['bv_file_path'] = $path;
					if (file_exists($path)) 
					{
						unlink($path);					
					}		
				}
				if(PatientEligibility::where('id',$eligibility_id)->delete())
				{
					  return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
				} 
				else 
				{
					  return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.error_msg"),'data'=>''));
				}
			} 
			else 
			{
				return Response::json(array('status'=>'failure_nodetail', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}	
	}
	public function gettemplates($id)
	{
		 $id  = Helpers::getEncodeAndDecodeOfId($id,'decode');
		 $templates = Template::where('id',$id)->first();
		 $content = $templates->content;
		 return $content;
	}
	public function getshowpdfApi($type,$id)
	{
		$value = PatientEligibility::find($id);
		$pdf = App::make('dompdf.wrapper');
		$default_view = Config::get('siteconfigs.production.defult_production');
		if(App::environment() == $default_view)
	    	$path_medcubic = $_SERVER['DOCUMENT_ROOT'].'/medcubic/';
        else
	    	$path_medcubic = public_path().'/';
		
	  if($type == '1')		
		$path = $path_medcubic.$value->edi_file_path;	
	  elseif ($type == '2')
		 $path = $path_medcubic.$value->bv_file_path;	
		
	    $pdf->loadHTML($value['content']);
		return $pdf->stream();	
	}
	

	/**** Start to check insurance eligiblity ***/
	public function checkPatientEligibility(Request $request)
	{  
		//$request = Request::all();
		//print_r($request); exit;
		$eligibility_api = ApiConfig::where('api_for','insurance_eligibility')->where('api_status','Active')->first();
		$get_practiceAPI = DBConnectionController::getUserAPIIds('insurance_eligibility');
		$status = '';
		$tempid	= '';
		$error = '';
		//echo "<pre>";print_r($get_practiceAPI);die;
		// removed form if condition && $get_practiceAPI ==1
		
		if($eligibility_api)
		{	
			if($eligibility_api->api_name == 'eligible' || $eligibility_api->api_name == 'Pverify' || $eligibility_api->api_name == 'waystar')
			{ 
				$elibility_arr = [];
				$elibility_arr['date'] = $request::input('scheduled_on');				
				if($request::input('scheduled_on')=='')
					$elibility_arr['date'] = date('Y-m-d');
					
				$patient_id = $request::input('patient_id');
				if(!is_numeric($patient_id)==1)
				{
					$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
				}
				
				$page_type					= $request::input('type');
				$elibility_arr['provider_id'] = $request::input('provider_id');
				$elibility_arr['category'] = $request::input('category');
				$elibility_arr['dos_from'] = $request::input('dos_from');
				$elibility_arr['dos_to'] = $request::input('dos_to');
				//$elibility_arr['provider_id'] = 6;
				$elibility_arr['patient_id'] = $patient_id;
				
				if($patient_id != '' && $patient_id != 'new')
				{
					// Get patient details
					$patient_details = Patient::where('id',$patient_id)->first();
					if($patient_details)
					{
						$elibility_arr['member_last_name'] = $patient_details->last_name;
						$elibility_arr['member_first_name'] = $patient_details->first_name;
						$elibility_arr['dob'] = $patient_details->dob;

						// Get Patient insurance details based on category
						if($request::input('category')!='')
						{
							$patient_insurance_details = PatientInsurance::with('insurance_details','insurance_details.insurancetype')->where('patient_id',$patient_id)->where('category',$request::input('category'))->first();
						}
						else
						{
							// Get Patient insurance details from insurance id and policy id.
							$patient_insurance_details = '';
							if($request::input('primary_insurance_id')!='' && $request::input('primary_insurance_policy_id')!='')
							{
								$patient_insurance_details = PatientInsurance::with('insurance_details','insurance_details.insurancetype')->where('patient_id',$patient_id)->where('insurance_id',$request::input('primary_insurance_id'))->where('policy_id',$request::input('primary_insurance_policy_id'))->first();	
							}
							else
							{
								$status = 'error';
								$error = Lang::get("common.validation.ins_avail_msg");
							}
						}
						
						if($patient_insurance_details)
						{
							$elibility_arr['insurance_id'] = $patient_insurance_details->insurance_id;
							$elibility_arr['member_id'] = $patient_insurance_details->policy_id;
							$elibility_arr['payer_id'] = isset($patient_insurance_details->insurance_details->payerid)? $patient_insurance_details->insurance_details->payerid : '';
							$elibility_arr['eligibility_payerid'] = isset($patient_insurance_details->insurance_details->eligibility_payerid)? $patient_insurance_details->insurance_details->eligibility_payerid : '';
							
							if(isset($patient_insurance_details->insurance_details->insurancetype->type_name)){
								if($eligibility_api->api_name == 'Pverify')
									$elibility_arr['medicare'] = "checkEligibility";
								else if($eligibility_api->api_name == 'waystar')
									$elibility_arr['medicare'] = "checkEligibility";
								else
									$elibility_arr['medicare'] = ($patient_insurance_details->insurance_details->insurancetype->type_name =="medicare") ? "checkMedicareEligibility":"checkEligibility";
							}else{
								$elibility_arr['medicare'] = "checkEligibility";
							}
						}
						elseif($page_type == 'pat_ins')
						{
							// Insurance add page.
							if($request::input('primary_insurance_id')!='' && $request::input('primary_insurance_policy_id')!='')
							{
								$elibility_arr['insurance_id'] = $request::input('primary_insurance_id');
								$elibility_arr['member_id'] = $request::input('primary_insurance_policy_id');
								$elibility_arr['payer_id'] = Insurance::getInsurancePayerId($request::input('primary_insurance_id'))->pluck('payerid')->first();
								$elibility_arr['eligibility_payerid'] = Insurance::getInsurancePayerId($request::input('primary_insurance_id'))->pluck('eligibility_payerid')->first();
								
								if($request::input('insurancetype')!=''){
									if($eligibility_api->api_name == 'Pverify')
										$elibility_arr['medicare'] = "checkEligibility";
									else
										$elibility_arr['medicare'] = ($request::input('insurancetype') =="medicare") ? "checkMedicareEligibility":"checkEligibility";	
								}else{
									$elibility_arr['medicare'] = "checkEligibility";
								}
							}
							else
							{
								$status = 'error';
								$error = Lang::get("common.validation.ins_avail_msg");
							}	
						}
						else
						{
							// Check the patient self pay or insurance.
							$status = 'error';
							$check_patientins = Patient::select('is_self_pay')->where('id',$patient_id)->first()->is_self_pay;
							if($check_patientins == 'Yes')
								$error = Lang::get("common.validation.patientins_selfpay_msg");
							else
								$error = Lang::get("common.validation.ins_avail_msg");
						}
					}
					else
					{
						$status = 'error';
						$error = Lang::get("common.validation.pat_avail_msg");
					}
				}
				else
				{
					$insurance_id = $request::input('primary_insurance_id');
					$insurance = Insurance::with('insurancetype')->where('id',$insurance_id)->first();
					if(isset($insurance->insurancetype->type_name)){
						if($eligibility_api->api_name == 'Pverify')
							$elibility_arr['medicare'] = "checkEligibility";
						else
							$elibility_arr['medicare'] = ($insurance->insurancetype->type_name =="medicare") ? "checkMedicareEligibility":"checkEligibility";	
					}else{
						$elibility_arr['medicare'] = "checkEligibility";
					}
					
					$elibility_arr['member_last_name'] = $request::input('patient_last_name');
					$elibility_arr['member_first_name']= $request::input('patient_first_name');
					$elibility_arr['dob']= $request::input('patient_dob');
					
					$elibility_arr['payer_id'] = Insurance::getInsurancePayerId($request::input('primary_insurance_id'))->pluck('payerid')->first();
					$elibility_arr['member_id'] = $request::input('primary_insurance_policy_id');
					$elibility_arr['insurance_id'] = $insurance_id;
				}
				
				if($error == '' ) 
				{
					$commonApiController = new CommonApiController();
					$fnName = $elibility_arr['medicare'];
					$eligibility_api_result = $commonApiController->$fnName($elibility_arr);
					$status = $eligibility_api_result['status'];
					$error	= $eligibility_api_result['error'];
					$tempid	= $eligibility_api_result['temp_id'];
				}
			}
			else
			{
				$status = 'error';
				$error =Lang::get("common.validation.api_credit_msg");
			}
		}
		else
		{
			$status = 'error';
			$error =Lang::get("common.validation.api_avail_msg");;
		}
				
		$result= json_encode(array("status"=>$status,"error"=>$error,"tempid"=>$tempid));
		return $result;
	}
	/**** End to check insurance eligiblity ***/
	
	/**** Start to get the eligiblity details ***/
	
	public function getPatientEligibilityWaystar(Request $request){
		$patient_id = Helpers::getEncodeAndDecodeOfId($request::input('patient_id'),'decode');
		$category = $request::input('category');
		if(isset($category) && !empty($category))
			$insuranceid = 0;
		else
			$insuranceid = $request::input('insuranceid');
		
		if(isset($insuranceid) && $insuranceid != 0){
			$patientInsuranceInfo = PatientInsurance::where('id',$insuranceid)->first();
			$data =  PatientEligibilityWaystar::where('patient_id',$patient_id)->where('insurance_id',$patientInsuranceInfo->insurance_id)->orderBy('id','desc')->first();
		}else{
			$data =  PatientEligibilityWaystar::where('patient_id',$patient_id)->orderBy('id','desc')->first();
		}
		return view('patients/eligibility/get_eligibility_waystar_details',  compact('data'));
	}
	
	public function getPatientEligibilityWaystarHistory(Request $request){
		$patient_id = $request::input('patient_id');
		$id = $request::input('id');
		$data =  PatientEligibilityWaystar::where('patient_id',$patient_id)->where('id',$id)->first();
		return view('patients/eligibility/get_eligibility_waystar_details',  compact('data'));
	}
	
	
	
	public function getPatientEligibility(Request $request)
	{
		
		$patient_id = $request::input('patient_id');
		$category 	= $request::input('category');
		$tempid 	= $request::input('tempid');
		$policyid 	 = $request::input('policyid');
		$page_type 	= $request::input('type');
		$insuranceid = $request::input('insuranceid');
		$eligibility = $request::input('eligibility');
		$insurance_id = explode("::",$insuranceid);
		if(count($insurance_id)>0)
		{
			$insuranceid = $insurance_id[0];
		}
		
		// check patient insurance page.

		$patient_insurance = '';
		
		if($patient_id != '' && !is_numeric($patient_id)){
			$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		}
		// Patient listing page.
		$ediEligibility 	= 0;
		if($category!='' && $patient_id != '')
		{
			$patient_insurance = PatientInsurance::where('patient_id',$patient_id)->where('category',$category)->first();
			if(count($patient_insurance)!=0)
			{
				$ediEligibility 	= EdiEligibility::with('insurance_details','patient','provider','user','userupdate','contact_details')->where('patient_id',$patient_id)->where('insurance_id',$patient_insurance->insurance_id)->where('policy_id',$patient_insurance->policy_id)->orderBy('created_at', 'desc')->first();	
			}
			else
			{
				return Lang::get("common.validation.ins_avail_msg");
			}				
		}elseif($eligibility!='' && $patient_id != ''){
			$eligibility = Helpers::getEncodeAndDecodeOfId($eligibility,'decode');
			$eligibility_details = EdiEligibility::where('patient_eligibility_id',$eligibility)->orderBy('created_at', 'desc')->first();
			$patient_insurance_details = PatientInsurance::where('patient_id',$patient_id)->where('insurance_id',$eligibility_details->insurance_id)->first();
			$category = $patient_insurance_details->category;
			if(count($patient_insurance)!=0)
			{
				$ediEligibility 	= EdiEligibility::with('insurance_details','patient','provider','user','userupdate','contact_details')->where('patient_id',$patient_id)->where('patient_eligibility_id',$eligibility)->orderBy('created_at', 'desc')->first();	
			}
			else
			{
				return Lang::get("common.validation.ins_avail_msg");
			}				
		}elseif($tempid == '' && $patient_id != ''){
			$patient_insurance = PatientInsurance::where('patient_id',$patient_id)->where('insurance_id',$insuranceid)->where('policy_id',$policyid)->first();
			$ediEligibility 	= EdiEligibility::with('insurance_details','patient','provider','user','userupdate','contact_details')->where('patient_id',$patient_id)->where('insurance_id',$insuranceid)->where('policy_id',$policyid)->orderBy('created_at', 'desc')->first();
		}
		
		if($patient_id == '')
		{
			$ediEligibility = EdiEligibility::with('insurance_details','patient','provider','user','userupdate','contact_details')->where('temp_patient_id',$tempid)->where('insurance_id',$insuranceid)->where('policy_id',$policyid)->orderBy('created_at', 'desc')->first();
		}
		
		
		if(!empty($ediEligibility)!= '')
		{
			$patienteligibilityid = $ediEligibility->patient_eligibility_id;
			$patient_eligibility = PatientEligibility::where('id',$patienteligibilityid)->first();
			
			if($ediEligibility->insurance_type =="Others")
			{
				$ediEligibilitydemo = EdiEligibilityDemo::where('edi_eligibility_id',$ediEligibility->id)->where('demo_type','subscriber')->first();
				$ediEliDemoDependent = EdiEligibilityDemo::where('edi_eligibility_id',$ediEligibility->id)->where('demo_type','dependent')->first();	
				
				$ediEli_Ins_SpPhy = '';
				$ediContact_detail = '';
				$ediEligibilityinsurance = '';
				if(EdiEligibilityInsurance::where('edi_eligibility_id',$ediEligibility->id)->count()>0)
				{
					$ediEligibilityinsurance = EdiEligibilityInsurance::where('edi_eligibility_id',$ediEligibility->id)->first();
					$ediEligibilityinsurance_id =$ediEligibilityinsurance->id;
					
					if(EdiEligibilityInsuranceSpPhysician::where('edi_eligibility_insurance_id',$ediEligibilityinsurance_id)->count()>0)
					{
						$ediEli_Ins_SpPhy = EdiEligibilityInsuranceSpPhysician::where('edi_eligibility_insurance_id',$ediEligibilityinsurance_id)->get();
						foreach($ediEli_Ins_SpPhy as $edi_phy)
						{
						$id[] = $edi_phy->id;
						}
						$ediContact_detail = EdiEligibilityContact_detail::whereIn('details_for_id',$id)->get();
					}
				}
				
				
				$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'encode');
				
				
				return view('patients/eligibility/get_eligibility_details',  compact('patient_eligibility','policyid','insuranceid','patient_id','category','ediEligibility','ediEligibilitydemo','ediEliDemoDependent','patient_insurance','ediContact_detail','ediEli_Ins_SpPhy','ediEligibilityinsurance','page_type'));		
			}
			else
			{
				$medicare_id = explode(",",$ediEligibility->plan_type);
				$edi_medicare_detail = EdiEligibilityMedicare::with('contact_details')->whereIn('id',$medicare_id)->orderBy("plan_type","ASC")->get()->toArray();
				return view('patients/eligibility/get_medicare_eligibility_details',  compact('patient_eligibility','patient_id','category','ediEligibility','patient_insurance','edi_medicare_detail'));
			}
		}
		else
		{
			return Lang::get("common.validation.no_record");
		}
		
	}
	/**** End to get the eligiblity details ***/
	
	
	/*** Insurance Eligibility Verification store in DB start ***/
	
	public function storeEdiApi($patient_id)
	{
		$eligibility_api = ApiConfig::where('api_for','insurance_eligibility')->where('api_status','Active')->first();
		$get_practiceAPI = DBConnectionController::getUserAPIIds('insurance_eligibility');
		$status = '';
		$tempid	= '';
		$error = '';
		if($eligibility_api && $get_practiceAPI ==1)
		{
			if($eligibility_api->api_name == 'eligible')
			{ 
				$request = Request::all();
				$elibility_arr = [];
				$elibility_arr['date'] = date('Y-m-d');
				$patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'],'decode');	
				$category = PatientInsurance::where('patient_id',$patient_id)->first();
				$elibility_arr['category'] = $category->category;
				$elibility_arr['provider_id'] = 6;
				$elibility_arr['patient_id'] = $patient_id;
				if($patient_id != '' && $patient_id != 'new')
				{
					// Get patient details
					$patient_details = Patient::where('id',$patient_id)->first();
					if($patient_details)
					{
						$elibility_arr['member_last_name'] = $patient_details->last_name;
						$elibility_arr['member_first_name'] = $patient_details->first_name;
						$elibility_arr['dob'] = $patient_details->dob;

						// Get Patient insurance details
						$patient_insurance_details = PatientInsurance::with('insurance_details','insurance_details.insurancetype')->where('patient_id',$patient_id)->where('category',$category->category)->first();
						if($patient_insurance_details)
						{
							$elibility_arr['insurance_id'] = $patient_insurance_details->insurance_id;
							$elibility_arr['member_id'] = $patient_insurance_details->policy_id;
							$elibility_arr['payer_id'] = isset($patient_insurance_details->insurance_details->payerid)? $patient_insurance_details->insurance_details->payerid : '';
							
							if(isset($patient_insurance_details->insurance_details->insurancetype->type_name))
								$elibility_arr['medicare'] = ($patient_insurance_details->insurance_details->insurancetype->type_name =="medicare") ? "checkMedicareEligibility":"checkEligibility";
							else
								$elibility_arr['medicare'] = "checkEligibility";
						}
						else
						{
							// Check the patient self pay or insurance.
							$status = 'error';
							$check_patientins = Patient::select('is_self_pay')->where('id',$patient_id)->first()->is_self_pay;
							if($check_patientins == 'Yes')
								$error = Lang::get("common.validation.patientins_selfpay_msg");
							else
								$error = Lang::get("common.validation.ins_avail_msg");
						}
					}
					else
					{
						$status = 'error';
						$error = Lang::get("common.validation.pat_avail_msg");
					}
				}
				if($error == '' ) 
				{
					$commonApiController = new CommonApiController();
					$eligibility_api_result = $commonApiController->$elibility_arr['medicare']($elibility_arr);
					$status = $eligibility_api_result['status'];
					$error	= $eligibility_api_result['error'];
					$tempid	= $eligibility_api_result['temp_id'];
				}
			}
			else
			{
				$status = 'error';
				$error =Lang::get("common.validation.api_credit_msg");
			}
		}
		else
		{
			$status = 'error';
			$error =Lang::get("common.validation.api_avail_msg");;
		}
				
		$result= json_encode(array("status"=>$status,"error"=>$error,"tempid"=>$tempid));
		return $result;
	}
	/***  Insurance Eligibility Verification store in DB End ***/
	public function getindexedi_eligibilityApi($patient_id = '')
	{
		echo'getindexedi_eligibilityApi';exit;
	}
	
	public function getEligibilityMoreInfo($id,$filename)
	{
		$main_dir_name 	= md5('P'.Session::get('practice_dbid'));
		$path = $main_dir_name."/patienteligibility/".$id."/";
		$file = Helpers::amazon_server_get_file($path,$filename);
		return Response::make($file, 200, [
		'Content-Type' => 'application/pdf',
		'Content-Disposition' => 'inline; filename="'.$filename.'"'
		]);
	}
	
	public function GetAuthTokenAPIPverifyPatient(){
		$commonApiController = new CommonApiController();
		$eligibility_api_result = $commonApiController->CheckPverifyEligibility();
		return $eligibility_api_result;
		
	}
        public function getEligibilityTemplateApi($patient_id = '',$export='')
	{
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id))
		{
			$patient_insurances = PatientInsurance::query()->where('patient_id', $patient_id)->leftjoin('insurances', 'insurances.id','=','patient_insurance.insurance_id')
			  ->select(['insurances.insurance_name as insname', DB::raw('CONCAT(insurances.id, "-", patient_insurance.category) AS full_name')])->pluck('insname', 'full_name')->all();			
			$benefit_verification = PatientEligibility::with('provider','provider.degrees','provider.provider_types','facility','facility.facility_address','facility.pos_details','facility.speciality_details','template','insurance_details','user')->where('patients_id', $patient_id)->where('is_edi_atatched',"!=", 1)->get();
			//dd($benefit_verification);
			$benifit_templates = Template::with('creator')->where('status', "Active")->where('template_type_id', 1)->get(); 
			//dd($benifit_templates);
			$eligibility = PatientEligibility::with('insurance_details','user','get_eligibilityinfo')->where('patients_id', $patient_id)->where('is_edi_atatched', 1)->get();
			return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_insurances','eligibility','benefit_verification', 'benifit_templates')));
		}
		else 
		{
			return Response::json(array('status' => 'failure', 'message' =>Lang::get("common.validation.empty_record_msg"), 
				'data' => ''));
		}
	}
	
}