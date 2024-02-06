<?php namespace App\Http\Controllers;

use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use View;
use Session;
use Cache;
use Config;
use DB;
use App;
use App\Models\Practice as Practice;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Controllers\Profile\Api\ProfileApiController as ProfileApiController;
use App\Http\Helpers\Helpers;

class PracticesController extends Api\PracticesApiController 
{
	public function __construct() 
	{      
       View::share ( 'heading', 'Practice' ); 
	   View::share ( 'selected_tab', 'practice' );
	   View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  

    public function index() 
	{		
		/// Need to do api since its static ///
		$practices = Practice::all();		
        return view('practice/practice/practice', compact('practices'));
    }

    public function edit($id) 
	{
 		$api_response 		= $this->getEditApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$practice 			= $api_response_data->data->practice;
			$specialities 		= $api_response_data->data->specialities;
			$speciality_id 		= $api_response_data->data->speciality_id;
			$languages 			= $api_response_data->data->language;
			$language_id 		= $api_response_data->data->language_id;
			$taxanomies 		= $api_response_data->data->taxanomies;
			$taxanomy_id 		= $api_response_data->data->taxanomy_id;
			$address_flags 		= (array)$api_response_data->data->addressFlag;
			$address_flag['pta'] = (array)$address_flags['pta'];
			$address_flag['ma'] = (array)$address_flags['ma'];
			$address_flag['pa'] = (array)$address_flags['pa'];
			$npi_flag 			= (array)$api_response_data->data->npi_flag;
            $time 				= (array)$api_response_data->data->time;
			return view('practice/practice/edit', compact('practice', 'specialities','speciality_id','languages','language_id','taxanomies','taxanomy_id','address_flag','npi_flag','time'));
		}
		else
		{
			if(Auth::user()->user_type == 'Medcubics')
			{
				return Redirect::to('/admin/customer')->with('error', $api_response_data->message);
			} 
			else 
			{
				return Redirect::to('/')->with('error', $api_response_data->message);
			}	
		}	
	}

	public function update($id,Request $request)
	{
		$api_response 		= $this->getUpdateApi($id, $request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('practice/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('practice/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
    }
    
    public function show($id) 
	{  		
		// dd($id);
		$dbconnection = new DBConnectionController();
		if(!$dbconnection->checkAllowToAccess('practice')) {
			return Redirect::to('dashboard')->with('error', 'You are not authorized to view this page.');
		} 

 		$api_response 		= $this->getShowApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$practice = $api_response_data->data->practice;
			$specialities = $api_response_data->data->specialities;
			$speciality_id = $api_response_data->data->speciality_id;
			$languages = $api_response_data->data->language;
			$language_id = $api_response_data->data->language_id;
			$taxanomies = $api_response_data->data->taxanomies;
			$taxanomy_id = $api_response_data->data->taxanomy_id;
			$address_flags = (array)$api_response_data->data->addressFlag;
			$address_flag['pta'] = (array)$address_flags['pta'];
			$address_flag['ma'] = (array)$address_flags['ma'];
			$address_flag['pa'] = (array)$address_flags['pa'];
			$npi_flag = (array)$api_response_data->data->npi_flag;

			// For set page title
			$details['practice_name'] = $practice->practice_name;				
			App\Http\Helpers\Helpers::setPageTitle('practice', $details);

			return view('practice/practice/show', compact('practice', 'specialities','speciality_id','languages','language_id','taxanomies','taxanomy_id','address_flag','npi_flag'));
		}
		else
		{
			if(Auth::user()->user_type == 'Medcubics')
			{
				return Redirect::to('/admin/customer')->with('error', $api_response_data->message);
			} 
			else 
			{
				return Redirect::to('/')->with('error', $api_response_data->message);
			}
		}
	}
    
   	public function updatetimesubmiteddate($id)
	{
		$api_response 		= $this->updatetimesubmiteddateApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
			$data['msg'] = "successfullu updated";
			$data['status'] = "success";
		return $data;	
	   
    }
    public function updatetimefileddate($id)
	{
		$api_response 		= $this->updatetimefileddateApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
			$data['msg'] = "successfullu updated";
			$data['status'] = "success";
		return $data;	
	   
    } 
    public function getWishlist($current_page = '')
	{	 
		
		 $list = Helpers::wishList(Auth::user()->id,$current_page);
		
		 //$get_module_count = $messagenotes->getProfileModuleCount();		 
		 return view('layouts/wishlist-notification', compact('list'));   
    } 
    public function messageNotes()
	{	
		 $messagenotes = new ProfileApiController();
		 $get_module_count = $messagenotes->getProfileModuleCount(Auth::user()->id);		 
		 return view('layouts/message-notes-notification', compact('get_module_count'));   
    } 
    public function listpractice()
	{ /// Need to do api since its static ///
        return view('practice/practice/get_taxanomies.php');
    }
	
	public function setCollapse()
    {
        $collapse = Request::input('is_sidebar_collapse');
        if($collapse == 'hide')
            Cache::forever ('sidebar_class','');
        else
            Cache::forever ('sidebar_class', 'sidebar-collapse');        
    }
	
	public function taxanomies()
	{
		return $this->getTaxanomies();
	}
	
	public function switchuser()
	{
	
		if(DB::connection()->getDatabaseName())
		{
		   /** Remove schedular cache ***/
			Cache::forget('default_view');
			Cache::forget('default_view_list_id');
			// If link is saved in book mark redirect listing page
			$practice_id = Session::get('practice_dbid');		   
			$dbconnection = new DBConnectionController();   
			$dbconnection->clearDBSession();
		   
		   //DB::connection()->getDatabaseName()->clearDBSession();
		   return Redirect::to('/');
		}
		/*if(Session::has('practice_dbid'))
		{	
			$dbconnection = new DBConnectionController();
			$dbconnection->clearDBSession();
			//$admin_db_name = getenv('DB_DATABASE');
			//$dbconnection->configureConnectionByName($admin_db_name);
			return Redirect::to('/');			
		}
		if(Auth::user()->user_type == 'Medcubics')
		{
			return Redirect::to('/admin/customer');
		} 
		else 
		{
			return Redirect::to('/practice/'.Auth::user()->id);
		}*/
	}

	public function avatar_picture($id,$picture_name)
	{	
		$api_response 		= $this->getDeleteApi($id,$picture_name);
		$api_response_data 	= $api_response->getData();
		return redirect()->back()->withInput()->with ( 'message', "Enter a practice name to start with.");
		//return Redirect::to('practice/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
	}
}