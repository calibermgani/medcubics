<?php namespace App\Http\Controllers\Support\Api;

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
use App\Models\Support\Ticket as Ticket;
use App\Models\Support\TicketDetail as TicketDetail;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\EmailTemplate;
use Lang;
use Mail;

class TicketApiController extends Controller 
{
	public function getTicketApi()
	{
		$this->checkPermission();
	}
	
	/*** Create the ticket ***/
	public function postTicketApi($request='')
	{
		$this->checkPermission();
		if($request == '')
			$request = Request::all();
		$validator = Validator::make($request, Ticket::$rules, Ticket::messages());
	
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
			if (Input::hasFile('ticketfile'))
			{ 
				$image              = Input::file('ticketfile');
				$filename           = rand(11111,99999);
				$extension          = $image->getClientOriginalExtension();
				$filestoreName      = $filename .'.'.$extension;
				$file_getmimetype 	= $image->getMimeType();
				Helpers::mediauploadpath('support','ticket',$image,'',$filestoreName); 
			}

			$today_time = date("Hs");
			$rand       = mt_rand(1201, 9871);
			$rand2      = mt_rand(10, 90);
			
			$ticket = Ticket::create($request);
			$get_ticket_id	 = $ticket->id.$today_time.$rand.$rand2;
			$ticket->ticket_id = $get_ticket_id;
			$ticket->notification_sent = 'No';
			$ticket->created_by = $user;
			$ticket->save ();			
			
			$TicketDetail = new TicketDetail;
			$TicketDetail->attach_details = $filestoreName;
			$TicketDetail->image_type = $file_getmimetype;
			$TicketDetail->ticket_id = $get_ticket_id;
			$TicketDetail->description = $request['description'];
			$TicketDetail->posted_by = 'User';
			$TicketDetail->postedby_id = $user;
			$TicketDetail->save();
			
			// mail send to admin
			if(EmailTemplate::where('template_for','ticket_admin')->count()>0)
			{
				$templates = EmailTemplate::where('template_for','ticket_admin')->first();
				$get_Email_Template = $templates->content;					
				$arr = [
					"##FIRSTNAME##" =>$request['name'],
					"##TICKETID##" => $get_ticket_id
				]; 
				$email_content = strtr($get_Email_Template, $arr);					
				$res = array('email'=>Config::get('siteconfigs.userticket.admin_email'),
					'subject'	=>	$templates->subject,
					'msg'		=>	$email_content,
					'name'		=>	'Admin'
				);	
				//$msg_status = CommonApiController::connectEmailApi($res);
			}
			
			
			// mail send to user
			if(EmailTemplate::where('template_for','ticket_user')->count()>0)
			{
				$templates = EmailTemplate::where('template_for','ticket_user')->first();
				$get_Email_Template = $templates->content;					
				$arr = [
					"##FIRSTNAME##" =>$request['name'],
					"##TICKETID##" => $get_ticket_id
				]; 
				$email_content = strtr($get_Email_Template, $arr);					
				$res = array('email'=>	@$request['email_id'],
					'subject'	=>	$templates->subject,
					'msg'		=>	$email_content,
					'name'		=>	@$request['name']
				);	
				//$msg_status = CommonApiController::connectEmailApi($res);
			}
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$get_ticket_id));					
		}
	}
	
	public function getMyTicketApi($export='')
	{
		$this->checkPermission();
		$user = Auth::user()->id;
		$ticket = Ticket::where('created_by',$user)->orderBy('id','DESC')->get();
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'		=>	'ticket',
				'heading'		=>	'',
				'fields' 		=>	array(
					'name'				=>	'Name',
					'email_id'			=>	'Email',
					'title'				=>	'Title',
					'type'				=>	'Type',
					'status'			=>	'Status',
					'notification_sent'	=>	'Notification Sent'
					)
			);
            $callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $ticket, $export); 
        }
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('ticket')));
	}
	
	public function checkPermission()
	{
		$dbconnection = new DBConnectionController();	
		View::share ( 'checkpermission', $dbconnection );
	}	
}