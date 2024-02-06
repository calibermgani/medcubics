<?php

namespace App\Http\Controllers\Patients;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Illuminate\Http\Request;
use PDF;
use Excel;
use App\Exports\BladeExport;

class PatientWalletHistoryController extends Api\PatientWalletHistoryApiController {

    public function __construct() {
        View::share('heading', 'Patient');
        View::share('selected_tab', 'patientpayment');
        View::share('heading_icon', 'fa-user');
    }

    public function index($patient_id) {
        $api_response = $this->getIndexApi($patient_id);
        $api_response_data = $api_response->getData();
        $payments = $api_response_data->data->payments;
        $statments = $api_response_data->data->statments;
        return view('patients/paymentwallet/wallethistory', compact('payments', 'patient_id', 'statments'));
    }
    
    public function paymentWalletExport($patient_id = '', $export = '') {
        $api_response = $this->getIndexApi($patient_id);
        $api_response_data = $api_response->getData();
        $payments = $api_response_data->data->payments;
        $statments = $api_response_data->data->statments;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Patient_Payment_Wallet_' . $date;

        if ($export == 'pdf') {
            $html = view('patients/paymentwallet/wallethistory_export_pdf', compact('payments', 'statments', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'patients/paymentwallet/wallethistory_export';
            $data['payments'] = $payments;
            $data['statments'] = $statments;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            //return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'patients/paymentwallet/wallethistory_export';
            $data['payments'] = $payments;
            $data['statments'] = $statments;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

    public function show($id, $number) {
        $api_response = $this->showApi($id, $number);
        $api_response_data = $api_response->getData();
        //dd($api_response_data);
        $claim_detail = $api_response_data->data->payments;
        return view('patients/paymentwallet/paymentpopup', compact('claim_detail', 'id', 'number'));
    }

}
