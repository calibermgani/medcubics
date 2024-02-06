<?php

namespace App\Http\Controllers\Claims\Api;

use App\Http\Controllers\Controller;
use App\Models\Patients\Patient as Patient;
use App\Models\Insurance as Insurance;
use App\Models\Facility as Facility;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Http\Controllers\Api\EdiApiController as EdiApiController;
use App\Models\Icd as Icd;
use App\Models\Medcubics\ClearingHouse as ClearingHouse;
use App\Models\Claims\EdiTransmission as EdiTransmission;
use App\Models\Claims\TransmissionClaimDetails as TransmissionClaimDetails;
use App\Models\Claims\TransmissionCptDetails as TransmissionCptDetails;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Charges\Api\ChargeApiController as ChargeApiController;
use App\Http\Controllers\Charges\ChargeController as ChargeController;
use App\Models\Claims\EdiReport as EdiReport;
use App\Models\Provider as Provider;
use App\Models\Holdoption as Holdoption;
use App\Models\Payments\ClaimInfoV1;
use App\Http\Helpers\Helpers as Helpers;
use Input;
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

class ClaimApiController extends EdiApiController {

    public $error_msg = [];
    public $clearing_house = [];
    public $s = 1;

    public function getIndexApi($type = '', $export = '') {
        if ($export != '' || Request::ajax())
            $request = Request::all();
        else
            $request = [];

        $request_data = Request::all();
        $request_data['is_export'] = ($export != "") ? 1 : 0;
        $result = $this->getClaimSearchApi($type, $request_data);
        $claims = $result["claim_list"];
        $count = $result["count"];

        // For handle export
        if ($export != "") {
            if ($type != 'rejected') {
                $exportparam = array(
                    'filename' => 'Claim Report',
                    'heading' => 'Ready Claims List',
                    'fields' => array(
                        'date_of_service' => 'DOS',
                        'claim_number' => 'Claim No',
                        'Patient Name' => array('table' => 'patient', 'column' => ['last_name', 'first_name'], 'label' => 'Patient Name'),
                        'Billed To' => array('table' => '', 'column' => 'insurance_id', 'use_function' => ['App\Models\Insurance', 'InsuranceName'], 'label' => 'Billed To'),
                        'Payer ID' => array('table' => 'insurance_details', 'column' => 'payerid', 'label' => 'Payer ID'),
                        'Rendering' => array('table' => 'rendering_provider', 'column' => 'short_name', 'label' => 'Rendering'),
                        'Billing' => array('table' => 'billing_provider', 'column' => 'short_name', 'label' => 'Billing'),
                        'Facility' => array('table' => 'facility_detail', 'column' => 'short_name', 'label' => 'Facility'),
                        'Unbilled' => array('table' => '', 'column' => 'total_charge', 'use_function' => ['App\Http\Helpers\Helpers', 'priceFormat'], 'label' => 'Unbilled'),
                        'status' => 'Status'
                ));
                $callexport = new CommonExportApiController();
                return $callexport->generatemultipleExports($exportparam, $claims, $export);
            } elseif ($type == 'rejected') {
                $exportparam = array(
                    'filename' => 'Claim Rejection Report',
                    'heading' => 'Rejection Claims List',
                    'fields' => array(
                        'claim_number' => 'Claim No',
                        'date_of_service' => 'DOS',
                        'acc_no' => array('table' => 'patient', 'column' => ['account_no'], 'label' => 'ACC No'),
                        'Patient Name' => array('table' => 'patient', 'column' => ['last_name', 'first_name'], 'label' => 'Patient Name'),
                        'total_charge' => 'Billed Amt',
                        'payer' => array('table' => 'insurance_details', 'column' => 'insurance_name', 'label' => 'Payer'),
                        'Payer ID' => array('table' => 'insurance_details', 'column' => 'payerid', 'label' => 'Payer ID'),
                        'submited_date' => 'Submitted Date',
                        'rejected_date' => 'Rejected Date'
                ));
                $callexport = new CommonExportApiController();
                return $callexport->generatemultipleExports($exportparam, $claims, $export);
            }
        }
        if (@$request['page_no'] != '')
            $page_no = $request['page_no'];
        else
            $page_no = 1;

        if (@$request['sort_option'] != '' && @$request['sort_value'] != '') {
            $sort_option = $request['sort_option'];
            $sort_value = $request['sort_value'];
        } else {
            $sort_option = '';
            $sort_value = @$request['sort_value'];
        }
        /*
          if(Request::ajax())
          {
          return Response::json(array('status' => 'success', 'message' => null, 'data' =>compact('claims','page_no','sort_option','sort_value')));
          }
          else
          {
         */
        $patients = Patient::where('status', 'Active')->selectRaw('CONCAT(last_name,", ",first_name, " ",middle_name) as patient_name, id')->orderBy('last_name', 'ASC')->pluck('patient_name', 'id')->all();
        $billing_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
        $rendering_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')]);
        $referring_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Referring')]);
        $insurances = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
        $facility = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims', 'patients', 'billing_provider', 'rendering_provider', 'referring_provider', 'insurances', 'facility', 'page_no', 'sort_option', 'sort_value', 'count')));
        //}
    }

