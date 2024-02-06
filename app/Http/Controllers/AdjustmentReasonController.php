<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class AdjustmentReasonController extends Api\AdjustmentReasonApiController { 

	 public function __construct() 
    {      
        View::share('heading', 'Practice');   
        View::share('selected_tab', 'adjustmentreason');
        View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }
    
	/***List the Adjustment reason ***/
	public function index()
	{
		 $api_response       = $this->getIndexApi();
        $api_response_data  = $api_response->getData();		
        $adjustmentreason     = $api_response_data->data->adjustment_reason;
        return view('practice/adjustmentreason/adjustmentreason',compact('adjustmentreason'));
	}
        
    public function getAdjustmentreasonExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $adjustmentreason = $api_response_data->data->adjustment_reason;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Adjustment_Reason_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/adjustmentreason/adjustmentreason_export_pdf', compact('adjustmentreason', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/adjustmentreason/adjustmentreason_export';
            $data['adjustmentreason'] = $adjustmentreason;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/adjustmentreason/adjustmentreason_export';
            $data['adjustmentreason'] = $adjustmentreason;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
        
	/***Create the Adjustment reason ***/
	public function create()
	{
		return view('practice/adjustmentreason/create');
   }

	/***Store the Adjustment reason ***/
	public function store(Request $request)
	{
		$api_response           = $this->getStoreApi($request::all());
        $api_response_data      = $api_response->getData();
        if($api_response_data->status == 'success')
        {
            return Redirect::to('adjustmentreason/'.$api_response_data->data)->with('success', $api_response_data->message);
        }
        else
        {
            return Redirect::to('adjustmentreason/create')->withInput()->withErrors($api_response_data->message);
        }	
	}

	/***Show the Adjustment reason ***/
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
		$adjustmentreason = $api_response_data->data->adjustmentreason;

		return view ( 'practice/adjustmentreason/show',compact('adjustmentreason'));
	}
		else{
			return Redirect::to('adjustmentreason')->with('error', $api_response_data->message);
		}
	}

	/*** Edit the Adjustment reason ***/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
		$adjustmentreason = $api_response_data->data->adjustmentreason;
		return view('practice/adjustmentreason/edit',  compact('adjustmentreason'));
		}
		else{
			return Redirect::to('adjustmentreason')->with('error', $api_response_data->message);
		}
	}

	/***Update the Adjustment reason ***/
	public function update($id, Request $request)
	{
		$api_response = $this->getUpdateApi( $id,Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') {
			return Redirect::to('adjustmentreason')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('adjustmentreason/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('adjustmentreason/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}
	}

	/***Delete the Adjustment reason ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('adjustmentreason')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('adjustmentreason')->with('error', $api_response_data->message);
		}	
	}

}
