<?php namespace App\Http\Controllers;

use Request;
use Input;
use View;
use Validator;
use Redirect;
use Auth;
use Session;
use Config;
use App\Models\Facilitymanagecare;
use App\Models\Facility;
use PDF;
use Excel;
use App\Exports\BladeExport;

class FacilityManagecareController extends Api\FacilitymanagecareApiController
{
	public function __construct() 
	{  
		//Tab selection, Heading, and Icon show  
       View::share( 'heading', 'Practice' );  
	   View::share( 'selected_tab', 'facility' );
	   View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  

	 /*** Display a listing of the resource. ***/
	public function index($id)
	{
		//Connection to FacilitymanagecareApiController
		$api_response 		= $this->getIndexApi($id); 
		//Data redrive from FacilitymanagecareApiController	
		$api_response_data 	= $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
		
		$managecare 		= $api_response_data->data->managecare;
		$facility 			= $api_response_data->data->facility;
		return view('practice/facility/managecare/managecare',  compact('managecare','facility'));
		return redirect("facility")->with('error',"Invalid facility");
	}
	
	public function facilityManagedCareExport($id = '', $export='') {
        $api_response = $this->getIndexApi($id);
        $api_response_data = $api_response->getData();
        $facility = $api_response_data->data->facility;
        $managecare = $api_response_data->data->managecare;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Facility_Managed_Care_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/facility/managecare/managecare_export_pdf', compact('facility', 'managecare', 'export'));
            return PDF::loadHTML($html)->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/facility/managecare/managecare_export';
            $data['facility'] = $facility;
            $data['managecare'] = $managecare;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/facility/managecare/managecare_export';
            $data['facility'] = $facility;
            $data['managecare'] = $managecare;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	
	/*** Show the form for creating a new resource.  @return Response  ***/
	public function create($id)
	{
		$api_response 		= $this->getCreateApi($id);
		//Data redrive from FacilitymanagecareApiController
		$api_response_data 	= $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			$insurances			= $api_response_data->data->insurances;
			$insurance_id 		= $api_response_data->data->insurance_id;
			$providers 			= $api_response_data->data->providers;
			$provider_id 		= $api_response_data->data->provider_id;
			$facility 			= $api_response_data->data->facility;        
			return view('practice/facility/managecare/create_managecare',  compact('insurances','insurance_id','providers','provider_id','managecare','facility'));
		}
		else
		{
			$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
			return Redirect::to('facility/'.$id.'/facilitymanagecare')->withInput()->withErrors($api_response_data->message);
		}	
	}
	
	/*** Store a newly created resource in storage. @return Response  ***/
	public function store($id, Request $request)
	{
		$api_response 		= $this->getStoreApi($id, $request::all());
		//Data redrive from FacilitymanagecareApiController
		$api_response_data 	= $api_response->getData();	
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
		// dd($api_response_data->data);
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('facility/'.$id.'/facilitymanagecare/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{	
			return Redirect::to('facility/'.$id.'/facilitymanagecare/create')->withInput()->withErrors($api_response_data->message);
		}		     
	}

	/*** Display the specified resource.  ***/
	public function show($ids,$id)
	{
		$api_response 		= 	$this->getShowApi($ids,$id);
		$api_response_data 	= 	$api_response->getData();
		//Data redrive from FacilitymanagecareApiController
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
			
		if($api_response_data->status == 'success')
		{
			$managedcare		= 	$api_response_data->data->managedcare;
			$facility 			= $api_response_data->data->facility;
                        $insurances			= $api_response_data->data->insurances;
			$insurance_id 		= $api_response_data->data->insurance_id;
			$providers 			= $api_response_data->data->providers;
			$provider_id 		= $api_response_data->data->provider_id;			
			return view ( 'practice/facility/managecare/show',compact('managedcare','facility','insurances','insurance_id','providers','provider_id'));
		}
		else
		{
			return Redirect::to('facility/'.$ids.'/facilitymanagecare')->with('message', $api_response_data->message);
		}
	}
	
	/*** Show the form for editing the specified resource. ***/
	public function edit($ids,$id)
	{
		$api_response 		= $this->getEditApi($ids,$id);
		//Data redrive from FacilitymanagecareApiController
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			$managecare 		= $api_response_data->data->managecare;
			$insurances 		= $api_response_data->data->insurances;
			$insurance_id 		= $api_response_data->data->insurance_id;
			$providers 			= $api_response_data->data->providers;
			$provider_id 		= $api_response_data->data->provider_id;
			$facility 			= $api_response_data->data->facility;
		   
			return View('practice/facility/managecare/edit_managecare', compact('insurances','insurance_id','providers','provider_id','managecare','facility'));
		}
		else
		{
			return Redirect::to('facility/'.$ids.'/facilitymanagecare')->with('message', $api_response_data->message);
		}	
	}

	/*** Update the specified resource in storage.  ***/
	public function update($facility_id,$id,Request $request)
	{
		$api_response 		= $this->getUpdateApi($facility_id, $id, $request::all());
		//Data redrive from FacilitymanagecareApiController
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
		if($api_response_data->status == 'failure_care') 
		{
			return Redirect::to('facility/'.$facility_id.'/facilitymanagecare')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('facility/'.$facility_id.'/facilitymanagecare/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('facility/'.$facility_id.'/facilitymanagecare/'.$facility_id.'/edit')->withInput()->withErrors($api_response_data->message);
		}	
	}

	/*** Remove the specified resource from storage. ***/
	public function destroy($facility_id,$id)
	{
		$api_response 		= $this->getDeleteApi($facility_id,$id);
		//Data redrive from FacilitymanagecareApiController
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			return Redirect::to('facility/'.$facility_id.'/facilitymanagecare')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('facility/'.$facility_id.'/facilitymanagecare')->with('message', $api_response_data->message);
		}
	}
}
