<?php

namespace App\Http\Controllers\Payments;

use Auth;
use App;
use DB;
use View;
use Input;
use Config;
use Session;
use Request;
use Response;
use Redirect;
use Validator;
use App\Models\Eras;
use App\Models\Insurance;
use App\Models\Code;
use App\Http\Helpers\Helpers;
use PDF;
use Excel;
use App\Models\Medcubics\ClearingHouse as ClearingHouse;
use SSH;
use App\Traits\ClaimUtil;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimTXDESCV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\ClaimEDIInfoV1;
use App\Models\Payments\PMTClaimCPTFINV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\STMTHoldReason as STMTHoldReason;
use App\Models\Patients\PatientNote;
use Log;
use Zipper;
use App\Exports\BladeExport;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
class PaymentController extends Api\PaymentApiController {

    use ClaimUtil;

    public function __construct() {
        View::share('heading', 'Payments');
        View::share('selected_tab', 'payments');
        View::share('heading_icon', 'fa-money');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $payment_details = $api_response_data->data->payment_details;
        $e_remittance = $api_response_data->data->e_remittance;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        if (Request::ajax()) {
            $view = 'payments/payments/payment_search';
        } else {
            $view = 'payments/payments/payments';
        }
        return view($view, compact('payment_details', 'search_fields', 'searchUserData'));
    }

