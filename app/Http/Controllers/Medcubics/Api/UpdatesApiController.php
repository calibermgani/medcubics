<?php namespace App\Http\Controllers\Medcubics\Api;

use Response;
use Validator;
use Request;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use DB;
use App;
use Lang;
use App\User;
use View;
use Html;
use Config;
use Input;
use Carbon\Carbon;
use Session;
use Illuminate\Http\Response as Responseobj;
use App\Models\Profile\Blog as Blog;
use App\Http\Controllers\HomeController as HomeController;
use App\Models\Profile\PersonalNotes;
use App\Models\Profile\MessageDetailStructure;
use App\Models\Profile\BlogUrl as BlogUrl;
use App\Models\Profile\BlogComments as BlogComments;
use App\Models\Profile\BlogGroup as BlogGroup;

class UpdatesApiController extends Controller {
	public $blog_itemperpage = 3;
	public $comments_itemperpage = 3;
	
	/**** LOG List page start ***/
	public function getIndexApi($order='',$keyword='')
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
		$blogselecteduser = Blog::whereRaw('(find_in_set("'.$user.'", `user_list`) and privacy="User") or privacy="Public"')->select(DB::raw('group_concat(id) as Blog_id'))->get();
		$getselectuserblogid = explode(',',$blogselecteduser[0]['Blog_id']);
		$get_collect_array = array_merge($get_collect_array,$getselectuserblogid);		
		$collectblogids =  array_filter($get_collect_array);		
		/* Blogs tab : Users: Online: Get active user list - Anjukaselvan*/
                $users_table = HomeController::getActiveUserList();
                $blogs = array();
		
		// Listing the blog based on private and public and group.
		if(count($collectblogids)>0)
		{
			$getblogs_public = Blog::with('user')->whereIn('id', $collectblogids)->whereIn('status',['Active','Inactive']);
			
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

	public function getCreateApi()
	{
		$blog = '';		
		$get_user  = User::orderBy('name','asc')->pluck('name','id')->all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('blog','get_user')));
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
			$result->description = html_entity_decode($request['description']);
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
					Helpers::mediauploadpath('admin','blog',$image,$resize,$fileName); 
				
				if($extension == 'pdf' or $extension == 'doc' or $extension == 'docx')
					Helpers::mediauploadpath('admin','blog',$image,'',$fileName); 
				
				if($extension == 'jpg' or $extension == 'jpeg' or $extension == 'png' OR $extension == 'pdf' or $extension == 'doc' or $extension == 'docx')
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

