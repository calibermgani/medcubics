<?php namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\PagePermissions as PagePermissions;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Practice as Practices;
use Auth;
use Session;
use Response;
use Illuminate\Http\Request;

class UserlistApiController extends Controller {

	public function getIndexApi()
	{
		//$customer_id 		= Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		//$customer_user_id 	= Helpers::getEncodeAndDecodeOfId(Auth::user()->id,'decode');
		$practice_id = Session::get('practice_dbid');
		$user_practices		= Setpracticeforusers::on('responsive')->with('user')->where('practice_id', $practice_id)->get();
		$role_id ="";
		foreach ($user_practices as $key => $user_practice){
			$role_id = explode(",",$user_practice->page_permission_ids);
			$remove_empty = array_filter($role_id);
			//dd(count($remove_empty));
			$pages_role_id = PagePermissions::whereIn('id',$remove_empty) ->selectRaw('CONCAT(menu," >> ",submenu," >> ",title) as concatname,id')->pluck('concatname','id')->all();
			$user_practices[$key]->page_permission_ids = $pages_role_id;
		}
		$pages_role_id = PagePermissions::whereIn('id',$role_id)->select('submenu','title')->get();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('user_practices')));
	//	return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('user_list')));
	}

	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');

		$user_practices		= Setpracticeforusers::on('responsive')->with('user')->where('id', $id)->get();
		$role_id ="";
		foreach ($user_practices as $key => $user_practice){
			$role_id = explode(",",$user_practice->page_permission_ids);
			$remove_empty = array_filter($role_id);
			//dd(count($remove_empty));
			$pages_role_id = PagePermissions::whereIn('id',$remove_empty) ->selectRaw('CONCAT(menu," >> ",submenu," >> ",title) as concatname,id')->pluck('concatname','id')->all();
			$user_practices[$key]->page_permission_ids = $pages_role_id;
		}
		$pages_role_id = PagePermissions::whereIn('id',$role_id)->select('submenu','title')->get();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('user_practices')));
	}

}