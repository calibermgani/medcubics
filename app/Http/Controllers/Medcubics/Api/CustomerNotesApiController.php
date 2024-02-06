<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use Lang;
use App\Models\Medcubics\Customernotes as Customernotes;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Medcubics\Customer as Customer;
use App\Http\Helpers\Helpers as Helpers;

class CustomerNotesApiController extends Controller 
{
	/********************** Start Display a listing of the customer notes ***********************************/
	public function getIndexApi($cust_id,$export='')
	{
		$cust_id 		= Helpers::getEncodeAndDecodeOfId($cust_id,'decode');
		if(Customer::where('id', $cust_id )->count())
        {
			$notes 		= Customernotes::with('user','userupdate')->orderBy('id','DESC')->get();
			$customer 	= Customer::where('id',$cust_id)->first();
			$tabs 		= "yes";
			if($export != "")
			{
				
				$exportparam 	= 	array(
									'filename'		=>	'Customer Notes',
									'heading'		=>	'Customer Notes',
									'fields' 		=>	array(
													'user' 		=>	array('table'=>'user' ,	'column' => 'name' ,	'label' => 'User Name'),
													'title' 	=> 'Title',
													'content' 	=> 'Content',
														)
									);
				$callexport 	= new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $notes, $export);
			}
			$customer->encid 		= Helpers::getEncodeAndDecodeOfId($customer->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customer','notes','tabs')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>'null'));
		}
	}
	/********************** End Display a listing of the customer notes ***********************************/

	/********************** Start Display the customer notes create page ***********************************/
	public function getCreateApi($cust_id)
	{	
		$cust_id 		= Helpers::getEncodeAndDecodeOfId($cust_id,'decode');
        if(Customer::where('id', $cust_id )->count())
        {
			$customer = customer::where('id', $cust_id)->first();
			$tabs = "yes";
			$customer->id 		= Helpers::getEncodeAndDecodeOfId($customer->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customer','tabs')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>'null'));
		}
	}
	/********************** End Display the customer notes create page ***********************************/

	/********************** Start customer notes added process ***********************************/
	public function getStoreApi($cust_id, $request='')
	{		
		if($request == '')
			$request = Request::all();
		$request['cust_id']		= Helpers::getEncodeAndDecodeOfId($request['cust_id'],'decode');
		$validator = Validator::make($request, customernotes::$rules, customernotes::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$customernotes 	= Customernotes::create(Request::all());
			$user 			= Auth::user ()->id;
			$customernotes->created_by = $user;
			$customernotes->save ();
			$customernotes->id		= Helpers::getEncodeAndDecodeOfId($customernotes->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$customernotes->id));
		}
	}
	/********************** End customer notes added process ***********************************/

	/********************** Start customer notes edit page display ***********************************/
	public function getEditApi($cust_id, $id)
	{
		$id		 	= 	Helpers::getEncodeAndDecodeOfId($id,'decode');
		$cust_id 	= 	Helpers::getEncodeAndDecodeOfId($cust_id,'decode');
		if(Customer::where('id', $cust_id )->count())
		{
			if(Customernotes::where('id', $id )->count())
			{
				$customer 		= Customer::with('user')->where('id',$cust_id)->first();
				$customernotes 	= Customernotes::find($id);
				$tabs 			= "yes";
				$customer->id		 	= 	Helpers::getEncodeAndDecodeOfId($customer->id,'encode');
				$customernotes->id		= 	Helpers::getEncodeAndDecodeOfId($customernotes->id,'encode');
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customer','customernotes','tabs')));
            }
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>'null'));
			}
        }
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/********************** End customer notes edit page display ***********************************/

	/********************** Start customer notes update process ***********************************/
	public function getUpdateApi($cust_id, $id, $request='')
	{
		$id		 	= 	Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '')
			$request = Request::all();	
		$request['cust_id']		= Helpers::getEncodeAndDecodeOfId($request['cust_id'],'decode');
		$validator = Validator::make($request, Customernotes::$rules, Customernotes::messages());
		if($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$customernotes 				= Customernotes::find($id);
			$customernotes->update($request);
			$user 						= Auth::user ()->id;
			$customernotes->updated_by 	= $user;
			$customernotes->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
		}
	}
	/********************** End customer notes update process ***********************************/

	/********************** Start customer notes deleted process ***********************************/
	public function getDeleteApi($type,$id)
	{
		$id		 	= 	Helpers::getEncodeAndDecodeOfId($id,'decode');
		Customernotes::Where('id',$id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}
	/********************** End customer notes deleted process ***********************************/
	
	function __destruct() 
	{
    }
}