    public function getClaimSearchApi($type, $request) {
        $start = isset($request['start']) ? $request['start'] : 0;
        $len = (isset($request['length'])) ? $request['length'] : 50;
        $search = (!empty($request['search']['value'])) ? trim($request['search']['value']) : "";
        $orderByField = 'claim_info_v1.updated_at';
        $orderByDir = 'DESC';

        if (!empty($request['order'])) {
            $orderByField = ($request['order'][0]['column']) ? $request['order'][0]['column'] : $orderByField;

            switch ($orderByField) {
                case '1':
                    $orderByField = 'date_of_service';
                    break;

                case '2':
                    $orderByField = 'claim_number';
                    break;

                case '3':
                    $orderByField = 'patients.last_name';                   //'patient_name';
                    break;

                case '4':
                    $orderByField = 'insurances.short_name';                // Billed to
                    break;

                case '5':
                    $orderByField = 'insurances.payerid';                   // Payer ID
                    break;

                case '6':
                    $orderByField = 'rendering_provider';                   // Rendering
                    break;

                case '7':
                    $orderByField = 'billing_provider';                     // Billing
                    break;

                case '8':
                    $orderByField = 'facilities.short_name';                // Facililty
                    break;

                case '9':
                    $orderByField = 'tot_insurance_due';                    //Billed
                    break;

                case '10':
                    $orderByField = 'claim_info_v1.status';                        // Status
                    break;

                default:
                    $orderByField = 'claim_info_v1.updated_at';
                    break;
            }

            $orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'DESC';
        }
		// Added condition for deleted at
        $claim_qry = ClaimInfoV1::where('claim_info_v1.id', '<>', 0)->whereNull('deleted_at');

        $claim_qry->join('patients', function($join) {
            $join->on('patients.id', '=', 'claim_info_v1.patient_id');
        });

        $claim_qry->leftjoin('insurances', function($join) {
            $join->on('insurances.id', '=', 'claim_info_v1.insurance_id');
        });

        $claim_qry->leftjoin('facilities', function($join) {
            $join->on('facilities.id', '=', 'claim_info_v1.facility_id');
        });

        $claim_qry->leftjoin('claim_cpt_info_v1', function($join) {
            $join->on('claim_cpt_info_v1.claim_id', '=', 'claim_info_v1.id');
        });

        if ($type == 'submitted')
            $claim_qry->where('claim_info_v1.status', 'Submitted');
        elseif ($type == 'hold')
            $claim_qry->where('claim_info_v1.status', 'Hold');
        elseif ($type == 'pending')
            $claim_qry->where('claim_info_v1.status', 'Pending');
        elseif ($type == 'rejected')
            $claim_qry->where('claim_info_v1.status', 'Rejection');
        else
            $claim_qry->whereIn('claim_info_v1.status', ['Ready']);

        if (@$request['patient_id'] != '')
            $claim_qry->where('claim_info_v1.patient_id', $request['patient_id']);

        if (@$request['rendering_provider_id'] != '')
            $claim_qry->where('claim_info_v1.billing_provider_id', $request['rendering_provider_id']);

        if (@$request['billing_provider_id'] != '')
            $claim_qry->where('claim_info_v1.billing_provider_id', $request['billing_provider_id']);

        if (@$request['referring_provider_id'] != '')
            $claim_qry->where('claim_info_v1.refering_provider_id', $request['referring_provider_id']);

        if (@$request['insurance_id'] != '')
            $claim_qry->where('claim_info_v1.insurance_id', $request['insurance_id']);

        if (@$request['facility_id'] != '')
            $claim_qry->where('claim_info_v1.facility_id', $request['facility_id']);

        if (@$request['claim_type'] != '')
            $claim_qry->where('claim_info_v1.claim_type', $request['claim_type']);

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
            $billed_amount = $request['billed'];

            $claim_qry->whereHas('dosdetails', function ($q) use ($billed_amount, $billed_option) {
                $q->select(DB::raw("SUM(charge) as total_charge"))
                        ->groupBy('claim_id')
                        ->having('total_charge', $billed_option, $billed_amount)
                        ->where('is_active', 1);
            });
        }

        if (@$request['dos_from'] != '' && @$request['dos_to'] != '') {
            $from = date("Y-m-d", strtotime($request['dos_from']));
            $to = date("Y-m-d", strtotime($request['dos_to']));
            $claim_qry->whereBetween('claim_info_v1.date_of_service', [$from, $to]);
        }

