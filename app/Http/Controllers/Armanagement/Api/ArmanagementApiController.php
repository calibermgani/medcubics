<?php

namespace App\Http\Controllers\Armanagement\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Patients\Patient as Patient;
use App\Models\Insurance as Insurance;
use App\Models\Facility as Facility;
use App\Models\Provider as Provider;
use App\Models\Patients\PatientNote as PatientNote;
use App\Models\FollowupCategory as FollowupCategory;
use App\Models\FollowupQuestion as FollowupQuestion;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Patients\PatientInsurance;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\Payments\PMTClaimCPTFINV1;
use App\Models\Holdoption as Holdoption;
use App\Models\ClaimSubStatus as ClaimSubStatus;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use Carbon\Carbon;
use Response;
use Request;
use Session;
use Auth;
use Config;
use DB;
use App\Traits\ClaimUtil;
use Log;
use DateTime;
use App\Http\Helpers\Helpers as Helpers;

class ArmanagementApiController extends Controller {
    use ClaimUtil;
    public function getIndexApi() {
		// Getting submitted count for ar management dashboard
		// Revision 1 : MEDV2-279 : Selva : 04 Nov 2019
		$claim_status_count = 	DB::table('claim_info_v1')->select(
								DB::raw('sum(status = "Hold") hold_count'),
								DB::raw('sum(status = "Rejection") rejection_count'),
								DB::raw('sum(status = "Denied") denied_count'),
								DB::raw('sum(status = "Pending") pending_count'),
								DB::raw('sum(status = "Submitted") submitted_count')
								)->whereNull('deleted_at')->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claim_status_count')));
    }
    public function getIndex1Api() {
        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
    }

    public function getInsuranceApi() {
        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
    }

    public function getInsurancewiseApi() {
        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
    }

    public function getInsclaimsApi() {
        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
    }

    public function getStatuswiseApi() {
        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
    }

    public function getInsurance1Api() {
        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
    }

    public function getarmanagementAjaxlistApi($export = '') {

        $request = Request::all();

        $start = isset($request['start']) ? $request['start'] : 0;
        $len = (isset($request['length'])) ? $request['length'] : 50;
        $search = (!empty($request['search']['value'])) ? trim($request['search']['value']) : "";
        $orderByField = 'claim_info_v1.id';
        $orderByDir = 'DESC';
		// Added condition for deleted at
        $query = ClaimInfoV1::where('claim_info_v1.id', '<>', 0)->whereNull('claim_info_v1.deleted_at');
        $query->selectRaw('claim_info_v1.*');

        $query->with('patient', 'rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail','pmtClaimFinData','followup_details', 'claim_sub_status');

        $query->join('patients', function($join) {
            $join->on('patients.id', '=', 'claim_info_v1.patient_id');
        });

        $query->join(DB::raw("(SELECT      
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
          ) as pmt_claim_fin_v1"), function($join) {
            $join->on('pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id');
        });

        $query->leftjoin('providers as rendering_provider', function($join) {
            $join->on('rendering_provider.id', '=', 'claim_info_v1.rendering_provider_id');
            $join->on('rendering_provider.provider_types_id', '=', DB::raw('1'));
        });

        $query->leftjoin('insurances', function($join) {
            $join->on('insurances.id', '=', 'claim_info_v1.insurance_id');
        });

        $query->leftjoin('facilities', function($join) {
            $join->on('facilities.id', '=', 'claim_info_v1.facility_id');
        });
		
		
		/* Selvakumar Written Code for Search :: Date 21-JUN-2018 */
		
		if(!empty(json_decode(@$request['dataArr']['data']['insurance_id'])))
            $query->whereIn('claim_info_v1.insurance_id', json_decode($request['dataArr']['data']['insurance_id']));
		
		if(!empty(json_decode(@$request['dataArr']['data']['dos']))){
			$date = explode('-',json_decode($request['dataArr']['data']['dos']));
            $from = date("Y-m-d", strtotime($date[0]));
			if($from == '1970-01-01'){
				$from = '0000-00-00';
			}
            $to = date("Y-m-d", strtotime($date[1]));
            $query->whereBetween('claim_info_v1.date_of_service', [$from, $to]);
        }
		
		if(!empty(json_decode(@$request['dataArr']['data']['claim_no'])))
            $query->where('claim_info_v1.claim_number', json_decode($request['dataArr']['data']['claim_no']));
		
        if(!empty(json_decode(@$request['dataArr']['data']['account_no'])))
            $query->where('patients.account_no', json_decode($request['dataArr']['data']['account_no']));
		
		
		if(!empty(json_decode(@$request['dataArr']['data']['patient_name']))){
			$dynamic_name = json_decode($request['dataArr']['data']['patient_name']);
			$query->Where(function ($query) use ($dynamic_name) {
				if (strpos(strtolower($dynamic_name), ",") !== false) {
					$searchValues = array_filter(explode(", ", $dynamic_name));
                    // AR Management: Claim listing:Patient search: Patient name not search according to the inputs, comma separated name search exact match condition
                    // Rev. 1 - Ref. MR-2825 - Ravi - 10-09-2019
                    $query->Where(function ($qry) use ($dynamic_name) {
                        $qry->Where(function ($query) use ($dynamic_name) {
                            $query = $query->orWhere(DB::raw('CONCAT(patients.last_name,", ", patients.first_name)'),  'like', "%{$dynamic_name}%" );
                        });
                    });
                    /*
					foreach ($searchValues as $value) {
						if ($value !== '') {
							$query = $query->orWhere('patients.last_name', 'like', "%{$value}%")
									->orWhere('patients.middle_name', 'like', "%{$value}%")
									->orWhere('patients.first_name', 'like', "%{$value}%");
						}
					}
                    */
				} else {
					$query = $query->orWhere('patients.last_name', 'LIKE', '%' . $dynamic_name . '%')
							->orWhere('patients.middle_name', 'LIKE', '%' . $dynamic_name . '%')
							->orWhere('patients.first_name', 'LIKE', '%' . $dynamic_name . '%');
				}
			});
		}
		
		if (!empty(json_decode(@$request['dataArr']['data']['rendering_provider_id'])))
            $query->whereIn('claim_info_v1.rendering_provider_id', json_decode($request['dataArr']['data']['rendering_provider_id']));
		
		if(!empty(json_decode(@$request['dataArr']['data']['insurance_id'])))
            $query->whereIn('claim_info_v1.insurance_id', json_decode($request['dataArr']['data']['insurance_id']));
		
		if (!empty(json_decode(@$request['dataArr']['data']['facility_id'])))
            $query->whereIn('claim_info_v1.facility_id', json_decode($request['dataArr']['data']['facility_id']));

		// Change by Baskar - 09/01/19 - Start
		
        if (isset($request['dataArr']['data']['charge_amt'])) {  
            $req = json_decode($request['dataArr']['data']['charge_amt']);
            if($req!==''){
                $data = Helpers::paymentFilter($req);
                $query->where('pmt_claim_fin_v1.total_charge', $data['condition'],$data['val']);
            }
        }
        if (isset($request['dataArr']['data']['paid'])) {  
            $req = json_decode($request['dataArr']['data']['paid']);
            if($req!==''){
                $data = Helpers::paymentFilter($req);
                $query->where('tot_paid', $data['condition'],$data['val']);
            }
        }
        if (isset($request['dataArr']['data']['ar_due'])) {  
            $req = json_decode($request['dataArr']['data']['ar_due']);
            if($req!==''){
                $data = Helpers::paymentFilter($req);
                $query->where('tot_ar', $data['condition'],$data['val']);
            }
        }
        if (isset($request['dataArr']['data']['pat_ar'])) {  
            $req = json_decode($request['dataArr']['data']['pat_ar']);
            if($req!==''){
                $data = Helpers::paymentFilter($req);
                $query->where('pmt_claim_fin_v1.patient_due', $data['condition'],$data['val']);
            }
        }
        if (isset($request['dataArr']['data']['ins_ar'])) {  
            $req = json_decode($request['dataArr']['data']['ins_ar']);
            if($req!==''){
                $data = Helpers::paymentFilter($req);
                $query->where('pmt_claim_fin_v1.insurance_due', $data['condition'],$data['val']);
            }
        }
        // Change by Baskar - 09/01/19 - End
		
		if (!empty(json_decode(@$request['dataArr']['data']['status']))) {
			if(!in_array('All' , json_decode($request['dataArr']['data']['status']))){
				$query->whereIn('claim_info_v1.status', json_decode($request['dataArr']['data']['status']));
                $statusArr = json_decode(@$request['dataArr']['data']['status']);
                    if(in_array("Hold", $statusArr)) {
                        if (!empty(json_decode(@$request['dataArr']['data']['hold_reason']))){
                            $holdReasonArr = (array)json_decode(@$request['dataArr']['data']['hold_reason']);
                            $query->whereIn('claim_info_v1.hold_reason_id', $holdReasonArr); 
                        }                
                    }
            }
        }    
		
        if (!empty(json_decode(@$request['dataArr']['data']['status_reason']))) {
			$status_code = (array)json_decode($request['dataArr']['data']['status_reason']);
            $query->whereIn('claim_info_v1.sub_status_id', $status_code);
        }
		/*
        if (count(array_filter(json_decode(@$request['dataArr']['data']['status_reason'])))!=count(json_decode(@$request['dataArr']['data']['status_reason']))) {
            $query->orWhereNull('claim_info_v1.sub_status_id');
        }*/

		/* Selvakumar Written Code for Search :: Date 21-JUN-2018 */

        if (@$request['billing_provider_id'] != '') {
            $query->where('billing_provider_id', $request['billing_provider_id']);
        }

        if (@$request['rendering_provider_id'] != '') {
            $query->where('billing_provider_id', $request['rendering_provider_id']);
        }

        if (@$request['referring_provider_id'] != '') {
            $query->where('refering_provider_id', $request['referring_provider_id']);
        }

        if (@$request['insurance_id'] != '') {
            $query->where('insurance_id', $request['insurance_id']);
        }

        if (@$request['facility_id'] != '') {
            $query->where('facility_id', $request['facility_id']);
        }

        if (@$request['patient_id'] != '') {
            $query->where('patient_id', $request['patient_id']);
        }

        if (@$request['dos_from'] != '' && @$request['dos_to'] != '') {
            $from = date("Y-m-d", strtotime($request['dos_from']));
            $to = date("Y-m-d", strtotime($request['dos_to']));
            $query->whereBetween('date_of_service', [$from, $to]);
        }

        if (@$request['billed_option'] != '' && @$request['billed'] != '') {
            if ($request['billed_option'] == 'lessthan')
                $billed_option = '<';
            elseif ($request['billed_option'] == 'lessequal')
                $billed_option = '<=';
            elseif ($request['billed_option'] == 'equal')
                $billed_option = '=';
            elseif ($request['billed_option'] == 'greaterthan')
                $billed_option = '>';
            elseif ($request['billed_option'] == 'greaterequal')
                $billed_option = '>=';
            else
                $billed_option = '=';
            $billed_amount = @$request['billed'];

            $query->whereHas('dosdetails', function ($q) use ($billed_amount, $billed_option) {
                $q->select(DB::raw("SUM(charge) as total_charge"))
                        ->groupBy('claim_id')
                        ->having('total_charge', $billed_option, $billed_amount)
                        ->where('is_active', 1);
            });
        }

