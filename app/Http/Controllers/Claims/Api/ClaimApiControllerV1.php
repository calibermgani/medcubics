<?php

namespace App\Http\Controllers\Claims\Api;

use App\Http\Controllers\Controller;
use App\Models\Patients\Patient as Patient;
use App\Models\Insurance as Insurance;
use App\Models\Facility as Facility;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Http\Controllers\Api\EdiApiController as EdiApiController;
use App\Models\Icd as Icd;
use App\Models\Eras as Eras;
use App\Models\Medcubics\ClearingHouse as ClearingHouse;
use App\Models\Claims\EdiTransmission as EdiTransmission;
use App\Models\Claims\TransmissionClaimDetails as TransmissionClaimDetails;
use App\Models\Claims\TransmissionCptDetails as TransmissionCptDetails;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController as ChargeV1ApiController;
use App\Http\Controllers\Charges\ChargeController as ChargeController;
use App\Models\Claims\EdiReport as EdiReport;
use App\Models\Provider as Provider;
use App\Models\Holdoption as Holdoption;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTClaimCPTFINV1;
use App\Models\Payments\ClaimCPTInfoV1 as Claimdoscptdetail;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Payments\ClaimTXDESCV1;
use App\Models\SearchFields as SearchFields;
use App\Models\SearchUserData as SearchUserData;
use Input;
use Carbon\Carbon;
use Auth;
use Response;
use Request;
use Config;
use Lang;
use DB;
use App;
use Redirect;
use Session;
use PDF;
use ZipArchive;
use Log;
use Dompdf\Dompdf;

class ClaimApiControllerV1 extends EdiApiController {

    public $error_msg = [];
    public $clearing_house = [];
    public $s = 1;

    #======================================================================================#
    #				 This function used to showing the claim Dashboard   			   	   #
    #				 Claim Dashboard Start Point                                           # 
    #======================================================================================#

