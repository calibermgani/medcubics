<?php namespace App\Http\Controllers;

use View;
use Auth;
use Request;
use Redirect;
use Config;
use App\Http\Controllers\Api\InsuranceApiController as InsuranceApiController;
use PDF;
use Excel;
use App\Exports\BladeExport;

class InsuranceController extends InsuranceApiController
{
	public function __construct()
	{
		View::share ( 'heading', 'Practice' );
		View::share ( 'selected_tab', 'insurance' );
		View::share( 'heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
	}
	
	/*** Start to Listing the Insurance	 ***/
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$insurances 		= $api_response_data->data->insurances;
		return view ( 'practice/insurance/insurance', compact ( 'insurances' ) );
	}
	/*** End to Listing the Insurance	 ***/
        
    public function getInsuranceExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $insurances = $api_response_data->data->insurances;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Insurance_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/insurance/insurance_export_pdf', compact('insurances', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/insurance/insurance_export';
            $data['insurances'] = $insurances;
			$data['export'] = $export;
			$data['file_path'] = $filePath;
			return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/insurance/insurance_export';
            $data['insurances'] = $insurances;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	
	/*** Start to Create the Insurance	 ***/
	public function create()
	{
		$api_response 				= $this->getCreateApi();
		$api_response_data 			= $api_response->getData();
		$insurancetypes 			= $api_response_data->data->insurancetypes;
		$insuranceclasses			= $api_response_data->data->insuranceclasses;
		$insurancetype_id 			= $api_response_data->data->insurancetype_id;
		$insuranceclass_id 			= $api_response_data->data->insuranceclass_id;
		$address_flags 				= (array)$api_response_data->data->addressFlag;
		$claimformats 				= (array)$api_response_data->data->claimformats;
		$cmstypes 					= $api_response_data->data->inscmstypes;		
		
		$address_flag['general'] 	= (array)$address_flags['general'];
        // $address_flag['appeal'] 	= (array)$address_flags['appeal'];     commented by revathi on April 12 as because we are not using it anymore.
		return view ( 'practice/insurance/create', compact ( 'claimformats','insurancetypes', 'insuranceclasses', 'claimtype_id','claimformat_id','insurancetype_id','insuranceclass_id','address_flag','cmstypes' ) );
	}
	/*** End to Create the Insurance	 ***/
	
	/*** Start to Store the Insurance	 ***/
	public function store(Request $request)
	{
		$api_response 		= 	$this->getStoreApi();
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('insurance/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Store the Insurance	 ***/
	
	/*** Start to Edit the Insurance	 ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status=='success')
		{
			$insurance 				= $api_response_data->data->insurance;
			$insurancetypes 		= $api_response_data->data->insurancetypes;
			$insuranceclasses		= $api_response_data->data->insuranceclasses;
			$claimformats			= $api_response_data->data->claimformats;
			$claimtype_id 			= $api_response_data->data->claimtype_id;
			$claimformat_id 		= $api_response_data->data->claimformat_id;
			$insurancetype_id 		= $api_response_data->data->insurancetype_id;
			$insuranceclass_id 		= $api_response_data->data->insuranceclass_id;
			$address_flags 			= (array)$api_response_data->data->addressFlag;
			$address_flag['general']= (array)$address_flags['general'];
		  //  $address_flag['appeal'] = (array)$address_flags['appeal'];
			$cmstypes 				= $api_response_data->data->inscmstypes;

			return view ( 'practice/insurance/edit', compact ('insurance','insurancetypes','insuranceclasses','claimformats','claimtype_id','claimformat_id','insurancetype_id','insuranceclass_id','address_flag','cmstypes') );
		}
		else
		{
			return redirect('/insurance')->with('error',$api_response_data->message);
		}
	}
	/*** End to Edit the Insurance	 ***/
	
	/*** Start to Update the Insurance	 ***/
	public function update($id, Request $request)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') {
				 return Redirect::to('insurance')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('insurance/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Update the Insurance	 ***/
	
	/*** Start to Destory the Insurance	 ***/
	public function destroy($id)
	{
		$api_response 		= $this->getDeleteApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('insurance')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('insurance')->with('error', $api_response_data->message);
		}
	}
	/*** End to Destory the Insurance	 ***/
	
	/*** Start to Show the Insurance	 ***/
	public function show($id)
	{	
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();		
                
		if($api_response_data->status == 'success')
		{
			$insurance		 	= 	$api_response_data->data->insurance;
			$address_flags = (array)$api_response_data->data->addressFlag;
			$claimformats 		= json_decode(json_encode($api_response_data->data->claimformats), True); 
			$address_flag['general'] = (array)$address_flags['general'];
			return view ( 'practice/insurance/show',compact('claimformats','insurance','address_flag'));
		}
		else
		{
			return redirect('/insurance')->with('error',$api_response_data->message);
		}
	}
	/*** End to Show the Insurance	 ***/
	
	/*** Start to New Select the Insurance	 ***/
	public function addnewselect()
	{
		$tablename = Request::input('tablename');
		$fieldname = Request::input('fieldname');
		$addedvalue = Request::input('addedvalue');		
		$addedcms_type = '';
		if(Request::has('cms_type')){
			$addedcms_type = Request::input('addedvalue');		
		}
		// For claim sub status unset option included
		if($tablename == 'claim_sub_status' && trim($addedvalue) == '-NA-'){
			$addedvalue = 0;
		}
		$addedvalue = Request::input('addedvalue', 'addedcms_type');		
		return $this->addnewApi($addedvalue);			
	}
	
	public function avatarinsurance($id,$picture_name)
	{
		$api_response 		= $this->avatarapipicture($id,$picture_name);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('insurance/'.$id.'/edit')->with($api_response_data->message);
	}
	/*** End to New Select the Insurance	 ***/
	
	public function GetInsuranceList($insurancename='',$search_category='')
	{
		$api_response 		= $this->getInsList($insurancename,$search_category);
		$api_response_data 	= $api_response->getData();
		$insurance_arr		 	= 	$api_response_data->data->get_Inslist;
		
		return view( 'practice/insurance/insurancelist',compact('insurance_arr'));
	}
	
	public function GetInsuranceDetails($insid='')
	{
		$api_response 		= $this->getInsDetails($insid);
		$api_response_data 	= $api_response->getData();
		
		$insurance_arr[1] = $api_response_data->data->image_tag;
		$insurance_arr[0] = $api_response_data->data->get_Insinfo;
		
		return $insurance_arr;
	}
}
