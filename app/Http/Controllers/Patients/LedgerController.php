<?php

namespace App\Http\Controllers\Patients;

use View;
use Config;
use Redirect;
use App\Http\Controllers\Api\LedgerApiController as LedgerApiController;
use App;
use App\Traits\ClaimUtil;

class LedgerController extends Api\LedgerApiController {

    use ClaimUtil;

    public function __construct() {
        view::share("claimutil", $this);
        View::share('heading', 'Patient');
        View::share('selected_tab', 'ledger');
        View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return view file
     */
    public function index($id) {
        //Called Api\LedgerApiController function
        $api_response = $this->getIndexApi($id);
        //Response comes in Api\LedgerApiController
        $api_response_data = $api_response->getData();
        //Api response data success goto view file else error  patient listing page show
        if ($api_response_data->status == 'success') {
            $patients = $api_response_data->data->patients;
            $pagination = $api_response_data->data->pagination;
            $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
            // For set page title
            $details['account_no'] = $patients->account_no;
            App\Http\Helpers\Helpers::setPageTitle('patients', $details);

            return view('patients/ledger/ledger', compact('patients', 'pagination'));
        } else {
            return Redirect::to('patients')->with('message', $api_response_data->message);
        }
    }

    /**
     * Get a AJAX of the resource.
     *
     * @return Redirect
     */
    public function ajaxclaimlist($id) {
        //Called Api\LedgerApiController function
        $api_response = $this->getAjaxclaimlistApi($id);
        $api_response_data = $api_response->getData();
        //dd($api_response_data);
        //Api response data success goto view file else error  patient listing page show
        if ($api_response_data->status == 'success') {
            $patients = $api_response_data->data->patients;
            $pagination = $api_response_data->data->pagination;
            $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
            return view('patients/ledger/claimlist', compact('patients', 'pagination'));
        } else {
            return Redirect::to('patients')->with('message', $api_response_data->message);
        }
    }

}