        if (!empty($search)) {
            $query->Where(function ($query) use ($search) {
                $query->Where(function ($query) use ($search) {
                    // dos search
                    $searchValues = array_filter(explode(",", $search));
                    foreach ($searchValues as $searchKey) {
                        if (strpos(strtolower($searchKey), "/") !== false) {
                            $dateSearch = date("Y-m-d", strtotime(@$searchKey));
                            $query = $query->orWhere('date_of_service', 'LIKE', '%' . $dateSearch . '%');
                        } else {
                            // $query = $query->orWhere('date_of_service', 'LIKE', '%'.$searchKey.'%'); 
                        }

                        // claim number search
                        $query = $query->orWhere('claim_number', 'LIKE', '%' . $searchKey . '%');

                        // billed_amount    
                        $query->orWhere('pmt_claim_fin_v1.total_charge', 'LIKE', '%' . $search . '%');

                        // Paid
                        $query->orWhere('tot_paid', 'LIKE', '%' . $search . '%');
                        $query->orWhere('total_due', 'LIKE', '%' . $search . '%');
                        
						// ar_due.
                      //  $query->orWhere('balance_amt', 'LIKE', '%' . $search . '%');
                        $query->orWhere('tot_ar', 'LIKE', '%' . $search . '%');

                        // Status search
                        $query = $query->orWhere('claim_info_v1.status', 'LIKE', '%' . $searchKey . '%');
                    }
                });

                // Patient name search search                
                $query->orWhere(function ($query) use ($search) {
                    $searchValues = array_filter(explode(",", $search));
                    $sub_sql = '';
                    foreach ($searchValues as $searchKey) {
                        $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                        $sub_sql .= "patients.last_name LIKE '%$searchKey%' OR patients.first_name LIKE '%$searchKey%' OR patients.middle_name LIKE '%$searchKey%' ";
                    }
                    if ($sub_sql != '')
                        $query->whereRaw($sub_sql);
                });

                // Facility                
                $query->orWhere(function ($query) use ($search) {
                    $searchValues = array_filter(explode(",", $search));
                    $sub_sql = '';
                    foreach ($searchValues as $searchKey) {
                        $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                        if (!is_numeric($search)) {
                            $sub_sql .= "facilities.short_name LIKE '%$searchKey%' ";
                        }
                    }
                    if ($sub_sql != '')
                        $query->whereRaw($sub_sql);
                });

                // Provider
                $query->orWhere(function ($query) use ($search) {

                    $searchValues = array_filter(explode(",", $search));
                    $sub_sql = '';
                    foreach ($searchValues as $searchKey) {
                        $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                        if (!is_numeric($search)) {
                            $sub_sql .= "rendering_provider.short_name LIKE '%$searchKey%' ";
                        }
                    }

                    if ($sub_sql != '') {
                        $query->whereRaw($sub_sql);
                    }
                });

                // Billed to
                $query->orWhere(function ($query) use ($search) {
                    $searchValues = array_filter(explode(",", $search));
                    $sub_sql = '';
                    foreach ($searchValues as $searchKey) {
                        $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                        $sub_sql .= "insurances.short_name LIKE '%$searchKey%' ";
                    }
                    if ($sub_sql != '')
                        $query->whereRaw($sub_sql);
                });
            });
        }

        if (!empty($request['order'])) {
            $orderField = ($request['order'][0]['column']) ? $request['order'][0]['column'] : 'claim_info_v1.id';
            switch ($orderField) {
                case '1':
                    $orderByField = 'claim_info_v1.date_of_service';        // DOS
                    break;

                case '2':
                    $orderByField = 'claim_info_v1.claim_number';           // claim_number
                    break;

                case '3':
                    $orderByField = 'patients.last_name';                   //'patient_name';
                    break;

                case '4':
                    $orderByField = 'rendering_provider.short_name';        // Provider
                    break;

                case '5':
                    $orderByField = 'facilities.short_name';                // Facility
                    break;

                case '6':
                    $orderByField = 'insurances.short_name';                // Billed To
                    break;

                case '7':
                    $orderByField = 'total_charge';                         // Billed Amount
                    break;

                case '8':
                    $orderByField = 'tot_paid';                     	   // Paid
                    break;

                case '9':
                    $orderByField = 'total_pat_due';                        // Total patient due
                    break;

                case '10':
                    $orderByField = 'total_ins_due';                        // Status
                    break;

                case '11':
                    $orderByField = 'tot_ar';                               // AR Due                    
                    break;

                case '12': 
                    $orderByField = 'claim_info_v1.status';                 // Status
                    break;

                default:
                    $orderByField = 'claim_info_v1.id';
                    break;
            }
            $orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'DESC';
        }
        $orderByField = ($orderByField != '') ? $orderByField : 'claim_info_v1.id';
        $count = $query->count(DB::raw('DISTINCT(claim_info_v1.id)'));
		// Armanagement dynamic checkbox selection  
		// Revision 1 : MR-2716 : 22 Aug 2019 : Selva 
		
		$claimsIds = $query->pluck('claim_info_v1.claim_number')->all();
		/* Armanagement  bulk notes and workbench chnaged based on all */
		/* Revision 1 : Ref: MR-2756 : 27 Aug 2019 : selva */
		$encodeClaimIds = [];
		foreach($claimsIds as $list){
			$encodeClaimIds[] = $list;
		}
        $query->groupBy('claim_info_v1.id');
        
        if($export != ''){
            $claims_list = $query->orderBy('claim_info_v1.date_of_service', 'DESC')->get();
        }else{
            $query->orderBy($orderByField, $orderByDir);
            $claims_list = $query->skip($start)->take($len)->get();
        }
        
