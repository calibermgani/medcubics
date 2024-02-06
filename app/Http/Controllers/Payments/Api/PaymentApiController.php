<?php

namespace App\Http\Controllers\Payments\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
//use App\Models\Patients\Claims;
use App\Models\Code;
use App\Http\Controllers\Patients\Api\BillingApiController;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController;
use App\Models\Patients\Patient as Patient;
use App\Models\Insurance as Insurance;
use App\Models\Medcubics\Users as Users;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Provider as Provider;
use App\Models\Eras as Eras;
use App\Models\Document as Document;
use Illuminate\Support\Facades\Storage;
use App\Models\Patients\PatientNote as PatientNote;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTWalletV1;
use App\Models\Payments\PMTUnpostedNotesV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\PMTClaimCPTTXV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\SearchFields;
use App\Models\SearchUserData;
use Redirect;
use Input;
use File;
use Auth;
use Response;
use Request;
use Validator;
use Schema;
use DB;
use Session;
use Image;
use App;
use Log;
use App\Http\Controllers\Api\CommonExportApiController;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use App\Models\Patients\DocumentFollowupList as DocumentFollowupList;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use Config;
use App\Traits\ClaimUtil;
use Lang;

class PaymentApiController extends Controller {

    use ClaimUtil;

    public static $patient_paid_amt = 0;
    static $remaining_amount;

