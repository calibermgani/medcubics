<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use Redirect;
use Auth;
use View;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class ReasonController extends Api\ReasonApiController
{
	public function __construct() { 
      
       View::share ( 'heading', 'Practice' );  
	   View::share ( 'selected_tab', 'reason_for_visit' ); 
       View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));

    }  
	/*** Reason for for visit Lising ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$reason = $api_response_data->data->reason;
		return view('practice/reason_for_visits/reason',  compact('reason'));
	}
        
    public function getReasonExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $reason = $api_response_data->data->reason;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Reason_For_Visit_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/reason_for_visits/reason_export_pdf', compact('reason', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/reason_for_visits/reason_export';
            $data['reason'] = $reason;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/reason_for_visits/reason_export';
            $data['reason'] = $reason;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

	/*** Reason for for visit Create ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$reason = $api_response->reason;
		return view('practice/reason_for_visits/create',  compact('reason'));
	}

	/*** Reason for for visit Store ***/	
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
			{
				return Redirect::to('reason/'.$api_response_data->data)->with('success', $api_response_data->message);
			}
		else
			{
				return Redirect::to('reason/create')->withInput()->withErrors($api_response_data->message);
			}  		
	}

	/*** Reason for for visit Show ***/
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
		$reason = $api_response_data->data->reason;
		return view ( 'practice/reason_for_visits/show',compact('reason'));
	}
		else{
			return Redirect::to('reason')->with('error', $api_response_data->message);
		}
	}

	/*** Reason for for visit Edit ***/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'success'){
		$reason = $api_response_data->data->reason;
		return view('practice/reason_for_visits/edit',  compact('reason'));
		}
		else{
			return Redirect::to('reason')->with('error', $api_response_data->message);
		}
	}
	
	/*** Reason for for visit Update ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'failure') {
			return Redirect::to('reason')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('reason/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('reason/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}
	}

	/*** Reason for for visit Delete ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('reason')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('reason')->with('error', $api_response_data->message);
		}
	}
}
