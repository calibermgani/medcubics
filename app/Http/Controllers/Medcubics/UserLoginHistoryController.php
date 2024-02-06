<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use Response;
use Request;
use Redirect;
use Auth;
use View;
use Config;
use DB;

class UserLoginHistoryController extends Api\UserLoginHistoryApiController
{
	public function __construct() { 
      
       View::share ( 'heading', 'Customers' );  
	   View::share ( 'selected_tab', 'userLoginHistory' ); 
       View::share( 'heading_icon', Config::get('cssconfigs.admin.users'));

    }  
	/*** Reason for for visit Lising ***/
	public function index($type)
	{	
		if( $type == "settings"){
			$api_response = $this->getIndexApi($type = "");
			$api_response_data = $api_response->getData();
			$searchUserData = $api_response_data->data->searchUserData;
			$search_fields = $api_response_data->data->search_details;
			if (Request::ajax()) {
				$type = last(Request::segments());
				$data = $this->getSecurityCodeSetting($type);
				return Response::json($data);
			}	
			return view('admin/userloginhistory/userSettings',compact('searchUserData','search_fields'));			
		}else{
			
			$api_response = $this->getIndexApi($type = "");
			$api_response_data = $api_response->getData();
			$searchUserData = $api_response_data->data->searchUserData;
			$search_fields = $api_response_data->data->search_details;
			if (Request::ajax()) {
				$type = last(Request::segments());
				$data = $this->getloginHistory($type);
				return Response::json($data);
			}
			return view('admin/userloginhistory/userloginhistory',compact('searchUserData','search_fields'));
		}
	}
	public function getloginHistory($type)
	{	
		$api_response = $this->getHistoryApi($type);
		$api_response_data = $api_response->getData();
		$userLoginInfo = (!empty($api_response_data->data->userLoginInfo))? (array)$api_response_data->data->userLoginInfo:[];
        $view_html = Response::view('admin/userloginhistory/loginhistory_ajax', compact('userLoginInfo'));
        $content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
		$request = Request::all();
        if (!empty($request['draw']))
           $data['draw'] = $request['draw'];
	    $data['data'] = $content;
	    $data['recordsTotal'] = $api_response_data->data->count;
	    $data['recordsFiltered'] = $api_response_data->data->count;
        return $data;
	}
	
	public function userStatusChange(){
		$api_response = $this->userStatusChangeApi();
		$api_response_data = $api_response->getData();
		return $userLoginInfo = $api_response_data->status;
	}
	public function userIpSecurityCodeRest(){
		$api_response = $this->userIpSecurityCodeRestApi();
		$api_response_data = $api_response->getData();
		return $userLoginInfo = $api_response_data->status;
	}

	public function userSettingFillter(){
		$request  = Request::all();

		if(isset($request['sel_Cust_id'])){
			$Customer_id = json_decode($request['sel_Cust_id']);
			if($Customer_id == [] || $Customer_id == [0]){
				$prac = DB::table('practices')->where('status', 'Active')->pluck('practice_name', 'id')->all();
				}else{
				$pass_id = $Customer_id;
				$prac = DB::table('practices')->where('status', 'Active')->whereIn('customer_id',$pass_id)->pluck('practice_name', 'id')->all();
				}
			return $prac;
		}elseif (isset($request['sel_prac_id'])) {
			$Practice_id = json_decode($request['sel_prac_id']);
			if($Practice_id == [] || $Practice_id == [0]){
				$user_list = DB::table('users')->where('status', 'Active')->pluck('short_name', 'id')->all();
			}else{
				$user = $Practice_id;
				$user_list = DB::table('users')->where('status', 'Active')->whereIn('admin_practice_id',$user)->pluck('short_name', 'id')->all();
			}
			return $user_list;
		}else{
			return $data = "Something went wrong!";
		}

		
	}

	public function getSecurityCodeSetting(){
		$api_response = $this->getSecurityCodeSettingApi();
		$api_response_data = $api_response->getData();
		$usersettingInfo = (!empty($api_response_data->data->usersettingInfo))? (array)$api_response_data->data->usersettingInfo:[];
		$view_html = Response::view('admin/userloginhistory/userSettings_ajax', compact('usersettingInfo'));
		$content_html = htmlspecialchars_decode($view_html->getContent());
		$content = array_filter(explode("</tr>", trim($content_html)));
		$request = Request::all();
        if (!empty($request['draw']))
           $data['draw'] = $request['draw'];
	    $data['data'] = $content;
	    $data['recordsTotal'] = $api_response_data->data->count;
	    $data['recordsFiltered'] = $api_response_data->data->count;
        return $data;
	}

	public function givApproval(){
		$api_response = $this->givApprovalApi();
		$api_response_data = $api_response->getData();
		return $userRemoveApproval = $api_response_data->status;
	}

	public function removeApproval(){
		$api_response = $this->removeApprovalApi();
		$api_response_data = $api_response->getData();
		return $userRemoveApproval = $api_response_data->status;
	}
	
}
