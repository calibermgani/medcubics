<?php namespace App\Http\Controllers\Reports\Billing\Api;

use App;
use Config;
use Response;
use DB;
use Request;
use Input;use Session;
use Carbon\Carbon ;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payments\ClaimInfoV1;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Facility as Facility;
use App\Models\Provider as Provider;
use App\Models\Holdoption as Holdoption;
use App\Models\ClaimSubStatus as ClaimSubStatus;
use App\Models\Insurance as Insurance;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
class BillingApiController extends Controller {
	
	public function getEnddaytotalApi(){
		$cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
		return Response::json(array('status' => 'success', 'message' => null,'data' =>compact('cliam_date_details')));
	}

	public function getFilterResultApi(){
		$request = Request::All();
		if($request['hidden_from_date'] == '')
			$start_date = $request['from_date'];
		else
			$start_date = $request['hidden_from_date'];
		if($request['hidden_to_date'] == '')
			$end_date = $request['to_date'];
		else
			$end_date = $request['hidden_to_date'];
		// @todo - check and replace new pmt flow
		$filter_result	= [];	
		/**
		@todo - check and replace this new function 
		PMTClaimTXV1::has('payment_info')
								->whereHas('payment_info', function($q){$q->whereIn('pmt_type', ["Payment","Adjustment","Refund"])->whereNotIn('source', ["refundwallet"]);}) 
								->has('claim')
								->with('claim','user','payment',"claim_patient_det")

								->whereRaw("(pmt_type = 'Insurance' or pmt_type = 'Patient') and (patient_paid_amt != '0.00' or insurance_paid_amt != '0.00' or total_adjusted != '0.00' or total_withheld != '0.00') ")

								->where('created_at','>=',date("Y-m-d",strtotime($start_date)))
								->where('created_at','<=',date("Y-m-d",strtotime($end_date)))
								->get();
		// Old Function.
		PaymentClaimDetail::has('payment')
		->whereHas('payment', function($q){$q->whereIn('payment_type', ["Payment","Adjustment","Refund"])
		->whereNotIn('type', ["refundwallet"]);})
		->has('claim')
		->with('claim','user','payment',"patient")
		->whereRaw("(payment_type = 'Insurance' or payment_type = 'Patient') and (patient_paid_amt != '0.00' or insurance_paid_amt != '0.00' or total_adjusted != '0.00' or total_withheld != '0.00') ")
		->where('created_at','>=',date("Y-m-d",strtotime($start_date)))
		->where('created_at','<=',date("Y-m-d",strtotime($end_date)))
		->get();
		*/

		return Response::json(array('status' => 'success', 'message' => null,'data' =>compact('start_date','end_date','filter_result')));
	}
	
	public function getExportResultApi(){
		$request = Input::get();
		// @todo - check and replace new pmt flow
		$export_result		=	[]; //PaymentClaimDetail::has('payment')->whereHas('payment', function($q){$q->whereIn('payment_type', ["Payment","Adjustment","Refund"])->whereNotIn('type', ["refundwallet"]);})->has('claim')->with('claim','user','payment',"patient")->whereRaw("(payment_type = 'Insurance' or payment_type = 'Patient') and (patient_paid_amt != '0.00' or insurance_paid_amt != '0.00' or total_adjusted != '0.00' or total_withheld != '0.00') ")->where('created_at','>=',date("Y-m-d",strtotime($request['start-date'])))->where('created_at','<=',date("Y-m-d",strtotime($request['end-date'])))->get();
			
		return Response::json(array('status' => 'success', 'message' => null,'data' =>compact('export_result')));
	}
	
	public function getUnbilledClaimApi(){
		$unbilled_claim_details = ClaimInfoV1::with(['insurance_details','rendering_provider','billing_provider','patient'])->where('claim_submit_count','0')->where('insurance_id','!=','0')->get();
		return Response::json(array('status' => 'success', 'message' => null,'data' =>compact('unbilled_claim_details')));
	}
	
