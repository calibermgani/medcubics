<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Patients\Api\BillingApiController;
use App\Http\Controllers\Payments\Api\PatientPaymentApiController as PatientPaymentApiController;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Patient;
use App\Models\Payments\PMTInfoV1;
use Redirect;
use Request;
use Response;
use Session;
use View;
use Log;
use PDF;
use Excel;
use App\Models\Patients\PatientNote;
use Validator;
use Storage;
use App\Exports\BladeExport;

class PatientPaymentController extends PatientPaymentApiController {

    public function __construct() {
        View::share('heading', 'Patient');
        View::share('selected_tab', 'payments');
        View::share('heading_icon', 'fa-user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($patient_id, $tab = null, $claim_id = null) {
        $api_response = $this->getIndexApi($patient_id, $tab, $claim_id);
        $patient_ins = $this->getPatientInsuranceApi($patient_id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            $claims_lists = $api_response_data->data->claims_list;
            $billing_provider = $api_response_data->data->billing_providers;
            $search_fields = $api_response_data->data->search_fields;
            $searchUserData = $api_response_data->data->searchUserData;
            $view = 'patients/payments/payments';
            if (Request::ajax() && $tab == "insurance") {
                $view = 'patients/payments/insurance_addpop';
            } elseif (Request::ajax() && $tab == "patient") {
                $view = 'patients/payments/patientaddpopup';
            }
            return view($view, compact('claims_lists', 'patient_id', 'billing_provider', 'patient_ins', 'claim_id', 'search_fields', 'searchUserData'));
        } else {
            return Redirect::to('/patients');
        }
    }
    public function getPaymentExport($patient_id ='', $tab = null, $claim_id = null, $export = ''){
        $api_response = $this->getIndexApi($patient_id, $tab, $claim_id);
        $api_response_data = $api_response->getData();
        $claims_lists = $api_response_data->data->claims_list;
        $search_by = $api_response_data->data->search_by;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Patient_Payments_List_' . $date;
       
        if ($export == 'pdf') {
            $html = view('patients/payments/payments_export_pdf', compact('claims_lists', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'patients/payments/payments_export';
            $data['claims_lists'] = $claims_lists;
            $data['export'] = $export;
            $data['search_by'] = $search_by;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {
                return $data;
            }
            $type = '.xls';
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'patients/payments/payments_export';
            $data['claims_lists'] = $claims_lists;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
    public function indexTableData($patient_id = null) {
        $billing = new BillingApiController() ; 
        $api_response = $billing->getListIndexApi($patient_id); //getIndexApi();
        $api_response_data = $api_response->getData();
        $claims_lists = (!empty($api_response_data->data->charges)) ? (array) $api_response_data->data->charges : [];
        $facilities = (!empty($api_response_data->data->facilities)) ? (array) $api_response_data->data->facilities : [];
        $rendering_providers = (!empty($api_response_data->data->rendering_providers)) ? (array) $api_response_data->data->rendering_providers : [];
        $billing_providers = (!empty($api_response_data->data->billing_providers)) ? (array) $api_response_data->data->billing_providers : [];
        $id = $patient_id;
        $view_html = Response::view('patients/payments/payments_listing', compact('claims_lists', 'facilities', 'rendering_providers', 'billing_providers', 'id'));
        $content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
        $request = Request::all();
        if (!empty($request['draw']))
            $data['draw'] = $request['draw'];
        $data['data'] = $content;
        $data['datas'] = ['id' => 2];
        $data['recordsTotal'] = $api_response_data->data->count;
        $data['recordsFiltered'] = $api_response_data->data->count;
        return Response::json($data);
    }
    public function getPaymentExportdata($patient_id ='',$export =''){
        $billing = new BillingApiController() ; 
        $api_response = $billing->getListIndexApi($patient_id); //getIndexApi();
        $api_response_data = $api_response->getData();
        $claims_lists = (!empty($api_response_data->data->charges)) ? (array) $api_response_data->data->charges : [];
        $search_by = (!empty($api_response_data->data->search_by)) ? (array) $api_response_data->data->search_by : [];
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Patient_Payments_List_' . $date;
        
        if ($export == 'pdf') {
            $html = view('patients/payments/payments_export_pdf', compact('claims_lists', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'patients/payments/payments_export';
            $data['claims_lists'] = $claims_lists;         
            $data['export'] = $export;
            $data['search_by'] = $search_by;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {
                return $data;
            }
            $type = '.xls';
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'patients/payments/payments_export';
            $data['claims_lists'] = $claims_lists;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
    public function getPaymentpopup($tab, $payment_detail_id = null) {
        $api_response = $this->getPaymentpopupApi($payment_detail_id);
        $api_response_data = $api_response->getData();
        $patient_ins = $api_response_data->data->insurance_list;
        $payment_details = $api_response_data->data->payment_details;  // To reopen check we get the data from payment details
        $patient_ins = (array) $patient_ins;
        $claims_lists = $api_response_data->data->claims_lists;
       // if(Session::has('ar_claim_id'))
           // Session::forget('ar_claim_id');
        if ($tab == "insurance") {
            $view = 'patients/payments/insurance_addpop';
        } elseif ($tab == "patient") {
            $view = 'patients/payments/patientaddpopup';
        }
        return view($view, compact('patient_ins', 'billing_provider', 'claims_lists', 'payment_details', 'tab'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request, $patient_id = null) {
        $post_val = Request::all();
        $method = $request::method();
        if (empty($post_val) && Session::has('post_val')) {
             
            $post_val = Session::get('post_val');
            $payment_id = Helpers::getEncodeAndDecodeOfId($post_val['payment_detail_id'], 'decode');
            if (!empty($payment_id)) {
                // This payment Concept to get the unapplied amount from latest
                $getval = PMTInfoV1::getPaymentDadetailData($payment_id);
                $post_val['payment_amt'] = $getval['balance'];
                $post_val['unapplied'] = !is_null($getval['balance']) ? $getval['balance'] : $post_val['unapplied'];
                $post_val['payment_amt_calc'] = !is_null($getval['balance']) ? $getval['balance'] : $post_val['unapplied'];
            }
        } elseif ($method == "GET" && !Session::has('post_val')) {
            return Redirect::to('/patients/' . $patient_id . '/payments');
        }
        if (!empty($post_val) && isset($post_val['claim_ids'])) { 
            $paymentV1Controller = new PaymentV1ApiController();
            $api_response = $paymentV1Controller->createPayment($post_val, $post_val['claim_ids']);

            $api_response_data = $api_response->getData();
            
            $claims_lists = [];
            if ($api_response_data->status == 'success') {
                $claims_lists = $api_response_data->data->claim_lists;
                $total_list = $api_response_data->data->total;
				
                // To get EOB attachment 
                if (!empty($post_val['temp_type_id'])) {
                    Session::put('eob_attachment', $post_val['temp_type_id']);
                }
                //$claim_id_list 		= 	$api_response_data->data->claim_id_list;
                unset($post_val['filefield_eob']);

                $patID = Helpers::getEncodeAndDecodeOfId(@$post_val['patient_id'], 'decode');        
                $patient_alert_note = PatientNote::where('notes_type_id', $patID)->where('notes_type', 'patient')->where('status', 'Active')->where('patient_notes_type', 'alert_notes')->select("created_by", "content")->first();

                return view('patients/payments/create', compact('claims_lists', 'post_val', 'total_list','patient_alert_note'));
                //return view('patients/payments/create', compact('claims_lists', 'post_val', 'total_list'));
            } elseif ($api_response_data->status == 'error') {
                return Redirect::to('/patients/' . $patient_id . '/payments')->with('error', $api_response_data->message);
            }
        } else {
            return Redirect::to('/patients/' . $patient_id . '/payments');
        }
    }

    public function create1($claim_id) {
        return view('patients/payments/edit01');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        $request = $request::all();
        $api_response = $this->getStoreApi($request);
        $api_response_data = $api_response->getData();
        $patient_id = $api_response_data->data;
        $payment_id = Helpers::getEncodeAndDecodeOfId(@$api_response_data->payment_id, 'encode');
        if ($api_response_data->status == 'success' && $request['next'] != 1) {
            return Redirect::to('/patients/' . $patient_id . '/payments')->with('success', $api_response_data->message);
        } elseif ($api_response_data->status == 'success' && $request['next'] == 1) {
            Session::put('post_val.payment_detail_id', $payment_id);
            return Redirect::to('/patients/' . $patient_id . '/payments/create');
        } else {
            $errMsg = isset($api_response_data->message) ? $api_response_data->message : "Check number already exist";
            return Redirect::to('/patients/' . $patient_id . '/payments')->with('error', $errMsg);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show() {
        return view('patients/billing/show');
    }

    public function getpopuppaymentdata($claim_id, $type = null) {
        $paymentV1ApiController = new PaymentV1ApiController();
        $newClaimDetails = $paymentV1ApiController->createPayment('', $claim_id);
        //$api_response = $this->getApiPopuppaymentdata($claim_id);
        //$api_response_data = $api_response->getData();
        $newapi_response_data = $newClaimDetails->getData();
        $claim_detail = (isset($newapi_response_data->data->claim_lists)) ? $newapi_response_data->data->claim_lists : [];
        $claim_transaction = isset($newapi_response_data->data->claim_tx_list) ? $newapi_response_data->data->claim_tx_list : [];
        $cpt_transaction = (isset($newapi_response_data->data->cpt_tx_list)) ? $newapi_response_data->data->cpt_tx_list : [];

        $attachments = (isset($newapi_response_data->data->attachment_detail)) ? $newapi_response_data->data->attachment_detail : [];
        return view('patients/payments/paymentpopup', compact('claim_detail', 'claim_transaction', 'cpt_transaction', 'attachments', 'type'));
    }
    
    public function getPopupPaymentDataExport($claim_id, $type = null, $export = '') {
        $paymentV1ApiController = new PaymentV1ApiController();
        $newClaimDetails = $paymentV1ApiController->createPayment('', $claim_id);
        $newapi_response_data = $newClaimDetails->getData();
        $claim_detail = (isset($newapi_response_data->data->claim_lists)) ? $newapi_response_data->data->claim_lists : [];
        $claim_transaction = isset($newapi_response_data->data->claim_tx_list) ? $newapi_response_data->data->claim_tx_list : [];
        $cpt_transaction = (isset($newapi_response_data->data->cpt_tx_list)) ? $newapi_response_data->data->cpt_tx_list : [];
        $attachments = (isset($newapi_response_data->data->attachment_detail)) ? $newapi_response_data->data->attachment_detail : [];        
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $claim_no = $claim_detail->claim_number;
        $name = 'Claim_No_' . $claim_no;
        
        if ($export == 'pdf') {
            $html = view('patients/payments/paymentpopup_export_pdf', compact('claim_detail', 'claim_transaction', 'cpt_transaction', 'attachments', 'type', 'export'));
            //return PDF::load($html, 'A4')->filename($name . ".pdf")->download();//For vsmoraes pdf
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");//For DOMPDF 
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $api_response = $this->getCreateApi();
        $api_response_data = $api_response->getData();
        $facilities = $api_response_data->data->facilities;
        $providers = $api_response_data->data->providers;
        $rendering_providers = $api_response_data->data->rendering_providers;
        $referring_providers = $api_response_data->data->referring_providers;
        $billing_providers = $api_response_data->data->billing_providers;
        $insurances = $api_response_data->data->insurances;
        $facility_id = '';
        $provider_id = '';
        $rendering_provider_id = '';
        $referring_provider_id = '';
        $billing_provider_id = '';
        $insurance_id = '';
        return view('patients/payments/edit', compact('facilities', 'facility_id', 'providers', 'rendering_providers', 'referring_providers', 'billing_providers', 'provider_id', 'rendering_provider_id', 'referring_provider_id', 'billing_provider_id', 'insurances', 'insurance_id'));
    }

    // this is temporary function to download log files

    public function download($type = null) {
        $file_name = "laravel-" . date('Y-m-d') . '.txt';
        $file = storage_path() . "/logs/" . $file_name;
        if ($type == "delete") {
            if (file_exists($file))
                unlink($file);
        } else {
            $handle = fopen($file, "r");
            readfile($handle);
            exit;
        }
    }

    public function addamounttowallet($patient_id, $type = null) {
        $request = Request::all();
        $patientPaymentControllerNew = new PaymentV1ApiController();
        if ($request['payment_type'] == 'Payment') {
            $request['source'] = 'posting';
            $api_response = $patientPaymentControllerNew->createWalletData($request);
        } else if ($request['payment_type'] == 'Refund') {
            //refund Payment Mode always Check
            $request['payment_mode'] = 'Check';
            $api_response = $patientPaymentControllerNew->doRefundFromWallet($request);
        }
        $api_response_data = (!empty($api_response))?$api_response->getData():[];
        $data['status'] = (isset($api_response_data->status)) ? $api_response_data->status : '';
        $data['message'] = (isset($api_response_data->message)) ? $api_response_data->message : '';
        $data['data'] = (isset($api_response_data->data)) ? $api_response_data->data : '';
        return $data;
    }

    public function addPatientNote($patient_id) {
        return view('patients/payments/patientnote', compact('patient_id'));
    }

    public function searchPayment($patient_id) {
        $charges = new BillingApiController();
        $api_response = $charges->getIndexApi($patient_id);
        $api_response_data = $api_response->getData();
        $claims_lists = $api_response_data->data->claims_list;
        $id = $patient_id;
        return view('patients/payments/payments_listing', compact('claims_lists', 'id'));
    }

}
