<?php namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Request;
use Input;
use Redirect;
use Auth;
use View;
use Html;
use App\Http\Helpers\Helpers as Helpers;
use Config;

class BlogGroupController extends Api\BlogGroupApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

   	public function __construct() { 
		View::share ( 'heading', 'Blogs' );    
		View::share ( 'selected_tab', 'blogs' ); 
		View::share( 'heading_icon', 'user');
    }  
	
	 ///List Group function ///
    public function index(){
		$api_response = $this->groupblog();
		$api_response_data = $api_response->getData();
		$group_list        = $api_response_data->data->grouplist;
		$get_user        = $api_response_data->data->get_usr;
		return view('profile/blog/group/grouplist',compact('blog','get_user','group_list'));
	}
	
	 ///Create Group function ///
    public function create(){
		$api_response = $this->CreateGroupApi();
		$api_response_data = $api_response->getData();
		$group_user        = $api_response_data->data->get_user;
		return view('profile/blog/group/creategroup',compact('user','group_user'));	
	}

	 ///store group function ///
	public function store(Request $request) {			
		$api_response = $this->storeGroupApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')	{
			return Redirect::to('profile/bloggroup')->with('success', $api_response_data->message);
		} else {
			return Redirect::to('profile/bloggroup/create')->withInput()->withErrors($api_response_data->message);
		} 
	}
	 ///View Group function ///
	public function show($id){		
		$api_response = $this->ViewGroupApi($id);
		$api_response_data = $api_response->getData();
		$group_list        = $api_response_data->data->grouplist;
		$user        = $api_response_data->data->user;
		return view('profile/blog/group/viewgroup',compact('group_list','id','user'));
	}
	
	 ///Edit Group function ///
	public function edit($id){		
		$api_response = $this->editGroupApi($id);
		$api_response_data = $api_response->getData();
		$group_list        = $api_response_data->data->grouplist;
		$group_user        = $api_response_data->data->get_user;
		
		return view('profile/blog/group/editgroup',compact('group_list','group_user','id'));
	}
	
	 ///Update Group function  ///
	public function update($id){	
		$api_response = $this->updateGroupApi($id,Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')	{
			return Redirect::to('profile/bloggroup/'.$id)->with('success', $api_response_data->message);
		} else {
			return Redirect::to('/profile/bloggroup/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}  
	}
	 ///Delete Group Function ///
	public function destroy($id){
		$api_response = $this->deleteApiGroup($id);
		$api_response_data = $api_response->getData();
		return redirect('profile/bloggroup')->with('success',$api_response_data->message);		
	}

}