<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use View;
use Request;
use Response;
use Redirect;
use Config;
use App\Http\Controllers\Controller;
use App\User;
use Html;
use App\Http\Helpers\Helpers as Helpers;

class UpdatesController extends Api\UpdatesApiController 
{
	public function __construct() { 
		View::share ( 'heading', 'Tickets' );  
		View::share ( 'selected_tab', 'admin/updates' );
		View::share( 'heading_icon', "fa-question");		
	} 
	
	/**** LOG List page start ***/
	public function index($order='',$keyword='')
	{		
		$check_page  = '1';
        $api_response      = $this->getIndexApi($order,$keyword);
		$api_response_data = $api_response->getData();
		$blogs1 = $api_response_data->data->getblogs_count;
		$login_user = $api_response_data->data->login_user;
		$total_record     = count($blogs1);
		$total_page_count = ceil(count($blogs1)/$this->blog_itemperpage);
		$blogs = $api_response_data->data->blogs;
		
		$favblogarray = $api_response_data->data->favblogarray;
		$favcountarray = $api_response_data->data->favcountarray;
		$commentcountarray = $api_response_data->data->commentcountarray;
		$blogpartcountarray = $api_response_data->data->blogpartcountarray;
		$blogvotearray = $api_response_data->data->blogvotearray;
		$today_notes = $api_response_data->data->today_notes;
		$show_loadmore = 1;
		$users=$api_response_data->data->users_table;
                $messages = $api_response_data->data->messages;
                $total_messages = count($messages);
		if($order == '' or $keyword='')
			return view('admin/updates/updateslisting',  compact('total_page_count','blogvotearray','favblogarray','favarray','total_record','blogs','favcountarray','commentcountarray','blogpartcountarray','users','login_user','today_notes','total_messages'));
		else
			return view('admin/updates/paginagionlisting',  compact('show_loadmore','total_page_count','total_record','check_page','blogs','commentcountarray','favblogarray','blogpartcountarray','favcountarray','blogvotearray','users','login_user','today_notes','total_messages'));
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$blog              = $api_response_data->data->blog;		
		$get_user        = $api_response_data->data->get_user;
		$privacy_id        = ''; 
		$groupid            = '';
		$user_id            = '';
		return view('admin/updates/create',  compact('blog','get_user','privacy_id','groupid','user_id'));//
	}
	public function store(Request $request)
	{
        $api_response = $this->getStoreApi($request::all());
        $api_response_data = $api_response->getData();
		if($api_response_data->status == 'success') {
			return Redirect::to('admin/updates')->with('success', $api_response_data->message);
		} else {
			return Redirect::to('admin/updates/create')->withInput()->withErrors($api_response_data->message);
		}  
	}
	public function edit($id)
	{
		$blog_id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$api_response = $this->getEditApi($blog_id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status=='success')
		{
			$blog              = $api_response_data->data->blog;
			$group_list        = $api_response_data->data->grouplist;
			$get_user        = $api_response_data->data->get_user;
			$privacy_id        = $api_response_data->data->privacy_id;
			$groupid        = $api_response_data->data->groupid;
			$user_id        = $api_response_data->data->user_id;
			return view('admin/updates/edit',  compact('blog','group_list','get_user','privacy_id','groupid','user_id'));
		}
		else 
		{
			return Redirect::to('admin/updates')->with('message', $api_response_data->message);
		}
	}
	public function show($id)
	{
		$blog_id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$api_response      = $this->getShowApi($blog_id);
        $api_response_data = $api_response->getData();
                
        if ($api_response_data->status == 'success') {
            $blog = $api_response_data->data->blog;
            $blog_favourite = $api_response_data->data->blog_favourite;
            $blog_favcount = $api_response_data->data->blog_favcount;
            $blog_commentcount = $api_response_data->data->blog_commentcount;
            $blogpart = $api_response_data->data->blogpart;
            $blog_url = $api_response_data->data->blog_url;
            $blog_comments1 = $api_response_data->data->blog_comments;
            $users = $api_response_data->data->users_table;
            $login_user = $api_response_data->data->login_user;
            $today_notes = $api_response_data->data->today_notes;
            $total_record = count($blog_comments1);
            $messages = $api_response_data->data->messages;
            $total_messages = count($messages);
            $total_page_count = ceil(count($blog_comments1) / $this->comments_itemperpage);
            $blog_comments = array_slice($blog_comments1, 0, $this->comments_itemperpage, true);
            return view('admin/updates/show', compact('total_record', 'total_page_count', 'blog', 'blog_url', 'blog_favourite', 'blog_favcount', 'blog_commentcount', 'blogpart', 'blog_comments', 'users', 'login_user', 'today_notes','total_messages'));
        } else {
            return Redirect::to('admin/updates')->with('message', $api_response_data->message);
        }
    }
    public function update($id)
	{
		$api_response = $this->getUpdateApi($id,Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$id = Helpers::getEncodeAndDecodeOfId($id,'encode');
			return Redirect::to('admin/updates/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			$id = Helpers::getEncodeAndDecodeOfId($id,'encode');
			return Redirect::to('admin/updates/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}  
	}
	public function view_log($file_name){ 
		$api_response = $this->getViewLogApi($file_name);
		$api_response_data = $api_response->getData();
        $file_content =  $api_response_data->data->file_content;
		return view('admin/log/show',  compact('file_content'));
	}
	public function newfeautureModel()
	{	
		$order = $keyword = "";
		$check_page  = '1';
        $api_response      = $this->getnewfeautureModelApi($order,$keyword);
		$api_response_data = $api_response->getData();
		$blogs1 = $api_response_data->data->getblogs_count;
		$login_user = $api_response_data->data->login_user;
		$total_record     = count($blogs1);
		$total_page_count = ceil(count($blogs1)/$this->blog_itemperpage);
		$blogs = $api_response_data->data->blogs;
		
		$favblogarray = $api_response_data->data->favblogarray;
		$favcountarray = $api_response_data->data->favcountarray;
		$commentcountarray = $api_response_data->data->commentcountarray;
		$blogpartcountarray = $api_response_data->data->blogpartcountarray;
		$blogvotearray = $api_response_data->data->blogvotearray;
		$today_notes = $api_response_data->data->today_notes;
		$show_loadmore = 1;
		$users=$api_response_data->data->users_table;
                $messages = $api_response_data->data->messages;
                $total_messages = count($messages);
		if($order == '' or $keyword='')
			$html =  view('admin/updates/updatesmodel')->with(compact('total_page_count','blogvotearray','favblogarray','favarray','total_record','blogs','favcountarray','commentcountarray','blogpartcountarray','users','login_user','today_notes','total_messages'))->render();
			return Response::json(array('status' => 'success', 'message' => null, 'data' => $html));
	}
	
}