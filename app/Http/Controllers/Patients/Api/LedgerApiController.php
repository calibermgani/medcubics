<?php

namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Models\Patients\Patient as Patient;
use App\Http\Controllers\LedgerReportController as LedgerReportController;
use App\Models\Patients\PatientDocument as PatientDocument;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Http\Controllers\Patients\Api\PaymentApiController as PaymentApiController;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Users as Users;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\Patients\PatientNote as PatientNote;
use Illuminate\Support\Collection;
use DateTime;
use Lang;
use Response;
use Request;
use Auth;
use Session;
use Config;
use App\Http\Helpers\Helpers as Helpers;
use DB;
use App\Traits\ClaimUtil;
use Log;

class LedgerApiController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    use ClaimUtil;

    public function getIndexApi($patient_id) {
        //Patient id decode
        $practice_timezone = Helpers::getPracticeTimeZone();
        $id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        // If patient id is a number, is not empty, count the patient id else Response error not patient message show
        if ((isset($id) && is_numeric($id)) && (Patient::where('id', $id)->count()) > 0) {
            //the Patient reation ship on the patient insurance, patient insurance archive, patient notes, 
            $patients = Patient::with(['patient_insurance' => function($query) {
                            $query->orderBy('category', 'asc');
                        }, 'patient_insurance.insurance_details', 'patient_insurance_archive' => function($query) {
                            $query->orderBy('id', 'desc');
                        }, 'patient_insurance_archive.insurance_details', 'authorization_details', 'notes' => function($query1) use($practice_timezone) {
                            $query1->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->whereIn('patient_notes_type', ['alert_notes', 'patient_notes', 'claim_notes','claim_denial_notes'])->whereIn('status', ['Active']);
                        }, 'notes.user', 'notes.claims', 'authorization_details.insurance_details', 'authorization_details.pos_detail', 'correspondence_details.template_detail.templatetype', 'contact_details'])->select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->where('id', $id)->first();
            //Patient Document attachent are listing 
            $document_list = PatientDocument::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with("document_categories")->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?) or (main_type_id = ? and document_sub_type <> ""))', array($id, $id))->orderBy('id', 'DESC')->get();
            //Patient Appointment listing
            $patient_appointment = PatientAppointment::with('provider', 'reasonforvisit', 'provider.degrees', 'facility')->where('patient_id', $id)->get();

            $patient_notes_det = PatientNote::where('notes_type_id', $id)->where('status','Active');
            $patient_notes_det = $patient_notes_det->select(DB::raw('sum(case when ( patient_notes_type = "claim_notes" OR patient_notes_type = "claim_denial_notes" ) then 1 else 0 end) AS claim_notes'), DB::raw('sum(case when patient_notes_type = "patient_notes" then 1 else 0 end) AS patient_notes'), DB::raw('sum(case when patient_notes_type = "alert_notes" then 1 else 0 end) AS alert_notes'))->first();

            //Patientnotes cliam  count 
            $patients->claim_notes = PatientNote::whereIn('patient_notes_type',['claim_notes','claim_denial_notes'])->where('status','Active')->where('notes_type_id',$id)->count();
            //$patients->claim_notes = ($patient_notes_det->claim_notes) ? $patient_notes_det->claim_notes : 0;
            // patient notes count
            //$patients->patient_notes = PatientNote::whereIn('patient_notes_type',['patient_notes'])->where('notes_type_id',$id)->count();
            $patients->patient_notes = ($patient_notes_det->patient_notes) ? $patient_notes_det->patient_notes : 0;
            // patient Alert note count	
            //$patients->alert_notes = PatientNote::whereIn('patient_notes_type',['alert_notes'])->where('notes_type_id',$id)->count();
            $patients->alert_notes = ($patient_notes_det->alert_notes) ? $patient_notes_det->alert_notes : 0;
            //Claims
            $claims = $this->getClaimsResult($id);
            //TO do Payment 
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            //Pagination
            $claims_paginate = @$claims['claims']->paginate($paginate_count);
            $claims_array = @$claims_paginate->toArray();
            $pagination_prt = @$claims_paginate->render();
            // $claim_id 			= 	$claims['claims']->pluck("claim_id")->first();
            //$total	 			= 	Claims::TotalTransfer($claim_id);
            //Pagination listing the detail
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>← Prev</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">Next →</a></li></ul>';
            $pagination = array('total' => $claims_array['total'], 'per_page' => $claims_array['per_page'], 'current_page' => $claims_array['current_page'], 'last_page' => $claims_array['last_page'], 'from' => $claims_array['from'], 'to' => $claims_array['to'], 'pagination_prt' => $pagination_prt);
            // MR-2717 - Listing Page: In claims list: Order should be shown according the DOS Date Desc as well as claim id
            // Rev 1 - 22-08-2019 - Ravi
            // Rev 2 - 26-08-2019 - Kannan - Changed the Query in getClaimsResult since the order was already changed in the query
            $claim_detail = @$claims['claims']->where('claim_info_v1.patient_id', $id)->orderBy('claim_info_v1.date_of_service', 'desc')->orderBy('claim_info_v1.id', 'desc')->get();
            
            //dd($claim_detail);
            //patient overall cliams
            //$claim_detail 	= Claims::with(['billing_provider', 'insurance_details', 'cpttransactiondetails','facility_detail', 'rendering_provider', 'refering_provider'])->where('patient_id', $id)->get();
            $practice_id = Session::get('practice_dbid');
            $practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();
            $admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
            $practice_user_arr2 = Users::whereRaw("((customer_id = ? and practice_user_type = 'customer' and status = 'Active') or ($admin_practice_id_like))", array(Auth::user()->customer_id))->pluck('id')->all();
            $practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
            $user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->pluck('name', 'id')->all();
            //Patient Model file called here
            $patients->financial_data = Patient::getFinancialData($id);
            $patients->redalert_data = Patient::getRedalertData($id);
            $patients->outstanding_data = Patient::getOutstandingData($id);
            $patients->appointment = (count($patient_appointment) > 0) ? $patient_appointment : [];
            // merge all details into patient
            $patients->documents = $document_list;
            $patients->claims = $claim_detail;
            $patients->users = $user_list;
            $temp = new Collection($patients);
            $temp_id = $temp['id'];
            $temp->pull('id');
            $temp_encode_id = $patient_id;
            $temp->prepend($temp_encode_id, 'id');
            $data = $temp->all();
            $patients = json_decode(json_encode($data), FALSE);
 //dd($patients);

            // $patients->id = $patient_id;
            /* Patient age calculate statrt */
            $from = new DateTime(@$patients->dob);
            $to = new DateTime('today');
            $from->diff($to)->y;
            # procedural
            $patients->age = date_diff(date_create(@$patients->dob), date_create('today'))->y;
            /* Patient age calculate end */
            //Response goto the LedgerController  
			//dd($patients->financial_data);
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('pagination', 'patients')));
        } else {
            //Response goto the LedgerController
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
        }
    }

    /**
     * Display a Ajax request comes.
     *
     * @return result return claims
     */
    public function getAjaxclaimlistApi($id) {
        // Decode the patient id
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        //Request assign to one variable
        $request = Request::All();
        $page = isset($request['page']) ? $request['page'] : 1;
        // If patient id is a number, is not empty, count the patient id else Response error not patient message show
        if ((isset($id) && is_numeric($id)) && (Patient::where('id', $id)->count()) > 0) {
            //matching first record taken
            $patients = Patient::where('id', $id)->first();
            // point to getClaimsResult function 
            $claims = $this->getClaimsResult($id, @$request['ledger_search']);
            //pagination count static validation
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');

            //Pagination
            $claims_paginate = $claims['claims']->paginate($paginate_count);
            //dd($claims_paginate->render());
            $claims_array = $claims_paginate->toArray();    
            $pagination_prt = $claims_paginate->render();
            //pagination  page not empty
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>← Prev</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">Next →</a></li></ul>';
            $pagination = array('total' => $claims_array['total'], 'per_page' => $claims_array['per_page'], 'current_page' => $claims_array['current_page'], 'last_page' => $claims_array['last_page'], 'from' => $claims_array['from'], 'to' => $claims_array['to'], 'pagination_prt' => $pagination_prt);
            $claim_detail = $claims['claims']->where('claim_info_v1.patient_id', $id)->get();
            $patients->claims = $claim_detail;
            //return json patient, pagination
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('pagination', 'patients')));
        }
        else {
            // claim not found return 	
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
        }
    }

    /**
     * Display a claim listing of the resource.
     *
     * @return Response
     */
    public function getClaimsResult($id, $search = '') {
        $practice_timezone = Helpers::getPracticeTimeZone(); 
        //TO do Payment Claim cpt etail table is removed from
        //Get patient Claims 
        // MR-2717 - Revision 1 - 26-08-2019 - Kannan - Removed orderBy(Date of service in Desc)
        $claim_detail = ClaimInfoV1::Join('pmt_claim_fin_v1', function($join){
            $join->on('claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id');
        })
        ->select('claim_info_v1.id as id','claim_info_v1.*','pmt_claim_fin_v1.claim_id', 'pmt_claim_fin_v1.total_charge', 'pmt_claim_fin_v1.total_allowed', 'pmt_claim_fin_v1.patient_paid', 'pmt_claim_fin_v1.insurance_paid', 'pmt_claim_fin_v1.patient_due', 'pmt_claim_fin_v1.insurance_due', 'pmt_claim_fin_v1.patient_adj', 'pmt_claim_fin_v1.insurance_adj', 'pmt_claim_fin_v1.withheld',
            DB::raw('claim_info_v1.total_charge - (  pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld) AS balance_summary'), DB::raw("CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."') as created_at"), DB::raw("CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."') as submited_date"), DB::raw("CONVERT_TZ(claim_info_v1.last_submited_date,'UTC','".$practice_timezone."') as last_submited_date"))
        ->with(['cpttransactiondetails', 'billing_provider', 'insurance_details', 'facility_detail', 'rendering_provider', 'refering_provider'])
            ->where('claim_info_v1.patient_id', $id)->take(1);
        if ($search != '') {              
                
            $claim_detail->Where(function ($claim_detail) use ($search, $id) {
                $claim_detail->whereRaw("(claim_info_v1.claim_number LIKE '%$search%' or claim_info_v1.total_charge LIKE '%$search%' 
                     or status LIKE '%$search%'   )")
                            ->orWhereHas('insurance_details', function($q)use($search) {$q->whereRaw("(short_name LIKE '%$search%')"); });
                //  or total_paid LIKE '%$search%' or patient_adjusted LIKE '%$search%'  or balance_amt LIKE '%$search%'
                //time format change 	
                $claim_detail->orWhere(function ($claim_detail) use ($search, $id) {
                    //$claim_detail->where('id', $id);
                    //$time = strtotime($search);
                    $new_data = explode("/", $search);
                    $newformat = @$new_data[2] . '-' . @$new_data[0] . '-' . @$new_data[1];
                    //echo str_replace("/", "-", $search);
                    //$newformat = date('Y-m-d',$search);
                    //CPT transaction table check DOS 
                    $claim_detail->WhereHas('claim_unit_details', function($q)use($newformat, $id) {
                        $q->whereRaw("((dos_from LIKE '%$newformat%' or dos_to LIKE '%$newformat%' ) AND claim_cpt_info_v1.patient_id= $id )");
                    });

                    $claim_detail->orWhereHas('pmtClaimFinData', function($q)use($search, $id) {                        
                        $q->whereRaw("((total_allowed LIKE '%$search%' or (patient_paid+insurance_paid) LIKE '%$search%' or  patient_adj LIKE '%$search%' or insurance_adj LIKE '%$search%' ) AND pmt_claim_fin_v1.patient_id = $id )");
                    });

                    $search = strtolower($search);
                    //Insurace type self search the word
                });

                if ($search == "self" || $search == "sel" || $search == "se" || $search == "s"){
                    $claim_detail->orWhere('status', 'Patient');
                }  
            });                
        }
        
        //$claim_detail->groupby('claim_info_v1.id');
        $result['claims'] = $claim_detail;
        //return the search cliams	        
        return $result;
    }

}