        // New pmt flow integration start
        for ($m = 0; $m < count($claims_list); $m++) {
            if (!empty($claims_list)) {
                $claim_id = $claims_list[$m]->id;
                $total_charge = $claims_list[$m]->total_charge;
                $pmtClaimFinData = (!empty($claims_list[$m]->pmtClaimFinData)) ? $claims_list[$m]->pmtClaimFinData : [];
                $finDet = ClaimInfoV1::getClaimCptFinBalance($total_charge, $pmtClaimFinData);
                $claims_list[$m]['total_paid'] = $finDet['totalPaid'];
                $claims_list[$m]['balance_amt'] = $finDet['balance'];
            }
        } 
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list', 'count', 'encodeClaimIds')));
    }

    public function getarmanagementlistApi() {
		// Added whereNull condition on tab showing statics
		// Revision 1 : MR-2838 : 12 Sep 2019 : Selva
		$claim_status_count = 	DB::table('claim_info_v1')->select(
								DB::raw('sum(status = "Hold") hold_count'),
								DB::raw('sum(status = "Rejection") rejection_count'),
								DB::raw('sum(status = "Denied") denied_count'),
								DB::raw('sum(status = "Pending") pending_count'),
								DB::raw('sum(status = "Submitted") submitted_count')
								)->whereNull('deleted_at')->get();
		 $ClaimController  = new ClaimControllerV1();	
		 $search_fields_data = $ClaimController->generateSearchPageLoad('armanagement_listing');
		 $searchUserData = $search_fields_data['searchUserData'];
		 $search_fields = $search_fields_data['search_fields'];
		// Added hold reason for bulk hold option in armanagement
		// Revision 1 : MR-2786 : 4 Sep 2019
		$hold_options = Holdoption::where('status', 'Active')->pluck('option', 'id')->all();
        if (Request::ajax()) {

            $request = Request::all();

            $query = ClaimInfoV1::with('patient', 'rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail','pmtClaimFinData','followup_details', 'claim_sub_status')->take(500)->whereNotIn('status', ['Hold']);
            
            if (@$request['billing_provider_id'] != '') {
                $query->where('billing_provider_id', $request['billing_provider_id']);
            }

            if (@$request['rendering_provider_id'] != '') {
                $query->where('billing_provider_id', $request['rendering_provider_id']);
            }

            if (@$request['referring_provider_id'] != '') {
                $query->where('refering_provider_id', $request['referring_provider_id']);
            }

            if (@$request['insurance_id'] != '') {
                $query->where('insurance_id', $request['insurance_id']);
            }

            if (@$request['facility_id'] != '') {
                $query->where('facility_id', $request['facility_id']);
            }

            if (@$request['patient_id'] != '') {
                $query->where('patient_id', $request['patient_id']);
            }

            if (@$request['dos_from'] != '' && @$request['dos_to'] != '') {
                $from = date("Y-m-d", strtotime($request['dos_from']));
                $to = date("Y-m-d", strtotime($request['dos_to']));
                $query->whereBetween('date_of_service', [$from, $to]);
            }

            if (@$request['billed_option'] != '' && @$request['billed'] != '') {
                if ($request['billed_option'] == 'lessthan')
                    $billed_option = '<';
                elseif ($request['billed_option'] == 'lessequal')
                    $billed_option = '<=';
                elseif ($request['billed_option'] == 'equal')
                    $billed_option = '=';
                elseif ($request['billed_option'] == 'greaterthan')
                    $billed_option = '>';
                elseif ($request['billed_option'] == 'greaterequal')
                    $billed_option = '>=';
                else
                    $billed_option = '=';
                $billed_amount = @$request['billed'];

                $query->whereHas('dosdetails', function ($q) use ($billed_amount, $billed_option) {
                    $q->select(DB::raw("SUM(charge) as total_charge"))
                            ->groupBy('claim_id')
                            ->having('total_charge', $billed_option, $billed_amount)
                            ->where('is_active', 1);
                });
            }

            $claims_list = $query->orderBy('id', 'DESC')->get();

            for ($m = 0; $m < count($claims_list); $m++) {
                if (!empty($claims_list)) {
                    $claim_id = $claims_list[$m]->id;
                    $total_charge = $claims_list[$m]->total_charge;
                    $pmtClaimFinData = (!empty($claims_list[$m]->pmtClaimFinData)) ? $claims_list[$m]->pmtClaimFinData : [];
                    $finDet = ClaimInfoV1::getClaimCptFinBalance($total_charge, $pmtClaimFinData);
                    $claims_list[$m]['total_paid'] = $finDet['totalPaid'];
                    $claims_list[$m]['balance_amt'] = $finDet['balance'];
					$claims_list[$m]['patient_due'] = $finDet['patient_due'];                    
                    $claims_list[$m]['insurance_due'] = $finDet['insurance_due'];  
                }    
            } 
			
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list','claim_status_count','hold_options')));
        } else {
            $claims_query = ClaimInfoV1::with('patient', 'rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail', 'pmtClaimFinData','followup_details', 'claim_sub_status')->take(500)->orderBy('id', 'DESC');
            

			$pagination_count  = $claims_query->count();
			$claims_list = $claims_query->get();
            for ($m = 0; $m < count($claims_list); $m++) {
                if (!empty($claims_list)) {
                    $claim_id = $claims_list[$m]->id;
                    $total_charge = $claims_list[$m]->total_charge;
                    $pmtClaimFinData = (!empty($claims_list[$m]->pmtClaimFinData)) ? $claims_list[$m]->pmtClaimFinData : [];
                    $finDet = ClaimInfoV1::getClaimCptFinBalance($total_charge, $pmtClaimFinData);
                    $claims_list[$m]['total_paid'] = $finDet['totalPaid'];
                    $claims_list[$m]['balance_amt'] = $finDet['balance'];                    
                    $claims_list[$m]['patient_due'] = $finDet['patient_due'];                    
                    $claims_list[$m]['insurance_due'] = $finDet['insurance_due'];                    
                }    
            }   
			
            $practice_id = Session::get('practice_dbid');
            $practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();
            $admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
            $practice_user_arr2 = Users::whereRaw("((customer_id = ? and practice_user_type = 'customer' and status = 'Active') or ($admin_practice_id_like))", array(Auth::user()->customer_id))->pluck('id')->all();
            $practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
            $user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->pluck('name', 'id')->all();

            $patients = Patient::where('status', 'Active')->selectRaw('CONCAT(last_name,", ",first_name, " ",middle_name) as patient_name, id')->orderBy('last_name', 'ASC')->pluck('patient_name', 'id')->all();
            $billing_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
            $rendering_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')]);
            $referring_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Referring')]);
            $insurances = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
            $facility = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();

            $category = FollowupCategory::where('deleted_at', null)->where('status', 'Active')->select('id', 'name', 'label_name')->get()->toArray();

            $question = FollowupCategory::with(['question' => function($query) {
                            $query->where('deleted_at', null)->where('status', 'Active');
                        }])->where('deleted_at', null)->where('status', 'Active')->select('id', 'name', 'label_name')->get()->toArray();
        
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list', 'user_list', 'patients', 'billing_provider', 'rendering_provider', 'referring_provider', 'insurances', 'facility', 'category', 'question','claim_status_count','pagination_count','search_fields','searchUserData','hold_options')));
        }
    }


    public function getARDenialListApi() {  
        $ClaimController  = new ClaimControllerV1();            
        $search_fields_data = $ClaimController->generateSearchPageLoad('ar_deniallist');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'data' => compact('searchUserData','search_fields')));
    }

    public function getARDenialListSummaryApi() {
        $sub_status = ClaimSubStatus::getClaimSubStatusList();    

        $totalPaid = 'pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.insurance_paid';
        $totalAdjustment = 'pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_adj';
        $balance = 'pmt_claim_fin_v1.total_charge - (' . $totalPaid . '+' . $totalAdjustment . '+pmt_claim_fin_v1.withheld)';
        $practice_timezone = Helpers::getPracticeTimeZone();  
        $practice_todate = date('Y-m-d',strtotime(Carbon::now($practice_timezone)));
        $denial_list = ClaimInfoV1::select(\DB::raw('IFNULL(claim_info_v1.sub_status_id,0) AS sub_status_id'), \DB::raw('DATEDIFF("'.$practice_todate.'", date_of_service) as claim_age_days'), \DB::raw('COUNT(claim_info_v1.id) as claimCnt'), 'claim_info_v1.id as claim_id', 'claim_info_v1.status as claim_status', 'claim_info_v1.total_charge', \DB::raw("sum(" . $balance . ") as balance"))
                        
                        ->leftjoin('pmt_claim_fin_v1', function($join) {
                            $join->on('pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id');
                        })

                        ->where('claim_info_v1.status', '=', 'Denied')
                        ->groupby('claim_info_v1.id')
                        ->orderBy('claim_info_v1.id')
                        ->get();

        $collection = collect($denial_list);        
        $result = [];
        foreach ($collection as $key => $item) {
            $subStatus = ($item->sub_status_id != 0 && $item->sub_status_id <> '' && isset($sub_status[$item->sub_status_id]) ) ? $item->sub_status_id : 'N-A';
            if(isset($result[$subStatus])) {
                $result[$subStatus]['claims']+= $item->claimCnt;
                $result[$subStatus]['description'] = ($subStatus <> 0 && isset($sub_status[$item->sub_status_id]) && $sub_status[$item->sub_status_id] != '' )? $sub_status[$item->sub_status_id] : '-Nil-';
                $result[$subStatus]['claim_age_days'] += $item->claim_age_days;                
                $result[$subStatus]['balance_amt'] += $item->balance;
            } else {
                $result[$subStatus]['claims'] = $item->claimCnt;
                $result[$subStatus]['description'] = ($subStatus <> 0 && isset($sub_status[$item->sub_status_id]) && $sub_status[$item->sub_status_id] != '') ? @$sub_status[$item->sub_status_id] : '-Nil-';
                $result[$subStatus]['claim_age_days'] = $item->claim_age_days;
                $result[$subStatus]['balance_amt'] = $item->balance;
            }
            $result['total']['description'] = 'Totals';
            $result['total']['claims'] = (isset($result['total']['claims'])) ? $result['total']['claims']+$item->claimCnt : $item->claimCnt;
            $result['total']['claim_age_days'] = (isset($result['total']['claim_age_days'])) ? $result['total']['claim_age_days']+$item->claim_age_days : $item->claim_age_days;
            $result['total']['balance_amt'] = (isset($result['total']['balance_amt'])) ? $result['total']['balance_amt']+$item->balance :$item->balance;
        }
        krsort($result);
        uasort ($result, function($a, $b) { return $a['claims'] < $b['claims'] ? 1 : ($a['claims'] == $b['claims'] ? 0 : -1); });
        if(isset($result['N-A'])) {
            $result['-Nil-'] = $result['N-A'];
            unset($result['N-A']);
        }
        return $result;            
    }

    public function getARDenialListFilterApi($export = '', $data = '') {
        $request = (isset($data) && !empty($data)) ? $data : Request::All();        
        $search_by = array();
        // dd(json_decode(@$request));
        /* Converting value to default search based */
        if (isset($request['export']) && $request['export'] == 'yes') {
            foreach ($request as $key => $value) {
                if (strpos($value, ',') !== false && $key != 'patient_name') {
                    $request['dataArr']['data'][$key] = json_encode(explode(',', $value));
                } else {
                    $request['dataArr']['data'][$key] = json_encode($value);
                }
            }
        }
        /* Converting value to default search based */
        $ClaimController  = new ClaimControllerV1();   
        $search_fields_data = $ClaimController->generateSearchPageLoad('ar_deniallist');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        $practice_timezone = Helpers::getPracticeTimeZone();  
        $practice_todate = date('Y-m-d',strtotime(Carbon::now($practice_timezone)));  
        $denial_cpts = PMTClaimCPTFINV1::select('pmt_claim_cpt_fin_v1.claim_id', 
                        'pmt_claim_cpt_fin_v1.claim_cpt_info_id',
                        \DB::raw('MAX(pmt_claim_tx_v1.id) as last_txn_id'),
                       // 'pmt_claim_cpt_tx_v1.denial_code', 'pmt_claim_cpt_tx_v1.pmt_claim_tx_id', //'pmt_claim_cpt_tx_v1.claim_cpt_info_id',
                        \DB::raw('pmt_claim_cpt_fin_v1.cpt_charge-(pmt_claim_cpt_fin_v1.patient_paid + pmt_claim_cpt_fin_v1.insurance_paid +pmt_claim_cpt_fin_v1.patient_adjusted+pmt_claim_cpt_fin_v1.insurance_adjusted+pmt_claim_cpt_fin_v1.with_held) as total_ar_due'))

                        ->leftjoin('claim_info_v1', 'pmt_claim_cpt_fin_v1.claim_id', '=', 'claim_info_v1.id')

                        ->leftjoin('pmt_claim_tx_v1', function($join) {
                            $join->on('pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id');
                        })

                        ->with([ 'recClaimTxn',
                            'lastcptdenialdesc',
                            'recentCptTxn',
                            'claim' => function($query)use($practice_todate) {
                                    $query->select('id', 'patient_id', 'claim_number', 'insurance_id','sub_status_id', 'date_of_service','total_charge', 'insurance_category', 'status','rendering_provider_id','facility_id' ,\DB::raw('DATEDIFF("'.$practice_todate.'", date_of_service) as claim_age_days'));
                                }, 
                            'claim.insurance_details' => function($query) {
                                    $query->select('id', 'short_name');
                                },
                            'claim.rend_providers' =>function($query){
                                $query->select('id', 'provider_name', 'short_name as provider_short');
                            },
                            'claim.facility' =>function($query){
                                $query->select('id', 'facility_name', 'short_name as facility_short');
                            },
                            'claim.claim_sub_status' => function($query){
                                $query->select('id', 'sub_status_desc');
                            },
                            'lastWorkbench', 'claimcpt'
                        ])

                        ->where('claim_info_v1.status', '=', 'Denied')
                        //->where('claim_info_v1.insurance_id', '<>', 0)
                        ->where('pmt_claim_tx_v1.pmt_method', '=', 'Insurance')
                        ->groupby('pmt_claim_tx_v1.claim_id', 'pmt_claim_cpt_fin_v1.claim_id', 'pmt_claim_cpt_fin_v1.claim_cpt_info_id')
                        ->orderBy('pmt_claim_tx_v1.id', 'desc')//->toSql()
                        ;
                                     
        // date_of_service
        if(!empty(json_decode(@$request['dataArr']['data']['date_of_service']))) {
            $date = explode('-',json_decode(@$request['dataArr']['data']['date_of_service']));
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));

            $denial_cpts->WhereHas('claim', function($q)use($start_date, $end_date) {
                $q->where(DB::raw('DATE(date_of_service)'),'>=',$start_date)->where(DB::raw('DATE(date_of_service)'),'<=',$end_date);
            });

            $search_by['DOS'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
        }

        // Responsibility
        if(!empty(json_decode(@$request['dataArr']['data']['responsibility']))) {
            $billed_to =  json_decode(@$request['dataArr']['data']['responsibility']);
            if($billed_to != 'All' && $billed_to != ''){
                $denial_cpts->WhereHas('lastcptdenialdesc', function($q)use($billed_to) {
                    if(is_array($billed_to))
                        $q->WhereIn('responsibility', $billed_to);
                    else     
                        $q->WhereIn('responsibility', explode(',', $billed_to));
                });
            }
            // Get Selected Insurance Names
            $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name");
            if(is_array($billed_to))
                $insurance = $insurance->whereIn('id', $billed_to)->get()->toArray();
            else     
                $insurance = $insurance->whereIn('id', explode(',',$billed_to))->get()->toArray();

            $search_by['Responsibility'][] = @array_flatten($insurance)[0];
        }


        if(!empty(json_decode(@$request['dataArr']['data']['cpt_type']))) {
            $cpt_type = json_decode(@$request['dataArr']['data']['cpt_type']);

            if($cpt_type == 'custom_type'){
                $search_by['CPT Type'][] = 'Custom Range';
                if(!empty(json_decode(@$request['dataArr']['data']['custom_type_from'])) && !empty(json_decode(@$request['dataArr']['data']['custom_type_to']))){                    
                    $custom_type_from = json_decode(@$request['dataArr']['data']['custom_type_from']);
                    $custom_type_to = json_decode(@$request['dataArr']['data']['custom_type_to']);
                    $cpts_list = Helpers::CptsRangeBetween($custom_type_from, $custom_type_to);

                    $denial_cpts->WhereHas('claimcpt', function($q)use($cpts_list) {
                        $q->whereIn('claim_cpt_info_v1.cpt_code', $cpts_list);                        
                    });
                }
            }

            if($cpt_type == 'cpt_code'){
                 $search_by['CPT Type'][] = 'CPT Code';
                if(!empty(json_decode(@$request['dataArr']['data']['cpt_code_id']))){
                    $cpt_code_id = json_decode(@$request['dataArr']['data']['cpt_code_id']);
                    $denial_cpts->WhereHas('claimcpt', function($q)use($cpt_code_id) {
                        // Comma separated search option added for CPT code.
                        $cptCodes = array_map('trim', explode(',', $cpt_code_id));
                        $q->Where(function ($qry) use ($cptCodes) {
                            foreach ($cptCodes as $key => $dc) {
                                if($key == 0)
                                    $qry->where('cpt_code', $dc);
                               else
                                    $qry->orWhere('cpt_code', $dc);
                            }
                        });
                    });
                    $search_by['CPT Code'][] = $cpt_code_id;
                }
            }
        }
         
        

        // Denied Date
        if(!empty(json_decode(@$request['dataArr']['data']['created_at']))) {
            $date = explode('-',json_decode(@$request['dataArr']['data']['created_at']));
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));            
            // Handle the condition for payment denied date
            $denial_cpts->Where(function ($q) use ($start_date, $end_date) {
                $dateSearch = date("Y-m-d", strtotime(@$search));
            });
            $search_by['Denied Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
        }

        // Submitted Date
        $practice_timezone = Helpers::getPracticeTimeZone();  
        if(!empty(json_decode(@$request['dataArr']['data']['submitted_date']))) {
            $date = explode('-',json_decode(@$request['dataArr']['data']['submitted_date']));
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));            
            // Handle the condition for payment denied date
            $denial_cpts->WhereHas('claim', function($q)use($start_date, $end_date,$practice_timezone) {
                $q->whereRaw("DATE(CONVERT_TZ(submited_date,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(submited_date,'UTC','".$practice_timezone."')) <= '$end_date'");
            });
            $search_by['Submitted Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
        }

        // denial_code
        if(!empty(json_decode(@$request['dataArr']['data']['denial_code']))) {
            $denailCodes = array_filter(array_map('trim', explode(',', $request['dataArr']['data']['denial_code'])));
            $search_by['Denial Code'][] = $request['denial_code'];
        }

        // claim_age
        if(!empty(json_decode(@$request['dataArr']['data']['claim_age']))) {
            $claimAge = json_decode(@$request['dataArr']['data']['claim_age']);
            if( $claimAge != 'All'){
                switch ($claimAge) {
                    case "0-30":
                        $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(30)));
                        $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(0)));
                        $denial_cpts->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                            $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                        });
                        $search_by['Claim Age'][] = "0-30 Days";
                        break;

                    case "31-60":
                        $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(60)));
                        $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(31)));
                        $denial_cpts->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                            $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                        });
                        $search_by['Claim Age'][] = "31-60 Days";
                        break;

                    case "61-90":
                        $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(90)));
                        $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(61)));
                        $denial_cpts->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                            $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                        });
                        $search_by['Claim Age'][] = "61-90 Days";
                        break;

                    case "91-120":
                        $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(120)));
                        $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(91)));
                        $denial_cpts->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                            $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                        });
                        $search_by['Claim Age'][] = "91-120 Days";
                        break;

                    case "121-150":
                        $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(150)));
                        $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(121)));
                        $denial_cpts->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                            $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                        });
                        $search_by['Claim Age'][] = "121-150 Days";
                        break;

                    case "150-above":
                        $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(151)));
                        $denial_cpts->WhereHas('claim', function($q)use($current_month) {
                            $q->where('date_of_service', '<=', $current_month);
                        });
                        $search_by['Claim Age'][] = ">150 Days";
                        break;
                }
            }
        }
        // Workbench status
        if(!empty(json_decode(@$request['dataArr']['data']['workbench_status']))) {    
            $search_by['Workbench Status'][] = $request['dataArr']['data']['workbench_status'];
        }

        // Workbench status
        if(!empty(json_decode(@$request['dataArr']['data']['exclude_zero_ar']))) {
            if(json_decode(@$request['dataArr']['data']['exclude_zero_ar']) == 'Exclude') {
                $exclude_zero_ar = json_decode(@$request['dataArr']['data']['exclude_zero_ar']);
                $denial_cpts->where(DB::raw('pmt_claim_cpt_fin_v1.cpt_charge-(pmt_claim_cpt_fin_v1.patient_paid + pmt_claim_cpt_fin_v1.insurance_paid +pmt_claim_cpt_fin_v1.patient_adjusted+pmt_claim_cpt_fin_v1.insurance_adjusted+pmt_claim_cpt_fin_v1.with_held)'), '<>', 0);

                $search_by['$0 Line Item'][] = "Remove $0 Line Item";
            } else {
                if($export == "") {
                    $search_by['$0 Line Item'][] = "Contains $0 Line Item";
                }
            }
        }

        if (!empty(json_decode(@$request['dataArr']['data']['rendering_provider_id']))) {
            if(is_array(json_decode(@$request['dataArr']['data']['rendering_provider_id'])))
                $denial_cpts->whereIn('claim_info_v1.rendering_provider_id', json_decode($request['dataArr']['data']['rendering_provider_id']));
            else
                $denial_cpts->where('claim_info_v1.rendering_provider_id', json_decode($request['dataArr']['data']['rendering_provider_id']));
        }       
        
        if (!empty(json_decode(@$request['dataArr']['data']['facility_id']))) {
            if(is_array(json_decode(@$request['dataArr']['data']['facility_id'])))
                $denial_cpts->whereIn('claim_info_v1.facility_id', json_decode($request['dataArr']['data']['facility_id']));
            else
                $denial_cpts->where('claim_info_v1.facility_id', json_decode($request['dataArr']['data']['facility_id']));
        }

        if (!empty(json_decode(@$request['dataArr']['data']['status_reason']))) {
            if(is_array(json_decode(@$request['dataArr']['data']['status_reason'])))
                $denial_cpts->whereIn('claim_info_v1.sub_status_id', json_decode($request['dataArr']['data']['status_reason']));
            else
                $denial_cpts->where('claim_info_v1.sub_status_id', json_decode($request['dataArr']['data']['status_reason']));
        }
        if (count(array_filter(json_decode(@$request['dataArr']['data']['status_reason'])))!=count(json_decode(@$request['dataArr']['data']['status_reason']))) {
            $denial_cpts->orWhereNull('claim_info_v1.sub_status_id');
        }

        $workbench_status = isset($request['dataArr']['data']['workbench_status']) ? json_decode($request['dataArr']['data']['workbench_status']) : 'Include';

        $pagination_count  = $denial_cpts->count();

        $txt_list = $denial_cpts->get();
        $collection = collect($txt_list);
        $result = [];
        $claim_nos = [];
        foreach ($collection as $key => $item) {
           $code_check = $date_check = $ins_check = 1; 
            // denial_code search
           if(!empty(json_decode(@$request['dataArr']['data']['denial_code']))) {    
                $request['denial_code'] = json_decode(@$request['dataArr']['data']['denial_code']);
                $denailCodes = array_filter(array_map('trim', explode(',', @$request['denial_code'])));
                $txn_denials = array_filter(array_map('trim', explode(',', @$item->lastcptdenialdesc->claimcpt_txn->denial_code)));

                $chkResp = array_intersect($txn_denials,$denailCodes);
                // Check if match found then add into result else ignore it.
                 $code_check = (!empty($chkResp)) ? 1 : 0;
            }

            $denied_date = '';
            if(isset($item->lastcptdenialdesc->pmtinfo)) {
                if($item->lastcptdenialdesc->pmtinfo->pmt_mode == 'EFT')
                    $denied_date = @$item->lastcptdenialdesc->pmtinfo->eftDetails->eft_date;
                elseif($item->lastcptdenialdesc->pmtinfo->pmt_mode == 'Credit')
                    $denied_date = @$item->lastcptdenialdesc->pmtinfo->creditCardDetails->expiry_date ;
                else
                    $denied_date = (isset($item->lastcptdenialdesc->pmtinfo->checkDetails->check_date)) ? $item->lastcptdenialdesc->pmtinfo->checkDetails->check_date : '';
            }else{
                $denied_date = Helpers::dateFormat(@$item->recClaimTxn->created_at,'datedb');
            }
            
            if(!empty(json_decode(@$request['dataArr']['data']['created_at']))) {    
                $date = explode('-',json_decode(@$request['dataArr']['data']['created_at']));
                $start_date = date("Y-m-d", strtotime($date[0]));
                if($start_date == '1970-01-01'){
                    $start_date = '0000-00-00';
                }
                $end_date = date("Y-m-d", strtotime($date[1]));
                
                $date_check = ( ( $denied_date >= $start_date ) && ( $denied_date <= $end_date ) ) ? 1 : 0;
            }
            // Report: Add "Payer" Filter in Denial Trend Analysis Report
            // Rev.1 Ref: MR-2847 - Ravi - 21-09-2019
            $denialIns = '';
            if(isset($item->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id)) {
                $denialIns = @$item->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id;
            }    else{
                $denialIns = @$item->recClaimTxn->payer_insurance_id;
            }
                          
            if(!empty(json_decode(@$request['dataArr']['data']['responsibility']))) {
                $billed_to =  json_decode(@$request['dataArr']['data']['responsibility']);
                if($billed_to != 'All' && $billed_to != ''){
                    if(is_array($billed_to))
                        $ins_check = (in_array($denialIns, $billed_to)) ? 1 :0 ;
                    else     
                        $ins_check = (in_array($denialIns, explode(',', $billed_to))) ? 1 :0 ;
                }
            }
            
            if($code_check && $date_check && $ins_check) {
                $item->denied_date = $denied_date;
                $selOptions = json_decode(@$request['dataArr']['data']['Options']);

                $claim_nos[] = $item->claim->claim_number;
                if($selOptions == 'claim'){
                    if(isset($result[$item->claim_id])){
                        $preAr      = $result[$item->claim_id]['total_ar_due'];
                        $preCharge  = $result[$item->claim_id]['total_charge'];
                        $preCpt     = $result[$item->claim_id]['cpt_codes'];                         
                        $result[$item->claim_id] = $item;
                        $result[$item->claim_id]['total_ar_due'] = $preAr+$item->total_ar_due;
                        $result[$item->claim_id]['total_charge'] = $preCharge+$item->claimcpt->charge;
                        $result[$item->claim_id]['cpt_codes'] = $preCpt.",".$item->claimcpt->cpt_code; 
                    } else {
                        $preAr = $preCharge = 0;
                        $preCpt     ='';
                        $result[$item->claim_id] = $item;
                        $result[$item->claim_id]['total_ar_due'] = $item->total_ar_due;
                        $result[$item->claim_id]['total_charge'] = $item->claimcpt->charge;
                        $result[$item->claim_id]['cpt_codes'] = $item->claimcpt->cpt_code;
                    }                    
                } else {
                    $result[] = $item;
                }
            }
        }

        $denial_cpt_list = $result;
        $denial_cpt_list_export = $result;
        $count = count($denial_cpt_list);
        $export_array = $result;
        $temp = [];
        if ($export == "") {
            $report_array = $this->paginate($denial_cpt_list)->toArray();
            $pagination_prt = $this->paginate($denial_cpt_list)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
        }
        $temp = array_chunk($denial_cpt_list, 50, true);

        $filter_result = "all";
        $start = 0;
        if (!empty($denial_cpt_list)) {            
            $startLimit = isset($request['start']) ? $request['start'] : 0;
            $start = ($startLimit > 0 ) ? ($startLimit / 50) : 0;                
            $denial_cpt_list = $temp[$start];
        }

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('denial_cpt_list','denial_cpt_list_export','pagination','pagination_prt','search_by','workbench_status','export_array', 'pagination_count','search_fields','searchUserData', 'count', 'claim_nos')));        
    }

    public function paginate($items, $perPage = 25) {
        $items = collect($items);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);

        //return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function getarmanagementfollowupApi() {
        $claims_list = ClaimInfoV1::with('patient', 'rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->take(500)->whereNotIn('status', ['Hold'])->orderBy('id', 'DESC')->get();

        $practice_id = Session::get('practice_dbid');
        $practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();
        $admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
        $practice_user_arr2 = Users::whereRaw("((customer_id = ? and practice_user_type = 'customer' and status = 'Active') or ($admin_practice_id_like))", array(Auth::user()->customer_id))->pluck('id')->all();
        $practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
        $user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->pluck('name', 'id')->all();

        $patients = Patient::where('status', 'Active')->selectRaw('CONCAT(last_name,", ",first_name, " ",middle_name) as patient_name, id')->orderBy('last_name', 'ASC')->pluck('patient_name', 'id')->all();
        $billing_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
        $rendering_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')]);
        $referring_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Referring')]);
        $insurances = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
        $facility = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
        $myfollowup_list = PatientNote::where('title', 'ArManagement')->where('status','Active')->where('user_id', Auth::user()->id)->pluck('claim_id')->all();
        $otherfollowup_list = PatientNote::where('status','Active')->pluck('claim_id')->all();

        $category = FollowupCategory::where('deleted_at', null)->where('status', 'Active')->select('id', 'name', 'label_name')->get()->toArray();

        $question = FollowupCategory::with(['question' => function($query) {
                        $query->where('deleted_at', null)->where('status', 'Active');
                    }])->where('deleted_at', null)->where('status', 'Active')->select('id', 'name', 'label_name')->get()->toArray();

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list', 'user_list', 'patients', 'billing_provider', 'rendering_provider', 'referring_provider', 'insurances', 'facility', 'myfollowup_list', 'otherfollowup_list', 'category', 'question')));
    }
    
    public function getAR_SummaryPageDataApi() 
    {       
       $data['collection_chart'] = $this->getCollectionDataApi();
       $data['insuranceLineChart'] = $this->getInsuranceCategoryPaymentLineChart();
       $data['aging_data'] = $this->getAging();
       $data['ar_days'] = $this->getARDaysApi();
       $data['insuranceAgingChart'] = $this->getTotalInsuranceAgingApi(); 
       $data['patientAgingChart'] = $this->getTotalPatientAgingApi(); 
       $data['insurance_aging'] = $this->InsuranceWiseAging('dos');
       $data['PatientAging'] = $this->PatientWiseAging();
       $data['patient_claims_status'] =  $this->getBalanceByStatus('','dos');
       $data['claim_status_wise_count'] = $this->getArByCountByStatus(); 
       $data['ins_category_value'] = $this->getInsuranceCategoryPayment();
       return Response::json(compact('data'));
    }

    public function getArByCountByStatus($patient_id = null)
    {       
        $claims_status_arr = ['Hold', 'Rejection', 'Denied', 'Pending', 'Submitted','Ready'];
        $get_data = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0')->whereIn('claim_info_v1.status', $claims_status_arr)->groupBy('claim_info_v1.status')->select(DB::raw('(sum(pmt_claim_fin_v1.total_charge)-(sum(pmt_claim_fin_v1.patient_paid)+sum(pmt_claim_fin_v1.insurance_paid) + sum(pmt_claim_fin_v1.withheld) + sum(pmt_claim_fin_v1.patient_adj) + sum(pmt_claim_fin_v1.insurance_adj) )) as total_billed'),DB::raw("count('claim_info_v1.id') as total_count"), 'claim_info_v1.status');
        if(!empty($patient_id))
            $get_data->where('claim_info_v1.patient_id', $patient_id);
        return $get_data->get();
     }    

     public function getBalanceByDos(){
        $aging_arr = ['0-30','31-60', '61-90', '91-120', '121-150'];
        foreach ($aging_arr as $key => $value) {
            $data_value = explode("-", $value);
            $end_date = date('Y-m-d', strtotime(Carbon::now()->subDay($data_value[1])));
            $start_date = date('Y-m-d', strtotime(Carbon::now()->subDay($data_value[0])));            
            $patient_claims[$value] =  DB::table('claim_info_v1')
                ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')            
                ->select('claim_info_v1.insurance_id', 'claim_info_v1.insurance_category',DB::raw('(sum(pmt_claim_fin_v1.total_charge)-(sum(pmt_claim_fin_v1.patient_paid)+sum(pmt_claim_fin_v1.insurance_paid) + sum(pmt_claim_fin_v1.withheld) + sum(pmt_claim_fin_v1.patient_adj) + sum(pmt_claim_fin_v1.insurance_adj) )) AS total_ar'))
                ->where('claim_info_v1.date_of_service', '>=', $end_date)->where('claim_info_v1.date_of_service', '<=', $start_date)
                ->groupBy('insurance_id', 'insurance_category')
                ->whereNull('claim_info_v1.deleted_at')
                ->where('insurance_id', '!=', 0);
            $patient_claims[$value] = $patient_claims[$value]->get();
        }        
        return  $patient_claims;
     }

    // Aging Summary Insurance and patient
    public function getAging(){
        $data['patient'] = $this->getAgingPatient();
        $data['insurance']  = $this->getAgingInsurance();
        $data['oustanding']  = $this->getAgingTotal($data['patient'],$data['insurance']);
        return $data;
    } 

    public function getAgingTotal($patient,$insurance){
        $aging_arr = ['Unbilled','0-30','31-60', '61-90', '91-120', '121-150', '>150'];
        foreach ($aging_arr as $key => $value) {
            $getDate = $this->getstartAndEndDate($value);
            $end_date = $getDate['end_date'];            
            $start_date = $getDate['start_date'];                                        
            if(empty($end_date) && !empty($start_date) &&  $start_date == "Unbilled") {   
                 $aging_data[$value][] = $insurance[$value][0]->claim_count;                 
                 $aging_data[$value][] = $insurance[$value][0]->total_ar;                 
            } elseif($start_date != "Unbilled"){
                $aging_data[$value][] = $patient[$value][0]->claim_count+$insurance[$value][0]->claim_count; 
                $aging_data[$value][] = $patient[$value][0]->patient_balance+$insurance[$value][0]->total_ar+(@$insurance[$value][1]); 
            }                                              
        } 
        return  $aging_data;
     }

     public function getAgingInsurance(){
        /*$claim_over = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where('claim_info_v1.insurance_id', 0)->where('pmt_claim_fin_v1.insurance_due', '<', 0);*/
        $aging_arr = ['Unbilled','0-30','31-60', '61-90', '91-120', '121-150', '>150']; 
        foreach ($aging_arr as $key => $value) {
            //$over = 0;
            $getDate = $this->getstartAndEndDate($value);
            $end_date = $getDate['end_date'];            
            $start_date = $getDate['start_date']; 

            //Insurance Responsibility for over payment
            /*if(empty($end_date) && !empty($start_date) &&  $start_date != "Unbilled") { 
                $claim_over = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where('claim_info_v1.insurance_id', 0)->where('pmt_claim_fin_v1.insurance_due', '<', 0)->where("claim_info_v1.date_of_service", "<=", $start_date);
            }
            if(!empty($end_date) && !empty($start_date) &&  $start_date != "Unbilled") {
                $claim_over = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where('claim_info_v1.insurance_id', 0)->where('pmt_claim_fin_v1.insurance_due', '<', 0)->where('claim_info_v1.date_of_service', '>=', $end_date)->where('claim_info_v1.date_of_service', '<=', $start_date);
            }
            if(!empty($start_date) &&  $start_date != "Unbilled") 
                $over = $claim_over->whereNull('claim_info_v1.deleted_at')->sum(DB::raw('pmt_claim_fin_v1.insurance_due'));
*/
            //\Log::info($over);
            $insurance_data = $this->getCommonInsAging($end_date, $start_date, "from"); 
            $aging_data[$value] = $insurance_data->get(); 
            //$aging_data[$value][1] = $over;   
        }     
        return  $aging_data;
    } 

    public function getAgingPatient(){
        $aging_arr = ['0-30','31-60', '61-90', '91-120', '121-150', '>150'];
        foreach ($aging_arr as $key => $value) {
            $getDate = $this->getstartAndEndDate($value);
            $end_date = $getDate['end_date'];            
            $start_date = $getDate['start_date']; 
            $patient_data =    $this->getCommonPatientAging($end_date, $start_date); 
            $aging_data[$value] =  $patient_data->get(); 
        } 
        return  $aging_data;         
    } 

    public function getClaimcount($start_date, $end_date)
    {  
        $count_data = DB::table('claim_info_v1')
            ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')            
            ->select(DB::raw('count(claim_info_v1.id) as claim_count'))
            ->where(function($query) use ($end_date, $start_date){
                if(empty($end_date)) {                           
                    $query->where("claim_info_v1.date_of_service", "<", $start_date );                             
                }else {
                    $query->where('claim_info_v1.date_of_service', '>=', $end_date)->where('claim_info_v1.date_of_service', '<=', $start_date); 
                }                                          
            })                    
            ->whereNull('claim_info_v1.deleted_at')->where('pmt_claim_fin_v1.patient_due', '>', '0')->pluck('claim_count')->first();
        return $count_data;                
     }

     public function getBalanceByStatus($patient_id= null,$option_by_date=null)
     {
        $aging_arr = ['Unbilled','0-30','31-60', '61-90', '91-120', '121-150', '>150'];
        $status_arr = ['Hold','Ready','Patient','Submitted','Denied','Pending','Rejection','Paid'];
        foreach($status_arr as $status){
            foreach ($aging_arr as $key => $value) {
                $getDate = $this->getstartAndEndDate($value);
                $end_date = $getDate['end_date'];            
                $start_date = $getDate['start_date']; 
                $claim_value = DB::table('claim_info_v1')
                ->leftJoin('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')            
                ->select('claim_info_v1.insurance_id', 'claim_info_v1.status',DB::raw('SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) AS total_ar'), DB::raw('count(claim_info_v1.id) as claim_count'))->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0');
                    if($value!='Unbilled'){
                        if(empty($end_date)) {  
                            if($option_by_date=='submission'){
                                if($status=='Patient')  {
                                    $claim_value->whereRaw("(claim_info_v1.submited_date < '".$start_date."' or claim_info_v1.submited_date = '0000-00-00 00:00:00' and claim_info_v1.created_at < '".$start_date."')");
                                }else{
                                    $claim_value->where("claim_info_v1.submited_date", "<", $start_date);
                                }
                            }else{
                                $claim_value->where("claim_info_v1.date_of_service", "<", $start_date);
                            }                                        
                        } else {
                             if($option_by_date=='submission'){
                                if($status=='Patient')  {
                                    $claim_value->whereRaw("(claim_info_v1.submited_date >= '".$end_date."' and claim_info_v1.submited_date <= '".$start_date."' or claim_info_v1.submited_date = '0000-00-00 00:00:00' and claim_info_v1.created_at <= '".$start_date."' and claim_info_v1.created_at >= '".$end_date."')"); 

                                }else{
                                    $claim_value->where('claim_info_v1.submited_date', '>=', $end_date)->where('claim_info_v1.submited_date', '<=', $start_date); 
                                }
                            }else{
                                $claim_value->where('claim_info_v1.date_of_service', '>=', $end_date)->where('claim_info_v1.date_of_service', '<=', $start_date); 
                            }
                        }   
                    }
                if($patient_id)
                    $claim_value = $claim_value->where('claim_info_v1.patient_id',$patient_id);
                if($status=='Patient')  {
                    if($value=='Unbilled'){
                        @$patient_claims[$status][$value][0]->claim_count = 'NA';
                        @$patient_claims[$status][$value][0]->total_ar = 0;
                    } else {
                        $patient_claims[$status][$value] = $claim_value->where('claim_info_v1.status',$status)
                                                            ->whereNull('claim_info_v1.deleted_at')
                                                            ->get();
                    }
                                
                }  else {
                    if($value=='Unbilled'){
                        $patient_claims[$status][$value] = $claim_value->where('claim_info_v1.status',$status)
                                                        ->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count', 0)
                                                        ->whereNull('claim_info_v1.deleted_at')
                                                        ->get();
                    } else {
                                $patient_claims[$status][$value] = $claim_value->where('claim_info_v1.status',$status)
                                                                ->where(function($qry){
                                                                    $qry->where(function($query){ 
                                                                        $query->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count','>' ,0);
                                                                    })->orWhere('claim_info_v1.insurance_id',0);
                                                                })
                                                                ->whereNull('claim_info_v1.deleted_at')
                                                                ->get();
                        }
                }                
               }
               // Requirement changed from DOS to submitted date as Per the meeting with Testing team
        }        
        return  $patient_claims;
     }

     public function getstatusclaim($start_date, $end_date,$value, $patient_id,  $from=null){
        $claim_value = DB::table('claim_info_v1')
                    ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')            
                    ->select('claim_info_v1.insurance_id', 'claim_info_v1.status',DB::raw('SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) AS total_ar'), DB::raw('count(claim_info_v1.id) as claim_count'))
                    ->where(function($query) use ($end_date, $start_date, $value, $from){
            if($from){
               if(empty($end_date)) {                                          
                 $query->where("claim_info_v1.date_of_service", "<", $start_date);                             
                }else {
                   $query->where('claim_info_v1.date_of_service', '>=', $end_date)
                   ->where('claim_info_v1.date_of_service', '<=', $start_date)  ;                     
                }       
            }
            else {
                if(empty($end_date)) {                                          
                 $query->where("claim_info_v1.date_of_service", "<", $start_date);                             
                }else {
                   $query->where('claim_info_v1.date_of_service', '>=', $end_date)
                   ->where('claim_info_v1.date_of_service', '<=', $start_date)
            ; 
                }      
            }
                                                
        })   
        ->where(function($query) use ($patient_id){
            if(!empty($patient_id)){
               $query->where('claim_info_v1.patient_id', $patient_id);
            }
        });

        return $claim_value;
    }

    public function getInsuranceCategoryPayment($patient_id = null)
    {
        // Insurance category initialize in array
        $practice_timezone = Helpers::getPracticeTimeZone();  
        $categories = ['Primary', 'Secondary', 'Tertiary'];
        // Initialize to Start date for 7 month before
        $dateS = Carbon::now($practice_timezone)->startOfMonth()->subMonth(6);
        $dateS = $dateS->toDateString();
        // Initialize to End date for now
        $dateE = $today = Carbon::today($practice_timezone);
        $dateE = $dateE->toDateString();

        // Insurance category wise paid
        foreach($categories as $category){
            $data[$category] = PMTClaimTXV1::select(DB::raw('sum(total_paid) as `total_paid`'), DB::raw("DATE_FORMAT(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'),'%b-%y') as monthNum"), DB::raw('sum(total_writeoff) + sum(total_withheld) as `total_writeoff`'))->orderBy("created_at")->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))
                ->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '$dateS' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '$dateE'")
                ->where(function($query) use ($patient_id){
                     if(!empty($patient_id)){
                        $query->where('patient_id', $patient_id);
                     }
                 })->where('ins_category', $category)->pluck('total_paid', 'monthNum')->all();
        }        

        // Patient paid
        $data['Patient'] = PMTClaimTXV1::select(DB::raw('sum(total_paid) as `total_paid`'), DB::raw("DATE_FORMAT(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'),'%b-%y') as monthNum"),
            DB::raw('sum(total_writeoff) as `total_writeoff`'))->orderBy("created_at")
            ->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '$dateS' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '$dateE'")
            ->where(function($query) use ($patient_id){
                if(!empty($patient_id)){
                    $query->where('patient_id', $patient_id);
                }

             })->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))->whereIn('pmt_method', ['Patient', 'Addwallet'])->pluck('total_paid', 'monthNum')->all();

        // Insurance Adjustment
        $data['Ins Adj'] = PMTClaimTXV1::select(DB::raw('sum(total_paid) as `total_paid`'), DB::raw("DATE_FORMAT(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'),'%b-%y') as monthNum"),
            DB::raw('sum(total_writeoff) + sum(total_withheld) as `total_writeoff`'))->orderBy("created_at")
            ->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '$dateS' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '$dateE'")
            ->where(function($query) use ($patient_id){
                if(!empty($patient_id)){
                    $query->where('patient_id', $patient_id);
                }
             })->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))
            ->pluck('total_writeoff', 'monthNum')->all();

        // Patient Adjustment
        $data['Pat Adj'] = PMTClaimTXV1::select(DB::raw('sum(total_paid) as `total_paid`'), DB::raw("DATE_FORMAT(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'),'%b-%y') as monthNum"),
            DB::raw('sum(total_writeoff) as `total_writeoff`'))
            ->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '$dateS' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '$dateE'")
            ->whereIn('pmt_method', ['Patient'])
            ->where(function($query) use ($patient_id){
                if(!empty($patient_id)){
                    $query->where('patient_id', $patient_id);
                }
             })->orderBy("created_at")->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))
            ->pluck('total_writeoff', 'monthNum')->all();

        // Last 7 months of month name and year(ex:Apr-18)
        for ($i = 0; $i < 7; $i++) {
          $date_label[$i]['label'] = date("M-y", strtotime("-$i month"));
        }

        $date_label = array_reverse($date_label);  
        $date_label_final['category'] = [];
        array_push($date_label_final['category'],$date_label);

        $data_final = [];
        foreach($data as $key=> $data_value) {
            $cat = $key;
            $data_final[$cat]['seriesname'] = $key;
            foreach(array_flatten($date_label) as $key => $date){
                if(in_array($date, array_keys($data_value))) {
                    $value =  $data_value[$date];
                } else{
                    $value =  "0.00";
                }
                $data_final[$cat]['data'][$key] = ["value" => $value] ;
            }
        }

        $datavaluefinal = array(array("dataset" => []),array("dataset" => []),array("dataset" => []));             
        array_push($datavaluefinal[0]['dataset'], $data_final["Primary"], $data_final["Secondary"], $data_final["Tertiary"]);                                         
        array_push($datavaluefinal[1]['dataset'], $data_final["Patient"]); // Need to simplyfy it     
        array_push($datavaluefinal[2]['dataset'], $data_final["Ins Adj"], $data_final["Pat Adj"]); // Need to simplyfy it     

        return json_encode(compact('datavaluefinal', 'date_label_final'));
    }

    public function getInsuranceCategoryPaymentLineChart()
    {
        $data = [];
        $categories = ['Primary', 'Secondary', 'Tertiary'];
        // $year_data = ["current_year" => date('Y'), "last_year" => date("Y",strtotime("-1 year"))];  
        $year_data = ["current_year" => date('Y')];        
         foreach ($year_data as $key => $value) {
              /*$data[$key] = PMTClaimFINV1::select(DB::raw('sum(insurance_due) as `insurance_due`'), "ins_category")
                  ->groupBy("ins_category")->whereIn('ins_category', $categories)
                  ->whereYear('created_at', '=',$value)->lists('total_paid', 'ins_category');
                  $data[$key]["Self"] = PMTClaimTXV1::select(DB::raw('sum(total_paid) as `total_paid`'), "ins_category")
                  ->whereIn('pmt_method', ['Patient', 'Addwallet'])
                  ->whereYear('created_at', '=',$value)
                  ->pluck('total_paid');*/
              $data[$key] = DB::table('claim_info_v1')
                        ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')                                 
                        ->select('claim_info_v1.insurance_category', DB::raw('sum(pmt_claim_fin_v1.insurance_due) as insurance_balance'))->groupBy("insurance_category")->whereIn('claim_info_v1.insurance_category', $categories)                      
                        ->whereNull('claim_info_v1.deleted_at')
                        ->pluck('insurance_balance', 'insurance_category')->all();
                /*$data[$key] = PMTClaimTXV1::select(DB::raw('sum(total_paid) as insurance_balance,ins_category as insurance_category'),  DB::raw('sum(total_writeoff) + sum(total_withheld) as `total_writeoff`'))->groupBy('ins_category')
                 ->whereIn('ins_category', $categories)->lists('insurance_balance', 'insurance_category');*/
                 
              $data[$key]["Self"] = DB::table('claim_info_v1')
                        ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')                                 
                        ->select(DB::raw('sum(pmt_claim_fin_v1.patient_due) as patient_balance'))                       
                        ->whereNull('claim_info_v1.deleted_at')->value('patient_balance');
                /*$data[$key]["Self"] = PMTClaimTXV1::select(DB::raw('sum(total_paid) as patient_balance'), DB::raw('sum(total_writeoff) + sum(total_withheld) as `total_writeoff`'))->groupBy('ins_category')->whereIn('pmt_method', ['Patient', 'Addwallet'])->pluck('patient_balance');*/
         }      
        return  $data;
    }

    public function PatientWiseAging()
    {
         $aging_arr = ['0-30','31-60', '61-90', '91-120', '121-150', '>150'];
         $bill_cycles = ['A - G', 'H - M', 'N - S', 'T - Z'];         
         foreach($bill_cycles as $bill_cycle) {
            $patient = 0;
             foreach ($aging_arr as $key => $value) {              
                $getDate = $this->getstartAndEndDate($value);
                $end_date = $getDate['end_date'];            
                $start_date = $getDate['start_date'];                               
                $patient_data = $this->getCommonPatientAging($end_date, $start_date, $bill_cycle);
                $patient_billcycle_claims[$bill_cycle][$value] = $patient_data->get();              
             }
             $patient_count = DB::select(DB::raw('SELECT patients.bill_cycle, count(DISTINCT fin.patient_id) AS patient_count FROM pmt_claim_fin_v1 fin INNER JOIN ( SELECT claim_id, MAX(created_at) AS created_at FROM claim_tx_desc_v1 WHERE transaction_type IN ("Responsibility", "New Charge") AND responsibility = 0 GROUP BY claim_id) AS tx ON tx.claim_id = fin.claim_id JOIN patients ON fin.patient_id = patients.id INNER JOIN ( SELECT id FROM claim_info_v1 WHERE status != "Paid" AND insurance_id = 0 and deleted_at is NULL) AS info ON tx.claim_id = info.id and patients.deleted_at is null and patients.bill_cycle = "'.$bill_cycle.'"'));
            $patient += @$patient_count[0]->patient_count;
            $patient_billcycle_claims[$bill_cycle]["patientCount"] = $patient;//$this->patientcountOnBillCycle($bill_cycle);            
         }       
         return $patient_billcycle_claims;       
    }

    public function patientcountOnBillCycle($bill_cycle){
         $bill_cycle = DB::table('claim_info_v1')                  
            ->join('patients', 'claim_info_v1.patient_id', '=', 'patients.id')           
            ->select( DB::raw(' count(DISTINCT claim_info_v1.patient_id) as patient_count'),'patients.bill_cycle')
            ->where('patients.bill_cycle', $bill_cycle)                
            ->whereNull('claim_info_v1.deleted_at')->pluck("patient_count")->first();
            return $bill_cycle;
    }

    public function InsuranceWiseAging($dos)
    {
         $aging_arr = ["Unbilled",'0-30','31-60', '61-90', '91-120', '121-150', '>150'];
         $insurances = $this->InsuranceClaims();
         $patient_insurance_claims = [];
         foreach($insurances as $insurance_id =>$insurance){             
                foreach ($aging_arr as $key => $value) { 
                $getDate = $this->getstartAndEndDate($value);
                $end_date = $getDate['end_date'];            
                $start_date = $getDate['start_date'];                                  
                $insurance_data = $this->getCommonInsAging($end_date, $start_date,'from',$dos);     
                $patient_insurance_claims[$insurance][$value] =  $insurance_data->where('claim_info_v1.insurance_id', $insurance_id)->get();                
             } 
         }        
         return $patient_insurance_claims;       
    }

    public function getPatientInsuranceCategoryPayment($patient_id)
    {
        $aging_arr = ['Unbilled','0-30','31-60', '61-90', '91-120', '121-150', '>150'];
        $categories = ['Primary', 'Secondary', 'Tertiary'];
        $claims_insurances = [];
        $claims_insurances = ClaimInfoV1::with(['insurance_details' => function($query){
                                $query->select("short_name", 'id');
                            }])->select('insurance_id', 'insurance_category')
                            ->groupBy('insurance_id', 'insurance_category')
                            ->where('insurance_category', '!=', '')
                            ->where("claim_info_v1.patient_id", $patient_id) 
                            ->get()->toArray();
        array_unshift($claims_insurances,['patient'=>"Patient AR"]);                        
        $claims_insurances = (object)$claims_insurances; 
        $patient_insurance_claims = [];     
             foreach($claims_insurances as $insurance){  
                $ins_cat = @$insurance['insurance_category'];
                $insId = @$insurance['insurance_id'];
                foreach ($aging_arr as $key => $value) {                        
                    $getDate = $this->getstartAndEndDate($value);
                    $end_date = $getDate['end_date'];            
                    $start_date = $getDate['start_date'];
                    $getname = isset($insurance['patient'])?$insurance['patient']:substr($ins_cat, 0,3)."-".@$insurance['insurance_details']['short_name'];
                    $patient_insurance_claims[$getname][$value] =  DB::table('claim_info_v1')
                    ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id') 
                    ->leftJoin('insurances', 'claim_info_v1.insurance_id', '=', 'insurances.id')           
                    ->select('claim_info_v1.insurance_id' ,'insurances.short_name', 'claim_info_v1.self_pay','claim_info_v1.insurance_category',DB::raw('count(claim_info_v1.id) as claim_count'),DB::raw('(sum(pmt_claim_fin_v1.total_charge)-(sum(pmt_claim_fin_v1.patient_paid)+sum(pmt_claim_fin_v1.insurance_paid) + sum(pmt_claim_fin_v1.withheld) + sum(pmt_claim_fin_v1.patient_adj) + sum(pmt_claim_fin_v1.insurance_adj) )) AS total_ar, (sum(pmt_claim_fin_v1.total_charge)-(sum(pmt_claim_fin_v1.patient_paid)+sum(pmt_claim_fin_v1.insurance_paid) + sum(pmt_claim_fin_v1.withheld) + sum(pmt_claim_fin_v1.patient_adj) + sum(pmt_claim_fin_v1.insurance_adj) )) as patient_balance,(sum(pmt_claim_fin_v1.total_charge)-(sum(pmt_claim_fin_v1.patient_paid)+sum(pmt_claim_fin_v1.insurance_paid) + sum(pmt_claim_fin_v1.withheld) + sum(pmt_claim_fin_v1.patient_adj) + sum(pmt_claim_fin_v1.insurance_adj) )) as insurance_balance'), DB::raw('count(claim_info_v1.insurance_id) as claim_insurance_count'))
                    ->where(function($query) use ($end_date, $start_date, $getname){
                    if(empty($end_date) && !empty($start_date) &&  $start_date != "Unbilled") {                           
                         $query->where("claim_info_v1.date_of_service", "<", $start_date);
                         if(strpos($getname, "Patient AR") === false) {
                            $query->where('claim_submit_count', '!=', 0);       
                         }                      
                    }elseif(empty($end_date) && !empty($start_date) &&  $start_date == "Unbilled") {                           
                         $query->where('claim_info_v1.claim_submit_count', '=', 0)->where('claim_info_v1.insurance_id', '!=',0);                             
                    }else {
                       $query->where('claim_info_v1.date_of_service', '>=', $end_date)->where('claim_info_v1.date_of_service', '<=', $start_date);
                       if(strpos($getname, "Patient AR") === false) {
                           $query->where('claim_info_v1.claim_submit_count', '!=', 0); 
                       }
                    }                                          
                })->where(function($query) use ($insId, $ins_cat, $getname){
                        if(strpos($getname, "Patient AR") !== false) {
                            $query->where("claim_info_v1.insurance_id", 0);
                        } else{
                            $query->where('claim_info_v1.insurance_id', $insId)
                            ->where('claim_info_v1.insurance_category', $ins_cat)
                            ->groupBy('claim_info_v1.insurance_id', 'claim_info_v1.insurance_category');
                        }
                        
                    })->where('claim_info_v1.patient_id', $patient_id)                       
                    ->get();
                 }                                      
         }              
         return $patient_insurance_claims;
    }

    public function getstartAndEndDate($date){
        $practice_timezone = Helpers::getPracticeTimeZone();  

        $data_value = explode("-", $date); 
        if((empty($data_value[1]))){
            $return_data['end_date'] = '';
        }else{
            $return_data['end_date'] = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(@$data_value[1]-1)));
        }
        if(strpos($date, ">") !== false){
            $return_data['start_date'] = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", 149))));
        } else{
            if(@$data_value[0]=='Unbilled'){
                $return_data['start_date'] = 'Unbilled';
            }else{
                if(@$data_value[0] == 0){
                    $return_data['start_date'] = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$data_value[0]))));                    
                }
                else{
                    $return_data['start_date'] = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$data_value[0]-1))));
                }
            }
        }
        /*if($date=='31-60')
            dd($return_data);
        //$return_data['end_date'] = (empty($data_value[1]))?"":($date=='121-150')?date('Y-m-d', strtotime(Carbon::now()->subDay(@$data_value[1]-1))):date('Y-m-d', strtotime(Carbon::now()->subDay(@$data_value[1])));            
        $return_data['start_date'] = (strpos($date, ">") !== false)?date('Y-m-d', strtotime(Carbon::now()->subDay(str_replace(">", "", @$data_value[0])))):(($data_value[0] == "Unbilled")?"Unbilled":date('Y-m-d', strtotime(Carbon::now()->subDay(@$data_value[0]))));*/
        /*if($date=='121-150')       
            dd(date('Y-m-d', strtotime($past)));*/
        return $return_data;
    }
    
    public function getCommonPatientAging($end_date, $start_date, $bill_cycle=null,$dos=null)
    {       
         $common_condition = DB::table('claim_info_v1')
                    ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id') 
                    ->join('patients', 'claim_info_v1.patient_id', '=', 'patients.id')           
                    /*->select( DB::raw(' count(DISTINCT claim_info_v1.patient_id) as patient_count'),'patients.bill_cycle',DB::raw('(sum(pmt_claim_fin_v1.total_charge)-(sum(pmt_claim_fin_v1.patient_paid)+sum(pmt_claim_fin_v1.insurance_paid) + sum(pmt_claim_fin_v1.withheld) + sum(pmt_claim_fin_v1.patient_adj) + sum(pmt_claim_fin_v1.insurance_adj) )) AS total_ar'), DB::raw('sum(pmt_claim_fin_v1.patient_due) as patient_balance'), DB::raw('count(claim_info_v1.id) as claim_count') )*/
                    ->select( DB::raw(' count(DISTINCT claim_info_v1.patient_id) as patient_count'),'patients.bill_cycle',DB::raw('count(claim_info_v1.id) as claim_count'),DB::raw('SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) AS total_ar'), DB::raw('count(claim_info_v1.id) as claim_count'), DB::raw('SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) as   patient_balance'))

                    ->where(function($query) use ($end_date, $start_date, $bill_cycle,$dos){
                        if(is_null($dos)){
                            if(empty($end_date)) {                            
                                 $query->where("claim_info_v1.date_of_service", "<", $start_date );                             
                            }else {
                               $query->where('claim_info_v1.date_of_service', '>=', $end_date)->where('claim_info_v1.date_of_service', '<=', $start_date); 
                            } 
                        }else{
                            if(empty($end_date)) {                            
                                 $query->whereRaw("IF(claim_info_v1.submited_date = '0000-00-00 00:00:00', date(claim_info_v1.created_at), date(claim_info_v1.submited_date)) < '". $start_date."'" );                             
                            }else {
                               $query->whereRaw("IF(claim_info_v1.submited_date = '0000-00-00 00:00:00', date(claim_info_v1.created_at), date(claim_info_v1.submited_date)) >= '". $end_date."'")->whereRaw("IF(claim_info_v1.submited_date = '0000-00-00 00:00:00', date(claim_info_v1.created_at), date(claim_info_v1.submited_date)) <= '". $start_date."'"); 
                            }
                        }
                        if(!empty($bill_cycle)) 
                            $query->where('patients.bill_cycle', $bill_cycle) ;                    
                    })                   
                    ->where('claim_info_v1.insurance_id',"=", '0')->whereNull('claim_info_v1.deleted_at')->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0');
        return $common_condition;
    }

    public function getCommonInsAging($end_date, $start_date, $from=null,$option_by_date=null){
        DB::enableQueryLog();
        if($start_date == "Unbilled") {
            $common_condition = DB::table('claim_info_v1')->select('claim_info_v1.insurance_id' ,'insurances.short_name', 'claim_info_v1.insurance_category',DB::raw('count(claim_info_v1.id) as claim_count'),DB::raw('SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) AS total_ar'), DB::raw('count(claim_info_v1.id) as claim_insurance_count'), DB::raw('SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) as   insurance_balance'))
        ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id') 
        ->join('insurances', 'claim_info_v1.insurance_id', '=', 'insurances.id')->where('claim_info_v1.insurance_id', '!=', 0)->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0');
        } else{
            $common_condition = DB::table('claim_info_v1')->select('claim_info_v1.insurance_id' ,'insurances.short_name', 'claim_info_v1.insurance_category',DB::raw('count(claim_info_v1.id) as claim_count'),DB::raw('SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) AS total_ar'), DB::raw('count(claim_info_v1.id) as claim_insurance_count'), DB::raw('SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) as   insurance_balance'))
        ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id') 
        ->join('insurances', 'claim_info_v1.insurance_id', '=', 'insurances.id')->where('claim_info_v1.insurance_id', '!=', 0)->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0');
        }
        $common_condition->where(function($qry)  use ($end_date, $start_date){
            if(empty($end_date) && !empty($start_date) &&  $start_date != "Unbilled") {                           
                $qry->where(function($query) use ($end_date, $start_date){
                 $query->where('claim_info_v1.claim_submit_count','>' ,0);
                 });                             
            }elseif(empty($end_date) && !empty($start_date) &&  $start_date == "Unbilled") {   
                $qry->where(function($query) use ($end_date, $start_date){                        
                     $query->where('claim_info_v1.claim_submit_count', '=', 0);
                 });
            }else {
                $qry->where(function($query) use ($end_date, $start_date){
               $query->where('claim_info_v1.claim_submit_count','>' ,0);
                });  
            }  
        });
        if($option_by_date=='submission'){
            if(empty($end_date) && !empty($start_date) &&  $start_date != "Unbilled") { 
                $common_condition->where("claim_info_v1.submited_date", "<", $start_date);
            }elseif(empty($end_date) && !empty($start_date) &&  $start_date == "Unbilled") { 
            }else {
                $common_condition->where('claim_info_v1.submited_date', '>=', $end_date)->where('claim_info_v1.submited_date', '<=', $start_date);
            } 
        }else{
            if(empty($end_date) && !empty($start_date) &&  $start_date != "Unbilled") { 
                $common_condition->where("claim_info_v1.date_of_service", "<", $start_date);
            }elseif(empty($end_date) && !empty($start_date) &&  $start_date == "Unbilled") { 
            }else {
                $common_condition->where('claim_info_v1.date_of_service', '>=', $end_date)->where('claim_info_v1.date_of_service', '<=', $start_date);
            } 
        }
        $common_condition->orderBy('claim_insurance_count')->whereNull('claim_info_v1.deleted_at');
        if(is_null($from)){
           $common_condition = $common_condition->groupBy('insurances.id')->orderBy('insurances.short_name','asc') ;
        }
        //\Log::info(DB::getQueryLog());
        return $common_condition;
    }

    public function InsuranceClaims()
    {
        $insurance_claims = DB::table('claim_info_v1')                
                ->join('insurances', 'claim_info_v1.insurance_id', '=', 'insurances.id')->join('pmt_claim_fin_v1','pmt_claim_fin_v1.claim_id','=','claim_info_v1.id')->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0')->selectRaw('sum(claim_info_v1.insurance_id) as insurances_count, claim_info_v1.insurance_id as ins_id, if(insurances.short_name is null or insurances.short_name = "", insurances.insurance_name,insurances.short_name) as short_name')->groupBy('insurances.id')->orderBy('insurances.short_name','asc')->pluck('short_name','ins_id')->all();
         return $insurance_claims;       
    }     

    public function getCollectionDataApi() {
        // Get start of month before 7 month 
        $practice_timezone = Helpers::getPracticeTimeZone();
        $dateS = Carbon::now($practice_timezone)->startOfMonth()->subMonth(6);
        $dateS = $dateS->toDateString();

        // Get end of month for Current month
        $dateE = $today = Carbon::today($practice_timezone);
        $dateE = $dateE->toDateString(); 
        
        // Initialize array index
        $chargeBar = [];           
        $Charge_val = $date_label = [];           
        
        // Last 7 months of month name and year(ex:Apr-18)
        for ($i = 6; $i >= 0; $i--) {
          $chargeMonth[$i]['monthName'] = date("M-y", strtotime("-$i month"));
        }
        //Billed
        $billed = ClaimInfoV1::where(function($qry){ 
                    $qry->where(function($query){ 
                        $query->where('insurance_id','!=',0)
                            ->where('claim_submit_count','>' ,0); })
                        ->orWhere('insurance_id',0); })
                            ->selectRaw('sum(total_charge) as total_charge,DATE_FORMAT(CONVERT_TZ(claim_info_v1.created_at,"UTC","'.$practice_timezone.'"),"%b-%y") as monthName')
                                ->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '$dateS' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '$dateE'")
                                ->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))
                                ->whereNull('deleted_at')
                                ->get()->toArray();//dd($billed);
        // Collections = Insurance Collections + Patient Collections
        // Insurance Collections
        $InsuranceCollections = PMTClaimTXV1::selectRaw('sum(total_paid) as total_paid,DATE_FORMAT(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"),"%b-%y") as monthName')->whereHas('pmt_info', function($q)  {
                        $q->whereRaw('void_check is null');
                    })
                ->where('pmt_method','Insurance')
                ->whereIn('pmt_type',['Payment','Refund'])
                ->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '$dateS' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '$dateE'")
                ->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))
                ->get()->toArray();
        
        //Patient Collections
        //For patient collection, patient refund must be subtracted with patient payment from DB (See documentation for reason)
        $PatientCollections = PMTInfoV1::selectRaw('sum(pmt_amt) as pmt_amt,DATE_FORMAT(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"),"%b-%y") as monthName')
                ->where('pmt_method','Patient')
                ->whereIn('pmt_type',['Payment','Credit Balance'])
                ->where('void_check',Null)
                ->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '$dateS' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '$dateE'")
                ->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))
                ->get()->toArray();
        
        $PatientRefundCollections = PMTInfoV1::selectRaw('sum(pmt_amt) as pmt_amt,DATE_FORMAT(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"),"%b-%y") as monthName')
                ->where('pmt_method','Patient')
                ->whereIn('pmt_type',['Refund'])
                ->where('source','posting')
                ->where('void_check',Null)
                ->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '$dateS' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '$dateE'")
                ->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))
                ->get()->toArray();

        // AR = Insurance AR + Patient AR
        //Insurance AR = Insurance Balance - (Patient Paid + Patient Adjustment)
        $insurance_ar = ClaimInfoV1::selectRaw('sum(pmt_claim_fin_v1.insurance_due - (pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj)) as total_ar, DATE_FORMAT(CONVERT_TZ(claim_info_v1.created_at,"UTC","'.$practice_timezone.'"),"%b-%y") as monthName')
                ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                ->where("claim_info_v1.insurance_id", "!=", "0")
                ->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$dateS' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$dateE'")
                ->groupBy(DB::raw("month(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."'))"))
                ->get()->toArray();
        //Patient AR = Patient Balance + (Insurance Balance) Since when a claim is in Self, Insurance Overpayment will be in Insurace due column
        $patient_ar = ClaimInfoV1::selectRaw('sum(pmt_claim_fin_v1.patient_due + pmt_claim_fin_v1.insurance_due) as total_ar, DATE_FORMAT(CONVERT_TZ(claim_info_v1.created_at,"UTC","'.$practice_timezone.'"),"%b-%y") as monthName')
                ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                ->where("claim_info_v1.insurance_id", "0")
                ->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$dateS' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$dateE'")
                ->groupBy(DB::raw("month(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."'))"))
                ->get()->toArray();
        
        // Initialize variable array index
        $ins = $pat = $ref = $ins_ar =$pat_ar = $chrge = 0;
        if(!empty($chargeMonth)){
            foreach($chargeMonth as $b){  
                // Chart label for month
                $date_label[]['label'] = $b['monthName'];
                //Billed Charges for the month
                if(isset($billed[$chrge]['monthName']) && $billed[$chrge]['monthName'] == $b['monthName']) {
                    $Charge_val['Charge'][] = ["value" => $billed[$chrge]['total_charge']];
                    $chrge++;
                }  else {
                    $Charge_val['Charge'][] = ["value" => "0.00"];
                }                          
                // Collections month wise
                // If insurance, patient and refund collection for same month
                if(isset($InsuranceCollections[$ins]['monthName'], $PatientCollections[$pat]['monthName'], $PatientRefundCollections[$ref]['monthName']) && $InsuranceCollections[$ins]['monthName'] == $b['monthName'] && $PatientCollections[$pat]['monthName'] == $b['monthName'] && $PatientRefundCollections[$ref]['monthName'] == $b['monthName']){
                    $total_col = $PatientCollections[$pat]['pmt_amt'] - $PatientRefundCollections[$ref]['pmt_amt'];
                    $Charge_val['Collections'][] = ["value" => $InsuranceCollections[$ins]['total_paid']+$total_col];
                    $ins++;
                    $pat++;
                    $ref++;
                // If insurance and patient collection for same month
                } elseif(isset($InsuranceCollections[$ins]['monthName'], $PatientCollections[$pat]['monthName']) && $InsuranceCollections[$ins]['monthName'] == $b['monthName'] && $PatientCollections[$pat]['monthName'] == $b['monthName']){
                    $Charge_val['Collections'][] = ["value" => $InsuranceCollections[$ins]['total_paid']+$PatientCollections[$pat]['pmt_amt']];
                    $ins++;
                    $pat++;
                // If insurance and refund collection for same month
                } elseif(isset($InsuranceCollections[$ins]['monthName'], $PatientRefundCollections[$ref]['monthName']) && $InsuranceCollections[$ins]['monthName'] == $b['monthName'] && $PatientRefundCollections[$ref]['monthName'] == $b['monthName']){
                    $Charge_val['Collections'][] = ["value" => $InsuranceCollections[$ins]['total_paid']-$PatientRefundCollections[$ref]['pmt_amt']];
                    $ins++;
                    $ref++;
                // If insurance only
                } elseif(isset($InsuranceCollections[$ins]['monthName']) && $InsuranceCollections[$ins]['monthName'] == $b['monthName']) {
                    $Charge_val['Collections'][] = ["value" => $InsuranceCollections[$ins]['total_paid']];
                    $ins++;
                // If patient only
                } elseif(isset($PatientCollections[$pat]['monthName']) && $PatientCollections[$pat]['monthName'] == $b['monthName']) {
                    $Charge_val['Collections'][] = ["value" => $PatientCollections[$pat]['pmt_amt']];
                    $pat++;
                    // If patient refund only
                } elseif(isset($PatientRefundCollections[$ref]['monthName']) && $PatientRefundCollections[$ref]['monthName'] == $b['monthName']) {
                    $Charge_val['Collections'][] = ["value" => '-'.$PatientRefundCollections[$ref]['pmt_amt']];
                    $ref++;
                } else {
                    $Charge_val['Collections'][] = ["value" =>"0.00"];
                }

                // AR month wise
                // If insurance and patient AR for same month
                if(isset($insurance_ar[$ins_ar]['monthName'], $patient_ar[$pat_ar]['monthName']) && $insurance_ar[$ins_ar]['monthName'] == $b['monthName'] && $patient_ar[$pat_ar]['monthName'] == $b['monthName']){
                    $Charge_val['Balance'][] = ["value" =>$insurance_ar[$ins_ar]['total_ar']+$patient_ar[$pat_ar]['total_ar']];
                    $ins_ar++;
                    $pat_ar++;
                // If insurance  AR only
                } elseif(isset($insurance_ar[$ins_ar]['monthName']) && $insurance_ar[$ins_ar]['monthName'] == $b['monthName'] ){
                    $Charge_val['Balance'][] = ["value" =>$insurance_ar[$ins_ar]['total_ar']];
                    $ins_ar++;
                // If patient  AR only
                } elseif(isset($patient_ar[$pat_ar]['monthName']) && $patient_ar[$pat_ar]['monthName'] == $b['monthName']){
                    $Charge_val['Balance'][] = ["value" =>$patient_ar[$pat_ar]['total_ar']];
                    $pat_ar++;
                } else{
                    $Charge_val['Balance'][] = ["value" =>"0.00"];
                }
            }
        }
        else{
                $Charge_val['Charge'][] =["value" => "0.00"];
                $Charge_val['Collections'][] = ["value" =>"0.00"];
                $Charge_val['Balance'][] = ["value" =>"0.00"];
        }
        $Charge_val['Balance'] = array_values($Charge_val['Balance']);
        $Charge_val['Charge'] = array_values($Charge_val['Charge']);
        $Charge_val['Collections'] = array_values($Charge_val['Collections']); 
        $Charge_val['insurance_chart_label'] = $date_label; //dd($Charge_val);     
        //dd($Charge_val);
        return $Charge_val;
    }
    
    public function getARDaysApi() 
    {
        return \App\Http\Helpers\Helpers::ardays();
    }
    public function getTotalCharge()
    {
        $charges_amount = $this->getClaimsTotalCharges();
        return $charges_amount;
    }
    public function getTotalInsuranceAgingApi()
    {   
        $aging_arr = ["Unbilled",'0-30','31-60', '61-90', '91-120', '121-150','>150'];  
        $patient_insurance_claims = [];                 
        foreach ($aging_arr as $key => $value) {
            $over = 0;
                $getDate = $this->getstartAndEndDate($value);
                $end_date = $getDate['end_date'];            
                $start_date = $getDate['start_date'];               
                $insurance_data = $this->getCommonInsAging($end_date, $start_date, "from"); 
                $insurance_claim_info = $insurance_data->get();     
                //Insurance Responsibility for over payment
                /*if(empty($end_date) && !empty($start_date) &&  $start_date != "Unbilled") { 
                    $claim_over = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where('claim_info_v1.insurance_id', 0)->where('pmt_claim_fin_v1.insurance_due', '<', 0)->where("claim_info_v1.date_of_service", "<=", $start_date);
                }
                if(!empty($end_date) && !empty($start_date) &&  $start_date != "Unbilled") {
                    $claim_over = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where('claim_info_v1.insurance_id', 0)->where('pmt_claim_fin_v1.insurance_due', '<', 0)->where('claim_info_v1.date_of_service', '>=', $end_date)->where('claim_info_v1.date_of_service', '<=', $start_date);
                }    
                if(!empty($start_date) &&  $start_date != "Unbilled") 
                    $over = $claim_over->whereNull('claim_info_v1.deleted_at')->sum(DB::raw('pmt_claim_fin_v1.insurance_due'));*/
                //dd($over);
                //$patient_insurance_claims[$value] =  $this->getChartValue(@$insurance_claim_info[0]->total_ar);
                 $patient_insurance_claims[$value] =  $this->getChartValue(@$insurance_claim_info[0]->insurance_balance, "Insurance");
        } 
        return $patient_insurance_claims;       
    }

    public function getTotalPatientAgingApi()
    {
         $aging_arr = ['0-30','31-60', '61-90', '91-120', '121-150', '>150'];         
         foreach ($aging_arr as $key => $value) {
             $getDate = $this->getstartAndEndDate($value);
             $end_date = $getDate['end_date'];            
             $start_date = $getDate['start_date'];
            $patient_data = $this->getCommonPatientAging($end_date, $start_date);
            $patient_claim_info = $patient_data->get(); 
            $patient_billcycle_claims[$value] = $this->getChartValue(@$patient_claim_info[0]->patient_balance,'Patient');           
         }   
         return $patient_billcycle_claims;       
    }

     public function getChartValue($maincond = 0, $type=null){
        //$total_charge = $this->getClaimsTotalCharges();  
       //dd($total_charge)     ;
        $amountInfo = $this->getPatientARandInsuranceAR();
       // $total_charge = ($type == "Patient")? $amountInfo['patient_balance']:$amountInfo['insurance_balance'];
         $total_charge = $maincond;
       // return $percentage = ($total_charge != 0) ? round(($maincond / $total_charge) * 100, 2) : 0;
        return $total_charge;

     }

     public function getPatientARandInsuranceAR()
     {

        $balance = PMTClaimFINV1::select(DB::Raw('sum(insurance_due) as   insurance_balance'), DB::Raw('sum(patient_due) as   patient_balance'))->first()->toArray();
        return $balance;
     }
     

}
