<?php 
namespace App\Http\Controllers\Profile\Api;
use Auth;
use Request;
use Response;
use Input;
use Validator;
use Hash;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as User;
use App\Models\Profile\ProfileEvents as ProfileEvents;
use App\Models\Profile\Blog as Blog;
use App\Models\Profile\PersonalNotes;
use App\Models\Profile\PrivateMessageDetails;
use App\Models\Profile\PrivateMessage;
use App\Models\Profile\MessageDetailStructure as MessageDetailStructure;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\UsersAppDetails as UsersAppDetails;
use App\Http\Controllers\HomeController as HomeController;
use App;
use DB;
use Session;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Users;

class DashboardApiController extends Controller {
	
	/**
	 * Getting all notification count in the function.
	 *
	 * @param  use App\Models\Profile\PersonalNotes;
	 * @param  use App\Models\Profile\PrivateMessageDetails;
	 * @param  use App\Models\Profile\Blog as Blog;
	 * @return login user detalis and notification count
	 */
    public function getindexApi()
    {   
        $user = Auth::user()->id;
        $start_date = date("Y-m-d");
		$login_user = User::orderBy('created_at', 'ASC')->where('id',$user)->get();
		// Get Events
        $events = ProfileEvents::where('start_date', '=', $start_date)->where('created_by',$user)->orderBy('created_at', 'DESC')->take(4)->get()->toArray();
		
		// Check blog based on public & private, group, selected user.
		$get_collect_array = array();
		// Get Blog Group based user.
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
		$blogs = Blog::whereIn('id', $collectblogids)->where('status','=','Active')->take(4)->get();
                /* Notes tabs : Users: Online: Get active user list - Anjukaselvan*/
                $users_table = HomeController::getActiveUserList();
                $message_inbox_list_arr = PrivateMessageDetails::with('user','PrivateMessage')->where('user_id',$user)->orderBy('created_at','DESC')->take(4)->get();	
		$today_notes = PersonalNotes::where('deleted_at',Null)->where('user_id',$user)->get()->count();		
		$notes_list = PersonalNotes::where('user_id',$user)->where('deleted_at',Null)->orderBy('id','DESC')->get();		
                $messages = MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); }, 'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('events','message_inbox_list_arr', 'blogs','user','users_table','login_user','notes_list','today_notes','messages')));
    }
	
	/**
	 * This function using to create and update notes in database.
	 *
	 * @param  use App\Models\Profile\PersonalNotes;
	 * @return all notes lists
	 */
	public function setnoteApi(Request $request){
		$request = Request::all();
		$user_id = Auth::user()->id;
		if($request['note_date'] != '')
			$insert_data['date'] = date('Y-m-d',strtotime($request['note_date']));	
		$insert_data['notes'] = $request['personal_note'];
		$insert_data['user_id'] = $user_id;
		if($request['note_id'] == '')
			$result = PersonalNotes::create($insert_data);
		else
			$result = PersonalNotes::where('id',$request['note_id'])->update($insert_data);
		$notes_list = PersonalNotes::where('user_id',$user_id)->where('deleted_at',Null)->orderBy('id','DESC')->get();
		return Response::json(array('status' => 'success','data'=>compact('notes_list')));
	}
	
	/**
	 * This function using to delete the already added notes in database.
	 *
	 * @param  use App\Models\Profile\PersonalNotes;
	 * @return all notes list after delete
	 */
	public function setdeleteApi($id){
		$result = PersonalNotes::where('id',$id)->update(array('deleted_at'=>'Yes'));
		$user_id = Auth::user()->id;
		$notes_list = PersonalNotes::where('user_id',$user_id)->where('deleted_at',Null)->orderBy('id','DESC')->get();
		return Response::json(array('status' => 'success','data'=>compact('notes_list')));
	}

	/**
	 * This is using to retrieved the stored notes in database.
	 *
	 * @param  use App\Models\Profile\PersonalNotes;
	 * @return all notes lists
	 */
	public function getnotesApi($id){
		$user_id = Auth::user()->id;
		$notes_list = PersonalNotes::where('user_id',$user_id)->where('id',$id)->where('deleted_at',Null)->orderBy('id','DESC')->get();
		return Response::json(array('status' => 'success','data'=>compact('notes_list')));
	}
	
}