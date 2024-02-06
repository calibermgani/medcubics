<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class HoldOptionController extends Api\HoldOptionApiController {
	public function __construct() {       
       View::share ( 'heading', 'Practice' );  
	   View::share ( 'selected_tab', 'holdoption' ); 
       View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	/*** Hold option List ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$holdoption = $api_response_data->data->holdoption;
		return view('practice/holdoption/holdoption',  compact('holdoption'));
		
	}
    
    public function getHoldReasonExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $holdoption = $api_response_data->data->holdoption;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Hold_Reason_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/holdoption/holdoption_export_pdf', compact('holdoption', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/holdoption/holdoption_export';
            $data['holdoption'] = $holdoption;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/holdoption/holdoption_export';
            $data['holdoption'] = $holdoption;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	/**	create new Hold option ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$holdoption = $api_response->holdoption;
		return view('practice/holdoption/create',  compact('holdoption'));
	}

	/** Store the hold option in Data base ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
	

		if($api_response_data->status == 'success')
			{
				return Redirect::to('holdoption/'.$api_response_data->data)->with('success', $api_response_data->message);
			}
		else
			{
				return Redirect::to('holdoption/create')->withInput()->withErrors($api_response_data->message);
			}  
	}

	/**	view holdoption ***/
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
		$holdoption = $api_response_data->data->holdoption;
		return view ( 'practice/holdoption/show',compact('holdoption'));
	}
		else{
			return Redirect::to('holdoption')->with('error', $api_response_data->message);
		}
	}

	/*** Edit hold option ***/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
		$holdoption = $api_response_data->data->holdoption;
		return view('practice/holdoption/edit',  compact('holdoption'));
		}
		else{
			return Redirect::to('holdoption')->with('error', $api_response_data->message);
		}
	}

	/*** Update holdoption values in database ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') {
			return Redirect::to('holdoption')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('holdoption/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('holdoption/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}
	}

	/***	Remove the hold option from database ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('holdoption')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('holdoption')->with('error', $api_response_data->message);
		}
	}

}
