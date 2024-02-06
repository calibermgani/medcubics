<?php

namespace App\Http\Controllers\Charges;

use Auth;
use App;
use View;
use Input;
use Session;
use Request;
use Response;
use Redirect;
use Validator;
use Dompdf\Dompdf;
use App\Models\Charges\Charge;
use App\Models\Patient;
use App\Models\Payments\ClaimInfoV1;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Patients\Api\BillingApiController;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController;
use Route;
use PDF;
use Excel;
use Log;
use App\Exports\BladeExport;

class ChargeController extends Api\ChargeApiController {

    public function __construct() {
        View::share('heading', 'Charges');
        View::share('selected_tab', 'charges');
        View::share('heading_icon', 'fa-pencil');
    }

    public static $box_count_val = 1;
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($status = null) {
        $api_response = $this->getIndexApi('', $status);
        $api_response_data = $api_response->getData();
        //$claims_lists = $api_response_data->data->charges;
        $charges = $api_response_data->data->charges;
        if (Request::ajax()) {
            return view('charges/charges/charges_listing', compact('charges'));
        }
        $charges = $api_response_data->data->charges;
        $facilities = $api_response_data->data->facilities;
        $rendering_providers = $api_response_data->data->rendering_providers;
        $billing_providers = $api_response_data->data->billing_providers;
        $pos = $api_response_data->data->pos;
		$search_fields = $api_response_data->data->search_fields;
		$searchUserData = $api_response_data->data->searchUserData;
        return view('charges/charges/charges', compact('charges', 'facilities', 'rendering_providers', 'billing_providers','pos',  'search_fields', 'searchUserData'));
    }

