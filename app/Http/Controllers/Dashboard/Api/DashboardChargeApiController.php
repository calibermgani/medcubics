<?php
namespace App\Http\Controllers\Dashboard\Api;
use App\Http\Controllers\Controller;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\Payments\ClaimCPTInfoV1 as ClaimCPTInfoV1;
use App\Models\Patients\Payment as Payment;
use App\Models\Provider as Provider;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Users as User; 
use App\Models\Eras;
use Input;
use Auth;
use Response;
use Request;
use App;
use DB;
use Lang;
use Carbon\Carbon;
use App\Models\Patients\Claimdoscptdetail;
use App\Models\Insurance;
use App\Models\Facility;
use App\Traits\ClaimUtil;
use Config;

class DashboardChargeApiController extends Controller {
	 use ClaimUtil;
    
    /* Dashboard Top: Starts */
    
    //Dashboard Top: Un Billed Charges
    //Desc: Total Billed Charges of the practice
    //Tech: Sum of total_charges: Claims which are not submitted even once, and claims whose Status are 'Hold' and 'Pending'
    public function getChargeAnalyticsApi($month_selection = null)
    {			
		/*$month_selection = date('m');
					
		$data['Billed'] = ClaimInfoV1::whereNotIn('status', ['Hold'])->select(DB::raw('count(*) as claim_count'), DB::raw('sum(total_charge) as total_charge'))->whereRaw('MONTH(created_at) = '.$month_selection .' AND YEAR(created_at) = YEAR(CURDATE())')->first()->toArray();
		$data['Hold'] = ClaimInfoV1::where('status', 'Hold')->select(DB::raw('count(*) as claim_count'), DB::raw('sum(total_charge) as total_charge'))->first()->toArray();
		$data['Rejection'] = ClaimInfoV1::where('status', 'Rejection')->select(DB::raw('count(*) as claim_count'), DB::raw('sum(total_charge) as total_charge'))->first()->toArray();	
		$data['Unbilled'] = ClaimInfoV1::where('claim_submit_count', '<', 1)
        ->where('status', 'Ready')
        ->where('self_pay', '!=', 'Yes')
        ->where('insurance_id', '!=', 0)        
		->select(DB::raw('count(*) as claim_count'), DB::raw('sum(total_charge) as total_charge'))->first()->toArray();	

		$data['UnbilledCurrent'] = ClaimInfoV1::where('claim_submit_count', '<', 1)
        ->where('status', 'Ready')
        ->where('self_pay', '!=', 'Yes')
        ->where('insurance_id', '!=', 0)
        ->whereRaw('MONTH(created_at) = '.$month_selection .' AND YEAR(created_at) = YEAR(CURDATE())')
		->select(DB::raw('count(*) as claim_count'), DB::raw('sum(total_charge) as total_charge'))->first()->toArray();
		$data['Billed']['total_charge'] = $data['Billed']['total_charge'] - $data['UnbilledCurrent']['total_charge'];
		$data['Billed']['claim_count'] = $data['Billed']['claim_count'] - $data['UnbilledCurrent']['claim_count'];	*/	
		$month_selection = date('m');	
		$data = $this->getChargeStatuswiseData();

		$data['percentage'] = $this->getChargePercentage();		

		$insuranceData = $this->getInsuranceDataApi();

		$total_charge_percentage = $this->getChargePercentageApi($month_selection);	
		
		$clean_claim = $this->cleanClaimApi();		
		$topCPT = $this->getTopCptApi();
		$performanceData = $this->getPerformanceManagementApi();
		return Response::json(compact('data', 'insuranceData', 'topCPT', 'performanceData', 'total_charge_percentage', 'clean_claim'));	
               
    }
    public function getChargeStatuswiseData()
    {    
        $month_selection = date('m');	
		
    	//$Unbilled_selector = ClaimInfoV1::where('claim_submit_count', '<', 1)->where('status', 'Ready')->select(DB::raw('count(*) as claim_count'));
    	// $billed_claim_count  = ClaimInfoV1::whereNotIn('status', ['Hold'])->select(DB::raw('count(*) as claim_count'))->whereRaw('MONTH(created_at) = '.$month_selection .' AND YEAR(created_at) = YEAR(CURDATE())')->pluck("claim_count");
    	//$unbilled_charge = $this->getClaimUnbilledTotalCharge();
				
		$billed_charges = $this->getClaimBilledTotalCharge_statsnew();
		$data['Billed']['total_charge'] =  preg_replace("/[^A-Za-z0-9\-.]/", "", @$billed_charges['total_amount']);
		$data['Billed']['claim_count'] =   $billed_charges['total_charges'];
		   	
		
		$claimstats = $this->getClaimStats('all');  				
    	$data['Unbilled']['total_charge'] =  preg_replace("/[^A-Za-z0-9\-.]/", "", @$claimstats['unbilled']['total_amount']);
		$data['Unbilled']['claim_count'] =  @$claimstats['unbilled']['total_charges'];
				
		$data['Rejection']['total_charge'] =   preg_replace("/[^A-Za-z0-9\-.]/", "", $claimstats['rejected']['total_amount']);
		$data['Rejection']['claim_count'] = $claimstats['rejected']['total_charges'];

		$data['Hold']['total_charge'] =  preg_replace("/[^A-Za-z0-9\-.]/", "", $claimstats['hold']['total_amount']);
		$data['Hold']['claim_count'] = $claimstats['hold']['total_charges'];

		return $data;    		    		
    }
    public function getChargePercentage()
    {
    	$data['Unbilled'] = $this->getCurrentMonthUnbilledPercentage();

    	$data['Rejection'] = $this->getCurrentMonthEdiRejectionPercentage();

    	$data['Billed'] = $this->getCurrentMonthBilledPercentage();

    	$data['Hold'] = $this->getCurrentMonthHoldPercentage();

    	return $data;
    	
    }
    public function getPercentageData($data_value) {
        $percentage = [];   	
	//	$condition_claim = ClaimInfoV1::select( DB::raw('sum(total_charge) as total_charge'))->whereRaw('MONTH(created_at) = ' . Carbon::today()->subMonths(1)->month . ' AND YEAR(created_at) = YEAR(CURDATE())');			
		$data['Billed'] = ClaimInfoV1::select( DB::raw('sum(total_charge) as total_charge'))
                        ->whereRaw('MONTH(created_at) = ' . Carbon::today()->subMonths(1)->month . ' AND YEAR(created_at) = YEAR(CURDATE())')
                        ->where(function($qry){
                            $qry->where(function($query){ 
                                $query->where('insurance_id', '!=', 0)->where('claim_submit_count', '>' ,0);
                            })->orWhere('insurance_id', '=', 0);
                        })->pluck("total_charge")->first();  

		$data['Hold'] = ClaimInfoV1::select( DB::raw('sum(total_charge) as total_charge'))
                        ->whereRaw('MONTH(created_at) = ' . Carbon::today()->subMonths(1)->month . ' AND YEAR(created_at) = YEAR(CURDATE())')
                        ->where('status', 'Hold')->pluck("total_charge")->first();	

		$data['Rejection'] = ClaimInfoV1::select( DB::raw('sum(total_charge) as total_charge'))
                            ->whereRaw('MONTH(created_at) = ' . Carbon::today()->subMonths(1)->month . ' AND YEAR(created_at) = YEAR(CURDATE())')
                            ->where('claim_submit_count', '>', 0)->where('status', 'Rejection')->pluck("total_charge")->first();	

		$data['Unbilled'] = ClaimInfoV1::select( DB::raw('sum(total_charge) as total_charge'))
                            ->whereRaw('MONTH(created_at) = ' . Carbon::today()->subMonths(1)->month . ' AND YEAR(created_at) = YEAR(CURDATE())')
                            ->where('claim_submit_count', '<', 1)
                            ->where('insurance_id', '!=', 0)
                        ->pluck("total_charge")->first();

       //$data['Billed'] = $data['Billed'] - $data['Unbilled'];      
		foreach($data as $key => $value)
		{
		    $current_value = $data_value[$key]['total_charge'];	
		    $last_value = $data[$key];		    	
			$percentage[$key] = ($current_value != 0 && $current_value != '')?round((($current_value - $last_value)/$current_value) *100):"0.00";
		}
		return $percentage;
    }

