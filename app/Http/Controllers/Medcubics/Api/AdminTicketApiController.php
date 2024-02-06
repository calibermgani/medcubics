<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use Input;
use Auth;
use Response;
use Request;
use Validator;
use Config;
use DB;
use View;
use Session;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Users as Users;
use App\Models\Support\Ticket as Ticket;
use App\Models\Support\TicketDetail as TicketDetail;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use App\Models\Support\AssignTicketHistory as AssignTicketHistory;
use App\Models\EmailTemplate;
use Lang;

class AdminTicketApiController extends Controller 
{
	/*** Create new ticket form start ***/
	public function getIndexApi()
	{
		$userlist = Users::where('id','!=','1')->pluck('name','id')->all();
		$assigneduserlist = Users::where('id','!=','1')->where('user_type','Medcubics')->pluck('name','id')->all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('userlist','assigneduserlist')));		
	}
	/*** Create new ticket form end ***/
	
	/*** Store ticket details start ***/
	public function postTicketApi($request='')
	{
		if($request == '')
			$request = Request::all();
		if($request['usertype'] == 'guestuser')
		{
			$admin_rules =  Ticket::$adminticketrules+array('name' => 'required','email_id' => 'required|email');
		}
		else
		{
			$admin_rules =  Ticket::$adminticketrules+array('userlist_id' => 'required');
		}
		
		$validator = Validator::make($request,$admin_rules, Ticket::messages());
	
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{
				$user = '';
			if(Auth::user ()!='')
				$user = Auth::user ()->id;
			
			$filestoreName = '';
			$file_getmimetype = '';
			if (Input::hasFile('attachmentfield'))
			{ 
				$image              = Input::file('attachmentfield');
				$filename           = rand(11111,99999);
				$extension          = $image->getClientOriginalExtension();
				$filestoreName      = $filename .'.'.$extension;
				$file_getmimetype 	= $image->getMimeType();
				Helpers::mediauploadpath('support','ticket',$image,'',$filestoreName); 
			}

			$today_time = date("Hs");
			$rand       = mt_rand(1201, 9871);
			$rand2      = mt_rand(10, 90);
			
			if($request['usertype'] == 'registereduser') {
				$userlistid = $request['userlist_id'];
				$get_userinfo = Users::where('id',$userlistid)->first();
				$name	= $get_userinfo['name'];
				$user_email	= $get_userinfo['email'];
			} else {
				$user_email	= $request['email_id'];
			}
			
			
			$ticket = Ticket::create($request);
			$get_ticket_id	 = $ticket->id.$today_time.$rand.$rand2;
			$ticket->ticket_id = $get_ticket_id;
			if($request['usertype'] == 'registereduser') {
				$ticket->name = $name;
				$ticket->email_id = $user_email;
			}
			$ticket->notification_sent = 'No';
			$ticket->assigned = $request['assigneduser_id'];
			$ticket->assignedby = $user;
			$ticket->assigneddate = date('Y-m-d');
			$ticket->created_by = $user;
			$ticket->save ();
			
			
			$TicketDetail = new TicketDetail;
			$TicketDetail->attach_details = $filestoreName;
			$TicketDetail->image_type = $file_getmimetype;
			$TicketDetail->ticket_id = $get_ticket_id;
			$TicketDetail->description = $request['description'];
			$TicketDetail->posted_by = 'Admin';
			$TicketDetail->postedby_id = $user;
			$TicketDetail->save();
			
			// Assignee history maintain
			$AssignTicketHistory = new AssignTicketHistory;
			$AssignTicketHistory->assigned = $request['assigneduser_id'];
			$AssignTicketHistory->assigned_by = $user;
			$AssignTicketHistory->ticket_id = $get_ticket_id;
			$AssignTicketHistory->save();
			
			// mail send to admin
			if(EmailTemplate::where('template_for','ticket_admin')->count()>0) 	{
				$templates = EmailTemplate::where('template_for','ticket_admin')->first();
				$get_Email_Template = $templates->content;					
				$arr = [
					"##FIRSTNAME##" =>$request['name'],
					"##TICKETID##" => $get_ticket_id
				]; 
				$email_content = strtr($get_Email_Template, $arr);					
				$res = array(	'email'		=>  Config::get('siteconfigs.userticket.admin_email'),
								'subject'	=>	$templates->subject,
								'msg'		=>	$email_content,
								'name'		=>	'Admin' );	
				$msg_status = CommonApiController::connectEmailApi($res);
			}
			
			// mail send to user
			if(EmailTemplate::where('template_for','ticket_user')->count()>0) {
				$templates = EmailTemplate::where('template_for','ticket_user')->first();
				$get_Email_Template = $templates->content;					
				$arr = [
					"##FIRSTNAME##" =>$request['name'],
					"##TICKETID##" => $get_ticket_id
				]; 
				$email_content = strtr($get_Email_Template, $arr);					
				$res = array(	'email'		=>	@$user_email,
								'subject'	=>	$templates->subject,
								'msg'		=>	$email_content,
								'name'		=>	@$request['name'] );	
				$msg_status = CommonApiController::connectEmailApi($res);
			}
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$get_ticket_id));
		}
	}
	/*** Store ticket details end ***/
	
	
}
