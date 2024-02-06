<?php

namespace App\Http\Controllers\Patients\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Payments\PMTInfoV1;
use App\Models\PatientStatementTrack;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Response;
use DB;

class PatientWalletHistoryApiController extends Controller {

    public function getIndexApi($id, $export = '') {
        $practice_timezone = Helpers::getPracticeTimeZone();  
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $payments = PMTInfoV1::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with(['checkDetails','creditCardDetails', 'eftDetails','insurancedetail', 'created_user'])
                ->where('pmt_method', 'Patient')
                ->where('patient_id', $id)
                ->whereIn('pmt_type', ['Payment', 'Refund', 'Credit Balance'])
                ->where('void_check', NULL)
                ->where('pmt_amt', '>', 0)
                ->get();
        $statments = PatientStatementTrack::select('*', DB::raw('CONVERT_TZ(send_statement_date,"UTC","'.$practice_timezone.'") as send_statement_date'))->where('patient_id', $id)->get();      
        if ($export != "") {
            $exportparam = array(
                'filename' => 'payment',
                'heading' => '',
                'fields' => array(
                    'paymentnumber' => 'Payment ID',
                    'check_no' => 'Check No',
                    'check_date' => 'Check date',
                    'payment_amt' => 'Check Amount',
                    'amt_used' => 'Posted',
                    'balance' => 'Un posted',
                    'created_at' => 'Posted Date',
                    'created_by' => array(
                        'table' => 'created_user', 'column' => 'short_name', 'label' => 'Posted By'
                    ),
                )
            );
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $payments, $export);
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('payments', 'statments')));
    }

    public function showApi($id, $number) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        //$number = Helpers::getEncodeAndDecodeOfId($number,'decode');

        $payments = PMTInfoV1::with(['insurancedetail', 'created_user'])
                        ->where('payment_method', 'Patient')
                        ->where('id', $id)->where('paymentnumber', $number)->first();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('payments')));
    }

}
