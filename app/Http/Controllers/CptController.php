<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Excel;
use App\Imports\WorkRvuImport;
use Input;
use User;
use DB;
use Config;
use App\Models\Cpt;
use App\Models\ProcedureCategory;
use App\Models\Insurance;
use App\Models\MultiFeeschedule as MultiFeeschedule;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use PDF;
use Validator;
use App\Exports\BladeExport;

class CptController extends Api\CptApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'cpt' );
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	/*** Cpt lists page Starts ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$cpt_arr = $api_response_data->data->cpt_arr;
		return view('practice/cpt/cpt',  compact('cpt_arr'));
	}
	/*** Cpt lists page Ends ***/
    public function getCptMasterExport($export=''){
        $api_response = $this->getCptApi();
        $api_response_data = $api_response->getData();
        $cpt_master = $api_response_data->data->cpt_arr;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'CPT/HCPCS_Master_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/cpt/cpt_master_export_pdf', compact('cpt_master', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/cpt/cpt_master_export';
            $data['cpt_master'] = $cpt_master;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/cpt/cpt_master_export';
            $data['cpt_master'] = $cpt_master;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
        
    public function getCptFavoritesExport($export=''){
        $api_response = $this->getListFavouritesApi();
        $api_response_data = $api_response->getData();
        $cpt_favorites = $api_response_data->data->favourites;
        $header = $api_response_data->data->header;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'CPT/HCPCS_Favorites_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/cpt/cpt_favorites_export_pdf', compact('cpt_favorites', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/cpt/cpt_favorites_export';
            $data['cpt_favorites'] = $cpt_favorites;
			$data['export'] = $export;
			$data['file_path'] = $filePath;
			$data['header'] = $header;
			return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/cpt/cpt_favorites_export';
            $data['cpt_favorites'] = $cpt_favorites;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	
	/*** Cpt create page Starts ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$pos = $api_response_data->data->pos;
		$qualifier = $api_response_data->data->qualifier;
		$modifier = $api_response_data->data->modifier;	
		return view('practice/cpt/create', compact('modifier','pos','qualifier'));
		//return view('practice/cpt/create', compact('pos','pos_id','qualifier'));
	}
	/*** Cpt create page Ends ***/
	
	/*** Cpt form submission Starts ***/
	public function store(Request $request)
	{	     
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('cpt')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('cpt/create')->withInput()->withErrors($api_response_data->message);
		}             
	}
	/*** Cpt form submission Ends ***/
	
	/*** Cpt details show page Starts ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$cpt = $api_response_data->data->cpt;
			return view('practice/cpt/show',  compact('cpt','id'));	
		}
		else
		{
			return Redirect::to('cpt')->with('error', $api_response_data->message);
		}
	}
	/*** Cpt form submission Ends ***/
	
	/*** Cpt details edit page Starts ***/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$cpt = $api_response_data->data->cpt;
			$pos = $api_response_data->data->pos;
			$pos_id = $api_response_data->data->pos_id;	
			$qualifier = $api_response_data->data->qualifier;	
			$modifier = $api_response_data->data->modifier;
			return view('practice/cpt/edit', compact('cpt','pos','pos_id','id','qualifier','modifier'));
		}
		else
		{
			return Redirect::to('cpt')->with('error', $api_response_data->message);
		}
	}
	/*** Cpt details edit page Ends ***/
	
	/*** Cpt details update Starts ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('cpt')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('cpt/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('cpt/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Cpt details update Ends ***/
	
	/*** Cpt details delete Starts ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('cpt')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('cpt')->with('error', $api_response_data->message);
		} 
	}
	/*** Cpt details delete Ends ***/
	
	/*** Cpt favoritelist Starts ***/
	public function listFavourites()
	{
		$api_response 		= $this->getListFavouritesApi();
		// dd($api_response);
		$api_response_data 	= $api_response->getData();
		$favourites 		= $api_response_data->data->favourites;

		$cpt_details 		= $this->getIndexApi();
		$cpt_data 			= $cpt_details->getData();
		$cpt_arr 			= $cpt_data->data->cpt_arr;
		if(Request::ajax()){
			return view('practice/cpt/cpt_favourites', compact('favourites','cpt_arr'));
		} else{
			return view('practice/cpt/favourites', compact('favourites','cpt_arr'));
		}
	}
	/*** Cpt favoritelist Ends ***/

	public function getpracticelists($database_name)
    {
        $db = new DBConnectionController();            
        $db->configureConnectionByName($database_name);
        $list = DB::connection($database_name)->select("select id,customer_id,practice_name from practices where status='Active'");
        return $list;
    }
	/*** Cpt updatelist Starts ***/
	public function cptUpdate()
	{
		$file 		= Request::file('sample_file');
		$request 	= Request::all();
		$user_id 		= Auth::user()->id;
		$org_filename	= $file->getClientOriginalName();
		$new_filename	= md5('FEES'.strtotime(date('Y-m-d H:i:s'))).".".$file->getClientOriginalExtension();
		$chk_env_site   = getenv('APP_ENV');
		$rows = Excel::toArray(new WorkRvuImport(),$file);
        try {
        	// Requirement pending
			/*if(!empty($rows))	
				foreach($rows[0] as $row){
					if($row[0]!='cpt_hcpcs'){
	                    $cpt = Cpt::where('cpt_hcpcs', $row[0])->first();
	                    if($row[1]==$cpt['cpt_hcpcs']){
	                    	$success = Cpt::where('cpt_hcpcs',$row[0])->update (['short_description' => $row [1],'work_rvu' => $row[2]]);
	                    }
	                }
                }*/
        }
        catch(Exception $e){
        	Log::info("Error on Update Cpts; Message: " . $e->getMessage());
			return Redirect::to('cptimport')->with('error','Unable to Upload');
        }
		return Redirect::to('cptimport')->with('success', 'Record updated');
	}

	public function cptImport(Request $request)
	{
		return view('practice/cpt/cpt_import');
	}
	/*** Cpt updatelist Ends ***/

	/*** Cpt favoritelist popup Starts ***/
	public function toggleFavourites($id)
	{
		return $this->getToggleFavouritesApi($id);
	}
	/*** Cpt favoritelist popup Ends ***/
	
	/*** Cpt details search page Starts ***/
	public function searchIndex()
	{
        return view('practice/cpt/search');
	}
	/*** Cpt details search page Ends ***/

	/*** Cpt import from master starts ***/
	public function importMasterCpt(){
		return $this->massImportCpt();
    }
	/*** Cpt import from master ends ***/
	
	
	public function getyearInsurance($year){
		if ($year == 'undefined') {
			$insuranceArr = [];
			return $insuranceArr;
		}
		else {
		$insuranceIDs = MultiFeeschedule::where('year',$year)->where('status','Active')->select('insurance_id')->groupBy('insurance_id')->pluck('insurance_id')->all();
		$insuranceArr = Insurance::whereIn('id',$insuranceIDs)->pluck('short_name','id')->all();
		return $insuranceArr;			
		}

	}
	
	public function multiFeeScheduleData(){
		$api_response 		= $this->multiFeeScheduleDataApi();
		$api_response_data 	= $api_response->getData();
		$data 		= $api_response_data->data->data;
		$data = (isset($data)) ? json_encode($data) : '';
		return $data;
	}
	public function get_SampleCpt_file($type)
	{
		$file_path = $this->api_get_SampleCpt_file($type);
		return (new Response($file_path,200))->header('Content-Type','application/vnd.ms-excel');
	}
	
}
