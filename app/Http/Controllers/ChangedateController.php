<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Http\Helpers\Helpers as Helpers;
use Response;
use View;
use Auth;
use App;
use Input;

class ChangedateController extends Controller {
	public function __construct() {
        View::share('heading', 'Practice');
        View::share('selected_tab', 'changedate');
        View::share('heading_icon', 'fa-medkit');
    }

	function changecreatedDate($type){
        $claims = [];
        $payments = [];
        if($type == "charges")
        {
            $claims = ClaimInfoV1::orderBy('created_at', 'desc')->limit(1000)->get();
        } else
        {
            $payments = PMTInfoV1::orderBy('created_at', 'desc')->limit(1000)->get();
        }
        return view("charges/charges/changedate", compact('payments','claims'));

    }

    function postCreatedDateApi($type, $id, $date){
        $claims = [];
        $payments = [];
        $date  = date("Y-m-d",strtotime(base64_decode($date)));      
        $id = Helpers::getEncodeAndDecodeOfId($id,'decode');          
        if($type == "charges")
        {            
            $claim_detail = ClaimInfoV1::with('patient')->findorFail($id);           
            $claim_detail->created_at = $date;
            $claim_detail->claim_unit_details->created_at = $date;
            $claim_detail->claim_details->created_at = $date;
            $patient_exist_date = $claim_detail->patient->created_at;
            if(date('d/m/Y', strtotime($claim_detail->patient->created_at)) < date('d/m/Y', strtotime($date))) 
            {            
            	$claim_detail->patient->created_at = $date;	
            }            
            $claim_detail->push();
        } 
        else
        {
            $payment_detail = PMTInfoV1::findorFail($id);
            $payment_detail->created_at = $date;
            $payment_detail->payment_claim_detail->created_at = $date;
            $payment_detail->payment_claim_cpt_data->created_at = $date;
            $payment_detail->push(); 
        }
        $claims = ClaimInfoV1::orderBy('created_at', 'desc')->limit(1000)->get();       
        $payments = PMTInfoV1::orderBy('created_at', 'desc')->limit(1000)->get();
      	return view("charges/charges/changenewdate", compact('payments','claims','type'));
    }

}