        /* Ajax search start */
        if (!empty($search)) {
            $claim_qry->Where(function ($claim_qry) use ($search, $type) {
                $claim_qry->Where(function ($query) use ($search, $type) {
                    // claim number search
                    $searchValues = array_filter(explode(",", $search));
                    foreach ($searchValues as $searchKey) {
                        $query = $query->orWhere('claim_number', 'LIKE', '%' . $searchKey . '%');
                        $query = $query->orWhere('claim_info_v1.status', 'LIKE', '%' . $searchKey . '%');
                        // dos search
                        if (strpos(strtolower($searchKey), "/") !== false) {
                            $dateSearch = date("Y-m-d", strtotime(@$searchKey));
                            $query = $query->orWhere('date_of_service', 'LIKE', '%' . $dateSearch . '%');
                            // Submitted date, rejecteddate
                            if ($type == 'rejected') {
                                $query = $query->orWhere('submited_date', 'LIKE', '%' . $dateSearch . '%');
								//@todo check with new paymnet flow
                                //$query = $query->orWhere('rejected_date', 'LIKE', '%' . $dateSearch . '%');
                            }
                        } else {
                            $query = $query->orWhere('date_of_service', 'LIKE', '%' . $searchKey . '%');
                            $query = $query->orWhere('submited_date', 'LIKE', '%' . $searchKey . '%');
							//@todo check with new paymnet flow
                            //$query = $query->orWhere('rejected_date', 'LIKE', '%' . $searchKey . '%');
                        }
                    }
                });

                // Patient name search                
                $claim_qry->orWhere(function ($query) use ($search) {
                    $searchValues = array_filter(explode(",", $search));
                    $sub_sql = '';
                    foreach ($searchValues as $searchKey) {
                        $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                        $sub_sql .= "patients.last_name LIKE '%$searchKey%' OR patients.first_name LIKE '%$searchKey%' OR patients.middle_name LIKE '%$searchKey%'";
                    }
                    if ($sub_sql != '')
                        $query->whereRaw($sub_sql);
                });

                // Billed to and Payer ID
                $claim_qry->orWhere(function ($query) use ($search) {
                    $searchValues = array_filter(explode(",", $search));
                    $sub_sql = '';
                    foreach ($searchValues as $searchKey) {
                        $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                        if (is_numeric($search)) {
                            $sub_sql .= "insurances.payerid LIKE '%$searchKey%' ";
                        } else {
                            $sub_sql .= "insurances.short_name LIKE '%$searchKey%' ";
                        }
                    }
                    if ($sub_sql != '')
                        $query->whereRaw($sub_sql);
                });

                // Type: Rendering - 1, Billing - 5
                $claim_qry->orWhere(function ($query) use ($search) {

                    $searchValues = array_filter(explode(",", $search));
                    $sub_sql = '';
                    foreach ($searchValues as $searchKey) {
                        $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                        if (!is_numeric($search)) {
                            $sub_sql .= "providers.short_name LIKE '%$searchKey%' AND ( providers.provider_types_id = 1 OR providers.provider_types_id = 5) ";
                        }
                    }
                    if ($sub_sql != '') {
                        $providers_arr = Provider::whereRaw($sub_sql)->pluck('id')->all();
                        if (!empty($providers_arr)) {
                            $query->whereIn('rendering_provider_id', $providers_arr);
                            $query->orWhereIn('billing_provider_id', $providers_arr);
                        }
                    }
                });

                // Facility                
                $claim_qry->orWhere(function ($query) use ($search) {
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

                // Billed
                if (is_numeric($search)) {
                    $claim_qry->orWhere(function ($query) use ($search) {
                        $query->whereHas('dosdetails', function ($q) use ($search) {
                            $q->select(DB::raw("SUM(charge) as total_charge"))
                                    ->groupBy('claim_id')
                                    ->having('total_charge', 'LIKE', '%' . $search . '%')
                                    ->where('is_active', 1);
                        });
                    });

                    /*
                      $claim_qry = $claim_qry->orWhereExists(function($query) use ($search) {
                      $query->selectRaw('sum(charge) as total_charge')
                      ->from('claimdoscptdetails')
                      ->whereRaw('claimdoscptdetails.claim_id = claims.id')
                      ->groupBy('claim_id')
                      ->Having('total_charge', 'LIKE', '%'.$search.'%');
                      });
                     */
                }
            });
        }
        $claim_qry->selectRaw('DISTINCT(claim_info_v1.id), claim_info_v1.*');
        $claim_qry->with('patient', 'facility_detail', 'insurance_details', 'billing_provider')
                ->with(array('rendering_provider' => function($query) use ($orderByField) {
                        if ($orderByField == 'rendering_provider')
                            $query->orderBy('short_name', 'DESC');
                    }));
        $result['count'] = $claim_qry->count(DB::raw('DISTINCT(claim_info_v1.id)'));
        $claim_qry->groupBy('claim_info_v1.id');
        $claim_qry->orderBy($orderByField, $orderByDir);
        if (isset($request['is_export']) && $request['is_export'] == 1) {
            // For export data no need to take limit 
        } else {
            $claim_qry->skip($start)->take($len);
        }
        $result['claim_list'] = $claim_qry->get();
        return $result;
    }

    public function getProvidersIdBySearchKey($searchKey) {
        $provider_ids = [];
        return $provider_ids;
    }

    public function getHoldReasonApi() {
        $hold_options = Holdoption::where('status', 'Active')->orderBy('option', 'ASC')->pluck('option', 'id')->all();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('hold_options')));
    }

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
                    ClaimInfoV1::where('id', $claim_id)->where('status', 'Ready')->update(['status' => 'Hold', 'is_hold' => 1, 'hold_reason_id' => $reason_id]);
                }
                $status = 'success';
                $message = 'Updated successfully';
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

    public function checkAndSubmitEdiClaim() {
        $request = Request::all();
        $claim_ids = $request['claim_ids'];
        $claim_success_count = $claim_error_count = 0;
        $status = '';
        $message = '';
        $claim_details_arr = $calim = [];
        $total_selected_claims = 0;

        // Check electronic claim api available or not //
        if ($this->checkClearingHouseApi()) {
            $clearing_house = '';
            if ($claim_ids != '') {
                $claim_ids_arr = explode(',', $claim_ids);
                $total_selected_claims = count($claim_ids_arr);
                foreach ($claim_ids_arr as $claim_id_encode) {
                    $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id_encode, 'decode');
                    /// Check valid claim or not whether its available in table or not and valid claim or not ///
                    $claim_details = $this->checkValidClaimOrNot($claim_id);

                    $claim_details['claim_id'] = $claim_id;
                    if ($claim_details['status'] == 'success') {
                        if (count($claim_details['patient_insurance_details']) > 0) {
                            /// Check initial scrubbing ///
                            //$this->doInitialScrubbing($claim_details);
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
        }
        return Response::json(array('status' => $status, 'message' => $message, 'data' => compact('claim_process_details')));
    }

    /* public function checkAndSubmitPaperClaim()
      {
      $request = Request::all();
      $claim_ids = $request['claim_ids'];
      $claim_success_count = $claim_error_count = 0;
      $status = '';
      $message = '';
      $claim_details_arr = $calim = [];
      $total_selected_claims = 0;

      if($claim_ids != '')
      {
      $claim_ids_arr = explode(',',$claim_ids);
      $total_selected_claims = count($claim_ids_arr);

      DB::beginTransaction();
      try
      {
      /// Starts - Insert claim transmission into tables ///
      $total_billed_amount = 0;
      $edi_transmission['transmission_type'] = 'Paper';
      //$edi_transmission['file_path'] = $claim_details['claim_id'];
      $edi_transmission = EdiTransmission::create($edi_transmission);
      $edi_transmission_id = $edi_transmission->id;
      /// Ends - Insert claim transmission into tables ///

      foreach($claim_ids_arr as $claim_id_encode)
      {
      $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id_encode,'decode');
      /// Check valid claim or not whether its available in table or not and valid claim or not ///
      $claim_details = $this->checkValidClaimOrNot($claim_id);
      $claim_details['claim_id'] = $claim_id;
      if($claim_details['status'] == 'success')
      {
      /// Check initial scrubbing ///
      $paper_submission_result = $this->basicScrubbing($claim_details,'paper');
      //dd($paper_submission_result);
      if(isset($this->error_msg[$claim_id]) && count(@$this->error_msg[$claim_id]) > 0)
      {
      $claim_error_count++;
      $this->error_msg[$claim_id] = implode('<br>', $this->error_msg[$claim_id]);
      }
      else
      {
      // Do Paper claim create process
      $claim_success_count++;
      //$claim_details_arr[] = $claim_details;

      /// Starts - Insert edi transmission claim details into tables ///
      $transmission_claim['edi_transmission_id'] = $edi_transmission_id;
      $transmission_claim['claim_id'] = $claim_id;
      $transmission_claim['claim_type'] = 'Primary';
      $transmission_claim['insurance_id'] = $claim_details['claim_section']['insurance_id'];
      $transmission_claim['icd'] = implode(',',$claim_details['claim_section']['diagnosis_details']['selected_icd']);
      $transmission_claim['referring_provider_id'] = $claim_details['claim_section']['referring_provider_id'];
      $transmission_claim['total_billed_amount'] = $claim_details['claim_section']['total_charge'];
      $total_billed_amount = $total_billed_amount+$claim_details['claim_section']['total_charge'];
      $transmission_claim_create = TransmissionClaimDetails::create($transmission_claim);
      $transmission_claim_id = $transmission_claim_create->id;
      /// Ends - Insert edi transmission claim details into tables ///

      foreach($claim_details['claim_section']['line_item'] as $line_item)
      {
      $line_item_id = $line_item['line_item_id'];
      /// Starts - Insert edi transmission claim details into tables ///
      $transmission_cpt['edi_transmission_id'] = $edi_transmission_id;
      $transmission_cpt['transmission_claim_id'] = $transmission_claim_id;
      $transmission_cpt['cpt'] = $line_item['cpt'];
      $transmission_cpt['icd_pointers'] = $line_item['icd_pointers'];
      $transmission_cpt['billed_amount'] = $line_item['billed_amount'][0].'.'.@$line_item['billed_amount'][1];
      TransmissionCptDetails::create($transmission_cpt);
      /// Ends - Insert edi transmission claim details into tables ///
      }

      /// Starts - Update Claim table details ///
      $cur_date = date("Y-m-d H:i:s");
      $claims_update = '';
      $claims_update = Claims::find($claim_id);
      $claims_update->status = 'Submitted';
      $claims_update->claim_type = 'paper';

      if($claim_details['claim_section']['claim_submit_count'] == 0)
      $claims_update->submited_date = $cur_date;
      else
      $claims_update->last_submited_date = $cur_date;
      $submission_count = $claim_details['claim_section']['claim_submit_count']+1;
      $claims_update->claim_submit_count = $submission_count;
      $claims_update->save();
      /// Ends - Update Claim table details ///
      $edi_transmission->total_billed_amount = $total_billed_amount;
      $edi_transmission['total_claims'] = $claim_success_count;
      $edi_transmission->save();
      }
      }
      else
      {
      $claim_error_count++;
      $this->error_msg[$claim_id] = $claim_details['message'];
      }

      // Update error message by claim id //
      $claim_error = 'no';
      if(isset($this->error_msg[$claim_id]) && $this->error_msg[$claim_id] != '')
      {
      $claim_array_msg = explode(",", $this->error_msg[$claim_id]);
      $claim_error_message = implode('<br>', $claim_array_msg);
      Claims::where('id',$claim_id)->update(['error_message'=>$claim_error_message,'no_of_issues'=>$claim_error_count]);
      }
      }
      DB::commit();
      } catch (\Exception $e) {
      DB::rollback();
      dd($e);
      }
      }
      else
      {
      $status = 'error';
      $message = 'No claims has been selected';
      }

      if($status != 'error')
      {
      $status = 'success';
      }
      $claim_process_details['total_selected_claims'] = $total_selected_claims;
      $claim_process_details['claim_error_count'] = $claim_error_count;
      $claim_process_details['claim_success_count'] = $claim_success_count;
      return Response::json(array('status' => $status, 'message' => $message, 'data' =>compact('claim_process_details')));
      } */

    public function checkAndSubmitPaperClaim() {
        $request = Request::all();
        $claim_ids = $request['claim_ids'];
        $claim_success_count = $claim_error_count = 0;
        $status = '';
        $message = '';
        $claim_details_arr = $calim = [];
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
                    if ($claim_details['status'] == 'success') {
                        /// Check initial scrubbing ///
                        ClaimInfoV1::where('id', $claim_id)->update(['status' => 'Submitted']);
                        $success_claim[] = $claim_id_encode;
                        $paper_submission_result = $this->basicScrubbing($claim_details, 'paper');
                        //dd($paper_submission_result);
                        if (isset($this->error_msg[$claim_id]) && count(@$this->error_msg[$claim_id]) > 0) {
                            $claim_error_count++;
                            $this->error_msg[$claim_id] = implode('<br>', $this->error_msg[$claim_id]);
                        } else {
                            
                        }
                    } else {
                        $claim_error_count++;
                        $this->error_msg[$claim_id] = $claim_details['message'];
                    }
                    if (isset($this->error_msg[$claim_id]) && $this->error_msg[$claim_id] != '') {
                        $claim_array_msg = explode(",", $this->error_msg[$claim_id]);
                        $claim_error_message = implode('<br>', $claim_array_msg);
                        $data['claim_error_message'] = $claim_error_message;
                        $data['claim_error_count'] = $claim_error_count;
                    } else {
                        
                    }
                }
            } catch (\Exception $e) {

                dd($e);
                $data['claim_error_message'] = $e;
            }
        } else {
            $status = 'error';
            $message = 'No claims has been selected';
        }

        if ($status != 'error') {
            $status = 'success';
        }
        $claim_process_details['total_selected_claims'] = $total_selected_claims;
        $claim_process_details['claim_error_count'] = $claim_error_count;
        $claim_process_details['claim_success_count'] = $total_selected_claims - $claim_error_count;
        $claim_process_details['success_claim'] = $success_claim;
        return Response::json(array('status' => $status, 'message' => $message, 'data' => compact('claim_process_details')));
    }

    /*  public function checkAndSubmitPaperClaim()
      {
      $request = Request::all();
      $claim_ids = $request['claim_ids'];
      $claim_success_count = $claim_error_count = 0;
      $status = '';
      $message = '';
      $claim_details_arr = $calim = [];
      $total_selected_claims = 0;
      $type = "";
      //$this->downloadCMS($request['claim_ids']);
      // $this->printCMS($request['claim_ids'], $type);
      $status = "success";
      $message = "adhaksjl";
      return Response::json(array('status' => $status, 'message' => $message, 'data' =>""));
      //dd($request['claim_ids']);
      } */

    public function downloadCMS($claim_ids, $type = null) {
        $claim_ids = explode(",", $claim_ids);
        $claim = new ChargeApiController();
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

    public function printCMS($claim_ids, $type = null) {
        $claim_ids = explode(",", $claim_ids);
        $claim = new ChargeController();
        $claim_data = '';
        foreach ($claim_ids as $claim_id) {
            $claim_data .= $claim->generatecmsform($claim_id, $type);
        }
        $pdfobj = new PDF();
        $pdfobj::setPaper('A5', 'portrait');
        return $pdfobj::load($claim_data)->show();
    }

    function create_zip($files = array(), $destination = '', $zipname) {

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $outputzippath = $destination . '/' . $zipname;
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

    public function checkValidClaimOrNot($claim_id) {
        $claim = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'billing_provider', 'facility_detail', 'insurance_details', 'dosdetails', 'patient', 'claim_details')->where('id', $claim_id)->where('status', 'Ready')->first();
        $claim_details = $insurance_details = $refering_provider = $rendering_provider = $dependent_details = $billing_provider = $patient_insurance_details = $facility_detail = [];
        if ($claim) {
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
                $billing_provider['zipcode'] = $this->checkAndSetZipCode($claim->billing_provider->zipcode5, $claim->billing_provider->zipcode4);
                $phone = Helpers::splitPhoneNumber($claim->billing_provider->phone);
                $billing_provider['phone_code'] = $phone['code'];
                $billing_provider['phone_no'] = $phone['no'];
                $billing_provider['tax_id_type'] = $claim->billing_provider->etin_type;
                $billing_provider['tax_id'] = $claim->billing_provider->etin_type_number;
                $billing_provider['taxanomy_code'] = $claim->billing_provider->taxanomy->code;
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
                    $patient_insurance_details['patient_insurance_category'] = $patient_insurance->category;
                    $patient_insurance_details['policy_id'] = $patient_insurance->policy_id;
                    $patient_insurance_details['group_id'] = $patient_insurance->group_id;
                    $patient_insurance_details['group_name'] = $patient_insurance->group_name;
                    //$patient_insurance_details['insurance_type'] = $patient_insurance->insurancetype->type_code;
                    $patient_details['relationship'] = $patient_insurance->relationship;

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
            $claim_section['other_claim_ids'] = @$claim->claim_details->other_claim_id;
            $claim_section['accept_assignment'] = @$claim->claim_details->accept_assignment;
            $claim_section['pos'] = isset($claim->pos_code) ? $claim->pos_code : '';
            $claim_section['emg'] = isset($claim->claim_details->emergency) ? @$claim->claim_details->emergency : '';
            $claim_section['epsdt'] = isset($claim->claim_details->epsdt) ? @$claim->claim_details->epsdt : '';

            // Starts - Diagnosis Codes //
            $diagnosis_details = [];
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

    public function checkValidationList($claim_id, $validation_arr, $field_value, $validation_error_msg, $length_arr = [], $relation = '') {
        $required = trans("practice/claim/claims.validation.required");
        foreach ($validation_arr as $key => $validation_option) {
            if ($validation_option == 'not_empty') {
                if ($field_value == '')
                    $this->error_msg[$claim_id][] = '- ' . $relation . " " . $validation_error_msg[$key] . $required;
            }
            elseif ($validation_option == 'length') {
                $length = $length_arr[$key];
                if (strlen($field_value) > $length)
                    $this->error_msg[$claim_id][] = '- ' . $relation . " " . $validation_error_msg[$key] . str_replace('VAR_CHAR', $length, trans("practice/claim/claims.validation.greater_than_msg"));
            }
        }
    }

    public function fieldLengthValidation($field_name, $length, $error_msg) {
        if (strlen($field_name) > $length)
            $this->error_msg[] = $error_msg . str_replace('VAR_CHAR', $length, trans("practice/claim/claims.validation.greater_than_msg"));
    }

    ///*** Starts - Basic / Common Scrubbing ***///
    public function basicScrubbing($claim_details, $claim_submit_type = 'electronic') {
        //dd($claim_details);
        /// Starts - Check required fields and character length ///
        $claim_id = $claim_details['claim_id'];

        /*         * **** Starts - Insured Details ***** */
        /// Starts - Patient details ///
        $patient_details = $claim_details['patient_details'];
        $patient_name = $patient_details['last_name'] . ', ' . $patient_details['first_name'] . ' ' . $patient_details['middle_name'];

        // Patient Name
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $patient_name, [trans("practice/claim/claims.validation.patient_name"), trans("practice/claim/claims.validation.patient_name")], ['', Config::get('siteconfigs.claim_length_validation.patient_name')]);

        // Patient Address
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $patient_details['address'], [trans("practice/claim/claims.validation.patient_address"), trans("practice/claim/claims.validation.patient_address")], ['', Config::get('siteconfigs.claim_length_validation.patient_address')]);

        // Patient City
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $patient_details['city'], [trans("practice/claim/claims.validation.patient_city"), trans("practice/claim/claims.validation.patient_city")], ['', Config::get('siteconfigs.claim_length_validation.patient_city')]);

        // Patient State
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $patient_details['state'], [trans("practice/claim/claims.validation.patient_state"), trans("practice/claim/claims.validation.patient_state")], ['', Config::get('siteconfigs.claim_length_validation.patient_state')]);

        // Patient Zipcode
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $patient_details['zipcode'], [trans("practice/claim/claims.validation.patient_zip"), trans("practice/claim/claims.validation.patient_zip")], ['', Config::get('siteconfigs.claim_length_validation.patient_zip')]);

        // Patient DOB
        $this->checkValidationList($claim_id, ['not_empty'], $patient_details['dob'], [trans("practice/claim/claims.validation.patient_dob")]);

        // Patient Gender
        $this->checkValidationList($claim_id, ['not_empty'], $patient_details['gender'], [trans("practice/claim/claims.validation.patient_sex")]);

        $this->checkValidationList($claim_id, ['not_empty', 'length'], $claim_details['patient_insurance_details']['policy_id'], [trans("practice/claim/claims.validation.policy_id"), trans("practice/claim/claims.validation.policy_id")], ['', Config::get('siteconfigs.claim_length_validation.insured_id')]);

        // Patient Relationship
        if ($claim_submit_type == 'electronic') {
            $this->checkValidationList($claim_id, ['not_empty'], $patient_details['relationship'], [trans("practice/claim/claims.validation.patient_relationship")]);
        }
        /*         * **** Ends - Insured Details ***** */

        /*         * **** Starts - Dependent Details, if not self ***** */
        if ($patient_details['relationship'] != 'Self') {
            $dependent_details = $claim_details['dependent_details'];
            $patient_name = $dependent_details['last_name'] . ', ' . $dependent_details['first_name'] . ' ' . $dependent_details['middle_name'];

            // Patient Name
            $this->checkValidationList($claim_id, ['not_empty', 'length'], $patient_name, [trans("practice/claim/claims.validation.patient_name"), trans("practice/claim/claims.validation.patient_name")], ['', Config::get('siteconfigs.claim_length_validation.patient_name')], $patient_details['relationship']);

            // Patient Address
            $this->checkValidationList($claim_id, ['not_empty', 'length'], $dependent_details['address'], [trans("practice/claim/claims.validation.patient_address"), trans("practice/claim/claims.validation.patient_address")], ['', Config::get('siteconfigs.claim_length_validation.patient_address')], $patient_details['relationship']);

            // Patient City
            $this->checkValidationList($claim_id, ['not_empty', 'length'], $dependent_details['city'], [trans("practice/claim/claims.validation.patient_city"), trans("practice/claim/claims.validation.patient_city")], ['', Config::get('siteconfigs.claim_length_validation.patient_city')], $patient_details['relationship']);

            // Patient State
            $this->checkValidationList($claim_id, ['not_empty', 'length'], $dependent_details['state'], [trans("practice/claim/claims.validation.patient_state"), trans("practice/claim/claims.validation.patient_state")], ['', Config::get('siteconfigs.claim_length_validation.patient_state')], $patient_details['relationship']);

            // Patient Zipcode
            $this->checkValidationList($claim_id, ['not_empty', 'length'], $dependent_details['zipcode'], [trans("practice/claim/claims.validation.patient_zip"), trans("practice/claim/claims.validation.patient_zip")], ['', Config::get('siteconfigs.claim_length_validation.patient_zip')], $patient_details['relationship']);

            // Patient DOB
            $this->checkValidationList($claim_id, ['not_empty'], $dependent_details['dob'], [trans("practice/claim/claims.validation.patient_dob")], $patient_details['relationship']);
        }
        /*         * **** Ends - Dependent Details, if not self ***** */

        /*         * **** Starts - Insurance Details ***** */
        $insurance_details = $claim_details['insurance_details'];
        // Insurance name
        $this->checkValidationList($claim_id, ['not_empty'], $insurance_details['insurance_name'], [trans("practice/claim/claims.validation.insurance_name")]);

        // Insurance Address
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $insurance_details['address_1'], [trans("practice/claim/claims.validation.insurance_address"), trans("practice/claim/claims.validation.insurance_address")], ['', Config::get('siteconfigs.claim_length_validation.patient_address')]);

        // Insurance City
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $insurance_details['city'], [trans("practice/claim/claims.validation.insurance_city"), trans("practice/claim/claims.validation.insurance_city")], ['', Config::get('siteconfigs.claim_length_validation.patient_city')]);

        // Insurance State
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $insurance_details['state'], [trans("practice/claim/claims.validation.insurance_state"), trans("practice/claim/claims.validation.insurance_state")], ['', Config::get('siteconfigs.claim_length_validation.patient_state')]);

        // Insurance Zipcode
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $insurance_details['zipcode'], [trans("practice/claim/claims.validation.insurance_zip"), trans("practice/claim/claims.validation.insurance_zip")], ['', Config::get('siteconfigs.claim_length_validation.patient_zip')]);

        if ($claim_submit_type == 'electronic') {
            $this->checkValidationList($claim_id, ['not_empty'], $insurance_details['payerid'], [trans("practice/claim/claims.validation.insurance_payerid")]);
        }
        /*         * **** Ends - Insurance Details ***** */

        /*         * **** Starts - Billing Provider Details ***** */
        $billing_provider = $claim_details['billing_provider'];

        // Billing Provider Name
        $this->checkValidationList($claim_id, ['not_empty'], $billing_provider['provider_name'], [trans("practice/claim/claims.validation.billing_provider_name")]);

        // Billing Provider Address
        $this->checkValidationList($claim_id, ['not_empty'], $billing_provider['address1'], [trans("practice/claim/claims.validation.billing_provider_address")]);

        // Billing Provider City
        $this->checkValidationList($claim_id, ['not_empty'], $billing_provider['city'], [trans("practice/claim/claims.validation.billing_provider_city")]);

        // Billing Provider State
        $this->checkValidationList($claim_id, ['not_empty'], $billing_provider['state'], [trans("practice/claim/claims.validation.billing_provider_state")]);

        // Billing Provider Zipcode
        $this->checkValidationList($claim_id, ['not_empty'], $billing_provider['zipcode'], [trans("practice/claim/claims.validation.billing_provider_zipcode")]);

        // Billing Provider NPI
        $this->checkValidationList($claim_id, ['not_empty'], $billing_provider['npi'], [trans("practice/claim/claims.validation.billing_provider_npi")]);
        /*         * **** Ends - Billing Provider Details ***** */

        /*         * **** Starts - Rendering Provider Details ***** */
        $rendering_provider = $claim_details['rendering_provider'];

        // Rendering Provider Name
        $this->checkValidationList($claim_id, ['not_empty'], $rendering_provider['provider_name'], [trans("practice/claim/claims.validation.rendering_provider_name")]);

        /* // Rendering Provider Address
          $this->checkValidationList($claim_id, ['not_empty'], $rendering_provider['address1'], [trans("practice/claim/claims.validation.rendering_provider_address")]);

          // Rendering Provider City
          $this->checkValidationList($claim_id, ['not_empty'], $rendering_provider['city'], [trans("practice/claim/claims.validation.rendering_provider_city")]);

          // Rendering Provider State
          $this->checkValidationList($claim_id, ['not_empty'], $rendering_provider['state'], [trans("practice/claim/claims.validation.rendering_provider_state")]);

          // Rendering Provider Zipcode
          $this->checkValidationList($claim_id, ['not_empty'], $rendering_provider['zipcode'], [trans("practice/claim/claims.validation.rendering_provider_zipcode")]); */

        // Rendering Provider NPI
        $this->checkValidationList($claim_id, ['not_empty'], $rendering_provider['npi'], [trans("practice/claim/claims.validation.rendering_provider_npi")]);
        /*         * **** Ends - Rendering Provider Details ***** */

        /*         * **** Starts - Facility Details ***** */
        $facility_detail = $claim_details['facility_detail'];

        // Facility Name
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $facility_detail['facility_name'], [trans("practice/claim/claims.validation.facility_name"), trans("practice/claim/claims.validation.facility_name")], ['', Config::get('siteconfigs.claim_length_validation.facility_name')]);

        // Facility Address
        $this->checkValidationList($claim_id, ['not_empty', 'length'], $facility_detail['address1'], [trans("practice/claim/claims.validation.facility_address"), trans("practice/claim/claims.validation.facility_address")], ['', Config::get('siteconfigs.claim_length_validation.facility_address')]);

        // Facility City
        $this->checkValidationList($claim_id, ['not_empty'], $facility_detail['city'], [trans("practice/claim/claims.validation.facility_city")]);

        // Facility State
        $this->checkValidationList($claim_id, ['not_empty'], $facility_detail['state'], [trans("practice/claim/claims.validation.facility_state")]);

        // Facility Zipcode
        $this->checkValidationList($claim_id, ['not_empty'], $facility_detail['zipcode'], [trans("practice/claim/claims.validation.facility_zip")]);

        // Facility NPI
        // $this->checkValidationList($claim_id, ['not_empty'], $facility_detail['npi'], [trans("practice/claim/claims.validation.facility_npi")]);
        /*         * **** Ends - Facility Details ***** */
        /// Ends - Patient details ///
        /// Ends - Check required fields and character length ///
    }

    ///*** Ends - Basic / Common Scrubbing ***///

    public function checkAndSetZipCode($zip5, $zip4 = '') {
        $zipcode = $zip5;
        if ($zip4 != '')
            $zipcode .= $zip4;
        return $zipcode;
    }

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

    public function viewClaimTransmissionApi($id) {
        $transmission_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $query = EdiTransmission::with('user', 'claim_transmission', 'claim_transmission.claims', 'claim_transmission.insurance', 'claim_transmission.claims.rendering_provider', 'claim_transmission.claims.billing_provider', 'claim_transmission.claims.facility_detail', 'claim_transmission.claims.patient', 'claim_transmission.cpt_transmission')->where('id', $transmission_id);
        //$query =	TransmissionClaimDetails::with('claims','insurance','claims.rendering_provider','claims.billing_provider','claims.facility_detail','claims.patient','cpt_transmission')->has("edi_transmission")->where('edi_transmission_id',$transmission_id)->get();
        $transmission = $query->first();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('transmission')));
    }

    public function downloadClaim837And835Api($type, $id) {
        $download_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if ($type == '837') {
            $file_path = EdiTransmission::where('id', $download_id)->value('file_path');
            $folder = 'clearing_house/';
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
            $folder = 'public/media/clearing_house/' . Session::get('practice_dbid') . '/';
            $full_file_path = $path_medcubic . $folder . $file_name;
            $headers = array('Content-Type: text/plain');
            return Response::download($full_file_path, $file_name, $headers);
        }
        else {
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

    public function getEdiReportsApi() {
        $edi_reports = EdiReport::with('user')->where('is_archive', '!=', 'Yes')->orderBy('created_at', 'DESC')->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('edi_reports')));
    }

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

    public function generateEdiReports() {
        if (App::environment() == "production") {
            if (App::environment() == "production")
                $path_medcubic = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
            else
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
                            $read += strlen($buffer);
                            if (fwrite($local, $buffer) === FALSE) {
                                $status = 'error';
                                $message .= 'Unable to write to local file: $file\n';
                                break;
                            } else {
                                $files_list++;
                                $file_type = explode('.', $file);
                                if (substr($file, 9, 12) == '_EDI_STATUS_') {
                                    $file_status = 'ClearingHouse Payer Response';
                                } elseif (substr($file, 0, 8) == 'FS_HCFA_' && substr($file, 18, 11) != 'ErrorReport') {
                                    $file_status = 'ClearingHouse Response';
                                } elseif (substr($file, 0, 8) == 'FS_HCFA_' && substr($file, 18, 11) == 'ErrorReport') {
                                    $file_status = 'ClearingHouse Error Response';
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

    /**
     * Open and Read the EDI file contents from its file location
     *
     * @param  $id - EDI report ID
     * @return EDI file content if file is available
     */
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
            $message = 'Updated successfully';
        } else {
            $status = 'error';
            $message = 'No claims has been selected';
        }
        return Response::json(array('status' => $status, 'message' => $message));
    }

    public function checkClearingHouseApi() {
        /*
         * Practice based clearing house checking status
         */

        $clearing_house = ClearingHouse::where('status', 'Active')->where('practice_id', Session::get('practice_dbid'))->first();

        if (@$clearing_house->enable_837 == 'Yes') {
            $this->clearing_house = $clearing_house;
            return true;
        } else
            return false;
    }

}