    public function getIndexApi($type = '', $export = '') {
		
		/* Stats Tab Value Getting Here */
		$dataArr['era_count']  			=  Eras::count();
		$dataArr['paper_count']  		=  ClaimInfoV1::whereNull('deleted_at')->where('claim_type','paper')->where('status','Ready')->where('no_of_issues',0)->where('error_message','')->count(); 
		$dataArr['counts'] 	=     ClaimInfoV1::select('status', DB::raw('count(*) as total'))
									->where('status', '<>', '')
									->whereNull('deleted_at')
									->groupBy('status')
									->pluck('total', 'status')->all();
		$dataArr['counts']['claimEdits'] = ClaimInfoV1::where('status','Ready')->whereNull('deleted_at')->where('no_of_issues','!=',0)->where('error_message','!=','')->count();
		$dataArr['counts']['electronicClaims'] = ClaimInfoV1::where('claim_type','electronic')->whereNull('deleted_at')->where('status','Ready')->where('no_of_issues',0)->where('error_message','')->count();
             
		/* Stats Tab Value Getting Here */
		
		/* Daily Edi Status Getting Here */
		
		//$dataArr['daily_submitted'] = ClaimTXDESCV1::whereIn('transaction_type',['Submitted','Resubmitted'])->whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->count();
		//$dataArr['daily_accepted'] = ClaimTXDESCV1::whereIn('transaction_type',['Payer Accepted','Clearing House Accepted'])->whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->count();
		//$dataArr['daily_rejected'] = ClaimTXDESCV1::whereIn('transaction_type',['Payer Rejected','Clearing House Rejection'])->whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->count();
		
		/* Daily Edi Status Getting Here */ 
		
		
		
		/* Graph for submitted and rejected */
		
		
		$dateS = Carbon::now()->startOfMonth()->subMonth(6);
		$dateS = $dateS->toDateString();
		$dateE = $today = Carbon::today();
		$dateE = $dateE->toDateString();
		$submitted_lable = '';
		$rejected_lable = '';
		$monthLable = '';
		
		$submitted = DB::table('claim_info_v1')->join('claim_tx_desc_v1','claim_info_v1.id','=','claim_tx_desc_v1.claim_id')->whereRaw("DATE(claim_tx_desc_v1.created_at) >= '$dateS' and DATE(claim_tx_desc_v1.created_at) <= '$dateE'")->whereIn('transaction_type',['Submitted','Submitted Paper'])->select(DB::raw('sum(claim_info_v1.total_charge) as total_charge'),DB::raw('count(*) as total_count'),DB::raw("DATE_FORMAT(claim_tx_desc_v1.created_at,'%M') as monthNum"))->groupBy('monthNum')->get('total_charge','total_count');
		$submitted = json_decode(json_encode($submitted), true); 
		
		$rejected = DB::table('claim_info_v1')->join('claim_tx_desc_v1','claim_info_v1.id','=','claim_tx_desc_v1.claim_id')->whereRaw("DATE(claim_tx_desc_v1.created_at) >= '$dateS' and DATE(claim_tx_desc_v1.created_at) <= '$dateE'")->whereIn('transaction_type',['Payer Rejected','Clearing House Rejection'])->select(DB::raw('sum(claim_info_v1.total_charge) as total_charge'),DB::raw('count(*) as total_count'),DB::raw("DATE_FORMAT(claim_tx_desc_v1.created_at,'%M') as monthNum"))->groupBy('monthNum')->get('total_charge','total_count');
		
		$rejected = json_decode(json_encode($rejected), true); 
		
		$today = date('M-y');
		for ($i = 1; $i < 12; $i++) {
			  $date_array[$i] = date('F', strtotime("-$i month", strtotime($today)));
			  $date_label[$i] = date("M-y", strtotime("-$i month", strtotime($today)));
		}
		
		$data_final_date = $date_labelnew[count($date_label)+1]= date("M-y");
		array_unshift($date_array, date("F"));  
		array_unshift($date_label, $data_final_date);               
		$date_array = array_reverse($date_array);               
		$date_label = array_reverse($date_label);                    
		$Charge_val = [];           
		for($i = 0; $i<count($date_array); $i++){  
			 $submitted_val = array_search($date_array[$i], array_column($submitted, 'monthNum')); 
			 $rejected_val = array_search($date_array[$i], array_column($rejected, 'monthNum')); 
			if($submitted_val !== false) {
				$submitted_lable .= '{"value": "'.$submitted[$submitted_val]['total_count'].'","toolText":"$ '.$submitted[$submitted_val]['total_charge'].', Count:'.$submitted[$submitted_val]['total_count'].'"},';
			}else{
				$submitted_lable .= '{"value": "0.00","toolText":"$ 0.00, Count: 0"},';
			}
			if($rejected_val !== false) {
				$rejected_lable .= '{"value": "'.$rejected[$rejected_val]['total_count'].'","toolText":"$ '.$rejected[$rejected_val]['total_charge'].', Count:'.$rejected[$rejected_val]['total_count'].'"},';
			}else{
				$rejected_lable .= '{"value": "0.00","toolText":"$ 0.00, Count: 0"},';
			}
		} 
		
		foreach($date_label as $date_list){
			$monthLable .= '{"label":"'. $date_list .'"},'; 
		}

		$dataArr['month'] = rtrim($monthLable,',');
		$dataArr['submitted_lable'] = rtrim(str_replace("_"," ",$submitted_lable),',');
		$dataArr['rejected_lable'] = rtrim(str_replace("_"," ",$rejected_lable),',');
	
		
		/* 267 / 277 Response Data Start */
		$response267_277 = DB::table('claim_info_v1')->join('claim_tx_desc_v1','claim_info_v1.id','=','claim_tx_desc_v1.claim_id')->leftjoin('insurances','insurances.id','=','claim_info_v1.insurance_id')->whereIn('claim_tx_desc_v1.transaction_type',['Submitted','Resubmitted','Payer Rejected','Payer Accepted','Clearing House Rejection','Clearing House Accepted'])->where('insurance_id','!=',0)->where('claim_tx_desc_v1.deleted_at', null)->select('claim_info_v1.id','insurances.short_name','claim_info_v1.insurance_id','claim_tx_desc_v1.transaction_type')->get();
		//print_r($response267_277);
		$responseArr = [];
		foreach($response267_277 as $list){
			$responseArr[$list->insurance_id][str_replace(' ','_',$list->transaction_type)] = $list->transaction_type;
			$responseArr[$list->insurance_id]['insurance_shortname'] = $list->short_name;
			$responseArr[$list->insurance_id][str_replace(' ','_',$list->transaction_type).'_count'] = @$responseArr[$list->insurance_id][str_replace(' ','_',$list->transaction_type).'_count'] + 1 ;
		}
		/* 267 / 277 Response Data End */
		//dd($responseArr);
		
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('dataArr','responseArr')));
    }

	public function getClaimsDataApi($type){
		
		$start 	= isset($request['start']) ? $request['start'] : 0;
        $len 	= (isset($request['length'])) ? $request['length'] : 50;
		$claim_qry = ClaimInfoV1::where('claim_info_v1.id', '<>', 0)->whereNull('claim_info_v1.deleted_at')->select('claim_info_v1.*','claim_edi_info_v1.rejected_date','claim_edi_info_v1.response_file_path','claim_edi_info_v1.denial_codes');

        $claim_qry->leftjoin('patients', function($join) {
            $join->on('patients.id', '=', 'claim_info_v1.patient_id');
        });

        $claim_qry->leftjoin('insurances', function($join) {
            $join->on('insurances.id', '=', 'claim_info_v1.insurance_id');
        });

        $claim_qry->leftjoin('facilities', function($join) {
            $join->on('facilities.id', '=', 'claim_info_v1.facility_id');
        });
        
       /*  $claim_qry->leftjoin('claim_cpt_info_v1', function($join) {
            $join->on('claim_cpt_info_v1.claim_id', '=', 'claim_info_v1.id');
        }); */
			
		$claim_qry->leftjoin('claim_edi_info_v1', function($join) {
			$join->on('claim_edi_info_v1.claim_id', '=', 'claim_info_v1.id');
		});
		
		
		if($type == 'electronic')
			$claim_qry->where('claim_info_v1.claim_type', $type)->whereIn('claim_info_v1.status', ['Ready'])->whereIn('claim_info_v1.self_pay', ['No'])->where('no_of_issues',0)->where('error_message','');
		elseif($type == 'paper')
			$claim_qry->where('claim_info_v1.claim_type', $type)->whereIn('claim_info_v1.status', ['Ready'])->where('no_of_issues',0)->where('error_message','');
		elseif($type == 'error')
			$claim_qry->whereIn('claim_info_v1.status', ['Ready'])->where('no_of_issues','>',0)->where('error_message','!=','');
		elseif($type == 'submitted')
			$claim_qry->where('claim_info_v1.status', $type);
		elseif ($type == 'rejected')
            $claim_qry->where('claim_info_v1.status', 'Rejection');
			
		$claim_qry->with('claimediinfo','patient', 'facility_detail', 'insurance_details', 'billing_provider')
                ->with(array('rendering_provider'));
		$pagination_count = $claim_qry->count(DB::raw('DISTINCT(claim_info_v1.id)'));
		$claim_qry->groupBy('claim_info_v1.id');
		$claim_qry->skip($start)->take($len);
		$claims = $claim_qry->get();
		return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims','pagination_count')));
	}
	
	public function getClaimsDataSearchApi($type,$export = ''){
		$request = Request::all();
		$request['type'] = $type;
		
		$start 	= isset($request['start']) ? $request['start'] : 0;
        $len 	= (isset($request['length'])) ? $request['length'] : 50;
		$search = (!empty($request['search']['value'])) ? trim($request['search']['value']) : "";
		$type = $request['type'];
		$practice_timezone = Helpers::getPracticeTimeZone();  
		$claim_qry = ClaimInfoV1::where('claim_info_v1.id', '<>', 0)->whereNull('claim_info_v1.deleted_at')->select('claim_info_v1.*', 'claim_edi_info_v1.rejected_date','claim_edi_info_v1.response_file_path','claim_edi_info_v1.denial_codes',DB::raw('CONVERT_TZ(claim_info_v1.created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(claim_info_v1.filed_date,"UTC","'.$practice_timezone.'") as filed_date'),DB::raw('sum(pmt_claim_fin_v1.total_charge - (pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.withheld)) as arbal'));
		
		$orderByField = 'claim_info_v1.updated_at';
        $orderByDir = 'DESC';
      	 /* Converting value to default search based */
        if(isset($request['export']) && $request['export'] == 'yes'){
            $orderByField = 'claim_info_v1.id';
            $orderByDir = 'DESC';
            foreach($request as $key=>$value){
                if(strpos($value, ',') !== false && $key != 'patient_name'){
                    $request['dataArr']['data'][$key] = json_encode(explode(',',$value));
                }else{
                    $request['dataArr']['data'][$key] = json_encode($value);    
                }
            }
        }
        
        if (!empty($request['order'])) {
            $orderByField = ($request['order'][0]['column'] != "") ? $request['order'][0]['column'] : $orderByField;

            switch (true) {
                case ($orderByField == '0' && $request['type'] == 'rejected'):
                    $orderByField = 'claim_number';
                    break;

                case ($orderByField == '1' && $request['type'] != 'rejected'):
                    $orderByField = 'claim_number';
                    break;

                case ($orderByField == '1' && $request['type'] == 'rejected'):
                    $orderByField = 'date_of_service';
                    break;                    

                case ($orderByField == '2' && $request['type'] != 'rejected'):
                    $orderByField = 'date_of_service';
                    break;

                case ($orderByField == '2' && $request['type'] == 'rejected'):
                    $orderByField = 'patients.account_no';
                    break;                    

                case ($orderByField == '3'):
                    $orderByField = 'patients.last_name';                   
                    break;

                case ($orderByField == '4' && $request['type'] != 'rejected'):
                    $orderByField = 'insurances.short_name';                
                    break;

                case ($orderByField == '4' && $request['type'] == 'rejected'):
                    $orderByField = 'claim_info_v1.total_charge';                
                    break;                    

                case ($orderByField == '5' && $request['type'] != 'rejected'):
                    $orderByField = 'claim_info_v1.insurance_category';                   
                    break;
                    
                case ($orderByField == '5' && $request['type'] == 'rejected'):
                    $orderByField = 'insurances.short_name';                   
                    break;                    

                case ($orderByField == '6'):
                    $orderByField = 'insurances.payerid';                   
                    break;

                case ($orderByField == '7' && $request['type'] != 'rejected'):
                    $orderByField = 'rend_provider.short_name';                   
                    break;

                case ($orderByField == '7' && $request['type'] == 'rejected'):
                    $orderByField = 'claim_info_v1.submited_date';                   
                    break;                    

                case ($orderByField == '8' && $request['type'] != 'rejected'):
                    $orderByField = 'providers.short_name';                         
                    break;

                case ($orderByField == '8' && $request['type'] == 'rejected'):
                    $orderByField = 'claim_edi_info_v1.rejected_date';                     
                    break;                    

                case ($orderByField == '9'):
                    $orderByField = 'facilities.short_name';                
                    break;

                case ($orderByField == '10'):
                    $orderByField = 'claim_info_v1.total_charge';                    
                    break;

                case ($orderByField == '11'):
                    $orderByField = 'claim_info_v1.created_at';                 
                    break;
                
                case ($orderByField == '12' && $request['type'] == 'submitted') :
                    $orderByField = 'claim_info_v1.submited_date';                 
                    break;

                case ($orderByField == '12' && $request['type'] == 'electronic') :
                    $orderByField = 'claim_info_v1.filed_date';                 
                    break;

                case ($orderByField == '12' && $request['type'] == 'error') :
                    $orderByField = 'claim_info_v1.filed_date';                 
                    break;

                case ($orderByField == '12' && $request['type'] == 'paper') :
                    $orderByField = 'claim_info_v1.filed_date';                 
                    break;                    

                case ($orderByField == '13' && $request['type'] == 'error') :
                    $orderByField = 'claim_info_v1.status';                 
                    break;                                        

                default:
                    $orderByField = 'claim_info_v1.updated_at';
                    break;
            }
            $orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'DESC';
        }
		
		
        $claim_qry->join('patients', function($join) {
            $join->on('patients.id', '=', 'claim_info_v1.patient_id');
        });
		
		$claim_qry->join('pmt_claim_fin_v1', function($join) {
            $join->on('pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id');
        });

        $claim_qry->leftjoin('insurances', function($join) {
            $join->on('insurances.id', '=', 'claim_info_v1.insurance_id');
        });

        $claim_qry->leftjoin('providers', function($join) {
            $join->on('providers.id', '=', 'claim_info_v1.billing_provider_id');
        });

        $claim_qry->leftjoin('providers as rend_provider', function($join) {
            $join->on('rend_provider.id', '=', 'claim_info_v1.rendering_provider_id');
        });

        $claim_qry->leftjoin('facilities', function($join) {
            $join->on('facilities.id', '=', 'claim_info_v1.facility_id');
        }); 
		
		/*	
		$claim_qry->leftjoin('claim_edi_info_v1', function($join) {
			$join->on('claim_edi_info_v1.claim_id', '=', 'claim_info_v1.id');
		});
		*/
		
		// Claim edi info joined using last record
		// Rev. 1 - 06-08-2019 - Ravi

		$claim_qry->leftjoin(DB::raw("(SELECT
			claim_edi_info_v1.claim_id,
			CONVERT_TZ(claim_edi_info_v1.rejected_date,'UTC','".$practice_timezone."') as rejected_date,	
			claim_edi_info_v1.response_file_path,
			claim_edi_info_v1.denial_codes,
			claim_edi_info_v1.id
          FROM claim_edi_info_v1
          WHERE claim_edi_info_v1.deleted_at IS NULL
          AND claim_edi_info_v1.id IN (SELECT MAX(id) FROM claim_edi_info_v1 GROUP BY claim_edi_info_v1.claim_id)
          GROUP BY claim_edi_info_v1.claim_id
          ) as claim_edi_info_v1"), function($join) {
            $join->on('claim_edi_info_v1.claim_id', '=', 'claim_info_v1.id');
        });

        /* $claim_qry->leftjoin('claim_cpt_info_v1', function($join) {
            $join->on('claim_cpt_info_v1.claim_id', '=', 'claim_info_v1.id');
        }); */
        // Claims Export issues fixed
		// Revision 1 : MR-2757 : 7 Sep 2019 : Selva
		$get_list_header = [];
        if (!empty(json_decode(@$request['dataArr']['data']['rendering_provider_id'])) && (json_decode(@$request['dataArr']['data']['rendering_provider_id'])) != "null" ) {
            $claim_qry->whereIn('claim_info_v1.rendering_provider_id', (array)json_decode($request['dataArr']['data']['rendering_provider_id']));
           $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', json_decode($request['dataArr']['data']['rendering_provider_id']))->get()->toArray();
            $get_list_header["Rendering Provider"] =  @array_flatten($provider)[0];
        }
        
        if ( !empty(json_decode(@$request['dataArr']['data']['billing_provider_id'])) ) {
            $claim_qry->whereIn('claim_info_v1.billing_provider_id', (array)json_decode($request['dataArr']['data']['billing_provider_id']));
            $provider= Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', json_decode(@$request['dataArr']['data']['billing_provider_id']))->get()->toArray();
            $get_list_header["Billing Provider"] =  @array_flatten($provider)[0];
        }

        if (!empty(json_decode(@$request['dataArr']['data']['referring_provider_id']))) {
            $claim_qry->whereIn('claim_info_v1.refering_provider_id', (array)json_decode($request['dataArr']['data']['referring_provider_id']));
            $provider= Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', json_decode(@$request['dataArr']['data']['referring_provider_id']))->get()->toArray();
            $get_list_header["Referring Provider"] =  @array_flatten($provider)[0];
        }

        if (!empty(json_decode(@$request['dataArr']['data']['facility_id']))) {
            $claim_qry->whereIn('claim_info_v1.facility_id', (array)json_decode($request['dataArr']['data']['facility_id']));
            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', json_decode($request['dataArr']['data']['facility_id']))->get()->toArray();
            $get_list_header["Facility"] =  @array_flatten($facility)[0];
        }

	    if(!empty(json_decode(@$request['dataArr']['data']['insurance_id']))){
	    	$insurance_id = json_decode(@$request['dataArr']['data']['insurance_id']);
            if(is_array(json_decode($request['dataArr']['data']['insurance_id'])))
                $claim_qry->whereIn('claim_info_v1.insurance_id', (array)json_decode($request['dataArr']['data']['insurance_id']));
            else  
                $claim_qry->where('claim_info_v1.insurance_id', json_decode($request['dataArr']['data']['insurance_id']));
            $insurance_name = Insurance::selectRaw("GROUP_CONCAT(insurance_name SEPARATOR ', ') as insurance_name")->whereIn('id', $insurance_id)->get()->toArray();
			$get_list_header["Insurance"] = @array_flatten($insurance_name)[0];
        }

		// Changed filter category to array datatype 
		// Revision 1 : MR-2757 : 27 Aug 2019 : Selva
		if(!empty(json_decode(@$request['dataArr']['data']['category']))) {
            $claim_qry->whereIn('claim_info_v1.insurance_category', (array)json_decode($request['dataArr']['data']['category']));
            $get_list_header["Category"] = json_decode(@$request['dataArr']['data']['category']);
        }

		if(!empty(json_decode(@$request['dataArr']['data']['claim_no']))) {
            $claim_qry->where('claim_info_v1.claim_number','like', '%'. json_decode($request['dataArr']['data']['claim_no']) . '%');
            $get_list_header["Category"] = json_decode(@$request['dataArr']['data']['category']);
        }

		if(!empty(json_decode(@$request['dataArr']['data']['dos']))){
			$date = explode('-',json_decode($request['dataArr']['data']['dos']));
            $from = date("Y-m-d", strtotime($date[0]));
			if($from == '1970-01-01'){
				$from = '0000-00-00';
			}
            $to = date("Y-m-d", strtotime($date[1]));
			$claim_qry->where(DB::raw('DATE(claim_info_v1.date_of_service)'),'>=',$from)->where(DB::raw('DATE(claim_info_v1.date_of_service)'),'<=',$to);
            //$claim_qry->whereBetween('claim_info_v1.date_of_service', [$from, $to]);
           $get_list_header["DOS"] = date("m/d/Y",strtotime($from)) . "  To " . date("m/d/Y",strtotime($to));
        }

		if(!empty(json_decode(@$request['dataArr']['data']['filed_date']))){
			$date = explode('-',json_decode($request['dataArr']['data']['filed_date']));
            $from = date("Y-m-d", strtotime($date[0]));
			if($from == '1970-01-01'){
				$from = '0000-00-00';
			}
            $to = date("Y-m-d", strtotime($date[1]));
            $claim_qry->whereRaw("DATE(CONVERT_TZ(claim_info_v1.filed_date,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(claim_info_v1.filed_date,'UTC','".$practice_timezone."')) <= '".$to."'"); 
            $get_list_header["Filed Date"] = date("m/d/Y",strtotime($from)) . "  To " . date("m/d/Y",strtotime($to));
			//$claim_qry->where(DB::raw('DATE(claim_info_v1.filed_date)'),'>=',$from)->where(DB::raw('DATE(claim_info_v1.filed_date)'),'<=',$to);
            //$claim_qry->whereBetween('claim_info_v1.filed_date', [$from, $to]);
        }

		if(!empty(json_decode(@$request['dataArr']['data']['submitted_date']))){
			$date = explode('-',json_decode($request['dataArr']['data']['submitted_date']));
            $from = date("Y-m-d", strtotime($date[0]));
			if($from == '1970-01-01'){
				$from = '0000-00-00';
			}
            $to = date("Y-m-d", strtotime($date[1]));
			$claim_qry->where(DB::raw('DATE(claim_info_v1.submited_date)'),'>=',date("Y-m-d",strtotime($from)))->where(DB::raw('DATE(claim_info_v1.submited_date)'),'<=',date("Y-m-d",strtotime($to)));
			$get_list_header["Submited Date"] = date("m/d/Y",strtotime($from)) . "  To " . date("m/d/Y",strtotime($to));
            //$claim_qry->whereBetween('claim_info_v1.submited_date', [$from, $to]);
        }
		
		if(!empty(json_decode(@$request['dataArr']['data']['rejected_date'])) && $request['type'] == 'rejected'){
			$date = explode('-',json_decode($request['dataArr']['data']['rejected_date']));
            $from = date("Y-m-d", strtotime($date[0]));
            $to = date("Y-m-d", strtotime($date[1]));
			$claim_qry->where(DB::raw('(claim_edi_info_v1.rejected_date)'),'>=',$from)->where(DB::raw('(claim_edi_info_v1.rejected_date)'),'<=',$to);
			$get_list_header["Rejected Date"] = date("m/d/Y",strtotime($from)) . "  To " . date("m/d/Y",strtotime($to));
        }
		
		//Claims: EDI Rejections: Reason code filters is to be added
        //Revision 1 - Ref: MR-2456 12 Aug 2019: Selva
		if(!empty(json_decode(@$request['dataArr']['data']['reason_code'])) && $request['type'] == 'rejected'){
			$claim_qry->where('claim_edi_info_v1.denial_codes', 'like', '%' . json_decode(@$request['dataArr']['data']['reason_code']) . '%');
        } 
		
		if(!empty(json_decode(@$request['dataArr']['data']['created_at']))){
			$date = explode('-',json_decode($request['dataArr']['data']['created_at']));
            $from = date("Y-m-d", strtotime($date[0]));
            $to = date("Y-m-d", strtotime($date[1])); 
			// commented timezone relevent 
            //$from = App\Http\Helpers\Helpers::utcTimezoneStartDate($date[0]);
           // $to = App\Http\Helpers\Helpers::utcTimezoneEndDate($date[1]); 
			$claim_qry->where(DB::raw('DATE(claim_info_v1.created_at)'),'>=',$from)->where(DB::raw('DATE(claim_info_v1.created_at)'),'<=',$to);
			$get_list_header["Created Date"] = date("m/d/Y",strtotime($from)) . "  To " . date("m/d/Y",strtotime($to));
			//$claim_qry->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'"); 
        }

		if(!empty(json_decode(@$request['dataArr']['data']['patient_name']))){
			$dynamic_name = json_decode($request['dataArr']['data']['patient_name']);
			$get_list_header["Patient Name"] =  $dynamic_name;
			$claim_qry->Where(function ($claim_qry) use ($dynamic_name) {
				$claim_qry->Where(function ($query) use ($dynamic_name) {
	                $claim_qry = $query->orWhere(DB::raw('CONCAT(patients.last_name,", ", patients.first_name)'),  'like', "%{$dynamic_name}%" );
	            });
			});
		}

		if(!empty(json_decode(@$request['dataArr']['data']['acc_no']))){
			$acc_no = json_decode($request['dataArr']['data']['acc_no']);
			$get_list_header["Acc No"] =  $acc_no;
			$claim_qry->Where(function ($claim_qry) use ($acc_no) {
				$claim_qry->orWhere(function ($query) use ($acc_no) {
					$sub_sql = '';
					$sub_sql = "patients.account_no LIKE '%$acc_no%'";
					if ($sub_sql != '')
						$query->whereRaw($sub_sql);
				});
			});
		}

		if(!empty(json_decode(@$request['dataArr']['data']['payer_id']))){
			$payer_id = json_decode(@$request['dataArr']['data']['payer_id']);
			$claim_qry->Where(function ($claim_qry) use ($payer_id) {
				$claim_qry->orWhere(function ($query) use ($payer_id) {
				$sub_sql = '';
				$sub_sql .= "insurances.payerid LIKE '%$payer_id%' ";
				if ($sub_sql != '')
					$query->whereRaw($sub_sql);
				});
			});
		}
		
		if($request['type'] == 'electronic')
			$claim_qry->where('claim_info_v1.claim_type', $request['type'])->whereIn('claim_info_v1.status', ['Ready'])->whereIn('claim_info_v1.self_pay', ['No'])->where('no_of_issues',0)->where('error_message','');
		elseif($request['type'] == 'paper')
			$claim_qry->where('claim_info_v1.claim_type', $request['type'])->whereIn('claim_info_v1.status', ['Ready'])->where('no_of_issues',0)->where('error_message','');
		elseif($request['type'] == 'error')
			$claim_qry->whereIn('claim_info_v1.status', ['Ready'])->where('no_of_issues','>',0)->where('error_message','!=','');
		elseif($request['type'] == 'submitted')
			$claim_qry->where('claim_info_v1.status', $request['type']);
		elseif ($request['type'] == 'rejected') {
            $claim_qry->where('claim_info_v1.status', 'Rejection');
		}
			
        $claim_qry->with('claimediinfo','patient', 'facility_detail', 'insurance_details', 'claim_sub_status')->with('rendering_provider', 'billing_provider');
                // ->with(array('billing_provider' => function($query) use ($orderByField, $orderByDir) {
                //     if ($orderByField == 'billing_provider')
                //     Log::info($orderByDir);
                //     $query->orderBy('short_name', $orderByDir); 
                // }))                    
                // ->with(array('rendering_provider' => function($query) use ($orderByField, $orderByDir) {
                //         if ($orderByField == 'rendering_provider')
                //             $query->orderBy('short_name', $orderByDir);
                // }));
		$pagination_count = $claim_qry->count(DB::raw('DISTINCT(claim_info_v1.id)'));
		// Removed repeated count fetching query.
		// Ver. 1 - 06-08-2019 - Ravi
		$counts = $pagination_count; // $claim_qry->count(DB::raw('DISTINCT(claim_info_v1.id)'));
		// Handling edi submission count mismatch issues fixed
		// Resivion 1 : MR-2839 : 12 Sep 2019 : Selva 
        $claim_qry->groupBy('claim_info_v1.id');
		$claimsIds = $claim_qry->pluck('claim_info_v1.id')->all();		
		$encodeClaimIds = [];
		foreach($claimsIds as $list){
			$encodeClaimIds[] = Helpers::getEncodeAndDecodeOfId($list,'encode');
		}
		$claim_qry->orderBy($orderByField, $orderByDir);
		if(empty($export)){
			$claim_qry->skip($start)->take($len);
		}
		$claims = $claim_qry->get();

		
		return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims','pagination_count','counts','type','encodeClaimIds','get_list_header')));
	}
	
	
    #======================================================================================#
    #								End Claim dashboard Page							   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to showing the claim getHoldReasonApi		   	   #
    #				 Claim getHoldReasonApi Start Point                                    # 
    #======================================================================================#

    public function getHoldReasonApi() {
        $hold_options = Holdoption::where('status', 'Active')->orderBy('option', 'ASC')->pluck('option', 'id')->all();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('hold_options')));
    }

    #======================================================================================#
    #								End Claim getHoldReasonApi 							   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to initial level of checking				   	   #
    #				 Claim Paper Checking Start Point        		                       # 
    #				 Claim Edi Electronic submission checking                              # 
    #======================================================================================#

    public function checkAndSubmitEdiClaim() {
        $request = Request::all();
        $claim_ids = $request['claim_ids'];
        $claim_success_count = $claim_error_count = 0;
        $status = '';
        $message = '';
        $claim_details_arr = $calim = [];
        $total_selected_claims = 0;

        // Check electronic claim api available or not 
         if ($this->checkClearingHouseApi()) { 
            $clearing_house = '';
            if ($claim_ids != '') {
                $claim_ids_arr = explode(',', $claim_ids);
                $total_selected_claims = count($claim_ids_arr);
                foreach ($claim_ids_arr as $claim_id_encode) {
                    $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id_encode, 'decode');
                    // Check valid claim or not whether its available in table or not and valid claim or not
                    $claim_details = $this->checkValidClaimOrNot($claim_id);

                    $claim_details['claim_id'] = $claim_id;
                    $claims_user_activity[] = $claim_id;
                    if ($claim_details['status'] == 'success') {
                        if (count($claim_details['patient_insurance_details']) > 0) {
                            /// Check initial scrubbing ///
                            $this->basicScrubbing($claim_details);
                            if (isset($this->error_msg[$claim_id]) && count(@$this->error_msg[$claim_id]) > 0) {
                                $claim_error_count++;
                                $this->error_msg[$claim_id] = implode('<br>', $this->error_msg[$claim_id]);
                            } else {
                                // Do EDI process
                                $claim_success_count++;
                                $claim_details_arr[] = $claim_details;
                            }
                        } else {
                            $claim_error_count++;
                            $this->error_msg[$claim_id] = 'No insurance found';
                        }
                    } else {
                        $claim_error_count++;
                        $this->error_msg[$claim_id] = $claim_details['message'];
                    }

                    // Update error message by claim id //
                    $claim_error = 'no';
                    if (isset($this->error_msg[$claim_id]) && $this->error_msg[$claim_id] != '') {
                        $claim_array_msg = explode(",", $this->error_msg[$claim_id]);
                        $claim_error_message = implode('<br>', $claim_array_msg);
                        ClaimInfoV1::where('id', $claim_id)->update(['error_message' => $claim_error_message, 'no_of_issues' => $claim_error_count]);
                    }
                }
            } else {
                $status = 'error';
                $message = 'No claims has been selected';
            }
            /// Starts - Condition to check, if any one of selected claim is valid or not ///
            if ($claim_success_count > 0) {
                $claim_detail_result = $this->createEDIFile($claim_details_arr, $this->clearing_house);
            }
            /// Ends - Condition to check, if any one of selected claim is valid or not ///
            $claim_process_details['total_selected_claims'] = $total_selected_claims;
            $claim_process_details['claim_error_count'] = $claim_error_count;
            $claim_process_details['claim_success_count'] = $claim_success_count;
        } else {
            $status = 'error';
            $message = "EDI setup currently unavailable. Try again later.";
        }

        if ($status != 'error') {
            $status = 'success';
            /*                     * * user activity ** */
            $action = "add";
            $get_name = json_encode($claims_user_activity);
            $fetch_url = Request::url();
            $module = "Claim";
            $submodule = "edi claim";
            $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
            /*                     * * user activity ** */
        }
        return Response::json(array('status' => $status, 'message' => $message, 'data' => compact('claim_process_details')));
    }
		
	public function checkAndSubmitEdiErrorClaim($data) {
        $request = $data;
        $claim_ids = $request['claim_ids'];
		
        $claim_success_count = $claim_error_count = 0;
        $status = '';
        $message = '';
        $claim_details_arr = $calim = [];
        $total_selected_claims = 0;

        // Check electronic claim api available or not 
         if ($this->checkClearingHouseApi()) { 
            $clearing_house = '';
            if ($claim_ids) {
                $claim_ids_arr = $claim_ids;
                $total_selected_claims = count($claim_ids_arr);
                foreach ($claim_ids_arr as $claim_id_encode) {
                    $claim_id = $claim_id_encode;
                    // Check valid claim or not whether its available in table or not and valid claim or not
                    $claim_details = $this->checkValidClaimOrNot($claim_id);
                    $claim_details['claim_id'] = $claim_id;
                    $claims_user_activity[] = $claim_id;
                    if ($claim_details['status'] == 'success') {
                        if (count($claim_details['patient_insurance_details']) > 0) {
                            /// Check initial scrubbing ///
                            $this->basicScrubbing($claim_details);
                            if (isset($this->error_msg[$claim_id]) && count(@$this->error_msg[$claim_id]) > 0) {
                                $claim_error_count++;
                                $this->error_msg[$claim_id] = implode('<br>', $this->error_msg[$claim_id]);
                            } else {
                                // Do EDI process
                                $claim_success_count++;
								$claim_details['submission'] = $request['submission'];
                                $claim_details_arr[] = $claim_details;
                            }
                        } else {
                            $claim_error_count++;
                            $this->error_msg[$claim_id] = 'No insurance found';
                        }
                    } else {
                        $claim_error_count++;
                        $this->error_msg[$claim_id] = $claim_details['message'];
                    }

                    // Update error message by claim id //
                    $claim_error = 'no';
                    if (isset($this->error_msg[$claim_id]) && $this->error_msg[$claim_id] != '') {
                        $claim_array_msg = explode(",", $this->error_msg[$claim_id]);
                        $claim_error_message = implode('<br>', $claim_array_msg);
                        ClaimInfoV1::where('id', $claim_id)->update(['error_message' => $claim_error_message, 'no_of_issues' => $claim_error_count]);
                    }
                }
            } else {
                $status = 'error';
                $message = 'No claims has been selected';
            }
            /// Starts - Condition to check, if any one of selected claim is valid or not ///
            if ($claim_success_count > 0) {
                $claim_detail_result = $this->createEDIFile($claim_details_arr, $this->clearing_house);
            }
            /// Ends - Condition to check, if any one of selected claim is valid or not ///
            $claim_process_details['total_selected_claims'] = $total_selected_claims;
            $claim_process_details['claim_error_count'] = $claim_error_count;
            $claim_process_details['claim_success_count'] = $claim_success_count;
        } else {
            $status = 'error';
            $message = "EDI setup currently unavailable. Try again later.";
        }

        if ($status != 'error') {
            $status = 'success';
            /*                     * * user activity ** */
            $action = "add";
            $get_name = json_encode($claims_user_activity);
            $fetch_url = Request::url();
            $module = "Claim";
            $submodule = "edi claim";
            $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
            /*                     * * user activity ** */
        }
        return Response::json(array('status' => $status, 'message' => $message, 'data' => compact('claim_process_details')));
    }	

    public function checkClearingHouseApi() {

        $clearing_house = ClearingHouse::where('status', 'Active')->where('practice_id', Session::get('practice_dbid'))->first();

        if (@$clearing_house->enable_837 == 'Yes') {
            $this->clearing_house = $clearing_house;
            return true;
        }else{
            return false;
		}
    }

    public function checkAndSubmitPaperClaim() {
        $request = Request::all(); 
        $claim_ids = $request['claim_ids'];
		
        $claim_success_count = $claim_error_count = 0;
        $status = '';
        $message = '';
        $claim_details_arr = $calim = $success_claim = [];
        $total_selected_claims = 0;
        if ($claim_ids != '') {
            $claim_ids_arr = explode(',', $claim_ids);
            $total_selected_claims = count($claim_ids_arr);
            try {

                $total_billed_amount = 0;
                foreach ($claim_ids_arr as $claim_id_encode) {
                    $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id_encode, 'decode');
                    /// Check valid claim or not whether its available in table or not and valid claim or not ///
					
                    $claim_details = $this->checkValidClaimOrNot($claim_id);
                    $claim_details['claim_id'] = $claim_id;
                    $claims_user_activity[] = $claim_id;
                    if ($claim_details['status'] == 'success') { 
						 /// Check initial scrubbing ///
						
                        $this->basicScrubbing($claim_details, 'paper');
						
                        if (isset($this->error_msg[$claim_id]) && count(@$this->error_msg[$claim_id]) > 0) {
                            $claim_error_count++;
                            $this->error_msg[$claim_id] = implode('<br>', $this->error_msg[$claim_id]);
                        } else {
							
							if($request['submission_status'] == 'submit'){
								$claim_success_count++;
							}
							$claim_info = ClaimInfoV1::where('id', $claim_id)->get()->toArray();
							$cur_date = date("Y-m-d H:i:s");
							$claim_update = '';
							if ($claim_info[0]['claim_submit_count'] == 0) { 
								$claim_update = ClaimInfoV1::find($claim_id);
								if($request['submission_status'] == 'submit'){
									$claim_update->submited_date = $cur_date;
									$claim_update->last_submited_date = $cur_date;
									$claim_update->claim_submit_count = $claim_update->claim_submit_count + 1;
									$claim_update->status = 'Submitted';
									$claim_update->save();
								}
								/* Storing the Trascation desc in claim wise */
								
								$insurance_category = $claim_details["patient_insurance_details"]["patient_insurance_category"];
								$patient_insurance_id = $claim_details["patient_insurance_details"]["patient_insurance_id"];
								$data['patient_ins_id'] = $patient_insurance_id;
								$data['pat_ins_category'] = $insurance_category;
								$data['claim_info_id'] = $claim_id;
								$data['resp'] = @$claim_update->insurance_id;
								$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
                                                ->where('claim_id',$claim_id)->get()->first();
								$data['pat_bal'] = $claimFinData['patient_due'];
								$data['ins_bal'] = $claimFinData['insurance_due'];
								if($request['submission_status'] == 'submit'){
									$claim_insurance = ClaimTXDESCV1::where('claim_id', $claim_id);
								
									$claim_submit_count = $claim_insurance->whereIn('transaction_type',["Submitted","Submitted Paper"])->get();
									$submitted_count = 0;
									foreach($claim_submit_count as $subCount){
										$descDet = json_decode($subCount->value_1, true);
										if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
											$patient_insurance_id = PatientInsurance::where('id',$descDet['patient_insurance_id'])->get()->first();
											if($patient_insurance_id->insurance_id == $claim_update->insurance_id && $claim_update->insurance_category == $descDet['insurance_category']){
												$submitted_count++;
											}
										}
									}
									
									if ($submitted_count > 0) {
										$claimTxnDesc = $this->storeClaimTxnDesc('Resubmitted Paper', $data);
									} else {
										$claimTxnDesc = $this->storeClaimTxnDesc('Submitted Paper', $data);
									}
								
								
									$dataArr['claim_tx_desc_id'] = $claimTxnDesc;
									$dataArr['claim_info_id'] = $claim_id;
									$dataArr['resp'] = @$claim_update->insurance_id;
									$claimCptDetails = Claimdoscptdetail::where('claim_id',$claim_id)->get()->toArray();
									foreach($claimCptDetails as $claimcptlist){
										$dataArr['claim_cpt_info_id'] = $claimcptlist['id'];
										$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
													  ->where('claim_cpt_info_id',$claimcptlist['id'])->get()->first();
										$dataArr['pat_bal'] = $cptFinData['patient_balance'];
										$dataArr['ins_bal'] = $cptFinData['insurance_balance'];
										if($request['submission_status'] == 'submit'){
											if ($submitted_count > 0) {
												$this->storeClaimCptTxnDesc('Resubmitted Paper', $dataArr);
											} else { 
												$this->storeClaimCptTxnDesc('Submitted Paper', $dataArr);
											}
										}
									}
								}
								
							} else {
								$claim_update = ClaimInfoV1::find($claim_id);
								if($request['submission_status'] == 'submit'){
									$claim_update->last_submited_date = $cur_date;
									$claim_update->claim_submit_count = $claim_update->claim_submit_count + 1;
									$claim_update->status = 'Submitted';
									$claim_update->save();
								}
								
								/* Storing the Trascation desc in claim wise */
								$insurance_category = $claim_details["patient_insurance_details"]["patient_insurance_category"];
								$patient_insurance_id = $claim_details["patient_insurance_details"]["patient_insurance_id"];
								$data['patient_ins_id'] = $patient_insurance_id;
								$data['pat_ins_category'] = $insurance_category;
								$data['claim_info_id'] = $claim_id;
								$data['resp'] = @$claim_update->insurance_id;
								$claim_insurance = ClaimTXDESCV1::where('claim_id', $claim_id);
                                $claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
                                    ->where('claim_id',$claim_id)->get()->first();
                                $data['pat_bal'] = $claimFinData['patient_due'];
                                $data['ins_bal'] = $claimFinData['insurance_due'];
								if($request['submission_status'] == 'submit'){
									$claim_submit_count = $claim_insurance->whereIn('transaction_type',["Submitted","Submitted Paper"])->get();
									$submitted_count = 0;
									foreach($claim_submit_count as $subCount){
										$descDet = json_decode($subCount->value_1, true);
										if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
											$patient_insurance_id = PatientInsurance::where('id',$descDet['patient_insurance_id'])->get()->first();
											if($patient_insurance_id->insurance_id == $claim_update->insurance_id && $claim_update->insurance_category == $descDet['insurance_category']){
												$submitted_count++;
											}
										}
									}
									if ($submitted_count > 0) {
										$claimTxnDesc = $this->storeClaimTxnDesc('Resubmitted Paper', $data);
									} else {
										$claimTxnDesc = $this->storeClaimTxnDesc('Submitted Paper', $data);
									}
								
									$dataArr['claim_tx_desc_id'] = $claimTxnDesc;
									$dataArr['claim_info_id'] = $claim_id;
									$dataArr['resp'] = $claim_update->insurance_id;
									$claimCptDetails = Claimdoscptdetail::where('claim_id',$claim_id)->get()->toArray();
									foreach($claimCptDetails as $claimcptlist){
										$dataArr['claim_cpt_info_id'] = $claimcptlist['id'];
										$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
											->where('claim_cpt_info_id',$claimcptlist['id'])->get()->first();
										$dataArr['pat_bal'] = $cptFinData['patient_balance'];
										$dataArr['ins_bal'] = $cptFinData['insurance_balance'];
										if($request['submission_status'] == 'submit'){
											if ($submitted_count > 0) {
												$this->storeClaimCptTxnDesc('Resubmitted Paper', $dataArr);
											} else {
												$this->storeClaimCptTxnDesc('Submitted Paper', $dataArr);
											}
										}
									}
								}
							}
							$success_claim[] = $claim_id_encode;
                        }
						
                    } else { 
                        $claim_error_count++;
                        $this->error_msg[$claim_id] = $claim_details['message'];
                    }
					
					if (isset($this->error_msg[$claim_id]) && $this->error_msg[$claim_id] != '') {
						$claim_array_msg = explode(",", $this->error_msg[$claim_id]);
						$claim_error_message = implode('<br>', $claim_array_msg);
						ClaimInfoV1::where('id', $claim_id)->update(['error_message' => $claim_error_message, 'no_of_issues' => $claim_error_count]);
					 }
                }
            } catch (\Exception $e) {
                //dd($e);
                \Log::info("Error on checkAndSubmitPaperClaim, Error MSg ".$e->getMessage()."line: ".$e->getLine());
                $data['claim_error_message'] = $e;
            }
        } else {
            $status = 'error';
            $message = 'No claims has been selected';
        }

        if ($status != 'error') {
            $status = 'success';
             /*                     * * user activity ** */
            $action = "add";
            $get_name = json_encode($claims_user_activity);
            $fetch_url = Request::url();
            $module = "Claim";
            $submodule = "paper claim";
            $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
            /*                     * * user activity ** */
        }
        $claim_process_details['total_selected_claims'] = $total_selected_claims;
        $claim_process_details['claim_error_count'] = $claim_error_count;
        //$claim_process_details['claim_success_count'] = $total_selected_claims - $claim_error_count;
		$claim_process_details['claim_success_count'] = $claim_success_count;
        $claim_process_details['success_claim'] = $success_claim;
        return Response::json(array('status' => $status, 'message' => $message, 'data' => compact('claim_process_details')));
    }

    public function checkValidClaimOrNot($claim_id) {
		$claimInfo = ClaimInfoV1::where('id', $claim_id)->first();
        if ($claimInfo) {
			
			$claim = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'billing_provider', 'facility_detail', 'insurance_details', 'dosdetails', 'patient', 'claim_details')->where('id', $claim_id)->where('status', 'Ready')->first();
		
			$claim_details = $insurance_details = $refering_provider = $rendering_provider = $dependent_details = $billing_provider = $patient_insurance_details = $facility_detail = [];
            /// Starts - Set Rendering Provider Details ///
			
            if ($claim->rendering_provider) {
                $rendering_provider['provider_name'] = $claim->rendering_provider->provider_name;
                $rendering_provider['first_name'] = $claim->rendering_provider->first_name;
                $rendering_provider['last_name'] = $claim->rendering_provider->last_name;
                $rendering_provider['middle_name'] = $claim->rendering_provider->middle_name;
                $rendering_provider['organization_name'] = $claim->rendering_provider->organization_name;
                $rendering_provider['npi'] = $claim->rendering_provider->npi;
                //$rendering_provider['secondary_type'] = $claim->rendering_provider->etin_type;
                // $rendering_provider['secondary_type_id'] = $claim->rendering_provider->etin_type_number;
                $rendering_provider['taxanomy_code'] = @$claim->rendering_provider->taxanomy->code;
            }
            /// Ends - Set Rendering Provider Details ///
            /// Starts - Set Billing Provider Details ///
            if ($claim->billing_provider) {
                $billing_provider['provider_name'] = $claim->billing_provider->provider_name;
                $billing_provider['first_name'] = $claim->billing_provider->first_name;
                $billing_provider['last_name'] = $claim->billing_provider->last_name;
                $billing_provider['middle_name'] = $claim->billing_provider->middle_name;
                $billing_provider['organization_name'] = $claim->billing_provider->organization_name;
                $billing_provider['npi'] = $claim->billing_provider->npi;
                $billing_provider['address1'] = $claim->billing_provider->address_1;
                $billing_provider['city'] = $claim->billing_provider->city;
                $billing_provider['state'] = $claim->billing_provider->state;
                $billing_provider['zipcode'] = $this->checkAndSetZipCode(@$claim->billing_provider->zipcode5, @$claim->billing_provider->zipcode4);
                $phone = Helpers::splitPhoneNumber($claim->billing_provider->phone);
                $billing_provider['phone_code'] = @$phone['code'];
                $billing_provider['phone_no'] = @$phone['no'];
                $billing_provider['tax_id_type'] = @$claim->billing_provider->etin_type;
                $billing_provider['tax_id'] = @$claim->billing_provider->etin_type_number;
                $billing_provider['taxanomy_code'] = @$claim->billing_provider->taxanomy->code;
                $billing_provider['secondary_type'] = @$claim->claim_details->billing_provider_qualifier;
                $billing_provider['secondary_type_id'] = @$claim->claim_details->billing_provider_otherid;
            }
            /// Ends - Set Billing Provider Details ///
            /// Starts - Set Referring Provider Details ///
            if ($claim->refering_provider) {
                $refering_provider['provider_type'] = '';
                if ($claim->refering_provider->provider_types_id == config('app.providertype.Supervising'))
                    $refering_provider['provider_type'] = 'DQ';
                elseif ($claim->refering_provider->provider_types_id == config('app.providertype.Referring'))
                    $refering_provider['provider_type'] = 'DN';
                elseif ($claim->refering_provider->provider_types_id == config('app.providertype.Ordering'))
                    $refering_provider['provider_type'] = 'DK';

                $refering_provider['first_name'] = $claim->refering_provider->first_name;
                $refering_provider['last_name'] = $claim->refering_provider->last_name;
                $refering_provider['middle_name'] = $claim->refering_provider->middle_name;
                $refering_provider['organization_name'] = $claim->refering_provider->organization_name;
                $refering_provider['npi'] = $claim->refering_provider->npi;
                $refering_provider['secondary_type'] = @$claim->claim_details->provider_qualifier;
                $refering_provider['secondary_type_id'] = @$claim->claim_details->provider_otherid;
            }
            /// Ends - Set Referring Provider Details ///
            /// Starts - Set Facility Details ///
            if ($claim->facility_detail) {
                $facility_detail['facility_name'] = $claim->facility_detail->facility_name;
                $facility_detail['npi'] = $claim->facility_detail->facility_npi;
                $facility_detail['address1'] = $claim->facility_detail->facility_address->address1;
                $facility_detail['city'] = $claim->facility_detail->facility_address->city;
                $facility_detail['state'] = $claim->facility_detail->facility_address->state;
                $facility_detail['zipcode'] = $this->checkAndSetZipCode($claim->facility_detail->facility_address->pay_zip5, $claim->facility_detail->facility_address->pay_zip4);
                $phone = Helpers::splitPhoneNumber($claim->facility_detail->phone);
                $facility_detail['phone_code'] = $phone['code'];
                $facility_detail['phone_no'] = $phone['no'];
                $facility_detail['secondary_type'] = @$claim->claim_details->service_facility_qual;
                $facility_detail['secondary_type_id'] = @$claim->claim_details->facility_otherid;
                $facility_detail['pos_id'] = $claim->pos_id;
            }
            /// Ends - Set Facility Details ///
            /// Starts - Set Payer Details ///
            if ($claim->insurance_details) {
                $claim->insurance_details->insurance_name = str_limit($claim->insurance_details->insurance_name, 25);
                $insurance_details['insurance_name'] = $claim->insurance_details->insurance_name;
                $insurance_details['address_1'] = $claim->insurance_details->address_1;
                $insurance_details['address_2'] = $claim->insurance_details->address_2;
                $insurance_details['city'] = $claim->insurance_details->city;
                $insurance_details['state'] = $claim->insurance_details->state;
                $insurance_details['zipcode'] = $this->checkAndSetZipCode($claim->insurance_details->zipcode5, $claim->insurance_details->zipcode4);
                $insurance_details['payerid'] = $claim->insurance_details->payerid;
                $insurance_details['insurance_type'] = @$claim->insurance_details->insurancetype->type_name;
            }
            /// Ends - Set Payer Details ///
            /// Starts - Set Patient Details ///
            if ($claim->patient) {
                $patient_details['patient_id'] = $claim->patient->id;
                $patient_details['first_name'] = $claim->patient->first_name;
                $patient_details['last_name'] = $claim->patient->last_name;
                $patient_details['middle_name'] = $claim->patient->middle_name;
                $patient_details['dob'] = $claim->patient->dob;
                $patient_details['gender'] = $claim->patient->gender;
                $patient_details['address'] = $claim->patient->address1;
                $patient_details['city'] = $claim->patient->city;
                $patient_details['state'] = $claim->patient->state;
                $patient_details['zipcode'] = $this->checkAndSetZipCode($claim->patient->zip5, $claim->patient->zip4);
                $phone = Helpers::splitPhoneNumber($claim->patient->phone);
                $patient_details['phone_code'] = $phone['code'];
                $patient_details['phone_no'] = $phone['no'];
            }
            /// Ends - Set Patient Details ///
            /// Starts - Set Patient Insurance and Dependent Details ///
            if ($claim->patient) {
                $patient_insurance = PatientInsurance::getPatientInsuranceDetailsById($claim->patient_id, $claim->patient_insurance_id, $claim->insurance_category);
                if ($patient_insurance) {
                    $patient_insurance_details['insurance_id'] = $patient_insurance->insurance_id;
                    $patient_insurance_details['patient_insurance_id'] = $patient_insurance->id;
                    $patient_insurance_details['patient_insurance_category'] = $patient_insurance->category;
                    $patient_insurance_details['policy_id'] = $patient_insurance->policy_id;
                    $patient_insurance_details['group_id'] = $patient_insurance->group_id;
                    $patient_insurance_details['group_name'] = $patient_insurance->group_name;
                    //$patient_insurance_details['insurance_type'] = $patient_insurance->insurancetype->type_code;
                    $patient_details['relationship'] = @$patient_insurance->relationship;

                    /// Starts - Set Patient Dependent Details ///
                    if ($patient_details['relationship'] != 'Self') {
                        $dependent_details['first_name'] = $patient_insurance->first_name;
                        $dependent_details['last_name'] = $patient_insurance->last_name;
                        $dependent_details['middle_name'] = $patient_insurance->middle_name;
                        $dependent_details['dob'] = $patient_insurance->insured_dob;
                        $dependent_details['gender'] = $patient_insurance->insured_gender;
                        $dependent_details['address'] = $patient_insurance->insured_address1;
                        $dependent_details['city'] = $patient_insurance->insured_city;
                        $dependent_details['state'] = $patient_insurance->insured_state;
                        $dependent_details['zipcode'] = $this->checkAndSetZipCode($patient_insurance->insured_zip5, $patient_insurance->insured_zip4);
                        $phone = Helpers::splitPhoneNumber($patient_insurance->insured_phone);
                        $dependent_details['phone_code'] = $phone['code'];
                        $dependent_details['phone_no'] = $phone['no'];
                    }
                    /// Ends - Set Patient Dependent Details /// 
                }
            }
            /// Ends - Set Patient Insurance and Dependent Details /// 
            /// Starts - Claim Section Details ///
            $claim_section = [];
            $claim_section['insurance_id'] = $claim->insurance_id;
            $claim_section['insurance_category'] = $claim->insurance_category;
            $claim_section['icd'] = $claim->icd_codes;
            $claim_section['is_send_paid_amount'] = $claim->is_send_paid_amount;
            $claim_section['claim_submit_count'] = $claim->claim_submit_count;
            $claim_section['referring_provider_id'] = $claim->referring_provider_id;
            $claim_section['total_charge'] = $claim->total_charge;
            if ($claim->is_send_paid_amount == 'Yes')
                $claim_section['paid_amount'] = $claim->insurance_paid;
            else
                $claim_section['paid_amount'] = 0;

            $claim_section['claim_codes'] = @$claim->claim_details->claim_code;
            $claim_section['claim_number'] = $claim->claim_number;
            $claim_section['related_to_employment'] = @$claim->claim_details->is_employment;
            $claim_section['related_to_auto_accident'] = @$claim->claim_details->is_autoaccident;
            $claim_section['related_to_auto_accident_state'] = @$claim->claim_details->autoaccident_state;
            $claim_section['related_to_other_accident'] = @$claim->claim_details->is_otheraccident;
            $claim_section['patient_signature_on_file'] = @$claim->claim_details->print_signature_onfile_box12;
            $claim_section['dependent_signature_on_file'] = @$claim->claim_details->print_signature_onfile_box13;
            //$claim_section['provider_signature_on_file'] = $claim->claim_details->insurance_paid;
            $claim_section['work_date_from'] = @$claim->claim_details->unable_to_work_from;
            $claim_section['work_date_to'] = @$claim->claim_details->unable_to_work_to;
            $claim_section['adminssion_date'] = $claim->admit_date;
            $claim_section['discharge_date'] = $claim->discharge_date;
            $claim_section['prior_authorization_number'] = @$claim->claim_details->box_23;
            if (!empty(@$claim->claim_details->illness_box14) && @$claim->claim_details->illness_box14 != '1970-01-01') {
                $claim_section['date_of_current_illness_date'] = @$claim->claim_details->illness_box14;
                $claim_section['date_of_current_illness_qualifier'] = 484;
            } else {
                $claim_section['date_of_current_illness_date'] = $claim->doi;
                $claim_section['date_of_current_illness_qualifier'] = 431;
            }
            $claim_section['outside_lab_charge'] = @$claim->claim_details->outside_lab;
            $claim_section['outside_lab_charge_amount'] = @$claim->claim_details->lab_charge;
            $claim_section['resubmission_code'] = @$claim->claim_details->resubmission_code;
            $claim_section['original_ref_no'] = @$claim->claim_details->original_ref_no;
            $claim_section['other_date'] = @$claim->claim_details->other_date;
            $claim_section['other_date_qualifier'] = @$claim->claim_details->other_date_qualifier;
            $claim_section['additional_claim_details'] = @$claim->claim_details->additional_claim_info;
            $claim_section['other_claim_ids'] = @$claim->claim_details->otherclaimid;
            $claim_section['accept_assignment'] = @$claim->claim_details->accept_assignment;
            $claim_section['pos'] = isset($claim->pos_code) ? $claim->pos_code : '';
            $claim_section['emg'] = isset($claim->claim_details->emergency) ? @$claim->claim_details->emergency : '';
            $claim_section['epsdt'] = isset($claim->claim_details->epsdt) ? @$claim->claim_details->epsdt : '';

            // Starts - Diagnosis Codes //
            $diagnosis_details = $selected_icd = [];
            if ($claim->icd_codes) {
                $selected_icd = Icd::getIcdValues($claim->icd_codes, 'yes');
            }
            $diagnosis_details['icd_indicator'] = '0';
            $diagnosis_details['selected_icd'] = $selected_icd;
            $claim_section['diagnosis_details'] = $diagnosis_details;
            // Ends - Diagnosis Codes //
            // Starts - Line Items Details //
            $doc = $line_item = [];
            $i = 1;
            $icd_pointer_key_arr = ['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D', '5' => 'E', '6' => 'F', '7' => 'G', '8' => 'H', '9' => 'I', '10' => 'J', '11' => 'K', '12' => 'L', ',' => ''];
            foreach ($claim->dosdetails as $dos_detail) {
                $line_item_arr = [];
                if (!empty($dos_detail)) {
                    if ($dos_detail->is_active == 1) {
                        $line_item_arr['line_item_id'] = $dos_detail->id;
                        $line_item_arr['dos_from'] = $dos_detail->dos_from;
                        $line_item_arr['dos_to'] = $dos_detail->dos_to;
                        $line_item_arr['is_active'] = $dos_detail->is_active;
                        $doc['row_' . $i]['from_mm'] = date('m', strtotime($dos_detail->dos_from));
                        $doc['row_' . $i]['from_dd'] = date('d', strtotime($dos_detail->dos_from));
                        $doc['row_' . $i]['from_yy'] = date('y', strtotime($dos_detail->dos_from));
                        $doc['row_' . $i]['to_mm'] = date('m', strtotime($dos_detail->dos_to));
                        $doc['row_' . $i]['to_dd'] = date('d', strtotime($dos_detail->dos_to));
                        $doc['row_' . $i]['to_yy'] = date('y', strtotime($dos_detail->dos_to));
                        $line_item_arr['cpt'] = $doc['row_' . $i]['cpt'] = $dos_detail->cpt_code;
                        $line_item_arr['mod1'] = $doc['row_' . $i]['mod1'] = isset($dos_detail->modifier1) ? $dos_detail->modifier1 : '';
                        $line_item_arr['mod2'] = $doc['row_' . $i]['mod2'] = isset($dos_detail->modifier2) ? $dos_detail->modifier2 : '';
                        $line_item_arr['mod3'] = $doc['row_' . $i]['mod3'] = isset($dos_detail->modifier3) ? $dos_detail->modifier3 : '';
                        $line_item_arr['mod4'] = $doc['row_' . $i]['mod4'] = isset($dos_detail->modifier4) ? $dos_detail->modifier4 : '';
                        $line_item_arr['billed_amount'] = $doc['row_' . $i]['billed_amt'] = isset($dos_detail->charge) ? explode('.', $dos_detail->charge) : '';
                        $line_item_arr['icd_pointers'] = $doc['row_' . $i]['icd_pointer'] = isset($dos_detail->cpt_icd_map_key) ? substr(strtr($dos_detail->cpt_icd_map_key, $icd_pointer_key_arr), 0, 4) : '';
                        $line_item_arr['units'] = $doc['row_' . $i]['unit'] = isset($dos_detail->unit) ? $dos_detail->unit : 1;
                        $line_item[] = $line_item_arr;
                    }
                }
                $i++;
            }
            $claim_section['line_item'] = $line_item;
            // Ends - Line Items Details //
            /// Ends - Claim Section Details ///  

            $claim_details['rendering_provider'] = $rendering_provider;
            $claim_details['billing_provider'] = $billing_provider;
            $claim_details['refering_provider'] = $refering_provider;
            $claim_details['facility_detail'] = $facility_detail;
            $claim_details['insurance_details'] = $insurance_details;
            $claim_details['patient_details'] = $patient_details;
            $claim_details['patient_insurance_details'] = $patient_insurance_details;
            $claim_details['dependent_details'] = $dependent_details;
            $claim_details['claim_section'] = $claim_section;
            //dd($claim_details);        
            $claim_details['status'] = 'success';
            $claim_details['insurance_due'] = $claim->insurance_due;
        } else {
            $claim_details['status'] = 'error';
            $claim_details['message'] = 'Invalid Claim Status';
        }
        return $claim_details;
    }

    public function checkAndSetZipCode($zip5, $zip4 = '') {
        $zipcode = $zip5;
        if ($zip4 != '')
            $zipcode .= $zip4;
        return $zipcode;
    }

    ///*** Starts - Basic / Common Scrubbing ***///
    public function basicScrubbing($claim_details, $claim_submit_type = 'electronic') {
        /// Starts - Check required fields and character length ///
        $claim_id = $claim_details['claim_id'];
        /*         * **** Starts - Insured Details ***** */
        /// Starts - Patient details ///
        $patient_details = $claim_details['patient_details'];
        $patient_name = $patient_details['last_name'] . ', ' . $patient_details['first_name'] . ' ' . $patient_details['middle_name'];
		
		// including space and comma length validation
		$patientNameLength = Config::get('siteconfigs.claim_length_validation.patient_name') + 3;
		
        // Patient Name
        $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], $patient_name, [trans("practice/claim/claims.validation.patient_name"), trans("practice/claim/claims.validation.patient_name")], ['', $patientNameLength]);
		
        // Patient Address
        $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], $patient_details['address'], [trans("practice/claim/claims.validation.patient_address"), trans("practice/claim/claims.validation.patient_address")], ['', Config::get('siteconfigs.claim_length_validation.patient_address')]);

        // Patient City
        $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], $patient_details['city'], [trans("practice/claim/claims.validation.patient_city"), trans("practice/claim/claims.validation.patient_city")], ['', Config::get('siteconfigs.claim_length_validation.patient_city')]);

        // Patient State
        $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], $patient_details['state'], [trans("practice/claim/claims.validation.patient_state"), trans("practice/claim/claims.validation.patient_state")], ['', Config::get('siteconfigs.claim_length_validation.patient_state')]);
		
        // Patient Zipcode
        $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], $patient_details['zipcode'], [trans("practice/claim/claims.validation.patient_zip"), trans("practice/claim/claims.validation.patient_zip")], ['', Config::get('siteconfigs.claim_length_validation.patient_zip')]);

        // Patient DOB
        $this->checkValidationList($claim_id, 'Patient', ['not_empty'], $patient_details['dob'], [trans("practice/claim/claims.validation.patient_dob")]);
		
        // Patient Gender
        $this->checkValidationList($claim_id, 'Patient', ['not_empty'], $patient_details['gender'], [trans("practice/claim/claims.validation.patient_sex")]);
	
        $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], @$claim_details['patient_insurance_details']['policy_id'], [trans("practice/claim/claims.validation.policy_id"), trans("practice/claim/claims.validation.policy_id")], ['', Config::get('siteconfigs.claim_length_validation.insured_id')]);
		
        // Patient Relationship
        if ($claim_submit_type == 'electronic') {
            $this->checkValidationList($claim_id, 'Patient', ['not_empty'], $patient_details['relationship'], [trans("practice/claim/claims.validation.patient_relationship")]);
        }
        /*         * **** Ends - Insured Details ***** */

        /*         * **** Starts - Dependent Details, if not self ***** */
        if (isset($patient_details['relationship'])  && $patient_details['relationship'] != 'Self') {
            $dependent_details = @$claim_details['dependent_details'];
            $patient_name = $dependent_details['last_name'] . ', ' . $dependent_details['first_name'] . ' ' . $dependent_details['middle_name'];

            // Patient Name
            $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], $patient_name, [trans("practice/claim/claims.validation.patient_name"), trans("practice/claim/claims.validation.patient_name")], ['', Config::get('siteconfigs.claim_length_validation.patient_name')], $patient_details['relationship']);

            // Patient Address
            $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], $dependent_details['address'], [trans("practice/claim/claims.validation.patient_address"), trans("practice/claim/claims.validation.patient_address")], ['', Config::get('siteconfigs.claim_length_validation.patient_address')], $patient_details['relationship']);

            // Patient City
            $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], $dependent_details['city'], [trans("practice/claim/claims.validation.patient_city"), trans("practice/claim/claims.validation.patient_city")], ['', Config::get('siteconfigs.claim_length_validation.patient_city')], $patient_details['relationship']);

            // Patient State
            $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], $dependent_details['state'], [trans("practice/claim/claims.validation.patient_state"), trans("practice/claim/claims.validation.patient_state")], ['', Config::get('siteconfigs.claim_length_validation.patient_state')], $patient_details['relationship']);

            // Patient Zipcode
            $this->checkValidationList($claim_id, 'Patient', ['not_empty', 'length'], $dependent_details['zipcode'], [trans("practice/claim/claims.validation.patient_zip"), trans("practice/claim/claims.validation.patient_zip")], ['', Config::get('siteconfigs.claim_length_validation.patient_zip')], $patient_details['relationship']);
			
            // Patient DOB
            $this->checkValidationList($claim_id, 'Patient', ['not_empty'], $dependent_details['dob'], [trans("practice/claim/claims.validation.patient_dob")], $patient_details['relationship']);
        }
        /*         * **** Ends - Dependent Details, if not self ***** */

        /*         * **** Starts - Insurance Details ***** */
        $insurance_details = @$claim_details['insurance_details'];
        // Insurance name
        $this->checkValidationList($claim_id, 'Insurance', ['not_empty'], $insurance_details['insurance_name'], [trans("practice/claim/claims.validation.insurance_name")]);
		
        // Insurance Address
        $this->checkValidationList($claim_id, 'Insurance', ['not_empty', 'length'], $insurance_details['address_1'], [trans("practice/claim/claims.validation.insurance_address"), trans("practice/claim/claims.validation.insurance_address")], ['', Config::get('siteconfigs.claim_length_validation.patient_address')]);

        // Insurance City
        $this->checkValidationList($claim_id, 'Insurance', ['not_empty', 'length'], $insurance_details['city'], [trans("practice/claim/claims.validation.insurance_city"), trans("practice/claim/claims.validation.insurance_city")], ['', Config::get('siteconfigs.claim_length_validation.patient_city')]);

        // Insurance State
        $this->checkValidationList($claim_id, 'Insurance', ['not_empty', 'length'], $insurance_details['state'], [trans("practice/claim/claims.validation.insurance_state"), trans("practice/claim/claims.validation.insurance_state")], ['', Config::get('siteconfigs.claim_length_validation.patient_state')]);

        // Insurance Zipcode
        $this->checkValidationList($claim_id, 'Insurance', ['not_empty', 'length'], $insurance_details['zipcode'], [trans("practice/claim/claims.validation.insurance_zip"), trans("practice/claim/claims.validation.insurance_zip")], ['', Config::get('siteconfigs.claim_length_validation.patient_zip')]);

        if ($claim_submit_type == 'electronic') {
            $this->checkValidationList($claim_id, 'Insurance', ['not_empty'], $insurance_details['payerid'], [trans("practice/claim/claims.validation.insurance_payerid")]);
        }
        /*         * **** Ends - Insurance Details ***** */

        /*         * **** Starts - Billing Provider Details ***** */
        $billing_provider = @$claim_details['billing_provider'];

        // Billing Provider Name
        $this->checkValidationList($claim_id, 'Billing', ['not_empty'], $billing_provider['provider_name'], [trans("practice/claim/claims.validation.billing_provider_name")]);

        // Billing Provider Address
        $this->checkValidationList($claim_id, 'Billing', ['not_empty'], $billing_provider['address1'], [trans("practice/claim/claims.validation.billing_provider_address")]);

        // Billing Provider City
        $this->checkValidationList($claim_id, 'Billing', ['not_empty'], $billing_provider['city'], [trans("practice/claim/claims.validation.billing_provider_city")]);

        // Billing Provider State
        $this->checkValidationList($claim_id, 'Billing', ['not_empty'], $billing_provider['state'], [trans("practice/claim/claims.validation.billing_provider_state")]);

        // Billing Provider Zipcode
        $this->checkValidationList($claim_id, 'Billing', ['not_empty'], $billing_provider['zipcode'], [trans("practice/claim/claims.validation.billing_provider_zipcode")]);

        // Billing Provider NPI
        $this->checkValidationList($claim_id, 'Billing', ['not_empty'], $billing_provider['npi'], [trans("practice/claim/claims.validation.billing_provider_npi")]);
        /*         * **** Ends - Billing Provider Details ***** */

        /*         * **** Starts - Rendering Provider Details ***** */
        $rendering_provider = @$claim_details['rendering_provider'];

        // Rendering Provider Name
        $this->checkValidationList($claim_id, 'Rendering', ['not_empty'], $rendering_provider['provider_name'], [trans("practice/claim/claims.validation.rendering_provider_name")]);

        /* // Rendering Provider Address
          $this->checkValidationList($claim_id, 'Rendering', ['not_empty'], $rendering_provider['address1'], [trans("practice/claim/claims.validation.rendering_provider_address")]);

          // Rendering Provider City
          $this->checkValidationList($claim_id, 'Rendering', ['not_empty'], $rendering_provider['city'], [trans("practice/claim/claims.validation.rendering_provider_city")]);

          // Rendering Provider State
          $this->checkValidationList($claim_id, 'Rendering', ['not_empty'], $rendering_provider['state'], [trans("practice/claim/claims.validation.rendering_provider_state")]);

          // Rendering Provider Zipcode
          $this->checkValidationList($claim_id, 'Rendering', ['not_empty'], $rendering_provider['zipcode'], [trans("practice/claim/claims.validation.rendering_provider_zipcode")]); */

        // Rendering Provider NPI
        $this->checkValidationList($claim_id, 'Rendering', ['not_empty'], $rendering_provider['npi'], [trans("practice/claim/claims.validation.rendering_provider_npi")]);
        /*         * **** Ends - Rendering Provider Details ***** */

        /*         * **** Starts - Facility Details ***** */
        $facility_detail = $claim_details['facility_detail'];

        // Facility Name
        $this->checkValidationList($claim_id, 'Facility', ['not_empty', 'length'], $facility_detail['facility_name'], [trans("practice/claim/claims.validation.facility_name"), trans("practice/claim/claims.validation.facility_name")], ['', Config::get('siteconfigs.claim_length_validation.facility_name')]);
		
		/* Pos 12 Patient address will be printed in cms1500 and Edi Segment */
		
		if($claim_details['facility_detail']['pos_id'] != '12'){
			// Facility Address
			$this->checkValidationList($claim_id, 'Facility', ['not_empty', 'length'], $facility_detail['address1'], [trans("practice/claim/claims.validation.facility_address"), trans("practice/claim/claims.validation.facility_address")], ['', Config::get('siteconfigs.claim_length_validation.facility_address')]);

			// Facility City
			$this->checkValidationList($claim_id, 'Facility', ['not_empty'], $facility_detail['city'], [trans("practice/claim/claims.validation.facility_city")]);

			// Facility State
			$this->checkValidationList($claim_id, 'Facility', ['not_empty'], $facility_detail['state'], [trans("practice/claim/claims.validation.facility_state")]);

			// Facility Zipcode
			$this->checkValidationList($claim_id, 'Facility', ['not_empty'], $facility_detail['zipcode'], [trans("practice/claim/claims.validation.facility_zip")]);
		}

        // Facility NPI
        // $this->checkValidationList($claim_id, 'Facility', ['not_empty'], $facility_detail['npi'], [trans("practice/claim/claims.validation.facility_npi")]);
    }

    public function checkValidationList($claim_id, $type, $validation_arr, $field_value, $validation_error_msg, $length_arr = [], $relation = '') {
        $required = trans("practice/claim/claims.validation.required");
        foreach ($validation_arr as $key => $validation_option) {
            if ($validation_option == 'not_empty') {
                if ($field_value == '')
                    $this->error_msg[$claim_id][] = '- ' . $type . " | " . $relation . " " . $validation_error_msg[$key] . $required;
            }
            elseif ($validation_option == 'length') {
                $length = $length_arr[$key];
                if (strlen($field_value) > $length)
                    $this->error_msg[$claim_id][] = '- ' . $type . " | " . $relation . " " . $validation_error_msg[$key] . str_replace('VAR_CHAR', $length, trans("practice/claim/claims.validation.greater_than_msg"));
            }
        }
    }

    #======================================================================================#
    #								End Claim EDI and PAPER Checking End				   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to DownloadCms								   	   #
    #				 Claim DownloadCms Start Point                       	               # 
    #======================================================================================#

    public function downloadCMS($claim_ids, $type = null,$status = null) {
        $claim_ids = explode(",", $claim_ids);
        $claim = new ChargeV1ApiController();
        foreach ($claim_ids as $claim_id) {
            $api_response = $claim->getcmsdataApi($claim_id);
            $api_response_data = $api_response->getData();
            $claim_detail = $api_response_data->data->claim_detail;
            $claim_number = $api_response_data->data->claim_detail->claim_no;
            $box_count = $api_response_data->data->box_count;
            $box_count_val = 1;
            $html = '';
            for ($i = 0; $i <= 3; $i++) { //As of now we only have maximum limit as 24 so, it will be around 4 pages availbale for cms1500form
                if ($box_count_val <= $box_count) {
                    $html .= view('charges/charges/cmsformpdf', compact('claim_detail', 'box_count_val', 'type'))->render();
                    $box_count_val = $box_count_val + 6;   // Adding maximum line items of the same charge
                } else {
                    continue;
                }
            }
            if (App::environment() == Config::get('siteconfigs.production.defult_production'))
                $path_medcubic = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
            else
                $path_medcubic = public_path() . '/';
            $path = $path_medcubic . 'media/paperclaim/';
            $path_archive = $path_medcubic . 'media/paperclaimarchieve/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $path_file = $path . $claim_number . ".pdf";
            $file[$claim_number] = $path_file;
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html);
            $pdf->save($path_file);
        }
        $user_id = Auth::user()->id;
        $default_view = Config::get('siteconfigs.production.defult_production');
        if (App::environment() == $default_view)
            $path = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
        else
            $path = public_path() . '/';
        $path_archive = $path . '/media/paperclaimarchieve/' . $user_id;
        $zipname = time() . '.zip';
        $this->create_zip($file, $path_archive, $zipname);
        array_map('unlink', glob($path_archive . '/*.*'));
        rmdir($path_archive);
    }

    function create_zip($files = array(), $destination = '', $zipname) {

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $outputzippath = $destination . '/' . $zipname;
		ob_end_clean();
        $zip = new ZipArchive;
        $zip->open($outputzippath, ZipArchive::CREATE);
        $i = 1;
        foreach ($files as $key => $filess) {
            $currentdate = date('Y-m-d');
            $content = file_get_contents($filess);
            $zip->addFromString($key . $i . $currentdate . '.pdf', $content);
            $i++;
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipname);
        header('Content-Length: ' . filesize($outputzippath));
        readfile($outputzippath);
    }

    #======================================================================================#
    #								End Claim downloadCMS								   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to updatependingclaims						   	   #
    #				 Claim Updatepending Start Point                                       # 
    #======================================================================================#

    public function updatePendingClaims() {
        $request = Request::all();
        $claim_ids = $request['claim_ids'];
        if ($claim_ids != '') {
            $claim_ids_arr = explode(',', $claim_ids);
            foreach ($claim_ids_arr as $claim_id_encode) {
                $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id_encode, 'decode');
                ClaimInfoV1::where('id', $claim_id)->where('status', 'Ready')->update(['status' => 'Pending']);
            }
            $status = 'success';
            $message = 'Status updated successfully';
        } else {
            $status = 'error';
            $message = 'No claims has been selected';
        }
        return Response::json(array('status' => $status, 'message' => $message));
    }

    #======================================================================================#
    #								End Claim Updatepending								   #
    #======================================================================================#
	
	
	#======================================================================================#
    #				 This function used to update paperclaims						   	   #
    #				 Claim Update Paper Start Point                                       # 
    #======================================================================================#

    public function updatePaperClaims() {
        $request = Request::all();
        $claim_ids = $request['claim_ids'];
        if ($claim_ids != '') {
            $claim_ids_arr = explode(',', $claim_ids);
            foreach ($claim_ids_arr as $claim_id_encode) {
                $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id_encode, 'decode');
                ClaimInfoV1::where('id', $claim_id)->where('status', 'Ready')->update(['claim_type' => 'paper']);
            }
            $status = 'success';
            $message = 'Status updated successfully';
        } else {
            $status = 'error';
            $message = 'No claims has been selected';
        }
        return Response::json(array('status' => $status, 'message' => $message));
    }

    #======================================================================================#
    #								End Claim Update Paper								   #
    #======================================================================================#
	
	
	#======================================================================================#
    #				 This function used to update Electronic claims					   	   #
    #				 Claim Update Paper Start Point                                        # 
    #======================================================================================#

    public function updateElectronicClaims() {
        $request = Request::all();
        $claim_ids = $request['claim_ids'];
        if ($claim_ids != '') {
            $claim_ids_arr = explode(',', $claim_ids);
            foreach ($claim_ids_arr as $claim_id_encode) {
                $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id_encode, 'decode');
                ClaimInfoV1::where('id', $claim_id)->where('status', 'Ready')->update(['claim_type' => 'electronic']);
            }
            $status = 'success';
            $message = 'Status updated successfully';
        } else {
            $status = 'error';
            $message = 'No claims has been selected';
        }
        return Response::json(array('status' => $status, 'message' => $message));
    }

    #======================================================================================#
    #								End Claim Update Electronic 						   #
    #======================================================================================#
	
	
	
	
	
    #======================================================================================#
    #				 This function used to postHoldClaims							   	   #
    #				 Claim postHoldClaims Start Point                                      # 
    #======================================================================================#

    public function postHoldClaims() {
        $request = Request::all();
        $claim_ids = explode(',', $request['hold_claim_ids']);
        $reason_id = $request['hold_reason_id'];

        if (count($claim_ids) > 0) {
            if ($reason_id == 'add_new') {
                $hold_options = Holdoption::where('option', $request['hold_reason'])->first();
                if ($hold_options)
                    $reason_id = $hold_options->id;
                else {
                    $hold_request['option'] = $request['hold_reason'];
                    $hold_request['created_by'] = Auth::user()->id;
                    $hold_options = Holdoption::create($hold_request);
                    $reason_id = $hold_options->id;
                }
            }
            if ($reason_id != '') {
                foreach ($claim_ids as $claim_id) {
                    $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
                    ClaimInfoV1::where('id', $claim_id)->where('status', 'Ready')->update(['status' => 'Hold', 'hold_reason_id' => $reason_id]);
                }
                $status = 'success';
                $message = 'Status updated successfully';
            } else {
                $status = 'error';
                $message = 'No claims has been selected';
            }
        } else {
            $status = 'error';
            $message = 'No claims has been selected';
        }
        return Response::json(array('status' => $status, 'message' => $message));
    }

    #======================================================================================#
    #								End Claim postHoldClaims							   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to listClaimTransmissionApi					   	   #
    #				 Claim listClaimTransmissionApi Start Point                            # 
    #======================================================================================#

    public function listClaimTransmissionApi($export = '') {
        $request = ($export != '') ? Request::all() : [];
        $result = $this->getClaimTransmissionSearchApi($request);
        $claim_transmission = $result["claim_list"];
        if ($export != "") {
            $exportparam = array(
                'filename' => 'Claim Transmission',
                'heading' => 'Claim Transmission',
                'fields' => array(
                    'transmission_type' => 'Transmission Type',
                    'total_claims' => 'No Of Claims',
                    'total_billed_amount' => 'Billed Amt',
                    'Transmited By' => array('table' => 'user', 'column' => 'name', 'label' => 'Transmited By'),
                    'created_at' => 'Transmited On',
            ));
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $claim_transmission, $export);
        }
        $hold_options = Holdoption::where('status', 'Active')->orderBy('option', 'ASC')->pluck('option', 'id')->all();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claim_transmission')));
    }

    public function getClaimTransmissionSearchApi($request) {
        $query = EdiTransmission::with('user')->where('is_transmitted', 'Yes');
        $result['claim_list'] = $query->orderBy('updated_at', 'DESC')->get();
        return $result;
    }

    #======================================================================================#
    #								End  listClaimTransmissionApi						   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to viewClaimTransmissionApi					   	   #
    #				 Claim viewClaimTransmissionApi Start Point                            # 
    #======================================================================================#

    public function viewClaimTransmissionApi($id) {
        $transmission_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $query = EdiTransmission::with('user', 'claim_transmission', 'claim_transmission.claims', 'claim_transmission.insurance', 'claim_transmission.claims.rendering_provider', 'claim_transmission.claims.billing_provider', 'claim_transmission.claims.facility_detail', 'claim_transmission.claims.patient', 'claim_transmission.cpt_transmission')->where('id', $transmission_id);
        $transmission = $query->first();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('transmission')));
    }

    #======================================================================================#
    #								End  viewClaimTransmissionApi						   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to getEdiReportsApi							   	   #
    #				 Claim getEdiReportsApi Start Point  		                           # 
    #======================================================================================#

    public function getEdiReportsApi() {
    	$request = Request::all();
		$list_page ="non_archive_list";
		$start 	= isset($request['start']) ? $request['start'] : 0;
        $len 	= (isset($request['length'])) ? $request['length'] : 50;
		$search = (!empty($request['search']['value'])) ? trim($request['search']['value']) : "";
		$edi_qry = EdiReport::with('user');
		if(!empty($request['dataArr']['data_update']) && $request['dataArr']['data_update']!==""){
			$up_data = $request['dataArr']['data_update'];
		    $res_option = $up_data['res_option'];
	        $list_page = $up_data['list_page'];

	        if ($res_option == 'edireport_make_read') {
	            $selected_edi_id_values = explode(",", $up_data['selected_edi_id_values']);
	            EdiReport::whereIn('id', $selected_edi_id_values)->update(['is_read' => 'Yes']);
	        } elseif ($res_option == 'edireport_make_unread') {
	            $selected_edi_id_values = explode(",", $up_data['selected_edi_id_values']);
	            EdiReport::whereIn('id', $selected_edi_id_values)->update(['is_read' => 'No']);
	        } elseif ($res_option == 'edireport_move_archive') {
	            $selected_edi_id_values = explode(",", $up_data['selected_edi_id_values']);
	            EdiReport::whereIn('id', $selected_edi_id_values)->update(['is_archive' => 'Yes']);
	        } elseif ($res_option == 'edireport_move_unarchive') {
	            $selected_edi_id_values = explode(",", $up_data['selected_edi_id_values']);
	            EdiReport::whereIn('id', $selected_edi_id_values)->update(['is_archive' => 'No']);
	        } elseif ($res_option == 'deleteedi') {
	            EdiReport::where('id', $up_data['ediid'])->delete();
	        }
	        if ($list_page == 'non_archive_list')
            	$edi_qry->where('is_archive', '!=', 'Yes');
            else
            $edi_qry->where('is_archive', '=', 'Yes');
	    }
	    else
			$edi_qry->where('is_archive', '!=', 'Yes');
		$orderByField = 'file_created_date';
        $orderByDir = 'DESC';
      	 /* Converting value to default search based */
        if (!empty($request['order'])) {
            $orderByField = ($request['order'][0]['column']) ? $request['order'][0]['column'] : $orderByField;

            switch ($orderByField) {
                case '1':
                    $orderByField = 'file_name';
                    break;

                case '2':
                    $orderByField = 'file_created_date';
                    break;

                case '3':
                    $orderByField = 'file_type';                   
                    break;

                default:
                    $orderByField = 'file_created_date';
                    break;
            }
      		$orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'DESC';
        }
        if(!empty(json_decode(@$request['dataArr']['data']['Name']))){
	      $filename = json_decode($request['dataArr']['data']['Name']);
	      $edi_qry->Where(function ($edi_qry) use ($filename) {
	        $edi_qry->Where(function ($query) use ($filename) {
	          $edi_qry = $query->orWhere('file_name','like', "%{$filename}%");
	        });
	      });
	    }
	    if(!empty(json_decode(@$request['dataArr']['data']['DateCreated']))){
	      $date = explode('-',json_decode($request['dataArr']['data']['DateCreated']));
	      $from = date("Y-m-d", strtotime($date[0]));
	      if($from == '1970-01-01'){
	        $from = '0000-00-00';
	      }
	      $to = date("Y-m-d", strtotime($date[1]));
	      $edi_qry->where(DB::raw('DATE(file_created_date)'),'>=',$from)->where(DB::raw('DATE(file_created_date)'),'<=',$to);
	    }
	    if (!empty(json_decode(@$request['dataArr']['data']['EdiStatus'])) && (json_decode(@$request['dataArr']['data']['EdiStatus'])) != "null" ) {
	        if (strpos($request['dataArr']['data']['EdiStatus'], ',') !== false) {
	            $edi_qry->whereIn('file_type', json_decode($request['dataArr']['data']['EdiStatus']));
	        }else{
	            $edi_qry->where('file_type', json_decode($request['dataArr']['data']['EdiStatus']));
	        }
	    }
	    
	    $edi_qry->orderBy($orderByField,$orderByDir);
	    $count = $edi_qry->count(DB::raw('DISTINCT(id)'));
	    $edi_qry->skip($start)->take($len);
	    $edi_reports = $edi_qry->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('edi_reports','count','list_page')));
    }

    #======================================================================================#
    #								End  getEdiReportsApi								   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to viewEdiReportApi							   	   #
    #				 Claim viewEdiReportApi Start Point  		                           # 
    #======================================================================================#

    public function viewEdiReportApi($id) {
        //Decode EDI ID
        $edi_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        //Get the EDI file path from the EDI Report table
        $file_path = EdiReport::where('id', $edi_id)->value('file_path');
        //If file is available get the contents of the file and return it, else display an error
        try {
            if ($file_path != '') {
                EdiReport::where('id', $edi_id)->update(array('is_read' => 'Yes'));
                $file_content = @file_get_contents($file_path);
                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('file_content')));
            } else {
                return Response::json(array('status' => 'error', 'message' => Lang::get("practice/claim/claims.validation.invalid_id"), 'data' => ''));
            }
        } catch (Exception $e) {
            return Response::json(array('status' => 'error', 'message' => Lang::get("practice/claim/claims.validation.invalid_id"), 'data' => ''));
        }
    }

    #======================================================================================#
    #								End  viewEdiReportApi								   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to getStatusEdiReportsApi					   	   #
    #				 Claim getStatusEdiReportsApi Start Point  	                           # 
    #======================================================================================#

    public function getStatusEdiReportsApi() {
        $request = Request::all();
        $res_option = $request['res_option'];
        $list_page = $request['list_page'];

        if ($res_option == 'edireport_make_read') {
            $selected_edi_id_values = explode(",", $request['selected_edi_id_values']);
            EdiReport::whereIn('id', $selected_edi_id_values)->update(['is_read' => 'Yes']);
        } elseif ($res_option == 'edireport_make_unread') {
            $selected_edi_id_values = explode(",", $request['selected_edi_id_values']);
            EdiReport::whereIn('id', $selected_edi_id_values)->update(['is_read' => 'No']);
        } elseif ($res_option == 'edireport_move_archive') {
            $selected_edi_id_values = explode(",", $request['selected_edi_id_values']);
            EdiReport::whereIn('id', $selected_edi_id_values)->update(['is_archive' => 'Yes']);
        } elseif ($res_option == 'edireport_move_unarchive') {
            $selected_edi_id_values = explode(",", $request['selected_edi_id_values']);
            EdiReport::whereIn('id', $selected_edi_id_values)->update(['is_archive' => 'No']);
        } elseif ($res_option == 'deleteedi') {
            EdiReport::where('id', $request['ediid'])->delete();
        }

        if ($list_page == 'non_archive_list') {
            $edi_reports = EdiReport::with('user')->where('is_archive', '!=', 'Yes')->orderBy('created_at', 'DESC')->get();
        } else {
            $edi_reports = EdiReport::with('user')->where('is_archive', '=', 'Yes')->orderBy('created_at', 'DESC')->get();
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('edi_reports', 'list_page')));
    }

    #======================================================================================#
    #								End  getStatusEdiReportsApi							   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to getedireporttabdetailsApi				   	   #
    #				 Claim getedireporttabdetailsApi Start Point  	                       # 
    #======================================================================================#

    public function getedireporttabdetailsApi() {
        $request = Request::all();

        if ($request['prev_sel_edireport_id_values'] == "") {
            $added_edireport_tabs = $request['selected_edireport_id_values'];
            $remove_edireport_tabs = "";
        } else {
            $prev_sel_edireport_id_arr = explode(",", $request['prev_sel_edireport_id_values']);
            $selected_edireport_id_arr = explode(",", $request['selected_edireport_id_values']);
            $remove_edireport_arr = array_diff($prev_sel_edireport_id_arr, $selected_edireport_id_arr);
            $added_edireport_arr = array_diff($selected_edireport_id_arr, $prev_sel_edireport_id_arr);
            $added_edireport_tabs = implode(",", $added_edireport_arr);
            $remove_edireport_tabs = implode(",", $remove_edireport_arr);
        }
        $edireport_ids_arr = explode(",", $added_edireport_tabs);
        $edireport_detail_obj = EdiReport::whereIn('id', $edireport_ids_arr);
        $edireport_detail = $edireport_detail_obj->get();
        $edireport_tab_list_arr = $edireport_detail_obj->select(DB::raw("CONCAT(file_name,'-::^^-',id) AS id_filename"), 'id')
        							->pluck('id_filename', 'id')->all();
        $edireport_tab_list = implode(",", $edireport_tab_list_arr);

        return Response::json(array('status' => 'success', 'added_edireport_tabs' => $added_edireport_tabs, 'remove_edireport_tabs' => $remove_edireport_tabs, 'edireport_detail' => $edireport_detail, 'edireport_tab_list' => $edireport_tab_list));
    }

    #======================================================================================#
    #								End  getedireporttabdetailsApi						   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to downloadClaim837And835Api				   	   #
    #				 Claim downloadClaim837And835Api Start Point                           # 
    #======================================================================================#

    public function downloadClaim837And835Api($type, $id) {
        $download_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if ($type == '837') {
            $file_path = EdiTransmission::where('id', $download_id)->value('file_path');
            $folder = 'clearing_house/'.Session::get('practice_dbid').'/';
            $redirect_url = 'claims/transmission';
        } elseif ($type == 'request') {

            $file_path = EdiReport::where('id', $download_id)->where('deleted_at', null)->value('file_path');
            $file_name = basename($file_path);
            $trns_filename = explode("_", $file_name);
            $file_name = $trns_filename[1] . ".txt";
            if (App::environment() == "production")
                $path_medcubic = '/home/johnbritto1947/medcubic/';
            else
                $path_medcubic = public_path() . '/';
            $folder = 'media/clearing_house/' . Session::get('practice_dbid') . '/';
            $full_file_path = $path_medcubic . $folder . $file_name;
            $headers = array('Content-Type: text/plain');
            return Response::download($full_file_path, $file_name, $headers);
        } else {
            $file_path = EdiReport::where('id', $download_id)->value('file_path');
            $folder = 'edi_report/' . Session::get('practice_dbid') . '/';
            if ($file_path != '')
                EdiReport::where('id', $download_id)->update(array('is_read' => 'Yes'));
            $redirect_url = 'claims/transmission';
        }

        if ($file_path != '') {
            $file_name_arr = explode($folder, $file_path);
            $headers = array('Content-Type: text/plain');
            return Response::download($file_path, $file_name_arr[1], $headers);
        } else {
            return Redirect::to($redirect_url)->with('error', Lang::get("practice/claim/claims.validation.invalid_id"));
        }
    }

    #======================================================================================#
    #								End  downloadClaim837And835Api						   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to generateEdiReports						   	   #
    #				 Claim generateEdiReports Start Point    		                       # 
    #======================================================================================#
	
	
	public function generateEdiReports() {
		if (App::environment() == "local") { 
            $path_medcubic = public_path() . '/';
            $path = $path_medcubic . 'media/edi_report/' . Session::get('practice_dbid') . '/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $clearing_house_details = ClearingHouse::where('status', 'Active')->where('practice_id', Session::get('practice_dbid'))->first();
            if ($clearing_house_details != '') {
                $clearingHouseType = $clearing_house_details->name;
                $ftp_server = $clearing_house_details->ftp_address;
                $ftp_username = $clearing_house_details->ftp_user_id;
                $ftp_password = $clearing_house_details->ftp_password;
                $ftp_port = $clearing_house_details->ftp_port;
                $ftp_folder = $clearing_house_details->edi_report_folder;
                $destination_dir = $path;
                $source_dir = $ftp_folder;
				// set up basic connection
                set_time_limit(0);
				
				if (!function_exists("ssh2_connect")) {
                    $status = 'error';
                    $message = 'Function ssh2_connect not found, you cannot use ssh2 here';
                } elseif (!$connection = ssh2_connect($ftp_server, $ftp_port)) {
                    $status = 'error';
                    $message = 'Connection cannot be made to clearing house. Please contact administrator';
                } elseif (!ssh2_auth_password($connection, $ftp_username, $ftp_password)) {
                    $status = 'error';
                    $message = 'Connection cannot be made to clearing house. Please contact administrator';
                } elseif (!$stream = ssh2_sftp($connection)) {
                    $status = 'error';
                    $message = 'Connection cannot be made to clearing house. Please contact administrator';
                } elseif (!$dir = opendir("ssh2.sftp://" . intval($stream) . "/{$source_dir}/./")) {
                    $status = 'error';
                    $message = 'ssh2.sftp://' . $stream . $source_dir . 'Could not open the directory';
                }
				if($clearingHouseType == 'OfficeAlly'){
					$files_list = 0;
					while (false !== ($file = readdir($dir))) {
						if ($file == "." || $file == "..")
								continue;
						$file_name = basename($file);
						if (!file_exists($destination_dir . $file_name)) {
							@fopen($destination_dir . $file, 'w');
							$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$source_dir}/" . $file);

							$myerafile = fopen($destination_dir . $file, "w+");
							fwrite($myerafile, $file_content);
							
							$files_list++;
							$file_type = explode('.', $file);
							if (substr($file, 9, 12) == '_EDI_STATUS_') {
								$file_status = 'Payer Response';
							} elseif (substr($file, 0, 8) == 'FS_HCFA_' && substr($file, 18, 11) != 'ErrorReport') {
								$file_status = 'EDI Response';
							} elseif (substr($file, 0, 8) == 'FS_HCFA_' && substr($file, 18, 11) == 'ErrorReport') {
								$file_status = 'EDI Error Response';
							} elseif (substr($file, 9, 12) == '_ERA_STATUS_') {
								$file_status = 'ERA Response';
							} else {
								$file_status = 'Error';
							}
							$edi_report['file_name'] = $file;
							$edi_report['file_created_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$source_dir}/" . $file));
							/*  $edi_report['file_type'] = $file_type[1];  */
							$edi_report['file_type'] = $file_status;
							$edi_report['file_size'] = filesize("ssh2.sftp://" . intval($stream) . "/{$source_dir}/" . $file);
							$edi_report['server_file_delete_date'] = date('Y-m-d', strtotime("+3 days"));
							$edi_report['file_path'] = $destination_dir . $file;
							$edi_report['created_by'] = Auth::user()->id;
							EdiReport::create($edi_report);
						}
						
					}
					if ($files_list > 0) {
						$status = 'success';
						$message = 'Downloaded successfully';
					} else {
						$status = 'error';
						$message = 'No files to download';
					}
				}elseif($clearingHouseType == 'Navicure'){
					$files_list = 0;
					// Changed Files copy method to our server
					// Revesion 1 : MR-2733 : 23 Aug 2019 : Selva
					$edi835Folder = $source_dir."/835";
					if ($dir = opendir("ssh2.sftp://" . intval($stream) . "/{$edi835Folder}/./")){
						while (false !== ($file = readdir($dir))) {
							if ($file == "." || $file == "..")
									continue;
							$file_name = basename($file);
							if (!file_exists($destination_dir . $file_name)) {
								
								@fopen($destination_dir . $file, 'w');
								$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$edi835Folder}/" . $file);
								$myerafile = fopen($destination_dir . $file, "w+");
								fwrite($myerafile, $file_content);
								$file_content = fopen($destination_dir.$file, 'r');
								$files_list++;
								$file_status = 'ERA Response';
								$edi_report['file_name'] = $file;
								$edi_report['file_created_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$edi835Folder}/" . $file));
								$edi_report['file_type'] = $file_status;
								$edi_report['file_size'] = filesize("ssh2.sftp://" . intval($stream) . "/{$edi835Folder}/" . $file);
								$edi_report['server_file_delete_date'] = date('Y-m-d', strtotime("+3 days"));
								$edi_report['file_path'] = $destination_dir . $file;
								$edi_report['created_by'] = Auth::user()->id;
								EdiReport::create($edi_report);
							}
							
						}
					}else{
						\Log::info('EDI 835 Navicure - ssh2.sftp://' . $stream . $edi835Folder . 'Could not open the directory')	;
					}
					
					$edi277Folder = $source_dir."/277";
					if ($dir = opendir("ssh2.sftp://" . intval($stream) . "/{$edi277Folder}/./")){
						while (false !== ($file = readdir($dir))) {
							if ($file == "." || $file == "..")
									continue;
							$file_name = basename($file);
							if (!file_exists($destination_dir . $file_name)) {
								@fopen($destination_dir . $file, 'w');
								$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$edi277Folder}/" . $file);
								$myerafile = fopen($destination_dir . $file, "w+");
								fwrite($myerafile, $file_content);
								
								/* $srcFile = fopen("ssh2.sftp://" . intval($stream) . "/{$edi277Folder}/" . $file, 'r');
								$resFile = fopen($destination_dir.$file, 'w');
								$writtenBytes = stream_copy_to_stream($srcFile, $resFile);
								$file_content = fopen($destination_dir.$file, 'r');
								fclose($resFile);
								fclose($srcFile); */
								$files_list++;
								$file_status = 'EDI Response';
								$edi_report['file_name'] = $file;
								$edi_report['file_created_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$edi277Folder}/" . $file));
								$edi_report['file_type'] = $file_status;
								$edi_report['file_size'] = filesize("ssh2.sftp://" . intval($stream) . "/{$edi277Folder}/" . $file);
								$edi_report['server_file_delete_date'] = date('Y-m-d', strtotime("+3 days"));
								$edi_report['file_path'] = $destination_dir . $file;
								$edi_report['created_by'] = Auth::user()->id;
								EdiReport::create($edi_report);
							}
						}
					}else{
						\Log::info('EDI 277 Navicure - ssh2.sftp://' . $stream . $edi277Folder . 'Could not open the directory')	;
					}
					
					$edi977Folder = $source_dir."/997";
					if ($dir = opendir("ssh2.sftp://" . intval($stream) . "/{$edi977Folder}/./")){
						while (false !== ($file = readdir($dir))) {
							if ($file == "." || $file == "..")
									continue;
							$file_name = basename($file);
							if (!file_exists($destination_dir . $file_name)) {
								@fopen($destination_dir . $file, 'w');
								$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$edi977Folder}/" . $file);
								$myerafile = fopen($destination_dir . $file, "w+");
								fwrite($myerafile, $file_content);
								
								/* $srcFile = fopen("ssh2.sftp://" . intval($stream) . "/{$edi977Folder}/" . $file, 'r');
								$resFile = fopen($destination_dir.$file, 'w');
								$writtenBytes = stream_copy_to_stream($srcFile, $resFile);
								$file_content = fopen($destination_dir.$file, 'r');
								fclose($resFile);
								fclose($srcFile); */
								$files_list++;
								$file_status = 'EDI Acknowledgment Response';
								$edi_report['file_name'] = $file;
								$edi_report['file_created_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$edi977Folder}/" . $file));
								$edi_report['file_type'] = $file_status;
								$edi_report['file_size'] = filesize("ssh2.sftp://" . intval($stream) . "/{$edi977Folder}/" . $file);
								$edi_report['server_file_delete_date'] = date('Y-m-d', strtotime("+3 days"));
								$edi_report['file_path'] = $destination_dir . $file;
								$edi_report['created_by'] = Auth::user()->id;
								EdiReport::create($edi_report);								
							}							
						}
					}else{
						\Log::info('EDI 997 Navicure - ssh2.sftp://' . $stream . $edi977Folder . 'Could not open the directory')	;
					}
					
					if ($files_list > 0) {
						$status = 'success';
						$message = 'Downloaded successfully';
					} else {
						$status = 'error';
						$message = 'No files to download';
					}
					
				}
				
			} else {
                $status = 'error';
                $message = 'Kindly setup clearing house and try again...';
            }
		} else {
            $status = 'error';
            $message = 'Unable to download files in local environment';
        }
		return Response::json(array('status' => $status, 'message' => $message));
	}
	
    public function generateEdiReports_old() {
        if (App::environment() == "local") { 
            $path_medcubic = public_path() . '/';
            $path = $path_medcubic . 'media/edi_report/' . Session::get('practice_dbid') . '/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $clearing_house_details = ClearingHouse::where('status', 'Active')->where('practice_id', Session::get('practice_dbid'))->first();
            if ($clearing_house_details != '') {
                $ftp_server = $clearing_house_details->ftp_address;
                $ftp_username = $clearing_house_details->ftp_user_id;
                $ftp_password = $clearing_house_details->ftp_password;
                $ftp_port = $clearing_house_details->ftp_port;
                $ftp_folder = $clearing_house_details->edi_report_folder;

                $destination_dir = $path;
                $source_dir = $ftp_folder;

                // set up basic connection
                set_time_limit(0);
                $files_list = 0;
                $connection = ssh2_connect($ftp_server, $ftp_port);
                if (ssh2_auth_password($connection, $ftp_username, $ftp_password)) {
                    // initialize sftp
                    $stream = ssh2_sftp($connection);
                    if (!$dir = opendir("ssh2.sftp://" . intval($stream) . "/{$source_dir}/./")) {
                        $status = 'error';
                        $message = 'Unable to open clearing house folder...';
                    }
                    $files = array();
                    while (false !== ($file = readdir($dir))) {
                        if ($file == "." || $file == "..")
                            continue;
                        if (!file_exists($path . $file)) {
                            $files[] = $file;
                        }
                    }

                    foreach ($files as $file) {
                        if (!$remote = @fopen("ssh2.sftp://" . intval($stream) . "/{$source_dir}/.//{$file}", 'r')) {
                            $status = 'error';
                            $message .= 'Unable to open remote file: $file\n';
                            continue;
                        }

                        if (!$local = @fopen($destination_dir . $file, 'w')) {
                            $status = 'error';
                            $message .= 'Unable to create local file: $file\n';
                            continue;
                        }

                        $read = 0;
                        $filesize = filesize("ssh2.sftp://" . intval($stream) . "/{$source_dir}/.//{$file}");
						
                        while ($read < $filesize && ($buffer = fread($remote, $filesize - $read))) {
							if(!file_exists($path . $file)) {
								$read += strlen($buffer);
								$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$source_dir}/" . $file);
								if (fwrite($local, $file_content) === FALSE) {
									$status = 'error';
									$message .= 'Unable to write to local file: $file\n';
									break;
								} else {
									$files_list++;
									$file_type = explode('.', $file);
									if (substr($file, 9, 12) == '_EDI_STATUS_') {
										$file_status = 'Payer Response';
									} elseif (substr($file, 0, 8) == 'FS_HCFA_' && substr($file, 18, 11) != 'ErrorReport') {
										$file_status = 'EDI Response';
									} elseif (substr($file, 0, 8) == 'FS_HCFA_' && substr($file, 18, 11) == 'ErrorReport') {
										$file_status = 'EDI Error Response';
									} elseif (substr($file, 9, 12) == '_ERA_STATUS_') {
										$file_status = 'ERA Response';
									} else {
										$file_status = 'Error';
									}
									$edi_report['file_name'] = $file;
									$edi_report['file_created_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$source_dir}/" . $file));
									/*  $edi_report['file_type'] = $file_type[1];  */
									$edi_report['file_type'] = $file_status;
									$edi_report['file_size'] = filesize("ssh2.sftp://" . intval($stream) . "/{$source_dir}/" . $file);
									$edi_report['server_file_delete_date'] = date('Y-m-d', strtotime("+3 days"));
									$edi_report['file_path'] = $destination_dir . $file;
									$edi_report['created_by'] = Auth::user()->id;
									EdiReport::create($edi_report);
									//if(EdiReport::create($edi_report))
									//ftp_delete($conn_id, $remote_file);
								}
							}	
                        }
                        fclose($local);
                        fclose($remote);
                    }
                }
                if ($files_list > 0) {
                    $status = 'success';
                    $message = 'Downloaded successfully';
                } else {
                    $status = 'error';
                    $message = 'No files to download';
                }
            } else {
                $status = 'error';
                $message = 'Kindly setup clearing house and try again...';
            }
        } else {
            $status = 'error';
            $message = 'Unable to download files in local environment';
        }
        return Response::json(array('status' => $status, 'message' => $message));
    }

    #======================================================================================#
    #								End generateEdiReports								   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to tabviewEdiReportApi						   	   #
    #				 Claim tabviewEdiReportApi Start Point    		                       # 
    #======================================================================================#

    public static function tabviewEdiReportApi($id) {
        $edi_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $file_path = EdiReport::where('id', $edi_id)->value('file_path');
        if ($file_path != '') {
            $file_content = file_get_contents($file_path);
            return $file_content;
        } else {
            return "Invalid Access";
        }
    }

    #======================================================================================#
    #								End tabviewEdiReportApi								   #
    #======================================================================================#
    #======================================================================================#
    #				 This function used to printCMS									   	   #
    #				 Claim printCMS Start Point			    		                       # 
    #======================================================================================#

    public function printCMS($claim_ids, $type = null, $status = null) {
        $claim_ids = explode(",", $claim_ids);
        $claim = new ChargeController();
        $claim_data = '';
        foreach ($claim_ids as $claim_id) {
            $claim_data .= $claim->generatecmsform($claim_id, $type);
        }
        /* $pdfobj = new PDF();
        $pdfobj::setPaper('A5', 'portrait');
        return $pdfobj::load($claim_data)->show(); */
		
		$pdfobj = new dompdf();
		$customPaper = array(0, 0, 648, 840);
        $pdfobj->setPaper($customPaper);
		$pdfobj->loadHTML($claim_data);
		$pdfobj->render();
		return $pdfobj->stream("cmsform.pdf", array("Attachment" => false));		
    }

    #======================================================================================#
    #								End printCMS										   #
    #======================================================================================#
		
	public function generateSearchApi($type){ 
		try{
			$search_details = SearchFields::where('page_name',$type)->select('id','search_fields','page_name')->orderBy('id', 'DESC')->first();
			if(empty($search_details)){
				// If not found search query in current page, get it from master settings.
				$database_name	= Config::get('siteconfigs.connection_database');
				$master_srch_det = SearchFields::on('responsive')->where('page_name',$type)->select('id','search_fields','page_name')->first();
				if(empty($master_srch_det)){		
					// Redirect to dashboard if search not defined in master settings.
					return Response::json(array('status' => 'error', 'message' => 'Search not defined. Please contact administrator', 'data'=>[]));		
					exit;
				} else {
					$dataArr['search_fields'] = $master_srch_det['search_fields'];
					$dataArr['page_name'] = $master_srch_det['page_name'];								
					$details = SearchFields::create($dataArr);
					$search_details = $details;
					$searchUserData = [];
				}			
			} else {
				// MR-2753 - Provider login showing previous user saved data : issues fixed  : Selva
				if(Auth::check() && isset(Auth::user()->id)) {
					$searchUserDataRes = SearchUserData::where('search_fields_id', @$search_details->id)->where('user_id',Auth::user()->id);
					$searchUserData = $searchUserDataRes->orderBy('id','desc')->first(); // Temp hide line for getting from page load 
				} else {
					$searchUserData = [];	
				}
				//$searchUserData = []; // page load empty data 
			}		
			return Response::json(array('status' => 'success', 'message' => '','data'=>compact('search_details','searchUserData')));
		} catch (Exception $e) {
			return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data'=>[]));
		}
	}
	
	public function searchSavedDataApi($type,$search_id = ''){ 
		$data = '';
		$search_details = SearchFields::where('page_name',$type)->select('id','search_fields','page_name')->first();
		if(!empty($search_details)){
			// Added condition for user based showing search filter saved data
			// Revision 1 : Ref : MR-2753 : 27 Aug 2019 : Selva
			$searchUserDataRes = SearchUserData::where('search_fields_id',$search_details->id)->where('id',$search_id)->where('user_id',Auth::user()->id);
			$searchUserData = $searchUserDataRes->orderBy('id','desc')->first();
			// Handling non object error in search filter with saved data
			if(isset($searchUserData) && !empty($searchUserData)){
				$dataArr = json_decode($searchUserData->search_fields_data);
				$data = '?search=yes&';
				foreach($dataArr as $list){
					$data .= $list->label_name."=".$list->value."&";
				}
			}
		}
		return Response::json(array('status' => 'success', 'message' => '','data'=>compact('data')));
	}
	
	public function searchDataApi(){
		$request = Request::all();
		$userID = Auth::user()->id;
		$dataArr['user_id'] = $userID;
		$dataArr['search_fields_id'] = $request['page_id'];
		$dataArr['search_fields_data'] = $request['remember_data'];
		$dataArr['deleted_at'] = '0000-00-00 00:00:00';
		if(!empty($request['more_data'])){
			$dataArr['more_field_data'] = implode(',',$request['more_data']);
		}else{
			$dataArr['more_field_data'] = '';
		}
		// MR-2753 - Provider login showing previous user saved data : issues fixed  : Selva
		$searchData = SearchUserData::where('search_fields_id',$request['page_id'])->where('user_id',$userID);
		$searchDataCount = $searchData->count();
		$searchDataRes = $searchData->orderBy('updated_at','desc')->first();
		if($searchDataCount == 0){
			SearchUserData::create($dataArr);
		}else{
			$dataArr['updated_at'] = date('Y-m-d h:i:s');
			SearchUserData::where('search_fields_id',$request['page_id'])->where('user_id',$userID)->update($dataArr);
		}
	}
	
	public function searchDataRemoveApi(){
		$request = Request::all();
		//SearchUserData::where('search_fields_id',$request['page_id'])->delete();
	}
		
	public function updateInsuranceCategoryApi(){
		$pmtinfo = PMTClaimTXV1::where('pmt_method','Insurance')->where('ins_category','!=',0)->where('ins_category','!=','')->get();
		foreach($pmtinfo as $list){
			$category = PatientInsurance::where('patient_id',$list->patient_id)->where('insurance_id',$list->ins_category)->whereIn('category',['Primary','Secondary','Tertiary'])->value('category');
			if(!empty($category))
				PMTClaimTXV1::where('id',$list->id)->update(['ins_category'=>$category]);
		}
	}
	
	
	#======================================================================================#
	#				 This function used to change the claims status			   	   		   #
	#				 Claim status change Start Point		 	                           # 
	#======================================================================================#
	
	public function changeClaimStatusApi(){
		/* Armanagement  bulk notes and workbench chnaged based on all */
		/* Revision 1 : Ref: MR-2756 : 27 Aug 2019 : selva */
		$request = Request::all();
		$type = $request['type'];
		if($type == 'SubStatus') {
			// Added claim sub status option in armanagement
			// Revision 1 : MR-2786 Ravi : 22 Oct 2019
			foreach($request['claim_ids'] as $claimId) {			
				$sub_status =  (isset($request['statusVal'])) ? $request['statusVal'] : '';				
				ClaimInfoV1::where('claim_number', $claimId)->update(['sub_status_id'=>$sub_status]);
			}
		} else {
			// Added hold reason for bulk hold option in armanagement
			// Revision 1 : MR-2786 : 4 Sep 2019
			// when we changed claims status to pending set hold reason id is 0
			// Revision 1 : MR-2813 : 11 Sep 2019
			
			// when we changed claims status to Ready set hold reason id is 0
			// Revision 1 : MEDV2-1011 : 24 Mar 2020: Selva
			if($type == 'Ready'){
				$dataArr['pending'] = $dataArr['hold'] = $dataArr['rejection'] = $dataArr['submitted'] = $dataArr['denied'] = 0;
				foreach($request['claim_ids'] as $claimId){
					$claimsInfo = ClaimInfoV1::where('claim_number', $claimId)->get()->first();
					if($claimsInfo->status == 'Pending')
						$dataArr['pending'] = $dataArr['pending'] + 1;
					else if($claimsInfo->status == 'Hold')
						$dataArr['hold'] = $dataArr['hold'] + 1;
					else if($claimsInfo->status == 'Rejection')
						$dataArr['rejection'] = $dataArr['rejection'] + 1;
					else if($claimsInfo->status == 'Submitted')
						$dataArr['submitted'] = $dataArr['submitted'] + 1;
					else if($claimsInfo->status == 'Denied')
						$dataArr['denied'] = $dataArr['denied'] + 1;
					
					ClaimInfoV1::where('claim_number', $claimId)->whereNotIn('status',['Paid','Patient','Ready'])->update(['status' => $type, 'hold_reason_id'=>'0','sub_status_id' => '']);
				}
				return Response::json(array('status' => 'success', 'message' => '','data'=>compact('dataArr')));
			}else{
				foreach($request['claim_ids'] as $claimId){
					if(isset($request['reasonVal']))
						ClaimInfoV1::where('claim_number', $claimId)->update(['status' => $type,'hold_reason_id'=>$request['reasonVal'],'sub_status_id' => '']);
					else
						ClaimInfoV1::where('claim_number', $claimId)->update(['status' => $type, 'hold_reason_id'=>'0','sub_status_id' => '']);
				}
			}
		}
	}
	
	#======================================================================================#
	#								End Claim claims status change						   #
	#======================================================================================#
	
	public function UpdateInsuCategory(){
		/* Uploading insurance category  */
		$pmtinfo = PMTClaimTXV1::where('pmt_method','Insurance')->where('ins_category','!=',0)->where('ins_category','!=','')->get();
		foreach($pmtinfo as $list){
			$category = PatientInsurance::where('patient_id',$list->patient_id)->where('insurance_id',$list->ins_category)->whereIn('category',['Primary','Secondary','Tertiary'])->value('category');
			if(!empty($category))
				PMTClaimTXV1::where('id',$list->id)->update(['ins_category'=>$category]);
		}
	}
	
}