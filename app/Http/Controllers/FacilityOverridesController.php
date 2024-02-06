<?php namespace App\Http\Controllers;

use Request;
use Input;
use View;
use DB;
use Route;
use Redirect;
use Config;
class FacilityOverridesController extends Api\FacilityoverridesApiController{

	public function __construct() {      
      
       View::share ( 'heading', 'Facility' ); 
	   View::share ( 'selected_tab', 'facility' );
	   View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.facility'));
    }  
	
	 /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id)
	{
		$api_response = $this->getIndexApi($id);
		$api_response_data = $api_response->getData();
		$overrides = $api_response_data->data->overrides;
		$facility = $api_response_data->data->facility;
        return view('practice/facility/overrides/overrides', compact('overrides','facility'));
	}

	 /**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	 
	public function create($id) /*here id is URL id*/
	{
		$api_response = $this->getCreateApi($id);
		$api_response_data = $api_response->getData();
		
		$facilities = $api_response_data->data->facilities;
		$facility_id = $api_response_data->data->facility_id;
		$insurances = $api_response_data->data->insurances;
		$insurance_id = $api_response_data->data->insurance_id;
		$practices = $api_response_data->data->practices;
		$providers = $api_response_data->data->providers;
		$provider_id = $api_response_data->data->provider_id;
		$id_qualifiers = $api_response_data->data->id_qualifiers;
		$id_qualifiers_id = $api_response_data->data->id_qualifiers_id;
		$facility = $api_response_data->data->facility;

		// return 'Showing account with ID:'.$id;
        return view('practice/facility/overrides/create_overrides',  compact('facilities','id_qualifiers_id','insurance_id','facility_id','providers','provider_id','insurances','practices','id_qualifiers','facility'));
	}

	 /**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($id, Request $request)
	{
		$api_response = $this->getStoreApi($id,$request::all());
		$api_response_data = $api_response->getData();
		
		$id = Input::get ( 'facilities_id' );
		
		if($api_response_data->status == 'success')
			{
				return Redirect::to('facility/'.$id.'/facilityoverrides')->with('success', $api_response_data->message);
			}
		else
			{
				return Redirect::back()->withInput()->withErrors($api_response_data->message);
			}        
			
		
	}

	 /**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($ids,$id)
	{   
		$api_response = $this->getEditApi($ids,$id);
		$api_response_data = $api_response->getData();
		
		$practices = $api_response_data->data->practices;
		$overrides = $api_response_data->data->overrides;
		$facilities = $api_response_data->data->facilities;
		$facility_id = $api_response_data->data->facility_id;
		$insurances = $api_response_data->data->insurances;
		$insurance_id = $api_response_data->data->insurance_id;
		$provider_id = $api_response_data->data->provider_id;
		$providers = $api_response_data->data->providers;
		$id_qualifiers = $api_response_data->data->id_qualifiers;
		$id_qualifiers_id = $api_response_data->data->id_qualifiers_id;
		$facility = $api_response_data->data->facility;
		
		return view('practice/facility/overrides/edit_overrides', compact('overrides','id_qualifiers_id','provider_id','insurance_id','providers','facility_id','id_qualifiers','facilities','insurances','practices','facility'));
	}

	 /**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($facility_id, $id,Request $request)
	{
		$api_response = $this->getUpdateApi($facility_id, $id, Request::all());
		$api_response_data = $api_response->getData();
		$ids = Input::get('facilities_id');
		if($api_response_data->status == 'success')
			{
				return Redirect::to('facility/'.$facility_id.'/facilityoverrides')->with('success',$api_response_data->message);
			}
		else
			{
				return Redirect::to('facility/'.$facility_id.'/facilityoverrides/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
			}     
			
		
        
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($facility_id, $id)
	{
		$api_response = $this->getDeleteApi($facility_id,$id);
		$api_response_data = $api_response->getData();
		return Redirect::to('facility/'.$facility_id.'/facilityoverrides')->with('success',$api_response_data->message);
	}

	public function show($ids,$id)
	{
		$api_response 		= 	$this->getShowApi($ids,$id);
		$api_response_data 	= 	$api_response->getData();		
		$overrides		 	= 	$api_response_data->data->overrides;
		$facility		 	= 	$api_response_data->data->facility;
	
		if($api_response_data->status == 'success')
		{
			return view ( 'practice/facility/overrides/show',compact('facility','overrides'));
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}

}
