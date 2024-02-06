<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Lang;
use View;
use App;
use App\Models\Cpt as Cpt;
use App\Models\Patients\Patient as Patient;
use App\Models\Practice as Practice;
use App\Models\Patients\PatientBudget as PatientBudget;
use App\Models\Patients\PatientOtherAddress as PatientOtherAddress;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\Payments\ClaimCPTInfoV1 as ClaimCptInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTClaimFINV1 as PMTClaimFINV1;
use App\Models\Patients\PatientContact as PatientContact;
use App\Models\PatientStatementTrack as PatientStatementTrack;
use App\Models\Icd as Icd;
use App\Models\EmailTemplate;
use App\Models\Patients\CheckReturn as CheckReturn;
use App\Models\PatientStatementSettings as PatientStatementSettings;
use App\Http\Helpers\Helpers as Helpers;
use Config;
use DB;
use ZipArchive;
use App\Traits\ClaimUtil;

use Input;
use Session;
use Route;
use PDF;
use Excel;
use Log;
use DOMDocument;
use App\Exports\BladeExport;

class PatientbulkstatementApiController extends Controller {

    use ClaimUtil;
    /*     * * lists page Starts ** */
    
    // For listing of bulk statement start
    public function getBulkStatementPatListApi($export = '') {
        $psettings = PatientStatementSettings::first();        
        $practice_timezone = Helpers::getPracticeTimeZone();

        $currentdate = date('Y-m-d');      
        
        $request = Request::all();
        $start = isset($request['start']) ?  $request['start'] : 0;
        $len = isset($request['length']) ? $request['length'] : 50;

        // Get current week        
        $currentweek = $this->weekOfMonth($currentdate);        

        $patient_qry = Patient::with('patient_last_pmt','patient_budget')->leftjoin('patient_budget','patient_budget.patient_id','=','patients.id')->where('patients.id', '<>', 0);
		
        $patient_qry->leftjoin('claim_info_v1', function($join) {
            $join->on('claim_info_v1.patient_id', '=', 'patients.id');
            $join->on('claim_info_v1.status', '<>', DB::raw("'Hold'"));
        });
		
		
		
		
        
        /*
        $patient_qry->leftjoin(DB::raw("(SELECT
            pmt_info_v1.patient_id,
            pmt_amt as total_paid,
            CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."') as last_pmt_dt
            FROM pmt_info_v1
            WHERE pmt_info_v1.deleted_at IS NULL
            GROUP BY pmt_info_v1.patient_id
            ) as pmt_info_v1"), function($join) {
                $join->on('pmt_info_v1.patient_id', '=', 'patients.id');
        });
        */
               
        $patient_qry->join(DB::raw("(SELECT      
          pmt_claim_fin_v1.patient_id,pmt_claim_fin_v1.claim_id,claim_info_v1.status,     
          SUM(pmt_claim_fin_v1.patient_due) as patient_due
          FROM pmt_claim_fin_v1 JOIN claim_info_v1 on pmt_claim_fin_v1.claim_id = claim_info_v1.id
          WHERE pmt_claim_fin_v1.deleted_at IS NULL and claim_info_v1.status != 'Hold'
          GROUP BY pmt_claim_fin_v1.patient_id
          ) as pmt_claim_fin_v1"), function($join) {
            $join->on('pmt_claim_fin_v1.patient_id', '=', 'patients.id');
        });

        // Add financial charges with patient balance.
        //if ($psettings->financial_charge == '1') {
            $patient_qry->leftjoin('check_returns', function($join) {
                $join->on('check_returns.patient_id', '=', 'patients.id');
            });
        //}   

        if ($psettings->financial_charge == '1') {
            $patient_qry = $patient_qry->whereIn('facility_id', explode(",", $currentweekcycle));
        }

        // Check statement cycle
        if ($psettings->statementcycle == 'All') {
            //
        } elseif ($psettings->statementcycle == 'Billcycle') { // Check statement cycle is based by billcycle or not.
            $currentweekcolumn = 'week_' . $currentweek . '_billcycle';
            $currentweekcycle = $psettings->$currentweekcolumn;
            $patient_qry = $patient_qry->whereIn('bill_cycle', explode(",", $currentweekcycle));
        } elseif ($psettings->statementcycle == 'Facility') { // Check statement cycle is based by facility or not.
            $currentweekcolumn = 'week_' . $currentweek . '_facility';
            $currentweekcycle = $psettings->$currentweekcolumn;
            $patient_qry = $patient_qry->whereIn('facility_id', explode(",", $currentweekcycle));
        } elseif($psettings->statementcycle == 'Provider') { // Check statement cycle is based by provider or not.
            $currentweekcolumn = 'week_' . $currentweek . '_provider';
            $currentweekcycle = $psettings->$currentweekcolumn;
            $patient_qry = $patient_qry->whereIn('rendering_provider_id', explode(",", $currentweekcycle));
        } elseif ($psettings->statementcycle == 'Account') {  // Check statement cycle is based by account or not.
            $currentweekcolumn = 'week_' . $currentweek . '_account';
            $currentweekcycle = $psettings->$currentweekcolumn;
            $patient_qry = $patient_qry->whereBetween('account_no', explode(",", $currentweekcycle));
        } elseif ($psettings->statementcycle == 'Category') {     // Check statement cycle is based by category or not.
            $currentweekcolumn = 'week_' . $currentweek . '_category';
            // if no category assigned to current week show empty list condition added.            
            $currentweekcycle = ($psettings->$currentweekcolumn != '') ? $psettings->$currentweekcolumn : -1; 
            $patient_qry = $patient_qry->whereIn('stmt_category', explode(",", $currentweekcycle));
        }

        $patient_qry->where('statements', '<>', 'No')
                    ->where('statements', '<>', 'Hold')
                    ->where('statements', '<>', 'Insurance Only');

