<?php

namespace App\Http\Controllers;
use Auth;
use App;
use View;

class StatsController extends Api\StatsApiController {

    public function __construct() {
        View::share('heading', 'Reports');
        View::share('selected_tab', 'reports');
        View::share('heading_icon', 'barchart');
    }

    /* Display a listing of the resource*/
    public function SelectlistChange($data) {
		$api_response = $this->getSelectlistChangeApi($data);
        $api_response_data = $api_response->getData();
		$module = $api_response_data->data->module; 
		$message = $api_response_data->message;
		$stats_list = $api_response_data->data->stats_list;
        return view('reports/reports/stats', compact('module','stats_list','message'));
	}
}
