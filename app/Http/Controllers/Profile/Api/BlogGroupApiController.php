<?php namespace App\Http\Controllers\Profile\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile\Blog as Blog;
use App\Models\Profile\BlogFavourite as BlogFavourite;
use App\Models\Profile\BlogComments as BlogComments;
use App\Models\Profile\BlogCommentFavourite as BlogCommentFavourite;
use App\Models\Profile\BlogVote as BlogVote;
use App\Models\Profile\BlogCommentsVote as BlogCommentsVote;
use App\Models\Profile\BlogGroup as BlogGroup;
use App\User as User;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use File;
use DB;
use Config; 
class BlogGroupApiController extends Controller {
	 ///Group Blog ///
	public function groupblog()
	{
		$get_usr[]='';
		$grouplist_id = BlogGroup::pluck('group_users')->all();
		$grouplist = BlogGroup::with('user')->orderBy('group_name','asc')->get();
		for($i=0;$i<count($grouplist_id);$i++) {
			$id= explode(",",$grouplist_id[$i]);
			$get_user= User::orderBy('name','asc')->whereIn('id',$id)->pluck('name')->first();
			$get_usr[$i]=$get_user;
		}
		$get_usr= json_decode(json_encode($get_usr), true);
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('get_usr','grouplist','get_user')));
	}
	 
	 ///Create Group API  ///
	public function CreateGroupApi(){
		$get_user  = User::orderBy('name','asc')->pluck('name', 'id')->all();
        return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('get_user')));
		
	}
	 ///Store Group API  ///
	public function storeGroupApi($request=''){
		if($request == '')
			$request = Request::all();
		$validator = Validator::make($request, BlogGroup::$rules, BlogGroup::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$user = Auth::user ()->id;
			$request['group_users'] = implode(',',$request['group_users']);
			$blogGroup = BlogGroup::create($request);
			$blogGroup->save ();
			return Response::json(array('status'=>'success', 'message'=>'Blog added successfully','data'=>''));					
		}
	}
	 ///View Group API ///
	public function ViewGroupApi($id){
		$grouplist = BlogGroup::with('user')->where('id',$id)->orderBy('group_name','asc')->first();
		$user = Auth::user ()->id;
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('user','grouplist')));
	}
	
	 ///Edit Group API ///
	public function editGroupApi($id){
	
		$grouplist = BlogGroup::with('user')->where('id',$id)->orderBy('group_name','asc')->first();
		$get_user  = User::orderBy('name','asc')->pluck('name', 'id')->all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('grouplist','get_user')));
	}
	
	
	 ///StoregroupApi ///
	public function updateGroupApi($id,$request='')
	{
		if($request == '')
			$request = Request::all();
		$validator = Validator::make($request, BlogGroup::$rules, BlogGroup::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));
		}
		else
		{	
			$user = Auth::user ()->id;
			$request['group_users'] = implode(',',$request['group_users']);
			$blogGroup=BlogGroup::find($id);
			$blogGroup->update($request);
			return Response::json(array('status'=>'success', 'message'=>'Blog update successfully','data'=>''));					
		}
	}
	
	public function deleteApiGroup($id){
		$delete_group = BlogGroup::find($id)->delete();
		return Response::json(array('status'=>'success', 'message'=>'Blog deleted successfully','data'=>''));	
	}

}