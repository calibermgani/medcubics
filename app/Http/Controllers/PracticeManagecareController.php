<?php namespace App\Http\Controllers;

use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use Session;
use View;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class PracticeManagecareController extends Api\PracticeManagecareApiController 
{
	public function __construct() 
	{      
		View::share('heading','Practice');
		View::share('selected_tab','practice');
		View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	
	/********************** Start Display a listing of the practice managecare ***********************************/
	public function index()
	{
        $api_response 		= $this->getIndexApi();		
		$api_response_data 	= $api_response->getData();		
		$managecares 		= $api_response_data->data->managecares;		
		$practice 			= $api_response_data->data->practice;
		return view('practice/practice/managecare/managecare',compact('managecares','practice'));
	}
	/********************** End Display a listing of the practice managecare ***********************************/
        
    public function practiceManagedCareExport($export='') {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $managecares = $api_response_data->data->managecares;
        $practice = $api_response_data->data->practice;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Practice_Managed_Care_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/practice/managecare/managecare_export_pdf', compact('practice', 'managecares', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/practice/managecare/managecare_export';
            $data['practice'] = $practice;
            $data['managecares'] = $managecares;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            //return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/practice/managecare/managecare_export';
            $data['practice'] = $practice;
            $data['managecares'] = $managecares;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

	/********************** Start show practice managecare add page ***********************************/
	public function create()
	{              
		$api_response 		= $this->getCreateApi();
		$api_response_data 	= $api_response->getData();
		$practice 			= $api_response_data->data->practice;
		$insurances 		= $api_response_data->data->insurances;
		$insurance_id 		= $api_response_data->data->insurance_id;
		$providers 			= $api_response_data->data->providers;
		$provider_id 		= $api_response_data->data->provider_id;
		return view('practice/practice/managecare/create_managecare', compact('practice','insurances','insurance_id','providers','provider_id'));
	}
	/********************** End show practice managecare add page ***********************************/
	
	/********************** Start store practice managecare process ***********************************/
	public function store(Request $request)
	{        
        $api_response 		= $this->getStoreApi($request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('managecare')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('managecare/create')->withInput()->withErrors($api_response_data->message);
		}    
    }
	/********************** End store practice managecare process ***********************************/
	
	/********************** Start practice managecare details page ***********************************/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status == 'success')
		{
			$managedcare		 	= 	$api_response_data->data->managedcare;
			$practice		 	= 	$api_response_data->data->practice;
			return view ( 'practice/practice/managecare/show',compact('managedcare','practice'));
		}
		else
		{
			return Redirect::to('managecare')->with('message', $api_response_data->message);
		}
	}
	/********************** End practice managecare details page ***********************************/
	
	/********************** Start show practice managecare edit page ***********************************/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$practice 		= $api_response_data->data->practice;
			$managecare 	= $api_response_data->data->managecare;
            $insurances 	= $api_response_data->data->insurances;
            $insurance_id 	= $api_response_data->data->insurance_id;
            $providers 		= $api_response_data->data->providers;
            $provider_id 	= $api_response_data->data->provider_id;
			return view('practice/practice/managecare/edit_managecare', compact('practice','managecare','insurances','insurance_id','providers','provider_id'));
		}
		else
		{
			return Redirect::to('managecare')->with('message', $api_response_data->message);
		}
	}
	/********************** End show practice managecare edit page ***********************************/
	
	/********************** Start practice managecare update process ***********************************/
	public function update($id, Request $request)
	{
		$api_response = $this->getUpdateApi($id,$request::all());
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('managecare')->with('message', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('managecare/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('managecare/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}                
	}
	/********************** End practice managecare update process ***********************************/

	/********************** Start practice managecare delete process ***********************************/
	public function destroy($id)
	{
		$api_response 		= $this->getDeleteApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('managecare')->with('message', $api_response_data->message);
		}
		return Redirect::to('managecare')->with('success',$api_response_data->message);
	}
	/********************** End practice managecare delete process ***********************************/
	
}