    public function indexTableData($status = null) {
        $api_response = $this->getListIndexApi('', $status); //getIndexApi();
        $api_response_data = $api_response->getData();

        $charges = (!empty($api_response_data->data->charges)) ? (array) $api_response_data->data->charges : [];
        $facilities = (!empty($api_response_data->data->facilities)) ? (array) $api_response_data->data->facilities : [];
        $rendering_providers = (!empty($api_response_data->data->rendering_providers)) ? (array) $api_response_data->data->rendering_providers : [];
        $billing_providers = (!empty($api_response_data->data->billing_providers)) ? (array) $api_response_data->data->billing_providers : [];

        $view_html = Response::view('charges/charges/charge_list_ajax', compact('charges', 'facilities', 'rendering_providers', 'billing_providers'));
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
    /*Charges Export*/
    public function chargesExport($export = '', $status = null) {     
        $api_response = $this->getListIndexApi($export, $status);
        $api_response_data = $api_response->getData();
        $charges = (!empty($api_response_data->data->charges)) ? (array) $api_response_data->data->charges : [];
        $facilities = (!empty($api_response_data->data->facilities)) ? (array) $api_response_data->data->facilities : [];
        $rendering_providers = (!empty($api_response_data->data->rendering_providers)) ? (array) $api_response_data->data->rendering_providers : [];
        $billing_providers = (!empty($api_response_data->data->billing_providers)) ? (array) $api_response_data->data->billing_providers : [];
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Charges_' . $date;

        if ($export == 'pdf') {
            $html = view('charges/charges/charges_export_pdf', compact('charges', 'facilities', 'rendering_providers', 'billing_providers', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'charges/charges/charges_export';
            $data['charges'] = $charges;
            $data['facilities'] = $facilities;
            $data['rendering_providers'] = $rendering_providers;
            $data['billing_providers'] = $billing_providers;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            
            return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xlsx');
        } elseif ($export == 'csv') {
            $filePath = 'charges/charges/charges_export';
            $data['charges'] = $charges;
            $data['facilities'] = $facilities;
            $data['rendering_providers'] = $rendering_providers;
            $data['billing_providers'] = $billing_providers;
            $data['export'] = $export;
            
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

    // Save the provider related data at session from charges page starts here
    public function saveprovider(Request $request) {
        $request_val = $request::all();		

        $rendering = Helpers::getEncodeAndDecodeOfId($request_val['rendering_provider_id'], 'encode');        
        $billing = Helpers::getEncodeAndDecodeOfId($request_val['billing_provider_id'], 'encode');
        $facility = Helpers::getEncodeAndDecodeOfId($request_val['facility_id'], 'encode');
        $dos_from = $request_val['dos_from'];
        $dos_to = $request_val['dos_to'];
        $pos = Helpers::getEncodeAndDecodeOfId(@$request_val['pos_id'], 'encode');
        $ref =  $request_val['reference'];
        if (!empty($request_val)) {            
            return Redirect::to('charges/newcharge?rendering='.$rendering.'&billing='.$billing.'&fac='.$facility.'&dos_from='.$dos_from.'&dos_to='.$dos_to.'&pos='.$pos.'&ref='.$ref);
        } else {
            return Redirect::to('charges');
        }
    }

    // Save the provider related data at session from charges page starts here
    //  This is just for display purpose without any mouse activity the create was brought from billing/create
    public function create($id = null, $type = null) {
        $api_response = $this->getCreateApi();
        $api_response_data = $api_response->getData();
        $facilities = $api_response_data->data->facilities;
        $rendering_providers = $api_response_data->data->rendering_providers;
        $billing_providers = $api_response_data->data->billing_providers;
        $pos = $api_response_data->data->pos;
        $charge_session_value = $api_response_data->data->data;
        return view('charges/charges/create', compact('facilities', 'rendering_providers', 'billing_providers', 'charge_session_value'));
    }

    public function searchpatient($type, $key) {
        $api_response = $this->getSearchPatientApi($type, $key);
        $api_response_data = $api_response->getData();
        $i = 0;
        foreach ($api_response_data->data->patient_list as $patient) {
            $api_response_data->data->patient_list[$i]->id = Helpers::getEncodeAndDecodeOfId($patient->id, 'encode');
            $i++;
        }
        if ($api_response_data->status == 'success') {
            return $api_response_data->data->patient_list;
        } else {
            return 'null';
        }
    }

    public function store(Request $request) {
        $request = $request::all();
        $claim_id = $request['claim_id'];
        if (!empty($claim_id) && !isset($request['fromedit'])) {
            $claim_id_val = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
            $charges = new BillingApiController();
            $return_value = $charges->findPaymentDone($claim_id_val);
            if (!$return_value) {
                return Redirect::to('patients/' . $request['patient_id'] . '/billing/edit/' . $claim_id)->withInput()->with('error', "Oops wrong page");
            }
        }
        $api_response = $this->getStoreApi($request);
        $api_response_data = $api_response->getData();
        $query_value = $request['jsqueryvalue'];
        if ($api_response_data->status == 'success') {
            if (!empty($request['claim_id']))
                return Redirect::to('charges')->with('success', "Claim data updated successfully");
            return Redirect::to('charges/create?'.$query_value)->with('success', $api_response_data->message);
        }
        else {
            return Redirect::to('charges')->with('error', $api_response_data->message);
        }
    }

    public function edit($claim_id, $value = '') {
        $insurances=[];
        $claim_id_val = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
 
        // This is used for url redirection depeds upon the charges status
        if (!empty($claim_id_val) && is_null($value)) {
            $charges = new BillingApiController();
            $return_value = $charges->findPaymentDone($claim_id_val);
            //dd($claim_id);       
            if (!$return_value) {
                return Redirect::to('charges/' . $claim_id . '/charge_edit/charge');
            }
        }
        // ends 
        $api_response = $this->getEditApi($claim_id_val);
        
        $api_response_data = $api_response->getData();

        $status = $api_response_data->status;
        if ($status == "success") {
            $facilities = $api_response_data->data->facilities;
            $rendering_providers = $api_response_data->data->rendering_providers;
            $referring_providers = $api_response_data->data->referring_providers;
            $billing_providers = $api_response_data->data->billing_providers;
            $insurance_data = $api_response_data->data->insurance_data;
            $patients = $api_response_data->data->patient_detail;
            $modifier = $api_response_data->data->modifier;
            $claims = $api_response_data->data->claims_list;
            $pos = $api_response_data->data->pos;
            $hold_option = $api_response_data->data->hold_options;
            $patient_id = $patients->id;
            $view = 'charges/charges/edit';
            if (strpos(Route::getCurrentRoute()->uri(), 'charge_edit') !== false) {
                $view = 'charges/charges/charges_edit';
            }
        
            // For set page title
            $details['claim_no'] = $claims->claim_number;
            App\Http\Helpers\Helpers::setPageTitle('charges', $details);
        

            return view($view, compact('modifier', 'facilities', 'rendering_providers', 'insurance_data', 'referring_providers', 'billing_providers', 'insurances', 'patients', 'claims', 'hold_option', 'patient_id', 'pos'));
        } else {

            return Redirect::to('charges');
        }
    }

    public function update(Request $request) {
        $request = $request::all();

        $api_response = $this->getUpdateApi($request);
        $api_response_data = $api_response->getData();

        if ($api_response_data->status == 'success') {
            if (!empty($request['claim_id']))
                return Redirect::to('charges')->with('success', "Claim data updated successfully")->with('Claim_Edit_In_AR', 1);
            return Redirect::to('charges/charges_edit')->with('success', $api_response_data->message);
        }
        else {
            return Redirect::to('charges')->with('error', $api_response_data->message);
        }
    }

    public function destroy($id) {
        $api_response = $this->getDeleteApi($id);
        $patient_id = $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            return Redirect::to('charges')->with('success', $api_response_data->message);
        } else {
            return Redirect::to('charges')->with('error', $api_response_data->message);
        }
    }

    public function getcmsform($claim_id) {
        $html = view('charges/charges/cmsform')->render();
        $time = time();
        $pdfobj = new PDF();
        $pdfobj::setPaper('A5', 'portrait');
        return $pdfobj::load($html)->filename($time . '.pdf')->show();
        $pdf->loadView('charges/cmsform', $content);
        // return view('charges/charges/cmsform');
    }

    public function getcmsform1($claim_id, $type = null) {
        $html = $this->generatecmsform($claim_id, $type);
        if ($html != '') {
            $pdfobj = new dompdf();
            $customPaper = array(0, 0, 648, 840);
            $pdfobj->setPaper($customPaper);
            //$pdfobj->setPaper('A4', 'portrait');
            $pdfobj->loadHTML($html);
            $pdfobj->render();
            $pdfobj->stream("cmsform.pdf", array("Attachment" => false));
            exit(0);
        }
        return '';
        // return view('charges/charges/cmsform');
    }
	
	public function getcmsform2($claim_id, $type = null) {
		$charges = new ChargeV1ApiController();
        $api_response = $charges->getcmsdataApi($claim_id);
        $api_response_data = $api_response->getData();
        $claim_detail = $api_response_data->data->claim_detail;
        $box_count = $api_response_data->data->box_count; //dd($box_count);
        $box_count_val = 1;
        return view('charges/charges/cmspdf',compact('claim_detail', 'box_count_val', 'type'));
    }

    public function generatecmsform($claim_id, $type = null) {
        //$api_response = $this->getcmsdataApi($claim_id);
        // $api_response_data = $api_response->getData();
        $charges = new ChargeV1ApiController();
        $api_response = $charges->getcmsdataApi($claim_id);
        $api_response_data = $api_response->getData();
        $claim_detail = $api_response_data->data->claim_detail;
        $box_count = $api_response_data->data->box_count; //dd($box_count);
        $box_count_val = 1;
        $html = '';
        for ($i = 0; $i <= 4; $i++) { //As of now we only have maximum limit as 24 so, it will be around 4 pages availble for cms1500form/changed to 30 as maximum as per billing requirement on  29/05/2018
            // dd($claim_detail->box_24);
            if ($box_count_val <= $box_count) {
                $html.= view('charges/charges/cmsformpdf', compact('claim_detail', 'box_count_val', 'type'))->render();
                $box_count_val = $box_count_val + 6;   // Adding maximum line items of the same charge
            } elseif ($box_count == 0) {
                // No DOS found 
                $html = view('charges/charges/cmsformpdf', compact('claim_detail', 'box_count_val', 'type'))->render();
            } else {
                continue;
            }
        }
        return $html;
    }

    public function changecreatedDate($type) {
        $api_response = $this->changeCreatedDateApi($type);
        $api_response_data = $api_response->getData();
        $payments = $api_response_data->data->payments;
        $claims = $api_response_data->data->claims;
        return view("charges/charges/changedate", compact('payments', 'claims'));
    }

}