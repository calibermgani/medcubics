<?php 
namespace App\Http\Controllers\Medcubics;
use View;
use Input;
use Request;
use Redirect;
use Config;

class InsuranceAppealAddressController extends Api\InsuranceAppealAddressApiController 
{
    public function __construct(Request $request) 
    {
        View::share ( 'heading', 'Insurance' );
        View::share ( 'selected_tab', 'admin/insurance' );
		View::share( 'heading_icon', Config::get('cssconfigs.common.insurance'));
    }
    
	/*** Start to Listing the Insurance Appeal Address	 ***/
    public function index($id)
    {
        $api_response = $this->getIndexApi($id);
        $api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
        {
			$insurance = $api_response_data->data->insurance;
			$appealaddress = $api_response_data->data->appealaddress;
			return view('admin/insurance/appealaddress/appealaddress', compact('appealaddress','insurance'));
		}
        else
        {
            return redirect('/admin/insurance')->with('message',$api_response_data->message);
        }
    }
	/*** End to Listing the Insurance Appeal Address	 ***/

	/*** Start to Create the Insurance Appeal Address	 ***/	
    public function create($id)
	{
        $api_response = $this->getCreateApi($id);
        $api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
        {
			$insurance = $api_response_data->data->insurance;
			$address_flags = (array)$api_response_data->data->addressFlag;
			$address_flag['appeal'] = (array)$address_flags['appeal']; 
			return view('admin/insurance/appealaddress/create_appealaddress',  compact('insurance','address_flag'));
		}
        else
        {
             return redirect('/admin/insurance')->with('error',$api_response_data->message);
        }
    }
	/*** End to Create the Insurance Appeal Address	 ***/
    
	/*** Start to Store the Insurance Appeal Address  ***/	
    public function store($id)
    {
        $api_response = $this->getStoreApi($id);
        $api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('/admin/insurance')->with('error', $api_response_data->message);
		}
        if($api_response_data->status == 'success')
        {
			return Redirect::to('/admin/insurance/'.$id.'/insuranceappealaddress/'.$api_response_data->data)->with('success', $api_response_data->message);
        }
        else
        {
			return Redirect::back()->withInput()->withErrors($api_response_data->message);
        }
    }
	/*** End to Store the Insurance Appeal Address	 ***/

	/*** Start to Edit the Insurance Appeal Address	 ***/	
	public function edit($ids,$id)
	{   
		$api_response = $this->getEditApi($ids,$id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status=='error')
		{
			return redirect('/admin/insurance/'.$ids.'/insuranceappealaddress')->with('message',$api_response_data->message);
		}
		if($api_response_data->status=='failure')
		{
			return redirect('/admin/insurance')->with('message',$api_response_data->message);
		}
		$appealaddress = $api_response_data->data->appealaddress;
		$insurance     = $api_response_data->data->insurance;
		$address_flags = (array)$api_response_data->data->addressFlag;
		$address_flag['appeal'] = (array)$address_flags['appeal'];
		return view('admin/insurance/appealaddress/edit_appealaddress', compact('appealaddress','insurance','address_flag'));
	} 
	/*** End to Edit the Insurance Appeal Address	 ***/	
	
	/*** Start to Update the Insurance Appeal Address	 ***/
	public function update($insurance_id, $id,Request $request)
	{
		$api_response = $this->getUpdateApi($insurance_id,$id, Request::all());
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status=='failure_ins')
		{
			return redirect('/admin/insurance')->with('error',$api_response_data->message);
		}
		
		if($api_response_data->status=='failure')
		{
			return redirect('/admin/insurance/'.$insurance_id.'/insuranceappealaddress')->with('error',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('/admin/insurance/'.$insurance_id.'/insuranceappealaddress/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('/admin/insurance/'.$insurance_id.'/insuranceappealaddress/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}     
	}  
	/*** End to Update the Insurance Appeal Address	 ***/

	/*** Start to Destory the Insurance Appeal Address	 ***/
	public function destroy($insurance_id, $id)
	{
		$api_response = $this->getDeleteApi($insurance_id,$id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status=='failure_ins')
		{
			return redirect('/admin/insurance')->with('error',$api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			return Redirect::to('/admin/insurance/'.$insurance_id.'/insuranceappealaddress')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('/admin/insurance/'.$insurance_id.'/insuranceappealaddress/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Destory the Insurance Appeal Address	 ***/
	
	/*** Start to Show the Insurance Appeal Address	 ***/
	public function show($ids, $id)
	{
		$api_response 		= 	$this->getShowApi($ids, $id);
		$api_response_data 	= 	$api_response->getData();		
                
		if($api_response_data->status=='failure')
		{
			return redirect('/admin/insurance')->with('message',$api_response_data->message);
		}
		$appealaddress		 	= 	$api_response_data->data->appealaddress;
		$insurance		 	= 	$api_response_data->data->insurance;
		$address_flags = (array)$api_response_data->data->addressFlag;
		$address_flag['general'] = (array)$address_flags['appeal'];
	
		if($api_response_data->status == 'success')
		{
			return view ( 'admin/insurance/appealaddress/show',compact('insurance','appealaddress','address_flag'));
		}
		else
		{
			return redirect('/admin/insurance/'.$ids.'/insuranceappealaddress')->with('message',$api_response_data->message);
		}
	}
	/*** End to Show the Insurance Appeal Address	 ***/
}