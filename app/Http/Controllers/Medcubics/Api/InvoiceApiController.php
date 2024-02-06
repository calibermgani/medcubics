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
use App\Models\Medcubics\Practice as Practice;
use App\Models\Invoice\McInvoice as McInvoice;
use App\Models\Invoice\McInvoiceBillto as McInvoiceBillto;
use App\Models\Invoice\McInvoiceProd as McInvoiceProd;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use App\Models\Support\AssignTicketHistory as AssignTicketHistory;
use App\Models\EmailTemplate;
use Lang;

class InvoiceApiController extends Controller 
{
	/*** Create new invoice form start ***/
	public function getIndexApi() {
		$invoicelist = McInvoice::get()->toArray();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('invoicelist')));		
	}
	public function getCreateApi()
	{
		$practicelist = Practice::pluck('practice_name','id')->all();
		// $assigneduserlist = Users::where('id','!=','1')->where('user_type','Medcubics')->pluck('name','id');
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practicelist')));		
	}
	/*** Create new invoice form end ***/
	
	/*** Store invoice details start ***/
	public function postInvoiceApi($request='')
	{
		// $request = Request::all();
		$practice = Practice::where('id',$request['practice_id'])->first();
		// dd($practice);
		$invoice = McInvoice::orderBy('invoice_no', 'desc')->first();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"), 'data'=>compact('practice','invoice')));
	}
	/*** Store invoice details end ***/

	public function storeInvoiceApi($request='')
	{
		// if($request == '')
		// $request = Request::all();
		// $practice_users = Users::where('customer_id',$request['practice_id'])->get();
		$practice = Practice::where('id',$request['practice_id'])->first()->toArray();
		$invoice = McInvoice::where('invoice_no',$request['invoice_no'])->first();
		if(!empty($invoice)){}
		else{
			$mc_invoice	= McInvoice::create($request);
			$user 		= Auth::user ()->id;
			$mc_invoice->created_by = $user;
			$mc_invoice->invoice_no = $request['invoice_no'];
			$mc_invoice->header = "images/header.png";
			$mc_invoice->practice_id = $request['practice_id'];
			$mc_invoice->invoice_date = date('Y-m-d', strtotime($request['invoice_date']));
			$period = explode("-",$request['invoice_period']);
			// dd($period);
			$mc_invoice->invoice_start_date = date('Y-m-d', strtotime($period[0]));
			$mc_invoice->invoice_end_date = date('Y-m-d', strtotime($period[1]));
			$mc_invoice->invoice_amt = $request['due_amount'];
			$mc_invoice->previous_amt_due = $request['previous_amount'];
			$mc_invoice->total_amt = $request['total_amount'];
			$mc_invoice->save ();

			$mc_invoice_billto	= McInvoiceBillto::create($practice);
			$mc_invoice_billto->created_by = $user;
			$mc_invoice_billto->practice_id = $practice['id'];
			$mc_invoice_billto->contact_name =$practice['practice_name'];
			$mc_invoice_billto->street_1 =$practice['mail_add_1'];
			$mc_invoice_billto->street_2 =$practice['mail_add_2'];
			$mc_invoice_billto->city =$practice['mail_city'];
			$mc_invoice_billto->state =$practice['mail_state'];
			$mc_invoice_billto->zip_4 =$practice['mail_zip4'];
			$mc_invoice_billto->zip_5 =$practice['mail_zip5'];
			$mc_invoice_billto->contact_no =$practice['phone'];
			$mc_invoice_billto->mobile_no =$practice['phoneext'];
			$mc_invoice_billto->save ();

			$invoice = McInvoice::where('invoice_no',$request['invoice_no'])->first();
			
			for ($i=0; $i < count($request['product']); $i++) {
				$mc_invoice_prod	= McInvoiceProd::create($practice);
				$mc_invoice_prod->created_by = $user;
				$mc_invoice_prod->invoice_id = $invoice->id;
				$mc_invoice_prod->product_start_date = date('Y-m-d', strtotime($request['start_date'][$i]));
				$mc_invoice_prod->product_end_date = date('Y-m-d', strtotime($request['end_date'][$i]));
				$mc_invoice_prod->description = $request['product'][$i];
				$mc_invoice_prod->unit_price = $request['units'][$i];
				$mc_invoice_prod->quantity = $request['quantity'][$i];
				$mc_invoice_prod->total_price = $request['total'][$i];
				$mc_invoice_prod->save ();
			}
		}
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg")));
	}
}