	public function getChargePercentageApi($month_selection = null)
    {
        $last_month = ClaimInfoV1::whereRaw('MONTH(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)')->sum('total_charge');                
       	$current_month = ClaimInfoV1::whereRaw('MONTH(created_at) = '.$month_selection.' AND YEAR(created_at) = YEAR(CURDATE())')->sum('total_charge');	       
		$curr = $current_month - $last_month;
        return $current_month == 0 ? 0 : round(($curr/$current_month)*100, 2);
    }

    public function cleanClaimApi()	{
       return $this->getCleanClaims();
	}

	public function getconditionPayment($type="Patient"){		  
	   if($type == "Patient"){
	   		$paid_data = DB::raw("sum(pmt_claim_tx_v1.total_paid) as patient_paid");
	   		$method = ["Patient"];
	   } else{
	   		$paid_data = DB::raw("sum(pmt_claim_tx_v1.total_paid) as insurance_paid");
	   		$method = ["Insurance"];
	   }		   
	   $chargeBar =  DB::table('claim_info_v1')
                    ->join('pmt_claim_tx_v1', 'claim_info_v1.id', '=', 'pmt_claim_tx_v1.claim_id')           
                    ->orderBy("pmt_claim_tx_v1.created_at")
                    ->select(DB::raw("DATE_FORMAT(pmt_claim_tx_v1.created_at,'%b') as monthNum"), $paid_data)
                    ->whereIn('pmt_claim_tx_v1.pmt_method', $method) 
                    ->groupBy(DB::raw("month(pmt_claim_tx_v1.created_at)"))           
                    ->get('patient_paid', 'monthNum', 'insurance_paid'); 
        return $chargeBar;
	}