	### Aging report module start ###
	/*** index function start ***/
	public function getAgingReportApi()
	{
		$insurance = Insurance::where('status','Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
		$facilities = Facility::getAllfacilities();
		return Response::json(array('status' => 'success', 'message' => null, 'data' =>compact('insurance','facilities')));
	}
	/*** index function end ***/

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	/*** search function start ***/
	public function getAgingReportSearchApi($export ='')
	{
		$request= Request::All();
		$aging_report = $this->getAgingReportResult($request);
		$result = array();
		$header = $aging_report['header'];
		$title = $aging_report['title'];
		$unbilled = $aging_report['unbilled'];
		unset($aging_report['title']);
		unset($aging_report['header']);
		foreach($aging_report  as $aging_report_key =>$aging_report_value)
		{	
			$result[$aging_report_key] = $aging_report_value;
			if($request['aging_by'] !='all' && $request['aging_by'] !='patient')
			{
			/*
				foreach(@$aging_report['provider']  as $key =>$value)
				{	
					$result[$key] = $value;
				}*/
			}
		}
		unset($result['provider']);
		$aging_report_list = $result;
		if($export != "") 
		{
			$aging = $this->getAgingReportExport($unbilled);
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($aging['exportparam'], $aging['value'], $export); 
		}
		if($export != "") 
		{
			$exportparam = array(
				'filename'	=>	$title,
				'heading'	=>	'aging_report',
				'fields' 	=>	$header);
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $aging_report_list, $export); 
		}
		return Response::json(array('status' => 'success', 'message' => null,'data' =>compact('aging_report_list','title','header')));
	}
	/*** search function end ***/
	### Export Start ###
	public function getAgingReportExport($unbilled)
	{	
		$total_charge_count  = 0;
		$get_list =[];
		$billed_amt = $_0to30 = $_31to60 = $_61to90 = $_91to120 = $_121to150 = $_150_above = $insurance_due = $patient_due =$total_due = 0;
		foreach($unbilled as $billed){
			foreach($billed as $keys=>$unbilled){
			foreach($unbilled as $unbilled)
			{				
				$patient_name = Helpers::getNameformat(@$unbilled->patient->last_name,@$unbilled->patient->first_name,@$unbilled->patient->middle_name);
				$get_arr = [];
				$get_arr['account_no']		= @$unbilled->patient->account_no;
				$get_arr['patient_name']		= $patient_name;
				$get_arr['claim_number']		= @$unbilled->claim_number;
				$get_arr['date_of_service']		= @$unbilled->date_of_service;
				$get_arr['insurance_details']		= isset($unbilled->insurance_details)? $unbilled->insurance_details->insurance_name: 'Patient';
				$billed_amt += $get_arr['billed']		= @$unbilled->total_charge;				
				$_0to30 +=     $get_arr['0to30']		= ((@$keys == "0-30")) ? @$unbilled->balance_amt : '0.00';
				$_31to60 +=    $get_arr['31to60']	= ((@$keys == "31-60")) ? @$unbilled->balance_amt : '0.00';
				$_61to90 += 	  $get_arr['61to90']	= ((@$keys == "61-90")) ? @$unbilled->balance_amt : '0.00';
				$_91to120 +=   $get_arr['91to120']	= ((@$keys == "91-120")) ? @$unbilled->balance_amt : '0.00';
				$_121to150 +=  $get_arr['121to150']	= ((@$keys == "121-150")) ? @$unbilled->balance_amt : '0.00';
				$_150_above += $get_arr['150toabove']= ((@$keys == "151-above")) ? @$unbilled->balance_amt : '0.00';
				$patient_due += $get_arr['patient_due']= @$unbilled->patient_due;
				$insurance_due += $get_arr['insurance_due']= @$unbilled->insurance_due;
				$total_due += $get_arr['total_due']			= Helpers::priceFormat(@$unbilled->insurance_due + @$unbilled->patient_due);
				$get_list[$total_charge_count] = $get_arr;
				$total_charge_count++;
			}}
			$get_result = $get_list;
		}
		$get_result[$total_charge_count] = ['account_no'=>'Total','patient_name'=>'','claim_number'=>'','date_of_service'=>'','insurance_details'=>'','billed'=>''.Helpers::priceFormat($billed_amt),'0to30'=>''.Helpers::priceFormat($_0to30),'31to60'=>''.Helpers::priceFormat($_31to60),'61to90'=>''.Helpers::priceFormat($_61to90),'91to120'=>''.Helpers::priceFormat($_91to120),'121to150'=>''.Helpers::priceFormat($_121to150),'150toabove'=>''.Helpers::priceFormat($_150_above),'patient_due'=>''.Helpers::priceFormat($patient_due),'insurance_due'=>''.Helpers::priceFormat($insurance_due),'total_due'=>''.Helpers::priceFormat($total_due)];
			
		$result["value"] = json_decode(json_encode($get_result));
		$result["exportparam"] = array(
			'filename'	=>	'Aging Report',
			'heading'	=>	'',
			'fields' 	=>	array(
				'account_no'	=>	'Account no',
				'patient_name'		=>	'Patient name',
				'claim_number'		=>	'Claim Number',
				'date_of_service'	=>	'Date Of Service',
				'insurance_details'	=>	'Insurance Details',
				'billed'			=>	'Billing',
				'0to30'				=>	'0 to 30',
				'31to60'			=>	'31 to 60',
				'61to90'			=>	'61 to 91',
				'91to120'			=>	'91 to 120',
				'121to150'			=>	'121 to 150',
				'150toabove'		=>	'150 above',
				'patient_due'		=>	'Patient Due',
				'insurance_due'		=>	'Insurance Due',
				'total_due'			=>	'Total Due',
			)
		);
		return $result;
		
	}
	### Export End ####
	
