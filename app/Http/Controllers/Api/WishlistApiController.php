<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use View;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Wishlist as Wishlist;
use Requests;
use DB;
use Lang;

class WishlistApiController extends Controller {

	/*** Start to Export the API List ***/
	public function getIndexApi($export='')
	{
		
	}
	/*** End to Export the API List	 ***/
	
	/*** Start to Create the API List ***/
	public function getCreateApi()
	{
		
	}
	/*** End to Create the API List	 ***/

	/*** Start to Store the API List ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		$data = Request::all();
		$c = explode(",",$data['sub_module']);
		$c = array_map('trim',$c);
		$c = array_filter($c);

		$mode_id = Helpers::getEncodeAndDecodeOfId($data['mode_id'],"decode");
		if(is_numeric($mode_id))
			$mode_id = $mode_id;
		else
			$mode_id = "";

		$value = Helpers::getClaimId($c, $mode_id);
		$data['mode'] = array_shift($c);
		$data['sub_module'] = implode(",",$c);
		$data['module'] = $value['icon'];
		$data['mode_id'] = $value['id'];
		
		$Wishlist = Wishlist::create($data);
		$user = Auth::user()->id;
		$Wishlist->created_by = $user;
		$Wishlist->save();
		return Response::json(array('status'=>'success', 'message'=>"success",'data'=>$Wishlist->id));
	}
	/*** End to Store the API List	 ***/
	
	/*** Start to Show the API List ***/
	public function getShowApi($id)
	{}
	/*** End to Show the API List	 ***/
	
	/*** Start to Edit the API List ***/
	public function getEditApi($id)
	{}
	/*** End to Edit the API List	 ***/

	/*** Start to Update the API List ***/
	public function getUpdateApi($type, $id, $request='')
	{}
	/*** End to Update the API List	 ***/

	/*** Start to Delete the API List ***/
	public function getDeleteApi($request='')
	{
		$request = Request::all();
		$user = Auth::user ()->id;
		Wishlist::Where('url',$request['url'])->Where('created_by',$user)->delete();		
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
	}
	/*** End to Delete the API List	 ***/
	
	public function removeAPIFromSite($id)
	{}
	
	function __destruct() 
	{
    }
}