    public function getInsuranceDataApi(){
	    $chargeBar_patient =  $this->getconditionPayment("Patient");		
	    $chargeBar_insurance =  $this->getconditionPayment("Insurance");		          
        $chargeBar_insurance = json_decode(json_encode($chargeBar_insurance), true);

        $chargeBar_patient = json_decode(json_encode($chargeBar_patient), true); 
       
        $date_array = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        $Charge_val = [];	           
        for($i = 0; $i<count($date_array); $i++){  
    		$search_val_ins = array_search($date_array[$i], array_column($chargeBar_insurance, 'monthNum'));
    		$search_val_pat = array_search($date_array[$i], array_column($chargeBar_patient, 'monthNum'));        		
    		if($search_val_ins !== false) {        			
    			$Charge_val['Insurance'][$date_array[$i]] = ["value" =>$chargeBar_insurance[$search_val_ins]['insurance_paid']];
    		} else{        			
    			$Charge_val['Insurance'][$date_array[$i]] = ["value" =>"0.00"];
    		}
    		if($search_val_pat !== false) {
    			$Charge_val['Patient'][$date_array[$i]] = ["value" =>$chargeBar_patient[$search_val_pat]['patient_paid']];
    			
    		} else{
    			$Charge_val['Patient'][$date_array[$i]] = ["value" =>"0.00"];
    			
    		}
    		
        }                  
       return   $Charge_val;     
	} 

/*	public function getInsuranceDataApi(){
		 $chargeBar_patient = DB::table('claim_info_v1')->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
		  ->select(DB::raw("DATE_FORMAT(claim_info_v1.created_at,'%b') as monthNum"), 
		  	DB::raw('sum(pmt_claim_fin_v1.patient_paid) as patient_paid'),
		  	DB::raw('sum(pmt_claim_fin_v1.insurance_paid) as insurance_paid'))
            ->orderBy("claim_info_v1.created_at")
            ->groupBy(DB::raw("month(claim_info_v1.created_at)"))
            ->whereNotIn('claim_info_v1.status', ['Hold'])    
             ->whereNull('claim_info_v1.deleted_at')                  
            ->get('patient_paid', 'insurance_paid', 'monthNum'); 
            DB::table('claim_info_v1')
            ->join('pmt_claim_tx_v1', 'claim_info_v1.id', '=', 'pmt_claim_tx_v1.claim_id')           
            ->orderBy("pmt_claim_tx_v1.created_at")
            ->select(DB::raw("DATE_FORMAT(pmt_claim_tx_v1.created_at,'%b') as monthNum"), DB::raw("sum(pmt_claim_tx_v1.total_paid) as patient_paid"))
            ->whereIn('pmt_claim_tx_v1.pmt_method', ['Patient']) 
            ->groupBy(DB::raw("month(pmt_claim_tx_v1.created_at)"))           
            ->get('patient_paid', 'monthNum');             

             $chargeBar_insurance = 
            DB::table('claim_info_v1')
            ->join('pmt_claim_tx_v1', 'claim_info_v1.id', '=', 'pmt_claim_tx_v1.claim_id')           
            ->orderBy("pmt_claim_tx_v1.created_at")
            ->select(DB::raw("DATE_FORMAT(pmt_claim_tx_v1.created_at,'%b') as monthNum"), DB::raw("sum(pmt_claim_tx_v1.total_paid) as insurance_paid"))
            ->whereIn('pmt_claim_tx_v1.pmt_method', ['Insurance']) 
            ->groupBy(DB::raw("month(pmt_claim_tx_v1.created_at)"))           
            ->get('insurance_paid', 'monthNum'); 
            // print_( $chargeBar_insurance );
           // dd( $chargeBar_insurance,$chargeBar_patient );
            $chargeBar = array_merge((array) $chargeBar_patient, (array) $chargeBar_insurance);

        // dd( $chargeBar );
            $chargeBar_insurance = json_decode(json_encode($chargeBar_insurance), true);
            $chargeBar_patient = json_decode(json_encode($chargeBar_patient), true);
           // $chargeBar = json_decode(json_encode($chargeBar), true);   
 
            $date_array = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            $Charge_val = [];	
            for($i = 0; $i<count($date_array); $i++){  
        		$search_val = array_search($date_array[$i], array_column($chargeBar, 'monthNum'));        		
        		if($search_val !== false) {
        			$Charge_val['Patient'][$date_array[$i]] = ["value" =>$chargeBar[$search_val]['patient_paid']];
        			$Charge_val['Insurance'][$date_array[$i]] = ["value" =>$chargeBar[$search_val]['insurance_paid']];
        		} else{
        			$Charge_val['Patient'][$date_array[$i]] = ["value" =>"0.00"];
        			$Charge_val['Insurance'][$date_array[$i]] = ["value" =>"0.00"];
        		}

            }             
           return   $Charge_val;     
	} */
	public function getTopCptApi()  {
		$TopCpts["Year"] = $this->getTopCptByCreated("Year");
		$TopCpts["Month"] = $this->getTopCptByCreated("Month");
		return $TopCpts;
	}

