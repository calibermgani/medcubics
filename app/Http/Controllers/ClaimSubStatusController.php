<?php

namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class ClaimSubStatusController extends Api\ClaimSubStatusApiController {

    public function __construct() {       
       View::share( 'heading', 'Practice' );  
	   View::share( 'selected_tab', 'claimsubstatus' ); 
       View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	
	/*** Claim Sub List ***/
	public function index() {
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$claimsubstatus = $api_response_data->data->claimsubstatus;
		return view('practice/claimsubstatus/claimsubstatus',  compact('claimsubstatus'));		
	}
    
    public function getClaimSubStatusExport($export='') {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $claimsubstatus = $api_response_data->data->claimsubstatus;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Claim_Sub_Status_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/claimsubstatus/claimsubstatus_export_pdf', compact('claimsubstatus', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/claimsubstatus/claimsubstatus_export';
            $data['claimsubstatus'] = $claimsubstatus;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/claimsubstatus/claimsubstatus_export';
            $data['claimsubstatus'] = $claimsubstatus;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

	/**	create new Claim Sub Status ***/
	public function create() {
		$api_response = $this->getCreateApi();
		$claimsubstatus = $api_response->claimsubstatus;
		return view('practice/claimsubstatus/create',  compact('claimsubstatus'));
	}

	/** Store the claim sub status in Data base ***/
	public function store(Request $request) {
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();	

		if($api_response_data->status == 'success')	{
			return Redirect::to('claimsubstatus/'.$api_response_data->data)->with('success', $api_response_data->message);
		} else {
			return Redirect::to('claimsubstatus/create')->withInput()->withErrors($api_response_data->message);
		}  
	}

	/**	view claimsubstatus ***/
	public function show($id) {
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success') {
			$claimsubstatus = $api_response_data->data->claimsubstatus;
			return view ( 'practice/claimsubstatus/show',compact('claimsubstatus'));
		} else {
			return Redirect::to('claimsubstatus')->with('error', $api_response_data->message);
		}
	}

	/*** Edit Claim Sub Status ***/
	public function edit($id) {
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
			$claimsubstatus = $api_response_data->data->claimsubstatus;
			return view('practice/claimsubstatus/edit',  compact('claimsubstatus'));
		} else {
			return Redirect::to('claimsubstatus')->with('error', $api_response_data->message);
		}
	}

	/*** Update claimsubstatus values in database ***/
	public function update($id,Request $request) {

		$api_response = $this->getUpdateApi($id, Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') {
			return Redirect::to('claimsubstatus')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')	{
			return Redirect::to('claimsubstatus/'.$id)->with('success',$api_response_data->message);
		} else {
			return Redirect::to('claimsubstatus/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}
	}

	/***	Remove the claim sub status from database ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')	{
			return Redirect::to('claimsubstatus')->with('success',$api_response_data->message);
		} else {
			return Redirect::to('claimsubstatus')->with('error', $api_response_data->message);
		}
	}
}