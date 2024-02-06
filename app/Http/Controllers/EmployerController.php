<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use View;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class EmployerController extends Api\EmployerApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );    
		View::share ( 'selected_tab', 'employer' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	/*** lists page Starts ***/
	public function index()	
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$employers 			= 	$api_response_data->data->employers;
		return view ( 'practice/employer/employer', compact ( 'employers') );
	}
	/*** lists page Ends ***/ 

    public function getEmployerExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $employers = $api_response_data->data->employers;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Employers_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/employer/employer_export_pdf', compact('employers', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/employer/employer_export';
            $data['employers'] = $employers;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            //return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/employer/employer_export';
            $data['employers'] = $employers;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
        
	/*** Create page Starts ***/
	public function create()
	{	
	   	$api_response 			= $this->getCreateApi();
		$api_response_data 		= $api_response->getData();
		
		$employers 				= $api_response_data->data->employers;
		$address_flags 			= (array)$api_response_data->data->addressFlag;
		$address_flag['general']= (array)$address_flags['general'];
		
		$employer_status		= array('Employed'=>'Employed','Self Employed'=>'Self Employed','Unemployed'=>'Unemployed','Retired'=>'Retired','Active Military Duty'=>'Active Military Duty','Employed(Full Time)'=>'Employed(Full Time)','Employed(Part Time)'=>'Employed(Part Time)','Unknown'=>'Unknown','Student'=>'Student');
		
		return view('practice/employer/create',  compact('employers','address_flag','employer_status'));
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function store(Request $request)
	{	
			 
		$api_response 		= $this->getStoreApi($request::all());
		
		$api_response_data 	= $api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('employer/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		elseif($api_response_data->status == 'failure'){
			return Redirect::to('employer/create')->with('success', $api_response_data->message);
		}else
		{
			return Redirect::to('employer/create')->withInput()->withErrors($api_response_data->message);
		}		
	}
	/*** Store Function Ends ***/
	
	/*** Show page Starts ***/
	public function show($id)
	{	
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();		
	
		if($api_response_data->status == 'success')
		{
			$employer		 		= 	$api_response_data->data->employers;
			$address_flags 			= 	(array)$api_response_data->data->addressFlag;
			$address_flag['general']= 	(array)$address_flags['general'];
			return view ( 'practice/employer/show', ['employer' => $employer,'address_flag'=>$address_flag] );
		}
		else
		{
			return Redirect::to('employer')->with('error', $api_response_data->message);
		}
	}
	/*** Show Function Ends ***/
	
	/*** Edit page Starts ***/ 
	public function edit($id)
	{		
		$api_response 		= $this->getEditApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$employer 				= $api_response_data->data->employers;
			$address_flags 			= (array)$api_response_data->data->addressFlag;
			$address_flag['general']= (array)$address_flags['general'];
			
			$employer_status		= array('Employed'=>'Employed','Self Employed'=>'Self Employed','Unemployed'=>'Unemployed','Retired'=>'Retired','Active Military Duty'=>'Active Military Duty','Employed(Full Time)'=>'Employed(Full Time)','Employed(Part Time)'=>'Employed(Part Time)','Unknown'=>'Unknown','Student'=>'Student');
			
			return view('practice/employer/edit', compact('employer','address_flag','employer_status'));
		}
		else
		{
			return Redirect::to('employer')->with('error', $api_response_data->message);
		}
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function update($id, Request $request)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('employer')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('employer/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** Update Function Ends ***/
	
	/*** Delete Function Starts ***/
	public function destroy($id)
	{
		$api_response 		= $this->getDeleteApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('employer')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('employer')->with('error',$api_response_data->message);
		}
	}
	/*** Delete Function Ends ***/
	
	public function empAvatarDelete($id,$picture_name)
	{
		$api_response 		= $this->avatarapipicture($id,$picture_name);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('employer/'.$id.'/edit')->with('success', $api_response_data->message);
	}
}