        $patient_qry = $patient_qry->selectRaw('DISTINCT(patients.id), 
            patients.id, patients.account_no, patients.last_name, patients.middle_name, patients.first_name,
            patients.dob, patients.gender, patients.address1, patients.city, patients.state, patients.phone, patients.work_phone, patients.statements_sent, patients.last_name, patients.statements, patients.stmt_category, patients.is_self_pay, patients.title, patients.zip5, patients.zip4, patients.email, patients.last_statement_sent_date, patient_budget.plan,

                CASE WHEN patients.last_statement_sent_date="00/00/00" THEN "-1"  ELSE DATEDIFF(NOW(), patients.last_statement_sent_date) END as stmt_days,
            
            pmt_claim_fin_v1.*,
            
            IFNULL(SUM(check_returns.financial_charges), 0) as pat_fin
            ');     
        // pmt_info_v1.*, 
        
        
        /*
        if ($statementtimedeley == 0) {
            if ($statementsentdate == '0000-00-00' || $statementsentdate == '') {
                $statement_download = 1;
            } else {
                // If date is not null, then get "last statement send date" to send the statement to the patient.
                if ($psettings->statementsentdays != '')
                    $getvalidstdate = date('Y-m-d', strtotime(date('Y-m-d') . ' -' . $psettings->statementsentdays . ' day'));

                $datetime1 = date_create($getvalidstdate);
                $datetime2 = date_create($statementsentdate);
                $interval = date_diff($datetime1, $datetime2);
                $validstdate = $interval->format('%R%a');

                if ($validstdate <= 0) {
                    $statement_download = 1;
                }
            }
        }
        */
        $patient_qry->groupBy('patients.id')->orderBy('patients.last_name')->orderBy('patients.first_name');

        if ($psettings->financial_charge == '1') {
            if ($psettings->minimumpatientbalance != '0' ) {
                $patient_qry = $patient_qry->havingRaw('patient_due + IFNULL(SUM(check_returns.financial_charges), 0)  > '.$psettings->minimumpatientbalance);
            } else {
                $patient_qry = $patient_qry->havingRaw('patient_due + IFNULL(SUM(check_returns.financial_charges), 0)  > 0');    
            }            
        } else {
            if ($psettings->minimumpatientbalance != '0' ) {
                $patient_qry = $patient_qry->havingRaw('patient_due > '.$psettings->minimumpatientbalance);
            } else {
                $patient_qry = $patient_qry->havingRaw('patient_due > 0');
            }    
        }
        //$patients = $patients->havingRaw('(SELECT SUM(wallet.amount) FROM pmt_wallet_v1 wallet WHERE wallet.patient_id = patients.id and deleted_at is null) != 0');
		
		$patientBudgetInfo = PatientBudget::where('status','Active')->select('patient_id')->pluck('patient_id')->toArray();

		$patientWeeklyInfo = PatientBudget::where('plan','Weekly')->where('status','Active')->WhereNull('last_statement_sent_date')->orWhereRaw('DATEDIFF(NOW(), last_statement_sent_date) > 7')->select('patient_id')->pluck('patient_id')->toArray();
		
		$patientBiWeeklyInfo = PatientBudget::where('plan','Biweekly')->where('status','Active')->WhereNull('last_statement_sent_date')->orWhereRaw('DATEDIFF(NOW(), last_statement_sent_date) > 14')->select('patient_id')->pluck('patient_id')->toArray();
		
		$patientMonthInfo = PatientBudget::where('plan','Monthly')->where('status','Active')->WhereNull('last_statement_sent_date')->orWhereRaw('DATEDIFF(NOW(),last_statement_sent_date) > 30')->select('patient_id')->pluck('patient_id')->toArray();
		
		$patientBiMonthInfo = PatientBudget::where('plan','Bimonthly')->where('status','Active')->WhereNull('last_statement_sent_date')->orWhereRaw('DATEDIFF(NOW(),last_statement_sent_date) > 60')->select('patient_id')->pluck('patient_id')->toArray(); 
		
		$budgetArr = array_merge($patientWeeklyInfo, $patientBiWeeklyInfo, $patientMonthInfo);
		
		
		if ($psettings->statementsentdays != '') {
			$stDays = $psettings->statementsentdays;
			$patientSentInfo = Patient::whereNotIn('id',$patientBudgetInfo)->where('last_statement_sent_date', '0000-00-00')->orWhere('last_statement_sent_date', '')->orWhereRaw('DATEDIFF(NOW(), last_statement_sent_date) > '. $stDays)->select('id')->pluck('id')->toArray();
		}else{
			$patientSentInfo = [];
		}
			
		$patientArr = array_merge($budgetArr, $patientSentInfo);
		
		
		$patient_qry->whereIn('patients.id',$patientArr);
		
		/* if ($psettings->statementsentdays != '') {
            $stDays = $psettings->statementsentdays;
            $patient_qry->Where(function ($patient_qry) use ($stDays) {
                $patient_qry->where('patients.last_statement_sent_date', '0000-00-00')
                            ->orWhere('patients.last_statement_sent_date', '')
                            ->orWhereRaw('DATEDIFF(NOW(), patients.last_statement_sent_date) > '. $stDays);
            });
        } */
		
        $cntObj = clone $patient_qry;

        $count = count($cntObj->get());
        if($export !=''){
            $patients_arr = $patient_qry->get();
        }else{
            $patient_qry->skip($start)->take($len);
            $patients_arr = $patient_qry->get();
        }
         
        // dd($patients_arr->toArray());   
        if (count((array)$patients_arr) > 0) {
            /*
            foreach ($patients_arr as $patient_value) {
               // \Log::info($patient_value);
            }
            */
        }       
        
        $patient_balance = [];
        
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact(
            'psettings','patients_arr','patient_balance','count')));
    }
    // For listing of bulk statement end

    public function getIndexApi() {
        $psettings = PatientStatementSettings::first();
        $bulkstatementlist = $this->getBulkStatementListApi('patientlisting');
        $insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();

        // Get patient list and patient balance
        $patient_balance = $patients_arr = [];        
        $patLastPmtArr = $patBalArr = $finChrArr = [];

        if (count((array)$bulkstatementlist) > 0 and $bulkstatementlist != 'failure') {
            $patLastPmt = DB::table('claim_info_v1')
                        ->join('pmt_claim_tx_v1', 'claim_info_v1.id', '=', 'pmt_claim_tx_v1.claim_id')
                        ->join('pmt_info_v1', 'pmt_claim_tx_v1.payment_id', '=', 'pmt_info_v1.id')
                        ->select('pmt_claim_tx_v1.created_at', 'pmt_info_v1.pmt_amt', 'claim_info_v1.patient_id')
                        ->whereIn('claim_info_v1.patient_id', $bulkstatementlist)
                        ->where('claim_info_v1.payment_hold_reason', '!=', 'Patient')
                        ->where('pmt_claim_tx_v1.pmt_type', 'Payment')
                        ->where('pmt_claim_tx_v1.pmt_method', 'Patient')
                        ->where('claim_info_v1.status', '!=', 'Hold')
                        ->where('claim_info_v1.status', '!=', 'E-bill')
                        ->where('pmt_claim_tx_v1.total_paid', '!=', '0.00')
                        ->groupBy('claim_info_v1.patient_id')
                        ->orderBy('pmt_claim_tx_v1.id', 'desc')
                        ->get();
            
            $patLastPmtArr = collect($patLastPmt)->keyBy('patient_id')->toArray();
                        //->pluck('det','claim_info_v1.patient_id')
                        //->keyBy('claim_info_v1.patient_id')->get()->toArray();
            
            $patBalArr = PMTClaimFINV1::select(DB::raw("SUM(patient_due) as pat_due"), "patient_id")
                        ->whereIn('patient_id', $bulkstatementlist)
                        ->groupBy('patient_id')
                        ->pluck('pat_due','patient_id')->all();

            if ($psettings->financial_charge == '1') {            
                $finChrArr = CheckReturn::select(DB::raw("SUM(financial_charges) as fin"), "patient_id")
                            ->whereIn('patient_id', $bulkstatementlist)
                            ->groupBy('patient_id')
                            ->pluck('fin','patient_id')->all();
            }
                       
            $patDetArr = Patient::whereIn('id', $bulkstatementlist)->get()->keyBy('id');

            foreach ($bulkstatementlist as $patient_value) {
                if(isset($patBalArr[$patient_value]))
                    $getpatient_balance = $patBalArr[$patient_value];
                 else   
                    $getpatient_balance = Helpers::getPatientBalance($patient_value, 'patient_balance');

                //to do PAyment
                //$getpatient_balance  = ClaimInfoV1::getBalance($patient_value,'patient_balance');
                if ($psettings->financial_charge == '1') {
                    if(isset($finChrArr[$patient_value]))
                        $financial_charges = $finChrArr[$patient_value];
                    else    
                        $financial_charges = CheckReturn::where('patient_id', $patient_value)->sum('financial_charges');
                    $getpatient_balance = $financial_charges + $getpatient_balance;
                }

                if ($getpatient_balance != '')
                    $patient_balance[$patient_value]['balance'] = $getpatient_balance;
                /*
                // Show patient latest payment info.
                $patient_latestpayment = DB::table('claim_info_v1')
                        ->join('pmt_claim_tx_v1', 'claim_info_v1.id', '=', 'pmt_claim_tx_v1.claim_id')
                        ->join('pmt_info_v1', 'pmt_claim_tx_v1.payment_id', '=', 'pmt_info_v1.id')
                        ->select('pmt_claim_tx_v1.created_at', 'pmt_info_v1.pmt_amt')
                        ->where('claim_info_v1.patient_id', $patient_value)
                        ->where('claim_info_v1.payment_hold_reason', '!=', 'Patient')
                        ->where('pmt_claim_tx_v1.pmt_type', 'Payment')
                        ->where('pmt_claim_tx_v1.pmt_method', 'Patient')
                        ->where('claim_info_v1.status', '!=', 'Hold')
                        ->where('claim_info_v1.status', '!=', 'E-bill')
                        ->where('pmt_claim_tx_v1.total_paid', '!=', '0.00')
                        ->orderBy('pmt_claim_tx_v1.id', 'desc')
                        ->first();
                
                $patient_balance[$patient_value]['lastpayment'] = '0';
                $patient_balance[$patient_value]['lastpaymentdate'] = '';
                if (!empty($patient_latestpayment)) {
                    $patient_balance[$patient_value]['lastpayment'] = $patient_latestpayment->pmt_amt;
                    $patient_balance[$patient_value]['lastpaymentdate'] = $patient_latestpayment->created_at;
                }
                */

                $patient_balance[$patient_value]['lastpayment'] = '0';
                $patient_balance[$patient_value]['lastpaymentdate'] = '';
                if(isset($patLastPmtArr[$patient_value])) {
                    $patient_latestpayment = $patLastPmtArr[$patient_value];
                    $patient_balance[$patient_value]['lastpayment'] = $patient_latestpayment->pmt_amt;
                    $patient_balance[$patient_value]['lastpaymentdate'] = $patient_latestpayment->created_at;
                }

                if(isset($patDetArr[$patient_value])){
                    $patients_arr[$patient_value] = $patDetArr[$patient_value];
                } else {                    
                    $patDetails = Patient::where('id', $patient_value)->first();
                    if (!empty($patDetails))
                        $patients_arr[$patient_value] = $patDetails;
                }
            }
        }            
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('psettings', 'patients_arr', 'patient_balance', 'insurances')));
    }

    /*     * * Lists Function Ends ** */

    public function weekOfMonth($date) {
        // estract date parts
        list($y, $m, $d) = explode('-', date('Y-m-d', strtotime($date)));
        // current week, min 1
        $w = 1;

        // for each day since the start of the month
        for ($i = 1; $i <= $d; ++$i) {
            // if that day was a sunday and is not the first day of month
            if ($i > 1 && date('w', strtotime("$y-$m-$i")) == 0) {
                // increment current week
                ++$w;
            }
        }
        // now return
        return $w;
    }

    public function getStoreApi() {
        
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        
        $request = Request::all();

        $paymentmessage = (isset($request['paymentmessage_1'])) ? $request['paymentmessage_1'] : 0;
        $sendtype = $request['sendtype'];
        
        $patientarray = Patient::getAllpatients();
        $patient_ids = array_unique(explode(',', $request['patient_ids']));
		
        $collect_file_path = [];
        $collect_patientinfo = [];
        $mailsentstatus = 0; 
        foreach ($patient_ids as $pid) {
            if ($sendtype == 'Email Statement') {
                $patientinfo = Patient::where('id', $pid)->first();
                $collect_patientinfo[$pid] = $patientinfo;
                 $download_type = 'pdf';
                if ($patientinfo->email != '') {
                    $get_message = $this->sendPatientStatement($pid, 'Emailstatement', $paymentmessage, $download_type);
                    $collect_file_path[$pid] = $get_message['message'];
                    $mailsentstatus = 1;
                }
            } else {                
                if(strtolower(trim($sendtype)) == 'send xml statement' ) {
                    $download_type = 'xml';
                } else {
                    $download_type = (strtolower(trim($sendtype)) == 'send csv statement') ? 'csv' : 'pdf';    
                }                
                $get_message = $this->sendPatientStatement($pid, 'Sendstatement', $paymentmessage, $download_type);
                $collect_file_path[$pid] = $get_message['message'];
            }
        }

        // Patient have no email ids.
        if ($sendtype == 'Email Statement' && $mailsentstatus == 0) {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("practice/practicemaster/patientstatementsettings.validation.mailstatementerrormsg")));
        }

        $files = array_filter($collect_file_path);

        // Generate Zip file.
        if (count((array)$files) > 0) {
            if ($sendtype == 'Email Statement') {
                foreach ($files as $key => $filess) {
                    $this->sendEmailPatientStatement($key, $filess, $collect_patientinfo);
                }
                return Response::json(array('status' => 'success', 'message' => Lang::get("practice/practicemaster/patientstatementsettings.validation.mailstatement")));
            } else {
                $get_unique_no = date("m-d-Y");

                if (is_dir('ps')) {
                    $this->deleteDirectory('ps');
                }
                if (!file_exists('ps'))
                    mkdir('ps');

                $currentdate = date('Y-m-d');
                $contextOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    )
                );

                if(trim(strtolower($download_type)) == 'xml') {

                    //Creates XML string and XML document using the DOM     
                    if (App::environment() == Config::get('siteconfigs.production.defult_production'))
                        $path_medcubic = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
                    else
                        $path_medcubic = public_path() . '/';

                    $xmlfile = $path_medcubic . 'media/patientstatement/ps_sample.xml';

                    $target = new DOMDocument();
                    $target->formatOutput = true;
                    $target->load($xmlfile);
                    // get 'res' element of document 1
                    $resp = $target->getElementsByTagName('statements')->item(0);                   
                    // iterate the source files array
                    foreach ($files as $name => $content) {
                      // load each source
                      $source = new DOMDocument();
                      $source->formatOutput = true;
                      $source->preserveWhiteSpace = true; 
                      $source->load($content);
                      // if it has a document element
                      if ($source->documentElement) {
                        // copy it to the target document
                        $resp = $source->getElementsByTagName('statement')->item(0);
                        // Import the node, and all its children, to the document
                        $node = $target->importNode($resp, true);                        
                        $tag = $target->getElementsByTagName('statements')->item(0);
                        // And then append it to the "<root>" node
                        $tag->appendChild($node);                        
                      }
                    }                    

                    $target->formatOutput = TRUE;
                    $target->preserveWhiteSpace = false;  
                    $target->formatOutput = true;                  
                    $opFile = $path_medcubic . 'media/patientstatement/'. $get_unique_no .'.xml';
                    $target->save($opFile);

                    // Create Zip
                    $outputzippath = 'ps/patientstatement_' . $get_unique_no . '.zip';
                    $zipname = 'patientstatement_' . $get_unique_no . '.zip';
					ob_end_clean();
                    $zip = new ZipArchive;
                    $zip->open($outputzippath, ZipArchive::CREATE);
                    
                    $content = file_get_contents($opFile, false, stream_context_create($contextOptions));
                    $zip->addFromString("patient_statment_" . $currentdate . '.'.$download_type, $content);
                    $zip->close();

                    header('Content-Type: application/zip');
                    header('Content-disposition: attachment; filename=' . $zipname);
                    header('Content-Length: ' . filesize($outputzippath));
                    readfile($outputzippath);
                    exit(); 

                } else {
                    $outputzippath = 'ps/patientstatement_' . $get_unique_no . '.zip';
                    $zipname = 'patientstatement_' . $get_unique_no . '.zip';
					ob_end_clean();
                    $zip = new ZipArchive;
                    $zip->open($outputzippath, ZipArchive::CREATE);
                    $i = 1;
                    foreach ($files as $key => $filess) {
                        if (isset($patientarray[$key])) {                            
                            $content = file_get_contents($filess, false, stream_context_create($contextOptions)); 
                            
                            //$content = file_get_contents($filess);
                            //$zip->addFromString($patientarray[$key] . $i . $currentdate . '.pdf', $content);
                            
                            $zip->addFromString($patientarray[$key] . $i . $currentdate . '.'.$download_type, $content);
                        }
                        //$zip->addFile($filess,$patientarray[$key].'.pdf');	
                        $i++;
                    }

                    $zip->close();

                    header('Content-Type: application/zip');
                    header('Content-disposition: attachment; filename=' . $zipname);
                    header('Content-Length: ' . filesize($outputzippath));
                    readfile($outputzippath);
                    exit(); 
                }
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("practice/practicemaster/patientstatementsettings.validation.bulkstatementmsg")));
        }
    }

    /*     * * Store Function Starts ** */

    public function getBulkStatementListApi($listtype = '', $passweek = '', $patientid = '') {
        $psettings = PatientStatementSettings::first();
        //$patientarray = Patient::getAllpatients();
        $currentdate = date('Y-m-d');
        $get_patient = array();

        // Get current week
        if ($passweek == '')
            $currentweek = $this->weekOfMonth($currentdate);
        else
            $currentweek = $passweek;

        // Check statement cycle
        if ($psettings->statementcycle == 'All') {
            $get_patient = ClaimInfoV1::pluck('patient_id')->all();
        }elseif ($psettings->statementcycle == 'Billcycle') { // Check statement cycle is based by billcycle or not.
            $currentweekcolumn = 'week_' . $currentweek . '_billcycle';
            $currentweekcycle = $psettings->$currentweekcolumn;
            $get_patient = ClaimInfoV1::whereHas('patient', function($q)use($currentweekcycle) {
                        $q->whereIn('bill_cycle', explode(",", $currentweekcycle));
                    })->get()->pluck('patient_id')->all();
        } elseif ($psettings->statementcycle == 'Facility') { // Check statement cycle is based by facility or not.
            $currentweekcolumn = 'week_' . $currentweek . '_facility';
            $currentweekcycle = $psettings->$currentweekcolumn;
            $get_patient = ClaimInfoV1::with('patient')->whereIn('facility_id', explode(",", $currentweekcycle))->get()->pluck('patient_id')->all();
        }elseif($psettings->statementcycle == 'Provider') { // Check statement cycle is based by provider or not.
            $currentweekcolumn = 'week_' . $currentweek . '_provider';
            $currentweekcycle = $psettings->$currentweekcolumn;
            $get_patient = ClaimInfoV1::with('patient')->whereIn('rendering_provider_id', explode(",", $currentweekcycle))->get()->pluck('patient_id')->all();
        } elseif ($psettings->statementcycle == 'Account') {  // Check statement cycle is based by account or not.
            $currentweekcolumn = 'week_' . $currentweek . '_account';
            $currentweekcycle = $psettings->$currentweekcolumn;

            $get_patient = ClaimInfoV1::whereHas('patient', function($q)use($currentweekcycle) {
                        $q->whereBetween('account_no', explode(",", $currentweekcycle));
                    })->get()->pluck('patient_id')->all();
        } elseif ($psettings->statementcycle == 'Category') {     // Check statement cycle is based by category or not.
            $currentweekcolumn = 'week_' . $currentweek . '_category';
            // if no category assigned to current week show empty list condition added.            
            $currentweekcycle = ($psettings->$currentweekcolumn != '') ? $psettings->$currentweekcolumn : -1; 
            $get_patient = ClaimInfoV1::whereHas('patient', function($q)use($currentweekcycle) {
                        $q->whereIn('stmt_category', explode(",", $currentweekcycle));
                    })->get()->pluck('patient_id')->all();
        }

        // Check patient statement is available or not for particular patient.
        if ($passweek != '') {
            if (in_array($patientid, $get_patient)) {
                $get_patient = array($patientid);
            } else { 
                $get_patient = array();
            }
        }
        // Collect valid patient record.

        if (count((array)$get_patient) > 0) {
            $get_unique_patient = array_unique($get_patient);

            // check unvalid patient id in claim table.
            if (($key = array_search(0, $get_unique_patient)) !== false) {
                unset($get_unique_patient[$key]);
            }

            $patSt = Patient::whereIn('id', $get_unique_patient)
                      ->select('statements', 'last_statement_sent_date', 'id')  
                    //->selectRaw('CONCAT(statements,"@@##@@",last_statement_sent_date) as det, id')
                    ->get()
                    //->pluck('det','id')->all();
                    ->keyBy('id')->toArray();

            $patBalArr = PMTClaimFINV1::select(DB::raw("SUM(patient_due) as pat_due"), "patient_id")
                        ->whereIn('patient_id', $get_unique_patient)
                        ->groupBy('patient_id')
                        ->pluck('pat_due','patient_id')->all();

            $collect_file_path = [];
            //$psettings = PatientStatementSettings::first();
            foreach ($get_unique_patient as $patient_id) { 
                //if (Patient::where('id', $patient_id)->count() > 0) {
                if(isset($patSt[$patient_id])) {
                    $pDet = $patSt[$patient_id];

                    $statements = isset($pDet['statements']) ? $pDet['statements'] : ''; 
                    $details = [];
                    $details['statment_setting'] = $psettings;
                    $details['patSt'] = $statements;
                    $details['patLastStDate'] = isset($pDet['last_statement_sent_date']) ? $pDet['last_statement_sent_date'] : '';
                    $details['patBalDet'] = isset($patBalArr[$patient_id])?$patBalArr[$patient_id]:'';

                    $get_message['status'] = 'no';
                    //if ($statements->statements != 'No' && $statements->statements != 'Hold' && $statements->statements != 'Insurance Only') {                    
                    if ($statements != 'No' && $statements != 'Hold' && $statements != 'Insurance Only') { 
                        // get patient ids based on bulk statement for patient listing.
                        $get_message = $this->checkPatientStatement($patient_id, 'patientlisting', 'bulk', 'pdf', $details);
                    }
                    // Create PDF for valid patient.					
                    if ($get_message['status'] == 'success') {
                        $collect_file_path[$patient_id] = $get_message['message'];
                    }
                }
            }
            $files = array_filter($collect_file_path);

            // Get the patient bulk statement records.
            if ($listtype == 'patientlisting')
                return $files;
        } else { 
            return 'failure';
        }
    }

    public function sendEmailPatientStatement($pid, $filepath, $patient_arr) {
        if (EmailTemplate::where('template_for', 'patientstatement')->count() > 0) {
            $templates = EmailTemplate::where('template_for', 'patientstatement')->first();
            $get_Email_Template = $templates->content;

            $get_newfilepreview = explode('media/',$filepath);
            $collect_pdf = url('media/'.$get_newfilepreview[1]);

            $arr = [
                "##LASTNAME##" => $patient_arr[$pid]->last_name,
                "##FIRSTNAME##" => $patient_arr[$pid]->first_name,
                "##STATEMENTDOWNLINK##" => '<a href="'.$collect_pdf.'" target="_blank" style="padding:15px 30px; margin-top:30px; border-radius:4px;"><button class="btn btn-medcubics" type="button">Download Statement</button></a>'
            ];

            $get_newfile = explode('patientstatement/', $filepath);
            $get_filename = explode('/', $get_newfile[1]);

            $email_content = strtr($get_Email_Template, $arr);
            $res = array('email' => $patient_arr[$pid]->email,
                'subject' => $templates->subject,
                'msg' => $email_content,
                'name' => @$patient_arr[$pid]->last_name . ' ' . $patient_arr[$pid]->first_name,
                'attachment' => $filepath,
                'attachment_as' => array_pop($get_filename),
                'attachment_mime' => 'text/pdf'
            );

            CommonApiController::connectEmailApi($res);
        }
    }

    public function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }

    public function deletepreview() {
        $mins = Config::get('siteconfigs.patientstatement.removepreviewinminutes');
        $seconds = $mins * 60;

        $path = 'media/patientstatement/';
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ((time() - filectime($path . $file)) >= $seconds) {
                    if (preg_match('/\.pdf$/i', $file)) {
                        @unlink($path . $file);
                    }
                }
            }
        }
    }

    public function checkPatientStatement($patient_id, $mode = '', $statement_type = "", $file_type='pdf', $details = []) {
        
        if(isset($details['statment_setting'])) {
            $psettings = $details['statment_setting'];
        } else {
            $psettings = PatientStatementSettings::first();    
        }
        /*if(isset($details['patient_det'])) {
            $patients = $details['patient_det'];
        } else {            
            $patients = Patient::where('id', $patient_id)->first();
        }
        */
        
        if (is_dir('media/patientstatement')) {
            $this->deletepreview();
        }
        $statement_download = 0;

        if(isset($details['patBalDet'])) {
            $patient_balance = $details['patBalDet'];
        } else {            
            $patient_balance = Helpers::getPatientBalance($patient_id, 'patient_balance', 0);
        }
        if(isset($details['patLastStDate'])) {
            $statementsentdate = $details['patLastStDate'];
        } else {
            $patients = Patient::where('id', $patient_id)->select('last_statement_sent_date')->first();
            $statementsentdate = $patients->last_statement_sent_date;
        }

        // to do Payment
        // Check amount in the place.
        //$patient_balance 	  = ClaimInfoV1::getBalance($patient_id,'patient_balance');   
        //Get last statement sent date, If patient have budget plan then get date in budget plan table otherwise in patient table.
        $statementtimedeley = 0;
        /*
        if (PatientBudget::where('patient_id', $patient_id)->count() > 0) {
            $get_budgetstatementdate = PatientBudget::where('patient_id', $patient_id)->first();
            $date1 = date_create($get_budgetstatementdate->statement_start_date);
            $date2 = date_create(date("Y-m-d"));
            $diff = date_diff($date1, $date2);
            $get_start_statement = $diff->format("%R%a");

            if ($get_start_statement >= 0) {
                $statementsentdate = $get_budgetstatementdate->last_statement_sent_date;
            } else {
                $statementtimedeley = 1;
            }
        } else {
            $statementsentdate = $patients->last_statement_sent_date;
        }
        */
        

        // Check date is null or not.
        // Statment start date is not reached	
        if ($statementtimedeley == 0) {
            if ($statementsentdate == '0000-00-00' || $statementsentdate == '') {
                $statement_download = 1;
            } else {
                // If date is not null, then get "last statement send date" to send the statement to the patient.
                if ($psettings->statementsentdays != '')
                    $getvalidstdate = date('Y-m-d', strtotime(date('Y-m-d') . ' -' . $psettings->statementsentdays . ' day'));

                $datetime1 = date_create($getvalidstdate);
                $datetime2 = date_create($statementsentdate);
                $interval = date_diff($datetime1, $datetime2);
                $validstdate = $interval->format('%R%a');

                if ($validstdate <= 0) {
                    $statement_download = 1;
                }
            }
        }

        if ($statement_type == 'bulk') {
            // Add financial charges with patient balance.
            if ($psettings->financial_charge == '1') {
                $financial_charges = CheckReturn::where('patient_id', $patient_id)->sum('financial_charges');
                $patient_balance = $financial_charges + $patient_balance;
            }

            // Check patient balance is minimum or not based on settings.
            if ($psettings->minimumpatientbalance != '0' && $patient_balance <= $psettings->minimumpatientbalance) {
                return array('status' => 'failure', 'message' => Lang::get("practice/practicemaster/patientstatementsettings.validation.lowpatientbalance"));
            }

            // Continue the process if patient balance is not nil.
            if ($statement_download == 1 && ($patient_balance > 0)) {
                return array('status' => 'success', 'message' => $patient_id);
            }
        } else {
            // Check Individual in bulk statement (statement type = payment_message)
            return $this->sendPatientStatement($patient_id, $mode, $statement_type, $file_type);
        }
    }

    public function sendPatientStatement($patient_id, $mode, $paymentmessage = '', $file_type='pdf') {
        if($patient_id == ''){
            return '';
        }
        $file_type = ($mode == 'preview') ? 'pdf' : $file_type;
		
        $psettings = PatientStatementSettings::first();
        //to do Payment with hidden payment_claimdetail() -- claim_unit_details     cpttransactiondetails
        $claims = ClaimInfoV1::with(['rendering_provider', 'facility_detail', 'cpttransactiondetails', 'dueClaimFin', 'payment_claimdetail' => function($q) {
                        $q->where('pmt_type', 'Adjustment');
                    }])
                    ->where('patient_id', $patient_id)
                    ->where('payment_hold_reason', '!=', 'Patient')
                    ->whereNotIn('status', ['Hold', 'E-bill']);

        if($psettings->insurance_balance == 0) {
           $claims = $claims->where('insurance_id', 0); 
        }
        $claims = $claims->get();
        
        $patients = Patient::with('patient_budget', 'patient_statement_note')->where('id', $patient_id)->first();
       
        $patient_guarantor_address = PatientContact::where('patient_id', $patient_id)->where('category', 'Guarantor')->first();
		
		$patient_other_address = PatientOtherAddress::where('patient_id', $patient_id)->where('status', 'Active')->first();		
        $icddetail = $icddetail_code = [];
        // Get ICD details from ICD ID in patient claim.
        $get_icd_id_collection = '';
        foreach ($claims as $key => $icdvalueid) {
            $get_icd_id_collection .= $icdvalueid->icd_codes . ',';
        }

        $get_icd_id_collection = rtrim($get_icd_id_collection, ',');

        $get_icddetail = Icd::whereIn('id', explode(',', $get_icd_id_collection))->get();
        foreach ($get_icddetail as $key => $icdvalue) {
            if ($icdvalue->id != '') {
                $icddetail[$icdvalue->id] = $icdvalue;
            }
        }

        // Get ICD details from ICD Code in patient claim.
        $get_claimdosdetails = ClaimCptInfoV1::where('patient_id', $patient_id)->get();
        $get_icd_code = $get_cpt_code = '';
        if (!empty($get_claimdosdetails)) {
            foreach ($get_claimdosdetails as $key => $claimvalue) {
                $get_icd_code .= $claimvalue->cpt_icd_code . ',';
                $get_cpt_code .= $claimvalue->cpt_code . ',';
            }
        }
        $get_icd_code_collection = rtrim($get_icd_code, ',');
        $get_cpt_code_collection = rtrim($get_cpt_code, ',');

        //$get_icddetail  = Icd::on("responsive")->whereIn('icd_code', explode(',',$get_icd_code_collection))->get();
        $get_icddetail = Icd::whereIn('icd_code', explode(',', $get_icd_code_collection))->get();
        foreach ($get_icddetail as $key => $icdcodevalue) {
            if ($icdcodevalue->icd_code != '') {
                $icddetail_code[$icdcodevalue->icd_code] = $icdcodevalue;
            }
        }

        // Get CPT details from CPT Code in patient claim.
        $get_cptdetail = Cpt::whereIn('cpt_hcpcs', array_unique(explode(',', $get_cpt_code_collection)))->get();
        $cptdetail_code = [];
        foreach ($get_cptdetail as $key => $cptcodevalue) {
            if ($cptcodevalue->cpt_hcpcs != '') {
                $cptdetail_code[$cptcodevalue->cpt_hcpcs] = $cptcodevalue;
            }
        }

        $practice = Practice::first();
        $patients->aging = Patient::getAgingForSTMT($patient_id);
        
        $financial_charges = CheckReturn::where('patient_id', $patient_id)->sum('financial_charges');
        $res = Patient::paymentclaimARForStmt($patient_id); 
        $patient_balance = @$res['patient_balance']; 
        $insurance_balance = @$res['insurance_balance'];         
        $claim_id_collection = @$res['claim_id_collection']; 
        $total_ar_collection = @$res['total_ar_collection']; 

        ## get created at with timezone for insert last payment date in "patientstatement_track" table  
        ## patient - statments module module 
        $practice_timezone = Helpers::getPracticeTimeZone();    
        $patient_latestpayment = DB::table('pmt_info_v1')->where('patient_id', $patient_id)
                        ->where('pmt_method', 'Patient')
                        ->where('pmt_type', '<>', 'Adjustment')
                        ->orderBy('created_at', 'DESC')->select("pmt_amt as patient_paid",DB::raw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) as created_at"), "pmt_mode")->first();

        $current_date = Helpers::timezone(date('Y-m-d H:i:s'),'Y-m-d');
        $get_budgetamount = [];
        
        $get_budgetamount = PatientBudget::where('patient_id', $patient_id)->first();
       
         if (count((array)$patients->patient_budget) > 0 && $mode != 'preview') {

            // Update patient statement download date 
            PatientBudget::whereNull('deleted_at')->where('patient_id', $patient_id)->update(['last_statement_sent_date' => date('Y-m-d H:i:s')]);
        }
        if($file_type != 'csv' && $file_type != 'xls') {

            if($file_type == 'xml') {
                $view = View::make('patients/patients/patientstatement_xml', compact('psettings', 'claims', 'patients', 'patient_guarantor_address', 'patient_other_address', 'practice', 'icddetail', 'icddetail_code', 'patient_balance', 'insurance_balance', 'patient_latestpayment', 'mode', 'cptdetail_code', 'financial_charges', 'get_budgetamount', 'paymentmessage'));
            } else {
                $view = View::make('patients/patients/patientstatement', compact('psettings', 'claims', 'patients', 'patient_guarantor_address', 'patient_other_address', 'practice', 'icddetail', 'icddetail_code', 'patient_balance', 'insurance_balance', 'patient_latestpayment', 'mode', 'cptdetail_code', 'financial_charges', 'get_budgetamount', 'paymentmessage', 'file_type'));
            }       
            $contents = $view->render(); 
        }

        if ($patient_id != '') {
            if (is_numeric($patient_id))
                $encodepatient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
            $fName = @$patients->last_name."".@$patients->first_name."".@$patients->middle_name."_".$current_date."_".time();
            if($file_type == 'pdf'){
                //$filename = 'patientstatement_' . $patient_id . time() . '.pdf';
                $filename = $fName.'.pdf';
            } elseif($file_type == 'xml'){
                 //dd($view->render());
                //$filename = 'patientstatement_' . $patient_id . time() . '.xml';
                $filename = $fName. '.xml';
            }  else {
                //$filenamewoExt = 'patientstatement_' . $patient_id . time();
                $filenamewoExt = $fName;
                //$filename = 'patientstatement_' . $patient_id . time() . '.csv';
                $filename = $fName. '.csv';
            }
        }

        if (App::environment() == Config::get('siteconfigs.production.defult_production'))
            $path_medcubic = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
        else
            $path_medcubic = public_path() . '/';

        $path = $path_medcubic . 'media/patientstatement/';

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $path_with_filename = $path . $filename;

        if($file_type == 'pdf') {           
            $type = '.pdf';
            $path = storage_path('app/Report/exports/');
            PDF::loadHTML($view, 'A4')->save($path_with_filename);
        } elseif($file_type == 'xls' || $file_type == 'csv') {                 
            // Before start donwload clear the buffer
            ob_end_clean();
            ob_start(); //At the very top of your program (first line)             
            $filePath = 'patients/patients/patientstatement_excel';
            $data['psettings'] = $psettings;
            $data['claims'] = $claims;
            $data['patients'] = $patients;
            $data['patient_guarantor_address'] = $patient_guarantor_address;
			$data['patient_other_address'] = $patient_other_address;
            $data['practice'] = $practice;
            $data['icddetail'] = $icddetail;
            $data['icddetail_code'] = $icddetail_code;
            $data['patient_balance'] = $patient_balance;
            $data['insurance_balance'] = $insurance_balance;
            $data['patient_latestpayment'] = $patient_latestpayment;
            $data['mode'] = $mode;
            $data['cptdetail_code'] = $cptdetail_code;
            $data['financial_charges'] = $financial_charges;
            $data['get_budgetamount'] = $get_budgetamount;
            $data['paymentmessage'] = $paymentmessage;
            $data['file_type'] = $file_type;
            //dd($filePath);
            libxml_use_internal_errors(true);
            Excel::store(new BladeExport($data,$filePath), 'media/patientstatement/'.$filename, 'patst');

            ob_flush();                        
        } elseif($file_type == 'xml' ) {
            try{
                if($contents != '') {
                    $dom = new DOMDocument;
                    $dom->preserveWhiteSpace = false;
                    $dom->formatOutput = true;
                    $dom->loadXML($contents);
                    //Save XML as a file
                    $dom->save($path_with_filename);
                }
            } catch(Exception $e){
                \Log::info("While generating xml getting error. Msg: ".$e->getMessage());
            }            
        }              
		
        if ($path_with_filename != '') {
            $get_laststatmentsentcount = $patients->statements_sent + 1;

            // Update patient statement download date and statement count
            if ($mode != 'preview') { 
                Patient::where('id', $patient_id)->update(['last_statement_sent_date' => date('Y-m-d H:i:s'), 'statements_sent' => $get_laststatmentsentcount]);
            	$date = date('Y-m-d H:i:s');
                $paybydate = date('Y-m-d', strtotime($date . ' +' . $psettings->paybydate . ' day'));
                
                // Store patient statement download details.
                $pttrack = new PatientStatementTrack;
                $pttrack->patient_id = $patient_id;
                $pttrack->claim_id_collection = $claim_id_collection;
                $pttrack->total_ar_collection = $total_ar_collection;
                $pttrack->pay_by_date = $paybydate;
                $pttrack->send_statement_date = $date; 
                // To handle comma separated value issue on store balance issue.
                if(isset($patient_balance)) {
                    $patient_balance = (float) (str_replace(',', '', $patient_balance));
                    $pttrack->balance = $patient_balance;
                } else {
                    $pttrack->balance = 0;
                }     
                
                $pttrack->statements = $get_laststatmentsentcount;

                if (count((array)$patient_latestpayment) > 0) {
                    $pttrack->latest_payment_amt = (isset($patient_latestpayment->patient_paid_amt)) ? @$patient_latestpayment->patient_paid_amt : 0.00;
                    $pttrack->latest_payment_date = $patient_latestpayment->created_at;
                }                
                $pttrack->type_for = (strtolower($mode) == 'sendstatement' || strtolower($mode) == 'sendcsvstatement' || strtolower($mode) == 'sendxmlstatement') ? 'Paper' : 'Email';
                $pttrack->created_by = Auth::user()->id;
                $pttrack->save();
            }
            return array('status' => 'success', 'message' => $path_with_filename);
        }
    }

    function __destruct() {
        
    }

}