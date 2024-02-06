<?php namespace App\Http\Controllers;

use Auth;
use View;
use Input;
use Request;
use Redirect;
use Config;
use App\Http\Controllers\Api\ProviderApiController as ProviderApiController;
use PDF;
use Excel;
use App\Exports\BladeExport;

class ProviderController extends ProviderApiController
{
	public function __construct()
	{
		View::share('heading','Practice');
		View::share('selected_tab','provider');
		View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
	}
	
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();		
		$providers 			= 	$api_response_data->data->providers;
		$provider_type		= 	$api_response_data->data->provider_type;
		return view ('practice/provider/provider', compact('providers','provider_type'));
	}
        
    public function providerExport($export = '') {
        $api_response = $this->getIndexApi($export);
        $api_response_data = $api_response->getData();
        $providers = $api_response_data->data->providers;
        $provider_type = $api_response_data->data->provider_type;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Provider_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/provider/provider_export_pdf', compact('providers', 'provider_type', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/provider/provider_export';
            $data['providers'] = $providers;
            $data['provider_type'] = $provider_type;
            $data['export'] = $export;
            ob_end_clean();
            ob_start();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/provider/provider_export';
            $data['providers'] = $providers;
            $data['provider_type'] = $provider_type;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	
	public function create()
	{
        $api_response 		= $this->getCreateApi();
        $api_response_data 	= $api_response->getData();
		$provider 			= $api_response_data->data->provider;
		$taxanomies 		= $api_response_data->data->taxanomies;
		$facilities 		= $api_response_data->data->facilities;
		$specialities 		= $api_response_data->data->specialities;
		$provider_type 		= $api_response_data->data->provider_type;
		$provider_degree 	= $api_response_data->data->provider_degree;
		$states 			= $api_response_data->data->states;
		$insurances 		= $api_response_data->data->insurances;               
		$degree_id 			= '';
		$taxanomies2 		= '';
		$taxanomy_id 		= '';
		$taxanomy_id2 		= '';
		$facility_id 		= '';
		$speciality_id 		= '';
		$speciality_id2 	= '';
		$provider_type_id 	= Input::old('provider_types_id');           
        $address_flags 		= (array)$api_response_data->data->addressFlag;
        $address_flag['general'] = (array)$address_flags['general'];
        $npi_flag 			= (array)$api_response_data->data->npi_flag;
        return view('practice/provider/create',compact('provider','taxanomies','taxanomies2','facilities','specialities','provider_type','provider_degree','degree_id','taxanomy_id','taxanomy_id2','facility_id','speciality_id','speciality_id2','provider_type_id','address_flag','npi_flag','states','insurances'));
	}
	
	public function store(Request $request)
	{
		$api_response 		= 	$this->getStoreApi();
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status=='error')
        {
            return redirect('/provider')->with('message',$api_response_data->message);
        }
		$provider 			= $api_response_data->data->provider;
		$facilities 		= $api_response_data->data->facilities;
		$specialities 		= $api_response_data->data->specialities;
        $taxanomies			= $api_response_data->data->taxanomies;
        $taxanomies2		= $api_response_data->data->taxanomies2;
		$provider_type 		= $api_response_data->data->provider_type;
		$provider_degree 	= $api_response_data->data->provider_degree;
		$degree_id 			= $provider->provider_degrees_id;
		$facility_id 		= $provider->def_facility;
		$speciality_id 		= $provider->speciality_id;
        $speciality_id2		= $provider->speciality_id2;
        $taxanomy_id 		= $provider->taxanomy_id;
        $taxanomy_id2 		= $provider->taxanomy_id2;
        $provider_type_id 	= $provider->provider_types_id;
        $insurances 		= $api_response_data->data->insurances;
        $states 			= $api_response_data->data->states;
		$address_flags 		= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] = (array)$address_flags['general'];
		$documents_personal_ssn 	= $api_response_data->data->documents_personal_ssn;
		$documents_PTAN 			= $api_response_data->data->documents_PTAN;
		$documents_medicaid_id 		= $api_response_data->data->documents_medicaid_id;
		$documents_bcbs_id 			= $api_response_data->data->documents_bcbs_id;
		$documents_aetna_id 		= $api_response_data->data->documents_aetna_id;
		$documents_uhc_id 			= $api_response_data->data->documents_uhc_id;
		$documents_other_id1 		= $api_response_data->data->documents_other_id1;
		$documents_other_id2 		= $api_response_data->data->documents_other_id2;
		$documents_other_id3 		= $api_response_data->data->documents_other_id3;
		$documents_tax_id 			= $api_response_data->data->documents_tax_id;
		$documents_state_license1 	= $api_response_data->data->documents_state_license1;
		$documents_state_license2 	= $api_response_data->data->documents_state_license2;
		$documents_state_license3 	= $api_response_data->data->documents_state_license3;
		$documents_dea_number 		= $api_response_data->data->documents_dea_number;
		$documents_mammography_cert 	= $api_response_data->data->documents_mammography_cert;
		$documents_care_plan_oversight 	= $api_response_data->data->documents_care_plan_oversight;
		// Added missing query 
		// Revision 1 - Ref: MED-2829 5 Augest 2019: Pugazh
		$documents_npi_id = $api_response_data->data->documents_npi_id;
		// dd($documents_npi_id);

		$npi_flag 			= (array)$api_response_data->data->npi_flag;

		return view( 'practice/provider/edit',compact('provider','taxanomies','taxanomies2','facilities','specialities','provider_type','provider_degree','degree_id','taxanomy_id','taxanomy_id2','facility_id','speciality_id','speciality_id2','provider_type_id','address_flag','npi_flag','insurances','states','documents_personal_ssn','documents_PTAN','documents_medicaid_id','documents_bcbs_id','documents_aetna_id','documents_uhc_id','documents_other_id1','documents_other_id2','documents_other_id3','documents_tax_id','documents_state_license1','documents_state_license2','documents_state_license3','documents_dea_number','documents_mammography_cert','documents_care_plan_oversight','documents_npi_id'));
	}
	
	public function update($id, Request $request)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status=='failure')
        {
            return redirect('/provider')->with('message',$api_response_data->message);
        }
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	
	public function destroy($id)
	{
		$api_response 		= 	$this->getDeleteApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success'){
			return Redirect::to('provider')->with ( 'success', $api_response_data->message );
		}
		elseif($api_response_data->status == 'relation_error'){
			return redirect('/provider/'.$id.'/edit')->with('error',$api_response_data->message);
		}
		else{
			return Redirect::to('provider')->with ( 'error', $api_response_data->message );
		}
	}
	
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status == 'success')
		{
			$provider					= $api_response_data->data->provider;
			$address_flags 				= (array)$api_response_data->data->addressFlag;
			$address_flag['general'] 	= (array)$address_flags['general'];
			$npi_flag 					= (array)$api_response_data->data->npi_flag;
			$documents_personal_ssn 	= $api_response_data->data->documents_personal_ssn;
			$documents_PTAN 			= $api_response_data->data->documents_PTAN;
			$documents_medicaid_id 		= $api_response_data->data->documents_medicaid_id;
			$documents_bcbs_id 			= $api_response_data->data->documents_bcbs_id;
			$documents_aetna_id 		= $api_response_data->data->documents_aetna_id;
			$documents_uhc_id 			= $api_response_data->data->documents_uhc_id;
			$documents_other_id1 		= $api_response_data->data->documents_other_id1;
			$documents_other_id2 		= $api_response_data->data->documents_other_id2;
			$documents_other_id3 		= $api_response_data->data->documents_other_id3;
			$documents_tax_id 			= $api_response_data->data->documents_tax_id;
			$documents_state_license1 	= $api_response_data->data->documents_state_license1;
			$documents_state_license2 	= $api_response_data->data->documents_state_license2;
			$documents_state_license3 	= $api_response_data->data->documents_state_license3;
			$documents_dea_number 		= $api_response_data->data->documents_dea_number;
			$documents_mammography_cert 	= $api_response_data->data->documents_mammography_cert;
			$documents_care_plan_oversight 	= $api_response_data->data->documents_care_plan_oversight;
			// Added missing query 
			// Revision 1 - Ref: MED-2654  06 Augest 2019: Pugazh
			$documents_npi_id = $api_response_data->data->documents_npi_id;

			return view ( 'practice/provider/show', ['provider' => $provider,'address_flag'=>$address_flag,'npi_flag'=>$npi_flag], compact('documents_personal_ssn','documents_PTAN','documents_medicaid_id','documents_bcbs_id','documents_aetna_id','documents_uhc_id','documents_other_id1','documents_other_id2','documents_other_id3','documents_tax_id','documents_state_license1','documents_state_license2','documents_state_license3','documents_dea_number','documents_mammography_cert','documents_care_plan_oversight','documents_npi_id') );
		}
		else
		{
			return redirect('/provider')->with('message',$api_response_data->message);
		}
	}
	
	public function get_sel_provider_type_display($sel_provider_id) 
	{
		$api_response = $this->api_get_sel_provider_type_display($sel_provider_id);
		echo $api_response;exit;
    }
	
	public function avatarprovider($id,$picture_name)
	{
		$api_response 		= $this->avatarapipicture($id,$picture_name);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('provider/'.$id.'/edit')->with('success', $api_response_data->message);
	}
	/*** Search Function Starts ***/
	public function searchIndexlist()
	{
		$request  = Request::all();
		$api_response = $this->getIndexApi('',$request);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$providers 			= 	$api_response_data->data->providers;
			return view ( 'practice/provider/provider-list', compact ('providers') );
		}
		else
		{
			print_r($api_response_data->message);
			exit;
		}
	}
	/*** Search Function Ends ***/
	
	
	public function trailProviderCreate(){
		$api_response 		= $this->getCreateApi();
        $api_response_data 	= $api_response->getData();
		$provider 			= $api_response_data->data->provider;
		$taxanomies 		= $api_response_data->data->taxanomies;
		$facilities 		= $api_response_data->data->facilities;
		$specialities 		= $api_response_data->data->specialities;
		$provider_type 		= $api_response_data->data->provider_type;
		$provider_degree 	= $api_response_data->data->provider_degree;
		$states 			= $api_response_data->data->states;
		$insurances 		= $api_response_data->data->insurances;               
		$degree_id 			= '';
		$taxanomies2 		= '';
		$taxanomy_id 		= '';
		$taxanomy_id2 		= '';
		$facility_id 		= '';
		$speciality_id 		= '';
		$speciality_id2 	= '';
		$provider_type_id 	= Input::old('provider_types_id');           
        $address_flags 		= (array)$api_response_data->data->addressFlag;
        $address_flag['general'] = (array)$address_flags['general'];
        $npi_flag 			= (array)$api_response_data->data->npi_flag;
        return view('practice/provider/trail_create',compact('provider','taxanomies','taxanomies2','facilities','specialities','provider_type','provider_degree','degree_id','taxanomy_id','taxanomy_id2','facility_id','speciality_id','speciality_id2','provider_type_id','address_flag','npi_flag','states','insurances'));
	}
	
	public function trailProviderStore(Request $request){
		$api_response 		= 	$this->getStoreTrailApi();
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	
	
}