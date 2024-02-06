<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Provider_degree as Provider_degree;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Lang;
use App\Models\Medcubics\Practice as Practice;
use DB;

class ProviderDegreeApiController extends Controller 
{
	
	/********************** Start Display a listing of the degree ***********************************/
	public function getIndexApi($export = "")
	{
		$degrees = Provider_degree::with('user','userupdate')->get();
		if($export != "")
		{
			$exportparam 	= 	array(
								'filename'	=>	'Provider Degree Medcubics',
								'heading'	=>	'Provider Degree Medcubics',
								'fields' 	=>	array(
												'degree_name'	=> 'Provider Degree Name',
												'created_at' 	=> 'Created On',
												'updated_at'	=> 'Updated On',
												'created_by'    => array('table'=>'user' ,'column' => 'short_name' ,'label' => 'Created by'),
												'updated_by'    => array('table'=>'userupdate' ,'column' => 'short_name' ,'label' => 'Updated by'),
												)
								);
			$callexport 	= new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $degrees, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('degrees')));
	}
	/********************** End Display a listing of the degree ***********************************/
	
	/********************** Start degree created page display*********************************************/
	public function getCreateApi()
	{
		$degrees = Provider_degree::all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('degrees')));
	}
	/********************** End degree created page display*********************************************/
	
	/************************** Start Degree store process*********************************************/
	public function getStoreApi($request='')
	{
		if($request == '')
		{
			$request = Request::all();
		}
		$validator = Validator::make($request, Provider_degree::$rules, Provider_degree::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$degrees 	= Provider_degree::create($request);
			$user 		= Auth::user ()->id;
			$degrees->created_by = $user;
			//$degrees->updated_at = "";
			$degrees->save ();
			$insertedId = Helpers::getEncodeAndDecodeOfId($degrees->id,'encode');
			### INSERT the POS To All Practice thilagavathy P start 28 nov 2019
			$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
			$practices = Practice::where("status","Active")->pluck('practice_name', 'id')->all();
	        $practice_list = [];
	        foreach ($practices as $key => $pra) {
	            $tenantDBs = str_replace(' ', '_', strtolower($pra));
	            $stats = DB::select($query, [$tenantDBs]);
	            if(!empty($stats))
		            $practice_list[$key] = $tenantDBs;   
	        }
	        // Included admin db name for execute query.
	        $adminDB = env('DB_DATABASE');		        
	        array_push($practice_list, $adminDB);
	      
			if(!empty($practice_list)){
				$success_practice = $failure_practice = $message = [];					
				foreach ($practice_list as $k=>$value) {					
					$db = new DBConnectionController();
					$db_name = $db->getpracticedbname($value);
					$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
          			$dbs = DB::select($query, [$db_name]);      
          			echo "<br> Start processing :".$db_name."<br>";					       
	    		if(!empty($dbs)){
                    $db = new DBConnectionController();			 	
                    $db->configureConnectionByName($db_name);
                    $mysqldbconn = DB::Connection($db_name);
                    $current_date =  date('Y-m-d');

                    // Update the Insurance types
                     $list = $mysqldbconn->select("SELECT `degree_name`,`id` FROM `provider_degrees` WHERE `id` = '" . $degrees->id . "' ");
                    if(empty($list)){
                        $catList = $mysqldbconn->insert("insert into `provider_degrees` (`degree_name`, `created_at`, `updated_at`, `created_by`, `updated_by`) values ('" . $request['degree_name'] . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . Auth::user ()->id . "', '" . Auth::user ()->id . "')");
	                       	if($catList){
	                       		 \Log::info("Taxanomy Inserted");	echo "Taxanomy Inserted In ".$db_name."<br>";
	                       	} else{
	                       		 \Log::info("Taxanomy NOT Inserted");	echo "Taxanomy Inserted NOT In ".$db_name."<br>";
	                       	}
                       	}            					

                    } else {
                        \Log::info("No practice found");	echo "No practice found<br>";
                    } 
				}
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$insertedId));
			} else {
				return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>$insertedId));
			}
			### Thilagavathy End 28 nov 2019
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$insertedId));					
		}
	}
	/************************** End Degree store process*********************************************/
	
	/********************** Start degree  details show page *******************************************/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$degrees = Provider_degree::with('user','userupdate')->where('id', $id )->first();
		if(Provider_degree::where('id', $id )->count())
		{
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('degrees')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>compact('degrees')));
		}   
	}
	/********************** End degree  details show page *******************************************/
	
	/********************** Start Degree Edit page Display*******************************************/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Provider_degree::where('id', $id )->count())
		{
			$degrees = Provider_degree::findOrFail($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('degrees')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>compact('degrees')));
		}   
	}
	/********************** End Degree Edit page Display*******************************************/
	
	/********************** Start Degree updated process *******************************************/
	public function getUpdateApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '')
		{
			$request = Request::all();
		}
		$validator = Validator::make(
			Input::all(),
			[
			'degree_name' => 'required|unique:provider_degrees,degree_name,'.$id
			], 
			
			[ 
			'degree_name.required' => Lang::get("admin/providerdegree.validation.providerdegree"),
			'degree_name.unique' => Lang::get("admin/providerdegree.validation.providerdegree_unique")
			]
		);

		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{		
			$degrees = Provider_degree::findOrFail($id);
			$degrees->update($request);
			$user = Auth::user ()->id;
			$degrees->updated_by = $user;
			$degrees->save ();
        	return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));
		}
	}
	/********************** End Degree updated process *******************************************/
	
	/********************** Start Degree deleted process *******************************************/
	public function getDestroyApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		Provider_degree::find($id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}
	/********************** End Degree deleted process *******************************************/
	
	function __destruct() 
	{
    }
	
}