	/*** result function start ***/
	public function getAgingReportResult($request)
	{
		$unbilled_arr ='';
		### Initialize two rows of result ###
		$responce['header'] = ["AR Days","Unbilled",'',"0-30",'',"31-60",'',"61-90",'',"91-120",'',"121-150",'',">150",'',"Totals",''];
		
		$responce['name'] 	= ["","Claims","Value","Claims","Value","Claims","Value","Claims","Value","Claims","Value","Claims","Value","Claims","Value","Claims","Value"];
		$claims = ClaimInfoV1::with('patient','insurance_details')->where('claim_submit_count','==',0)->where('self_pay','==','No')->orderBy('id','asc');
		$responce['header'] =['Acct','patient Name','Claim no','Dos','Responsibility','Billed',"0-30","31-60","61-90","91-120","121-150",">151",'Pat Bal','Ins Bal', "Total Bal"]; 
		$claim_by = $request['claim_by'];
		$age_date = ["0-30","31-60","61-90","91-120","121-150","151-above"];
		
		$provider = $request['rendering_provider_id'];
		$bill_providers = $request['billing_provider_id'];
		$facility = $request['facility_id'];
		$insurance = $request['insurance_id'];
		
		$render_provider = $billing_provider_id  = $facility_id = $insurance_id =[]; 
		if($request['aging_by']== 'all' || $provider != 'all' || $bill_providers != 'all' || $facility_id != 'all' || $insurance_id !='all'){
			$patient= 'all';
			$claim_count= $this->aging_days1($claim_by,$request["aging_days"],$age_date,$provider,$bill_providers,$patient,$facility,$insurance);	
			$render_provider[] = $provider;
			$billing_provider_id[] = $bill_providers;
			$facility_id[] = $facility;
		}	
		
		if($request['aging_by']== 'rendering_provider' && $provider == 'all'){
			$rendering_provider = array_unique(ClaimInfoV1::pluck('rendering_provider_id')->all());
			foreach($rendering_provider as $ren_provider1)
			{
				$bill_provider= $patients = $insurance = $facility ="all";
				$claim_count[]= $this->aging_days1($claim_by,$request["aging_days"],$age_date,$ren_provider1,$bill_provider,$patients, $facility,$insurance);
				$render_provider[] = $ren_provider1;
			}
			unset($claim_count['unbilled']);
		}
		//Aging by insurance
		if($request['aging_by']== 'insurance' && $insurance == 'all'){ 
			$insurances = array_unique(ClaimInfoV1::pluck('insurance_id')->all());
			
			foreach($insurances as $insurances)
			{	if($insurances != 0){
				$bill_provider = $patients = $ren_provider = $facility = "all";
				$claim_count[]= $this->aging_days1($claim_by,$request["aging_days"],$age_date,$ren_provider,$bill_provider,$patients, $facility,$insurances);
				$insurance_id[] = $insurance;
				$ins[] =$insurances;
				}
			}
			unset($claim_count['unbilled']);
		}
		//Aging by Billing provider
		if($request['aging_by']== 'billing_provider' && $bill_providers == 'all'){
			$billing_provider = array_unique(ClaimInfoV1::pluck('billing_provider_id')->all());
			foreach($billing_provider as $bill_provider)
			{	
				$ren_provider= $patients = $insurance = "all";
				$claim_count[]= $this->aging_days1($claim_by,$request["aging_days"],$age_date,$ren_provider,$bill_provider,$patients, $facility,$insurance);
				$billing_provider_id[] = $bill_provider;
			}
			unset($claim_count['unbilled']);
		}
		// Aging by patient
		if($request['aging_by']== 'patient'){
			$patients = array_unique(ClaimInfoV1::pluck('patient_id')->all());
			foreach($patients as $patient)
			{	
				$bill_provider = $ren_provider = $insurance = "all";
				$claim_count[]= $this->aging_days1($claim_by,$request["aging_days"],$age_date,$ren_provider,$bill_provider,$patient, $facility,$insurance);
				$patient_id[] = $patient;
			}
			unset($claim_count['unbilled']);
		}
		//Aging by Facility
		if($request['aging_by']== 'facility' && $request['facility_id']== 'all'){
			$facility = array_unique(ClaimInfoV1::pluck('facility_id')->all());
			foreach($facility as $facility)
			{	
				$bill_provider = $ren_provider = $patient = $insurance= "all";
				$claim_count[]= $this->aging_days1($claim_by,$request["aging_days"],$age_date,$ren_provider,$bill_provider,$patient, $facility,$insurance);
				$facility_id[] = $facility;
			}
			unset($claim_count['unbilled']);
		}
		$responce['title'] = $request;
		$responce['unbilled'] = $claim_count;
		$responce['rendering_provider_id'] = $provider;
		$responce['billing_provider_id'] = $bill_providers;
		$responce['bill_provider'] = @$bill_provider;
		$responce['aging_by'] = $request['aging_by'];
		$responce['render_provider'] = $render_provider;
		$responce['billing_provider'] = $billing_provider_id;
		$responce['patient_id'] = @$patient_id;
		$responce['facility_id'] = $facility_id;
		$responce['insurance_id'] = $insurance;
		$responce['ins'] = @$ins;
		$responce['claim_by'] = @$claim_by;
		$responce['aging_days'] = $request["aging_days"];
		
		return $responce;
	}
	/*** result function end ***/
	function aging_days1($claim_by,$request,$age_date,$provider,$bill_provider,$patient, $facility,$insurance)
	{ 
		
		$unbilled = $total_arr = $billed= [];
		foreach($age_date as $key=>$value)
		{
			$claims = ClaimInfoV1::with('patient','insurance_details')->where('claim_submit_count','=',0)->where('self_pay','=','No');
			if($provider != 'all')
			{
				$claims->where('rendering_provider_id',$provider);
			}	
			if($bill_provider != 'all')
			{
				$claims->where('billing_provider_id',$bill_provider);
			}	
			
			if($patient != 'all')
			{
				$claims->where('patient_id',$patient);
			}	
			if($insurance != 'all')
			{
				$claims->where('insurance_id',$insurance);
			}	
			if($facility != 'all')
			{
				$claims->where('facility_id',$facility);
			}	
			if(($request == "0-30" || $request == 'all') && $age_date[0] == $value){
				$last_month_carbon = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(30)));
				$current_month = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(0)));
				if($claim_by  == 'create_by')
					$unbilled = $claims->where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month);
				else
				{
					$last_month_carbon = date('Y-m-d',strtotime(Carbon::now()->subDay(30)));
					$current_month = date('Y-m-d ',strtotime(Carbon::now()->subDay(0)));
					$unbilled = $claims->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
				}
				$billed[$value] =$unbilled->get();		
			}
			if(($request == "31-60" || $request == 'all') && $age_date[1] == $value){
				$last_month_carbon = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(60)));
				$current_month = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(31)));
				if($claim_by  == 'create_by')
					$unbilled = $claims->where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month);
				else
				{	
					$last_month_carbon = date('Y-m-d',strtotime(Carbon::now()->subDay(60)));
					$current_month = date('Y-m-d',strtotime(Carbon::now()->subDay(31)));
					$unbilled = $claims->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
				}	
				
			$billed[$value] =$unbilled->get();
			}
			if(($request == "61-90" || $request == 'all') && $age_date[2] == $value){
				$last_month_carbon = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(90)));
				$current_month = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(61)));
				if($claim_by  == 'create_by')
					$unbilled = $claims->where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month);
				else
				{
					$last_month_carbon = date('Y-m-d',strtotime(Carbon::now()->subDay(90)));
					$current_month = date('Y-m-d',strtotime(Carbon::now()->subDay(61)));
					$unbilled = $claims->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
				}	
				$billed[$value] =$unbilled->get();
			}
			if(($request == "91-120" || $request == 'all') && $age_date[3] == $value){
				$last_month_carbon = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(120)));
				$current_month = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(91)));
				if($claim_by  == 'create_by')
					$unbilled = $claims->where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month);
				else	{
					$last_month_carbon = date('Y-m-d',strtotime(Carbon::now()->subDay(120)));
					$current_month = date('Y-m-d',strtotime(Carbon::now()->subDay(91)));
					$unbilled = $claims->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
				}	
				$billed[$value] =$unbilled->get();
			}
			if(($request == "121-150" || $request == 'all') && $age_date[4] == $value){
				$last_month_carbon = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(150)));
				$current_month = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(121)));
				if($claim_by  == 'create_by')
					$unbilled = $claims->where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month);
				else{	
					$last_month_carbon = date('Y-m-d',strtotime(Carbon::now()->subDay(150)));
					$current_month = date('Y-m-d',strtotime(Carbon::now()->subDay(121)));
					$unbilled = $claims->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
				}//$unbilled = $claims->orWhere('created_at','>=',$last_month_carbon)->where('created_at','<=',$current_month);
			$billed[$value] =$unbilled->get();
			}
			if(($request == "150-above" || $request == 'all') && $age_date[5] == $value){
				
				$current_month = date('Y-m-d h:i:s',strtotime(Carbon::now()->subDay(151)));
				if($claim_by  == 'create_by')
					$unbilled = $claims->where('claim_submit_count','=',0)->where('created_at','<=',$current_month);
				else
				{
					$current_month = date('Y-m-d',strtotime(Carbon::now()->subDay(151)));
					$unbilled = $claims->where('claim_submit_count','=',0)->where('date_of_service','<=',$current_month);
				}	
				$billed[$value] =$unbilled->get();
			}
				
		}
		$res['unbilled'] = $billed;
		return $res;
		}
	/*** Aging wise calculate function start here ***/
	public function AgingCalc($type,$type_field,$current_id,$age_date)
	{		
		$result_value	= $result_arr	= $total_arr	= $claims_count	= $claims_amt	= [];
		foreach($age_date  as $key =>$value)
		{
			### Patient[Responsibilty] wise get record ###
			if($type =="patient")
				$claim_arr	= 	ClaimInfoV1::whereIn('status',['Patient','Paid'])->where('self_pay','Yes')->where('patient_paid',"!=",'0.00')->where('patient_due',"!=",'0.00');
			
			 ### Insurance[Responsibilty] wise get record ###
			if($type =="insurance")
				$claim_arr	= 	ClaimInfoV1::where('status','Submitted')->where('self_pay','No')->where('insurance_due',"!=",'0.00')->where('claim_submit_count',"!=",'0');
			### Provider, Facility, Insurance wise individual record ###
			if($type =="provider")
				$claim_arr	=	ClaimInfoV1::where($type_field,$current_id)->where(function($query) { return $query->where('patient_paid',"!=",'0.00')->orWhere('insurance_paid',"!=",'0.00');})->where(function($query) { return $query->where('patient_due',"!=",'0.00')->orWhere('insurance_due',"!=",'0.00');})->where('claim_submit_count',"!=",'0');
		
			$date_key 	= 	explode("-",$value);
			$start_date = 	date('Y-m-d h:i:s', strtotime('-'.$date_key[0].' day'));
			$end_date   = 	($date_key[1] =="above") ? 'above':date('Y-m-d h:i:s', strtotime('-'.$date_key[1].' day'));
			if($end_date =="above")
			{ 
				$claim_arr->where('created_at',"<=", $start_date);	
			}
			else
			{
				$claim_arr->where('created_at',"<=",$start_date)->where('created_at',">=",$end_date);	
			}
			$result['claims'] 	= 	(int)$claim_arr->count();
			$result['value'] 	= 	(int)$claim_arr->sum('total_charge');
			$claims_count[$key] = 	$result['claims'];
			$claims_amt[$key]  	= 	$result['value'];
			$total_arr[$value] 	= 	$result;
		}
		$result_value["aging"] = $total_arr;
		$result_value["claims"]= array_sum($claims_count);
		$result_value["value"] = array_sum($claims_amt);
		return $result_value;
	}
	/*** Aging wise calculate function start here ***/
	/*** Percentage function start here ***/
	public function SumAndPercentageCalc($array_list)
	{
		/*** Add Two array values start ***/
		$get_combined_value = $this->SumMultiArrayList($array_list);
		$result['total'] 	= $get_combined_value;
		$result['total'][0] = "Total AR";
		/*** Add Two array values end ***/
		
		/*** Get percentage of total values start ***/
		foreach($get_combined_value  as $key =>$value)
		{	
			$total_value 	= $result['total'][count($result['total'])-1];
			$percentage 	= 0;
			if($key !=0 && $key % 2 == 0)
			{
				if($total_value >0 && $value >0)
					$percentage 	=  round(($value/$total_value)*100,2);
				$percentage_array[$key]	= $percentage."%";
			}
			else
				$percentage_array[$key]	= '';
		}
		$result['total_percentage'] 	= $percentage_array;
		$result['total_percentage'][0] 	= "Total AR %";
		/*** Get percentage of total values start ***/
		return $result;
	}
	/*** Percentage function end here ***/
	
	/*** Adding multi dimentional array function start here ***/
	public function SumMultiArrayList($array)
	{
		$new_array = $added_array = $a = array();
		foreach($array as $value) { if(count($value)>0)$new_array[] = $value; }
		foreach($new_array  as $key =>$values_arr)
		{	
			foreach($values_arr  as $key_arr =>$values)
			{	
				
				$value = ($key ==0) ? $new_array[$key][$key_arr] : $new_array[$key][$key_arr]+$a[$key-1][$key_arr];
				$a[$key][] = (string)$value;
			}
			$added_array = $a[$key];
		}
		return $added_array;
	}
	/*** Adding multi dimentional array function end here ***/
	
	############ Aging Report End ############
	public function getChargeList()
	{
		$insurance = Insurance::where('status','Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
		$facilities = Facility::getAllfacilities();
		$ClaimController  = new ClaimControllerV1();
		$search_fields_data = $ClaimController->generateSearchPageLoad('charge_listing_report');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
		return Response::json(array('status' => 'success', 'message' => null, 'data' =>compact('insurance','facilities','search_fields','searchUserData')));
	}
	public function getChargesSearchApi() {
        $request = Request::all();
        $orderField = $orderByField = 'claim_info_v1.id';
        $orderByDir = 'DESC';
        if(isset($request['export']))
            $orderByDir = 'DESC';
		
		$practice_timezone = Helpers::getPracticeTimeZone();
		
		/* $claim_qryV2 = ClaimInfoV1::where('claim_info_v1.id', '<>', 0)->select('claim_info_v1.id')
			 ->leftjoin('pmt_claim_tx_v1', 'pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id'); */ 
		
		if(isset($request['transaction_date'])) 
		{
			$date = explode('-',$request['transaction_date']);
			$from = date("Y-m-d", strtotime($date[0]));
			if($from == '1970-01-01'){
				$from = '0000-00-00';
			}
			
			$to = date("Y-m-d", strtotime($date[1]));
			
			/* $claim_qryV2->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'")->orWhereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'"); */
		}else{
			$from = date("Y-m-d", strtotime('2017-01-01'));
			$to = date("Y-m-d");
		}
		
		/* if (isset($request['facility'])) {
        	if(is_array($request['facility']))
        		$facility_id = $request['facility'];
        	else
        		$facility_id = explode(',',$request['facility']);
			$claim_qryV2->whereIn('claim_info_v1.facility_id', $facility_id);
			
		}
        
        if (isset($request['rendering'])){
        	if(is_array($request['rendering']))
        		$rendering = $request['rendering'];
        	else
        		$rendering = explode(',',$request['rendering']);
			$claim_qryV2->whereIn('claim_info_v1.rendering_provider_id', $rendering);
			
		} */
		
		/* $claimIds = $claim_qryV2->get()->pluck('id')->toArray(); */
        
        $claim_qry = ClaimInfoV1::whereIN('claim_info_v1.id', function($query) use($practice_timezone,$from,$to){
						$query->select('claim_info_v1.id')->from('claim_info_v1')->leftjoin('pmt_claim_tx_v1', 'pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id')->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'")->orWhereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'");
					})
                ->join('patients','patients.id', '=', 'claim_info_v1.patient_id')
                ->join('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->leftjoin('insurances', 'insurances.id', '=', 'claim_info_v1.insurance_id')
                ->leftjoin('facilities', 'facilities.id', '=', 'claim_info_v1.facility_id')
                ->leftjoin('facilityaddresses', 'facilityaddresses.facilityid', '=', 'facilities.id')
                ->leftjoin('providers as rendering_provider', 'rendering_provider.id', '=', 'claim_info_v1.rendering_provider_id')
                ->leftjoin('providers as billing_provider', 'billing_provider.id', '=', 'claim_info_v1.billing_provider_id')
                ->leftjoin('insurancetypes', 'insurancetypes.id', '=', 'insurances.insurancetype_id')
                ->leftJoin('claim_sub_status', 'claim_sub_status.id', '=', 'claim_info_v1.sub_status_id');
				if(isset($request['transaction_date']) && $request['transaction_date'] == 'No'){
						$claim_qry->leftJoin(DB::raw("(SELECT      
						  claim_id,created_at,     
						  sum(total_paid) as clamPaid,
						  sum( case when pmt_method = 'Insurance' then total_writeoff else 0 end ) as clamTotalWithheld,
						  sum( case when pmt_method = 'Insurance' then total_withheld else 0 end ) as InsuranceAdj,
						  sum( case when pmt_method = 'Patient' then total_writeoff else 0 end ) as PatientAdj
						  FROM pmt_claim_tx_v1
						  WHERE pmt_claim_tx_v1.deleted_at IS NULL AND pmt_claim_tx_v1.pmt_type != 'Refund'
						  AND DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' AND DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'
						  GROUP BY pmt_claim_tx_v1.claim_id
						  ) as pmt_claim_tx_v1"), function($join){
							$join->on('pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id');
						});
				}else{
					$claim_qry->leftJoin(DB::raw("(SELECT      
						  claim_id,created_at,     
						  sum(total_paid) as clamPaid,
						  sum( case when pmt_method = 'Insurance' then total_writeoff else 0 end ) as clamTotalWithheld,
						  sum( case when pmt_method = 'Insurance' then total_withheld else 0 end ) as InsuranceAdj,
						  sum( case when pmt_method = 'Patient' then total_writeoff else 0 end ) as PatientAdj
						  FROM pmt_claim_tx_v1
						  WHERE pmt_claim_tx_v1.deleted_at IS NULL
						  AND DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' AND DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'
						  GROUP BY pmt_claim_tx_v1.claim_id
						  ) as pmt_claim_tx_v1"), function($join){
							$join->on('pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id');
						});
				}
			
        if (isset($request['patient_id']) && $request['patient_id'] != '') {
            $claim_qry->where('claim_info_v1.patient_id', $request['patient_id']);
        }
       
        /*if (isset($request['status']) && $request['status'] != '' && $request['status'] != 'All') {
            if(is_array($request['status']))
        		$status = $request['status'];
        	else
        		$status = explode(',',$request['status']);              
            $claim_qry->whereIn('claim_info_v1.status', $status);
        }*/
        $orderByField = 'claim_info_v1.id';
        if(!empty($request)){
            $results = $this->searchFilterApi($claim_qry, $request);
            $claim_qry = $results['claim_query'];
            $search_by = $results['search_by'];
        }

        $result['count'] = $claim_qry->distinct('claim_info_v1.id')->count();

        $claim_qry->selectRaw("claim_info_v1.id,
            claim_info_v1.id as claim_id,
            claim_info_v1.created_at as created_at,
            pmt_claim_tx_v1.clamPaid,
            pmt_claim_tx_v1.clamTotalWithheld,
            pmt_claim_tx_v1.InsuranceAdj,
            pmt_claim_tx_v1.PatientAdj,
            claim_info_v1.claim_number,
            claim_info_v1.date_of_service,
            claim_info_v1.insurance_id,
            claim_info_v1.total_charge,
            claim_info_v1.status,
            claim_info_v1.claim_submit_count,
            claim_info_v1.charge_add_type,
            claim_info_v1.rendering_provider_id,
            claim_info_v1.refering_provider_id,
            claim_info_v1.billing_provider_id,
            claim_info_v1.facility_id,
            claim_info_v1.insurance_id,
            insurancetypes.type_name as insurance_type_name,
            claim_info_v1.icd_codes,
            patients.id as patient_id,
            patients.account_no,
            patients.first_name,
            patients.last_name,
            patients.middle_name,
            patients.title,
            patients.dob,
            patients.gender,
            patients.address1,
            patients.city,
            patients.state,
            patients.zip5,
            patients.zip4,
            patients.is_self_pay,
            patients.phone,
            patients.mobile,
            
            facilities.facility_name,
            facilities.short_name as facility_short_name,
            facilityaddresses.address1 as facility_address1,
            facilityaddresses.city as facility_city,
            facilityaddresses.state as facility_state,
            facilityaddresses.pay_zip5 as facility_pay_zip5,
            facilityaddresses.pay_zip4 as facility_pay_zip4,

            billing_provider.short_name as billing_short_name,
            billing_provider.provider_name as billing_full_name,
            billing_provider.provider_dob as billing_dob,
            billing_provider.gender as billing_gender,
            billing_provider.etin_type as billing_etin_type,
            billing_provider.etin_type_number as billing_etin_no,
            billing_provider.npi as billing_npi,

            rendering_provider.short_name as rendering_short_name,
            rendering_provider.provider_name as rendering_full_name,
            rendering_provider.provider_dob as rendering_dob,
            rendering_provider.gender as rendering_gender,            
            rendering_provider.etin_type as rendering_etin_type,
            rendering_provider.etin_type_number as rendering_etin_no,
            rendering_provider.npi as rendering_npi,
             
            pmt_claim_fin_v1.insurance_due,
            pmt_claim_fin_v1.patient_due,
            pmt_claim_fin_v1.patient_adj,
            pmt_claim_fin_v1.insurance_adj,
            pmt_claim_fin_v1.insurance_paid,
            pmt_claim_fin_v1.patient_paid,
            pmt_claim_fin_v1.withheld,
            claim_sub_status.sub_status_desc,
            (claim_info_v1.total_charge-(pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.withheld)) as balance_amt,
            (pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.withheld) as totalAdjustment,
            (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid) as total_paid,
            IF(claim_info_v1.claim_submit_count > 0,'false','true') as unbilled
            ");

        
        if($orderByField == 'claim_info_v1.date_of_service')
            $claim_qry->orderBy($orderByField, $orderByDir)->orderBy('claim_info_v1.id', $orderByDir);
        else
            $claim_qry->orderBy($orderByField, $orderByDir);
        // End of Revision
        if (isset($request['export'])) {
        	$claim_lists = $claim_qry->get();
        	return Response::json(array('status' => 'success', 'data' => compact('claim_lists','search_by')));
        } else {
        	$paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $claim_qry = $claim_qry->paginate($paginate_count);
            // Get export result
            $ref_array = $claim_qry->toArray();

            $pagination_prt = $claim_qry->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
                                        <li class="disabled"><span>&laquo;</span></li>
                                        <li class="active"><span>1</span></li>
                                        <li><a class="disabled" rel="next">&raquo;</a>
                                        </li>
                                    </ul>';
            $pagination = array('total' => $ref_array['total'], 'per_page' => $ref_array['per_page'], 'current_page' => $ref_array['current_page'], 'last_page' => $ref_array['last_page'], 'from' => $ref_array['from'], 'to' => $ref_array['to'], 'pagination_prt' => $pagination_prt);
            $claim_qry = json_decode($claim_qry->toJson());
            $claim_lists = $claim_qry->data;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claim_lists','pagination','pagination_prt','search_by')));
        }
    }
	public function searchFilterApi($claim_query, $request = []){   
        $practice_timezone = Helpers::getPracticeTimeZone();
		
		
		$claim_query->join(DB::raw("(SELECT      
          claim_id,     
          total_charge,patient_due, insurance_due,
          sum(total_charge)-(sum(patient_paid)+sum(insurance_paid) + sum(withheld) + sum(patient_adj) + sum(insurance_adj)) as tot_ar,
          SUM(pmt_claim_fin_v1.insurance_due) as total_ins_due,
          SUM(pmt_claim_fin_v1.patient_due) as total_pat_due,
          (pmt_claim_fin_v1.patient_due+pmt_claim_fin_v1.insurance_due) as total_due,
          (pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.insurance_paid) as tot_paid
          FROM pmt_claim_fin_v1
          WHERE pmt_claim_fin_v1.deleted_at IS NULL
          GROUP BY pmt_claim_fin_v1.claim_id
          ) as fin"), function($join) {
            $join->on('fin.claim_id', '=', 'claim_info_v1.id');
        })->selectRaw('fin.tot_ar,
            fin.total_ins_due,
            fin.total_pat_due,
            fin.total_due,
            fin.tot_paid
            ');
        $search_by = [];
        if (isset($request['claim_number'])){
            $claim_query->where('claim_info_v1.claim_number', 'LIKE', '%' . $request['claim_number'] . '%');
        	$search_by['Claim Number'] = $request['claim_number'];
        }
        if (isset($request['acc_no'])){
            $claim_query->where('patients.account_no', 'LIKE', '%'. $request['acc_no'] .'%');
            $search_by['Acc No'] = $request['acc_no'];
        }
		
        if (isset($request['patient_name'])) {
			$dynamic_name = $search = trim(($request['patient_name']));
            $claim_query->Where(function ($query) use ($dynamic_name) {
                $query = $query->orWhere(DB::raw('CONCAT(patients.last_name,", ", patients.first_name)'),  'like', "%{$dynamic_name}%" );
            });
            $search_by['Patient Name'] = $dynamic_name ;

		}

		if(isset($request['date_of_service'])) {
			$date = explode('-',($request['date_of_service']));
            $from = date("Y-m-d", strtotime($date[0]));
			if($from == '1970-01-01'){
				$from = '0000-00-00';
			}
            $to = date("Y-m-d", strtotime($date[1]));
			$search_by['DOS'] = date("m/d/y", strtotime(@$from)) . ' to ' . date("m/d/y", strtotime(@$to));
            $claim_query->whereBetween('claim_info_v1.date_of_service', [ $from,  $to]);
        }

		 if(isset($request['transaction_date'])) {
			 $date = explode('-',$request['transaction_date']);
            $from = date("Y-m-d", strtotime($date[0]));
			if($from == '1970-01-01'){
				$from = '0000-00-00';
			}
            $to = date("Y-m-d", strtotime($date[1])); 
			$search_by['Transaction Date'] =  date("m/d/y", strtotime(@$from)) . ' to ' . date("m/d/y", strtotime(@$to));
            /* $claim_query->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'"); */
        } 

        if (isset($request['facility'])) {
        	if(is_array($request['facility']))
        		$facility_id = $request['facility'];
        	else
        		$facility_id = explode(',',$request['facility']);
			$claim_query->whereIn('claim_info_v1.facility_id', $facility_id);
			$facility = Facility::selectRaw("GROUP_CONCAT(`facility_name` SEPARATOR ',  ') as short_name")->whereIn('id', $facility_id)->get()->toArray();
            $search_by['Facility'] = @array_flatten($facility)[0];
		}
        
        if (isset($request['rendering'])){
        	if(is_array($request['rendering']))
        		$rendering = $request['rendering'];
        	else
        		$rendering = explode(',',$request['rendering']);
			$claim_query->whereIn('claim_info_v1.rendering_provider_id', $rendering);
			$provider = Provider::selectRaw("GROUP_CONCAT(`provider_name` SEPARATOR ',  ') as short_name")->whereIn('id', $rendering)->get()->toArray();
            $search_by['Rendering Provider'] = @array_flatten($provider)[0];
		}

		if (isset($request['refering'])){
			if(is_array($request['refering']))
        		$refering = $request['refering'];
        	else
        		$refering = explode(',',$request['refering']);
			$claim_query->whereIn('claim_info_v1.refering_provider_id', $refering);
			$provider = Provider::selectRaw("GROUP_CONCAT(`provider_name` SEPARATOR ',  ') as short_name")->whereIn('id', $refering)->get()->toArray();
            $search_by['Referring Provider'] = @array_flatten($provider)[0];
		}
        if (isset($request['billing'])){
        	if(is_array($request['billing']))
        		$billing = $request['billing'];
        	else
        		$billing = explode(',',$request['billing']);
			$claim_query->whereIn('claim_info_v1.billing_provider_id', $billing);
			$provider = Provider::selectRaw("GROUP_CONCAT(`provider_name` SEPARATOR ',  ') as short_name")->whereIn('id', $billing)->get()->toArray();
            $search_by['Billing Provider'] = @array_flatten($provider)[0];
		}
       
        if (isset($request['insurance_id'])){	
	        if(is_array($request['insurance_id']))
        		$ins_data = $request['insurance_id'];
        	else
        		$ins_data = explode(',',$request['insurance_id']);	
            $claim_query->whereIn('claim_info_v1.insurance_id', $ins_data);
            $insurance_name = Insurance::selectRaw("GROUP_CONCAT(`short_name` SEPARATOR ',  ') as short_name")->whereIn('id', $ins_data)->get()->toArray();
            $search_by["Insurance"] =  @array_flatten($insurance_name)[0];
        }
        
        if(isset($request['status'])){
        	if(is_array($request['status'])){
        		$statusArr = $request['status'];
	            $search_by["status"] =  implode(',', $statusArr);
        	} else{
        		$search_by["status"] = $request['status'];
        		$statusArr = explode(',',$request['status']);
        	}
            if(in_array("All", $statusArr)) {
                $claim_query->whereIn('claim_info_v1.status', ['Hold','Pending','Ready','Patient','Submitted','Paid','Denied','Rejection']);                
            }else{
                $claim_query->whereIn('claim_info_v1.status', $statusArr);
            }
            if(in_array("Hold", $statusArr)) {
                if(isset($request['hold_reason'])) {
                	if(is_array($request['hold_reason']))
		        		$holdReasonArr = $request['hold_reason'];
		        	else
		        		$holdReasonArr = explode(',',$request['hold_reason']);
					
					
                    $claim_query->whereIn('claim_info_v1.hold_reason_id', $holdReasonArr);
                    $Holdoption = Holdoption::selectRaw("GROUP_CONCAT(`option` SEPARATOR ',  ') as short_name")->whereIn('id', $holdReasonArr)->get()->toArray();
		            $search_by["Hold Reason"] =  @array_flatten($Holdoption)[0];
                }
            }            
        }

        if (isset($request['status_reason'])) {
        	if(is_array($request['status_reason']))
        		$status_reason = $request['status_reason'];
        	else
        		$status_reason = explode(',',$request['status_reason']);
	        $claim_query->whereIn('claim_info_v1.sub_status_id', $status_reason);
	        $ClaimSubStatus = ClaimSubStatus::selectRaw("GROUP_CONCAT(`sub_status_desc` SEPARATOR ',  ') as short_name")->whereIn('id', $status_reason)->get()->toArray();
            $search_by["Claim Sub Status"] =  @array_flatten($ClaimSubStatus)[0];
        }
        /*if(!isset($request['patient_id']) && isset($request['status_reason']))
        if (count(array_filter(($request['status_reason'])))!=count(($request['status_reason']))) {
            $claim_query->orWhereNull('claim_info_v1.sub_status_id');
        }*/
		if (isset($request['unbilledamt'])) {  
			$search_by["Unbilled"] = $request['unbilledamt'];
            $unbilledamt = ($request['unbilledamt']);
            $unbilledamt_con = '=';
            if (preg_match('/</', $unbilledamt)){
                $exp = explode('<',$unbilledamt);
                $unbilledamt_con = '<=';
                $unbilledamt = $exp[1];
            }
            if (preg_match('/>/', $unbilledamt)){
                $exp = explode('>',$unbilledamt);
                $unbilledamt_con = '>=';
                $unbilledamt = $exp[1];
            }
            if($unbilledamt!==''){
                $claim_query->where('claim_info_v1.total_charge', $unbilledamt_con,$unbilledamt);
            }
            $claim_query->where('claim_info_v1.insurance_id', '!=', 0)->where('claim_info_v1.claim_submit_count', 0);
        }

		if (isset($request['billedamt'])) {
			$search_by["Billed"] = $request['billedamt'];
            $billedamt = ($request['billedamt']);
            $billedamt_con = '=';
            if(preg_match('/</', $billedamt)){
                $exp = explode('<',$billedamt);
                $billedamt_con = '<=';
                $billedamt = $exp[1];
            }
            if(preg_match('/>/', $billedamt)){
                $exp = explode('>',$billedamt);
                $billedamt_con = '>=';
                $billedamt = $exp[1];
            }
            if($billedamt !== '') {
                $claim_query->where('claim_info_v1.total_charge', $billedamt_con,$billedamt);
                $claim_query->where(function($qry){
                    $qry->where(function($query){ 
                        $query->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count','>' ,0);
                    })->orWhere('claim_info_v1.insurance_id',0);
                });
            }
        }

		if (isset($request['paid_amt'])) {
			$search_by["Paid"] = $request['billedamt'];
             $paid_amt = ($request['paid_amt']);
            $paid_amt_con = '=';
            if(preg_match('/</', $paid_amt)){
                $exp = explode('<',$paid_amt);
                $paid_amt_con = '<=';
                $paid_amt = $exp[1];
            }
            if(preg_match('/>/', $paid_amt)){
                $exp = explode('>',$paid_amt);
                $paid_amt_con = '>=';
                $paid_amt = $exp[1];
            }
            if($paid_amt !== '')
                $claim_query->where('tot_paid', $paid_amt_con,$paid_amt);
        }

		if (isset($request['pat_bal'])) {
			$search_by["Patient Balance"] = $request['pat_bal'];
            $pat_amt = ($request['pat_bal']);
            $pat_amt_con = '=';
            if(preg_match('/</', $pat_amt)){
                $exp = explode('<',$pat_amt);
                $pat_amt_con = '<=';
                $pat_amt = $exp[1];
            }
            if(preg_match('/>/', $pat_amt)){
                $exp = explode('>',$pat_amt);
                $pat_amt_con = '>=';
                $pat_amt = $exp[1];
            }
            if($pat_amt !== '')
                $claim_query->where('pmt_claim_fin_v1.patient_due', $pat_amt_con,$pat_amt);
        }

		if (isset($request['ins_bal'])) {
			$search_by["Insurance Balance"] = $request['ins_bal'];
            $ins_amt = ($request['ins_bal']);
            $ins_amt_con = '=';
            if(preg_match('/</', $ins_amt)){
                $exp = explode('<',$ins_amt);
                $ins_amt_con = '<=';
                $ins_amt = $exp[1];
            }
            if(preg_match('/>/', $ins_amt)){
                $exp = explode('>',$ins_amt);
                $ins_amt_con = '>=';
                $ins_amt = $exp[1];
            }
            if($ins_amt !== '')
                $claim_query->where('pmt_claim_fin_v1.insurance_due', $ins_amt_con,$ins_amt);
        }

		if (isset($request['ar_bal'])) {
			$search_by["AR Balance"] = $request['ar_bal'];
            $ar_amt = ($request['ar_bal']);
            $ar_amt_con = '=';
            if(preg_match('/</', $ar_amt)){
                $exp = explode('<',$ar_amt);
                $ar_amt_con = '<=';
                $ar_amt = $exp[1];
            }
            if(preg_match('/>/', $ar_amt)){
                $exp = explode('>',$ar_amt);
                $ar_amt_con = '>=';
                $ar_amt = $exp[1];
            }
            if($ar_amt !== '')
                $claim_query->where('tot_ar', $ar_amt_con,$ar_amt);
        }
        $result['search_by'] = $search_by;
        $result['claim_query'] = $claim_query;
        return $result;
    }
	
	public function getChargesSearchApiV2(){
		$practice_timezone = Helpers::getPracticeTimeZone();
		
		$claim_qry = ClaimInfoV1::where('claim_info_v1.id', '<>', 0)->select('claim_info_v1.created_at','claim_info_v1.id')
			 ->leftjoin('pmt_claim_tx_v1', 'pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id'); 
		
		$date = explode('-','02/01/2021 - 02/28/2021');
		$from = date("Y-m-d", strtotime($date[0]));
		if($from == '1970-01-01'){
			$from = '0000-00-00';
		}
		
		$to = date("Y-m-d", strtotime($date[1]));
		$search_by['Transaction Date'] =  date("m/d/y", strtotime(@$from)) . ' to ' . date("m/d/y", strtotime(@$to));
		
		$claim_qry->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'")->orWhereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'");
		
		$claim_qry = $claim_qry->get()->pluck('id')->toArray();
		
		
		echo "<pre>"; print_r($claim_qry);die;
		
		
	}

}