    public function indexTableData() {
        $api_response = $this->getListIndexApi();
        $api_response_data = $api_response->getData();
        $payment_details = (!empty($api_response_data->data->payment_list)) ? $api_response_data->data->payment_list : [];
        $view_html = Response::view('payments/payments/payments_list_ajax', compact('payment_details'));
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

    /*payment export*/
    public function paymentsExport($export = '') {
        $api_response = $this->getListIndexApi($export);
        $api_response_data = $api_response->getData();
        $payment_details = (!empty($api_response_data->data->payment_list)) ? $api_response_data->data->payment_list : [];
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Payments_' . $date;

        if ($export == 'pdf') {
            $html = view('payments/payments/payments_export_pdf', compact('payment_details', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            ini_set('precision', 20);
            $filePath = 'payments/payments/payments_export';
            $data['payment_details'] = $payment_details;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'payments/payments/payments_export';
            $data['payment_details'] = $payment_details;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

    public function searchpatient($type, $search_val, $payment_type = null, $status = null) {
        //$insurance_id = (strpos($payment_type,'insurance') !== false)?explode("::", $payment_type):"";
        if (strpos($payment_type, 'insurance') !== false) {
            $insurance_id = explode("::", $payment_type);
        } elseif (strpos($payment_type, 'payment') !== false && strpos($payment_type, '::') !== false) {
            $insurance_id = explode("::", $payment_type);
            $payment_type = $insurance_id[0];
        } else {
            $insurance_id = '';
        }
        if (isset($status)) {
            $api_response = !empty($insurance_id) ? $this->getClaimStatusSearchApi($type, $search_val, $status, $insurance_id[1]) : $this->getClaimStatusSearchApi($type, $search_val, $status);
        } else {
            $api_response = !empty($insurance_id) ? $this->getPatientSearchApi($type, $search_val, $insurance_id[1]) : $this->getPatientSearchApi($type, $search_val);
        }
        if ($type == "patient")
            $search_val = '';      // to delete the serch value from text box and avoid getting id listing on text box
        $api_response_data = $api_response->getData();
        $patient_details = $api_response_data->data->patient_list;
        $claims = $api_response_data->data->claim_lists;
        $patient_data = $api_response_data->data->patient_data;
        $search_val = (strpos($search_val, '::') !== false) ? explode("::", $search_val) : $search_val;
        $search_val = is_array($search_val) ? $search_val[0] : $search_val;
        $search_val = ($type == "dob") ? base64_decode($search_val) : $search_val;
        return view('payments/payments/payment_detail', compact('claims', 'patient_details', 'payment_type', 'type', 'patient_data', 'status', 'search_val'));
    }

    public function create(Request $request) {
        $method = $request::method();
        $post_val = [];
        $post_val = Request::all();
        if (empty($post_val) && Session::has('post_val')) {
            $post_val = Session::get('post_val');
            $payment_id = Helpers::getEncodeAndDecodeOfId($post_val['payment_detail_id'], 'decode');
            if (!empty($payment_id)) {
                $getval = PMTInfoV1::getPaymentDadetailData($payment_id);
                
                //$post_val['payment_amt'] = !is_null($getval['balance'])?$getval['balance'];
                $post_val['unapplied'] = !is_null($getval['balance']) ? $getval['balance'] : $post_val['unapplied'];
            }
        } elseif ($method == "GET" && !Session::has('post_val')) {
            return Redirect::to('payments');
        }
        $patient_id = $post_val['patient_id'];
        $api_response = $this->getCreateApi($post_val);
        $api_response_data = $api_response->getData();
        
        if ($api_response_data->status != 'error') {    
            $claims_list = $api_response_data->data->claims_lists;
            $remarkcode = $api_response_data->data->remarkcode;
            $insurance_lists = $api_response_data->data->insurance_lists;
            $insurance_list_total = $api_response_data->data->insurance_list_total;
            $check_box_count = $api_response_data->data->check_box_count;
            $view = 'payments/payments/insurance_create';
            if (Request::ajax())
                $view = 'payments/payments/apend_ajax_payment';
            unset($post_val['filefield_eob']);
            $stmt_holdreason = STMTHoldReason::getStmtHoldReasonList();
            $patID = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
            $patient_alert_note = PatientNote::where('notes_type_id', $patID)->where('notes_type', 'patient')->where('status', 'Active')->where('patient_notes_type', 'alert_notes')->select("created_by", "content")->first();
            return view($view, compact('claims_list', 'post_val', 'remarkcode', 'insurance_lists', 'patient_id', 'insurance_list_total', 'check_box_count', 'stmt_holdreason', 'patient_alert_note'));
        } elseif ($api_response_data->status == 'error') {

            return Redirect::to('payments')->with('error', $api_response_data->message);
        } else {

            return Redirect::to('payments');
        }
    }

    public function getPaymentdetail($id) {
        $api_response = $this->getPaymentcheckdataApi($id);
        $api_response_data = $api_response->getData();
        $payment_details = $api_response_data->data->payment_detail;
        return view('payments/payments/payment_claim_detail', compact('payment_details'));
    }

    public function searchcheck() {
        $api_response = $this->getPaymentcheckdataApi();
        $api_response_data = $api_response->getData();
        $insurance_list = $api_response_data->data->insurance_list;
        return view('payments/payments/search_popup', compact('insurance_list'));
    }

    public function store(Request $request) {
        $request = Request::all();
		// removed validator ('is_send_paid_amount'=>'required','change_insurance_category'=>'required',)
        /* $validate = Validator::make($request,[
                                'type'=>'required|alpha',
                                'patient_id'=>'required',
                                'payment_type'=>'required|alpha',
                                'claim_id'=>'required',
                                'payment_method'=>'required|alpha',
                                'payment_mode'=>'nullable|alpha',
                                'payment_amt'=>'required|numeric',
                                'check_no'=>'nullable|alpha_num',
                                'check_date'=>'nullable|date',
                                'deposite_date'=>'required|date',
                                'tot_billed_amt'=>'required|numeric',
                                'tot_paid_amt'=>'required|numeric',
                                'tot_balance_amt'=>'required|numeric',
                                'posting_date'=>'required|date',
                                'payment_unapplied_amt'=>'required|numeric',
                                'claim_balance'=>'required|numeric',
                                'dos_from'=>'required',
                                'cpt'=>'required|max:6',
                                'cpt_billed_amt'=>'required|between:0,99.99',
                                'cpt_allowed_amt'=>'required|between:0,99.99',
                                'balance'=>'required|between:0,99.99',
                                'co_pay'=>'required|between:0,99.99',
                                'co_ins'=>'required|between:0,99.99',
                                'with_held'=>'required|between:0,99.99',
                                'adjustment'=>'required|between:0,99.99',
                                'paid_amt'=>'required|between:0,99.99',
                                'ids'=>'required',
                                ]); */
        /* if($validate->fails()){
            return Redirect::to('/payments')->with('error', implode('<br>', (array_unique($validate->errors()->all()))));
        }else{ */
            $api_response = $this->getStoreApi($request);

            $api_response_data = $api_response->getData();
            $patient_id = $api_response_data->data;

            $payment_id = Helpers::getEncodeAndDecodeOfId(@$api_response_data->payment_id, 'encode'); // To passpayment id and get the payment related data to the next page on main payment posting
            /* if($api_response_data->status == 'success' && $request['insurance_unapplied_amt'] == 0) 
              {
              return Redirect::to('/payments')->with('success',$api_response_data->message);
              }
              else */
            if ($api_response_data->status == 'success' && $request['next'] == 1) {
                Session::put('post_val.payment_detail_id', $payment_id);
                return Redirect::to('/payments/insurancecreate')->with('success', $api_response_data->message);
            } elseif ($api_response_data->status == 'success' &&
                    $request['payment_type'] != "Adjustment") {
                return Redirect::to('/payments/paymentadd/' . $payment_id)->with('success', $api_response_data->message);
            } elseif ($api_response_data->status == 'error') {
                return Redirect::to('/payments')->with('error', $api_response_data->message);
            } else {
                return Redirect::to('/payments')->with('success', $api_response_data->message);
            }
       // }
    }

    public function paymentadd($payment_detail_id) {
        $api_response = $this->getPaymentcheckdataApi($payment_detail_id); // $this->getPaymentDetailApi($payment_detail_id);
        $api_response_data = $api_response->getData();
        $post_val = $api_response_data->data->payment_detail;
        $insurance_lists = $api_response_data->data->insurance_list;
        if (!is_null($post_val) && !empty($post_val)) {
            return view('payments/payments/paymentadd', compact('post_val', 'insurance_lists'));
        } else {
            return Redirect::to('payments');
        }
    }

    public function searchCheckInfo(Request $request) {
        $request = $request::all();
        $api_response = $this->searchCheckApi($request);
        $api_response_data = $api_response->getData();
        $payment_details = $api_response_data->data->payment_details;
        return view('payments/payments/payment_search', compact('payment_details'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function checkexist($type, $check_number, $patient_id = null) {
        $api_response = $this->checkexistApi($type, $check_number, '', $patient_id);
        $api_response_data = $api_response->getData();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function editcheck($id) {
        $api_response = $this->editCheckApi($id);
        $api_response_data = $api_response->getData();
        $payment_details = $api_response_data->data->payment_detail;
        $insurance_detail = $api_response_data->data->insurance_detail;
        $billing_providers = $api_response_data->data->billing_providers;
        $check_document_exist = $api_response_data->data->check_document_exist;
        return view('payments/payments/paymentedit', compact('payment_details', 'insurance_detail', 'billing_providers', 'check_document_exist'));
    }

    public function posteditcheck(Request $request) {
        $request = $request::all();
        $api_response = $this->updateCheckdataApi($request);
        $api_response_data = $api_response->getData();
        $payment_details = $api_response_data->data->payment_detail;
        $payment_details->url = $api_response_data->data->url;
        $payment_details->payment_amt = $api_response_data->data->payment_detail->pmt_amt;
        return json_encode($payment_details);
    }

    public function editpayment($claim_id, $type = null) {
		
        $api_response = $this->editPaymentApi($claim_id, $type);
        if (!empty($type)) {
            $patient_id = $api_response['patient_id'];
            return Redirect::to('patients/' . $patient_id . '/payments');
        }
        return Redirect::to('payments');
    }

    public function searchclaim($insurace_id, $patient_id) {
        $sel_claim = '';
        if ($insurace_id == "insurance")
            $insurace_id = '';
        $api_response = $this->searchClaimbyInsuranceApi($insurace_id, $patient_id);
        $api_response_data = $api_response->getData();
        $claims_lists = $api_response_data->data->claim_lists;
        return view('patients/payments/payment_claim_display', compact('claims_lists', 'sel_claim'));
    }

    public function getClaimdata($claim_id) {
        $api_response = $this->getClaimdataApi($claim_id);
        $api_response_data = $api_response->getData();
        $claim = $api_response_data->data->claim_data;
        return view('payments/payments/armanagement_claim_append', compact('claim'));
    }

    public function delete($id, $type = null) {
        $api_response = $this->getDeleteApi($id, $type);
        $api_response_data = $api_response->getData();
        $status = $api_response_data->status;
        return $status;
    }
  
    public function download_e_remittance() {
        $ar_main_page = 'payments';
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('e_remittance');
        // $api_response = $this->getSearchApi();
        // $api_response_data = $api_response->getData();
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        if(Request::ajax()) {
            $data = $this->getEra();
            return Response::json($data);
        }
        // $e_remittance = $api_response_data->data->e_remittance;
		
        return view('payments/payments/payments_remittance', compact('searchUserData', 'search_fields'));
        // return view('payments/payments/payments_remittance', compact('e_remittance', 'ar_main_page', 'clearing_house_details'));
    }

    public function getEra()
    {   $request = Request::all();
        $api_response = $this->getEraApi($request);
        $api_response_data = $api_response->getData();
        $e_remittance = (!empty($api_response_data->data->e_remittance))? (array)$api_response_data->data->e_remittance:[];
        $view_html = Response::view('payments/payments/era_ajax', compact('e_remittance'));
        $content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
        
        if (!empty($request['draw']))
           $data['draw'] = $request['draw'];
        $data['data'] = $content;
        $data['recordsTotal'] = $api_response_data->data->count;
        $data['recordsFiltered'] = $api_response_data->data->count;
        return $data;
    }
    
    public function export_e_remittance($export = '') {
        $request = Request::all();
        $api_response = $this->getEraApi($request,$export);
        $api_response_data = $api_response->getData();
        $e_remittance = (!empty($api_response_data->data->e_remittance))? (array)$api_response_data->data->e_remittance:[];
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Payments_E_remittance' . $date;

        if ($export == 'pdf') {
            $html = view('payments/payments/payments_remittance_export_pdf', compact('e_remittance', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'payments/payments/payments_remittance_export';
            $data['e_remittance'] = $e_remittance;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'payments/payments/payments_remittance_export';
            $data['e_remittance'] = $e_remittance;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
  
	public function manual_download_e_remittance(){ 
        $path_medcubic = public_path() . '/';
		$local_path = $path_medcubic . 'media/era_files/' . Session::get('practice_dbid') . '/';
        if (!file_exists($local_path)) {
            mkdir($local_path, 0777, true);
        }
		/* Original zip storage folder*/
		$zipFilePath = $local_path.'EraZipFile/';
		if(!file_exists($zipFilePath))
			mkdir($zipFilePath, 0777, true);
        $clearing_house_details = ClearingHouse::where('status', 'Active')->where('practice_id', Session::get('practice_dbid'))->first();
        $error_code = '';
        $file_count = 0;
		$chkCount = 0;
        if (count((array)$clearing_house_details) > 0) {
            $ClearingHouseType = $clearing_house_details->name;
            $ftp_server = $clearing_house_details->ftp_address;
            $ftp_username = $clearing_house_details->ftp_user_id;
            $ftp_password = $clearing_house_details->ftp_password;
            $ftp_port = $clearing_house_details->ftp_port;
			if($ClearingHouseType == "OfficeAlly")
				$destination_file = $clearing_house_details->edi_report_folder;
			else
				$destination_file = $clearing_house_details->edi_report_folder."/835";
			// \Log::info($destination_file);
            if (!function_exists("ssh2_connect")) {
                $status = 'error';
                $error_code = 'Function ssh2_connect not found, you cannot use ssh2 here';
				return $error_code;
            } elseif (!$connection = ssh2_connect($ftp_server, $ftp_port)) {
                $status = 'error';
                $error_code = 'Connection cannot be made to clearing house. Please contact administrator';
				return $error_code;
            } elseif (!ssh2_auth_password($connection, $ftp_username, $ftp_password)) {
                $status = 'error';
                $error_code = 'Not able to connect to Clearing House; Please check if the clearing house is working or the login credentials has been changed';
				return $error_code;
            } elseif (!$stream = ssh2_sftp($connection)) {
                $status = 'error';
                $error_code = 'Connection cannot be made to clearing house. Please contact administrator';
				return $error_code;
            } elseif (!$dir = opendir("ssh2.sftp://" . intval($stream) . "/{$destination_file}/./")) {
                $status = 'error';
                $error_code = 'ssh2.sftp://' . $stream . $destination_file . 'Could not open the directory';
				return $error_code;
            }
            $files = array();
			
            if (empty($error_code)) { 
                while (false !== ($file = readdir($dir))) {
					if($ClearingHouseType == 'OfficeAlly'){
						if ($file == "." || $file == "..")
							continue;
						$filename = $file;
						$local_file = $local_path . $filename;
						$data_arr = array();
						$check_count = 0;
						$file_type = explode('.',$file);
						if(isset($file_type[1]) && !empty($file_type[1])){
							if(strtolower($file_type[1]) == 'zip'){
								 if(!file_exists($zipFilePath . $file)) {
									$file_count++;
									$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file);
									$myerafile = fopen($zipFilePath . $file, "w+");
									fwrite($myerafile, $file_content);						
									if(!is_dir($zipFilePath.$file_type[0])){
										mkdir($local_path.$file_type[0], 0777, true);
										$extractLocation = $local_path.$file_type[0];
										Zipper::make($zipFilePath.$file)->extractTo($extractLocation,array($zipFilePath.$file));
										$Era835File = $extractLocation.'/'.str_replace('STATUS','835',$file_type[0]).'.835';
										$file_content = file_get_contents($Era835File);
										$file_full_content = explode('~', $file_content);
										
										if (strpos($file_content, "ST*835") !== FALSE) {
											$symb_check = implode('', $file_full_content);
											$first_segment = $file_full_content[0];
											if (count(explode('|', $symb_check)) > 5) {
												$separate = "|";
											} elseif (count(explode('*', $symb_check)) > 1) {
												$separate = "*";
											}
											$claimCount = 0;

											foreach ($file_full_content as $key => $segment) {
												if (substr($segment, 0, 3) == 'ST' . $separate) {
													$check_count++;
												}

												if (substr($segment, 0, 4) == 'TRN' . $separate) {
													$temp = explode($separate, $segment);
													// Remove remove special characters from cheque no in era
													// Revision 1 : MR-2799 : 6 Sep 2019 : Selva
													$data_arr[$check_count]['org_check_no'] = $temp[2];
													$temp[2] = preg_replace("/[^a-zA-Z0-9]/", "", $temp[2]);
													$data_arr[$check_count]['check_no'] = $temp[2];
													
												}
												if (substr($segment, 0, 6) == 'N1' . $separate . 'PR' . $separate) {
													$temp = explode($separate, $segment);
													if (!empty($temp[2]))
														$data_arr[$check_count]['insurance_name'] = $temp[2];
												}
												if (substr($segment, 0, 4) == 'BPR' . $separate) {
													$temp = explode($separate, $segment);
													if (!empty($temp[16]))
														$data_arr[$check_count]['check_date'] = date('Y-m-d', strtotime($temp[16]));
													$data_arr[$check_count]['check_paid_amount'] = $temp[2];
												}
												if (substr($segment, 0, 4) == 'CLP' . $separate) {
													$claimCount ++;
													$temp = explode($separate, $segment);
													if (!empty($temp[1]))
														$data_arr[$check_count]['claimNumber'][$temp[1]] = 'No';
													if (!empty($temp[3]))
														$data_arr[$check_count]['check_amount'] = 0;
												}
												
												if (substr($segment, 0, 7) == 'NM1' . $separate . 'QC' . $separate) {
													$temp = explode($separate, $segment);
													if(isset($temp[8]) && $temp[8] == 'MI' && isset($temp[9]) && !empty($temp[9]))
														$data_arr[$check_count]['insurance_id'] = $this->findInsuranceID($temp[9]);	
												}
												
												if ((substr($segment, 0, 7) == 'NM1' . $separate . '74' . $separate) && (empty(@$data_arr[$check_count]['insurance_id']))) {
													$temp = explode($separate, $segment); 
													if(isset($temp[8]) && $temp[8] == 'C' && isset($temp[9]) && !empty($temp[9]))
														$data_arr[$check_count]['insurance_id'] = $this->findInsuranceID($temp[9]);	
												}
												
												if ((substr($segment, 0, 7) == 'NM1' . $separate . 'IL' . $separate) && (empty(@$data_arr[$check_count]['insurance_id']))) {
													$temp = explode($separate, $segment); 
													if(isset($temp[8]) && $temp[8] == 'MI' && isset($temp[9]) && !empty($temp[9]))
														$data_arr[$check_count]['insurance_id'] = $this->findInsuranceID($temp[9]);	
												}

												if (substr($segment, 0, 6) == 'N1' . $separate . 'PE' . $separate) {
													$temp = explode($separate, $segment);
													$data_arr[$check_count]['provider_npi_id'] = $temp[4];
												}
												if ($check_count != 0) {
													$data_arr[$check_count]['receive_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file));
													//$data_arr[$check_count]['receive_date'] = date("Y-m-d", filemtime($local_path . $file));
													$data_arr[$check_count]['pdf_name'] = $filename;
												}
											}
											$data_arr[$check_count]['total_claims'] = $claimCount;
											foreach ($data_arr as $single_cheque_data_arr) {
												$single_cheque_data_arr['claim_nos'] = json_encode(@$single_cheque_data_arr['claimNumber']);
												
												$created = Eras::create($single_cheque_data_arr);
												$chkCount++;
											}
										}
									}
								}
							}
						}
					}elseif($ClearingHouseType == 'Navicure'){
						if ($file == "." || $file == "..")
							continue;
						$filename = $file;
						
						$local_file = $local_path . $filename;
						$data_arr = array();
						$check_count = 0;
						$file_type = explode('.',$file);
						$org_filename = $file_type[0]."-STATUS";
						if(strtolower($file_type[1]) == '835'){
							 if(!file_exists($zipFilePath . $org_filename.".835")) {
								$file_count++;
								$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file);
								$myerafile = fopen($zipFilePath . $org_filename.".835", "w+");
								fwrite($myerafile, $file_content);						
								if(!is_dir($local_path.$org_filename)){
									$tempPath = $local_path.$org_filename;
									$tempName = str_replace('STATUS','835',$org_filename).".835";
                                    if (!file_exists($tempPath)) {
                                        mkdir($tempPath, 0777, true);
                                    }
																		
									@fopen($tempPath ."/". $tempName, 'w');
									$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file);
									$myera835file = fopen($tempPath ."/". $tempName, "w+");
									fwrite($myera835file, $file_content);
									$Era835File = $tempPath.'/'.str_replace('STATUS','835',$org_filename).'.835';
									$file_content = file_get_contents($Era835File);
									$file_full_content = explode('~', $file_content);
									
									if (strpos($file_content, "ST*835") !== FALSE) {
										$symb_check = implode('', $file_full_content);
										$first_segment = $file_full_content[0];
										if (count(explode('|', $symb_check)) > 5) {
											$separate = "|";
										} elseif (count(explode('*', $symb_check)) > 1) {
											$separate = "*";
										}
										$claimCount = 0;

										foreach ($file_full_content as $key => $segment) {
											if (substr($segment, 0, 3) == 'ST' . $separate) {
												$check_count++;
											}

											if (substr($segment, 0, 4) == 'TRN' . $separate) {
												$temp = explode($separate, $segment);
												// Removed special characters from cheque no in era
												// Revision 1 : MR-2799 : 6 Sep 2019 : Selva
												$data_arr[$check_count]['org_check_no'] = $temp[2];
												$temp[2] = preg_replace("/[^a-zA-Z0-9]/", "", $temp[2]);
												$data_arr[$check_count]['check_no'] = $temp[2];
												
											}
											if (substr($segment, 0, 6) == 'N1' . $separate . 'PR' . $separate) {
												$temp = explode($separate, $segment);
												if (!empty($temp[2]))
													$data_arr[$check_count]['insurance_name'] = $temp[2];
											}
											if (substr($segment, 0, 4) == 'BPR' . $separate) {
												$temp = explode($separate, $segment);
												if (!empty($temp[16]))
													$data_arr[$check_count]['check_date'] = date('Y-m-d', strtotime($temp[16]));
												$data_arr[$check_count]['check_paid_amount'] = $temp[2];
											}
											if (substr($segment, 0, 4) == 'CLP' . $separate) {
												$claimCount ++;
												$temp = explode($separate, $segment);
												if (!empty($temp[1]))
													$data_arr[$check_count]['claimNumber'][$temp[1]] = 'No';
												if (!empty($temp[3]))
													$data_arr[$check_count]['check_amount'] = 0;
											}
											
											if (substr($segment, 0, 7) == 'NM1' . $separate . 'QC' . $separate) {
												$temp = explode($separate, $segment);
												if(isset($temp[8]) && $temp[8] == 'MI' && isset($temp[9]) && !empty($temp[9]))
													$data_arr[$check_count]['insurance_id'] = $this->findInsuranceID($temp[9]);	
											}
											
											if ((substr($segment, 0, 7) == 'NM1' . $separate . '74' . $separate) && (empty(@$data_arr[$check_count]['insurance_id']))) {
												$temp = explode($separate, $segment); 
												if(isset($temp[8]) && $temp[8] == 'C' && isset($temp[9]) && !empty($temp[9]))
													$data_arr[$check_count]['insurance_id'] = $this->findInsuranceID($temp[9]);	
											}
											
											if ((substr($segment, 0, 7) == 'NM1' . $separate . 'IL' . $separate) && (empty(@$data_arr[$check_count]['insurance_id']))) {
												$temp = explode($separate, $segment); 
												if(isset($temp[8]) && $temp[8] == 'MI' && isset($temp[9]) && !empty($temp[9]))
													$data_arr[$check_count]['insurance_id'] = $this->findInsuranceID($temp[9]);	
											}

											if (substr($segment, 0, 6) == 'N1' . $separate . 'PE' . $separate) {
												$temp = explode($separate, $segment);
												$data_arr[$check_count]['provider_npi_id'] = $temp[4];
											}
											if ($check_count != 0) {
												$data_arr[$check_count]['receive_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file));
												//$data_arr[$check_count]['receive_date'] = date("Y-m-d", filemtime($local_path . $file));
												$data_arr[$check_count]['pdf_name'] = $org_filename.".835";
											}
										}
										$data_arr[$check_count]['total_claims'] = $claimCount;
										foreach ($data_arr as $single_cheque_data_arr) {
											$single_cheque_data_arr['claim_nos'] = json_encode(@$single_cheque_data_arr['claimNumber']);
											
											$created = Eras::create($single_cheque_data_arr);
											$chkCount++;
										}
									}
								}
							}
						}
					}
                }
             } else {
                $error_code = 'You have no clearing house setup. Please contact administrator';
            }
            if ($file_count == 0 && $error_code == '') {
                $error_code = 'There are no EOB / ERA 835 downloads available. Do try after sometime';
            }else{
				 $error_code = 'ERA 835 downloaded';
			}
            return $error_code;
        }
    }

    public function pdf_generation($id = '', $cheque = '') {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $filename = Eras::where('id', $id)->pluck('pdf_name')->first();
        $eraFileName = explode('.',$filename);
        $orgERAFolderName = $eraFileName[0];
        $orgERAFileName = str_replace('STATUS','835',$eraFileName[0]).'.835';
        $path_medcubic = public_path() . '/';
        $local_path = $path_medcubic . 'media/era_files/' . Session::get('practice_dbid') . '/'.$orgERAFolderName.'/';
        $check_count = 0;
        /**
         * Declaration part array's and variables
         */
        $glossary = $basic_info = $insert_data = [];
        foreach (glob($local_path . $orgERAFileName) as $list) {

            /**
             * Getting file content using file function
             * Convert the file content into array using (~)
             */
            $file_content = file($list);
            $file_full_content = explode('~', $file_content[0]);

            /**
             * Using file content to find separator
             */
            $symb_check = implode('', $file_full_content);
            $first_segment = $file_full_content[0];
            if (count(explode('|', $symb_check)) > 5) {
                $separate = "|";
            } elseif (count(explode('*', $symb_check)) > 1) {
                $separate = "*";
            }
            $spl_symb = explode($separate, $first_segment);
            $spl_separate = $spl_symb[16];

            /**
             * Separating the segment and getting data in the segment
             */
            foreach ($file_full_content as $key => $segment) {
                if (substr($segment, 0, 3) == 'ST' . $separate) {
                    $check_count++;
                    $basic_count = 0;
                    $claim_count = 0;
                    $claim_cpt_count = 0;
                }

                if (substr($segment, 0, 6) == 'N1' . $separate . 'PR' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['payer']['insurance_company'] = $temp[2];
                    $basic_count ++;
                }

                if (substr($segment, 0, 3) == 'N3' . $separate && $basic_count == 1) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$check_count]['payer']['insurance_address_info1'] = $temp[1];
                    if (!empty($temp[2])) {
                        $basic_info[$check_count]['payer']['insurance_address_info2'] = $temp[2];
                    }
                }

                if (substr($segment, 0, 3) == 'N4' . $separate && $basic_count == 1) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$check_count]['payer']['insurance_city'] = $temp[1];
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['payer']['insurance_state'] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['payer']['insurance_zipcode'] = $temp[3];
                }

                if (substr($segment, 0, 6) == 'N1' . $separate . 'PE' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['payee']['practice_company'] = $temp[2];
                    if (!empty($temp[4]))
                        $basic_info[$check_count]['payee']['payee_npi_id'] = $temp[4];
                    $basic_count ++;
                }

                if (substr($segment, 0, 3) == 'N3' . $separate && $basic_count == 2) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$check_count]['payee']['practice_address_info1'] = $temp[1];
                    if (!empty($temp[2])) {
                        $basic_info[$check_count]['payee']['practice_address_info2'] = $temp[2];
                    }
                }

                if (substr($segment, 0, 3) == 'N4' . $separate && $basic_count == 2) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$check_count]['payee']['practice_city'] = $temp[1];
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['payee']['practice_state'] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['payee']['practice_zipcode'] = $temp[3];
                }

                if (substr($segment, 0, 4) == 'TRN' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['check_details']['check_no'] = $temp[2];
                }

                if (substr($segment, 0, 4) == 'BPR' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[16]))
                        $basic_info[$check_count]['check_details']['check_date'] = date('Y-m-d', strtotime($temp[16]));
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['check_details']['check_paid_amount'] = $temp[2];
                }

                if (substr($segment, 0, 4) == 'CLP' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['check_details']['check_amount'] = $temp[3];
                }

                if (substr($segment, 0, 4) == 'CLP' . $separate) {
                    $claim_count++;
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$check_count]['claim'][$claim_count]['claim_id'] = $temp[1];
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['claim_insurance_type'] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['claim'][$claim_count]['claim_total_amount'] = $temp[3];
                    if (!empty($temp[4]))
                        $basic_info[$check_count]['claim'][$claim_count]['claim_paid_amount'] = $temp[4];
                    if (!empty($temp[5]))
                        $basic_info[$check_count]['claim'][$claim_count]['claim_coins_amount'] = $temp[5];
                    if (!empty($temp[7]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_icn'] = $temp[7];
                }

                if (substr($segment, 0, 4) == 'CAS' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$check_count]['claim'][$claim_count]['claims_adj'] = $temp[1];
                }

                if (substr($segment, 0, 7) == 'NM1' . $separate . 'QC' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_lastname'] = $temp[3];
                    if (!empty($temp[4]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_firstname'] = $temp[4];
                    if (!empty($temp[5]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_Suffix'] = $temp[5];
                    if (!empty($temp[9]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_hic'] = $temp[9];
                }

                if (substr($segment, 0, 4) == 'MOA' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_moa'] = $temp[3];
                }

                if (substr($segment, 0, 4) == 'DTM' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]) && $temp[1] == 232)
                        $basic_info[$check_count]['claim'][$claim_count]['start_date'] = $temp[2];
                    if (!empty($temp[2]) && $temp[1] == 233)
                        $basic_info[$check_count]['claim'][$claim_count]['end_date'] = $temp[2];
                    if (!empty($temp[2]) && $temp[1] == 050)
                        $basic_info[$check_count]['claim'][$claim_count]['patient_statement_date'] = date('d/m/Y', strtotime($temp[2]));
                }

                if (substr($segment, 0, 4) == 'SVC' . $separate) {
                    $claim_cpt_count ++;
                    $temp = explode($separate, $segment);
                    $temp_proc = explode($spl_separate, $temp[1]);
                    if (!empty($temp_proc[1]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['proc'] = $temp_proc[1];
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['billed_amount'] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['insurance_paid_amount'] = $temp[3];
                    if (!empty($temp[5]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['units'] = $temp[5];
                }

                if (substr($segment, 0, 4) == 'DTM' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]) && $temp[1] == 150)
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['start_date'] = $temp[2];
                    if (!empty($temp[2]) && $temp[1] == 151)
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['end_date'] = $temp[2];
                    if (!empty($temp[2]) && $temp[1] == 472)
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['service_date'] = $temp[2];
                }

                if (substr($segment, 0, 4) == 'CAS' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['type_' . $temp[1]] = $temp[1];
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['type_coins_' . $temp[1]] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['type_' . $temp[1]] = $temp[3];
                    if ($temp[1] == 'CO' || $temp[1] == 'OA' || $temp[1] == 'PI')
                        $glossary[] = $temp[2];
                }

                if (substr($segment, 0, 7) == 'REF' . $separate . 'LU' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['pos'] = $temp[2];
                }

                if (substr($segment, 0, 7) == 'AMT' . $separate . 'B6' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['allowed'] = $temp[2];
                }
            }
        }
        //echo "<pre>";print_r($basic_info);die;
        $glossary_details = array();
        foreach ($glossary as $code_list) {
            $codes_details = Code::where('transactioncode_id', $code_list)->pluck('description')->first();
            $glossary_details[$code_list] = $codes_details;
        }
        //return view('payments/payments/era_pdf_generation',compact('basic_info','glossary_details','cheque'));
        $filename_only = explode('.', $filename);
        PDF::loadHTML(view('payments/payments/era_pdf_generation', compact('basic_info', 'glossary_details', 'cheque')))->download($filename_only[0] . ".pdf");
    }

    public function auto_post() {
        /*
         * 	server era file path decalartion.
         */
       
		$path_medcubic = public_path() . '/';

        $server_local_path = $path_medcubic . 'media/era_files/' . Session::get('practice_dbid') . '/';

        /*
         * 	Variable decalartion part
         */
        $request = Request::all();
        $total_claim_success = '';
        $claim_fail_count = '';
        $auto_post_details = Eras::whereIn('id', $request['id'])->select('id', 'pdf_name', 'check_no')->get()->toArray();
        foreach ($auto_post_details as $table_data) {
            foreach (glob($server_local_path . $table_data['pdf_name']) as $list) {
                $claim_data = array();
                $claimdoscpt_data = array();
                $payments_data = array();
                $payment_claim_data = array();
                $payment_claim_ctp_data = array();

                $payments_data['payment_amt'] = 0;
                $payments_data['balance'] = 0.00;
                $payments_data['type'] = "posting";
                $payments_data['payment_method'] = "Insurance";
                $payments_data['payment_type'] = "Payment";
                $payments_data['paymentnumber'] = PMTInfoV1::generatepaymentid($payments_data);
                $payments_data['payment_mode'] = "Check";
                $payments_data['created_by'] = Auth::user()->id;

                $file_content = file($list);
                $file_full_content = explode('~', $file_content[0]);
                $symb_check = implode('', $file_full_content);
                $first_segment = $file_full_content[0];

                if (count(explode('|', $symb_check)) > 5) {
                    $separate = "|";
                } elseif (count(explode('*', $symb_check)) > 1) {
                    $separate = "*";
                }

                $spl_symb = explode($separate, $first_segment);
                $spl_separate = $spl_symb[16];
                $claim_count = '';
                $claim_cpt_count = '';
                foreach ($file_full_content as $key => $segment) {
                    if (substr($segment, 0, 4) == 'TRN' . $separate) {
                        $temp = explode($separate, $segment);
                        if (!empty($temp[2])) {
                            $payments_data['check_no'] = $temp[2];
                        }
                    }
                    if (substr($segment, 0, 4) == 'BPR' . $separate) {
                        if (!empty($temp[16]))
                            $payments_data['check_date'] = date('Y-m-d', strtotime($temp[16]));
                        $payments_data['deposite_date'] = date('Y-m-d');
                    }
                    if (substr($segment, 0, 4) == 'CLP' . $separate) {
                        $claim_count++;
                        $temp = explode($separate, $segment);
                        $payment_claim_data[$claim_count]['payment_type'] = "Insurance";
                        $payment_claim_data[$claim_count]['posting_date'] = date("Y-m-d");
                        $payment_claim_data[$claim_count]['created_by'] = Auth::user()->id;
                        if (!empty($temp[1])) {
                            $cliam_details = ClaimInfoV1::where('claim_number', $temp[1])->get()->toArray();
                            $claim_data['claim_number'] = $temp[1];
                            $payment_claim_data[$claim_count]['payment_id'] = "demo";
                            $payment_claim_data[$claim_count]['claim_id'] = $cliam_details[0]['id'];
                            $payment_claim_data[$claim_count]['patient_id'] = $cliam_details[0]['patient_id'];
                            $payments_data['patient_id'] = $cliam_details[0]['patient_id'];
                            $claim_data['patient_id'] = $cliam_details[0]['patient_id'];
                            $claim_data['rendering_provider_id'] = $cliam_details[0]['rendering_provider_id'];
                            $payments_data['billing_provider_id'] = $claim_data['billing_provider_id'] = $cliam_details[0]['billing_provider_id'];
                            $claim_data['facility_id'] = $cliam_details[0]['facility_id'];
                            $payment_claim_data[$claim_count]['payer_insurace_id'] = $cliam_details[0]['insurance_id'];
                            $payment_claim_data[$claim_count]['insurace_id'] = $cliam_details[0]['insurance_id'];
                            $payments_data['insurance_id'] = $cliam_details[0]['insurance_id'];
                            $claim_data['insurance_id'] = $cliam_details[0]['insurance_id'];
                        }
                        if (!empty($temp[2])) {
                            if ($temp[2] == 4)
                                $claim_data['status'] = "Denied";
                            if ($temp[2] == 1)
                                $payment_claim_data[$claim_count]['insurace_category'] = $claim_data['insurance_category'] = "Primary";
                            if ($temp[2] == 2)
                                $payment_claim_data[$claim_count]['insurace_category'] = $claim_data['insurance_category'] = "Secondary";
                            if ($temp[2] == 3)
                                $payment_claim_data[$claim_count]['insurace_category'] = $claim_data['insurance_category'] = "Tertiary";
                        }

                        if (!empty($temp[3])) {
                            $payment_claim_data[$claim_count]['total_allowed'] = $temp[3];
                        }

                        if (!empty($temp[4])) {
                            $payments_data['payment_amt'] = $payments_data['payment_amt'] + $temp[4];
                            $payments_data['amt_used'] = $payments_data['payment_amt'];
                            $payment_claim_data[$claim_count]['insurance_paid_amt'] = $temp[4];
                            $payment_claim_data[$claim_count]['balance'] = $payment_claim_data[$claim_count]['total_allowed'] - $temp[4];
                            $payment_claim_data[$claim_count]['insurance_due'] = $payment_claim_data[$claim_count]['total_allowed'] - $temp[4];
                            $payment_claim_data[$claim_count]['total_adjusted'] = $cliam_details[0]['total_charge'] - $payment_claim_data[$claim_count]['insurance_due'];
                            $claim_data['total_paid'] = $cliam_details[0]['total_paid'] + $temp[4];
                            $claim_data['insurance_paid'] = $cliam_details[0]['insurance_paid'] + $temp[4];
                            $claim_data['insurance_due'] = $cliam_details[0]['insurance_due'] - $temp[4];
                        }
                    }

                    if (substr($segment, 0, 4) == 'SVC' . $separate) {
                        $cpt_count = $claim_cpt_count++;
                        $claim_cpt_counts = $claim_count . "." . $cpt_count;
                        $temp = explode($separate, $segment);
                        $temp_proc = explode($spl_separate, $temp[1]);

                        if (!empty($temp_proc[1])) {
                            /* @todo - check and remove this
                              $claim_cpt_details = Claimdoscptdetail::where('patient_id', $claim_data['patient_id'])->where('claim_id', $cliam_details[0]['id'])->where('cpt_code', $temp_proc[1])->get()->toArray();
                              $claim_cpt_id = $claim_cpt_details[0]['id'];
                              $payment_claim_ctp_data[$claim_cpt_counts]['claimdoscptdetail_id'] = $claim_cpt_details[0]['id'];
                              $claimdoscpt_data[$claim_cpt_counts][0]['cpt_code'] = $temp_proc[1];
                              $claimdoscpt_data[$claim_cpt_counts][0]['id'] = $claim_cpt_id;
                             * *
                             */
                        }
                        $claimdoscpt_data[$claim_cpt_counts][0]['cpt_allowed_amt'] = $temp[2];
                        $claimdoscpt_data[$claim_cpt_counts][0]['cpt_billed_amt'] = $temp[3];
                        $claimdoscpt_data[$claim_cpt_counts][0]['patient_id'] = $claim_data['patient_id'];
                        $claimdoscpt_data[$claim_cpt_counts][0]['claim_id'] = $cliam_details[0]['id'];
                        $payment_claim_ctp_data[$claim_cpt_counts]['paid_amt'] = $temp[3];
                        $payment_claim_ctp_data[$claim_cpt_counts]['billed_amt'] = $claim_cpt_details[0]['charge'];
                        $payment_claim_ctp_data[$claim_cpt_counts]['allowed_amt'] = $temp[2];
                        $payment_claim_ctp_data[$claim_cpt_counts]['balance_amt'] = $temp[2] - $temp[3];
                        $payment_claim_ctp_data[$claim_cpt_counts]['insurance_balance'] = $temp[2] - $temp[3];
                        $payment_claim_ctp_data[$claim_cpt_counts]['claim_id'] = $cliam_details[0]['id'];
                        $payment_claim_ctp_data[$claim_cpt_counts]['patient_id'] = $cliam_details[0]['patient_id'];
                        $payment_claim_ctp_data[$claim_cpt_counts]['insurance_id'] = $cliam_details[0]['insurance_id'];
                        $payment_claim_ctp_data[$claim_cpt_counts]['payer_insurace_id'] = $cliam_details[0]['insurance_id'];
                        $payment_claim_ctp_data[$claim_cpt_counts]['created_by'] = Auth::user()->id;
                        $payment_claim_ctp_data[$claim_cpt_counts]['posting_type'] = "Insurance";
                    }

                    if (substr($segment, 0, 4) == 'CAS' . $separate) {
                        $temp = explode($separate, $segment);
                        if (!empty($temp[3])) {
                            $claimdoscpt_data[$claim_cpt_counts][0]['co_ins'] = $temp[3];
                        }
                    }

                    if (substr($segment, 0, 7) == 'AMT' . $separate . "B6" . $separate) {
                        $temp = explode($separate, $segment);
                        if (!empty($temp[2])) {
                            $claimdoscpt_data[$claim_cpt_counts][0]['paid_amt'] = $temp[2];
                            $claimdoscpt_data[$claim_cpt_counts][0]['insurance_paid'] = $temp[2];
                            $payment_claim_ctp_data[$claim_cpt_counts]['insurance_paid'] = $temp[2];
                            $claimdoscpt_data[$claim_cpt_counts][0]['balance'] = $claim_cpt_details[0]['balance'] - $claimdoscpt_data[$claim_cpt_counts][0]['cpt_billed_amt'];
                            $claimdoscpt_data[$claim_cpt_counts][0]['insurance_balance'] = $claim_cpt_details[0]['balance'] - $claimdoscpt_data[$claim_cpt_counts][0]['cpt_billed_amt'];
                        }
                    }
                }
                $claim_full_data['claim'] = $claim_data;
                $claim_full_data['claim_cpt'] = $claimdoscpt_data;
                $payment_full_data[0]['payment'] = $payments_data;
                $payment_full_data[0]['payment_claim'][0] = $payment_claim_data;
                $payment_full_data[0]['payment_claim'][0]['payment_claim_cpt'] = $payment_claim_ctp_data;

                DB::beginTransaction();
                try {
                    $this->payment_insert($payment_full_data);
                    $this->claim_update($claim_data, $claimdoscpt_data);
                    Eras::where('id', $table_data['id'])->where('check_no', $table_data['check_no'])->update(['status' => 'Yes']);
                    //DB::commit();
                    $total_claim_success = $total_claim_success + $claim_count;
                } catch (\Exception $e) {
                    DB::rollback();
                    $claim_fail_count++;
                }
            }
        }
        $data['eras_ids'] = Eras::where('status', 'Yes')->select('id')->get()->toArray();
        $data['eras_ids'] = array_column($data['eras_ids'], 'id');
        $data['claim_success_count'] = $total_claim_success - $claim_fail_count;
        if (empty($claim_fail_count))
            $data['claim_fail_count'] = 0;
        else
            $data['claim_fail_count'] = $claim_fail_count;

        return $data;
    }

    public function claim_update($claim_data, $claimdoscpt_data) {
        $claim_details = ClaimInfoV1::where('claim_number', $claim_data['claim_number'])->update($claim_data);
        /** @todo - check and remove 
          foreach ($claimdoscpt_data as $data) {
          Claimdoscptdetail::where('id', $data[0]['id'])->update($data[0]);
          }
         */
    }

    public function payment_insert($payment_full_data) {
        // @todo - check and implement new pmt flow

        foreach ($payment_full_data as $data) {
            $payment_details = PMTInfoV1::create($data['payment']);
            $payment_id = $payment_details->id;
            $payment_claim_count = 1;
            foreach ($data['payment_claim'] as $subdata) {
                $subdata[$payment_claim_count]['payment_id'] = $payment_id;
                //$PaymentClaim_details = PaymentClaimDetail::create($subdata[$payment_claim_count]);
                $payment_claim_detail_id = $PaymentClaim_details->id;
                $payment_claim_count++;

                foreach ($subdata['payment_claim_cpt'] as $subinnerdata) {
                    $subinnerdata['payment_claim_detail_id'] = $payment_claim_detail_id;
                    $subinnerdata['payment_id'] = $payment_id;
                    //PaymentClaimCtpDetail::create($subinnerdata);
                }
            }
        }
    }

    public function clearing_house_response() {
		
        $file_count = 0;
        $claims_count = 0;
        $claim_accpet = 0;
        $claim_reject = 0;
		$status = $error_code = '';
        $dataArr = array();
		
        if (App::environment() == "local") {
            $path_medcubic = public_path() . '/';
            $local_path = $path_medcubic . 'media/clearingHouse_response/' . Session::get('practice_dbid') . '/';
            if (!file_exists($local_path)) {
                mkdir($local_path, 0777, true);
            }
            $clearing_house_details = ClearingHouse::where('status', 'Active')->where('practice_id', Session::get('practice_dbid'))->first();

            if (count((array)$clearing_house_details) > 0) {
                $clearingHouseType = $clearing_house_details->name;
                $ftp_server = $clearing_house_details->ftp_address;
                $ftp_username = $clearing_house_details->ftp_user_id;
                $ftp_password = $clearing_house_details->ftp_password;
                $ftp_port = $clearing_house_details->ftp_port;
				
				if($clearingHouseType == 'OfficeAlly')
					$destination_file = $clearing_house_details->edi_report_folder;
				else
					$destination_file = $clearing_house_details->edi_report_folder."/277";

                if (!function_exists("ssh2_connect")) {
                    $status = 'error';
                    $error_code = 'Function ssh2_connect not found, you cannot use ssh2 here';
                } elseif (!$connection = ssh2_connect($ftp_server, $ftp_port)) {
                    $status = 'error';
                    $error_code = 'Connection cannot be made to clearing house. Please contact administrator';
                } elseif (!ssh2_auth_password($connection, $ftp_username, $ftp_password)) {
                    $status = 'error';
                    $error_code = 'Connection cannot be made to clearing house. Please contact administrator';
                } elseif (!$stream = ssh2_sftp($connection)) {
                    $status = 'error';
                    $error_code = 'Connection cannot be made to clearing house. Please contact administrator';
                } elseif (!$dir = opendir("ssh2.sftp://" . intval($stream) . "/{$destination_file}/./")) {
                    $status = 'error';
                    $error_code = 'ssh2.sftp://' . $stream . $destination_file . 'Could not open the directory';
                }
                $files = array();
				$navicureClaimCount = 0;
                while (false !== ($file = readdir($dir))) {
					if($clearingHouseType == 'OfficeAlly'){
						if (substr($file, 0, 8) == 'FS_HCFA_' && substr($file, 18, 11) != 'ErrorReport') {
							if ($file == "." || $file == "..")
								continue;
							$file_name = basename($file);

							if (!file_exists($local_path . $file_name)) {
								DB::beginTransaction();
								try {
									$file_count++;
									@fopen($local_path . $file, 'w');
									$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file);

									$myerafile = fopen($local_path . $file, "w+");
									fwrite($myerafile, $file_content);
									
									$read_claim_item = 0;
									$claim_count = 1;
									$dataArr = array();
									$file_content = file($local_path . $file_name);
									foreach ($file_content as $key => $file_line) {

										if (substr($file_line, 0, 6) == 'CLAIM#') {
											$read_claim_item = 1;
										}
										if ($read_claim_item == 1 && (substr($file_line, 0, 2) == $claim_count . ')' || substr($file_line, 0, 3) == $claim_count . ')' || substr($file_line, 0, 4) == $claim_count . ')')) {
											$claims_count ++;
											$dataArr[$claim_count]['CLAIM#'] = substr($file_line, 0, 6);
											$dataArr[$claim_count]['OA_Claim_ID'] = substr($file_line, 7, 10);
											$dataArr[$claim_count]['PATIENT_ID'] = substr($file_line, 19, 18);
											$dataArr[$claim_count]['LAST_FIRST'] = substr($file_line, 37, 20);
											$dataArr[$claim_count]['DOB'] = substr($file_line, 57, 10);
											$dataArr[$claim_count]['FROM_DOS'] = substr($file_line, 70, 10);
											$dataArr[$claim_count]['TO_DOS'] = substr($file_line, 81, 10);
											$dataArr[$claim_count]['CPT'] = substr($file_line, 92, 5);
											$dataArr[$claim_count]['DIAG'] = substr($file_line, 100, 7);
											$dataArr[$claim_count]['TAX_ID'] = substr($file_line, 109, 10);
											$dataArr[$claim_count]['ACCNT#'] = substr($file_line, 121, 14);
											$dataArr[$claim_count]['PHYS_ID'] = substr($file_line, 135, 10);
											$dataArr[$claim_count]['PAYER'] = substr($file_line, 146, 5);
											$dataArr[$claim_count]['ERROR'] = substr($file_line, 153, 30);
											

											$claim_details = ClaimInfoV1::where('claim_number', trim($dataArr[$claim_count]['ACCNT#']));
											$claimsInfo = $claim_details->get()->first();
											
											//$insuranceID = $this->findInsuranceIDCategory($dataArr[$claim_count]['OA_Claim_ID']);
											$insuranceID = '';
											if(empty($insuranceID)){
												$claimsTXDetails = ClaimTXDESCV1::where('claim_id',$claimsInfo->id)->where('transaction_type','Submitted')->orderBy('id','desc')->get()->first();
												if(isset($claimsTXDetails->responsibility) && !empty($claimsTXDetails->responsibility)){
													$insuranceID = $claimsTXDetails->responsibility;
												}else{
													$claimsTXDetails = ClaimTXDESCV1::where('claim_id',$claimsInfo->id)->where('transaction_type','Submitted')->orderBy('id','desc')->get()->first();
													$insuranceID = $claimsTXDetails->responsibility;
												}
												$patientInsurance = PatientInsurance::where('insurance_id',$insuranceID)->where('patient_id',$claimsInfo->patient_id)->get()->first();
												$insuranceID = $patientInsurance->category.'-'.$claimsTXDetails->responsibility;	
											}
											
											$paymentV1 = new PaymentV1ApiController();
											$response = $paymentV1->changeClaimRespobilityInClearingHouseUpdation($claimsInfo->patient_id, $claimsInfo->id, $insuranceID);
											
											
											// @todo check and implement new pmt flow    
											if (!empty(trim($dataArr[$claim_count]['ERROR']))) {
												$claim_reject++;

												// Update the claim Status
												$claim_details->update(['status' => 'Rejection']);
												$claim_full_details = $claim_details->get()->toArray();

												// Storing the response file 
												$claimArr['rejected_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file));
												$claimArr['response_file_path'] = $file_name;
												$claimArr['denial_codes'] = trim($dataArr[$claim_count]['ERROR']);
												$claimArr['created_by'] = Auth::user()->id;
												$claimArr['claim_id'] = $claim_full_details[0]['id'];
												ClaimEDIInfoV1::create($claimArr);

												// Storing the claim TRNS DESC
												$paymentclaimArr['claim_info_id'] = $claim_full_details[0]['id'];
												$paymentclaimArr['resp'] = $claim_full_details[0]['insurance_id'];
												
												$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
													->where('claim_id',$claim_full_details[0]['id'])->get()->first();
												$paymentclaimArr['pat_bal'] = $claimFinData['patient_due'];
												$paymentclaimArr['ins_bal'] = $claimFinData['insurance_due'];
												
												$claim_tnx_id = $this->storeClaimTxnDesc('Clearing_House_Rejection', $paymentclaimArr);

												// Storing the claim cpt level TRNS DESC
												$dataArrs['claim_tx_desc_id'] = $claim_tnx_id;
												$dataArrs['resp'] = $claim_full_details[0]['insurance_id'];
												$dataArrs['claim_info_id'] = $paymentclaimArr['claim_info_id'];
												$ClaimCpt_info = ClaimCPTInfoV1::where('claim_id', $paymentclaimArr['claim_info_id'])->where('is_active', 1)->get()->toArray();
												
												foreach ($ClaimCpt_info as $cpt_id) {
													$dataArrs['claim_cpt_info_id'] = $cpt_id['id'];
													$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
														->where('claim_cpt_info_id',$cpt_id['id'])->get()->first();
													$dataArrs['pat_bal'] = $cptFinData['patient_balance'];
													$dataArrs['ins_bal'] = $cptFinData['insurance_balance'];
													$this->storeClaimCptTxnDesc('Clearing_House_Rejection', $dataArrs);
												}
											} else {
												$claim_accpet++;

												// Getting Claims Details
												$claim_full_details = $claim_details->get()->toArray();

												// Storing the response file 
												$claimArr['rejected_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file));
												$claimArr['response_file_path'] = $file_name;
												$claimArr['created_by'] = Auth::user()->id;
												$claimArr['claim_id'] = $claim_full_details[0]['id'];
												ClaimEDIInfoV1::create($claimArr);

												// Storing the claim TRNS DESC
												$paymentclaimArr['claim_info_id'] = $claim_full_details[0]['id'];
												$paymentclaimArr['resp'] = $claim_full_details[0]['insurance_id'];
												$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
													->where('claim_id',$claim_full_details[0]['id'])->get()->first();
												$paymentclaimArr['pat_bal'] = $claimFinData['patient_due'];
												$paymentclaimArr['ins_bal'] = $claimFinData['insurance_due'];
												$claim_tnx_id = $this->storeClaimTxnDesc('Clearing_House_Accepted', $paymentclaimArr);

												// Storing the claim TRNS DESC
												$dataArrs['claim_tx_desc_id'] = $claim_tnx_id;
												$dataArrs['resp'] = $claim_full_details[0]['insurance_id'];
												$dataArrs['claim_info_id'] = $paymentclaimArr['claim_info_id'];
												$ClaimCpt_info = ClaimCPTInfoV1::where('claim_id', $paymentclaimArr['claim_info_id'])->where('is_active', 1)->get()->toArray();
												foreach ($ClaimCpt_info as $cpt_id) {
													$dataArrs['claim_cpt_info_id'] = $cpt_id['id'];
													$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
														->where('claim_cpt_info_id',$cpt_id['id'])->get()->first();
													$dataArrs['pat_bal'] = $cptFinData['patient_balance'];
													$dataArrs['ins_bal'] = $cptFinData['insurance_balance'];
													$this->storeClaimCptTxnDesc('Clearing_House_Accepted', $dataArrs);
												}
											}

											DB::commit();
											$status = 'success';
											$error_code = 'Downloaded successfully';

											$claim_count++;
										}
									}
								} catch (\Exception $e) {
									DB::rollback();
								}
							} else {
								$status = 'success';
								$error_code = "Nothing to update the status! Try again...";
							}
						}
					}elseif($clearingHouseType == 'Navicure'){
						if ($file == "." || $file == "..")
							continue;
						$file_name = basename($file);
						$seprator = ':';
						
						if (!file_exists($local_path . $file_name)) {
							DB::beginTransaction();
							try {
								$file_count++;
								@fopen($local_path . $file, 'w');
								$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file);

								$myerafile = fopen($local_path . $file, "w+");
								fwrite($myerafile, $file_content);
								
								$read_claim_item = 0;
								$claim_count = 1;
								$tempCount = 0;
								$dataArr = array();
					
									$file_content = file($local_path . $file_name);
									$fileSegmentContent = explode('~', $file_content[0]);
									$claimNo = '';
									 foreach ($fileSegmentContent as $key => $segmentList) {
										if(substr($segmentList, 0, 4) == 'ISA*'){ 
											$temp = explode('*', $segmentList);
											$seprator = $temp[16];										 
										} 
										if(substr($segmentList, 0, 6) == 'TRN*2*'){
											$temp = explode('*', $segmentList);
											if(isset($temp[2]) && !empty($temp[2])){
												$claim_details = ClaimInfoV1::where('claim_number', trim($temp[2]))->first();

												if(isset($claim_details) && !empty($claim_details->claim_number)){
														$multiCode = "";
														$claimNo = $dataArr[$claim_details->claim_number]['CLAIM#'] = $claim_details->claim_number;
														$claim_count++;
														$tempCount = 1;
												}else{
													$tempCount = 0;
												}															
											}
										}

										if(substr($segmentList, 0, 4) == 'STC*' && $tempCount == 1){
											$temp = explode('*', $segmentList);
											$tempSegment = explode($seprator, $temp[1]);
											if(!empty($claimNo) && isset($tempSegment[0]) && isset($tempSegment[1])){
												if(isset($temp[12])){
													$multiCode .= "<li><span class='med-orange font600'>".$tempSegment[0].$tempSegment[1]."</span> - ".$temp[12]."</li>";
													$dataArr[$claimNo]['ERROR'] = $multiCode;
												}else{
													$dataArr[$claimNo]['ERROR'] = '';
												}
												if($tempSegment[0] == 'A0' || $tempSegment[0] == 'A1' || $tempSegment[0] == 'A2' || $tempSegment[0] == 'A5' || $tempSegment[0] == 'F0'){
													if($tempSegment[0] == 'A0'){
														$dataArr[$claimNo]['STATUS'] = 'ACCEPTED';
														$dataArr[$claimNo]['TYPE'] = 'Clearing';
													}else{
														$dataArr[$claimNo]['STATUS'] = 'ACCEPTED';
														$dataArr[$claimNo]['TYPE'] = 'Payer';
													}
												}else{
													$dataArr[$claimNo]['STATUS'] = 'REJECTED';
													$dataArr[$claimNo]['TYPE'] = 'Clearing';
												}
											}
										}
									}
									
									
									
									foreach($dataArr as $list){
										$claim_details = ClaimInfoV1::where('claim_number', trim($list['CLAIM#']));
										$claimsInfo = $claim_details->get()->first();
										$insuranceID = '';
										$claimsTXDetails = ClaimTXDESCV1::where('claim_id',$claimsInfo->id)->where('transaction_type','Insurance Payment')->orderBy('id','desc')->get()->first();
										if(isset($claimsTXDetails->responsibility) && !empty($claimsTXDetails->responsibility)){
											$insuranceID = $claimsTXDetails->responsibility;
											$patientInsurance = PatientInsurance::where('insurance_id',$insuranceID)->where('patient_id',$claimsInfo->patient_id)->get()->first();
											$insuranceID = $patientInsurance->category.'-'.$claimsTXDetails->responsibility;	
											
											
											$paymentV1 = new PaymentV1ApiController();
											$response = $paymentV1->changeClaimRespobilityInClearingHouseUpdation($claimsInfo->patient_id, $claimsInfo->id, $insuranceID);
										}
										
										if ($list['STATUS'] == 'REJECTED') {
											$claim_details = ClaimInfoV1::where('claim_number', trim($list['CLAIM#']));
											$claim_full_details = $claim_details->get()->toArray();
											$claimId = $claim_full_details[0]['id'];
											// Check claim submitted count in claim transaction table
											$claimSubmittedCount = ClaimTXDESCV1::whereIn('transaction_type',['Submitted','Submitted Paper'])->where('claim_id',$claimId)->count();
											if($claimSubmittedCount != 0){
												$claim_reject++;

												// Update the claim Status
												$claim_details->update(['status' => 'Rejection']);
												// Handling other practice submitted claims not reflect our medcubics 
												if($claim_full_details[0]['claim_submit_count'] != 0 && $claim_full_details[0]['submited_date'] != '0000-00-00 00:00:00'){
													// Storing the response file 
													$claimArr['rejected_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file));
													$claimArr['response_file_path'] = $file_name;
													$claimArr['denial_codes'] = trim($list['ERROR']);
													$claimArr['created_by'] = Auth::user()->id;
													$claimArr['claim_id'] = $claim_full_details[0]['id'];
													ClaimEDIInfoV1::create($claimArr);
													
													// Storing the claim TRNS DESC
													$paymentclaimArr['claim_info_id'] = $claim_full_details[0]['id'];
													$paymentclaimArr['resp'] = $claim_full_details[0]['insurance_id'];
													
													$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
														->where('claim_id',$claim_full_details[0]['id'])->get()->first();
													$paymentclaimArr['pat_bal'] = $claimFinData['patient_due'];
													$paymentclaimArr['ins_bal'] = $claimFinData['insurance_due'];
													
													$claim_tnx_id = $this->storeClaimTxnDesc('Clearing_House_Rejection', $paymentclaimArr);

													// Storing the claim cpt level TRNS DESC
													$dataArrs['claim_tx_desc_id'] = $claim_tnx_id;
													$dataArrs['resp'] = $claim_full_details[0]['insurance_id'];
													$dataArrs['claim_info_id'] = $paymentclaimArr['claim_info_id'];
													$ClaimCpt_info = ClaimCPTInfoV1::where('claim_id', $paymentclaimArr['claim_info_id'])->where('is_active', 1)->get()->toArray();
													
													foreach ($ClaimCpt_info as $cpt_id) {
														$dataArrs['claim_cpt_info_id'] = $cpt_id['id'];
														$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
															->where('claim_cpt_info_id',$cpt_id['id'])->get()->first();
														$dataArrs['pat_bal'] = $cptFinData['patient_balance'];
														$dataArrs['ins_bal'] = $cptFinData['insurance_balance'];
														$this->storeClaimCptTxnDesc('Clearing_House_Rejection', $dataArrs);
													}
												}
											}	
										} else {
										
											$claim_full_details = $claim_details->get()->toArray();
											$claimId = $claim_full_details[0]['id'];
											// Check claim submitted count in claim transaction table
											$claimSubmittedCount = ClaimTXDESCV1::whereIn('transaction_type',['Submitted','Submitted Paper'])->where('claim_id',$claimId)->count();
											if($claimSubmittedCount != 0){
												$claim_accpet++;
												
												// Update the claim Status
												$claim_details->update(['status' => 'Submitted']);
												
												// Handling other practice submitted claims not reflect our medcubics 
												if($claim_full_details[0]['claim_submit_count'] != 0 && $claim_full_details[0]['submited_date'] != '0000-00-00 00:00:00'){
													// Storing the response file 
													$claimArr['rejected_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file));
													$claimArr['response_file_path'] = $file_name;
													$claimArr['created_by'] = Auth::user()->id;
													$claimArr['claim_id'] = $claim_full_details[0]['id'];
													ClaimEDIInfoV1::create($claimArr);

													// Storing the claim TRNS DESC
													$paymentclaimArr['claim_info_id'] = $claim_full_details[0]['id'];
													$paymentclaimArr['resp'] = $claim_full_details[0]['insurance_id'];
													$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
														->where('claim_id',$claim_full_details[0]['id'])->get()->first();
													$paymentclaimArr['pat_bal'] = $claimFinData['patient_due'];
													$paymentclaimArr['ins_bal'] = $claimFinData['insurance_due'];
													if($list['TYPE'] == 'Clearing')
														$claim_tnx_id = $this->storeClaimTxnDesc('Clearing_House_Accepted', $paymentclaimArr);
													else
														$claim_tnx_id = $this->storeClaimTxnDesc('payer_accepted', $paymentclaimArr);
													// Storing the claim TRNS DESC
													$dataArrs['claim_tx_desc_id'] = $claim_tnx_id;
													$dataArrs['resp'] = $claim_full_details[0]['insurance_id'];
													$dataArrs['claim_info_id'] = $paymentclaimArr['claim_info_id'];
													$ClaimCpt_info = ClaimCPTInfoV1::where('claim_id', $paymentclaimArr['claim_info_id'])->where('is_active', 1)->get()->toArray();
													foreach ($ClaimCpt_info as $cpt_id) {
														$dataArrs['claim_cpt_info_id'] = $cpt_id['id'];
														$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
															->where('claim_cpt_info_id',$cpt_id['id'])->get()->first();
														$dataArrs['pat_bal'] = $cptFinData['patient_balance'];
														$dataArrs['ins_bal'] = $cptFinData['insurance_balance'];
														if($list['TYPE'] == 'Clearing')
															$this->storeClaimCptTxnDesc('Clearing_House_Accepted', $dataArrs);
														else
															$this->storeClaimCptTxnDesc('payer_accepted', $dataArrs);
													}
												}
											}
										}

										DB::commit();
										$status = 'success';
										$error_code = 'Downloaded successfully';
									}
									$navicureClaimCount = count($dataArr);
									$claims_count = $claims_count + $navicureClaimCount;
									
									fclose($myerafile);
									
								/* }else{
									Helpers::EdiFileLogGenerate('Claim Edi Rejection',$file_name,$ftpFileSize,$localFileSize);
									
								} */
							} catch (\Exception $e) {
								DB::rollback();
							}
						} else {
							$status = 'success';
							$error_code = "Nothing to update the status! Try again...";
						}
					}
                }
            } else {
                $status = 'error';
                $error_code = "Kindly setup clearing house and try again...";
            }
        } else {
            $status = 'error';
            $error_code = 'Unable to download files in local environment';
        }
		
		
		
        return Response::json(array('status' => $status, 'message' => $error_code, 'file_count' => $file_count, 'claim_count' => $claims_count, 'claim_accpet' => $claim_accpet, 'claim_reject' => $claim_reject));
    }

    public function clearing_house_edi_response() {
        $file_count = 0;
        $claims_count = 0;
        $claim_accpet = 0;
        $claim_reject = 0;
		$status = $error_code = '';
        if (App::environment() == "local") {
            $path_medcubic = public_path() . '/';
            $local_path = $path_medcubic . 'media/clearingHouse_Edi_response/' . Session::get('practice_dbid') . '/';
            if (!file_exists($local_path)) {
                mkdir($local_path, 0777, true);
            }
            $clearing_house_details = ClearingHouse::where('status', 'Active')->where('practice_id', Session::get('practice_dbid'))->first();

            if (count((array)$clearing_house_details) > 0) {
				$clearingHouseType = $clearing_house_details->name;
                $ftp_server = $clearing_house_details->ftp_address;
                $ftp_username = $clearing_house_details->ftp_user_id;
                $ftp_password = $clearing_house_details->ftp_password;
                $ftp_port = $clearing_house_details->ftp_port;
				
                $destination_file = $clearing_house_details->edi_report_folder;
				if($clearingHouseType == "OfficeAlly"){
					if (!function_exists("ssh2_connect")) {
						$status = 'error';
						$error_code = 'Function ssh2_connect not found, you cannot use ssh2 here';
					} elseif (!$connection = ssh2_connect($ftp_server, $ftp_port)) {
						$status = 'error';
						$error_code = 'Connection cannot be made to clearing house. Please contact administrator';
					} elseif (!ssh2_auth_password($connection, $ftp_username, $ftp_password)) {
						$status = 'error';
						$error_code = 'Connection cannot be made to clearing house. Please contact administrator';
					} elseif (!$stream = ssh2_sftp($connection)) {
						$status = 'error';
						$error_code = 'Connection cannot be made to clearing house. Please contact administrator';
					} elseif (!$dir = opendir("ssh2.sftp://" . intval($stream) . "/{$destination_file}/./")) {
						$status = 'error';
						$error_code = 'ssh2.sftp://' . $stream . $destination_file . 'Could not open the directory';
					}
					$files = array();

					while (false !== ($file = readdir($dir))) {
						$file_name = basename($file);
						if (substr($file, 9, 12) == '_EDI_STATUS_') {
							if ($file == "." || $file == "..")
								continue;
							if (!file_exists($local_path . $file_name)) {
								DB::beginTransaction();
								try {
									$file_count++;
									@fopen($local_path . $file, 'w');
									$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file);

									$myerafile = fopen($local_path . $file, "w+");
									fwrite($myerafile, $file_content);
									$Reached_status = 0;
									$dataArr = array();
									$file_content = file($local_path . $file_name);
									foreach ($file_content as $file_line) {
										if (substr($file_line, 0, 11) == '    File ID') {
											$Reached_status = 1;
										}
										if ($Reached_status == 1 && is_numeric(substr($file_line, 4, 9))) {
											$claims_count++;
											
											$dataArr[trim(substr($file_line, 26, 14))]['file_id'] = substr($file_line, 4, 9);
											$dataArr[trim(substr($file_line, 26, 14))]['claim_id'] = substr($file_line, 14, 10);
											$dataArr[trim(substr($file_line, 26, 14))]['acct'] = substr($file_line, 26, 14);
											$dataArr[trim(substr($file_line, 26, 14))]['patient_name'] = substr($file_line, 41, 20);
											$dataArr[trim(substr($file_line, 26, 14))]['amount'] = substr($file_line, 61, 9);
											$dataArr[trim(substr($file_line, 26, 14))]['practice_id'] = substr($file_line, 73, 10);
											$dataArr[trim(substr($file_line, 26, 14))]['tax_id'] = substr($file_line, 84, 10);
											$dataArr[trim(substr($file_line, 26, 14))]['payer'] = substr($file_line, 95, 5);
											$dataArr[trim(substr($file_line, 26, 14))]['payer_process_dt'] = substr($file_line, 105, 10);
											$dataArr[trim(substr($file_line, 26, 14))]['payer_ref_id'] = substr($file_line, 122, 15);
											$dataArr[trim(substr($file_line, 26, 14))]['status'] = substr($file_line, 142, 8);
											$dataArr[trim(substr($file_line, 26, 14))]['payer_response'] = substr($file_line, 152, 255);
										}
										// @todo - check and implement new pmt flow    
										if ($Reached_status == 1 && is_numeric(trim(substr($file_line, 26, 14)))) {
											$claim_number = trim(substr($file_line, 26, 14));
											
											$claim_details = ClaimInfoV1::where('claim_number', trim($claim_number));
											$claimsInfo = $claim_details->get()->first();
											
											$claimsTXDetails = ClaimTXDESCV1::where('claim_id',$claimsInfo->id)->where('transaction_type','Submitted')->orderby('id','desc')->get()->first();
											$insuranceID = $claimsTXDetails->responsibility;
											$patientInsurance = PatientInsurance::where('insurance_id',$insuranceID)->where('patient_id',$claimsInfo->patient_id)->get()->first();
											$insuranceID = $patientInsurance->category.'-'.$claimsTXDetails->responsibility;	
											
											
											$paymentV1 = new PaymentV1ApiController();
											$response = $paymentV1->changeClaimRespobilityInClearingHouseUpdation($claimsInfo->patient_id, $claimsInfo->id, $insuranceID);
											
											
											if ($dataArr[$claim_number]['status'] == 'REJECTED') {
												$claim_reject++;
												
												// Update Claim Status
												$claim_details = ClaimInfoV1::where('claim_number', $claim_number);
												$claim_details->update(['status' => 'Rejection']);
												$claim_full_details = $claim_details->get()->toArray();


												// Storing the response file 
												$claimArr['rejected_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file));
												$claimArr['response_file_path'] = $file_name;
												$claimArr['created_by'] = Auth::user()->id;
												$claimArr['claim_id'] = $claim_full_details[0]['id'];
												ClaimEDIInfoV1::create($claimArr);


												// Claim Level TRNS DESC
												$paymentclaimArr['claim_info_id'] = $claim_full_details[0]['id'];
												$paymentclaimArr['resp'] = $claim_full_details[0]['insurance_id'];
												$paymentclaimArr['value1'] = @$dataArr[$claim_number]['payer_response'];
												$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
													->where('claim_id',$claim_full_details[0]['id'])->get()->first();
												$paymentclaimArr['pat_bal'] = $claimFinData['patient_due'];
												$paymentclaimArr['ins_bal'] = $claimFinData['insurance_due'];
												$claim_tnx_id = $this->storeClaimTxnDesc('payer_rejected', $paymentclaimArr);

												// Claim cpt Level TRNS DESC
												$dataArrs['claim_tx_desc_id'] = $claim_tnx_id;
												$dataArrs['resp'] = $claim_full_details[0]['insurance_id'];
												$dataArrs['claim_info_id'] = $paymentclaimArr['claim_info_id'];
												$ClaimCpt_info = ClaimCPTInfoV1::where('claim_id', $paymentclaimArr['claim_info_id'])->where('is_active', 1)->get()->toArray();
												foreach ($ClaimCpt_info as $cpt_id) {
													$dataArrs['claim_cpt_info_id'] = $cpt_id['id'];
													$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
														->where('claim_cpt_info_id',$cpt_id['id'])->get()->first();
													$dataArrs['pat_bal'] = $cptFinData['patient_balance'];
													$dataArrs['ins_bal'] = $cptFinData['insurance_balance'];
													$this->storeClaimCptTxnDesc('payer_rejected', $dataArrs);
												}
											} elseif ($dataArr[$claim_number]['status'] == 'ACCEPTED') {
												$claim_accpet++;
												
												// Getting claim Details
												$claim_details = ClaimInfoV1::where('claim_number', $claim_number);
												$claim_full_details = $claim_details->get()->toArray();

												// Claim Level Trns Desc
												$paymentclaimArr['claim_info_id'] = $claim_full_details[0]['id'];
												$paymentclaimArr['resp'] = $claim_full_details[0]['insurance_id'];
												$paymentclaimArr['value1'] = @$dataArr[$claim_number]['payer_response'];
												$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
													->where('claim_id',$claim_full_details[0]['id'])->get()->first();
												$paymentclaimArr['pat_bal'] = $claimFinData['patient_due'];
												$paymentclaimArr['ins_bal'] = $claimFinData['insurance_due'];
												$claim_tnx_id = $this->storeClaimTxnDesc('payer_accepted', $paymentclaimArr);

												// Claim Cpt Level Trans Desc
												$dataArrs['claim_tx_desc_id'] = $claim_tnx_id;
												$dataArrs['resp'] = $claim_full_details[0]['insurance_id'];
												;
												$dataArrs['claim_info_id'] = $paymentclaimArr['claim_info_id'];
												$ClaimCpt_info = ClaimCPTInfoV1::where('claim_id', $paymentclaimArr['claim_info_id'])->where('is_active', 1)->get()->toArray();
												foreach ($ClaimCpt_info as $cpt_id) {
													$dataArrs['claim_cpt_info_id'] = $cpt_id['id'];
													$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
														->where('claim_cpt_info_id',$cpt_id['id'])->get()->first();
													$dataArrs['pat_bal'] = $cptFinData['patient_balance'];
													$dataArrs['ins_bal'] = $cptFinData['insurance_balance'];
													$this->storeClaimCptTxnDesc('payer_accepted', $dataArrs);
												}
											}
										}
										DB::commit();
										$status = 'success';
										$error_code = 'Downloaded successfully';
									}
									\Log::info($dataArr);
								} catch (\Exception $e) {
									DB::rollback();
								}
							} else {
								$status = 'error';
								$error_code = "Nothing to update the status! Try again...";
							}
						}
					}
				}
            } else {
                $status = 'error';
                $error_code = "Kindly setup clearing house and try again...";
            }
        } else {
            $status = 'error';
            $error_code = 'Unable to download files in local environment';
        }
        return Response::json(array('status' => $status, 'message' => $error_code, 'file_count' => $file_count, 'claim_count' => $claims_count, 'claim_accpet' => $claim_accpet, 'claim_reject' => $claim_reject));
    }

    public function download_response_file($file_name) {
        header('Content-Type: application/octet-stream');
        
        $path_medcubic = public_path() . '/';
		if (substr($file_name, 9, 12) == '_EDI_STATUS_') {
			$local_path = $path_medcubic . 'media/clearingHouse_Edi_response/' . Session::get('practice_dbid') . '/';
		}else{
			$local_path = $path_medcubic . 'media/clearingHouse_response/' . Session::get('practice_dbid') . '/';
		}
        header('Content-Disposition: attachment; filename=' . basename($local_path . $file_name));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($local_path . $file_name));
        readfile($local_path . $file_name);
        exit;
    }

    public function get_practice_session_id() {
		$data['role_id'] = Auth::user()->role_id;
        $data['id'] = Session::get('practice_dbid');
        return $data;
    }

    public function getInsurancebalance($claim_id = '') {
        $claim_id = $claim_id;
        //$claim_insurance_due_value = PaymentClaimDetail::where('claim_id', $claim_id)->orderBy('id', 'DESC')->pluck('insurance_due');
        $claim_insurance_due_value = $this->getInsurancebalance($claim_id);
        return $claim_insurance_due_value;
    }
	
	
	public function file_response(){
		$path_medcubic = public_path() . '/';
        $local_path = $path_medcubic . 'media/clearing_house/' . Session::get('practice_dbid') . '/';
		$read_claim_item = 0;
		$claims_count = 0;
		$claim_count = 1;
		$dataArr = array();
		$file_name = 'FS_HCFA_848303600_IN_C.txt';
		$file_content = file($local_path . $file_name);
		foreach ($file_content as $key => $file_line) {
			if (substr($file_line, 0, 6) == 'CLAIM#') {
				$read_claim_item = 1; echo substr($file_line, 0, 2).$claim_count;
			}
			if ($read_claim_item == 1 && (substr($file_line, 0, 2) == $claim_count . ')' || substr($file_line, 0, 3) == $claim_count . ')' || substr($file_line, 0, 4) == $claim_count . ')')) {
				$claims_count ++;
				$dataArr[$claim_count]['CLAIM#'] = substr($file_line, 0, 6);
				$dataArr[$claim_count]['OA_Claim_ID'] = substr($file_line, 7, 10);
				$dataArr[$claim_count]['PATIENT_ID'] = substr($file_line, 19, 18);
				$dataArr[$claim_count]['LAST_FIRST'] = substr($file_line, 37, 20);
				$dataArr[$claim_count]['DOB'] = substr($file_line, 57, 10);
				$dataArr[$claim_count]['FROM_DOS'] = substr($file_line, 70, 10);
				$dataArr[$claim_count]['TO_DOS'] = substr($file_line, 81, 10);
				$dataArr[$claim_count]['CPT'] = substr($file_line, 92, 5);
				$dataArr[$claim_count]['DIAG'] = substr($file_line, 100, 7);
				$dataArr[$claim_count]['TAX_ID'] = substr($file_line, 109, 10);
				$dataArr[$claim_count]['ACCNT#'] = substr($file_line, 121, 14);
				$dataArr[$claim_count]['PHYS_ID'] = substr($file_line, 135, 10);
				$dataArr[$claim_count]['PAYER'] = substr($file_line, 146, 5);
				$dataArr[$claim_count]['ERROR'] = substr($file_line, 153, 30);
				
				
				// @todo check and implement new pmt flow    
				/* if (!empty(trim($dataArr[$claim_count]['ERROR']))) {
					$claim_reject++;

					// Update the claim Status
					$claim_details = ClaimInfoV1::where('claim_number', trim($dataArr[$claim_count]['ACCNT#']));
					$claim_details->update(['status' => 'Rejection']);
					$claim_full_details = $claim_details->get()->toArray();

					// Storing the response file 
					$claimArr['rejected_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file));
					$claimArr['response_file_path'] = $file_name;
					$claimArr['denial_codes'] = trim($dataArr[$claim_count]['ERROR']);
					$claimArr['created_by'] = Auth::user()->id;
					$claimArr['claim_id'] = $claim_full_details[0]['id'];
					ClaimEDIInfoV1::create($claimArr);

					// Storing the claim TRNS DESC
					$paymentclaimArr['claim_info_id'] = $claim_full_details[0]['id'];
					$paymentclaimArr['resp'] = $claim_full_details[0]['insurance_id'];
					
					$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
						->where('claim_id',$claim_full_details[0]['id'])->get()->first();
					$paymentclaimArr['pat_bal'] = $claimFinData['patient_due'];
					$paymentclaimArr['ins_bal'] = $claimFinData['insurance_due'];
					
					$claim_tnx_id = $this->storeClaimTxnDesc('Clearing_House_Rejection', $paymentclaimArr);

					// Storing the claim cpt level TRNS DESC
					$dataArrs['claim_tx_desc_id'] = $claim_tnx_id;
					$dataArrs['resp'] = $claim_full_details[0]['insurance_id'];
					$dataArrs['claim_info_id'] = $paymentclaimArr['claim_info_id'];
					$ClaimCpt_info = ClaimCPTInfoV1::where('claim_id', $paymentclaimArr['claim_info_id'])->where('is_active', 1)->get()->toArray();
					
					foreach ($ClaimCpt_info as $cpt_id) {
						$dataArrs['claim_cpt_info_id'] = $cpt_id['id'];
						$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
							->where('claim_cpt_info_id',$cpt_id['id'])->get()->first();
						$dataArrs['pat_bal'] = $cptFinData['patient_balance'];
						$dataArrs['ins_bal'] = $cptFinData['insurance_balance'];
						$this->storeClaimCptTxnDesc('Clearing_House_Rejection', $dataArrs);
					}
				} else {
					$claim_accpet++;

					$claim_details = ClaimInfoV1::where('claim_number', trim($dataArr[$claim_count]['ACCNT#']));
					$claim_full_details = $claim_details->get()->toArray();

					// Storing the response file 
					$claimArr['rejected_date'] = date("Y-m-d", filemtime("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file));
					$claimArr['response_file_path'] = $file_name;
					$claimArr['created_by'] = Auth::user()->id;
					$claimArr['claim_id'] = $claim_full_details[0]['id'];
					ClaimEDIInfoV1::create($claimArr);

					// Storing the claim TRNS DESC
					$paymentclaimArr['claim_info_id'] = $claim_full_details[0]['id'];
					$paymentclaimArr['resp'] = $claim_full_details[0]['insurance_id'];
					$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
						->where('claim_id',$claim_full_details[0]['id'])->get()->first();
					$paymentclaimArr['pat_bal'] = $claimFinData['patient_due'];
					$paymentclaimArr['ins_bal'] = $claimFinData['insurance_due'];
					$claim_tnx_id = $this->storeClaimTxnDesc('Clearing_House_Accepted', $paymentclaimArr);

					// Storing the claim TRNS DESC
					$dataArrs['claim_tx_desc_id'] = $claim_tnx_id;
					$dataArrs['resp'] = $claim_full_details[0]['insurance_id'];
					$dataArrs['claim_info_id'] = $paymentclaimArr['claim_info_id'];
					$ClaimCpt_info = ClaimCPTInfoV1::where('claim_id', $paymentclaimArr['claim_info_id'])->where('is_active', 1)->get()->toArray();
					foreach ($ClaimCpt_info as $cpt_id) {
						$dataArrs['claim_cpt_info_id'] = $cpt_id['id'];
						$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
							->where('claim_cpt_info_id',$cpt_id['id'])->get()->first();
						$dataArrs['pat_bal'] = $cptFinData['patient_balance'];
						$dataArrs['ins_bal'] = $cptFinData['insurance_balance'];
						$this->storeClaimCptTxnDesc('Clearing_House_Accepted', $dataArrs);
					}
				} */

				DB::commit();
				$status = 'success';
				$error_code = 'Downloaded successfully';

				$claim_count++;
				
			}
			
		}echo "<pre>";print_r($dataArr);
	}
	
	public function findInsuranceID($patientPolicyId){
		$patientInsurance = PatientInsurance::where('policy_id',$patientPolicyId)->get()->first();
		$patientInsId = (isset($patientInsurance->insurance_id)) ? $patientInsurance->insurance_id : '';
		return $patientInsId;
	}
	
	public function findInsuranceIDCategory($patientPolicyId){
		$patientInsurance = PatientInsurance::where('policy_id',$patientPolicyId)->get()->first();
		$patientInsId = (isset($patientInsurance->insurance_id)) ? $patientInsurance->category.'-'.$patientInsurance->insurance_id : '';
		return $patientInsId;
	}
	
	public function updateArchiveStatus(){
		$request = Request::all();
		$type = $request['type'];
		foreach($request['erasId'] as $key => $list){
			Eras::where('id', $list)->update(['archive_status'=>$type]);
		}
	}
	
	
	public function ClearingRespChange($claimNo){
		$claim_details = ClaimInfoV1::where('claim_number', trim($claimNo));
		$claimsInfo = $claim_details->get()->first();
		
		$insuranceID = '';
		if(empty($insuranceID)){
			$claimsTXDetails = ClaimTXDESCV1::where('claim_id',$claimsInfo->id)->where('transaction_type','Insurance Payment')->orderBy('id','desc')->get()->first();
			$insuranceID = $claimsTXDetails->responsibility;
			$patientInsurance = PatientInsurance::where('insurance_id',$insuranceID)->where('patient_id',$claimsInfo->patient_id)->get()->first();
			$insuranceID = $patientInsurance->category.'-'.$claimsTXDetails->responsibility;	
		}
		
		$paymentV1 = new PaymentV1ApiController();
		$response = $paymentV1->changeClaimRespobilityInClearingHouseUpdation($claimsInfo->patient_id, $claimsInfo->id, $insuranceID);
	}
	
	
	public function getEdiFileContent(){
		$path_medcubic = public_path() . '/';
        $local_path = $path_medcubic . 'media/clearing_house/' . Session::get('practice_dbid') . '/';
		$read_claim_item = 0;
		$claims_count = $claim_reject = $claim_accpet = 0;
		$claim_count = 1;
		$Reached_status = 0;
		$dataArr = array();
		$file_name = '882990025_EDI_STATUS_20211019.txt';
		$file_content = file($local_path . $file_name);
		foreach ($file_content as $file_line) {
			if (substr($file_line, 0, 11) == '    File ID') {
				$Reached_status = 1;
			}
			if ($Reached_status == 1 && is_numeric(substr($file_line, 4, 9))) {
				$claims_count++;
				
				$dataArr[trim(substr($file_line, 26, 14))]['file_id'] = substr($file_line, 4, 9);
				$dataArr[trim(substr($file_line, 26, 14))]['claim_id'] = substr($file_line, 14, 10);
				$dataArr[trim(substr($file_line, 26, 14))]['acct'] = substr($file_line, 26, 14);
				$dataArr[trim(substr($file_line, 26, 14))]['patient_name'] = substr($file_line, 41, 20);
				$dataArr[trim(substr($file_line, 26, 14))]['amount'] = substr($file_line, 61, 9);
				$dataArr[trim(substr($file_line, 26, 14))]['practice_id'] = substr($file_line, 73, 10);
				$dataArr[trim(substr($file_line, 26, 14))]['tax_id'] = substr($file_line, 84, 10);
				$dataArr[trim(substr($file_line, 26, 14))]['payer'] = substr($file_line, 95, 5);
				$dataArr[trim(substr($file_line, 26, 14))]['payer_process_dt'] = substr($file_line, 105, 10);
				$dataArr[trim(substr($file_line, 26, 14))]['payer_ref_id'] = substr($file_line, 122, 15);
				$dataArr[trim(substr($file_line, 26, 14))]['status'] = substr($file_line, 142, 8);
				$dataArr[trim(substr($file_line, 26, 14))]['payer_response'] = substr($file_line, 152, 255);
			}
			// @todo - check and implement new pmt flow    
			if ($Reached_status == 1 && is_numeric(trim(substr($file_line, 26, 14)))) {
				$claim_number = trim(substr($file_line, 26, 14));
				
				$claim_details = ClaimInfoV1::where('claim_number', trim($claim_number));
				$claimsInfo = $claim_details->get()->first();
				
				$claimsTXDetails = ClaimTXDESCV1::where('claim_id',$claimsInfo->id)->where('transaction_type','Submitted')->orderBy('id','desc')->get()->first();
				$insuranceID = $claimsTXDetails->responsibility;
				$patientInsurance = PatientInsurance::where('insurance_id',$insuranceID)->where('patient_id',$claimsInfo->patient_id)->get()->first();
				$insuranceID = $patientInsurance->category.'-'.$claimsTXDetails->responsibility;	
				
				
				$paymentV1 = new PaymentV1ApiController();
				$response = $paymentV1->changeClaimRespobilityInClearingHouseUpdation($claimsInfo->patient_id, $claimsInfo->id, $insuranceID);
				
				
				if ($dataArr[$claim_number]['status'] == 'REJECTED') {
					$claim_reject++;
					
					// Update Claim Status
					$claim_details = ClaimInfoV1::where('claim_number', $claim_number);
					$claim_details->update(['status' => 'Rejection']);
					$claim_full_details = $claim_details->get()->toArray();


					// Storing the response file 
					$claimArr['rejected_date'] = date("Y-m-d");//, filemtime("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $file));
					$claimArr['response_file_path'] = $file_name;
					$claimArr['created_by'] = Auth::user()->id;
					$claimArr['claim_id'] = $claim_full_details[0]['id'];
					ClaimEDIInfoV1::create($claimArr);


					// Claim Level TRNS DESC
					$paymentclaimArr['claim_info_id'] = $claim_full_details[0]['id'];
					$paymentclaimArr['resp'] = $claim_full_details[0]['insurance_id'];
					$paymentclaimArr['value1'] = @$dataArr[$claim_number]['payer_response'];
					$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
						->where('claim_id',$claim_full_details[0]['id'])->get()->first();
					$paymentclaimArr['pat_bal'] = $claimFinData['patient_due'];
					$paymentclaimArr['ins_bal'] = $claimFinData['insurance_due'];
					$claim_tnx_id = $this->storeClaimTxnDesc('payer_rejected', $paymentclaimArr);

					// Claim cpt Level TRNS DESC
					$dataArrs['claim_tx_desc_id'] = $claim_tnx_id;
					$dataArrs['resp'] = $claim_full_details[0]['insurance_id'];
					$dataArrs['claim_info_id'] = $paymentclaimArr['claim_info_id'];
					$ClaimCpt_info = ClaimCPTInfoV1::where('claim_id', $paymentclaimArr['claim_info_id'])->where('is_active', 1)->get()->toArray();
					foreach ($ClaimCpt_info as $cpt_id) {
						$dataArrs['claim_cpt_info_id'] = $cpt_id['id'];
						$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
							->where('claim_cpt_info_id',$cpt_id['id'])->get()->first();
						$dataArrs['pat_bal'] = $cptFinData['patient_balance'];
						$dataArrs['ins_bal'] = $cptFinData['insurance_balance'];
						$this->storeClaimCptTxnDesc('payer_rejected', $dataArrs);
					}
				} elseif ($dataArr[$claim_number]['status'] == 'ACCEPTED') {
					$claim_accpet++;
					
					
					
					// Getting claim Details
					$claim_details = ClaimInfoV1::where('claim_number', $claim_number);
					$claim_full_details = $claim_details->get()->toArray();

					// Claim Level Trns Desc
					$paymentclaimArr['claim_info_id'] = $claim_full_details[0]['id'];
					$paymentclaimArr['resp'] = $claim_full_details[0]['insurance_id'];
					$paymentclaimArr['value1'] = @$dataArr[$claim_number]['payer_response'];
					$claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
						->where('claim_id',$claim_full_details[0]['id'])->get()->first();
					$paymentclaimArr['pat_bal'] = $claimFinData['patient_due'];
					$paymentclaimArr['ins_bal'] = $claimFinData['insurance_due'];
					$claim_tnx_id = $this->storeClaimTxnDesc('payer_accepted', $paymentclaimArr);

					// Claim Cpt Level Trans Desc
					$dataArrs['claim_tx_desc_id'] = $claim_tnx_id;
					$dataArrs['resp'] = $claim_full_details[0]['insurance_id'];
					;
					$dataArrs['claim_info_id'] = $paymentclaimArr['claim_info_id'];
					$ClaimCpt_info = ClaimCPTInfoV1::where('claim_id', $paymentclaimArr['claim_info_id'])->where('is_active', 1)->get()->toArray();
					foreach ($ClaimCpt_info as $cpt_id) {
						$dataArrs['claim_cpt_info_id'] = $cpt_id['id'];
						$cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
							->where('claim_cpt_info_id',$cpt_id['id'])->get()->first();
						$dataArrs['pat_bal'] = $cptFinData['patient_balance'];
						$dataArrs['ins_bal'] = $cptFinData['insurance_balance'];
						$this->storeClaimCptTxnDesc('payer_accepted', $dataArrs);
					}
				}
			}
		}
		
		echo "<pre>";print_r($dataArr);die;
	}

}