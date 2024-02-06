<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Excel;
use Input;
use User;
use DB;
use Config;
use Validator;
use Response;
use Schema;
use Lang;
use App\Models\Cpt;
use App\Models\ProcedureCategory as ProcedureCategory;
use App\Models\Insurance;
use App\Models\MultiFeeschedule as MultiFeeschedule;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use PDF;
use App\Http\Helpers\Helpers as Helpers;
use App\Exports\BladeExport;

class ProcedureCategoryController extends Api\CptApiController 
{
	public function __construct() 
	{ 
		View::share('heading', 'Practice');  
		View::share('selected_tab', 'procedurecategory');
		View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }

	/*** Procedure Category lists page Starts ***/
	public function index()
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$category = ProcedureCategory::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->orderBy('created_by', 'desc')->get();
		return view('practice/procedurecategory/index', compact('category'));
	}
	/*** Procedure Category lists page Ends ***/
	
    public function getProcedureCategoryExport($export=''){
    	$practice_timezone = Helpers::getPracticeTimeZone();
        $category = ProcedureCategory::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->orderBy('created_by', 'desc')->get();
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'ProcedureCategory_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/procedurecategory/procedurecategory_export_pdf', compact('category', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/procedurecategory/procedurecategory_export';
            $data['category'] = $category;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/procedurecategory/procedurecategory_export';
            $data['category'] = $category;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
        
	/*** Procedure Category create page Starts ***/
	public function create()
	{
		return view('practice/procedurecategory/create');
	}
	/*** Procedure Category create page Ends ***/
	
	/*** Procedure Category form submission Starts ***/
	public function store(Request $request)
	{	     
		$data = Request::all();
		$data['created_by'] = Auth::User()->id;
		$category = ProcedureCategory::create($data);
		if(!empty($category->id)) {
			return Redirect::to('procedurecategory')->with('success');
		} else {
			return Redirect::to('procedurecategory/create')->withInput()->withErrors(Lang::get("common.validation.empty_record_msg"));
		}             
	}
	/*** Procedure Category form submission Ends ***/

	/*** Procedure Category details show page Starts ***/
	public function show($id)
	{		
		$practice_timezone = Helpers::getPracticeTimeZone();
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(ProcedureCategory::where('id',$id)->count()>0 && is_numeric($id)) {		
			$procedurecategory = ProcedureCategory::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->where('id',$id)->first();
			
			return view ( 'practice/procedurecategory/show',compact('procedurecategory'));
		} else {
			return Redirect::to('procedurecategory')->with('error', Lang::get("common.validation.empty_record_msg"));		   
		}
	}
	/*** Procedure Category form submission Ends ***/
	
	/*** Procedure Category details edit page Starts ***/
	public function edit($id)
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(ProcedureCategory::where('id',$id)->count()>0 && is_numeric($id)) { 			// Check invalid id		
			$procedurecategory = ProcedureCategory::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->where('id',$id)->first();
			
			return view ( 'practice/procedurecategory/edit',compact('procedurecategory'));
		} else {
			return Redirect::to('procedurecategory')->with('error', Lang::get("common.validation.empty_record_msg"));		   
		}
	}
	/*** Procedure Category details edit page Ends ***/
	
	/*** Procedure Category details update Starts ***/
	public function update($id,Request $request)
	{		
		if($request == '')
			$request = Request::all();
		
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(ProcedureCategory::where('id',$id)->count()>0 && is_numeric($id)) {		// Check invalid id		
			// Check the option for unique
			$rules = array('procedure_category' => 'required|unique:procedure_categories,procedure_category,'.$id.',id,deleted_at,NULL', 'status' => 'required');
			$messages = ProcedureCategory::messages();
			$validator = Validator::make(Request::all(), $rules, $messages );
			// Check validation.
			if ($validator->fails()) {	
				$errors = $validator->errors(); 
				return Redirect::to('procedurecategory/'.$id.'/edit')->withInput()->withErrors($errors);
			} else {	
				$procedurecategory = ProcedureCategory::findOrFail($id);
				$procedurecategory->update(Request::all());
				$user = Auth::user ()->id;
				$procedurecategory->updated_by = $user;
				$procedurecategory->updated_at = date('Y-m-d h:i:s');
				$procedurecategory->save ();
				return Redirect::to('procedurecategory')->with('success', Lang::get("common.validation.update_msg"));				
			}
		} else { 
			return Redirect::to('procedurecategory/'.$id.'/edit')->withInput()->withErrors(Lang::get("common.validation.empty_record_msg"));
		}

		// $api_response = $this->getUpdateApi(Request::all(), $id);
		// $api_response_data = $api_response->getData();
		
		// if($api_response_data->status == 'failure') 
		// {
		// 	return Redirect::to('cpt')->with('error', $api_response_data->message);
		// }
		
		// if($api_response_data->status == 'success')
		// {
		// 	return Redirect::to('cpt/'.$id)->with('success',$api_response_data->message);
		// }
		// else
		// {
		// 	return Redirect::to('cpt/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		// }        
	}
	/*** Procedure Category details update Ends ***/
	
	/*** Procedure Category details delete Starts ***/
	public function destroy($id)
	{
		dd($id);
		// $api_response = $this->getDeleteApi($id);
		// $api_response_data = $api_response->getData();
		// if($api_response_data->status == 'success')
		// {
		// 	return Redirect::to('cpt')->with('success',$api_response_data->message);
		// }
		// else
		// {
		// 	return Redirect::to('cpt')->with('error', $api_response_data->message);
		// } 
	}
	/*** Procedure Category details delete Ends ***/	
}
