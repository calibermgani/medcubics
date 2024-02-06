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
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\EmailTemplate;
use Lang;

class MyticketApiController extends Controller 
{
	public function getMyTicketApi($export='')
	{
		$this->checkPermission();
		$ticket = $this->getMyticketSearchApi();
		
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'		=>	'ticket',
				'heading'		=>	'',
				'fields' 		=>	array(
					'ticket_id'			=>	'Ticket ID',
					'email_id'			=>	'Email',
					'title'				=>	'Title',
					'status'			=>	'Status'
					)
			);
            $callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $ticket, $export); 
        }
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('ticket')));
	}
	
	public function getMyticketSearchApi()
	{
		$user = Auth::user()->id;
		$request = Request::all();
		$ticket = Ticket::where('created_by',$user)->orderBy('id','DESC');
		
		if(isset($request['ticket_id']) && $request['ticket_id'] != '')
		{
			$ticket->where('ticket_id','like', '%' . $request['ticket_id'] . '%');
		}
		
		if(isset($request['title']) && $request['title'] != '')
		{
			$ticket->where('title','like', '%' . $request['title'] . '%');
		}
		
		if(isset($request['status']) && $request['status'] != '')
		{
			$ticket->where('status',$request['status']);
		}		
		return $ticket->get();
	}
	
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$this->checkPermission();
		if(Ticket::where('id', $id )->count()>0)
		{
			$ticket = Ticket::where('id',$id)->first();
			$ticket_detail = TicketDetail::with('posted_user')->where('ticket_id',$ticket->ticket_id)->get();
			$getLastTicketinfo = TicketDetail::with('posted_user')->where('ticket_id',$ticket->ticket_id)->orderBy('id', 'desc')->get();
			
			// remove last reply.
			$getlastticket = [];
			foreach($getLastTicketinfo as $ticketinfo)
			{
				if($ticketinfo->posted_by == 'Admin') {
					break;
				} else {
					$getlastticket[]	= 	$ticketinfo->id;	
				}					
			}
			
			$ticket->id = Helpers::getEncodeAndDecodeOfId($ticket->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('ticket','ticket_detail','getlastticket')));	
		} else {
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	
	public function removeReplyApi($ticketid,$replyid)
	{
		$replyid = Helpers::getEncodeAndDecodeOfId($replyid,'decode');
		if(TicketDetail::with('posted_user')->where('ticket_id',$ticketid)->where('id',$replyid)->count()>0) {
			TicketDetail::with('posted_user')->where('ticket_id',$ticketid)->where('id',$replyid)->delete();
			return 'yes';			
		} else {
			return 'no';
		}
		
	}
	
	public function checkPermission()
	{
		$dbconnection = new DBConnectionController();	
		View::share ( 'checkpermission', $dbconnection );
	}	
}