    public function getIndexApi($export = '') {
        $payment_details = PMTInfoV1::whereIn('pmt_type', ['Payment', 'Refund', 'Credit Balance'])
                        ->where('void_check', NULL)
                        //'refundwallet is not Required                                          
                        ->whereIn('source', ['posting', 'addwallet', 'scheduler', 'charge'])// Because if we add copay check here and reduce its amount it will not affect with the paid amount on the claim
                        ->with(['insurancedetail', 'created_user'])->get();

        Session::forget('check_number');
        // For eob attachment session data delete value starts here
        if (Session::has('eob_attachment')) {
            Session::forget('eob_attachment');
        }
        $e_remittance = Eras::with('insurance_details')->where('deleted_at', NULL)->get();

        // For eob attachment session data delete value ends here  
        if ($export != "") {
            $exportparam = array(
                'filename' => 'payment',
                'heading' => '',
                'fields' => array(
                    'paymentnumber' => 'Payment ID',
                    'payment_mode' => 'Payment Mode',
                    'Insurance Name' => array('table' => '', 'column' => 'insurance_id', 'use_function' => ['App\Models\Payments\ClaimInfoV1', 'GetInsuranceName'], 'label' => 'Payer'),
                    'check_no' => 'Check No',
                    'card_no' => 'Card Number',
                    'Check date' => array(
                        'table' => '', 'column' => 'check_date', 'use_function' => ['App\Models\Payments\PMTInfoV1', 'CheckDateInfo'], 'label' => 'Check date'),
                    'payment_amt' => 'Check Amount',
                    'amt_used' => 'Posted',
                    'balance' => 'Un posted',
                    'created_at' => 'Posted Date',
                    'created_by' => array(
                        'table' => 'created_user', 'column' => 'name', 'label' => 'Posted By'
                    ),
                )
            );
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $payment_details, $export);
        }
        $ClaimController = new ClaimControllerV1("payment");
        $search_fields_data = $ClaimController->generateSearchPageLoad('payment_listing');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('payment_details', 'e_remittance', 'search_fields', 'searchUserData')));
    }

    /* payment listing page starts here */

    public function getListIndexApi($export = '') {
        $request = Request::all();
        $request['is_export'] = ($export != "") ? 1 : 0;
        $result = $this->getPaymentsSearchApi($request);
        $payment_list = $result["payment_list"];

        $count = $result["count"];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('payment_list', 'count')));
    }

    public function getPaymentsSearchApi($request) {
        $start = isset($request['start']) ? $request['start'] : 0;
        $len = (isset($request['length'])) ? $request['length'] : 50;
        $search = (!empty($request['search']['value'])) ? trim($request['search']['value']) : "";
        $orderByField = 'pmt_info_v1.updated_at';
        $orderByDir = 'DESC';
        if (isset($request['is_export']) && $request['is_export'] == 1)
            $orderByDir = 'DESC';

        if (!empty($request['order'])) {
            $orderByField = (isset($request['order'][0]['column'])) ? $request['order'][0]['column'] : 'pmt_info_v1.updated_at';
            switch ($orderByField) {
                case '0':
                    $orderByField = 'pmt_info_v1.pmt_no';                          // Payment ID
                    break;

                case '1':
                    $orderByField = 'pmt_info_v1.id';                          // Payment ID
                    break;

                case '2':
                    $orderByField = 'pmt_info_v1.created_at';                  // Payer
                    break;

                case '3':
                    $orderByField = 'pmt_info_v1.pmt_mode';                 // Mode // check_number 
                    break;

                case '4':
                    $orderByField = 'pmt_info_v1.check_date';              // Check Date
                    break;

                case '5':
                    $orderByField = 'pmt_info_v1.pmt_amt';                 // Check Amount
                    break;

                case '6':
                    $orderByField = 'pmt_info_v1.amt_used';                 // Check Amount
                    break;

                case '7':
                    $orderByField = 'bal_amt';                                  // Posted
                    break;

                case '8':
                    $orderByField = 'pmt_info_v1.created_at';                  // Created_on
                    break;

                case '9':
                    $orderByField = 'pmt_info_v1.created_at';                  // Created_on
                    break;

                case '10':
                    $orderByField = 'pmt_info_v1.created_at';                  // user
                    break;

                default:
                    $orderByField = 'pmt_info_v1.updated_at';
                    break;
            }

            $orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'DESC';
        }
        $orderByField = ($orderByField == '') ? 'pmt_info_v1.updated_at' : $orderByField;

        //$payment_qry = Payment::whereIn('payment_type', ['Payment', 'Refund'])
        $payment_qry = PMTInfoV1::whereIn('pmt_type', ['Payment', 'Refund', 'Credit Balance'])
                ->where('void_check', NULL)
                //->whereIn('source', ['posting', 'addwallet', 'scheduler', 'charge', 'refundwallet'])// Because if we add copay check here and reduce its amount it will not affect with the paid amount on the claim
                //refundwallet is not Required
                ->whereIn('source', ['posting', 'addwallet', 'scheduler', 'charge','refundwallet']);// Because if we add copay check here and reduce its amount it will not affect with the paid amount on the claim
                /*->leftJoin('pmt_check_info_v1','pmt_check_info_v1.id','=','pmt_info_v1.pmt_mode_id')
                ->leftJoin('pmt_card_info_v1','pmt_card_info_v1.id','=','pmt_info_v1.pmt_mode_id')
                ->leftJoin('pmt_eft_info_v1','pmt_eft_info_v1.id','=','pmt_info_v1.pmt_mode_id')
                ->leftJoin('insurances','insurances.id','=','pmt_info_v1.pmt_mode_id')
                ->leftJoin('pmt_unposted_notes','pmt_unposted_notes.id','=','pmt_info_v1.pmt_mode_id')
                ->with(['created_user']);*/
                //->with(['checkDetails', 'creditCardDetails', 'eftDetails', 'insurancedetail', 'created_user', 'pmtNotes']);

        $payment_qry->leftjoin('insurances', function($join) {
            $join->on('insurances.id', '=', 'pmt_info_v1.insurance_id');
        });
        $payment_qry->leftjoin('betacore.users', function($join) {
            $join->on('betacore.users.id', '=', 'pmt_info_v1.created_by');
        });

        $payment_qry->leftjoin('pmt_check_info_v1', function($join) {
            $join->on('pmt_check_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id');
            // money order and check both data having in pmt_check_info_v1 itself.
            $join->whereIn('pmt_info_v1.pmt_mode', ['Check','Money Order']);            
            //$join->on('pmt_info_v1.pmt_mode', '=', DB::raw("'Check'"));
            // money order and check both data having in pmt_check_info_v1 itself.
            // $join->on(DB::raw('( pmt_info_v1.pmt_mode = "Check"  OR pmt_info_v1.pmt_mode = "Money Order" )'), DB::raw(''), DB::raw(''));
        });

        $payment_qry->leftjoin('pmt_eft_info_v1', function($join) {
            $join->on('pmt_eft_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id');
            $join->on('pmt_info_v1.pmt_mode', '=', DB::raw("'EFT'"));
        });

        $payment_qry->leftjoin('pmt_card_info_v1', function($join) {
            $join->on('pmt_card_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id');
            $join->on('pmt_info_v1.pmt_mode', '=', DB::raw("'Credit'"));
        });

        // if (!empty(json_decode(@$request['dataArr']['data']['pmt_no'])))
        //    $payment_qry->where('pmt_info_v1.pmt_no', 'LIKE', '%' .json_decode($request['dataArr']['data']['pmt_no']). '%');
        /* Ajax search start */
        /* if (!empty($search)) {
          $payment_qry->Where(function ($payment_qry) use ($search) {
          $payment_qry->Where(function ($query) use ($search) {

          // Payment ID
          $query = $query->orWhere('pmt_no', 'LIKE', '%' . $search . '%');
          // Check
          $query->orWhere('pmt_check_info_v1.check_no', 'LIKE', '%' . $search . '%');
          if (strpos(strtolower($search), "/") !== false) {
          $dateSearch = date("Y-m-d", strtotime(@$search));
          $query = $query->orWhere('pmt_check_info_v1.check_date', 'LIKE', '%' . $dateSearch . '%');
          }
          /*
          $query->orWhereHas('checkDetails', function($query) use ($search) {
          $query = $query->Where('check_no', 'LIKE', '%' . $search . '%');
          if (strpos(strtolower($search), "/") !== false) {
          $dateSearch = date("Y-m-d", strtotime(@$search));
          $query = $query->orWhere('check_date', 'LIKE', '%' . $dateSearch . '%');
          }
          });
         */
        // EFT                    
        /* $query->orWhere('pmt_eft_info_v1.eft_no', 'LIKE', '%' . $search . '%');
          if (strpos(strtolower($search), "/") !== false) {
          $dateSearch = date("Y-m-d", strtotime(@$search));
          $query = $query->orWhere('pmt_eft_info_v1.eft_date', 'LIKE', '%' . $dateSearch . '%');
          }
          /*
          $query->orWhereHas('eftDetails', function($query) use ($search) {
          $query = $query->Where('eft_no', 'LIKE', '%' . $search . '%');
          if (strpos(strtolower($search), "/") !== false) {
          $dateSearch = date("Y-m-d", strtotime(@$search));
          $query = $query->orWhere('eft_date', 'LIKE', '%' . $dateSearch . '%');
          }
          });
         */
        // card
        /* $query->orWhere('pmt_card_info_v1.card_last_4', 'LIKE', '%' . $search . '%');

          $query->orWhereHas('creditCardDetails', function($q) use ($search) {
          $q->Where('card_last_4', 'LIKE', '%' . $search . '%');
          });


          // Mode
          $query = $query->orWhere('pmt_mode', 'LIKE', '%' . $search . '%');

          // Included payment method for search patient payment
          if (stripos($search, 'pat')!==false)
          $query = $query->orWhere('pmt_method', 'LIKE', '%' . $search . '%');

          // Check Date, Created On
          if (strpos(strtolower($search), "/") !== false) {
          $dateSearch = date("Y-m-d", strtotime(@$search));
          $query = $query->orWhere('pmt_info_v1.created_at', 'LIKE', '%' . $dateSearch . '%');
          } else {
          $query = $query->orWhere('pmt_info_v1.created_at', 'LIKE', '%' . $search . '%');
          }
          // Check Amount
          $query = $query->orWhere('pmt_amt', 'LIKE', '%' . $search . '%');

          // Posted
          $query = $query->orWhere('amt_used', 'LIKE', '%' . $search . '%');
          // Un Posted
          $query = $query->orWhere('balance', 'LIKE', '%' . $search . '%');
          });

          // Payer
          $payment_qry->orWhere(function ($query) use ($search) {
          $searchValues = array_filter(explode(",", $search));
          $sub_sql = '';
          foreach ($searchValues as $searchKey) {
          $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
          $sub_sql .= "insurances.short_name LIKE '%$searchKey%' ";
          }
          if ($sub_sql != '')
          $query->whereRaw($sub_sql);
          });

          $payment_qry->orWhere(function ($query) use ($search) {
          $userIds = Users::where('name', 'like', '%' . $search . '%')->orwhere('short_name', 'like', '%' . $search . '%')->pluck('id')->all();
          if (!empty($userIds)) {
          $query->whereIn('pmt_info_v1.created_by', $userIds);
          }
          });
          });
          } */
        /* Ajax search end */
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
        if (!empty($request['dataArr'])) {
            $payment_qry = $this->searchFilterApi($payment_qry, $request);
        }
        $result['count'] = $payment_qry->count(DB::raw('DISTINCT(pmt_info_v1.id)'));
        $payment_qry->groupBy('pmt_info_v1.id');
        $payment_qry->selectRaw('*,pmt_info_v1.id as pmt_id,pmt_info_v1.created_at as created_date, betacore.users.short_name as user_name, insurances.short_name as insurance_name, (pmt_info_v1.pmt_amt - pmt_info_v1.amt_used) as bal_amt ');
        $payment_qry->orderBy($orderByField, $orderByDir);

        if (isset($request['is_export']) && $request['is_export'] == 1) {
            // For export data no need to take limit 
        } else {
            $payment_qry->skip($start)->take($len);
        }
        $result['payment_list'] = $payment_qry->get();

        //  dd($result['payment_list']);
        return $result;
    }

    public function searchFilterApi($payment_qry, $request = []) {
        if (!empty(json_decode(@$request['dataArr']['data']['pmt_no'])))
            $payment_qry->where('pmt_info_v1.pmt_no', 'LIKE', '%' . trim(json_decode($request['dataArr']['data']['pmt_no'])) . '%');

        $ins_data = isset($request['dataArr']['data']['insurance_id']) ? (array) json_decode($request['dataArr']['data']['insurance_id']) : [];

        $pmtModes = isset($request['dataArr']['data']['pay_mode']) ? (array) json_decode($request['dataArr']['data']['pay_mode']) : [];

        if (!empty($ins_data)) {
            if (is_array($ins_data) && !in_array("0", $ins_data)) { 
                $payment_qry->where('pmt_method', 'Insurance')->whereIn('pmt_info_v1.insurance_id', $ins_data);
            } elseif ($ins_data != '' && !empty($ins_data)) { 
                $payment_qry->whereIn('pmt_info_v1.insurance_id', $ins_data);
            }
        }

        if (!empty(json_decode(@$request['dataArr']['data']['pmt_type']))) {
            if (is_array(json_decode(@$request['dataArr']['data']['pmt_type'])))
                $payment_qry->whereIn('pmt_info_v1.pmt_method', json_decode($request['dataArr']['data']['pmt_type']));
            else
                $payment_qry->where('pmt_info_v1.pmt_method', json_decode($request['dataArr']['data']['pmt_type']));
        }

        if (!empty($pmtModes)) {
            $payment_qry->whereIn('pmt_info_v1.pmt_mode', $pmtModes);
        }

        if (!empty(json_decode(@$request['dataArr']['data']['check_no']))) {
            // Check is pmt mode selected then search only with in the mode data
            // 'Cash', 'Check', 'Money Order', 'Credit', 'EFT'
            if (empty($pmtModes) || ( in_array('EFT', $pmtModes) && ( in_array('Check', $pmtModes) || in_array('Money Order', $pmtModes)))) {
                $srch_no = json_decode($request['dataArr']['data']['check_no']);

                $payment_qry->Where(function ($query) use ($srch_no) {
                  $query->where('pmt_check_info_v1.check_no', 'LIKE', '%' . trim($srch_no) . '%');
                  $query->orwhere('pmt_eft_info_v1.eft_no', 'LIKE', '%' . trim($srch_no) . '%');
                  $query->orwhere('pmt_card_info_v1.card_last_4', 'LIKE', '%' . trim($srch_no) . '%');
                })->whereNull('pmt_info_v1.void_check');

            } elseif (in_array('Check', $pmtModes) || in_array('Money Order', $pmtModes)) {
                $payment_qry->where('pmt_check_info_v1.check_no', 'LIKE', '%' . trim(json_decode($request['dataArr']['data']['check_no'])) . '%')->whereNull('pmt_info_v1.void_check');
            } elseif (in_array('EFT', $pmtModes)) {
                $payment_qry->where('pmt_eft_info_v1.eft_no', 'LIKE', '%' . trim(json_decode($request['dataArr']['data']['check_no'])) . '%')->whereNull('pmt_info_v1.void_check');
            } elseif (in_array('Credit', $pmtModes)) {
                $payment_qry->where('pmt_card_info_v1.card_last_4', 'LIKE', '%' . trim(json_decode($request['dataArr']['data']['check_no'])) . '%')->whereNull('pmt_info_v1.void_check');
            }
        }

        // Filter by check amount
        if (!empty(json_decode(@$request['dataArr']['data']['check_amt']))) {
            $payment_qry->where('pmt_info_v1.pmt_amt', '=', trim(json_decode($request['dataArr']['data']['check_amt'])));
        }

        // Filter by created
        if (!empty(json_decode(@$request['dataArr']['data']['create_date']))) {
            $date = explode('-', json_decode($request['dataArr']['data']['create_date']));
            $practice_timezone = Helpers::getPracticeTimeZone();
            $from = date("Y-m-d", strtotime($date[0]));
            if ($from == '1970-01-01') {
                $from = '0000-00-00';
            }
            $to = date("Y-m-d", strtotime($date[1]));
            //$from = App\Http\Helpers\Helpers::utcTimezoneStartDate($date[0]);
            //$to = App\Http\Helpers\Helpers::utcTimezoneEndDate($date[1]);
            $payment_qry->where(function($query) use ($from, $to, $practice_timezone) {
                $query->whereRaw("DATE(CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$from' and DATE(CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$to'");
            });
        }

        if (!empty(json_decode(@$request['dataArr']['data']['check_date']))) {
            $date = explode('-', json_decode($request['dataArr']['data']['check_date']));
            $from = date("Y-m-d", strtotime(@$date[0]));
            if ($from == '1970-01-01') {
                $from = '0000-00-00';
            }
            $to = date("Y-m-d", strtotime(@$date[1]));
            $payment_qry->where(function($query) use ($from, $to) {
                $query->whereRaw("DATE(pmt_check_info_v1.check_date) >= '$from' and DATE(pmt_check_info_v1.check_date) <= '$to'")
                        ->orwhereRaw("DATE(pmt_eft_info_v1.eft_date) >= '$from' and DATE(pmt_eft_info_v1.eft_date) <= '$to'");
            });
        }

        if (!empty(json_decode(@$request['dataArr']['data']['unpostedamt']))) {
            $unposted_amt = json_decode($request['dataArr']['data']['unpostedamt']);
            $unposted_amt_con = '=';
            if (preg_match('/</', $unposted_amt)) {
                $exp = explode('<', $unposted_amt);
                $unposted_amt_con = '<=';
                $unposted_amt = $exp[1];
            }
            if (preg_match('/>/', $unposted_amt)) {
                $exp = explode('>', $unposted_amt);
                $unposted_amt_con = '>=';
                $unposted_amt = $exp[1];
            }
            $payment_qry->where(DB::raw('(pmt_info_v1.pmt_amt - pmt_info_v1.amt_used)'), $unposted_amt_con, $unposted_amt);
        }

        if (!empty(json_decode(@$request['dataArr']['data']['postedamt']))) {
            $posted_amt = json_decode($request['dataArr']['data']['postedamt']);
            $posted_amt_con = '=';
            if (preg_match('/</', $posted_amt)) {
                $exp = explode('<', $posted_amt);
                $posted_amt_con = '<=';
                $posted_amt = $exp[1];
            }
            if (preg_match('/>/', $posted_amt)) {
                $exp = explode('>', $posted_amt);
                $posted_amt_con = '>=';
                $posted_amt = $exp[1];
            }
            $payment_qry->where('pmt_info_v1.amt_used', $posted_amt_con, $posted_amt);
        }

        if (!empty(json_decode(@$request['dataArr']['data']['created_by'])))
            $payment_qry->whereIn('pmt_info_v1.created_by', json_decode($request['dataArr']['data']['created_by']));
        return $payment_qry;
    }

    /* payment listing page ends here */

    public function getCreateApi($request) {

        $paymentV1Api = new PaymentV1ApiController();
        $claim_id = isset($request['claim_ids']) ? trim($request['claim_ids'], ',') : '';
        if ($claim_id != '')
        //$claim_id = explode(',',$claim_id);
            $patient_id = isset($request['patient_id']) ? Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode') : '';
        $file = Request::file('filefield_eob');
        if (isset($request['payment_type_ins']) && $request['payment_type_ins'] != 'Adjustment' && empty($request['payment_detail_id'])) {
            $pmt_mode = ($request['payment_type_ins'] == "Refund") ? @$request['insur_payment_mode_refund'] : @$request['insur_payment_mode'];
            $check_val = (is_numeric($request['check_no']) && $request['check_no'] / 1 == 0) ? false : true;
            $checkNoStatus = PMTInfoV1::findCheckExistsOrNot($request['check_no'], 'Insurance', $pmt_mode, 'insurancePayment', '');
            if ($check_val == 0) {
                $checkNoStatus = false;
            }
            if ($checkNoStatus) {
                return Response::json(array('status' => 'error', 'message' => "check number already exits", 'data' => $request['patient_id']));
            }
        }
        if (isset($claim_id) && !empty($claim_id) && !empty($request)) {

            // $request['payment_mode'] = $request['insur_payment_mode_refund'];
            $claims_list = $paymentV1Api->createPayment($request, $claim_id);
            $claims_list_data = $claims_list->getData();
            $claims_lists = $claims_list_data->data->claim_lists;
            $claimTotal = 0;
            if (!empty($claims_lists)) {
                $claims_lists->cpttransactiondetails = (isset($claims_list_data->data->cpt_tx_list)) ? $claims_list_data->data->cpt_tx_list : [];
                $claimTotal = $claims_list_data->data->total;
            }
            $create_claim_id = Helpers::getEncodeAndDecodeOfId($claim_id[0], 'decode'); // Get the first claim record when creating payment
            $denial_code = config::get('siteconfigs.payment.denial_code');
            $remarkcode = Code::where('status', 'Active')->whereIn('codecategory_id', [config('siteconfigs.payment.remark_code'), config('siteconfigs.payment.adjustment_code')])->pluck('transactioncode_id', 'id')->all();
            // Statically given code category id as 4 For remark codes 
            $i = 1;
            $remark_codes = [];
            $j = 3;
            foreach ($denial_code as $key => $denialcode) {
                $remarkcodes[$denialcode] = array_map(function($remarkcode) use ($denialcode) {
                    return $denialcode . '' . $remarkcode;
                }, $remarkcode);
                $remark_codes = array_merge($remark_codes, $remarkcodes[$denialcode]);

                $i++;
            }
            $remarkcode = $remark_codes;
            $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
            $insurance_lists = Patient::getPatientInsuranceWithCategory($patient_id, false, $claim_id, "posting");
            $check_box_count['total_lineitem_count'] = (!empty($claims_lists)) ? $claims_list_data->data->claim_lists->total_lineitem_count : 0;
            $check_box_count['active_count'] = (!empty($claims_lists)) ? $claims_list_data->data->claim_lists->active_lineitem_count : 0;

            $insurance_list_total = Insurance::where('status', 'Active')->pluck('insurance_name', 'id')->all();
            $insurance_list_total[0] = 'Self';

            // To get EOB attachment 
            if (!empty($request['temp_type_id'])) {
                Session::put('eob_attachment', $request['temp_type_id']);
            }

            // dd($claims_list);
            // To get EOB attachment             
            if (!empty($claims_list)) { //dd($claims_list);
                // return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('claim_lists', 'remarkcode', 'insurance_lists', 'insurance_list_total', 'check_box_count')));
                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_lists', 'remarkcode', 'insurance_lists', 'insurance_list_total', 'check_box_count', 'claimTotal')));
            } else {
                return Response::json(array('status' => 'error', 'message' => "No claims available", 'data' => ''));
            }
        } else {
            return Response::json(array('status' => 'error', 'message' => "No claims available", 'data' => ''));
        }
    }

    public function getStoreApi($request) {
        try {
            $data = $request;
            $paymentV1ApiConntroller = new PaymentV1ApiController();
            $response = $paymentV1ApiConntroller->insurancePaymentProcessHandler($data);
            $responseData = $response->getData();
            $patientId = $responseData->data;
            $status = @$responseData->status;
            $msg = @$responseData->message;
            $payment_id = isset($responseData->payment_id) ? $responseData->payment_id : 0;
            return Response::json(array('status' => $status, 'message' => $msg, 'data' => $patientId, 'payment_id' => $payment_id));
        } catch (Exception $e) {
            DB::rollBack();
            \Log::info("Error occured txn rollbacked on pmt api getStoreApi. Error " . $e->getMessage());
            throw $e;
        }
    }

    //Unused functions are commented 
    /* function findPatientNextInsurance($claim_id) {
      $current_insurance = ClaimInfoV1::where('id', $claim_id)->select('patient_id', 'insurance_id', 'insurance_category')->first();
      $current_insurance_id = $current_insurance->insurance_id;
      $current_category = $current_insurance->insurance_category;
      $patient_id = $current_insurance->patient_id;
      $current_category = trim($current_category);
      if ($current_category == 'Primary') {
      $next_insurance_data = PatientInsurance::where('patient_id', $patient_id)->where('category', 'like', '%Secondary%')->pluck('insurance_id');
      $insurance_data['insurance_id'] = $next_insurance_data;
      $insurance_data['category'] = "Secondary";
      if (empty($next_insurance_data)) {
      $next_insurance_data = PatientInsurance::where('patient_id', $patient_id)->where('category', 'like', '%Tertiary%')->pluck('insurance_id');
      $insurance_data['insurance_id'] = $next_insurance_data;
      $insurance_data['category'] = "Secondary";
      }
      } elseif ($current_category == 'Secondary') {
      $next_insurance_data = PatientInsurance::where('patient_id', $patient_id)->where('category', 'like', '%Tertiary%')->pluck('insurance_id');
      $insurance_data['insurance_id'] = $next_insurance_data;
      $insurance_data['category'] = "Tertiary";
      } elseif ($current_category == 'Tertiary') {
      $next_insurance_data = "patient";
      }
      return (isset($insurance_data['insurance_id']) && ($insurance_data['insurance_id'] != '')) ? $insurance_data : "patient";
      } */

    // Back end calculation to save payment_claim_details table starts here
    /* function claimbalancecalculation($data) {

      $claim_data = ClaimInfoV1::where('id', $data['claim_id'])->first();
      $payment_type = $data['payment_type'];
      if (isset($data['balance_secondary'])) {
      $total_billed = array_sum($data['balance_secondary']);    // Summing up all the values by line itemwise
      //$total_billed = array_sum($data['balance_original']); // changed because patient paid not getting applied eventhough it applied already  scneario MED-1890
      } elseif (isset($data['old_balance'])) {
      $total_billed = array_sum($data['cpt_billed_amt']);  //  array_sum($data['old_balance']) making takeback issue
      } else {
      $total_billed = array_sum($data['cpt_billed_amt']);    // Summing up all the values by line itemwise
      }

      //dd($total_billed)       ;
      // When do positive and negative transaction on each CPT makes balance calcualtion error
      if (isset($data['patient_exist_balance']) && isset($data['insurance_exist_balance'])) {
      $total_bal = array_sum($data['patient_exist_balance']) + array_sum($data['insurance_exist_balance']);
      $total_billed = ($total_billed < $total_bal) ? $total_bal : $total_billed;
      }
      $adjustment_calculation = array_sum($data['adjustment']);
      //$adjustment = array_map(function($data['cpt_allowed_amt']) {  return ($data['cpt_allowed_amt'] != 0)?;  }, $data['cpt_allowed_amt']);
      //dd($total_billed);
      // If no allowed amount is given insurance balance getting calculated wrongly thats y made dynamic allowed amount

      foreach ($data['cpt_allowed_amt'] as $key => $value) {
      $data['cpt_allowed_amt'][$key] = ($data['cpt_allowed_amt'][$key] == 0 && $data['adjustment'][$key] == 0) ? $data['old_balance'][$key] : $data['cpt_allowed_amt'][$key];
      }

      $total_allowed = (array_sum($data['cpt_allowed_amt']) <= 0 && $adjustment_calculation == 0 || array_sum($data['cpt_allowed_amt']) <= 0 && $payment_type != "Adjustment" && $adjustment_calculation == 0) ? $total_billed : array_sum($data['cpt_allowed_amt']);
      // If allowed amount is zero then take the billed amount as allowed amount
      //dd($data);
      // dd($total_billed);
      $total_copay = array_sum($data['co_pay']);
      $total_deductable = array_sum($data['deductable']);
      $total_co_ins = array_sum($data['co_ins']);
      $total_with_held = array_sum($data['with_held']);
      if ($adjustment_calculation != 0 && $payment_type == "Payment") {
      $total_adjustment = $adjustment_calculation;
      } else {
      //  $total_adjustment = $total_billed - $total_allowed;
      $total_adjustment = $adjustment_calculation;
      }
      $total_paid = array_sum($data['paid_amt']);
      //dd($data);
      $patient_paid = $claim_data->patient_paid; // Need to change this as like using the patient paid unused amount because for the next cycle
      // dd($patient_paid)       ;

      $patient_due_claim = $claim_data->patient_due;

      $patient_adjustment = $claim_data->patient_adjusted; // We need this value when we change the responsibility to patient from insurance (Need to clear the balacne amount)
      $claim_patient_due = $claim_data->patient_due;
      $claim_insurance_due = $claim_data->insurance_due;
      $balance_amt = $claim_data->balance_amt;

      $patient_balance = $total_copay + $total_deductable + $total_co_ins;

      if ($patient_adjustment > 0 && isset($data['patient_adjusted_excluded'])) {
      $total_billed = array_sum($data['patient_adjusted_excluded']);
      }
      // $insurance_balance = $total_allowed - ($total_paid+$patient_balance+$total_with_held);
      $insurance_balance = ($total_billed) - ($total_paid + $patient_balance + $total_with_held + $total_adjustment);
      //dd($total_billed);
      $exist_total = $claim_data->total_adjusted + $claim_data->total_paid + $claim_data->total_withheld; // Already subtracted adjsutment also get subtracted again so that we use this.
      if ($patient_adjustment && $total_billed <= $exist_total) {
      $insurance_balance = $insurance_balance;
      } else if ($patient_adjustment) {
      $insurance_balance = $insurance_balance - $patient_adjustment;
      }
      //$insurance_balance = $insurance_balance -$patient_adjustment; // Patient adjusted should be reflect on insurance balance and AR balance too;
      //dd($insurance_balance)    ;
      $insurance_exist_balance = isset($data['insurance_exist_balance']) ? array_sum($data['insurance_exist_balance']) : 0;

      if ($claim_patient_due > 0 && $claim_insurance_due < 0) {
      if ($insurance_balance > 0) {
      $ins_due = $insurance_balance;  // When one CPT was in negaitve and one CPT has balance then combine both
      } else {
      $ins_due = $claim_insurance_due - $total_paid;
      }
      //$return_val['patient_due'] = $claim_patient_due;   //when do refund it makes problem
      $return_val['patient_due'] = 0;   //when do refund it makes problem
      $return_val['balance'] = $ins_due + $claim_patient_due;
      $return_val['insurance_due'] = $ins_due;
      } else {
      $return_val['patient_due'] = $patient_balance;
      $return_val['balance'] = $patient_balance + $insurance_balance;
      $return_val['insurance_due'] = $insurance_balance;
      }
      //dd($return_val['patient_due']);
      $return_val['total_adjusted'] = $total_adjustment;
      // Insurance adjustment concept starts here
      $return_val['total_withheld'] = $total_with_held;
      $return_val['total_paid'] = $total_paid;
      $return_val['total_allowed'] = ($total_allowed < 0) ? 0 : $total_allowed;
      $return_val['remaining_amt'] = 0;
      $return_val['patient_paid'] = $patient_paid;
      $return_val['patient_adjustment'] = $patient_adjustment;

      if ($payment_type == "Adjustment") {
      $adjusted_amt = array_sum($data['adjustment']);
      $return_val['total_adjusted'] = $adjusted_amt;
      // $return_val['balance']  = $return_val['balance'] - $adjusted_amt;
      $return_val['balance'] = $return_val['balance'];
      $return_val['insurance_due'] = $insurance_balance; // Insurance adjsutment concept ends here
      } elseif ($payment_type == "Refund") {
      $return_val['total_paid'] = -1 * $total_paid;
      $return_val['total_adjusted'] = 0;
      $return_val['patient_adjustment'] = 0;
      $return_val['total_allowed'] = 0;
      $return_val['balance'] = array_sum($data['old_balance']) + $total_paid;
      // $return_val['balance'] = array_sum($data['balance']);
      $return_val['insurance_due'] = $return_val['balance'];
      // dd($return_val);
      } elseif ($payment_type == "Payment" && $total_adjustment < 0) {  // When do payment with negative adjustment
      $return_val['balance'] = $return_val['balance'];
      $return_val['insurance_due'] = $return_val['insurance_due'];
      }
      // if(!empty($data['next_insurance_id'])) {
      //}
      //dd($data['next_responsibility']);
      $insurance_data = [];
      if (!isset($data['next_responsibility']) && empty($data['next_responsibility'])) {
      $insurance_data = $this->findPatientNextInsurance($data['claim_id']);
      }

      if (isset($insurance_data['insurance_id']) && !empty($insurance_data['insurance_id']) || @$data['next_responsibility'] != "patient") {
      $return_val['remaining_amt'] = $patient_paid;
      } else if ($return_val['balance'] <= 0 && $return_val['patient_due'] == 0 && $insurance_exist_balance > 0 && ($payment_type == "Payment" || $payment_type == "Adjustment")) {
      $ins_payment = $insurance_exist_balance - $total_paid;
      $remaining = ($ins_payment < 0) ? $patient_paid : $patient_paid - ($ins_payment - $total_adjustment);
      $return_val['remaining_amt'] = ($remaining > 0) ? $remaining : 0;
      } elseif ($return_val['balance'] >= 0 && $return_val['patient_due'] == 0 && $insurance_exist_balance == 0 && ($payment_type == "Payment" || $payment_type == "Adjustment")) {
      $total_paid = $claim_data->total_paid;
      $remaining = $patient_paid - $return_val['balance'];
      // dd($remaining);
      $remaining = ($return_val['balance'] == 0) ? $patient_paid - $remaining : $remaining;
      $return_val['remaining_amt'] = ($remaining > 0) ? $remaining : 0;
      } elseif ($return_val['balance'] < 0 && $return_val['patient_due'] == 0 && $payment_type == "Adjustment") {
      //$return_val['balance'] <= 0 condition removed because with adjsutment it made patient paid to get refund

      $return_val['remaining_amt'] = $patient_paid;
      } elseif ($return_val['balance'] <= 0 && $return_val['patient_due'] == 0 && $payment_type == "Payment" && $total_paid > 0) {

      $ins_payment = $insurance_exist_balance - $total_paid;
      $remaining = ($ins_payment < 0 && $patient_paid > abs($ins_payment)) ? abs($ins_payment) : $patient_paid;
      $return_val['remaining_amt'] = $remaining;
      } elseif ($return_val['balance'] == 0 && $return_val['patient_due'] > 0 && $return_val['insurance_due'] < 0 && ($payment_type == "Payment" || $payment_type == "Adjustment")) {
      $patient_balance_remaining = $return_val['patient_due'] - $patient_paid;
      $return_val['remaining_amt'] = ($patient_balance_remaining > 0) ? 0 : abs($patient_balance_remaining);
      } elseif ($payment_type == "Refund") {
      $return_val['remaining_amt'] = 0;
      } elseif ($return_val['balance'] > 0 && $patient_paid > 0) {

      $remaining = $patient_paid - $return_val['balance'];
      $return_val['remaining_amt'] = ($remaining > 0) ? $remaining : 0;
      }
      //dd($return_val) ;
      // echo "<pre>";print_r($return_val) ; exit;
      return $return_val;
      } */

    // Back end calculation to save payment_claim_details table ends here

    /* public function saveClaimCptInsurancedata($data, $savedata) {
      if (!empty($data)) {
      $dos_spt_details = [];
      for ($i = 0; $i < count($data['cpt']); $i++) {

      if (!empty($data['cpt'][$i])) {
      $dos_detail_id = Helpers::getEncodeAndDecodeOfId($data['ids'][$i], 'decode');
      $get_exiting_status = Claimdoscptdetail::where('id', $dos_detail_id)->first();
      //dd($get_exiting_status)                 ;
      $claim_patient_due = $get_exiting_status->patient_balance;
      $claim_insurance_due = $get_exiting_status->insurance_balance;
      $patient_paid = $get_exiting_status->patient_paid;
      $patient_adjusted = $get_exiting_status->patient_adjusted;
      $balance = $get_exiting_status->balance;
      //dd($balance);
      $payment_type = $data['payment_type'];
      $paid_amt = $data['paid_amt'][$i];
      $allowed_amt = $data['cpt_allowed_amt'][$i];
      $billed_amt = $data['cpt_billed_amt'][$i];
      $adjustment = $data['adjustment'][$i];

      $old_balance = isset($data['old_balance'][$i]) ? $data['old_balance'][$i] : ($allowed_amt <= 0 ? $billed_amt : $allowed_amt);

      if ($get_exiting_status->insurance_paid != 0 || $get_exiting_status->insurance_paid != 0) {
      // $old_balance = isset($data['old_balance'][$i])?$data['old_balance'][$i]:($allowed_amt <=0?$allowed_amt:$allowed_amt);
      $old_balance = isset($old_balance) ? $old_balance : ($allowed_amt <= 0 ? $allowed_amt : $allowed_amt);
      }
      if ($patient_adjusted != 0) {
      $old_balance = $data['patient_adjusted_excluded'][$i];
      }
      // dd($old_balance);
      $patient_balance = $data['co_pay'][$i] + $data['deductable'][$i] + $data['co_ins'][$i];
      // dd($data)                    ;
      $adjusted_amt = $data['adjustment'][$i];
      if ($payment_type == "Adjustment")
      $allowed_amt = ($allowed_amt <= 0 && $adjusted_amt == 0 || $allowed_amt <= 0 && $payment_type != "Adjustment") ? $billed_amt : $allowed_amt;
      $insurance_balance = $old_balance - ($patient_balance + $data['with_held'][$i] + $paid_amt + $adjusted_amt);

      if ($paid_amt == 0 && $savedata['insurance_id'] != "" && isset($data['old_balance'][$i])) {
      // $insurance_balance = $old_balance; // Check next responsibility, if it was patient move the total amount to patient responsibility
      } elseif ($paid_amt == 0 && $savedata['insurance_id'] == "" && isset($data['old_balance'][$i])) {
      // $patient_balance = $old_balance;
      }

      if ($data['balance'][$i] < 0) {
      $insurance_balance = ($insurance_balance < 0) ? $insurance_balance : $data['balance'][$i]; // For excesss amount payment the balcen comes in negative  and we put teh balance in negative here
      /// dd($insurance_balance);
      // Patient paid amount refund process too getting added to insurance balance
      if($patient_paid) {
      $insurance_balance = ($data['payment_type']=="Payment")?$insurance_balance:$insurance_balance+$patient_paid;
      }
      }
      $exist_total = $get_exiting_status->adjustment + $get_exiting_status->paid_amt + $get_exiting_status->with_held; // Already subtracted adjsutment also get subtracted again so that we use this.

      if ($patient_adjusted && $old_balance <= $exist_total) {
      $insurance_balance = $insurance_balance;
      } else if ($patient_adjusted) {
      $insurance_balance = $insurance_balance - $patient_adjusted;
      }
      // if($patient_adjusted) {
      //  $insurance_balance = $insurance_balance - (($balance >0)?$patient_adjusted:0);
      //  }
      // dd($insurance_balance);
      $dos_spt_details[$i]['payment_id'] = $savedata['payment_id'];
      $dos_spt_details[$i]['claim_id'] = $data['claim_id'];
      //$dos_spt_details[$i]['insurance_id'] = $data['insurance_id'];
      $dos_spt_details[$i]['insurance_id'] = (isset($data['changed_insurance_id']) && !empty($data['changed_insurance_id'])) ? $data['changed_insurance_id'] : $data['claim_insurance_id'];
      $dos_spt_details[$i]['payment_claim_detail_id'] = $savedata['payment_claim_detail_id'];
      $dos_spt_details[$i]['claimdoscptdetail_id'] = $dos_detail_id;
      $dos_spt_details[$i]['posting_type'] = "Insurance";
      $dos_spt_details[$i]['billed_amt'] = $billed_amt;
      $dos_spt_details[$i]['allowed_amt'] = $allowed_amt;
      $dos_spt_details[$i]['paid_amt'] = $paid_amt;
      $dos_spt_details[$i]['deductable'] = $data['deductable'][$i];
      // dd($claim_patient_due)
      if ($claim_patient_due > 0 && $claim_insurance_due < 0) {
      //$dos_spt_details[$i]['patient_balance'] = $patient_balance;
      //$dos_spt_details[$i]['insurance_balance'] = $insurance_balance;
      $ins_due = $claim_insurance_due - $paid_amt;
      $dos_spt_details[$i]['patient_balance'] = $claim_patient_due;
      // $return_val['balance']  = $ins_due + $claim_patient_due;
      $dos_spt_details[$i]['insurance_balance'] = $ins_due;
      } else {
      $dos_spt_details[$i]['patient_balance'] = $patient_balance;
      $dos_spt_details[$i]['insurance_balance'] = $insurance_balance;
      }
      $total_balance = $patient_balance + $insurance_balance;

      $dos_spt_details[$i]['balance_amt'] = ($paid_amt == 0 && $total_balance == 0 && $adjustment == 0) ? $old_balance : $total_balance;
      $dos_spt_details[$i]['insurance_paid'] = $paid_amt;
      // dd($dos_spt_details);
      // Adjustment calculation starts here
      if ($payment_type == "Adjustment") {
      $dos_spt_details[$i]['patient_balance'] = 0;   // No copay and deductible was not available for adjustment
      if ($data['balance'][$i] >= 0) {
      // $dos_spt_details[$i]['balance_amt'] =  $dos_spt_details[$i]['balance_amt'] - $adjusted_amt;
      // $dos_spt_details[$i]['insurance_balance'] = $insurance_balance - $adjusted_amt;
      $dos_spt_details[$i]['balance_amt'] = $data['balance'][$i];
      $dos_spt_details[$i]['insurance_balance'] = $insurance_balance;
      } else {
      $dos_spt_details[$i]['balance_amt'] = $dos_spt_details[$i]['balance_amt'];
      $dos_spt_details[$i]['insurance_balance'] = $data['balance'][$i];
      }
      } elseif ($payment_type == "Refund") {
      $dos_spt_details[$i]['paid_amt'] = ($paid_amt < 0) ? $paid_amt : -1 * $paid_amt;
      // For refund process balance will be taken from balance that has been shown at the cpt level of display
      $dos_spt_details[$i]['insurance_paid'] = ($paid_amt < 0) ? $paid_amt : -1 * $paid_amt;
      // $dos_spt_details[$i]['insurance_balance'] = $data['balance'][$i];
      $dos_spt_details[$i]['insurance_balance'] = $data['old_balance'][$i] + $paid_amt;
      $dos_spt_details[$i]['balance_amt'] = $data['balance'][$i];
      }
      // Adjustment calculation ends here
      $dos_spt_details[$i]['co_pay'] = $data['co_pay'][$i];
      $dos_spt_details[$i]['patient_id'] = $data['patient_id'];
      $dos_spt_details[$i]['co_ins'] = $data['co_ins'][$i];
      $dos_spt_details[$i]['with_held'] = $data['with_held'][$i];
      $dos_spt_details[$i]['adjustment'] = $data['adjustment'][$i];
      $dos_spt_details[$i]['payer_insurance_id'] = $data['insurance_id'];
      // $dos_spt_details[$i]['denial_code'] = $data['denial_code'][$i];
      //$dos_spt_details[$i]['remark_code'] = (isset($data['remarkcode'][$i]) && !empty($data['remarkcode'][$i]))?trim(implode(',', $data['remarkcode'][$i]), ','):"";
      $dos_spt_details[$i]['remark_code'] = $data['remarkcode'][$i];
      $dos_spt_details[$i]['created_by'] = Auth::user()->id;
      $dos_spt_details[$i]['next_insurance_id'] = $savedata['next_insurance_id'];
      $dos_spt_details[$i]['is_active'] = isset($data['active_lineitem'][$i]) ? 1 : 0; // To make claimdos line as a active
      if ($data['status'] == "Denied")
      $dos_spt_details[$i]['description'] = "Claim Denied";
      //dd($dos_spt_details[$i])  ;
      $result = PaymentClaimCtpDetail::create($dos_spt_details[$i]);
      // Claim dos cpt details ends here
      //$this->saveTransactionhistory($savedata, $payment_id, $result->id) ;
      $this->updateClaimdoscptdetail($dos_spt_details[$i]);
      }
      }
      // exit;
      return Response::json(array('status' => 'success', 'message' => 'Refund from wallet initiated successfully.', 'data' => ''));
      }
      } */

    /* This function is used when we change the responsibility again we need to put an entry as like responsibility has changed */
    /* public function changeResponsibility($data, $save_claim_data)
      {
      $new_data['insurance_id'] = $save_claim_data['insurance_id'];
      $new_data['payer_insurance_id'] = (!empty($data['next_insurance_id']))?$data['next_insurance_id']:$save_claim_data['next_insurance_id'];
      $new_data['claim_id'] = $save_claim_data['claim_id'];
      $new_data['patient_id'] = $save_claim_data['patient_id'];
      $new_data['payment_id'] = $save_claim_data['payment_id'];
      $new_data['payment_type'] = "Insurance";
      $patient_adjustment  = $save_claim_data['patient_adjustment'];
      //$patient_paid  = $save_claim_data['patient_paid'] + $patient_adjustment;// This making problem when we change responsibility from insurance to patient after adjustment
      $balance_value = $save_claim_data['balance_amt'];
      //dd($save_claim_data)  ;
      $patient_paid  = $save_claim_data['patient_paid'] ;// Add adjsutment to reduce balance from patient payment it it was adjusted already
      $remaining = $patient_paid - $save_claim_data['balance_amt'];
      $patient_claim_due = 0;
      // dd($data);

      if($save_claim_data['next_insurance_id'] != "patient")
      {
      $new_data['insurance_due'] = $save_claim_data['balance_amt'];  // To avoid
      $save_claim_data['insurance_due'] = $save_claim_data['balance_amt'];
      //dd($new_data)  ;
      // Check next responsibility, if it was patient move the total amount to patient responsibility
      if($save_claim_data['patient_due'] != 0) {
      $patient_new_balance = ($patient_paid >0)?$patient_paid - $save_claim_data['patient_due']:$save_claim_data['patient_due'];
      $patient_actual_paid = ($patient_paid>0?(($patient_new_balance>0)?$save_claim_data['patient_due']:$patient_paid):0);
      $patient_claim_due = ($patient_paid>0?(($patient_new_balance>0)?0:abs($patient_new_balance)):$save_claim_data['patient_due']);
      $new_data['insurance_due'] = (($patient_paid>0 && $save_claim_data['insurance_due'] >=0)?($save_claim_data['balance_amt'] - $patient_actual_paid):$save_claim_data['insurance_due']);

      }
      //dd($new_data['insurance_due']);
      //$new_data['patient_due'] = 0;
      $new_data['patient_due'] = $patient_claim_due;  // Hided because got issue when keep patient balance and insurance balance
      if($new_data['insurance_due'] >=0 && $patient_claim_due >0){
      $new_data['insurance_due'] = $new_data['insurance_due'];
      $new_data['patient_due'] = 0;
      } elseif($new_data['insurance_due'] <0 && $patient_claim_due >0){
      $new_data['insurance_due'] = $new_data['insurance_due'] +$patient_claim_due;
      $new_data['patient_due'] = 0;
      }
      $balance_value = $new_data['patient_due']+$new_data['insurance_due'];
      // dd($new_data);
      $responsibility = "Insurance";
      $new_data['insurance_category'] = $save_claim_data['next_insurance_category'];
      // dd($new_data);
      }
      else
      {
      $claim_data = Claims::where('id', $data['claim_id'])->pluck('total_charge');

      if($patient_paid > 0 || $patient_adjustment>0)
      {

      if($save_claim_data['insurance_paid_amt'] == 0)
      { // When we do adjustment default value was zero it making the balance to get wrong after changing repsonsibility
      //dd($save_claim_data)                ;
      $balance_value = Claims::where('id', $data['claim_id'])->pluck('balance_amt');
      // $balance_value = $balance_value -
      //if($data['payment_type'] == 'Adjustment'){
      $balance_value = $balance_value - $save_claim_data['total_adjusted'];
      $balance_value = ($balance_value <0)?0:$balance_value;
      // }
      // dd($balance_value);

      } else{

      $balance_value =$balance_value;
      if($data['payment_type'] != "Refund"){ // For refund the patient paid amount caculated and refunded always
      $patient_paid  = $save_claim_data['patient_paid'] + $patient_adjustment;
      } else{
      $patient_paid  = 0;
      }

      // dd($patient_paid)  ;
      $remaining = $patient_paid - $balance_value;  // For zero payment traasaction after adjustment the value was getting deducted when we change the responsibility as patient

      $balance_value = ($remaining >= 0)?0:abs($remaining);
      // dd($balance_value);


      }
      //dd($balance_value)                                         ;
      $new_data['patient_due'] = $balance_value;
      } else{

      $new_data['patient_due'] = ($save_claim_data['patient_due'] == 0 && $balance_value!=0 && $save_claim_data['insurance_due'] <0)?0:(($save_claim_data['insurance_due'] <0 && $save_claim_data['patient_due'] >0)?$save_claim_data['patient_due']:$balance_value);
      // dd($new_data['patient_due']);
      }

      $new_data['insurance_due'] = ($save_claim_data['insurance_due']<0)?$save_claim_data['insurance_due']:0; //when excess payment done
      $responsibility = "Patient";
      }
      //dd($new_data);
      $new_data['balance_amt'] = ($remaining >= 0)?0:$balance_value;
      $new_data['transaction_type'] ="responsibility";
      $new_data['description']  = "Responsibility changed to ".$responsibility;
      $save_claim_data['posting_date'] =  (isset($data['posting_date']) && $data['posting_date'] !== '')?date("Y-m-d",strtotime($data['posting_date'])):date("Y-m-d");
      //print_r($new_data);
      //dd($new_data);
      $result = PaymentClaimDetail::create($new_data); // Save payment data as in claim level
      $new_data['payment_claim_detail_id'] = $result->id;
      if(!empty($data))
      {
      $dos_spt_details = [];
      $patient_claim_paid_amt = $save_claim_data['patient_paid'];

      $patient_claim_amt_adjusted = $save_claim_data['patient_adjustment'];
      for($i=0;$i<count($data['cpt']);$i++)
      {

      // echo $patient_claim_paid_amt;
      // echo "</br>";
      if(!empty($data['cpt'][$i]))
      {
      $dos_detail_id = Helpers::getEncodeAndDecodeOfId($data['ids'][$i],'decode');
      $get_exiting_status = Claimdoscptdetail::where('id', $dos_detail_id)->first();
      $get_exiting_status_count = PaymentClaimCtpDetail::where('claimdoscptdetail_id', $dos_detail_id)->where('posting_type', 'Insurance')->count();
      //dd($get_exiting_status_count);
      $allowed_amt = $data['cpt_allowed_amt'][$i];
      $billed_amt = $data['cpt_billed_amt'][$i];
      $adjusted_amt = $data['adjustment'][$i];

      $old_balance = isset($data['old_balance'][$i])?$data['old_balance'][$i]:($allowed_amt <=0?$billed_amt:$allowed_amt);
      if($get_exiting_status_count >1) {
      // $old_balance = isset($data['old_balance'][$i])?$data['old_balance'][$i]:($allowed_amt <=0?$allowed_amt:$allowed_amt);  //med-1890

      $old_balance = isset($data['balance_original'][$i])?$data['balance_original'][$i]:($allowed_amt <=0?$allowed_amt:$allowed_amt);
      }
      //dd($data)                    ;
      $paid_amt = ($data['payment_type']=="Refund")?-1*$data['paid_amt'][$i]:$data['paid_amt'][$i];
      $patient_paid = $patient_claim_paid_amt;
      $patient_adjusted = $patient_claim_amt_adjusted;
      $patient_claim_paid = 0;
      $patient_claim_adjusted = 0;
      $patient_balance = $data['co_pay'][$i]+$data['deductable'][$i]+$data['co_ins'][$i];
      $insurance_balance =  $old_balance- ($patient_balance+$data['with_held'][$i]+$paid_amt+$adjusted_amt);
      //dd($insurance_balance);
      if(($insurance_balance>0 || $patient_balance >0)&& ($patient_paid>0|| $patient_adjusted>0)){
      // dd($patient_balance);
      if($patient_balance != 0 && $insurance_balance ==0) {
      $patient_actual_paid = $patient_paid - $patient_balance;
      $patient_actual_paid = ($patient_actual_paid<0)?$patient_paid:$patient_balance;
      } else if($insurance_balance != 0 && $patient_balance == 0) {
      $patient_actual_paid = $patient_paid - $insurance_balance;
      $patient_actual_paid = ($patient_actual_paid<0)?$patient_paid:$insurance_balance;
      } else if($patient_balance != 0 && $patient_balance != 0) {
      $tot_balance = $patient_balance+$insurance_balance;
      $patient_actual_paid = $patient_paid - ($tot_balance);
      $patient_actual_paid = ($patient_actual_paid<0)?$patient_paid:$tot_balance;
      }

      //$patient_actual_paid = ($patient_actual_paid>0?$patient_balance:$patient_paid);
      //$patient_actual_paid = ($patient_actual_paid<0?$patient_paid:($patient_balance==0?$patient_paid:$patient_balance));
      // dd($patient_actual_paid);
      // $patient_claim_paid  = ($patient_actual_paid>0)?$patient_balance:$patient_paid;
      $patient_claim_paid = $patient_actual_paid;
      $patient_balance = ($patient_actual_paid>0 && $patient_balance >0)?($patient_balance-$patient_actual_paid):(($patient_balance == 0)?$patient_balance:abs($patient_actual_paid));
      //dd($patient_balance);


      if($patient_adjusted >0){
      // dd($patient_adjusted);
      $patient_balance = ($patient_balance>0)?$patient_balance -$patient_adjusted:0;
      //dd($patient_balance);
      $patient_claim_adjusted  = ($patient_balance>0)?$patient_balance:$patient_adjusted;
      $patient_claim_paid = $patient_claim_paid+$patient_claim_adjusted;
      //dd($patient_claim_adjusted);
      }

      // $insurance_balance = ($insurance_balance>0 && $patient_balance ==0 && $save_claim_data['next_insurance_id'] == "patient")?$insurance_balance - $patient_claim_paid:$insurance_balance;
      //  dd($patient_claim_paid)             ;
      // dd($patient_claim_paid);
      $ins_bal =   $insurance_balance - $patient_claim_paid;
      // $ins_bal =   $insurance_balance ;
      $insurance_balance = ($insurance_balance>0 && $patient_balance ==0 && $save_claim_data['next_insurance_id'] == "patient")?($ins_bal>0?$ins_bal:0):$insurance_balance;
      }
      //dd($ins_bal)  ;
      if($patient_adjusted) {
      $insurance_balance = $insurance_balance - $patient_adjusted;
      }
      $total_balance =  $patient_balance+$insurance_balance;

      // dd($insurance_balance);
      if($data['payment_type'] == "Refund")
      {
      // $total_balance = $data['balance'][$i];
      $dos_spt_details[$i]['insurance_balance'] = $data['balance_original'][$i] -$paid_amt;
      } elseif($data['payment_type'] == "Adjustment")
      {
      // $total_balance = ($patient_paid>0 && $patient_paid>=$total_balance)?0:$total_balance-$patient_paid;
      $total_balance = ($patient_paid>0 && $patient_paid>=$total_balance)?0:$total_balance;
      }
      $dos_spt_details[$i]['payment_id'] = $new_data['payment_id'];
      $dos_spt_details[$i]['claim_id'] = $new_data['claim_id'];
      $dos_spt_details[$i]['insurance_id'] = $new_data['insurance_id'];
      $dos_spt_details[$i]['payer_insurance_id'] = $new_data['payer_insurance_id'];
      $dos_spt_details[$i]['payment_claim_detail_id'] = $new_data['payment_claim_detail_id'];
      $dos_spt_details[$i]['claimdoscptdetail_id'] = $dos_detail_id;
      $dos_spt_details[$i]['posting_type'] = "Insurance";
      $dos_spt_details[$i]['patient_id'] = $save_claim_data['patient_id'];

      if($save_claim_data['next_insurance_id'] != "patient")
      {
      // Check next responsibility, if it was patient move the total amount to patient responsibility
      $dos_spt_details[$i]['insurance_balance'] = ($insurance_balance<=0)?$total_balance:$total_balance;
      // $dos_spt_details[$i]['patient_balance'] = ($patient_balance> 0 && $insurance_balance<=0)?$patient_balance:0; because if insurance balance 0 and only patient balance both will have the same amount
      // $dos_spt_details[$i]['patient_balance'] = ($patient_balance> 0 && $insurance_balance<0)?$patient_balance:0;
      $dos_spt_details[$i]['patient_balance']  = 0;
      $responsibility = "Insurance";
      // dd($dos_spt_details);
      } else
      {
      //dd($insurance_balance);
      $dos_spt_details[$i]['patient_balance'] = ($patient_balance== 0 && $insurance_balance>0 || $patient_balance > 0 && $insurance_balance>0)?$total_balance:$patient_balance;
      $dos_spt_details[$i]['insurance_balance'] = ($insurance_balance<0 && $paid_amt !=0)?$insurance_balance:0; //
      $responsibility = "Patient";
      }
      $dos_spt_details[$i]['transaction_type'] =  "responsibility";
      $dos_spt_details[$i]['description']  = "Responsibility changed to ".$responsibility;
      $dos_spt_details[$i]['balance_amt'] = $total_balance;
      $dos_spt_details[$i]['created_by'] = Auth::user()->id;
      //echo "<pre>";print_r($dos_spt_details) ;
      $result = PaymentClaimCtpDetail::create($dos_spt_details[$i]);   // Save payment data in cpt level
      }
      $patient_claim_paid_amt = $patient_claim_paid_amt-  $patient_claim_paid;
      $patient_claim_amt_adjusted = $patient_claim_amt_adjusted-  $patient_claim_adjusted;
      }

      }
      // exit;
      return $new_data;
      } */
    /* This function is used when we change the responsibility again we need to put an entry as like responsibility has changed */

    /* This function is used when we change the responsibility again we need to put an entry as like responsibility has changed */

    /* public function changeResponsibility($data, $save_claim_data) {
      $new_data['insurance_id'] = $save_claim_data['insurance_id'];
      $new_data['payer_insurance_id'] = (!empty($data['next_insurance_id'])) ? $data['next_insurance_id'] : $save_claim_data['next_insurance_id'];
      $new_data['claim_id'] = $save_claim_data['claim_id'];
      $new_data['patient_id'] = $save_claim_data['patient_id'];
      $new_data['payment_id'] = $save_claim_data['payment_id'];
      $new_data['payment_type'] = "Insurance";
      $patient_adjustment = $save_claim_data['patient_adjustment'];
      // This making problem when we change responsibility from insurance to patient after adjustment
      $balance_value = $save_claim_data['balance_amt'];
      $patient_paid = $save_claim_data['patient_paid']; // Add adjsutment to reduce balance from patient payment it it was adjusted already

      $remaining = $patient_paid - $save_claim_data['balance_amt'];
      // $remaining = $patient_paid;
      $patient_claim_due = 0;
      if ($save_claim_data['next_insurance_id'] != "patient") {
      $new_data['insurance_due'] = $save_claim_data['balance_amt'];  // To avoid
      $save_claim_data['insurance_due'] = $save_claim_data['balance_amt'];
      $responsibility = "Insurance";
      $new_data['insurance_category'] = $save_claim_data['next_insurance_category'];
      $new_data['patient_due'] = 0;
      } else {
      $claim_data = ClaimInfoV1::where('id', $data['claim_id'])->pluck('total_charge');

      if ($patient_paid > 0 || $patient_adjustment > 0) {

      if ($save_claim_data['insurance_paid_amt'] == 0) {
      $balance_value = ClaimInfoV1::where('id', $data['claim_id'])->pluck('balance_amt');

      $balance_value = $balance_value - $save_claim_data['total_adjusted'];
      $balance_value = ($balance_value < 0) ? 0 : $balance_value;
      } else {
      $balance_value = $balance_value;
      if ($data['payment_type'] != "Refund") { // For refund the patient paid amount caculated and refunded always
      $patient_paid = $patient_adjustment;
      $remaining = $patient_paid - $balance_value;  // For zero payment traasaction after adjustment the value was getting deducted when we change the responsibility as patient
      //dd($remaining);
      $balance_value = ($remaining <= 0) ? 0 : abs($remaining);
      } else {
      $patient_paid = 0;
      }
      }
      $new_data['patient_due'] = $balance_value;
      } else {

      $new_data['patient_due'] = ($save_claim_data['patient_due'] == 0 && $balance_value != 0 && $save_claim_data['insurance_due'] < 0) ? 0 : (($save_claim_data['insurance_due'] < 0 && $save_claim_data['patient_due'] > 0) ? $save_claim_data['patient_due'] : $balance_value);
      }

      $new_data['insurance_due'] = ($save_claim_data['insurance_due'] < 0) ? $save_claim_data['insurance_due'] : 0; //when excess payment done
      $responsibility = "Patient";
      }
      $new_data['balance_amt'] = ($remaining >= 0 && $data['payment_type'] != "Refund") ? 0 : $balance_value;
      $new_data['transaction_type'] = "responsibility";
      $new_data['description'] = "Responsibility changed to " . $responsibility;
      $save_claim_data['posting_date'] = (isset($data['posting_date']) && $data['posting_date'] !== '') ? date("Y-m-d", strtotime($data['posting_date'])) : date("Y-m-d");
      //dd($new_data);
      $result = PaymentClaimDetail::create($new_data); // Save payment data as in claim level
      $payment_claim_detail_id = $new_data['payment_claim_detail_id'] = $result->id;
      if (!empty($data)) {
      $dos_spt_details = [];
      for ($i = 0; $i < count($data['cpt']); $i++) {
      if (!empty($data['cpt'][$i])) {
      $dos_detail_id = Helpers::getEncodeAndDecodeOfId($data['ids'][$i], 'decode');
      $paid_data = PaymentClaimCtpDetail::with(['dosdetails' => function($query) {
      $query->select('patient_paid', 'insurance_paid', 'insurance_paid', 'insurance_adjusted', 'claim_id', 'id');
      }])->where('claimdoscptdetail_id', $dos_detail_id)->orderBy('id', 'desc')->first();
      // echo "<pre>";                    print_r($paid_data);
      $insurance_balance = $paid_data->insurance_balance;
      $patient_balance = $paid_data->patient_balance;

      $balance_amt = $paid_data->balance_amt;
      $patient_paid = $paid_data->dosdetails->patient_paid;
      $insurance_paid_amt = $data['paid_amt'][$i];
      $insurance_adjusted = $data['adjustment'][$i];
      //dd($data);
      $dos_spt_details[$i]['payment_id'] = $new_data['payment_id'];
      $dos_spt_details[$i]['claim_id'] = $new_data['claim_id'];
      $dos_spt_details[$i]['insurance_id'] = $new_data['insurance_id'];
      $dos_spt_details[$i]['payer_insurance_id'] = $new_data['payer_insurance_id'];
      $dos_spt_details[$i]['payment_claim_detail_id'] = $new_data['payment_claim_detail_id'];
      $dos_spt_details[$i]['claimdoscptdetail_id'] = $dos_detail_id;
      $dos_spt_details[$i]['posting_type'] = "Insurance";
      $dos_spt_details[$i]['patient_id'] = $save_claim_data['patient_id'];
      //echo "sdfsd".$balance_amt;

      if ($save_claim_data['next_insurance_id'] != "patient") {
      // Check next responsibility, if it was patient move the total amount to patient responsibility
      $dos_spt_details[$i]['insurance_balance'] = $balance_amt;
      $dos_spt_details[$i]['patient_balance'] = ($insurance_balance >= 0) ? 0 : $patient_balance;
      $responsibility = "Insurance";
      $move_amt = $patient_paid;
      } else {
      if ($data['payment_type'] == "Refund") {
      $dos_spt_details[$i]['patient_balance'] = ($balance_amt > 0) ? $balance_amt : 0;
      $move_amt = 0;
      } else {
      if ($insurance_paid_amt == 0) {
      $balance_value = Claimdoscptdetail::where('id', $dos_detail_id)->pluck('balance');
      $balance_value = $balance_value - $insurance_adjusted;
      $balance_value = ($balance_value < 0) ? 0 : $balance_value;
      } else {
      $remaining = $patient_paid - $balance_amt;
      // dd($patient_paid)                              ;
      $balance_value = ($remaining >= 0) ? 0 : $balance_amt - $patient_paid;
      $move_amt = $remaining;
      }
      $dos_spt_details[$i]['patient_balance'] = $balance_value;
      }
      $dos_spt_details[$i]['insurance_balance'] = ($insurance_balance < 0) ? $insurance_balance : 0;
      $responsibility = "Patient";
      }
      $dos_spt_details[$i]['transaction_type'] = "responsibility";
      $dos_spt_details[$i]['description'] = "Responsibility changed to " . $responsibility;
      $dos_spt_details[$i]['balance_amt'] = $balance_amt;
      $dos_spt_details[$i]['created_by'] = Auth::user()->id;
      //echo "<pre>";print_r($dos_spt_details[$i]);
      $this->updatePatientandInsuranceBalanceOnCPT($dos_spt_details[$i]);
      $result = PaymentClaimCtpDetail::create($dos_spt_details[$i]);   // Save payment data in cpt level
      $balance_data[$dos_detail_id] = ($patient_paid > 0 && $move_amt > 0) ? $this->updatePatientPaidOnPaymentcptdetails($move_amt, $dos_detail_id, $dos_spt_details[$i]) : "";
      }
      }
      $value = array_filter($balance_data);
      // dd($value);
      //dd($new_data['balance_amt']);
      // dd($value);
      if (!empty($value)) // if($new_data['balance_amt'] ==0 && !empty($value))
      $new_data = $this->movePaymentamounttoWallet($new_data, $value, "response");
      //dd($new_data);

      $get_data = $this->getpatientInsuranceBalanceByCPT($new_data['claim_id'], $new_data['payer_insurance_id']);
      $new_data['patient_due'] = $get_data['patient_balance'];
      $new_data['insurance_due'] = $get_data['insurance_balance'];
      PaymentClaimDetail::where('id', $payment_claim_detail_id)->update(['patient_due' => $get_data['patient_balance'], 'insurance_due' => $get_data['insurance_balance']]);
      }
      // exit;
      return $new_data;
      }

      // This function is used when we change the responsibility again we need to put an entry as like responsibility has changed

      public function updatePatientandInsuranceBalanceOnCPT($cpt_lineitem) {
      Claimdoscptdetail::where('id', $cpt_lineitem['claimdoscptdetail_id'])->update(['patient_balance' => $cpt_lineitem['patient_balance'], 'insurance_balance' => $cpt_lineitem['insurance_balance']]);
      }

      public function updateClaimdoscptdetail($cpt_line_itemdata) {
      $dos_spt_details['co_ins'] = $cpt_line_itemdata['co_ins'];
      $dos_spt_details['co_pay'] = $cpt_line_itemdata['co_pay'];
      $dos_spt_details['deductable'] = $cpt_line_itemdata['deductable'];
      $dos_spt_details['cpt_allowed_amt'] = $cpt_line_itemdata['allowed_amt'];
      $dos_spt_details['with_held'] = !(empty($cpt_line_itemdata['with_held'])) ? DB::raw("with_held +" . $cpt_line_itemdata['with_held']) : DB::raw("with_held +0");
      $dos_spt_details['adjustment'] = !(empty($cpt_line_itemdata['adjustment'])) ? DB::raw("adjustment +" . $cpt_line_itemdata['adjustment']) : DB::raw("adjustment +0");
      $dos_spt_details['paid_amt'] = !(empty($cpt_line_itemdata['paid_amt'])) ? DB::raw("paid_amt +" . $cpt_line_itemdata['paid_amt']) : DB::raw("paid_amt +0");
      $dos_spt_details['insurance_paid'] = !(empty($cpt_line_itemdata['insurance_paid'])) ? DB::raw("insurance_paid +" . $cpt_line_itemdata['insurance_paid']) : DB::raw("insurance_paid +0");
      //dd($cpt_line_itemdata)    ;
      if ($cpt_line_itemdata['balance_amt'] == 0 && $cpt_line_itemdata['patient_balance'] != 0 && $cpt_line_itemdata['insurance_balance'] != 0) {
      $dos_spt_details['patient_balance'] = $cpt_line_itemdata['patient_balance'];       // Applied when there is no
      $dos_spt_details['insurance_balance'] = $cpt_line_itemdata['insurance_balance'];
      } elseif (!empty($cpt_line_itemdata['next_insurance_id']) && $cpt_line_itemdata['next_insurance_id'] != "patient") { // Applied when nextresposibility has choosen
      $dos_spt_details['patient_balance'] = 0;
      if ($cpt_line_itemdata['balance_amt'] == 0 && $cpt_line_itemdata['insurance_balance'] > 0) {
      $dos_spt_details['insurance_balance'] = $cpt_line_itemdata['insurance_balance']; // keep the insurance balance as in insurance balacne when we apply patient payment
      } else {
      $dos_spt_details['insurance_balance'] = $cpt_line_itemdata['balance_amt'];
      }
      } elseif ($cpt_line_itemdata['next_insurance_id'] == "patient" && $cpt_line_itemdata['balance_amt'] != 0) {
      //$cpt_line_itemdata['patient_balance']==0 conditions removed because when patient balance = 40 and insbal =-50 becomes error
      $dos_spt_details['patient_balance'] = ($cpt_line_itemdata['patient_balance'] >= 0 && $cpt_line_itemdata['balance_amt'] < 0) ? $cpt_line_itemdata['patient_balance'] : $cpt_line_itemdata['balance_amt'];
      //dd($dos_spt_details['patient_balance']);
      $dos_spt_details['insurance_balance'] = ($cpt_line_itemdata['patient_balance'] >= 0 && $cpt_line_itemdata['insurance_balance'] < 0) ? $cpt_line_itemdata['insurance_balance'] : 0;
      } else {
      $dos_spt_details['patient_balance'] = $cpt_line_itemdata['patient_balance'];       // Applied when there is no nextresposibility has choosen
      $dos_spt_details['insurance_balance'] = $cpt_line_itemdata['insurance_balance'];
      }

      $dos_spt_details['is_active'] = $cpt_line_itemdata['is_active'];
      $dos_spt_details['denial_code'] = isset($cpt_line_itemdata['remark_code']) ? $cpt_line_itemdata['remark_code'] : "";
      $paid_amt_cpt = $cpt_line_itemdata['adjustment'] + $cpt_line_itemdata['paid_amt'] + $cpt_line_itemdata['with_held'];
      //dd($paid_amt_cpt);
      //dd($dos_spt_details);
      $dos_spt_details['balance'] = $cpt_line_itemdata['balance_amt'];
      $dos_spt_details['updated_by'] = Auth::user()->id;
      $dos_spt_details['status'] = "Paid";

      if ($dos_spt_details['balance'] > 0) {
      $dos_spt_details['status'] = ($cpt_line_itemdata['next_insurance_id'] == "patient") ? "Patient" : "Pending";
      }
      //dd($dos_spt_details);
      // Allowed amount update starts here
      $claimdos_data = Claimdoscptdetail::where('id', $cpt_line_itemdata['claimdoscptdetail_id'])->select('id', 'charge', 'adjustment')->first();
      $allowed_amt = $claimdos_data->adjustment + $cpt_line_itemdata['adjustment'];
      $billed_amt = $claimdos_data->charge;
      $dos_spt_details['cpt_allowed_amt'] = $billed_amt - $allowed_amt;
      $dos_spt_details['balance'] = DB::raw("balance -" . $paid_amt_cpt);
      //dd($dos_spt_details);
      Claimdoscptdetail::where('id', $cpt_line_itemdata['claimdoscptdetail_id'])->update($dos_spt_details);
      }

      public function updateclaims($save_data) {
      $claim = ClaimInfoV1::where('id', $save_data['claim_id']);
      $balance_dedection = $save_data['total_adjusted'] + $save_data['insurance_paid_amt'] + $save_data['total_withheld'];
      $balance_amt = $save_data['balance_amt'];
      // dd($balance_dedection);
      $save_claim['total_allowed'] = $save_data['total_allowed'];
      $save_claim['total_paid'] = !(empty($save_data['insurance_paid_amt'])) ? DB::raw("total_paid +" . $save_data['insurance_paid_amt']) : DB::raw("total_paid +0");
      $save_claim['total_adjusted'] = !(empty($save_data['total_adjusted'])) ? DB::raw("total_adjusted +" . $save_data['total_adjusted']) : DB::raw("total_adjusted +0");
      $save_claim['insurance_paid'] = !(empty($save_data['insurance_paid_amt'])) ? DB::raw("insurance_paid +" . $save_data['insurance_paid_amt']) : DB::raw("insurance_paid +0");
      $save_claim['total_withheld'] = !(empty($save_data['total_withheld'])) ? DB::raw("total_withheld +" . $save_data['total_withheld']) : DB::raw("total_withheld +0");
      $save_claim['balance_amt'] = !(empty($balance_dedection)) ? DB::raw("balance_amt -" . $balance_dedection) : DB::raw("balance_amt -0");
      //dd($save_claim['balance_amt']);
      $save_claim['insurance_due'] = $save_data['insurance_due'];  // Applied when there is no nextresposibility has choosen
      $save_claim['patient_due'] = $save_data['patient_due'];
      $next_insurance_category = @$save_data['next_insurance_category'];
      $save_claim['is_send_paid_amount'] = @$save_data['is_send_paid_amount'];
      $save_claim['payment_hold_reason'] = isset($save_data['payment_hold_reason']) ? $save_data['payment_hold_reason'] : "";
      //dd($balance_dedection);
      $claim_data = $claim->select('total_charge', 'total_adjusted', 'insurance_category', 'balance_amt', 'patient_id', 'status', 'self_pay')->first();
      // Updating total charge and total adjusted calculation starts here
      $total_charge = $claim_data->total_charge;
      $total_adjusted = $claim_data->total_adjusted + $save_data['total_adjusted'];
      $total_allowed = $total_charge - $total_adjusted;
      $total_balance = $claim_data->balance_amt;
      $save_claim['total_allowed'] = $total_allowed;
      $save_claim['insurance_category'] = (isset($next_insurance_category) && !empty($next_insurance_category)) ? $next_insurance_category : $claim_data->insurance_category;
      if (!empty($save_data['next_insurance_id']) && $save_data['next_insurance_id'] != "patient") {
      $save_claim['insurance_id'] = $save_data['next_insurance_id'];
      $save_claim['self_pay'] = "No";
      } elseif ($save_data['next_insurance_id'] == "patient") {
      $save_claim['insurance_id'] = 0;
      $save_claim['self_pay'] = "Yes";
      $save_claim['insurance_category'] = "";
      }
      $save_claim['status'] = "Paid";
      $balance = $total_balance - (($balance_dedection > 0) ? $balance_dedection : 0); // Find status
      $balance = ($balance_amt > 0) ? $balance_amt : 0;
      if ($balance > 0) {
      $save_claim['status'] = "Pending";
      if (!empty($save_data['next_insurance_id']) && $save_data['next_insurance_id'] != "patient") {
      $save_claim['status'] = "Ready";
      } elseif (!empty($save_data['next_insurance_id']) && $save_data['next_insurance_id'] == "patient") {
      $save_claim['status'] = "Patient";
      }
      } else {
      $save_claim['status'] = (!empty($save_data['next_insurance_id']) && $save_data['next_insurance_id'] != "patient") ? "Ready" : $save_claim['status'];
      }
      $save_claim['status'] = !empty($save_data['status']) ? $save_data['status'] : $save_claim['status'];
      if ($save_claim['payment_hold_reason'] == "insurance") {
      $save_claim['status'] = "Pending";
      }

      // Updating total charge and total adjusted calculation starts here
      $balace = $save_data['claim_balance'];
      // dd($save_claim);
      $claim->update($save_claim);
      $claim_data = $claim->first();
      $current_balance_amt = $claim_data->balance_amt;
      $patient_balance = $claim_data->patient_due;
      $self_pay = $claim_data->self_pay;
      if ($current_balance_amt > 0 && $self_pay == "Yes") {
      $claim->update(['status' => 'Patient']);
      } else if ($current_balance_amt < 0 && $self_pay == "Yes") {
      $claim->update(['status' => 'Paid']);
      } else if ($patient_balance > 0 && $current_balance_amt < 0) {
      $claim->update(['status' => 'Patient']);
      }
      if (abs($claim_data->balance_amt) == abs($balace)) {
      // $claim->update($save_claim);
      } else {
      // DB::rollBack();
      //return Response::json(array('status' => 'error', 'message' =>  "Your balance amount mismatched", 'data' => $claim_data->patient_id));
      }
      if ($save_data['remaining_amt'] > 0 && empty($save_data['next_insurance_id'])) {
      //dd($save_data['remaining_amt']);
      $this->moveAmountToWallet($save_data['patient_id'], $save_data['claim_id'], abs($save_data['remaining_amt']), $save_data['next_insurance_id']);
      }
      //$result = Claims::find($save_data['claim_id']);
      //dd($result) ;
      //$return_val = $this->generatecmsformpayment($save_data['claim_id'], $result);
      }

      public function updatewalletinfo($save_data, $type) {
      $amt_used = $save_data['insurance_paid_amt'];
      $payment_id = $save_data['payment_id'];
      $balace = $save_data['claim_balance'];
      $claim_data = ClaimInfoV1::where('id', $save_data['claim_id'])->select('total_charge', 'total_adjusted', 'insurance_category', 'balance_amt')->first();
      if ($type == "Refund") {
      PMTInfoV1::where('id', $payment_id)->update(['amt_used' => DB::raw("amt_used -" . $amt_used), 'balance' => DB::raw("balance +" . $amt_used)]);
      } else {
      PMTInfoV1::where('id', $payment_id)->update(['amt_used' => DB::raw("amt_used +" . $amt_used), 'balance' => DB::raw("balance -" . $amt_used)]);
      }
      DB::commit();
      }

      public function generatecmsformpayment($claim_id, $claim_data) {
      $billing = new BillingApiController();
      if (!empty($claim_data->document_path) && !empty($claim_data->cmsform)) {
      $billing->deleteExistingdocument($claim_id);
      }
      $billing->generatecms1500($claim_id, 'frompayment');
      return true;
      }
     */
    /* public function saveTransactionhistory($transactiondata, $payment_id, $payment_cpt_detail_id = null) {
      $posting_type = $transactiondata['posting_type'];
      $trans_data['claim_id'] = $transactiondata['claim_id'];
      $trans_data['patient_id'] = $transactiondata['patient_id'];
      $trans_data['payment_id'] = $payment_id;
      $trans_data['posting_type'] = $transactiondata['posting_type'];
      $trans_data['type'] = isset($transactiondata['type']) ? $transactiondata['type'] : 'posting';
      $trans_data['type_id'] = $payment_id;
      $trans_data['paymentcpt_detail_id'] = $payment_cpt_detail_id;
      $trans_data['description'] = isset($transactiondata['description']) ? $transactiondata['description'] : 'testing';
      $trans_data['created_by'] = Auth::user()->id;
      PaymentTransactionHistory::create($trans_data);
      } */

    public function getremarkcodeApi($id) {
        $arr_val = explode(',', $id);
        $remark_codes = array_filter($arr_val);
        $code = [];
        $dataArr = ['CO', 'PR', 'OA', 'PI'];
        foreach ($remark_codes as $remark_code) {
            $str2 = substr(trim($remark_code), 0, 2);
            if (in_array($str2, $dataArr)) {
                $str2 = $str2;
                $code_value = substr(trim($remark_code), 2);
                $code_val = Code::where('status', 'Active')->where('transactioncode_id', $code_value)->pluck('description')->first();
                if (!empty($code_val)) {
                    $code[] = '<span class="med-orange font600">' . $str2 . '-' . $code_value . ':</span> ' . $code_val;
                }
            } else {
                $str2 = trim($remark_code);
                $code_value = substr(trim($remark_code), 2);
                $code_val = Code::where('status', 'Active')->where('transactioncode_id', $str2)->pluck('description')->first();
                if (!empty($code_val)) {
                    $code[] = '<span class="med-orange font600">' . $str2 . ':</span> ' . $code_val;
                }
            }
        }
        return json_encode(compact('code'));
    }

    // Refund amount to patient if all the amount paid by insurance starts here
    /* public function moveAmountToWallet($patient_id, $claim_id, $paid_amt, $next_response = null) {
      $data['patient_id'] = $patient_id;
      $save_claim['claim_id'] = $claim_id;
      $save_claim['patient_id'] = $patient_id;
      $save_claim['created_by'] = Auth::user()->id;
      $save_claim['payment_type'] = "Addwallet";
      //dd($save_claim)       ;
      $this->getPatientPaidAmountForClaim($save_claim, $claim_id, $paid_amt, $next_response);
      // dd($paid_amt);
      DB::table('claimdoscptdetails')->where('claim_id', $claim_id)->where('cpt_code', 'Patient')->update(['patient_paid' => DB::raw('patient_paid - ' . $paid_amt)]); // Update CPT line item tables
      DB::table('claims')->where('id', $claim_id)->update(['total_paid' => DB::raw("total_paid -" . $paid_amt), 'balance_amt' => DB::raw("balance_amt +" . $paid_amt),
      'patient_paid' => DB::raw("patient_paid -" . $paid_amt)]); // Update claims table
      //echo "comes here";
      }
     */
    // Refund amount to patient if all the amount paid by insurance ends here  
    // This is used to get the lists of payments that have been done by patients for the claim
    /* public function getPatientPaidAmountForClaim($savedata, $claim_id, $refunded_amount, $next_response = null) {
      $patient_paid_details = PaymentClaimDetail::with('paymentcptdetailclaim')
      ->where('claim_id', $claim_id)->where('payment_type', 'Patient')
      ->where('patient_paid_amt', '>', 0)->get();
      //dd($patient_paid_details);
      $remaining_amount = $refunded_amount;
      foreach ($patient_paid_details as $patient_paid_detail) {
      $patient_paid = $patient_paid_detail->patient_paid_amt;
      if ($remaining_amount > $patient_paid) {
      $remaining_amount = $remaining_amount - $patient_paid;
      $payment_amount = $patient_paid;
      $payment_id = $patient_paid_detail->payment_id;
      $this->saveRefundedClaimData($savedata, $payment_amount, $payment_id, $patient_paid_detail, $next_response);
      } elseif ($remaining_amount <= $patient_paid) {
      $payment_amount = $remaining_amount;
      $payment_id = $patient_paid_detail->payment_id;
      $this->saveRefundedClaimData($savedata, $payment_amount, $payment_id, $patient_paid_detail, $next_response);
      break;
      }
      }
      } */

    /* public function updatePatientPaidOnPaymentcptdetails($paid_amt, $dos_id, $paid_info, $res = null) {
      $paidinformation = PaymentClaimCtpDetail::where('claimdoscptdetail_id', $dos_id)->where('posting_type', 'Patient')->where('patient_paid', '!=', 0)->whereNotIn('transaction_type', ['responsibility'])->select('claim_id', 'patient_id', 'payment_id', 'claimdoscptdetail_id', DB::raw('sum(patient_paid) as patient_paid'))->groupBy('payment_id')->get();
      $remaining_amount = $paid_amt;
      $claim_paid = [];
      // dd($paid_info)          ;
      foreach ($paidinformation as $key => $paidinformation) {
      $patient_paid = $paidinformation->patient_paid;
      $paid_amt = ($remaining_amount <= $patient_paid) ? $remaining_amount : $paidinformation->patient_paid;
      DB::table('claimdoscptdetails')->where('id', $dos_id)->update(['patient_paid' => DB::raw('patient_paid - ' . $paid_amt), 'balance' => DB::raw("balance +" . $paid_amt), 'paid_amt' => DB::raw('paid_amt - ' . $paid_amt)]); // Update CPT line item tables
      $remaining_amount = $remaining_amount - $patient_paid;
      $data['claimdoscptdetail_id'] = $dos_id;
      $data['claim_id'] = $paidinformation->claim_id;
      $data['patient_id'] = $paidinformation->patient_id;
      $data['payment_id'] = $paidinformation->payment_id;
      $data['patient_balance'] = $paid_info['patient_balance'];
      $data['posting_type'] = "Patient";
      $data['insurance_balance'] = (!empty($res)) ? $paid_info['insurance_balance'] + $paid_amt : $paid_info['insurance_balance'];
      $data['patient_paid'] = -1 * $paid_amt;
      $data['paid_amt'] = -1 * $paid_amt;
      $data['description'] = "addwallet";
      if (isset($claim_paid[$paidinformation->payment_id])) {
      $claim_paid[$paidinformation->payment_id] += $paid_amt; // To know payment ID that has paid for the claim and return to payment claim
      } else {
      $claim_paid[$paidinformation->payment_id] = $paid_amt;
      }
      ($paid_amt != 0) ? PaymentClaimCtpDetail::create($data) : "";
      if ($remaining_amount <= 0) {
      break;
      }
      }
      //dd($claim_paid);
      return $claim_paid;
      //$this->movePaymentamounttoWallet($data);
      // dd($paidinformation);
      } */

    /* public function updatePatientPaidOncptdetails($paymentdata, $paid_amt, $payment_id) {
      $paidinformation = @$paymentdata->paymentcptdetailclaim;
      $remaining_amount = $paid_amt;
      // dd($dos_id);
      foreach ($paidinformation as $paidinformation) {
      $dos_id = $paidinformation->claimdoscptdetail_id;
      $patient_paid = $paidinformation->patient_paid;
      $paid_amt = ($remaining_amount <= $patient_paid) ? $remaining_amount : $paidinformation->patient_paid;
      DB::table('claimdoscptdetails')->where('id', $dos_id)->update(['patient_paid' => DB::raw('patient_paid - ' . $paid_amt), 'balance' => DB::raw("balance +" . $paid_amt), 'paid_amt' => DB::raw('paid_amt - ' . $paid_amt)]); // Update CPT line item tables
      $balance_calculation = PaymentClaimCtpDetail::where('claimdoscptdetail_id', $dos_id)->select('patient_balance', 'insurance_balance')->orderBy('id', 'desc')->first();
      $remaining_amount = $remaining_amount - $patient_paid;
      $data['claimdoscptdetail_id'] = $dos_id;
      $data['claim_id'] = $paymentdata->claim_id;
      $data['patient_id'] = $paymentdata->claim_id;
      $data['payment_id'] = $payment_id;
      $data['patient_balance'] = $balance_calculation->patient_balance;
      $data['insurance_balance'] = $balance_calculation->insurance_balance;
      $data['patient_paid'] = -1 * $paid_amt;
      $data['paid_amt'] = -1 * $paid_amt;
      $data['description'] = "addwallet";
      $data['posting_type'] = "Patient";

      //dd($paid_amt)           ;
      ($paid_amt != 0) ? PaymentClaimCtpDetail::create($data) : "";
      if ($remaining_amount <= 0) {
      break;
      }
      }
      //echo "<pre>";print_r($data);
      // dd($paidinformation);
      } */

    /* public function getLastbalanceAmount($id, $type) {
      if ($type == "cpt") {
      $balance_calculation = PaymentClaimCtpDetail::where('claimdoscptdetail_id', $id)->select('patient_balance', 'insurance_balance')->orderBy('id', 'desc')->first();
      } else {
      $balance_calculation = PaymentClaimDetail::where('claim_id', $id)->select('insurance_due', 'patient_due', 'id')->orderBy('id', 'desc')->first();
      }
      return $balance_calculation;
      } */

    /* public function saveRefundedClaimData($saveData, $amount, $payment_id, $paid_data, $next_response = null) {
      $saveData['payment_id'] = $payment_id;
      $saveData['patient_paid_amt'] = -1 * $amount; // deducting amount
      //echo "<pre>"; print_r($saveData);
      if (empty($next_response))
      $this->updatePatientPaidOncptdetails($paid_data, $amount, $payment_id);
      $get_payment_info = $this->getLastbalanceAmount($saveData['claim_id'], "claim");
      $saveData['insurance_due'] = $get_payment_info->insurance_due;
      $saveData['patient_due'] = $get_payment_info->patient_due;
      //dd($saveData);
      $payment_claim_detail = PaymentClaimDetail::create($saveData);

      if ($payment_claim_detail)
      PMTInfoV1::where('id', $payment_id)->update(['amt_used' => DB::raw("amt_used -" . $amount), 'balance' => DB::raw("balance +" . $amount)]);
      }
     */
    public function getPatientSearchApi($type, $key, $insurance_id = null) {
        $claim_lists = [];
        $patient_list = [];
        $patient_data = [];
        $limit = Config::get('siteconfigs.charges.patientorchargelimit');
        if ($type == 'dob') {
            $key = date("Y-m-d", strtotime(base64_decode($key)));
            $patient_list = Patient::where($type, '=', $key)->where('status', 'Active')->select('id', 'account_no', 'last_name', 'middle_name', 'first_name', 'dob', 'ssn')->get();
        } elseif ($type == "policy_id") {
            $patient_list = Patient::where('status', 'Active')->whereHas('patient_insurance', function($q) use ($key) {
                        $q->where('policy_id', '=', trim($key));
                    })->get();
        } elseif ($type == "claim_number") {
            $data = explode('::', $key);

            $claim_number = $data[0];
            $insurance_id = $data[1];
           // $claim_no = (strlen($claim_number) < 5) ? str_pad($claim_number, 5, "0", STR_PAD_LEFT) : $claim_number;
            $claim_lists = ClaimInfoV1::with(['insurance_details', 'patient' => function($q) {
                            $q->select('id', 'last_name', 'first_name', 'middle_name');
                        }])->whereNotIn('status', ['E-bill'])->where('claim_number', 'LIKE', $claim_number.'%');
            // This is changed because we need to filter the claims based on the EOB choosen insurance
            if (!empty($insurance_id)) {
                /* $claim_lists =  $claim_lists->where(function($query) use ($insurance_id){
                  return $query->where('insurance_id', $insurance_id)
                  ->orwhere('self_pay', 'Yes');
                  })->get(); */
                $claim_lists = $claim_lists->get();
            } else {
                $claim_lists = $claim_lists->get();
            }
            if (!empty($claim_lists[0])){
                $patient_data = $claim_lists[0]->patient;
                $patient_id = $patient_data->id; 
                $patIns = PatientInsurance::whereIn('category', ['Primary','Secondary','Tertiary'])->where('patient_id',$patient_id)->pluck('insurance_id')->all();
                // For post insurance payment, from other then have to get confirmation popup related check start
                $patIns = !empty($patIns) ? $patIns : [];
                $patOthIns = PatientInsurance::where('category', 'Others')->where('patient_id',$patient_id)->whereNotIn('insurance_id',$patIns)->pluck('insurance_id')->all();
                $patient_data['other_ins'] = (!empty($patOthIns)) ? implode(",", $patOthIns) : 0;
            }  
        } elseif ($type == "patient") {
            $patient_id = Helpers::getEncodeAndDecodeOfId($key, 'decode');
            $claim_lists = ClaimInfoV1::with(['insurance_details', 'patient' => function($q) {
                            $q->select('id', 'last_name', 'first_name', 'middle_name');
                        }])->whereNotIn('status', ['E-bill'])->where('patient_id', $patient_id);
            if (empty($insurance_id)) {
                $claim_lists = $claim_lists->get();
            } else {
                $claim_lists = $claim_lists->where(function($query) use ($insurance_id) {
                            return $query->where('insurance_id', $insurance_id)->orwhere('self_pay', 'Yes');
                        })->get();
            }
            $patient_data = Patient::where('id', $patient_id)->where('status', 'Active')->first();
            
            $patIns = PatientInsurance::whereIn('category', ['Primary','Secondary','Tertiary'])->where('patient_id',$patient_id)->pluck('insurance_id')->all();
            // For post insurance payment, from other then have to get confirmation popup related check start
            $patIns = !empty($patIns) ? $patIns : [];
            $patOthIns = PatientInsurance::where('category', 'Others')->where('patient_id',$patient_id)->whereNotIn('insurance_id',$patIns)->pluck('insurance_id')->all();
            $patient_data['other_ins'] = (!empty($patOthIns)) ? implode(",", $patOthIns) : 0;
            // For post insurance payment, from other then have to get confirmation popup related check end
        } elseif ($type == "last_name" || $type == "first_name") {
            $patient_list = Patient::with(['patient_claim_fin'])->where('status', 'Active')->where(function($query) use ($key) {
                        $query->where('last_name', 'like', '%' . $key . '%')->orwhere('first_name', 'like', '%' . $key . '%');
                    })->select('id', 'account_no', 'last_name', 'middle_name', 'first_name', 'dob', 'ssn', 'status')->take($limit)->get();
        } elseif ($type == "name") {
            //$key = preg_replace('/\s*,\s*/', ',', $key);
            $key = str_replace(' ', '', $key);
            $patient_list = Patient::with(['patient_claim_fin'])->where('status', 'Active')->where(DB::raw("CONCAT(last_name,',',first_name,middle_name)"), 'like', "%$key%")->get();
        } elseif( $type != '' && $type != null ) {
            $patient_list = Patient::with(['patient_claim_fin'])->where('status', 'Active')->where($type, 'like', '%' . $key . '%')->select('id', 'account_no', 'last_name', 'middle_name', 'first_name', 'dob', 'ssn')->get();
        } else {
            $patient_list = Patient::with(['patient_claim_fin'])->where('status', 'Active')->select('id', 'account_no', 'last_name', 'middle_name', 'first_name', 'dob', 'ssn')->get();
        }
        foreach ($claim_lists as $claim) {
            $claim_id = $claim->id;
            $total_charge = $claim->total_charge;
            $paymentV1ApiController = new PaymentV1ApiController();
            $resultData = $paymentV1ApiController->getClaimsFinDetails($claim_id, $total_charge);
            unset($resultData['id']); //no need
            $claim['total_paid'] = $resultData['total_paid'];
            $claim['totalAdjustment'] = $resultData['totalAdjustment'];
            $claim['withheld'] = $resultData['withheld'];
            $claim['patient_paid'] = $resultData['patient_paid'];
            $claim['patient_due'] = $resultData['patient_due'];
            $claim['balance_amt'] = $resultData['patient_due'];
            $claim['insurance_due'] = $resultData['insurance_due'];
            $claim['balance_amt'] = $resultData['balance_amt'];
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_list', 'claim_lists', 'patient_data')));
    }

    // claims sorting options at popup starts here
    public function getClaimStatusSearchApi($type, $key, $status = null, $insurance_id = null) {
        //echo $insurance_id;  
        $patient_list = [];
        if ($status == "alll")
            $status = "All";
        $status = explode(",", $status);
        $status = !in_array('pending', $status) ? $status : (count($status) > 1 ? ['Submitted', 'Denied', 'Ready', 'Paid', 'Pending'] : ['Submitted', 'Denied', 'Ready', 'Pending', 'Patient']);
        $patient_id = Helpers::getEncodeAndDecodeOfId($key, 'decode');
        /* $claim_lists = ClaimInfoV1::with(['insurance_details', 'patient' => function($q) {
          $q->select('id', 'last_name', 'first_name', 'middle_name');
          }])->whereNotIn('status', ['E-bill', 'Hold'])->where('patient_id', $patient_id); */
        $claim_lists = ClaimInfoV1::
                with('rendering_provider', 'refering_provider', 'facility_detail', 'insurance_details', 'billing_provider')
                ->where('patient_id', $patient_id)
                // ->whereNotIn('status', ['E-bill', 'Hold']);
                ->whereNotIn('status', ['E-bill']);


        if (isset($status[0]) && $status[0] != "All")
            $claim_lists->whereIn('status', $status);
        if (!empty($insurance_id))
            $claim_lists = $claim_lists->where(function($query) use ($insurance_id) {
                return $query->where('insurance_id', $insurance_id)
                                ->orwhere('self_pay', 'Yes');
            });
        $claim_lists = $claim_lists->get();
        foreach ($claim_lists as $claim) {
            $claim_id = $claim->id;
            $total_charge = $claim->total_charge;
            $paymentV1ApiController = new PaymentV1ApiController();
            $resultData = $paymentV1ApiController->getClaimsFinDetails($claim_id, $total_charge);
            unset($resultData['id']); //no need
            $claim['total_paid'] = $resultData['total_paid'];
            $claim['totalAdjustment'] = $resultData['totalAdjustment'];
            $claim['withheld'] = $resultData['withheld'];
            $claim['patient_paid'] = $resultData['patient_paid'];
            $claim['patient_due'] = $resultData['patient_due'];
            $claim['balance_amt'] = $resultData['patient_due'];
            $claim['insurance_due'] = $resultData['insurance_due'];
            $claim['balance_amt'] = $resultData['balance_amt'];
        }
        $patient_data = Patient::where('id', $patient_id)->first();
        $patIns = PatientInsurance::whereIn('category', ['Primary','Secondary','Tertiary'])->where('patient_id',$patient_id)->pluck('insurance_id')->all();
        // For post insurance payment, from other then have to get confirmation popup related check start
        $patIns = !empty($patIns) ? $patIns : [];
        $patOthIns = PatientInsurance::where('category', 'Others')->where('patient_id',$patient_id)->whereNotIn('insurance_id',$patIns)->pluck('insurance_id')->all();
        $patient_data['other_ins'] = (!empty($patOthIns)) ? implode(",", $patOthIns) : 0;

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_list', 'claim_lists', 'patient_data')));
    }

    // claims sorting options at popup ends here
    // Main payment listing page claims listing for payment data starts here
    public function getPaymentcheckdataApi($payment_id = null) {
        $payment_id = Helpers::getEncodeAndDecodeOfId($payment_id, 'decode');
        if ($payment_id == '') {
            $payment_detail = PMTInfoV1::select('*', DB::raw('pmt_amt - amt_used as balance'))->get();
        } else {
            $document = Document::where('payment_id', $payment_id)->orderBy('id', 'desc')->first();
            $payment_detail = PMTInfoV1::getPaymentInfoDetailsById($payment_id);

            if ($payment_detail->pmt_type != 'Credit Balance') {
                $paymentandTxDetails = PMTInfoV1::with(['insurancedetail', 'attachment_detail', 'payment_claim_detail', 'payment_claim_cpt_detail'])
                                ->whereHas('payment_claim_cpt_detail', function ($q) use ($payment_id) {
                                    $q->where('payment_id', $payment_id);
                                })->first();
            } else {
                $paymentandTxDetails = PMTInfoV1::with(['insurancedetail', 'attachment_detail', 'payment_claim_detail', 'payment_claim_cpt_detail'])
                                ->whereHas('payment_claim_cpt_detail', function ($q) use ($payment_id) {
                                    $q->where('payment_id', $payment_id);
                                })->first();
            }
            if (isset($paymentandTxDetails)) {
                $paymentandTxDetails = $paymentandTxDetails->toArray();

                if (!empty($paymentandTxDetails['payment_claim_cpt_detail'])) {
                    $claimTxn = [];
                    foreach ($paymentandTxDetails['payment_claim_detail'] as $claimDet) {
                        $claimTxn[$claimDet['claim_id']] = $claimDet;
                    }
                    $claimCptTxn = [];
                    foreach ($paymentandTxDetails['payment_claim_cpt_detail'] as $claimCptDet) {
                        $claimTxn[$claimCptDet['claim_id']]['cpts'][] = $claimCptDet;
                    }
                }
                $paymentClaimTxDetails = (object) $claimTxn;     //dd($paymentClaimTxDetails);
                $paymentClaimCptTxDetails = (object) $paymentandTxDetails['payment_claim_cpt_detail'];
                $payment_detail->payment_claim_txns = $paymentClaimTxDetails;
                $payment_detail->payment_claim_detail = $paymentClaimCptTxDetails;
                $payment_detail->insurancedetail = (object) $paymentandTxDetails['insurancedetail'];
                $payment_detail->attachment_detail = (object) $document;
            }
        }
        if (empty($payment_detail)) {   // When no claims were added this will be called
            $payment_detail = PMTInfoV1::with(['insurancedetail', 'attachment_detail'])->where('id', $payment_id)->first();
        }

        $insurance_list = Insurance::where('status', 'Active')->pluck('insurance_name', 'id')->all(); // Thsi is for search check
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('payment_detail', 'insurance_list')));
    }

    // Main payment listing page claims listing for payment data ends here

    public function searchCheckApi($request) {
        if (empty($request))
            $request = Request::all();
        $category = $request['category'];
        $search_by = $request['search_by'];
        $search_name = trim($request['name']);
        $search_from_date = $request['search_from'];
        $insurance_id = $request['insurance'];
        $search_to_date = $request['search_to'];
        // common condition check starts here 

        if ($category == "both") {
            $paymentdetail = PMTInfoV1::where(function($q) {
                        $q->where('pmt_method', "Insurance")->orwhere('pmt_method', "Patient");
                    });
        } else {
            $paymentdetail = PMTInfoV1::where('pmt_method', $category);
        }

        $payment_details = $paymentdetail->with('created_user', 'insurancedetail', 'checkDetails', 'creditCardDetails', 'eftDetails')
                ->whereIn('pmt_type', ['Payment', 'Refund'])
                ->where('void_check', NULL)
                ->whereIn('source', ['posting', 'addwallet', 'scheduler', 'refundwallet', 'charge']);

        $payment_details->leftjoin('pmt_check_info_v1', function($join) {
            $join->on('pmt_check_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id');
            $join->on('pmt_info_v1.pmt_mode', '=', DB::raw("'Check'"));
        });

        $payment_details->leftjoin('pmt_eft_info_v1', function($join) {
            $join->on('pmt_eft_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id');
            $join->on('pmt_info_v1.pmt_mode', '=', DB::raw("'EFT'"));
        });

        $payment_details->leftjoin('pmt_card_info_v1', function($join) {
            $join->on('pmt_card_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id');
            $join->on('pmt_info_v1.pmt_mode', '=', DB::raw("'Credit'"));
        });
        // common condition check ends here 
        //$search_by = paymentnumber /insurance_name / check_no / claim_no /  check_date / created_at / posted_by

        switch ($search_by) {

            case 'pmt_no':
            case 'paymentnumber':
                $paymentdetail->where('pmt_no', '=', $search_name);
                break;

            case 'insurance_name':
                $paymentdetail->where('insurance_id', $insurance_id);
                break;

            case 'check_no':
                $paymentdetail->Where(function ($paymentdetail) use ($search_name) {
                    // Check
                    $paymentdetail->Where('pmt_check_info_v1.check_no', 'LIKE', $search_name . '%');

                    /*
                      $paymentdetail->WhereHas('checkDetails', function($paymentdetail) use ($search_name) {
                      $paymentdetail = $paymentdetail->Where('check_no', 'LIKE', '%' . $search_name . '%');
                      });
                     */
                    // EFT
                    $paymentdetail->orWhere('pmt_eft_info_v1.eft_no', 'LIKE', $search_name . '%');
                    /*
                      $paymentdetail->orWhereHas('eftDetails', function($paymentdetail) use ($search_name) {
                      $paymentdetail = $paymentdetail->Where('eft_no', 'LIKE', '%' . $search_name . '%');
                      });
                     */
                    // card            
                    $paymentdetail->orWhere('pmt_card_info_v1.card_last_4', 'LIKE', $search_name . '%');
                    /*
                      $paymentdetail->orWhereHas('creditCardDetails', function($paymentdetail) use ($search_name) {
                      $paymentdetail->Where('card_last_4', 'LIKE', '%' . $search_name . '%');
                      });
                     */
                });
                break;

            case 'claim_no':
                $search_name = (strlen($search_name) < 5) ? str_pad($search_name, 5, "0", STR_PAD_LEFT) : $search_name;
                $payment_ids = PMTClaimTXV1::WhereHas('claims', function($q) use ($search_name) {
    		                            $q->where('claim_number', $search_name);
    		                        })->pluck('payment_id')->all();
                $paymentdetail->whereIn('pmt_info_v1.id', $payment_ids);
                break;

            case 'created_at':
                $paymentdetail->whereDate('created_at', '=', date('Y-m-d', strtotime($request['search_date'])));
                break;

            case 'check_date':
                $srch_date = $request['search_date'];
                $paymentdetail->Where(function ($paymentdetail) use ($srch_date) {
                    $dateSearch = date("Y-m-d", strtotime(@$search));
                    //$paymentdetail->whereDate('pmt_check_info_v1.check_date', '=', date('Y-m-d', strtotime($srch_date)));
                    $paymentdetail->Where('pmt_check_info_v1.check_date', '=', date('Y-m-d', strtotime($srch_date)));
                    $paymentdetail->orWhere('pmt_eft_info_v1.eft_date', '=', date('Y-m-d', strtotime($srch_date)));
                    /*
                      $paymentdetail->WhereHas('checkDetails', function($paymentdetail) use ($srch_date) {
                      $paymentdetail->whereDate('check_date', '=', date('Y-m-d', strtotime($srch_date)));
                      });
                      $paymentdetail->orWhereHas('eftDetails', function($paymentdetail) use ($srch_date) {
                      $paymentdetail->whereDate('eft_date', '=', date('Y-m-d', strtotime($srch_date)));
                      });
                     */
                });
                break;

            case 'posted_by':
                $user_ids = Users::where('name', 'like', '%' . $search_name . '%')->orwhere('short_name', 'like', '%' . $search_name . '%')->pluck('id')->all();

                $paymentdetail->whereIn('created_by', $user_ids);
                break;
                
            default:
                break;
        }
        $payment_details->orwhereBetween('pmt_info_v1.created_at', [$search_from_date, $search_to_date]);
        $payment_details = $payment_details->selectRaw('pmt_info_v1.*')->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('payment_details')));
    }

    public function getPaymentDetailApi($payment_id) {
        $payment_id = Helpers::getEncodeAndDecodeOfId($payment_id, 'decode');
        $payment_detail = PMTInfoV1::getPaymentInfoDetailsById($payment_id);
        $insurance_list = Insurance::where('status', 'Active')->pluck('insurance_name', 'id')->all();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('payment_detail', 'insurance_list')));
    }

    public function checkexistApi($type, $check_number, $check_type = null, $patient_id = 0) {
        $patient_id = is_numeric(@$patient_id) ? @$patient_id : Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $check_val = (is_numeric($check_number) && $check_number / 1 == 0 ) ? false : true;
        $paymetType = ($type == 'Patient') ? 'patientPayment' : 'insurancePayment';
        $count = PMTInfoV1::findCheckExistsOrNot($check_number, $type, $check_type, $paymetType, $patient_id);
        if ($count && $check_val) {
            return response()->json(["error"]);
        } else {
            return response()->json(["success"]);
        }
    }

    public function checkexistMoneyApi() {
        $request = Request::all();
        $check_number = $request['value'];
        $check_type = $request['type'];
        $type = ($check_type == 'MO') ? 'Patient' : 'Insurance';
        $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
        $paymetType = ($check_type == 'MO') ? 'patientPayment' : 'insurancePayment';
        $count = PMTInfoV1::findCheckExistsOrNot($check_number, $type, $check_type, $paymetType, $patient_id);
        if ($count) {
            return '{ "valid": false }';
        } else {
            return'{ "valid": true }';
        }
    }

    public function editCheckApi($id) {
        $payment_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $payment_details = PMTInfoV1::with('insurancedetail', 'attachment_detail')->where('id', $payment_id)->first();
        $payment_detail = PMTInfoV1::getPaymentInfoDetailsById($payment_id);
        $payment_detail->insurancedetail = (object) $payment_details['insurancedetail'];
        $payment_detail->attachment_detail = (object) $payment_details['attachment_detail'];
        $check_document_exist = Document::where('type_id', $payment_id)->where('document_type', 'payments')->first();
        $insurance_detail = Insurance::where('status', 'Active')->pluck('insurance_name', 'id')->all();
        $billing_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('payment_detail', 'insurance_detail', 'billing_providers', 'check_document_exist')));
    }

    public function updateCheckdataApi($request) {

        $pmtInfo = new PMTInfoV1();
        $payment_id = Helpers::getEncodeAndDecodeOfId($request['payment_id'], 'decode');
        $document_id = isset($request['document_id']) ? Helpers::getEncodeAndDecodeOfId($request['document_id'], 'decode') : "";
        $new_amt = $request['pmt_amt'];
        $payment = PMTInfoV1::where('id', $payment_id);
        $pmt_data = $payment->select('patient_id', 'pmt_amt', 'pmt_mode', 'pmt_mode_id', 'pmt_method', 'amt_used')->first();
        $savadata['pmt_amt'] = $new_amt;
        $savadata['pmt_method'] = $pmt_data['pmt_method'];
        $savadata['patient_id'] = $pmt_data['patient_id'];
		// Check number empty checking included.
        if(isset($request['check_no']) && $request['check_no'] != '')
          $savadata['check_no'] = $request['check_no'];
        // Check date not empty checking included
        if(isset($request['check_date']) && $request['check_date'] != '')
          $savadata['check_date'] = !(empty($request['check_date'])) ? date("Y-m-d", strtotime($request['check_date'])) : "";
        
		    $pmtInfo->updatePaymentInfoDetailsById($savadata, $pmt_data['pmt_mode'], $pmt_data['pmt_mode_id'], $payment_id);
        $savadata['pmt_amt'] = $new_amt - $pmt_data['amt_used'];
        PMTWalletV1::updatePmtWalletAmount($payment_id, $savadata);
        $request['main_type_id'] = $request['type_id'] = $payment_id;
        if (isset($request['temp_type_id'])) {
            $file_name = $this->editPaymentPostingDocument($request, $payment_id);
        }
        $url = '';
        if (!empty($file_name)) {
            $url = url($request['payment_id'] . '/document/get/patients' . '/' . $file_name);
        }
        $payment_details = PMTInfoV1::with('insurancedetail', 'attachment_detail')->where('id', $payment_id)->first();
        $payment_detail = PMTInfoV1::getPaymentInfoDetailsById($payment_id);
        $payment_detail->insurancedetail = (object) $payment_details['insurancedetail'];
        $payment_detail->attachment_detail = (object) $payment_details['attachment_detail'];
        /*** user activity ***/
        $action = "edit";
        $get_name = $pmt_data['pmt_mode_id']. ' '.$pmt_data['pmt_mode'];
        $fetch_url = Request::url();
        $module = "payments";
        $submodule = "";
        $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
        /*** user activity ***/
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('payment_detail', 'url')));
    }

    // Save payment by clicking on cancel button starts here
    public function savePaymentdataonCancelApi(Request $request) {
        $request = $request::all();
        $paymentV1 = new PaymentV1ApiController();
        $request['payment_amt_pop'] = $request['payment_amt'];
        $response = $paymentV1->createWalletData($request);
        $responseData = $response->getData();
        if (!empty($responseData->data)) {
            return $response;
        } else {
            return $response;
        }
    }

    // Save payment by clicking on cancel button ends here
    public function movePaymentAttachment($request, $document_id = null, $file_name = null) {
        $type = "patients";
        $request["document_categories_id"] = Document::DocumentCategoriesId("Payer_Reports_ERA_EOB");
        $file = Request::file('filefield_eob');
        $src = '';
        $request['document_type'] = "patients";
        $request['upload_type'] = "browse";
        $request['check_no'] = isset($request['check_no']) ? $request['check_no'] : "";
        $request['check_date'] = !(empty($request['check_date'])) ? date("Y-m-d", strtotime($request['check_date'])) : "";
        $request['payer'] = isset($request['insurance_id']) ? $request['insurance_id'] : '';
        $request['category'] = 'Payer_Reports_ERA_EOB';
        $request['checkno'] = $request['check_no'];
        $request['checkdate'] = $request['check_date'];
        if (isset($request['payment_id'])) {
            $request['payment_id'] = Helpers::getEncodeAndDecodeOfId($request['payment_id'], 'decode');
        }
        if (isset($request['payment_amt_pop']))
            $request['checkamt'] = $request['payment_amt_pop'];
        elseif (isset($request['payment_amt']))
            $request['checkamt'] = $request['payment_amt'];
        elseif (isset($request['pmt_amt']))
            $request['checkamt'] = $request['pmt_amt'];

        if (!is_null($document_id) && !empty($document_id)) {
            $data = Document::where('id', $document_id)->first();
            $this->deletePaymentattachment($document_id);
        } else {
            if (!empty($file)) {
                if (!isset($request['type_id']))
                    $request['type_id'] = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
                $request['main_type_id'] = $request['type_id'];
                if (!empty($request['claim_ids'])) {
                    $claims_id = explode(',', @$request['claim_ids']);
                    foreach ($claims_id as $claim_id) {
                        $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
                        $request['claim_number_data'] = ClaimInfoV1::where('id', $claim_id)->pluck('claim_number')->first();
                        $request['title'] = time() . " Payer Eob";
                        $data = Document::create($request);
                        $assign_data['document_id'] = $data->id;
                        $assign_data['patient_id'] = $data->type_id;
                        $assign_data['assigned_user_id'] = Auth::user()->id;
                        $assign_data['notes'] = "Payment Eob";
                        $assign_data['priority'] = 'High';
                        $assign_data['followup_date'] = date('y-m-d');
                        $assign_data['status'] = "Assigned";
                        $assign_data['created_by'] = Auth::user()->id;
                        $assign_data['claim_id'] = $claim_id;
                        $assigned_data = DocumentFollowupList::create($assign_data);
                        $assigned_data->save();
                        $file_store_name = md5($data->id . strtotime(date('Y-m-d H:i:s'))) . '.' . $file->getClientOriginalExtension();
                        $store_arr = Helpers::amazon_server_folder_check($type, $file, $file_store_name, $src);
                        $data->filename = $file_store_name;
                        $temp = explode(".", $file_store_name);
                        $data->document_extension = isset($temp[1]) ? $temp[1] : "";
                        $data->document_path = $store_arr[0];
                        $data->document_domain = $store_arr[1];
                        $data->save();
                    }
                } else {
                    $request['title'] = time() . " Payer Eob";
                    $data = Document::create($request);
                    $assign_data['document_id'] = $data->id;
                    $assign_data['patient_id'] = $data->type_id;
                    $assign_data['assigned_user_id'] = Auth::user()->id;
                    $assign_data['notes'] = "Payment Eob";
                    $assign_data['priority'] = 'High';
                    $assign_data['followup_date'] = date('y-m-d');
                    $assign_data['status'] = "Assigned";
                    $assign_data['created_by'] = Auth::user()->id;
                    $assign_data['claim_id'] = '';
                    $assigned_data = DocumentFollowupList::create($assign_data);
                    $assigned_data->save();
                    $file_store_name = md5($data->id . strtotime(date('Y-m-d H:i:s'))) . '.' . $file->getClientOriginalExtension();
                    $store_arr = Helpers::amazon_server_folder_check($type, $file, $file_store_name, $src);
                    $data->filename = $file_store_name;
                    $temp = explode(".", $file_store_name);
                    $data->document_extension = isset($temp[1]) ? $temp[1] : "";
                    $data->document_path = $store_arr[0];
                    $data->document_domain = $store_arr[1];
                    $data->save();
                }
            }
        }
        if ($file_name == 'file_name') {
            return $file_store_name;
        }
        return $data->id;
    }

    public function deletePaymentattachment($document_id) {
        $document = Document::where('id', $document_id)->first();
        $main_dir_name = md5('P4');  // Statically given
        if (Session::get('practice_dbid') != '') {
            $main_dir_name = md5('P' . Session::get('practice_dbid'));
        }
        if ($main_dir_name != '') {
            $chk_env_site = getenv('APP_ENV');
            $default_view = Config::get('siteconfigs.production.defult_production');
            $chk_env_site = getenv('APP_ENV');
            if ($chk_env_site == "local")
                $storage_disk = "s3";
            elseif ($chk_env_site == $default_view)
                $storage_disk = "s3_production";
            else
                $storage_disk = "s3";
            $document_path = @$document->document_path;
            $document_name = @$document->file_name;
            if (Storage::disk($storage_disk)->exists($document_path . $document_name)) {
                Storage::disk($storage_disk)->delete([$document_path . $document_name]);
                return true;
            }
        }
    }

    public function editPaymentApi($claim_id, $type = null) {
         Session::put('ar_claim_id', $claim_id);
        $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
        $patient_id = '';
        if (!empty($type)) {
            $patient_id = ClaimInfoV1::where('id', $claim_id)->value('patient_id');
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
            Session(['ar_patient_id'=> $patient_id]);
        }
        return compact('patient_id');
    }

    public function searchClaimbyInsuranceApi($insurance_id, $patient_id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if (empty($insurance_id)) {
            $claim_lists = ClaimInfoV1::with('insurance_details')->where('patient_id', $patient_id)->get();
        } else {
            $claim_lists = ClaimInfoV1::with('insurance_details')->where(function($query) use ($insurance_id) {
                        return $query->where('insurance_id', $insurance_id)
                                        ->orwhere('self_pay', 'Yes');
                    })->where('patient_id', $patient_id)->get();
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claim_lists')));
    }

    /*  public function getpatientInsuranceBalanceByCPT($claim_id, $nextresposibility = null) {
      $dos_cpt_details = Claimdoscptdetail::where('claim_id', $claim_id)->whereNotIn('cpt_code', ['Patient'])->pluck('id')->all();
      $detail = [];
      $data = [];
      foreach ($dos_cpt_details as $dos_cpt_detail) {
      $payment_claim_details = PaymentClaimCtpDetail::where('claimdoscptdetail_id', $dos_cpt_detail)->orderBy('id', 'desc')->select('id', 'patient_balance', 'insurance_balance')->first();
      $detail[$dos_cpt_detail]['patient_balance'] = $payment_claim_details->patient_balance;
      $detail[$dos_cpt_detail]['insurance_balance'] = $payment_claim_details->insurance_balance;
      }
      $data['patient_balance'] = ($nextresposibility == 'patient') ? array_sum(array_column($detail, 'patient_balance')) : 0;
      $data['insurance_balance'] = array_sum(array_column($detail, 'insurance_balance'));
      return $data;
      }
     */
    /* public function movePaymentamounttoWallet($data, $paid_amt_claim = null, $from = null) {
      $sumArray = array();
      foreach ($paid_amt_claim as $k => $subArray) {
      foreach ($subArray as $id => $value) {
      isset($sumArray[$id]) ? $sumArray[$id] += $value : $sumArray[$id] = $value;
      }
      }

      $data['claim_paid'] = 0;
      unset($data["transaction_type"], $data["payment_claim_detail_id"]);
      $get_balance = $this->getpatientInsuranceBalanceByCPT($data['claim_id']);
      $patient_balance = $get_balance['patient_balance'];
      $insurance_balance = $get_balance['insurance_balance'];
      //dd($sumArray)        ;
      foreach ($sumArray as $key => $value) {
      $balance_amt = $data["insurance_due"];
      $balance_amt = (@$data['payment_type'] == "Refund") ? $balance_amt + $value : $balance_amt;
      if (@$data['payer_insurance_id'] == "patient") {
      $data["patient_due"] = $patient_balance;
      $data["insurance_due"] = $insurance_balance;
      } else {
      $data["insurance_due"] = $insurance_balance;
      $data["patient_due"] = $patient_balance;
      }
      $data['description'] = "addwallet";
      $data['patient_paid_amt'] = -1 * $value;
      $data['payment_type'] = "Addwallet";
      $data['payment_id'] = $key;
      $data['claim_paid'] += $value;
      unset($data['insurance_id']);
      //dd($data) ;
      $result = PaymentClaimDetail::create($data);
      if ($result)
      PMTInfoV1::where('id', $key)->update(['amt_used' => DB::raw("amt_used -" . $value), 'balance' => DB::raw("balance +" . $value)]);
      if ($from) {

      DB::table('claims')->where('id', $data['claim_id'])->update(['total_paid' => DB::raw("total_paid -" . $value), 'balance_amt' => DB::raw("balance_amt +" . $value), 'patient_paid' => DB::raw('patient_paid -' . $value)]);
      }
      }
      return $data;
      } */

    /* When responsibility changed patient paid amount moved to wallet starts */

    /* public function getPatientPaidamountMovedWallet($dos_id, $paid_info, $response = null) {
      $claim_dos_data = Claimdoscptdetail::with('claim_details')->findorFail($dos_id);
      $paid_amt = $claim_dos_data->patient_paid;
      $claim_id = $claim_dos_data->claim_id;
      $paid_info['patient_balance'] = 0;
      $response = (is_null($response)) ? $response : "responsibility";
      $data_get = [];
      if ($paid_amt > 0) {
      Claimdoscptdetail::where("claim_id", $claim_id)->where("cpt_code", "Patient")->update(["patient_paid" => DB::raw("patient_paid - " . $paid_amt)]);
      DB::table('claims')->where('id', $claim_id)->update(['total_paid' => DB::raw("total_paid -" . $paid_amt), 'balance_amt' => DB::raw("balance_amt +" . $paid_amt), 'patient_paid' => DB::raw('patient_paid -' . $paid_amt)]);
      $data_get = $this->updatePatientPaidOnPaymentcptdetails($paid_amt, $dos_id, $paid_info, $response);
      return $data_get;
      }
      } */

    /* When responsibility changed patient paid amount moved to wallet ends */

    public function getClaimdataApi($claim_id) {
        $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
        $claim_data = ClaimInfoV1::with('insurance_details')->where('id', $claim_id)->first();
        return Response::json(array('data' => compact('claim_data')));
        //dd($claim_data);
    }

    /**
     * Remove the checklist from storage.
     * 
     * @param  int  $id
     * @return Response
     * if pmtClaimTx exists
     *      update the void check->1
     * else
     *      delete the records from the pmt_info
     */
    public function getDeleteApi($id, $type = null) {
        $pmtId = Helpers::getEncodeAndDecodeOfId($id, 'decode');

        $paymentData = PMTInfoV1::where('id', $pmtId)->first();
        // If check is refund check then proceed with the refund check process same as patient check.
        if ($paymentData['pmt_type'] == 'Refund') {
            $this->voidRefundPaymentcheckdataApi($pmtId);
        }

        $txCount = $this->findTheTxisExistsOrNot($pmtId);
        if (!empty($txCount)) {
            PMTInfoV1::where('id', $pmtId)->update(['void_check' => 1]);
            return Response::json(array('status' => 'success', 'message' => 'Check deleted successfully'));
        } elseif (empty($txCount)) {
            $pmtInfo = PMTInfoV1::where('id', $pmtId)->first();
            if (!empty($pmtInfo)) {
                PMTInfoV1::where('id', $pmtId)->delete();
                if ($pmtInfo->source == 'addwallet' || $pmtInfo->pmt_type == 'Credit Balance')  {
                    // Delete corresponding wallet entry if exists.
                    PMTWalletV1::where('pmt_info_id', $pmtId)->delete();
                }    
            }
            return Response::json(array('status' => 'success', 'message' => 'Check deleted successfully'));
        } else {
            return Response::json(array('status' => 'error', 'message' => 'Check has not deleted'));
        }
    }

    // Refund void payment
    public function voidRefundPaymentcheckdataApi($payment_id) {
        try {
            $paymentData = PMTInfoV1::where('id', $payment_id)->first();
            if ($paymentData['pmt_method'] == 'Insurance') {
                $claimTxData = PMTClaimTXV1::select('id', 'payment_id', 'total_paid', 'claim_id', 'pmt_method', 'pmt_type', 'total_paid')
                        ->where('payment_id', $payment_id)
                        ->groupBy("payment_id", "claim_id");
                $claimTxDetails = $claimTxData->get()->toArray();
            }
            //\Log::info("Void refund payment \n claim tx details");  \Log::info($claimTxDetails);
            DB::beginTransaction();
            foreach ($claimTxDetails AS $claimTxDatas) {
                if ($claimTxDatas['total_paid'] < '0') {
                    $claimTxIDS = $claimTxDatas['id'];
                    $txnFor = $claimTxDatas['pmt_method'] . ' ' . $claimTxDatas['pmt_type'];

                    /* Claim related handle start */
                    if (!empty($claimTxDatas['claim_id'])) {

                        $claimCptTx = PMTClaimCPTTXV1::select('*', DB::raw('sum(paid) as paid'))
                                ->where('pmt_claim_tx_id', $claimTxIDS)
                                ->groupBy("payment_id", "claim_cpt_info_id") // Group by pmt id and cpt id used to handle mulitple cpt.
                                ->get();
                        //\Log::info($claimCptTx);
                        $finDatas = PMTClaimFINV1::where('claim_id', $claimTxDatas['claim_id'])->first();

                        $patientId = $finDatas['patient_id'];
                        $newPmtClaimTx = array(
                            "payment_id" => $payment_id,
                            "claim_id" => $claimTxDatas['claim_id'],
                            "pmt_method" => 'Insurance',
                            "pmt_type" => 'Refund',
                            "patient_id" => $patientId,
                            "total_paid" => -1 * ($claimTxDatas['total_paid']),
                            "posting_date" => date("Y-m-d"),
                            "created_by" => Auth::user()->id
                        );
                        //\Log::info("Creating new claim TXN"); \Log::info($newPmtClaimTx);
                        $pmtClaimTxId = PMTClaimTXV1::create($newPmtClaimTx)->id;
                        //dd($pmtClaimTxId);
                        $desArr = array(
                            'pmt_id' => $payment_id,
                            'claim_info_id' => $claimTxDatas['claim_id'],
                            'txn_id' => $pmtClaimTxId,
                            'resp' => $this->getClaimResponsibility($claimTxDatas['claim_id']),
                            'value2' => 1,
                            'check_amount' => -1 * ($claimTxDatas['total_paid'])
                        );
                        $claimTxDesId = $this->storeClaimTxnDesc('Void Check', $desArr);
                        //\Log::info("Store Claim Txn Desc");   \Log::info($desArr);

                        $chargeV1 = new ChargeV1ApiController();
                        foreach ($claimCptTx as $claimCpt) {

                            $pmtClaimTxDetails = PMTClaimTXV1::select(['id', 'pmt_method', 'pmt_type'])
                                    ->where('id', $claimCpt['pmt_claim_tx_id'])
                                    ->first();
                            $paidAmount = $claimCpt['paid'];
                            $claimCpt['paid'] = -1 * $paidAmount;
                            $claimCpt = $claimCpt->toArray();
                            $claimCpt['resp'] = $this->getClaimResponsibility($claimCpt['claim_id']);
                            $claimCpt['pmt_claim_tx_id'] = $pmtClaimTxId;
                            $resultSet = PMTClaimCPTTXV1::create($claimCpt);
                            //\Log::info("New pmtclaim cpt tx");  \Log::info($claimCpt);

                            $desArr = array(
                                'pmt_id' => $claimCpt['payment_id'],
                                'txn_id' => $resultSet->id,
                                'claim_tx_desc_id' => $claimTxDesId,
                                'claim_info_id' => $claimCpt['claim_id'],
                                'claim_cpt_info_id' => $claimCpt['claim_cpt_info_id'],
                                'check_amount' => $claimCpt['paid'],
                                'value2' => 1,
                                'resp' => $this->getClaimResponsibility($claimCpt['claim_id'])
                            );
                            $txnFor = $pmtClaimTxDetails['pmt_method'] . ' ' . $pmtClaimTxDetails['pmt_type'];
                            $usedAmount = ($paidAmount < 0) ? $paidAmount : $claimCpt['paid'];                            
                            $cptTxDesId = $this->storeClaimCptTxnDesc('Void Check', $desArr);
                            PMTInfoV1::updatePaymettAmoutUsed($payment_id, $usedAmount);
                            //$chargeV1->updateClaimCptTxData($claimCpt, $txnFor);
                            $currentFinBalance = $chargeV1->updateClaimCptFindData($claimCpt, $txnFor);
                            //dd($currentFinBalance);                            
                            $chargeV1->updateBalanceClaimCPTTXDesc($cptTxDesId, $currentFinBalance['patient_balance'], $currentFinBalance['insurance_balance']);
                        }
                        $currentClaimFinBalance = $chargeV1->findClaimLevelPatientandInsuranceBal(['claim_id' => $claimTxDatas['claim_id']], $claimTxDesId);                        
                        $chargeV1->updateBalanceClaimTXDesc($claimTxDesId, $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);                        
                        ClaimInfoV1::updateClaimStatus($claimTxDatas['claim_id'], $txnFor);
                    }
                }/* else{
                  return Response::json(array('status' => 'failed', 'message' => 'failed', 'data' => 'failed'));
                  } */
            }

            /* Claim related handle end */
            $walletDatas = PMTWalletV1::where('pmt_info_id', $payment_id)->get();
            if (!empty($claimTxDatas)) {
                foreach ($walletDatas as $walletData) {
                    $walletAmount = $walletData['amount'];
                    $walletData['amount'] = -1 * $walletData['amount'];
                    $walletData['tx_type'] = 'Debit';
                    $walletData = $walletData->toArray();
                    PMTWalletV1::create($walletData);
                    $walletData = ($walletAmount < 0) ? abs($walletAmount) : $walletData['amount'];
                    PMTInfoV1::updatePaymettAmoutUsed($payment_id, $walletData);
                }
            }

            //Document::where('main_type_id', $payment_id)->delete();
            PMTInfoV1::where('id', $payment_id)->update(['void_check' => 1]);
            DB::commit();
            return Response::json(array('status' => 'success', 'message' => 'success', 'data' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            $this->showErrorResponse("updatePmt_infoAmt_usedColumn", $e);
            return Response::json(array('status' => 'failed', 'message' => 'failed', 'data' => 'failed'));
        }
    }

    public function findTheTxisExistsOrNot($pmtId) {
        $txCount = PMTClaimTXV1::where('payment_id', $pmtId)->count();
        return $txCount;
    }

    public function getTransactionForRefund($id) {
        $data = PaymentTransactionHistory::Has('payment_data')
                ->groupBy('source_payment_id')->where('payment_id', $id)
                ->selectRaw('source_payment_id, sum(refund_amt) as refund_amt')
                ->pluck('refund_amt', 'source_payment_id')->all();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                PMTInfoV1::where('id', $key)
                        ->update(['amt_used' => DB::raw("amt_used -" . $value),
                            'balance' => DB::raw("balance +" . $value)]);
            }
        }
    }

    public function noteAdd() {
        try {
            $request = Request::all();
            $data['notes'] = $request['note'];
            $data['user_id'] = Auth::user()->id;
            $data['pmt_id'] = $request['pmt_id'];
            PMTUnpostedNotesV1::create($data);
        } catch (Exception $e) {
            \Log::info("Error occured Payment unposted notes. Error " . $e->getMessage());
        }
    }

    public function editPaymentPostingDocument($request, $paymentId) {

        $document = Document::where('payment_id', $paymentId);
        $document_count = $document->count();
        $documentInfo = $document->get()->first();

        if ($document_count > 0 && isset($request['temp_type_id']) && !empty($request['temp_type_id'])) {
            $newDocument = Document::where('temp_type_id', $request['temp_type_id'])->get()->first();
            $document->update(['checkno' => @$request['check_no'], 'checkamt' => @$request['pmt_amt'], 'checkdate' => date('Y-m-d', strtotime(@$request['check_date'])), 'document_path' => $newDocument->document_path, 'document_extension' => $newDocument->document_extension, 'document_domain' => $newDocument->document_domain, 'filename' => $newDocument->filename, 'filesize' => $newDocument->filesize, 'original_filename' => $newDocument->original_filename, 'mime' => $newDocument->mime]);
            if (isset($request['temp_type_id']) && !empty($request['temp_type_id']))
                Document::where('temp_type_id', $request['temp_type_id'])->delete();
            return $newDocument->filename;
        }

        if ((($document_count == 0 ) && isset($request['temp_type_id']) && !empty($request['temp_type_id']))) {
            $currentDocument = Document::where('temp_type_id', $request['temp_type_id']);
            $currentDocumentInfo = $currentDocument->get()->first();
            $currentDocument->update(['checkno' => @$request['check_no'], 'checkamt' => @$request['pmt_amt'], 'checkdate' => date('Y-m-d', strtotime(@$request['check_date'])), 'payment_id' => $paymentId, 'type_id' => $paymentId, 'main_type_id' => $paymentId, 'temp_type_id' => '']);
            return $currentDocumentInfo->filename;
        }
    }
  
    public function getEraApi($data,$export = ''){
      if(isset($data) && !empty($data))
          $request = $data;
      else
          $request = Request::all();
      // if(isset($request['export']) && $request['export'] == 'yes'){
      //     foreach($request as $key=>$value){
      //         if(strpos($value, ',') !== false && $key != 'patient_name'){
      //             $request['dataArr']['data'][$key] = json_encode(explode(',',$value));
      //         }else{
      //             $request['dataArr']['data'][$key] = json_encode($value);    
      //         }
      //     }
      // }
      $start = (isset($request['start'])) ? $request['start'] : 0;
      $len = (isset($request['length'])) ? $request['length'] : 50;
      $era_qry = Eras::leftjoin('insurances','insurances.id','=','eras.insurance_id')
						->leftjoin('pmt_check_info_v1','pmt_check_info_v1.check_no','=','eras.check_no')
						->leftjoin('pmt_eft_info_v1','pmt_eft_info_v1.eft_no','=','eras.check_no')
						->leftjoin('pmt_info_v1 AS eftPmt',function($join){
							$join->on('eftPmt.pmt_mode_id','=','pmt_eft_info_v1.id')
								->where("eftPmt.pmt_mode","=","EFT");
						})
						->leftjoin('pmt_info_v1 AS chkPmt',function($join){
							$join->on('chkPmt.pmt_mode_id','=','pmt_check_info_v1.id')
								->where("chkPmt.pmt_mode","=","Check");
						})
						->with('insurance_details','check_details','eft_details')
						->select('eras.*','insurances.id AS ins_id','eftPmt.amt_used AS eft_used','eftPmt.pmt_amt','chkPmt.amt_used AS chk_used','chkPmt.pmt_amt');
      $search = (!empty($request['search']['value'])) ? trim($request['search']['value']) : "";
      $orderByField = 'eras.receive_date';
      $orderByDir = 'DESC';
      if (!empty($request['order'])) {
        $orderByField = ($request['order'][0]['column']) ? $request['order'][0]['column'] : $orderByField;
        switch ($orderByField) {
          case '1':
            $orderByField = 'eras.receive_date';
            break;
          case '2':
            $orderByField = 'eras.insurance_name';
            break;
          case '3':
            $orderByField = 'eras.check_no';                   
            break;
          case '4':
            $orderByField = 'eras.check_date';                
            break;
          case '5':
            $orderByField = 'eras.check_paid_amount';                   
            break;
          default:
            $orderByField = 'eras.receive_date';
            break;
        }
        $orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'DESC';
      }
      if(!empty(json_decode(@$request['dataArr']['data']['ReceivedDate']))){
        $date = explode('-',json_decode($request['dataArr']['data']['ReceivedDate']));
        $from = date("Y-m-d", strtotime($date[0]));
        if($from == '1970-01-01'){
          $from = '0000-00-00';
        }
        $to = date("Y-m-d", strtotime($date[1]));
        $era_qry->where(DB::raw('DATE(eras.receive_date)'),'>=',$from)->where(DB::raw('DATE(eras.receive_date)'),'<=',$to);
      }
      if (!empty(json_decode(@$request['dataArr']['data']['Insurance'])) && (json_decode(@$request['dataArr']['data']['Insurance'])) != "null" ) {
          if (strpos($request['dataArr']['data']['Insurance'], ',') !== false) {
              $era_qry->whereIn('insurances.id', json_decode($request['dataArr']['data']['Insurance']));
          }else{
              $era_qry->where('insurances.id', json_decode($request['dataArr']['data']['Insurance']));
          }
      }
      if(!empty(json_decode(@$request['dataArr']['data']['CheckNo']))){
        $check_no = json_decode($request['dataArr']['data']['CheckNo']);
        $era_qry->Where(function ($era_qry) use ($check_no) {
          $era_qry->Where(function ($query) use ($check_no) {
            $era_qry = $query->orWhere('eras.check_no','like', "%{$check_no}%");
          });
        });
      } 
	  
		if(json_decode(@$request['dataArr']['data']['archive_list'])){
			$era_qry->Where('eras.archive_status', 'Archive');
		}else{
			$era_qry->Where('eras.archive_status', 'Unarchive');
		}
	  
		if(json_decode(@$request['dataArr']['data']['posted_list']) && !json_decode(@$request['dataArr']['data']['unposted_list'])){
		
      $era_qry->where(function ($query) {
        $query->whereRaw("( eftPmt.pmt_mode='EFT' and eras.check_paid_amount <= eftPmt.amt_used) or (eftPmt.pmt_mode='Check' and eras.check_paid_amount <= chkPmt.amt_used)");
      });
		}
		
		if(json_decode(@$request['dataArr']['data']['unposted_list']) && !json_decode(@$request['dataArr']['data']['posted_list'])){
			$era_qry->where(function ($query) {
				$query->whereRaw("( eftPmt.pmt_mode='EFT' and eras.check_paid_amount > eftPmt.amt_used) or (eftPmt.pmt_mode='Check' and eras.check_paid_amount > chkPmt.amt_used)");
			});
		}
		
      if(!empty(json_decode(@$request['dataArr']['data']['CheckDate']))){
        $date = explode('-',json_decode($request['dataArr']['data']['CheckDate']));
        $from = date("Y-m-d", strtotime($date[0]));
        if($from == '1970-01-01'){
          $from = '0000-00-00';
        }
        $to = date("Y-m-d", strtotime($date[1]));
        $era_qry->where(DB::raw('DATE(eras.check_date)'),'>=',$from)->where(DB::raw('DATE(eras.check_date)'),'<=',$to);
      }
		/* $era_qry->where(function ($query) {
			$query->where('eftPmt.pmt_type','Payment')
			  ->orwhere('chkPmt.pmt_type','Payment');
		}); */
		$era_qry->where('eras.deleted_at', NULL);
		$era_qry->orderBy($orderByField,$orderByDir);
    
    //Added one more sorting condition for receive_date
      //Revision 1 - Ref: MR-2711 21 Aug 2019: Selva
    
	
      if($orderByField == 'eras.receive_date')
		    $era_qry->orderBy('eras.created_at',$orderByDir);
  
    
      $count = $era_qry->count(DB::raw('(eras.id)'));
      if($export == ''){
        $era_qry->skip($start)->take($len);
      }
      $e_remittance = $era_qry->get();
	 // dd($e_remittance->toArray());
      return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('e_remittance','count')));
    }
}