	public function getViewLogApi($file_name){
		try{
			$log_path = storage_path('logs/');
			$file_content = @file($log_path.$file_name);
		} catch(Exception $e){
			$file_content = '';
		}
		return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('file_content')));
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

	public function getBlogdocumentApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$blog = Blog::where('id','=',$id)->first();
		if(isset($blog->attachment) && $blog->attachment != '') {
			$file = Helpers::amazon_server_get_file('admin/image/blog/',$blog->attachment);			
			$extension = substr(strrchr($blog->attachment, "."), 1);

			switch ($extension) {
	            case 'pdf':
	                $type = "application/pdf";
	                break;

	            case 'jpg':
	            case 'jpeg':
	                $type = "image/jpeg";
	                break;

	            case 'png':
	                $type = "image/jpeg";
	                break;

	            case 'xls':
	            case 'xlsx':
	                $type = "Application/x-msexcel";
	                break;
	            
	            case 'doc':
	            	$type = "application/msword";
	                break;
	            
	            case 'docx':
	                $type = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
	                break;    

	            default:
	                $type = "application/octet-stream";
	                break;
	        }
			return (new Responseobj ( $file, 200 ))->header ( 'Content-Type', $type); //'image/jpeg');
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

		if ($validator->fails()) {
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		} else {
			$blog = Blog::findOrFail($id);
			$request['description'] = html_entity_decode($request['description']);

			if (Input::hasFile('attachment'))
			{
				
				$image = Input::file('attachment');
				$extension = $image->getClientOriginalExtension();
				$fileName = rand(11111,99999).'.'.$extension;
				$resize = array('300','300');
				
				if($extension == 'jpg' or $extension == 'jpeg' or $extension == 'png')
					Helpers::mediauploadpath('admin','blog',$image,$resize,$fileName,$blog->attachment);  
				
				if($extension == 'pdf' or $extension == 'doc' or $extension == 'docx')
					Helpers::mediauploadpath('admin','blog',$image,'',$fileName,$blog->attachment);

				if($extension == 'jpg' or $extension == 'jpeg' or $extension == 'png' OR $extension == 'pdf' or $extension == 'doc' or $extension == 'docx')					
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
	public function getViewErrorLogApi($file_name, $errType){

		try{
			$log_path = storage_path('logs/');
			$file_content = @file($log_path.$file_name);
			//$file_content = file_get_contents($log_path.$file_name);
			$resp = [];
			foreach ($file_content as $line_num => $line) {
				$lineTxt = htmlspecialchars($line);	
				if($errType == 'error') {
					if (($tmp = stristr($lineTxt, '.ERROR:')) !== false) {	//if (strpos($lineTxt, '.ERROR:') !== false) {
						if(trim(str_replace("\n", '', $lineTxt)) != ''){
							$lineTxt = str_ireplace('local.ERROR:', ' - ', $lineTxt);
							array_push($resp, $lineTxt);				
						}	
					}
				} else {					
					if (($tmp = stristr($lineTxt, '.ERROR:')) !== false || ($tmp = stristr($lineTxt, '.INFO:')) !== false) {	
						if(trim(str_replace("\n", '', $lineTxt)) != ''){
							$lineTxt = str_ireplace('local.ERROR:', ' - ', $lineTxt);
							array_push($resp, $lineTxt);				
						}	
					}
				}
			    //echo "<br>Line #<b>{$line_num}</b> : " .$lineTxt . "<br />\n";
			}
			arsort($resp);					// Get Log Details as Decending Order
			$resp = array_slice($resp, 0, 20);	// Get Last 20 Errors
			$file_content = $resp;
		} catch(Exception $e){
			$file_content = '';
		}
		return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('file_content')));
	}
	public function getnewfeautureModelApi($order='',$keyword='')
	{		
		$user = Auth::user ()->id;
		$get_collect_array = array();
		$practice_timezone = Helpers::getPracticeTimeZone();
		$end_date = date('Y-m-d',strtotime(Carbon::now()));
		$start_date = date('Y-m-d',strtotime(Carbon::now()->subDays(30)));
		
		// Get Blog Group based user.
		$login_user = User::orderBy('created_at', 'ASC')->where('id',$user)->get();
		$bloggroup = Blog::on('responsive')->where(DB::raw('DATE(created_at)'),'>=',$start_date)->where(DB::raw('DATE(created_at)'),'<=',$end_date)->whereHas('Blog_group', function($q) use($user)
		{
			$q->whereRaw('find_in_set(?, `group_users`)',[$user]);

		})->where('privacy','=','Group')->where('status','=','Active')->select(DB::raw('group_concat(id) as Blog_id'))->get();
		
		$getgroupblogid = explode(',',$bloggroup[0]['Blog_id']);
		$get_collect_array = array_merge($get_collect_array,$getgroupblogid);	
		
		// Get selected user & public list.	
		$blogselecteduser = Blog::on('responsive')->where(DB::raw('DATE(created_at)'),'>=',$start_date)->where(DB::raw('DATE(created_at)'),'<=',$end_date)->whereRaw('(find_in_set("'.$user.'", `user_list`) and privacy="User") or privacy="Public"')->where('status','=','Active')->select(DB::raw('group_concat(id) as Blog_id'))->get();
		$getselectuserblogid = explode(',',$blogselecteduser[0]['Blog_id']);
		$get_collect_array = array_merge($get_collect_array,$getselectuserblogid);		
		$collectblogids =  array_filter($get_collect_array);		
		/* Blogs tab : Users: Online: Get active user list - Anjukaselvan*/
                $users_table = HomeController::getActiveUserList();
                $blogs = array();
		
		// Listing the blog based on private and public and group.
		if(count($collectblogids)>0)
		{
			$getblogs_public = Blog::on('responsive')->where(DB::raw('DATE(created_at)'),'>=',$start_date)->where(DB::raw('DATE(created_at)'),'<=',$end_date)->with('user')->whereIn('id', $collectblogids)->where('status','=','Active');
			
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
			$blog_favourite = Blog::on('responsive')->whereHas('Blog_favourite', function($q) use($user)
			{
				$q->where('user_id', '=', $user);

			})->where('id', $blog['id'] )->count();
			$blog_favcount     = Blog::on('responsive')->find($blog['id'])->Blog_favcount;
			$blog_commentcount = Blog::on('responsive')->find($blog['id'])->Blog_commentscount;
			$blogpart = Blog::on('responsive')->find($blog['id'])->Blog_commentscount->groupBy('user_id');
			$blog_vote = Blog::on('responsive')->find($blog['id'])->Blog_vote()->where('user_id', '=', $user)->first();
			
			$favblogarray[$blog['id']]        = $blog_favourite;
			$favcountarray[$blog['id']]   	= count($blog_favcount);
			$commentcountarray[$blog['id']]   = count($blog_commentcount);
			$blogpartcountarray[$blog['id']]   = count($blogpart);
			$blogvotearray[$blog['id']]          = $blog_vote;
		}
		$today_notes = PersonalNotes::on('responsive')->where('deleted_at',Null)->where('user_id',$user)->get()->count();
		$getblogs_public = Blog::on('responsive')->with('user')->where('user_id','=',$user);
		$getblogs_count = $getblogs_public->get();
                $messages = MessageDetailStructure::on('responsive')->with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); }, 'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get();
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('blogs','getblogs_count','blogvotearray','favblogarray','favcountarray','commentcountarray','blogpartcountarray','users_table','login_user','today_notes','messages')));
	}	

	
	
	/**** LOG View page end ***/
}
