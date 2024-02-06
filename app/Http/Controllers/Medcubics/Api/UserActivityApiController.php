<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Useractivity as UserActivity;
use App\Models\Medcubics\Practice as Practice;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Redirect;
use Hash;
use Lang;

class UserActivityApiController extends Controller 
{
	
	function convert_code($n)
	{
		return Helpers::getEncodeAndDecodeOfId($n,'encode');
	}
	/*** Start to Listing the User Activity ***/
	public function getIndexApi($export = "")
	{
		$user = Users::orderBy('name','ASC')->pluck('id','name')->all();
		$module = UserActivity::orderBy('module','ASC')->groupBy('module')->pluck('module','module')->all();
		$practice = Practice::orderBy('practice_name','ASC')->pluck('id','practice_name')->all();
		$user = array_map(array($this,'convert_code'),$user);
		$practice = array_map(array($this,'convert_code'),$practice);
		$user = array_flip($user);
		$practice = array_flip($practice);
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('user','practice','module')));
	}
	/*** End to Listing the User Activity ***/
	
	/*** Start to Search the User Activity ***/	
    public function getUserRecordApi($data='')
	{	
			// ini_set('memory_limit',-1);

			$useractivity = UserActivity::with('user', 'practice');

			if(isset($data['practice_id']) && !empty($data['practice_id'])){
				$get_practiceid = Helpers::getEncodeAndDecodeOfId($data['practice_id'],'decode');
				$useractivity->where('main_directory',$get_practiceid);
			}
			if(isset($data['user_id']) && !empty($data['user_id'])){
				$get_userid = Helpers::getEncodeAndDecodeOfId($data['user_id'],'decode');
				$useractivity->where('userid',$get_userid);
			} 
			if(isset($data['module']) && !empty($data['module'])){
				$useractivity->where('module',$data['module']);
			}
			if(isset($data['user']) && !empty($data['user'])){
				$userid = Helpers::getEncodeAndDecodeOfId($data['user'],'decode');
				$useractivity->where('userid', $userid);
			}
			if(isset($data['practice']) && !empty($data['practice'])){
				$practice = Helpers::getEncodeAndDecodeOfId($data['practice'],'decode');
				$useractivity->whereHas('practice' , function($query) use($practice){ 
			 				$query->where('id', $practice);
			 			});
			}
			if(isset($data['transaction_date']) && !empty($data['transaction_date'])){
				$test = explode('-', $data['transaction_date']);
				$startDate = date('Y/m/d', strtotime($test[0]));
				$endDate = date('Y/m/d', strtotime($test[1]));
				$useractivity->whereBetween('activity_date', [$startDate, $endDate]);
			}
			
			if(isset($data['date_range']) && !empty($data['date_range'])){
				$date = explode('-', $data['date_range']);
				$start_date = $date[0];
				$end_date = $date[1];
				$s_date = date('Y-m-d',strtotime($start_date));
				$e_date = date('Y-m-d',strtotime($end_date));
				$useractivity->whereDate('activity_date','>=',$s_date)->whereDate('activity_date','<=',$e_date);
			}

			$get_userid = !empty($get_userid)?$get_userid:'';
			$useractivity = $useractivity->get();

		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('get_userid','useractivity')));
	}
	
	public function get_activitylist()
	{
		$request 	= Request::all();	
		$start 		= $request['start'];
		$len 		= $request['length'];
        $cloum 		= intval($request["order"][0]["column"]);
		$order 		= $request['columns'][$cloum]['data'];
		$activity_arr_details = [];
		
		$order_decs = $request["order"][0]["dir"];
		$search = '';
        if(!empty($request['search']['value']))
		{
			$search	= $request['search']['value'];
		}
        $getuseractivity = UserActivity::where(function($query) use($search){ 
				        		return $query->where('main_directory', 'LIKE', '%'.$search.'%')
								->orWhere('module', 'LIKE', '%'.$search.'%')
								->orWhere('user_activity_msg', 'LIKE', '%'.$search.'%');
							})
			        		->orderBy($order,$order_decs)
			        		->skip($start)->take($len)->get()->toArray();

		$total_rec_count = UserActivity::where(function($query) use($search){ return $query->where('main_directory', 'LIKE', '%'.$search.'%')
				->orWhere('module', 'LIKE', '%'.$search.'%')
				->orWhere('user_activity_msg', 'LIKE', '%'.$search.'%');})->count();

		foreach($getuseractivity as $useractivity)
		{
			$practice = Practice::pluck( 'practice_name', 'id' )->all();
			$userlocation = '';	
			$activitytype = '';	
			if($useractivity['main_directory']!='')
			{	
				if($useractivity['main_directory']!='admin')
				{
					$userlocation = (isset($practice[$useractivity['main_directory']]))?$practice[$useractivity['main_directory']]:'';
					$activitytype = Helpers::getEncodeAndDecodeOfId($useractivity['main_directory'],'encode');				
				}
				else
				{
					$userlocation = 'admin';  
					$activitytype = 'admin';
				}
			}
			
			$activity_details = $useractivity;
			$activity_details['activitytype'] = $activitytype;
			$activity_details['main_directory'] = $userlocation;
			$activity_details["activity_date"] 	= $useractivity["activity_date"];
			$activity_arr_details[] = $activity_details;
		} 

		$data['data'] = $activity_arr_details;		
		$data 				= array_merge($data,$request);
		$data['recordsTotal'] 		= $total_rec_count;
		$data['recordsFiltered'] 	= $total_rec_count;		
		return Response::json($data);
	}
	
	/*** End to Search the User Activity ***/
}
