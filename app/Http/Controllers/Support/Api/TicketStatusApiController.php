<?php namespace App\Http\Controllers\Support\Api;

use App\Http\Controllers\Controller;
use Input;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use View;
use Lang;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Support\Ticket as Ticket;
use App\Models\Support\TicketDetail as TicketDetail;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use Illuminate\Http\Response as Responseobj;
use App\Models\EmailTemplate;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Payments\ClaimInfoV1;
use Session;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;

class TicketStatusApiController extends Controller 
{
	public function getTicketStatusApi()
	{
		$this->checkPermission();
	}
	
	public function getTicketDetailApi($id,$page='')
	{
		$this->checkPermission();
		$getuser_id = (isset(Auth::user()->id))? Auth::user()->id:'0';
		
		if($getuser_id == 0)
		{
			$email_id = $page;
		}
		
		if(Ticket::where('ticket_id','=',$id)->count())
		{
			$get_ticket = Ticket::where('ticket_id','=',$id)->first();
			
			if(($getuser_id != '0' && $get_ticket->created_by == $getuser_id) || ( $getuser_id == 0 && $get_ticket->email_id == $email_id))
			{
				$get_ticket_detail = array();
				if(TicketDetail::where('ticket_id','=',$id)->count())
				{
					$get_ticket_detail = TicketDetail::with('posted_user')->where('ticket_id','=',$id)->get();
				}
				else
				{
					return Lang::get("support/ticket.validation.notfound");
				}
				
				$getLastTicketinfo = TicketDetail::with('posted_user')->where('ticket_id',$id)->orderBy('id', 'desc')->get();
				
				// remove last reply.
				$getlastticket = [];
				foreach($getLastTicketinfo as $ticketinfo)
				{
					if($ticketinfo->posted_by == 'Admin')
					{
						break;
					}	
					else
					{
						$getlastticket[]	= 	$ticketinfo->id;	
					}					
				}
				
				return view('support/ticketstatus/ticketconversation',compact('get_ticket','get_ticket_detail','page','getlastticket'));	
			}
			else
			{
				return Lang::get("support/ticket.validation.notfound");
			}
		}
		else
		{
			return Lang::get("support/ticket.validation.notfound");
		}
	}
	
	public function getReplyTicketApi($request)
	{
		$this->checkPermission();
		$ticket_id = $request['ticket_id'];
		
		if($request['closeticket']==1)
		{
			Ticket::where('ticket_id','=',$ticket_id)->update(['status'=>'closed']);
		}
		
		$user = '';
		if(Auth::user ()!='')
			$user = Auth::user ()->id;
		
		$TicketDetail = new TicketDetail;
		if (Input::hasFile('attachment'))
		{
			$image = Input::file('attachment');
			$extension = $image->getClientOriginalExtension();
			$fileName = rand(11111,99999).'.'.$extension;
			Helpers::mediauploadpath('support','ticket',$image,'',$fileName); 
			$TicketDetail->image_type = $image->getMimeType();
			$TicketDetail->attach_details= $fileName;
		}	  
		$TicketDetail->description = $request['description'];
		$TicketDetail->ticket_id = $request['ticket_id'];
		$TicketDetail->posted_by  = 'User';
		$TicketDetail->postedby_id  = $user;
		$TicketDetail->save();
	}
	
	public function getticketdocumentApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$get_ticket_info   = TicketDetail::with('posted_user')->where('id','=',$id)->first();
		$file = Helpers::amazon_server_get_file('support/image/ticket/',$get_ticket_info->attach_details);
		return (new Responseobj ( $file, 200 ))->header ( 'Content-Type', $get_ticket_info->image_type);
	}
	
	public function checkPermission()
	{
		$dbconnection = new DBConnectionController();	
		View::share ( 'checkpermission', $dbconnection );
	}
	
	public static function ticketName($ticket_id)
	{
		return Ticket::where('ticket_id','=',$ticket_id)->first()->name;
	}

	public function getTicketStatus($id)
	{
		$get_ticket_stat = Ticket::where('created_by',$id)->orderBy('id', 'desc')->take(5)->select('ticket_id','assigneddate','updated_at')->get()->toArray();

		$resultArray = array_map( function($value) { return (array)$value; }, $get_ticket_stat );

		
		return $resultArray;

	}

	public function getTicketDetails($ticket_id,$ticket_upd_date,$user_id)
	{
		$ticket_stat = Ticket::where('ticket_id',$ticket_id)->pluck('status')->first();

		
		if($ticket_stat == 'Closed')
		{	

			$ticket_comment = TicketDetail::where('ticket_id', $ticket_id)->latest()->where('postedby_id','!=',$user_id)->select('description','created_at')->first();

			if($ticket_comment != null)
			{
				$return_data="This issue has been marked closed on ".$ticket_comment['created_at']." with the following comments: ".$ticket_comment['description'].". <br> <br> Is there something more I can help you with? / Do you want to know more?";
			}else{
				$return_data="This issue has been marked closed on ".$ticket_upd_date.".<br> <br> Is there something more I can help you with? / Do you want to know more?";
			}
		

		}
		else if($ticket_stat == 'Open'){

			$ticket_comment = TicketDetail::where('ticket_id', $ticket_id)->latest()->where('postedby_id','!=',$user_id)->select('description','created_at')->first();

			if(!empty($ticket_comment)){

				$return_data="This issue has been marked closed on ".$ticket_comment['created_at']." with the following comments: ".$ticket_comment['description'].".<br> <br> Is there something more I can help you with? / Do you want to know more?";
			}elseif(empty($ticket_comment)){

				$return_data="This issue is still being worked on. I will give you an update once the issue is closed. Comments: No Comments. <br> <br> Is there something more I can help you with? / Do you want to know more?";
			}
		}
		return $return_data;

	}

	public function getTicketState($id)
	{

		$get_ticket_stat = Ticket::on('responsive')->with('ticket_detail')->where('created_by',$id)->latest()->get()->toArray();

		$get_ticket_stat = Ticket::with('ticket_detail')->where('created_by',$id)->latest()->get()->toArray();
		
		$resultArray = array_map( function($value) { return (array)$value; }, $get_ticket_stat );

		return $resultArray;

	}

	public function getClaimStatus($id)
    {
    	$db = new DBConnectionController();
        $db->connectPracticeDB(Session::get('practice_dbid'));

        $get_ticket_stat = ClaimInfoV1::where('created_by',$id)->orderBy('id', 'desc')->select('claim_number','status','updated_at')->get()->toArray();

        $resultArray = array_map( function($value) { return (array)$value; }, $get_ticket_stat );
        
        return $resultArray;
    }

    public function getClaimNumber($id)
    {
    	$db = new DBConnectionController();
        $db->connectPracticeDB(Session::get('practice_dbid'));

        $paymentV1 = new PaymentV1ApiController();

    	$get_ticket_stat = ClaimInfoV1::where('claim_number', $id)->get()->toArray();

		
    	if(count($get_ticket_stat) > 0){

    		$resultData = $paymentV1->getClaimsFinDetails($get_ticket_stat[0]['id'], $get_ticket_stat[0]['total_charge']);

    		$return = "This claim status ".$get_ticket_stat[0]['status']." With AR Balance ". $resultData['balance_amt']. ".<br> <br> Is there something more I can help you with? / Do you want to know more?";	
    	}else if(count($get_ticket_stat) == 0){
    	
    		$return = "Claim number " .$id. " does not exist .<br> <br> Is there something more I can help you with? / Do you want to know more?";	
    	}

        return $return;

    }
		
}