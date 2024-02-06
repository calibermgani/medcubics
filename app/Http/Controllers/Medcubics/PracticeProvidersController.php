<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use View;
use Input;
use DB;
use Artisan;
use Session;
use App;
use Route;
use Config;

class PracticeProvidersController extends Api\PracticeProvidersApiController 
{

	public function __construct() 
	{       
		View::share('heading', 'Customer');  
        View::share('selected_tab', 'provider_details');  
		View::share('heading_icon', Config::get('cssconfigs.admin.users'));
    }  
	
	/********************** Start Display a listing of the providers ***********************************/
	public function index($cust_id,$practice_id)
	{	
            $api_response 		= $this->getIndexApi($practice_id);
            $api_response_data 	= $api_response->getData();
		if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
		$practice 		= $api_response_data->data->practice;
        $providers 		= $api_response_data->data->providers;
		$customer_id 	= $api_response_data->data->customer_id;
		$customer 		= $api_response_data->data->customer;		
        return view('admin/customer/practiceproviders/index', compact('customer_id','customer','providers','cust_id','practice'));	
	}
	/********************** End Display a listing of the providers ***********************************/

	/********************** Start Display the provider create page ***********************************/
	public function create($cust_id,$practice_id)
	{
		$api_response 		= $this->getCreateApi($practice_id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
		$providers 			= $api_response_data->data->providers;			
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
		$provider_type_id 	= '';           
        $address_flags 				= (array)$api_response_data->data->addressFlag;
        $address_flag['general'] 	= (array)$address_flags['general'];
        $npi_flag 					= (array)$api_response_data->data->npi_flag;			
        return view ( 'admin/customer/practiceproviders/create', compact('cust_id','practice_id','providers','taxanomies','taxanomies2','facilities','specialities','provider_type','provider_degree','degree_id','taxanomy_id','taxanomy_id2','facility_id','speciality_id','speciality_id2','provider_type_id','address_flag','npi_flag','states','insurances'));
	}
	/********************** End Display the provider create page ***********************************/

	/********************** Start provider added process ***********************************/
	public function store(Request $request)
	{		
		$requestdetails 		= $request::all();			
		$cust_id 				= $requestdetails['customer_id'];
		$practice_id 			= $requestdetails['practice_id'];
		$api_response 			= 	$this->getStoreApi();
		$api_response_data 		= 	$api_response->getData();			
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{			
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/********************** End provider added process ***********************************/
	
	/********************** Start provider details show page ***********************************/
	public function show($cust_id,$practice_id,$provider_id)
	{
		
		$api_response 		= 	$this->getShowApi($cust_id,$practice_id,$provider_id);
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
		$provider					= 	$api_response_data->data->provider;
        $address_flags 				= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] 	= (array)$address_flags['general'];
		$npi_flag 					= (array)$api_response_data->data->npi_flag;
        $practice_name 				= $api_response_data->data->practice_name;
        $facility_name 				= $api_response_data->data->facility_name;
		
		if($api_response_data->status == 'success')
		{
			return view ( 'admin/customer/practiceproviders/show', ['cust_id'=>$cust_id,'practice_id'=>$practice_id,'provider' => $provider,'address_flag'=>$address_flag,'npi_flag'=>$npi_flag,'practice_id' => $practice_name->id,'facility_name'=>$facility_name] );
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}		
	}
	/********************** End provider details show page ***********************************/
	
	/********************** Start provider edit page display ***********************************/
	public function edit($cust_id,$practice_id,$id)
	{		
		$api_response 		= 	$this->getEditApi($cust_id,$practice_id,$id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
		$provider 			= 	$api_response_data->data->provider;
		$taxanomies			= 	$api_response_data->data->taxanomies;
        $taxanomies2		= 	$api_response_data->data->taxanomies2;
		$facilities 		= 	$api_response_data->data->facilities;
		$specialities 		= 	$api_response_data->data->specialities;
		$provider_type 		= 	$api_response_data->data->provider_type;
		$provider_degree 	= 	$api_response_data->data->provider_degree;
		$degree_id 			= 	$provider->provider_degrees_id;
		$taxanomy_id 		= 	$api_response_data->data->taxanomy_id;
    	$facility_id 		= 	$provider->	def_facility;
		$speciality_id 		= 	$provider->speciality_id;
        $speciality_id2		= 	$provider->speciality_id2;
		$provider_type_id 	= 	$provider->provider_types_id;
        $taxanomy_id2 		= 	$provider->taxanomy_id2;
        $insurances 		= $api_response_data->data->insurances;
        $states 			= $api_response_data->data->states;
        $practice_name 		= $api_response_data->data->practice_name;
		$address_flags 		= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] 	= (array)$address_flags['general'];
		$npi_flag 					= (array)$api_response_data->data->npi_flag;
		
		return view ( 'admin/customer/practiceproviders/edit', compact ('cust_id','practice_id','provider','taxanomies','taxanomies2','facilities','specialities','provider_type','provider_degree','degree_id','taxanomy_id','taxanomy_id2','facility_id','speciality_id','speciality_id2','provider_type_id','address_flag','npi_flag','insurances','states','practice_name') );
	}
	/********************** End provider edit page display ***********************************/
	
	/********************** Start provider update process ***********************************/
	public function update($cust_id,$practice_id,$id, Request $request)
	{		
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/********************** End provider update process ***********************************/
	
	/********************** Start provider deleted process ***********************************/
	public function destroy($cust_id,$practice_id,$id)
	{
		$api_response 		= 	$this->getDeleteApi($practice_id,$id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success'){
			return Redirect::to('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers')->with ( 'success', $api_response_data->message );
		}
		elseif($api_response_data->status == 'relation_error'){
			return redirect('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers/'.$id.'/edit')->with('error',$api_response_data->message);
		}
		else{
			return redirect()->back()->with ( 'error', $api_response_data->message );
		}
	}
	/********************** Start provider deleted process ***********************************/
	
	/********************** Start provider Image process ***********************************/
	public function avatarProvider($c_id,$practice_id,$provider_id,$picture_name)
	{   //dd($c_id,$practice_id,$provider_id,$picture_name);
		$api_response 		= $this->getavatarProvider($c_id,$practice_id,$provider_id,$picture_name);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('admin/customer/'.$c_id.'/practice/'.$practice_id.'/providers/'.$provider_id.'/edit')->withInput()->with($api_response_data->message);
	}
	/********************** Stop provider Image process ***********************************/
}
