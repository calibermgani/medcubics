<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Lang;
use View;
use App;
use App\Models\Patients\Patient as Patient;
use App\Models\PatientStatementSettings as PatientStatementSettings;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use App\Http\Controllers\Api\PatientbulkstatementApiController as PatientbulkstatementApiController;
use App\Models\EmailTemplate;
use App\Models\PatientStatementTrack as PatientStatementTrack;
use App\Models\Patients\CheckReturn as CheckReturn;
use App\Http\Helpers\Helpers as Helpers;
use Config;
use DB;
use URL;
use App\Traits\ClaimUtil;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use Redirect;
use Illuminate\Http\Response as Responseobj;
use ZipArchive;


class PatientindividualstatementApiController extends Controller 
{
	/*** lists page Starts ***/
	public function getIndexApi() 
	{
		$psettings = PatientStatementSettings::first();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('psettings')));
    }
	/*** Lists Function Ends ***/
	
	// Search patient based on patient name.
	public function getPatientListApi($patientname) 
	{
		$result = Request::all();
		$psettings = PatientStatementSettings::first();
		$query = Patient::whereHas('patient_claim',function($q){
			$q->where('payment_hold_reason','!=','Patient')->whereNotIn('status', ['Hold','E-bill']);
		})->where('status','Active')->where('statements','Yes');
       //split both comma(,), space( )          
        $search_arr =  preg_split("/(:| |;)/", $result['patient_search_key']);
        $search_key = $result['patient_search_key'];
		if(count((array)$search_arr) > 2)
        {
            foreach($search_arr as $search) {
				$query->where(function($sub_query)use($search){
					$sub_query->where('last_name','LIKE','%'.rtrim($search, ',').'%')->orWhere('first_name','LIKE','%'.$search.'%')
					->orWhere('middle_name','LIKE','%'.$search.'%')->orWhere('account_no', 'LIKE','%'.$search.'%');
				});
			}
        }
        elseif(count((array)$search_arr) == 2) {
			$query->where(function($sub_query)use ($search_arr,$search_key) {
			/*	$sub_query->where('last_name','LIKE','%'.$search_arr[0].'%')->orWhere('first_name','LIKE','%'.$search_arr[0].'%')->orWhere('account_no','LIKE','%'.$search_arr[0].'%')
				->orWhere('last_name','LIKE','%'.$search_arr[1].'%')->orWhere('first_name','LIKE','%'.$search_arr[1].'%')->orWhere('account_no','LIKE','%'.$search_arr[1].'%');*/
				  $sub_query->orWhere(DB::raw('CONCAT(patients.last_name,", ", patients.first_name)'),  'like', "%{$search_key}%")->orWhere('account_no','LIKE','%'.$search_key.'%');
			});
		}
        elseif(count($search_arr) < 2) {
			$query->where(function($sub_query)use ($search_arr)	{
				$sub_query->where('last_name','LIKE','%'.$search_arr[0].'%')->orWhere('first_name','LIKE','%'.$search_arr[0].'%')->orWhere('account_no','LIKE','%'.$search_arr[0].'%');
			});
		}           
        
        $patients_arr = $query->get();
		$patient_balance = array();
		foreach($patients_arr as $patient_value)
		{
			//$get_data = Patient::getPatienttabData($patient_value->id);
			//$getpatient_balance  = $get_data['patient_due'];

			$res = Patient::paymentclaimARForStmt($patient_value->id);
        	$getpatient_balance = @$res['patient_balance'];
			
			if($psettings->financial_charge == '1'){
				$financial_charges = CheckReturn::where('patient_id',$patient_value->id)->sum('financial_charges');
				$getpatient_balance = $financial_charges +$getpatient_balance; 
			}
			
			$patient_balance[$patient_value->id]['balance'] = $getpatient_balance; 
			
			// @todo check and update new pmt flow.
			// Show patient latest payment info.
			$patient_latestpayment = [];
			
			$patient_latestpayment = DB::table('claim_info_v1')
				->join('pmt_claim_tx_v1', 'claim_info_v1.id', '=', 'pmt_claim_tx_v1.claim_id')
				->join('pmt_info_v1', 'pmt_claim_tx_v1.payment_id', '=', 'pmt_info_v1.id')
				->select('pmt_claim_tx_v1.created_at', 'pmt_info_v1.pmt_amt')
				->where('claim_info_v1.patient_id',$patient_value->id)
				->where('claim_info_v1.payment_hold_reason','!=','Patient')
				->where('pmt_claim_tx_v1.pmt_type','Payment')
				->where('pmt_claim_tx_v1.pmt_method','Patient')
				->where('claim_info_v1.status','!=','Hold')
				->where('claim_info_v1.status','!=','E-bill')
				->where('pmt_claim_tx_v1.total_paid','!=', '0.00')
				->orderBy('pmt_claim_tx_v1.id', 'desc')
				->first();
				
			$patient_balance[$patient_value->id]['lastpayment'] = '0';
			$patient_balance[$patient_value->id]['lastpaymentdate'] = '';
			if(count((array)$patient_latestpayment)>0) {
				$patient_balance[$patient_value->id]['lastpayment'] = $patient_latestpayment->pmt_amt;
				$patient_balance[$patient_value->id]['lastpaymentdate'] = $patient_latestpayment->created_at;	
			}
						
			/*
			DB::table('claim_info_v1')
            ->join('payment_claim_details', 'claim_info_v1.id', '=', 'payment_claim_details.claim_id')
			->join('pmt_info_v1', 'payment_claim_details.payment_id', '=', 'pmt_info_v1.id')
            ->select('payment_claim_details.created_at', 'payment_claim_details.patient_paid_amt', 'payments.payment_mode')
            ->where('claim_info_v1.patient_id',$patient_value->id)
            ->where('claim_info_v1.payment_hold_reason','!=','Patient')
            ->where('pmt_info_v1.pmt_type','Payment')->where('pmt_info_v1.pmt_method','Patient')
            ->where('claim_info_v1.status','!=','Hold')
            ->where('claim_info_v1.status','!=','E-bill')
            ->where('payment_claim_details.patient_paid_amt','!=', '0.00')
            ->orderBy('payment_claim_details.id', 'desc')
            ->first();
			
			$patient_balance[$patient_value->id]['lastpayment'] = '0';
			$patient_balance[$patient_value->id]['lastpaymentdate'] = '';
			//if(count($patient_latestpayment)>0)
			{	$patient_paid = Helpers::getPatientLastPaymentAmount($patient_value->id ,$method = 'Patient');
				$patient_balance[$patient_value->id]['lastpayment'] = $patient_paid->total_paid;
				$patient_balance[$patient_value->id]['lastpaymentdate'] =  $patient_paid->created_at;	
			}*/
		}		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('patients_arr','patient_balance')));
	}
		
	/*** Process patient statement based on type (preview, send statement, email statement) ***/
	public function getTypeApi($patient_id,$submit_type,$paymentmessage) 
	{
		$patientarray = Patient::getAllpatients();	
		if($patient_id!='')
		{
			if(!is_numeric($patient_id))
				  $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		
			$get_patient = Patient::findOrFail($patient_id);
			
			// Create Patient Statement PDF, if patient valid. 
			$callstatement = new PatientbulkstatementApiController;	
			if($submit_type == 'sendxmlstatement')		
				$downlod_type = 'xml';
			else
				$downlod_type = ($submit_type == 'sendcsvstatement') ? 'csv':'pdf';
			$get_message = $callstatement->checkPatientStatement($patient_id,$submit_type,$paymentmessage,$downlod_type); 
			
			if($get_message['status'] == 'success')
			{
				$path_with_filename = $get_message['message'];
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>$get_message['message']));
			}
			
			$chk_env_site   = getenv('APP_ENV');
			$default_view = Config::get('siteconfigs.production.defult_production');
			
			if($path_with_filename!='') 
			{
				$get_newfile = explode('patientstatement/',$path_with_filename);
			
				$get_filename = explode('/',$get_newfile[1]);
				
				if($submit_type == 'sendstatement' || $submit_type == 'sendcsvstatement' || $submit_type == 'sendxmlstatement')
				{
					// Download statment
					$collect_pdf = array_pop($get_filename); 
					$currentdate = App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'Y-m-d');
					$get_newfilepreview = explode('media/',$path_with_filename);
					$collect_pdf = url('media/'.$get_newfilepreview[1]);
					if($submit_type == 'sendcsvstatement') {
						return Response::json(array('status'=>'success', 'message'=>$collect_pdf,'filename'=>$patientarray[$patient_id].'-'.$currentdate.'.csv'));
					} elseif($submit_type == 'sendxmlstatement'){
						return Response::json(array('status'=>'success', 'message'=>$collect_pdf,'filename'=>$patientarray[$patient_id].'-'.$currentdate.'.xml'));	
					} else {
						return Response::json(array('status'=>'success', 'message'=>$collect_pdf,'filename'=>$patientarray[$patient_id].'-'.$currentdate.'.pdf'));
					}
				}
				elseif($submit_type == 'preview')
				{
					// Preview statment
					$get_newfilepreview = explode('media/',$path_with_filename);
					$collect_pdf = url('media/'.$get_newfilepreview[1]);
					return Response::json(array('status'=>'success', 'message'=>$collect_pdf,'filename'=>$patientarray[$patient_id].'.pdf'));
				}
				elseif($submit_type == 'emailstatement')
				{
					$get_newfilepreview = explode('media/',$path_with_filename);
					$collect_pdf = url('media/'.$get_newfilepreview[1]);
					// mail send to patient with patient statement attachement.
					if(EmailTemplate::where('template_for','patientstatement')->count()>0 && $get_patient->email!='')
					{
						$templates = EmailTemplate::where('template_for','patientstatement')->first();
						$get_Email_Template = $templates->content;					
						$arr = [
							"##LASTNAME##" =>$get_patient->last_name,
							"##FIRSTNAME##" => $get_patient->first_name,
							//"##STATEMENTDOWNLINK##" => '<a href="'.$collect_pdf.'" target="_blank" style="padding:15px 30px;text-decoration:none; margin-top:30px; border-radius:4px;background:#f07d08;height:40px">Download Statement</a>'
							"##STATEMENTDOWNLINK##" => '<br><a href="'.$collect_pdf.'" target="_blank" style="padding: 8px 12px; border: 1px solid #f07d08;border-radius: 2px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #f07d08;text-decoration: none;font-weight:bold;display: inline-block;height:40px">Download Statement</a>'
						];

						$email_content = strtr($get_Email_Template, $arr);					
						$res = array('email'=>	$get_patient->email,
							'subject'	=>	$templates->subject,
							'msg'		=>	$email_content,
							'name'		=>	@$get_patient->last_name.' '.$get_patient->first_name,
							'attachment'=> $path_with_filename,
							'attachment_as'=> array_pop($get_filename),
							'attachment_mime'=> 'text/pdf'
						);	
						
						$get_result = CommonApiController::connectEmailApi($res); 
						
						if($get_result == 'Success') {
							return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.mail_send_msg"),'filename'=>''));
						} elseif($get_result != 'Success') {
							return Response::json(array('status'=>'failure', 'message'=>$get_result));
						}
					}
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.mail_send_msg"),'filename'=>''));
				}
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("practice/practicemaster/patientstatementsettings.validation.nopatientbalance")));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.not_found_msg")));
		}
	}
	
	public function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }

	// Download the patient statement.
	public function getIndividualDownloadApi($filename,$id,$existname)
	{
		set_time_limit(0);
		try{

			$get_unique_no = date("m-d-Y");

            if (is_dir('pst')) {
                $this->deleteDirectory('pst');
            }
            if (!file_exists('pst'))
                mkdir('pst');

            $path = url('media/patientstatement/'.urlencode($existname)); 

            $outputzippath = 'pst/patientstatement_' . $get_unique_no . '.zip';
            $zipname = 'patientstatement_' . $get_unique_no . '.zip';

            $zip = new ZipArchive;
            $zip->open($outputzippath, ZipArchive::CREATE);
            $i = 1;
            $currentdate = date('Y-m-d');
           	$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $path);
			curl_setopt($ch, CURLOPT_TIMEOUT, 0); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Encoding: none','Content-Type: application/pdf')); 

			header('Content-type: application/pdf');
			$result = curl_exec($ch);
			curl_close($ch);
			echo $result;
            //$content = file_get_contents($filess);
           
			/*
			header('Content-type: text/pdf');    
			// What file will be named after downloading                                  
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			
			$img_details = ['practice_name'=>null,'module_name'=>'patientstatement','patient_id'=>$id,'file_name'=>$existname];
			
			$path = url('media/patientstatement/'.$existname); 

			// File to download
	        readfile($path);
	        $contextOptions = array(
	            'ssl' => array(
	                'verify_peer' => false,
	                'verify_peer_name' => false
	            )
	        );
	        file_get_contents($path, false, stream_context_create($contextOptions));
	        */
	    } catch(Exception $e){
	    	\Log::info("Exception occured on download PDF. msg: ".$e->getMessage() );
	    }
	}
	
	// Get patient balance using patient id
	public function getPatientDetailsApi($patientid)
	{
		if (!is_numeric($patientid))
			$patientid = Helpers::getEncodeAndDecodeOfId($patientid,'decode');
		
		$patient_bal  = Helpers::getPatientDuebalSTMT($patientid,'patient_balance');
		$patient_balance  = ($patient_bal ==  0) ? 0 : $patient_bal; 
		$patient_balance = Helpers::priceFormat($patient_balance) ;
		//use for span tag is 0 balance in color change
		$patient_balance = "<span class='med-red'>$patient_balance</span>";
		
		$patients = Patient::where('id',$patientid)->select('email')->first();		
		$psettings = PatientStatementSettings::first();
		$settings = count((array)$psettings);
		
		return json_encode(array('balance'=>$patient_balance,'email'=>$patients->email,'settings'=>$settings));
	}
	
	public static function checkPatientClaimInfo($patientid)
	{
		if (!is_numeric($patientid))
			$patientid = Helpers::getEncodeAndDecodeOfId($patientid,'decode');
		
		$calculate_claim = ClaimInfoV1::where('patient_id',$patientid)->where('payment_hold_reason','!=','Patient')->whereNotIn('status', ['Hold','E-bill'])->count();		
		if($calculate_claim>0 )
		{
			return true;	
		}
	}
	
	public function getStatementHistoryApi($patient_id)
	{
		if($patient_id!='')
			$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		
		$statement_history = $this->getStmtHistorySearchApi($patient_id);
		$insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();
		$patient_list = []; 
		/*Patient::whereHas('patient_claim',function($q){
			$q->where('payment_hold_reason','!=','Patient')->whereNotIn('status', ['Hold','E-bill']);
		})->selectRaw('CONCAT(last_name,", ",first_name, " ",middle_name) as patient_name, id')->where('status','Active')->pluck('patient_name','id')->all();
		*/
		$psettings = PatientStatementSettings::first();
		return Response::json(array('status'=>'success','data'=>compact('statement_history','psettings','patient_list', 'insurances')));
	}
	
	public function getStmtHistorySearchApi($patient_id)
	{
		$request = Request::all();
		
		$statement_historylist = PatientStatementTrack::with('patient_detail','user_detail');
		
		if(isset($patient_id) && $patient_id!='')
		{
			$statement_historylist->where('patient_id',$patient_id);
		}
			
		if((isset($request['patient_search']) && $request['patient_search']!='') && ($request['patient_text'] != ''))
		{
			$statement_historylist->whereHas('patient_detail',function($q) use($request){
				
				if($request['patient_search'] == '1')
				{
					$search_arr = explode(' ',$request['patient_text']);
					if(count((array)$search_arr) > 0)
					{
						foreach($search_arr as $search)
						{
							$q->where(function($sub_query)use($search)
							{
								$sub_query->where('last_name','LIKE','%'.rtrim($search, ',').'%')->orWhere('first_name','LIKE','%'.$search.'%')->orWhere('middle_name','LIKE','%'.$search.'%');
							});
						}
					}
				}
				elseif($request['patient_search'] == '2')
				{
					$q->where('account_no','LIKE','%'.$request['patient_text'].'%');
				}
				elseif($request['patient_search'] == '3')
				{
					 $currentdob = date('Y-m-d',strtotime($request['patient_text'])); 
					
					$q->where('dob','LIKE','%'.$currentdob.'%');
				}
				elseif($request['patient_search'] == '4')
				{
					$q->where('ssn','LIKE','%'.$request['patient_text'].'%');
				}
				elseif($request['patient_search'] == '5')
				{
					$q->where('gender','LIKE','%'.$request['patient_text'].'%');
				}
			});
		}
		
		if(@$request['billed_option'] != '' && @$request['billed'] != '')
		{
			if($request['billed_option'] == 'lessthan')
                $billed_option = '<';
            elseif($request['billed_option'] == 'lessequal')
                $billed_option = '<=';
            elseif($request['billed_option'] == 'equal')
                $billed_option = '=';
            elseif($request['billed_option'] == 'greaterthan')
                $billed_option = '>';
            elseif($request['billed_option'] == 'greaterequal')
                $billed_option = '>=';            
            else
                $billed_option = '=';  
            $billed_amount = $request['billed'];
			
			$statement_historylist->where('balance',$billed_option,$billed_amount);
		}
		
		if(isset($request['type']) && $request['type']!='')
		{
			$statement_historylist->where('type_for',$request['type']);
		}
		
		if(isset($request['sendstatement_from']) && $request['sendstatement_to']!='')
		{
			$from = date("Y-m-d", strtotime($request['sendstatement_from']));
            $to = date("Y-m-d", strtotime($request['sendstatement_to']));
            $statement_historylist->whereBetween('send_statement_date', [$from, $to]);
		}
		
		if(isset($request['paybydate_from']) && $request['paybydate_to']!='')
		{
			$from = date("Y-m-d", strtotime($request['paybydate_from']));
            $to = date("Y-m-d", strtotime($request['paybydate_to']));
            $statement_historylist->whereBetween('pay_by_date', [$from, $to]);
		}
				
		$result = $statement_historylist->orderBy('id', 'desc')->get();		
		return $result;
	}
		
	function __destruct() 
	{
    }
    
    public function getPatientStatementsApi() 
	{
		$psettings = PatientStatementSettings::first();
                 $patients_arr = $query->get();
		
		$patient_balance = array();
		foreach($patients_arr as $patient_value)
		{
			$getpatient_balance  = Helpers::getPatientDueInsuranceDue($patient_value->id,'patient_balance');
			
			if($psettings->financial_charge == '1'){
				$financial_charges = CheckReturn::where('patient_id',$patient_value->id)->sum('financial_charges');
				$getpatient_balance = $financial_charges +$getpatient_balance; 
			}
			
			$patient_balance[$patient_value->id]['balance'] = $getpatient_balance; 
			// @todo check and update new pmt flow
			// Show patient latest payment info.		
			
			$patient_latestpayment = DB::table('claim_info_v1')
				->join('pmt_claim_tx_v1', 'claim_info_v1.id', '=', 'pmt_claim_tx_v1.claim_id')
				->join('pmt_info_v1', 'pmt_claim_tx_v1.payment_id', '=', 'pmt_info_v1.id')
				->select('pmt_claim_tx_v1.created_at', 'pmt_info_v1.pmt_amt')
				->where('claim_info_v1.patient_id',$patient_value->id)
				->where('claim_info_v1.payment_hold_reason','!=','Patient')
				->where('pmt_claim_tx_v1.pmt_type','Payment')
				->where('pmt_claim_tx_v1.pmt_method','Patient')
				->where('claim_info_v1.status','!=','Hold')
				->where('claim_info_v1.status','!=','E-bill')
				->where('pmt_claim_tx_v1.total_paid','!=', '0.00')
				->orderBy('pmt_claim_tx_v1.id', 'desc')
				->first();
				
			$patient_balance[$patient_value->id]['lastpayment'] = '0';
			$patient_balance[$patient_value->id]['lastpaymentdate'] = '';
			if(count((array)$patient_latestpayment)>0) {
				$patient_balance[$patient_value->id]['lastpayment'] = $patient_latestpayment->pmt_amt;
				$patient_balance[$patient_value->id]['lastpaymentdate'] = $patient_latestpayment->created_at;	
			}			
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('psettings','$patients_arr','patients_arr')));
    }
}