	public function getTopCptByCreated($type="Year")
	{
        $cpt_limit = 10;             
		$current = ClaimCPTInfoV1::groupBy('cpt_code')->where('cpt_code', '!=', '')->selectRaw("sum(charge) as total_charge, cpt_code, sum(unit) as cpt_unit")->orderby('cpt_unit', "desc");
		$prev = ClaimCPTInfoV1::groupBy('cpt_code')->where('cpt_code', '!=', '')->selectRaw("sum(charge) as total_charge, cpt_code, sum(unit) as cpt_unit")->orderby('cpt_unit', "desc");		
		if($type == "Year")	{
			$data['current'] = $current->whereRaw('YEAR(created_at) = YEAR(CURDATE())')->take($cpt_limit)->get('total_charge', 'cpt_code' ,'cpt_unit')->toArray();
			$data['prev'] = $prev->whereRaw('YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))')->take($cpt_limit)->get('total_charge', 'cpt_code' ,'cpt_unit')->toArray();
        } else {
        	$data['current'] =  $current->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURDATE())')->take($cpt_limit)->get('total_charge', 'cpt_code' ,'cpt_unit')->toArray();
        	$data['prev'] = $prev->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(CURDATE())')->take($cpt_limit)->get('total_charge', 'cpt_code' ,'cpt_unit')->toArray();
        }
        $cptData = [];
        for($i=0;$i<count($data['current']);$i++) {
        	$cpt_code = $data['current'][$i]['cpt_code'];
        	$cpt_unit = $data['current'][$i]['cpt_unit'];
        	$total_charge = $data['current'][$i]['total_charge'] ;       	
        	$search_val = array_search($cpt_code, array_column($data['prev'], "cpt_code"));			
        	$cptData["current"][$i] = ['value' => $cpt_unit, 'toolText' => "U: ".$cpt_unit.',  $'.$total_charge]; 
        	$cptData["label_data"][$i] = ['label' => $cpt_code];       	
			$cptData["previous"][$i] = ($search_val !== false)?['value' => $data['prev'][$search_val]['cpt_unit'], 'toolText' => "U: ".$data['prev'][$search_val]['cpt_unit'].', $'.$data['prev'][$search_val]['total_charge']]:['value' =>"0.00",  'toolText' => "0.00"];        	
        }       
        return $cptData;    
	}	
	
	public function getPerformanceManagementApi($model = 'billing_provider_id')
	{		   
		$arr_relation = ['billing_provider_id' => 'billingclaims', 'facility_id' => 'claim_data', 'rendering_provider_id' => 'renderingclaims'];
		$arr_model = ['billing_provider_id' => 'App\Models\Provider', 'facility_id' => 'App\Models\Facility', 'rendering_provider_id' => 'App\Models\Provider'];
		$limit = 10;
		$relation_data = $arr_relation[$model];		
		$Model = $arr_model[$model];
		$performance_data = $Model::Has($relation_data)->with([$relation_data => function($query) use ($model){
            						$query->select(DB::raw("DATE_FORMAT(created_at,'%M') as monthNum"), DB::raw("sum(total_charge) as total_charge"), $model)->groupBy(DB::raw("month(created_at)"),$model );
            				}])->where(function($query) use ($model){
                                if($model == 'billing_provider_id'){
                                     $query->where('provider_types_id', Config::get('siteconfigs.providertype.Billing'));
                                }else if($model == 'rendering_provider_id'){
                                     $query->where('provider_types_id', Config::get('siteconfigs.providertype.Rendering'));
                                } 
                            })->get()->toArray();	        
		$claimMonth = [];
		$data_final = [];
		$date_array = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');		
		foreach($performance_data as $data){			
			$ClaimData = $data[$relation_data];				
			foreach($ClaimData as $claim){
					$claimMonth[$data['short_name']][$claim['monthNum']]	= $claim['total_charge'];
			}			
		}
		foreach($claimMonth as $key=> $data_value){			
			foreach($date_array as $key_data => $date){
				 if(in_array($date, array_keys($data_value))) {
					$value =  $data_value[$date];
				 } else {
					 $value =  "0.00";
				 }
				$data_final[$key][$date] =  $value ;
			}			
		}			
		return $data_final;
	}

	/*public function getarray($){

	}*/
	/*public function getPerformanceManagementApiNew()
	{		
		$Chargedetail = Claims::select(DB::raw('sum(total_charge) as `total_charge`'), DB::raw("DATE_FORMAT(created_at,'%M') as monthNum"))
					->orderBy("created_at")
					->groupBy(DB::raw("month(created_at)"))
					->whereNotIn('status', ['Hold']);							
		$data['Unbilled'] = $Chargedetail->has('paymentclaimtransaction', '<', 1)->pluck('total_charge', 'monthNum')->first();
		$data['Billed'] = Claims::select(DB::raw('sum(total_charge) as `total_charge`'), DB::raw("DATE_FORMAT(created_at,'%M') as monthNum"))
					->orderBy("created_at")
					->groupBy(DB::raw("month(created_at)"))
					->whereNotIn('status', ['Hold'])->pluck('total_charge', 'monthNum')->all();
		return $this->getBilledMonth($data);

	}	
	public function getBilledMonth($data){
		$monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
			$i=0;
			$chargeArray= [];
		    foreach($data as $key=>$value) {
				$chargeArray[$i]['seriesname']= $key;
				$j = 0;
		    foreach ($monthArray as $month) {
                        $chargeArray[$i]['data'][$j] = in_array($month, array_flip($value)) ? ['value' => $value[$month]] : ['value' =>0];
						$j++;
                    }
					$i++;
			}	
		return json_encode($chargeArray);	
	}
	public function getInsurancePiechartApi(){		
		$insurance_limit = 10;
		$insurance_lists = Claims::has('insurance_details')->with('insurance_details')->selectRaw("sum(total_charge) as total_charge_amt, insurance_id")
		->groupBy('insurance_id')->take($insurance_limit)->orderBY('total_charge_amt', 'desc')->get();
		$data_insurance = [];
		foreach($insurance_lists as $key =>$insurance_list) {
				$data_insurance[$key]['label'] = $insurance_list->insurance_details->short_name;
				$data_insurance[$key]['value'] = $insurance_list->total_charge_amt;
				$data_insurance[$key]['issliced'] = 0;
		}
		return json_encode($data_insurance);		
		
	}
	
	
	public function getInsurancewiseTotalChargeApi()
	{	
	    $insurance_limit = 10;		
		$insurance_lists = Insurance::has('claims')->with(['claims' =>function($query){
				$query->select(DB::raw('sum(total_charge) as `total_charge`'), DB::raw("DATE_FORMAT(created_at,'%M') as monthNum"), 'insurance_id')
					 ->orderBy("created_at")					 
					 ->groupBy(DB::raw("month(created_at)"))
					 ->whereNotIn('status', ['Hold']);					
	   }])->take($insurance_limit)->get();	 
	   /*$queue = DB::table('insurances')->select('insurances.id','insurances.short_name',DB::raw('sum(claims.total_charge) as `total_charge`'), DB::raw("DATE_FORMAT(claims.created_at,'%M') as monthNum"))
        ->join('claims','claims.insurance_id','=','insurances.id')
		->orderBy("total_charge", 'desc')
		->groupBy("insurances.id")		
        ->get();
		dd($queue);	
		$queue = DB::table('claims')->select('insurances.id','insurances.short_name',DB::raw('sum(claims.total_charge) as `total_charge`'), DB::raw("DATE_FORMAT(claims.created_at,'%M') as monthNum"))
        ->join('insurances','insurances.id','=','claims.insurance_id') 		
		->groupBy('claims.insurance_id')
		->groupBy(DB::raw("month(claims.created_at)"))
		->orderBy("total_charge")
		->whereNotIn('claims.status', ['Hold'])		
        ->get();
	   //dd($queue);	  
	    foreach($insurance_lists as $insurance_list){
			$insurance_list_data[$insurance_list->short_name] = $insurance_list->claims->pluck('total_charge', 'monthNum')->all();			
	    }
		dd($insurance_list_data);
	}*/
	
}