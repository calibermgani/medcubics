<?php namespace App\Http\Controllers\Profile\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile\Blog as Blog;
use App\Models\Profile\BlogFavourite as BlogFavourite;
use App\Models\Profile\BlogComments as BlogComments;
use App\Models\Profile\BlogReplyComments as BlogReplyComments;
use App\Models\Profile\BlogCommentFavourite as BlogCommentFavourite;
use App\Models\Profile\BlogVote as BlogVote;
use App\Models\Profile\BlogCommentsVote as BlogCommentsVote;
use App\Models\Profile\BlogReplyCommentsVote as BlogReplyCommentsVote;
use App\Models\Profile\BlogGroup as BlogGroup;
use App\Models\Profile\BlogUrl as BlogUrl;
use App\Models\Profile\PersonalNotes;
use App\Models\Profile\MessageDetailStructure;
use App\Models\Profile\ProfileCoverPhoto as ProfileCoverPhoto;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\HomeController as HomeController;
use App\Models\Medcubics\Roles as Roles;
use App\User as User;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use File;
use DB;
use Config;
use Session;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Users;

class BlogApiController extends Controller
{
	public $blog_itemperpage 	= 3;
	public $comments_itemperpage = 3;
	public static $comments_count = 3;
	
	public function getIndexApi($order='',$keyword='')
	{ 
		$user = Auth::user ()->id;
		$getblogs_public = Blog::with('user')->where('user_id','=',$user);
		$login_user = User::orderBy('created_at', 'ASC')->where('id',$user)->get();
		$today_notes = PersonalNotes::where('deleted_at',Null)->where('user_id',$user)->get()->count();
		/* Manage Blogs tab : Users: Online: Get active user list - Anjukaselvan*/
                $users_table = HomeController::getActiveUserList();
		if($keyword!='')
		{
			$userlist = User::whereRaw('(`name` like "%'.$keyword.'%")')->select(DB::raw('group_concat(id) as user_id'))->get();
			$getblogs_public->whereRaw('(`title` like "%'.$keyword.'%" or `description` like "%'.$keyword.'%" or `user_id`="'.$userlist[0]['user_id'].'")');
		}
		
		// Search the blog
		if($order == 'newest')	
			$getblogs_public->orderBy('created_at', 'Desc');
		elseif($order == 'oldest')
			$getblogs_public->orderBy('created_at', 'Asc');
		elseif($order == 'highcomment')	
			$getblogs_public->orderBy('comment_count', 'Desc');
		elseif($order == 'lowcomment')	
			$getblogs_public->orderBy('comment_count', 'Asc');
		else
			$getblogs_public->orderBy('created_at', 'Desc');	
			
		$blogs = $getblogs_public->get();
		
		$blogvotearray = array();
		$favblogarray  = array();
		$favcountarray = array();
		$commentcountarray = array();
		$blogpartcountarray = array();
		
		foreach($blogs as $blog) 
		{
			$blog_favourite = Blog::whereHas('Blog_favourite', function($q) use($user)
			{
				$q->where('user_id', '=', $user);

			})->where('id', $blog['id'] )->count();
			$blog_favcount     = Blog::find($blog->id)->Blog_favcount;
			$blog_commentcount = Blog::find($blog->id)->Blog_commentscount;
			$blogpart = Blog::find($blog->id)->Blog_commentscount->groupBy('user_id');
			$blog_vote = Blog::find($blog->id)->Blog_vote()->where('user_id', '=', $user)->first();
			
			$favblogarray[$blog['id']]        = $blog_favourite;
			$favcountarray[$blog->id]   = count($blog_favcount);
			$commentcountarray[$blog->id]   = count($blog_commentcount);
			$blogpartcountarray[$blog->id]   = count($blogpart);
			$blogvotearray[$blog->id]   =   $blog_vote;
		}
                $messages = MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); }, 'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get();
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('blogs','blogvotearray','favblogarray','favcountarray','commentcountarray','blogpartcountarray','users_table','login_user','today_notes','messages')));
	}
	
	public function getCreateApi()
	{
		$blog = '';
		$grouplist = BlogGroup::orderBy('group_name','asc')->pluck('group_name','id')->all();
		$get_user  = User::orderBy('name','asc')->pluck('name','id')->all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('blog','grouplist','get_user')));
	}
        
	public function blogListingApi($order='',$keyword='')
	{
		$user = Auth::user ()->id;
		$get_collect_array = array();
		// Get Blog Group based user.
		$login_user = User::orderBy('created_at', 'ASC')->where('id',$user)->get();
		$bloggroup = Blog::whereHas('Blog_group', function($q) use($user)
		{
			$q->whereRaw('find_in_set(?, `group_users`)',[$user]);

		})->where('privacy','=','Group')->where('status','=','Active')->select(DB::raw('group_concat(id) as Blog_id'))->get();
		
		$getgroupblogid = explode(',',$bloggroup[0]['Blog_id']);
		$get_collect_array = array_merge($get_collect_array,$getgroupblogid);	
		
		// Get selected user & public list.	
		$blogselecteduser = Blog::whereRaw('(find_in_set("'.$user.'", `user_list`) and privacy="User") or privacy="Public"')->where('status','=','Active')->select(DB::raw('group_concat(id) as Blog_id'))->get();
		$getselectuserblogid = explode(',',$blogselecteduser[0]['Blog_id']);
		$get_collect_array = array_merge($get_collect_array,$getselectuserblogid);		
		$collectblogids =  array_filter($get_collect_array);		
		/* Blogs tab : Users: Online: Get active user list - Anjukaselvan*/
                $users_table = HomeController::getActiveUserList();
                $blogs = array();
		
		// Listing the blog based on private and public and group.
		if(count($collectblogids)>0)
		{
			$getblogs_public = Blog::with('user')->whereIn('id', $collectblogids)->where('status','=','Active');
			
			if($keyword!='')
			{
				$userlist = User::whereRaw('(`name` like "%'.$keyword.'%")')->select(DB::raw('group_concat(id) as user_id'))->get();
				$getblogs_public->whereRaw('(`title` like "%'.$keyword.'%" or `description` like "%'.$keyword.'%" or `user_id`="'.$userlist[0]['user_id'].'")');
			}
			
			// Sort the blog.
			if($order == 'newest')	
				$getblogs_public->orderBy('created_at', 'Desc');
			elseif($order == 'oldest')
				$getblogs_public->orderBy('created_at', 'Asc');
			elseif($order == 'highcomment')	
				$getblogs_public->orderBy('comment_count', 'Desc');
			elseif($order == 'lowcomment')	
				$getblogs_public->orderBy('comment_count', 'Asc');
			else
				$getblogs_public->orderBy('created_at', 'Desc');	
				
			$blogs_public = $getblogs_public->get();
			
			$blogs = json_decode(json_encode($blogs_public),true); 
		}
	   
		$favblogarray = array();
		$favcountarray = array();
		$commentcountarray = array();
		$blogpartcountarray = array();
		$blogvotearray      = array();
		
		foreach($blogs as $blog) 
		{
			$blog_favourite = Blog::whereHas('Blog_favourite', function($q) use($user)
			{
				$q->where('user_id', '=', $user);

			})->where('id', $blog['id'] )->count();
			$blog_favcount     = Blog::find($blog['id'])->Blog_favcount;
			$blog_commentcount = Blog::find($blog['id'])->Blog_commentscount;
			$blogpart = Blog::find($blog['id'])->Blog_commentscount->groupBy('user_id');
			$blog_vote = Blog::find($blog['id'])->Blog_vote()->where('user_id', '=', $user)->first();
			
			$favblogarray[$blog['id']]        = $blog_favourite;
			$favcountarray[$blog['id']]   	= count($blog_favcount);
			$commentcountarray[$blog['id']]   = count($blog_commentcount);
			$blogpartcountarray[$blog['id']]   = count($blogpart);
			$blogvotearray[$blog['id']]          = $blog_vote;
		}
		$today_notes = PersonalNotes::where('deleted_at',Null)->where('user_id',$user)->get()->count();
		$getblogs_public = Blog::with('user')->where('user_id','=',$user);
		$getblogs_count = $getblogs_public->get();
                $messages = MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); }, 'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get();
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('blogs','getblogs_count','blogvotearray','favblogarray','favcountarray','commentcountarray','blogpartcountarray','users_table','login_user','today_notes','messages')));
	}	
        
	public function getStoreApi($request='')
	{
        ini_set('user_agent','CharlesUserAgent1.0');
		if($request == '')
			$request = Request::all();
		$rule = array('attachment'=>Config::get('siteconfigs.file_uplode.defult_file_attachment'));	
		$message = array('attachment.mimes'=>Config::get('siteconfigs.file_uplode.defult_file_message'));
		$validator = Validator::make($request,Blog::$rules+$rule,Blog::$messages+$message);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			//dd($errors);
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{
			$result = new Blog;
			$result->title   = $request['title'];
			$result->description = $request['description'];
			$result->privacy = $request['privacy'];
			$result->url = $request['url'];
			
			if($request['privacy'] == 'Group')
			{
				$result->user_list = $request['group'];
			}
			if($request['privacy'] == 'User')
			{
				$group_user = $request['selectuser'];
				$result->user_list = implode(",",$group_user);
			}
			$result->status   = $request['status'];
					
			$user = Auth::user ()->id;
							
			if (Input::hasFile('attachment'))
			{
				$image = Input::file('attachment');
				$extension = $image->getClientOriginalExtension();
				$fileName = rand(11111,99999).'.'.$extension;
				
				$resize = array('300','300');
				if($extension == 'jpg' or $extension == 'jpeg' or $extension == 'png')
					Helpers::mediauploadpath('','blog',$image,$resize,$fileName); 
				
				if($extension == 'pdf' or $extension == 'doc' or $extension == 'docx')
					Helpers::mediauploadpath('','blog',$image,'',$fileName); 
				
				if(Session::get('practice_dbid')!='')
					$result->attachment = $fileName;
			}
			$result->user_id = $user;
			$result->save ();
			
			if($request['url']!='')
			{
				// Get the url based result.
				$html = @file_get_contents($request['url']);
				
				 if($html === false)
				 {
					// error
				 } 
				 else 
				 {
					//parsing begins here:
					$doc = new \DOMDocument();
					$ss = @$doc->loadHTML($html);

					$nodes = $doc->getElementsByTagName('title');
					$title = $nodes->item(0)->nodeValue;

					$description = '';
					$metas = $doc->getElementsByTagName('meta');
					for ($i = 0; $i < $metas->length; $i++) 
					{
						$meta = $metas->item($i);
						if($meta->getAttribute('name') == 'description')
							$description = $meta->getAttribute('content');
					}
					$tags = $doc->getElementsByTagName('img');
					$i = 0;

					$iurl = '';
					foreach ($tags as $tag) 
					{
						$img_url = $tag->getAttribute('src');
						if($i<2)
						{
						   $ext = pathinfo($img_url, PATHINFO_EXTENSION);
						   if(($ext == 'jpg' or $ext == 'jpeg' or $ext == 'png') && !filter_var($img_url, FILTER_VALIDATE_URL) === false ) 
						   {
							   $i++;
							  $iurl = $img_url;
						   }
						}
					} 

					$resulturl = new BlogUrl;
					$resulturl->blog_id = $result->id;
					$resulturl->url = $request['url'];
					$resulturl->image = $iurl;
					$resulturl->title   = $title;
					$resulturl->description = $description;
					$resulturl->datetime = date('Y-m-d h:i:s');
					$resulturl->save();
				}   
			}
			return Response::json(array('status'=>'success', 'message'=>'Blog added successfully','data'=>''));					
		}
	}

	public function getShowApi($id)
	{
		$user = Auth::user ()->id;
		$move = 0;
		$login_user = User::orderBy('created_at', 'ASC')->where('id',$user)->get();
		$today_notes = PersonalNotes::where('deleted_at',Null)->where('user_id',$user)->get()->count();
		/* Blogs readmore tab : Users: Online: Get active user list - Anjukaselvan*/
                $users_table = HomeController::getActiveUserList();
                $messages = MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); }, 'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get();
		// Display the blog based on public, private, group.
		if(Blog::where('id',$id)->count())
		{    
			$check_privacy_query = Blog::where('id', $id )->first();
			$check_privacy       = $check_privacy_query->privacy;
			if($check_privacy == 'Public')
			{
				$move = 1;
			}
			elseif($check_privacy == 'Private')
			{
				$privatebloguserid = $check_privacy_query->user_id;
				if($privatebloguserid == $user)
				{
				   $move = 1; 
				}
			}
			elseif($check_privacy == 'Group')
			{
				 $bloguserid   = $check_privacy_query->user_id;
				 $groupid = $check_privacy_query->user_list;
				 $collect_users = BlogGroup::where('id', $groupid )->first()->group_users;
				 $collect_userarray = explode(",",$collect_users);
				 if($bloguserid == $user)
				 {
					  $move = 1; 
				 }
				 elseif(in_array($user, $collect_userarray)) 
				 {
					 $move = 1; 
				 }
			}
			elseif($check_privacy == 'User')
			{
				$bloguserid   = $check_privacy_query->user_id;
				$collect_users = $check_privacy_query->user_list;
				$collect_userarray = explode(",",$collect_users);
				if($bloguserid == $user)
				{
					  $move = 1; 
				 }elseif(in_array($user, $collect_userarray)) 
				 {
					 $move = 1; 
				 }
			}
		}    
			
		if($move == '1')
		{
			$blog = Blog::with('user')->where('id', $id )->first();

			$blog_favourite = Blog::whereHas('Blog_favourite', function($q) use($user)
			{
				$q->where('user_id', '=', $user);

			})->where('id', $id )->count();
			$blog_favcount     = Blog::find($id)->Blog_favcount;
			$blog_commentcount = Blog::find($id)->Blog_commentscount;
			$blogpart 	   = Blog::find($id)->Blog_commentscount->groupBy('user_id');
			$blog_url  	   = BlogUrl::where('blog_id', $id )->first();
			$blog_comments = BlogComments::with('user')->where('blog_id',$id)->orderBy('id', 'Desc')->get();
			
			
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('blog','blog_url','blog_favourite','blog_favcount','blogpart','blog_commentcount','blog_comments','users_table','login_user','today_notes','messages')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'No blogs found.','data'=>null));
		}
	}

	public function getEditApi($id)
	{
        $user = Auth::user ()->id; 
        if(Blog::where('id',$id)->where('user_id', '=', $user)->count())
		{
			$blog = Blog::where('id', '=', $id)->where('user_id', '=', $user)->first();
			$grouplist = BlogGroup::orderBy('group_name','asc')->pluck('group_name','id')->all();
			$get_user  = User::orderBy('name','asc')->pluck('name','id')->all();
			$privacy_id = $blog->privacy;
					
			$groupid = '';
			if($blog->privacy == 'Group') 
			{
				$groupid   = $blog->user_list;
			}
			$user_id = '';
			if($blog->privacy == 'User') 
			{
				$user_id   = explode(",",$blog->user_list);
			}
					
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('blog','grouplist','get_user','groupid','privacy_id','user_id')));
			}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'No record found.','data'=>null));
		}
	}
	
	public function getUpdateApi($id, $request='')
	{
        ini_set('user_agent','CharlesUserAgent1.0');
		if($request == '')
		$request = Request::all();
	
		// check upload file size.
		$rules = Blog::$rules + array('attachment'=>Config::get('siteconfigs.file_uplode.defult_file_attachment'));
		$messages = Blog::$messages + array('attachment.mimes'=>Config::get('siteconfigs.file_uplode.defult_file_message'));
		$validator = Validator::make($request,$rules, $messages);

		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{		
			$blog = Blog::findOrFail($id);
			if (Input::hasFile('attachment'))
			{
				$image = Input::file('attachment');
				$extension = $image->getClientOriginalExtension();
				$fileName = rand(11111,99999).'.'.$extension;
				
				$resize = array('300','300');
				if($extension == 'jpg' or $extension == 'jpeg' or $extension == 'png')
					Helpers::mediauploadpath('','blog',$image,$resize,$fileName,$blog->attachment);  
				
				if($extension == 'pdf' or $extension == 'doc' or $extension == 'docx')
					Helpers::mediauploadpath('','blog',$image,'',$fileName,$blog->attachment); 
				
				if(Session::get('practice_dbid')!='')
					$request['attachment'] = $fileName;
			}
			
			// Get url based on results.
			if($blog->url != $request['url'] && $request['url']!='')
			{
				$html = @file_get_contents($request['url']);  
				if($html === false)
				{
					// error
				} 
				else 
				{
					//parsing begins here:
					$doc = new \DOMDocument();
					$ss = @$doc->loadHTML($html);

					$nodes = $doc->getElementsByTagName('title');
					$title = $nodes->item(0)->nodeValue;

					$description = '';
					$metas = $doc->getElementsByTagName('meta');
					for ($i = 0; $i < $metas->length; $i++) 
					{
						$meta = $metas->item($i);
						if($meta->getAttribute('name') == 'description')
							$description = $meta->getAttribute('content');
					}
					$tags = $doc->getElementsByTagName('img');
					$i = 0;

					$iurl = '';
					foreach ($tags as $tag) 
					{
						$img_url = $tag->getAttribute('src');
						if($i<2)
						{
						   $ext = pathinfo($img_url, PATHINFO_EXTENSION);
						   if(($ext == 'jpg' or $ext == 'jpeg' or $ext == 'png') && !filter_var($img_url, FILTER_VALIDATE_URL) === false ) 
						   {
							  $i++;
							  $iurl = $img_url;
						   }
						}
					}
					
					$blog_url_array = array('blog_id'=>$blog->id,'url'=>$request['url'],'image'=>$iurl,'title'=>$title,'description'=>$description,'datetime'=>date('Y-m-d h:i:s'));
					$checkblogurlquery = BlogUrl::where('blog_id','=',$blog->id);
					if($checkblogurlquery->count()>0) 
					{
						$resulturl = $checkblogurlquery->update($blog_url_array);
					}
					else 
					{
						BlogUrl::create($blog_url_array);
					}
			  }
			}
			
			if($request['url']=='') 
			{
				$checkblogurlquery = BlogUrl::where('blog_id','=',$blog->id);
				if($checkblogurlquery->count()>0)
				{
					$resulturl = $checkblogurlquery->delete();
				}
			}
							
			$blog->update($request);
							
			if($request['privacy'] == 'Group')
				$blog->user_list = $request['group'];
			
			if($request['privacy'] == 'Private')
				$blog->user_list = '';
			
			if($request['privacy'] == 'Public')
				$blog->user_list = '';
			
			if($request['privacy'] == 'User')
			{
				$group_user = $request['selectuser'];
				$blog->user_list = implode(",",$group_user);
			}
							
			$user = Auth::user ()->id;
			$blog->user_id = $user;
			$blog->save ();
							
			return Response::json(array('status'=>'success', 'message'=>'Blog updated successfully','data'=>''));					
		}
	}

	public function getDestroyApi($id)
	{
	   $user = Auth::user ()->id;
		if(Blog::where('id',$id)->where('user_id', '=', $user)->count())
		{   
			// Delete the blog, favourate, url details.
			$blog_query = Blog::where('id', '=', $id)->where('user_id', '=', $user)->delete();
			BlogFavourite::where('blog_id', '=', $id)->delete();
			BlogUrl::where('blog_id', '=', $id)->delete(); 
			$blogcommentsquery = BlogComments::where('blog_id','=',$id);
			
			if($blogcommentsquery->count()>0)
			{
				$img_comments = $blogcommentsquery->get();
				foreach ($img_comments as $img)
				{
					if($img->attachment!='') 
					{
						Helpers::removeimage('','blog_comments',$img->attachment); 
					}
				}
			  $blogcommentsquery->delete();
			}
			return Response::json(array('status'=>'success', 'message'=>'Blog deleted successfully','data'=>''));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'No record found.','data'=>null));
		}    
	}
	    
	public function getFavouriteApi($get)
	{
		$blog_id = $get['blogid'];
		$user_id = Auth::user ()->id;
		if($get['page']=='1') 
		{
			$fav_count = BlogFavourite::where('user_id','=',$user_id)->where('blog_id', '=', $blog_id)->count();
			if($fav_count == '0')
			{
				$BlogFavourite = new BlogFavourite;
				$BlogFavourite->blog_id = $blog_id;
				$BlogFavourite->user_id = $user_id;
				$BlogFavourite->datetime = date('Y-m-d h:i:s');
				$BlogFavourite->save();
				return '1';
			}
			if($fav_count=='1')
			{
				BlogFavourite::where('user_id','=',$user_id)->where('blog_id', '=', $blog_id)->delete();
				return '2';
			}
		}
		
		// check favourite count option
		
		if($get['page']=='3')
		{
			return $blog_fav_count = BlogFavourite::where('blog_id', '=', $blog_id)->count();
		}
		
		// blog thumb option
		
		if($get['page']=='4'){
			$thumb_count = BlogVote::where('user_id','=',$user_id)->where('blog_id','=',$blog_id)->count();
			if($thumb_count == '0'){
					$BlogVote = new BlogVote;
					$BlogVote->blog_id = $blog_id;
					$BlogVote->user_id = $user_id;
					$BlogVote->datetime = date('Y-m-d h:i:s');
				if($get['type']=='up'){
					$BlogVote->up = '1';
					$BlogVote->save();

					$blog = Blog::find($blog_id);
					$blog->up_count = $blog->up_count+1;
					$blog->save();
					return 1;
				}
			
				if($get['type'] == 'down'){
					$BlogVote->down = '1';
					$BlogVote->save();

					$blog = Blog::find($blog_id);
					$blog->down_count = $blog->down_count+1;
					$blog->save();
					return 1;
				}
			} else {
				return 2;
			}
		}
	       
	    }
	    
    public function getCommentFavouriteApi($get){
            
        $user_id = Auth::user ()->id;
        // show comment count in blog
        
        if($get['page'] == '4'){
			 $blog_id = $get['blogid'];
             $comment_count = BlogComments::where('blog_id','=',$blog_id)->count();
             $participants_query = DB::table('blog_comments')->groupBy('user_id')->where('blog_id', '=', $blog_id)->whereNull('deleted_at')->get();
             $participants_count = count($participants_query);
             return $comment_count.'|~|'.$participants_count;
        }
        
         // show comment thumb
        
        if($get['page'] == '5'){
			$blog_id = $get['blogid'];
            $comment_id = $get['commentid'];
           $thumb_count = BlogCommentsVote::where('user_id','=',$user_id)->where('comment_id','=',$comment_id)->where('blog_id','=',$blog_id)->count();
            if($thumb_count == '0'){
                    $BlogCommentsVote = new BlogCommentsVote;
                    $BlogCommentsVote->blog_id = $blog_id;
                    $BlogCommentsVote->user_id = $user_id;
                    $BlogCommentsVote->comment_id = $comment_id;
                    $BlogCommentsVote->datetime = date('Y-m-d h:i:s');
                if($get['type']=='up'){
                    $BlogCommentsVote->up = '1';
                    $BlogCommentsVote->save();

                    $BlogComments = BlogComments::find($comment_id);
                    $BlogComments->up_count = $BlogComments->up_count+1;
                    $BlogComments->save();
                    return 1;
                }
            
                if($get['type'] == 'down'){
                    $BlogCommentsVote->down = '1';
                    $BlogCommentsVote->save();

                    $BlogComments = BlogComments::find($comment_id);
                    $BlogComments->down_count = $BlogComments->down_count+1;
                    $BlogComments->save();
                    return 1;
                }
            } else {
                return 2;
            }
        }
        
		// Add vote for reply comment.
		
		if($get['page'] == '7'){
            $comment_id 		= $get['commentid'];
			$parentcommentid	= $get['parentid'];
           $thumb_count = BlogReplyCommentsVote::where('user_id','=',$user_id)->where('comment_id','=',$comment_id)->where('parentcomment_id','=',$parentcommentid)->count();
            if($thumb_count == '0') {
                    $BlogCommentsVote = new BlogReplyCommentsVote;
                    $BlogCommentsVote->parentcomment_id = $parentcommentid;
                    $BlogCommentsVote->user_id = $user_id;
                    $BlogCommentsVote->comment_id = $comment_id;
                    $BlogCommentsVote->datetime = date('Y-m-d h:i:s');
                if($get['type']=='up') {
                    $BlogCommentsVote->up = '1';
                    $BlogCommentsVote->save();

                    $BlogReplyComments = BlogReplyComments::find($comment_id);
                    $BlogReplyComments->up_count = $BlogReplyComments->up_count+1;
                    $BlogReplyComments->save();
                    return 1;
                }
            
                if($get['type'] == 'down'){
                    $BlogCommentsVote->down = '1';
                    $BlogCommentsVote->save();
                    $BlogReplyComments = BlogReplyComments::find($comment_id);
                    $BlogReplyComments->down_count = $BlogReplyComments->down_count+1;
                    $BlogReplyComments->save();
                    return 1;
                }
            } else {
                return 2;
            }
        }
    }
    
    // Add comment    
    public function CommentApi($request='') 
	{
        if($request == '')
		$request = Request::all();
		$user = Auth::user ()->id;   
		$BlogComments = new BlogComments;
		if (Input::hasFile('attachment'))
		{
			$image = Input::file('attachment');
			$extension = $image->getClientOriginalExtension();
			$fileName = rand(11111,99999).'.'.$extension;
			
			$resize = array('300','300');
			if($extension == 'jpg' or $extension == 'jpeg' or $extension == 'png')
				Helpers::mediauploadpath('','blog_comments',$image,$resize,$fileName); 
			
			if($extension == 'pdf' or $extension == 'doc' or $extension == 'docx')
				Helpers::mediauploadpath('','blog_comments',$image,'',$fileName); 
			
			if(Session::get('practice_dbid')!='')
				$BlogComments->attachment= $fileName;
		}
	  
		$BlogComments->user_id = $user;
		$BlogComments->blog_id = $request['blog_id'];
		$BlogComments->comments  = $request['comment'];
		$BlogComments->datetime  = date('Y-m-d h:i:s');
		$BlogComments->save();
		
		$blog_detail = Blog::find($request['blog_id']);
		$blog_detail->comment_count = $blog_detail->comment_count+1;
		$blog_detail->save();
		
		$get_newcomment = BlogComments::find($BlogComments->id);
		
        return Response::json(array('status'=>'success', 'message'=>'Comments posted successfully','data'=>compact('get_newcomment')));					
    }
	
	 // Add Reply comment    
    public function CommentReplyApi($request='') 
	{
        if($request == '')
		$request = Request::all();
		$user = Auth::user ()->id;   
		$BlogReplyComments = new BlogReplyComments;
		$BlogReplyComments->user_id = $user;
		$BlogReplyComments->blog_id = $request['blog_id'];
		$BlogReplyComments->comment_id = $request['comment_id'];
		$BlogReplyComments->comments  = $request['comments'];
		$BlogReplyComments->created_at  = date('Y-m-d h:i:s');
		$BlogReplyComments->save();
		$get_newcomment = BlogReplyComments::find($BlogReplyComments->id);
		
        return Response::json(array('status'=>'success', 'message'=>'Comments posted successfully','data'=>compact('get_newcomment')));					
    }
    
	// Get blog comments.
    public function getCommentApi($get)
	{
        $blog_id 	 = $get['blogid'];
        $get_comment = BlogComments::with('user')->where('blog_id',$blog_id)->orderBy('id', 'Desc')->get();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('get_comment')));
    }
    
	// Delete blog comments.
    public function deleteCommentsApi($id,$blogid) 
	{
        $comment_query = BlogComments::where('id', '=', $id);
        if($comment_query->first()->attachment!='') 
		{
              Helpers::removeimage('','blog_comments',$comment_query->first()->attachment); 
        }
        $comment_query->delete();
        BlogReplyComments::where('comment_id','=',$id)->where('blog_id', '=', $blogid)->delete();
        return BlogComments::where('blog_id',$blogid)->count();
    }
	
	// Delete reply blog comments.
	public function deleteReplyCommentsApi($replyid,$parentid)
	{
		BlogReplyComments::where('id','=',$replyid)->where('comment_id', '=', $parentid)->delete();
        return BlogReplyComments::where('comment_id',$parentid)->count();
	}
	
	// Get cover photo details.
	public static function getCoverPhotoApi()
	{
		$user = Auth::user ()->id; 
		$get_photo 	  = ProfileCoverPhoto::where('userid', '=', $user)->get();
		//$rolelist	  = Roles::pluck('role_name','id')->all();
	
		if(count($get_photo)>0)
		{
			return Response::json(array('status'=>'available', 'data'=>compact('get_photo')));					
		}
		else 
		{
			return Response::json(array('status'=>'unavailable', 'data'=>compact('get_photo')));
		}
	}
	
	/*** Start to Add/Edit cover photo ***/ 	
	public function addCoverPhotoApi($request='')
	{
		 if($request == '')
			$request = Request::all();
		
		$user = Auth::user ()->id;
		$getAmazonImgUrl = Helpers::getPracticeBlogImgUrl('cover_photo','ProfileUserCover');
		
		if (Input::hasFile('attachment'))
		{
			$image = Input::file('attachment');
			$extension = $image->getClientOriginalExtension();
			$fileName = rand(11111,99999).'.'.$extension;
			
			$resize = array('850','315');
			//$resize = array('50','50');
			
			if(Session::get('practice_dbid')!='')
			{
				if($request['action'] == 'add')	
				{
					$result = new ProfileCoverPhoto;
					$result->coverphoto = $fileName;
					Helpers::mediauploadpath('ProfileUserCover','cover_photo',$image,$resize,$fileName); 
					$result->userid = $user;
					$result->save ();
					echo $getAmazonImgUrl.'/'.$fileName;
					exit;
				}
				if($request['action'] == 'edit')
				{
					$getcover = ProfileCoverPhoto::where('userid', '=', $user)->get();
					Helpers::mediauploadpath('ProfileUserCover','cover_photo',$image,$resize,$fileName,$getcover[0]->coverphoto); 
					ProfileCoverPhoto::where('userid', '=', $user)->update(['coverphoto'=>$fileName]);
					echo $getAmazonImgUrl.'/'.$fileName;
					exit;
				}
			}	
		}
	}
	/*** End to Add/Edit cover photo ***/ 
	
	/*** Start to recent favourites ***/
	public static function getRecentFavouriteApi()
	{
        $user_id = Auth::user ()->id;
        return $getfav = BlogFavourite::with('blog','user')->where('user_id','=',$user_id)->orderBy('datetime', 'Desc')->take(3)->get();
	}
	/*** End to recent favourites ***/
	
	public static function teamMemberApiConmtroller()
	{
		$user = user::get();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('user')));
	}
	
	/*** Start to recent comments ***/
	public static function getRecentCommentsApi($blogid)
	{
		return $blog_comments = BlogComments::with('user')->where('blog_id','=',$blogid)->orderBy('datetime', 'Desc')->take(3)->get();
	}
	/*** End to recent comments ***/
	
	/*** Start to most viewed  ***/
	public static function getMostViewedApi()
	{
		$user_id = Auth::user ()->id;
		return $blog = Blog::with('user')->where('user_id','=',$user_id)->orderBy('comment_count', 'Desc')->take(3)->get();
	}
	/*** End to most viewed ***/
	
	// Get reply comments.
	public static function getReplyCommentsApi($comment_id)
	{
		$replycomment = BlogReplyComments::with('user')->where('comment_id','=',$comment_id)->orderBy('created_at', 'Desc')->get();	
		return Response::json(array('data'=>compact('replycomment')));
	}
	
	// Remove cover photo.
	public function removeCoverPhotoApi()
	{
		$user_id = Auth::user ()->id;
		$getimage = ProfileCoverPhoto::where('userid', '=', $user_id)->get();
		$getCoverName = $getimage[0]->coverphoto;
		if($getCoverName!='')
			Helpers::removeimage('ProfileUserCover','cover_photo',$getCoverName); 	
		ProfileCoverPhoto::where('userid', '=', $user_id)->delete();
	}
	
	public function getUsersFilterApi($keyword){
		 $practice_id = Session::get('practice_dbid');
           $practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();
           $admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
           $practice_user_arr2 = Users::whereRaw("(($admin_practice_id_like))")->pluck('id')->all();
           $practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
          
		if($keyword == '')
			 $user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->get();
		else
			 $user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->where('name', 'LIKE', '%'.$keyword.'%')->get();


          
		
		return Response::json(array('data'=>compact('user_list')));
	}
}