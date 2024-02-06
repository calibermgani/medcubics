<?php 
namespace App\Http\Controllers;
use View;
use Input;
use Request;
use Redirect;
use Config;
use App\Http\Controllers\Api\ProviderOverridesApiController as ProviderOverridesApiController;

class InsuranceOverridesController extends Api\InsuranceOverridesApiController 
{
    public function __construct(Request $request) 
    {
        View::share( 'heading', 'Insurance');
        View::share( 'selected_tab', 'insurance');
		View::share( 'heading_icon',Config::get('cssconfigs.common.insurance'));
    }
    
	/*** Start to listing the Overrides  ***/
    public function index($id)
    {
        $api_response = $this->getIndexApi($id);
        $api_response_data = $api_response->getData();
        if($api_response_data->status == 'success')
        {
			$overrides = $api_response_data->data->overrides;
			$insurance = $api_response_data->data->insurance;
			$address_flags = (array)$api_response_data->data->addressFlag;
			$address_flag['general'] = (array)$address_flags['general'];
			return view('practice/insurance/overrides/overrides', compact('overrides','insurance','address_flag'));
		}
        else
        {
             return redirect('/insurance')->with('error',$api_response_data->message);
        }
    }
	/*** End to listing the Overrides  ***/
	
	/*** Start to Create the Overrides	 ***/
    public function create($id)
    {
        $api_response = $this->getCreateApi($id);
        $api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
        {
			$facilities = $api_response_data->data->facilities;
			$facilities_id = $api_response_data->data->facilities_id;
			$insurances = $api_response_data->data->insurances;
			$insurance_id = $api_response_data->data->insurance_id;
			$providers = $api_response_data->data->providers;
			$provider_id = $api_response_data->data->provider_id;
			$id_qualifiers = $api_response_data->data->id_qualifiers;
			$id_qualifiers_id = $api_response_data->data->id_qualifiers_id;
			$insurance = $api_response_data->data->insurance;
			$address_flags = (array)$api_response_data->data->addressFlag;
			$address_flag['general'] = (array)$address_flags['general'];

			// return 'Showing account with ID:'.$id;
			return view('practice/insurance/overrides/create_overrides',  compact('facilities','id_qualifiers_id','insurance_id','facilities_id','providers','provider_id','insurances','id_qualifiers','insurance','address_flag'));
		}
        else
        {
             return redirect('/insurance')->with('error',$api_response_data->message);
        }
    }
	/*** End to Create the Overrides	 ***/
	
	/*** Start to Store the Overrides	 ***/
	public function store($id, Request $request)
	{
		$api_response = $this->getStoreApi($id,$request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') {
			return Redirect::to('insurance')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('insurance/'.$id.'/insuranceoverrides/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** End to Store the Overrides	 ***/
	
	/*** Start to Edit the Overrides	 ***/
	public function edit($ids,$id)
	{   
		$api_response = $this->getEditApi($ids,$id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status=='failure')
		{
			return redirect('/insurance')->with('error',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			$overrides = $api_response_data->data->overrides;
			$facilities = $api_response_data->data->facilities;
			$facilities_id = $api_response_data->data->facilities_id;
			$provider_id = $api_response_data->data->provider_id;
			$providers = $api_response_data->data->providers;
			$id_qualifiers = $api_response_data->data->id_qualifiers;
			$id_qualifiers_id = $api_response_data->data->id_qualifiers_id;
			$insurance = $api_response_data->data->insurance;
			
			$address_flags = (array)$api_response_data->data->addressFlag;
			$address_flag['general'] = (array)$address_flags['general'];
			
			return view('practice/insurance/overrides/edit_overrides', compact('overrides','id_qualifiers_id','provider_id','providers','facilities_id','id_qualifiers','facilities','insurance','address_flag'));
		}
		else
		{
			return redirect('/insurance/'.$ids.'/insuranceoverrides')->with('error',$api_response_data->message);
		}  
	}
	/*** End to Edit the Overrides ***/

	/*** Start to Update the Overrides	 ***/
	public function update($insurance_id, $id,Request $request)
	{
		$api_response = $this->getUpdateApi($insurance_id, $id, Request::all());
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status=='failure_ins')
		{
			return redirect('/insurance')->with('error',$api_response_data->message);
		}
		
		if($api_response_data->status=='failure')
		{
			return redirect('/insurance/'.$insurance_id.'/insuranceoverrides')->with('error',$api_response_data->message);
		}
		
		$ids = Input::get('facilities_id');
		if($api_response_data->status == 'success')
		{
			return Redirect::to('insurance/'.$insurance_id.'/insuranceoverrides/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('insurance/'.$insurance_id.'/insuranceoverrides/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}     
	}
	/*** End to Update the Overrides	 ***/
	
	/*** Start to Destory the Overrides	 ***/
	public function destroy($insurance_id, $id)
	{
		$api_response = $this->getDeleteApi($insurance_id,$id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status=='failure_ins')
		{
			return redirect('/insurance')->with('error',$api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			return Redirect::to('insurance/'.$insurance_id.'/insuranceoverrides')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('insurance/'.$insurance_id.'/insuranceoverrides/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		} 
	}
	/*** End to Destory the Overrides	 ***/
	
	/*** Start to Show the Overrides	 ***/
	public function show($ids, $id)
	{
		$api_response 		= 	$this->getShowApi($ids, $id);
		$api_response_data 	= 	$api_response->getData();		
		
		if($api_response_data->status=='failure')
		{
			return redirect('/insurance')->with('error',$api_response_data->message);
		}
	
		if($api_response_data->status == 'success')
		{
			$overrides		 	= 	$api_response_data->data->overrides;
			$insurance		 	= 	$api_response_data->data->insurance;
			$address_flags = (array)$api_response_data->data->addressFlag;
			$address_flag['general'] = (array)$address_flags['general'];
			return view ( 'practice/insurance/overrides/show',compact('insurance','overrides','address_flag'));
		}
		else
		{
			return redirect('/insurance/'.$ids.'/insuranceoverrides')->with('error',$api_response_data->message);
		}
	}
	/*** End to Show the Overrides ***/
}
