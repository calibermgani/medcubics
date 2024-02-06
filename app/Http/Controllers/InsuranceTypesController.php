<?php namespace App\Http\Controllers;
use View;
use Redirect;
use Request;
use Config;
//use App\Http\Controllers\Api\InsuranceTypesApiController as InsuranceTypesApiController;
use PDF;
use Excel;
use App\Exports\BladeExport;

class InsuranceTypesController extends Api\InsuranceTypesApiController 
{
	public function __construct() 
	{
		View::share ( 'heading', 'Practice' );  
	   View::share ( 'selected_tab', 'insurance_type' ); 
       View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
	}
	
	/*** Start to listing the Insurance Types  ***/
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$insurancetypes 	= 	$api_response_data->data->insurancetypes;
		return view ( 'practice/insurancetypes/insurancetypes', compact ( 'insurancetypes') );
	}
	/*** End to listing the Insurance Types  ***/
        
    public function getInsuranceTypesExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $insurancetypes = $api_response_data->data->insurancetypes;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Insurance_Types_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/insurancetypes/insurancetypes_export_pdf', compact('insurancetypes', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/insurancetypes/insurancetypes_export';
            $data['insurancetypes'] = $insurancetypes;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/insurancetypes/insurancetypes_export';
            $data['insurancetypes'] = $insurancetypes;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
        
	/*** Start to Create the Insurance Types ***/
	public function create()
	{
		$api_response 		= $this->getCreateApi();
		$api_response_data 	= $api_response->getData();
		$cmstypes = $api_response_data->data->inscmstypes;
		return view('practice/insurancetypes/create', compact('cmstypes'));
	}
	/*** End to Create the Insurance Types	 ***/

	/*** Start to Store the Insurance Types	 ***/
	public function store(Request $request)
	{
		$api_response 		= $this->getStoreApi(Request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('insurancetypes/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('insurancetypes/create')->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Store the Insurance Types	 ***/
	
	/*** Start to Show the Insurance Types ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
                
		if($api_response_data->status=='error')
		{
			return redirect('insurancetypes')->with('message',$api_response_data->message);
		}
                
		$insurancetypes		= 	$api_response_data->data->insurancetypes;
		if($api_response_data->status == 'success')
		{
			return view ( 'practice/insurancetypes/show', ['insurancetypes' => $insurancetypes] );
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Show the Insurance Types ***/
	
	/*** Start to Edit the Insurance Types	 ***/
	public function edit($id)
	{
		$api_response 		= $this->getEditApi($id);
		$api_response_data 	= $api_response->getData();
                
		if($api_response_data->status=='error')
		{
			return redirect('insurancetypes')->with('message',$api_response_data->message);
		}
                
		$insurancetypes		= 	$api_response_data->data->insurancetypes;
		$cmstypes = $api_response_data->data->inscmstypes;		
		return view('practice/insurancetypes/edit', compact('insurancetypes','cmstypes'));
	}
	/*** End to Edit the Insurance Types ***/
	
	/*** Start to Update the Insurance Types	 ***/
	public function update($id, Request $request)
	{
		$api_response 		= 	$this->getUpdateApi($id, $request);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('insurancetypes/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Update the Insurance Types	 ***/
	
	/*** Start to Destory Insurance Types ***/
	public function destroy($id)
	{
		$api_response 		= $this->getDeleteApi($id);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('insurancetypes')->with('success',$api_response_data->message);
	}
	/*** End to Destory Insurance Types	 ***/
}
