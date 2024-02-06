<?php namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Request;
use Input;
use Redirect;
use Auth;
use View;
use Html;
use App\User;
use App\Http\Helpers\Helpers as Helpers;
use Config;
use Session;

class BlogController extends Api\BlogApiController 
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

   	public function __construct() 
   	{ 
		View::share ( 'heading', 'Blogs' );    
		View::share ( 'selected_tab', 'blogs' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.blogs'));
	}  
	
	public function index($order='',$keyword='')
	{
		if(Session::get('practice_dbid')=='') {
			return Redirect::to('profile');
		}
		$api_response = $this->getIndexApi($order,$keyword);
		$api_response_data = $api_response->getData();
		$check_page = 2;
		$blogs1 = $api_response_data->data->blogs;
		$login_user = $api_response_data->data->login_user;
		$total_record     = count($blogs1);
		$total_page_count = ceil(count($blogs1)/$this->blog_itemperpage);
		$blogs = $blogs1;
		$favblogarray = $api_response_data->data->favblogarray;
		$favcountarray = $api_response_data->data->favcountarray;
		$commentcountarray = $api_response_data->data->commentcountarray;
		$blogpartcountarray = $api_response_data->data->blogpartcountarray;
		$blogvotearray = $api_response_data->data->blogvotearray;
		$today_notes = $api_response_data->data->today_notes;
                $messages = $api_response_data->data->messages;
                $total_messages = count($messages);
		$show_loadmore = 1;
		$users=$api_response_data->data->users_table;
		if($order == '' or $keyword='')
			return view('profile/blog/blog',  compact('blogs','blogvotearray','favblogarray','total_record','total_page_count','favcountarray','commentcountarray','blogpartcountarray','users','login_user','today_notes','total_messages'));
		else
			return view('profile/blog/paginagionlisting',  compact('show_loadmore','total_page_count','total_record','check_page','blogs','commentcountarray','favblogarray','blogpartcountarray','favcountarray','blogvotearray','users','login_user','today_notes','total_messages'));	
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
		$group_list        = $api_response_data->data->grouplist;
		$get_user        = $api_response_data->data->get_user;
		$privacy_id        = ''; 
		$groupid            = '';
		$user_id            = '';
		return view('profile/blog/create',  compact('blog','group_list','get_user','privacy_id','groupid','user_id'));//
	}
        
    public function bloglisting($order='',$keyword='') 
	{  
		if(Session::get('practice_dbid')=='') {
			return Redirect::to('profile');
		}
		View::share ( 'selected_tab', 'blog_listing' ); 
		$check_page  = '1';
        $api_response      = $this->blogListingApi($order,$keyword);
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
			return view('profile/blog/listing',  compact('total_page_count','blogvotearray','favblogarray','favarray','total_record','blogs','favcountarray','commentcountarray','blogpartcountarray','users','login_user','today_notes','total_messages'));
		else
			return view('profile/blog/paginagionlisting',  compact('show_loadmore','total_page_count','total_record','check_page','blogs','commentcountarray','favblogarray','blogpartcountarray','favcountarray','blogvotearray','users','login_user','today_notes','total_messages'));
    }
                
	public function getblog() 
	{
		$getorder 	  = $_GET['getorder'];
		$getkeyword   = $_GET['getkeyword'];
		$current_page = $_GET['page'];
		$check_page   = $_GET['individual'];
		
		if($check_page == 1)
			$api_response      = $this->blogListingApi($getorder,$getkeyword);
		
		if($check_page == 2)
			$api_response      = $this->getIndexApi($getorder,$getkeyword);
		
		$api_response_data = $api_response->getData();
		$blogs1 = $api_response_data->data->blogs;
		$total_record     = '1';
		$total_page_count = '1';
		$start = $current_page * $this->blog_itemperpage;
		$blogs = array_slice($blogs1, $start, $this->blog_itemperpage, true);
		$favblogarray  = $api_response_data->data->favblogarray;
		$favcountarray = $api_response_data->data->favcountarray;
		$commentcountarray = $api_response_data->data->commentcountarray;
		$blogpartcountarray = $api_response_data->data->blogpartcountarray;
		$blogvotearray      = $api_response_data->data->blogvotearray;
		$show_loadmore = 0;
		
		return view('profile/blog/paginagionlisting',  compact('show_loadmore','total_page_count','total_record','check_page','blogs','commentcountarray','favblogarray','blogpartcountarray','favcountarray','blogvotearray'));
	}
		
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
        $api_response = $this->getStoreApi($request::all());
        $api_response_data = $api_response->getData();
		if($api_response_data->status == 'success') {
			return Redirect::to('profile/userblog')->with('success', $api_response_data->message);
		} else {
			return Redirect::to('profile/blog/create')->withInput()->withErrors($api_response_data->message);
		}  
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
        
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
            return view('profile/blog/show', compact('total_record', 'total_page_count', 'blog', 'blog_url', 'blog_favourite', 'blog_favcount', 'blog_commentcount', 'blogpart', 'blog_comments', 'users', 'login_user', 'today_notes','total_messages'));
        } else {
            return Redirect::to('profile/userblog')->with('message', $api_response_data->message);
        }
    }

    /**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
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
			return view('profile/blog/edit',  compact('blog','group_list','get_user','privacy_id','groupid','user_id'));
		}
		else 
		{
			return Redirect::to('profile/userblog')->with('message', $api_response_data->message);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$api_response = $this->getUpdateApi($id,Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$id = Helpers::getEncodeAndDecodeOfId($id,'encode');
			return Redirect::to('profile/blog/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			$id = Helpers::getEncodeAndDecodeOfId($id,'encode');
			return Redirect::to('profile/blog/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}  
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$blog_id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$api_response = $this->getDestroyApi($blog_id);
		$api_response_data = $api_response->getData();
		return Redirect::to('profile/userblog')->with('success',$api_response_data->message);
	}
        
	public function favourite()
	{
		echo $api_response = $this->getFavouriteApi($_GET);
	}
	
	// store comments
	public function comments(Request $request)
	{
	   $api_response = $this->CommentApi(Request::all());
	   $api_response_data = $api_response->getData();
	   $addnewcommend = $api_response_data->data->get_newcomment;
	   return view('profile/blog/show_comment', compact('addnewcommend'));
	}
	
	// store reply comments
	public function replycomments(Request $request)
	{
	   $api_response = $this->CommentReplyApi(Request::all());
	   $api_response_data = $api_response->getData();
	   $addnewrpycommend = $api_response_data->data->get_newcomment;
	   return view('profile/blog/show_reply_comment', compact('addnewrpycommend'));
	}
	
	public function getcomments()
	{
		$current_page = $_GET['page'];
		$blogownid	  =	$_GET['blogownid'];
		$api_response      = $this->getCommentApi($_GET);
		$api_response_data = $api_response->getData();
		$comment = $api_response_data->data->get_comment;
		$start = $current_page * $this->comments_itemperpage;
		$blog_comments = array_slice($comment, $start, $this->comments_itemperpage, true);
		return view('profile/blog/paginagioncommentlisting',  compact('blog_comments','blogownid'));
	}
	
	public function commentsfavourite()
	{
		echo $api_response = $this->getCommentFavouriteApi($_GET);
	}
	
	public function deletecomments($id,$blogid) 
	{
		echo $this->deleteCommentsApi($id,$blogid);
	}
	
	public function deletereplycomments($replyid,$parentid)
	{
		echo $this->deleteReplyCommentsApi($replyid,$parentid);
	}

	/*** Start to get cover photo details ***/	
	public static function getCoverPhoto()
	{
		$api_response = self::getCoverPhotoApi();
		$api_response_data = $api_response->getData();
		$status = $api_response_data->status;
		$get_photo = $api_response_data->data->get_photo;
		//$rolelist = $api_response_data->data->rolelist;
		return compact('status','get_photo');
	}
	/*** End to get cover photo details ***/	
	
	/*** Start to store cover photo details ***/	
	Public function addCoverPhoto(Request $request)
	{
		$api_response = $this->addCoverPhotoApi($request::all());
		return '1';
	}
	/*** End to store cover photo details ***/	
	
	public static function getFavourite()
	{
		return self::getRecentFavouriteApi();
	}
	
	public static function teamMemberController()
	{
		$api_response = self::teamMemberApiConmtroller();
		$api_response_data = $api_response->getData();
		$user =$api_response_data->data->user;
		return $user;
	}

	/*** Start to recent comments ***/
	public static function getRecentComments($blogid)
	{
		return self::getRecentCommentsApi($blogid);
	}
	/*** End to recent comments ***/
	
	/*** Start to most viewed  ***/
	public static function getMostViewed()
	{
		return self::getMostViewedApi();
	}
	/*** End to most viewed ***/
	
	/*** Start to get reply comments ***/
	public static function getReplyComments($comment_id)
	{
		$api_response = self::getReplyCommentsApi($comment_id);
		$api_response_data = $api_response->getData();		
		$replycomment1 = $api_response_data->data->replycomment;		
		$reply_total_record     = count($replycomment1);
		$reply_total_page_count = ceil(count($replycomment1)/self::$comments_count);
		$replycomment = array_slice($replycomment1, 0, self::$comments_count, true);
		return compact('reply_total_record','reply_total_page_count','replycomment');
	}
	/*** End to get reply comments ***/
	
	/*** Start to get reply comments for show more ***/
	public function getMoreReplyComments()
	{
		$current_page 		= $_GET['page'];
		$parentcommentid  	= $_GET['parentcommentid'];
		$blogownid  		= $_GET['blogownid'];
		
		$api_response      = self::getReplyCommentsApi($parentcommentid);
		$api_response_data = $api_response->getData();
		$replycomment1 = $api_response_data->data->replycomment;
		$start = $current_page * $this->comments_itemperpage;
		$blog_replycomments = array_slice($replycomment1, $start, $this->comments_itemperpage, true);
		return view('profile/blog/paginagionreplycommentlisting',  compact('blog_replycomments','blogownid'));
	}
	/*** End to get reply comments for show more ***/
	
	public function removeCoverPhoto()
	{
		$api_response = $this->removeCoverPhotoApi();
		return '/img/blog_cover.jpg';	
	}
	
	public function filteruser($keyword=''){
		$api_response = $this->getUsersFilterApi($keyword);
		$api_response_data = $api_response->getData();
		$users = $api_response_data->data->user_list;
		return view('profile/blog/Filteruser',compact('users'));
	}
	
}