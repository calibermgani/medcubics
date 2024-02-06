<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use App\Models\Provider;
use App\Models\PracticeManagecare as PracticeManagecare;
use Illuminate\Support\Collection;
use App\Models\Practice as Practice;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use Lang;
use App\Models\Document as Document;
use App\Http\Helpers\Helpers as Helpers;

class PracticeManagecareApiController extends Controller 
{

	/********************** Start Display a listing of the practice managecare ***********************************/
	public function getIndexApi($export='')
	{ 		
		$practice = Practice::first();	
		$temp = new Collection($practice);
		$prac_id = $temp['id'];
		$temp->pull('id');
		$practice_id = Helpers::getEncodeAndDecodeOfId($prac_id, 'encode');
		$temp->prepend($practice_id, 'id');
		$prac = $temp->all();
		$practice = json_decode(json_encode($prac), FALSE);
		// dd($practice);
		$managecares = Practicemanagecare::with('insurance','provider','provider.degrees','provider.provider_types')->orderBy('id','DESC')->get();
		if($export != "")
		{
			$exportparam 	= 	array(
								'filename'		=>	'Practice_Managecare',
								'heading'		=>	$practice->practice_name,
								'fields' 		=>	array(
													'Insurance'			=>	array('table'=>'insurance','column'=>'insurance_name','label'=>'Insurance'),
													'Provider Name'		=>	array('table'=>'provider','column'=>'provider_name','label'=>'Provider'),
													'enrollment'		=>	'Credential',
													'entitytype'		=>	'Entity Type',
													'effectivedate'		=>	'Effective Date',
													'terminationdate'	=>	'Termination Date',
													'feeschedule'		=>	'Fee Schedule',
													)
								);
			
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $managecares, $export);
		}
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('managecares','practice')));
	}
	/********************** End Display a listing of the practice managecare ***********************************/

	/********************** Start show practice managecare add page ***********************************/
	public function getCreateApi()
	{
		$practice 		= Practice::first();
		$temp = new Collection($practice);
		$prac_id = $temp['id'];
		$temp->pull('id');
		$practice_id = Helpers::getEncodeAndDecodeOfId($prac_id, 'encode');
		$temp->prepend($practice_id, 'id');
		$prac = $temp->all();
		$practice = json_decode(json_encode($prac), FALSE);	              
		$insurances 	= Insurance::pluck('insurance_name','id')->all();
        $insurance_id 	= '';
		$providers = Provider::getBillingAndRenderingProvider("yes");    
		$providers 			= array_flip($providers);  
		$providers	 		= array_flip(array_map(array($this,'myfunction'),$providers));
		$provider_id 	= '';
		// $practice->id = Helpers::getEncodeAndDecodeOfId($practice->id,'encode');
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practice','insurances','insurance_id','providers','provider_id')));
	}
	/********************** End show practice managecare add page ***********************************/
	function myfunction($num)
	{
	  return(Helpers::getEncodeAndDecodeOfId($num,'encode'));
	}
	/********************** Start store practice managecare process ***********************************/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();

		$request['providers_id'] = Helpers::getEncodeAndDecodeOfId($request['providers_id'],'decode');	
		$validator = Validator::make($request, Practicemanagecare::$rules, Practicemanagecare::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{								
			//************** changes start ***********//
			if($request['effectivedate'] != '') 
				$request['effectivedate']= date("Y-m-d",strtotime($request['effectivedate']));
            if($request['terminationdate'] != '') 
				$request['terminationdate']= date("Y-m-d",strtotime($request['terminationdate']));
			//************** changes end ***********//
				
			$practice = Practice::first();					
			$result = Practicemanagecare::create($request);
			if(isset($request['temp_doc_id']))
			{
				if($request['temp_doc_id']!="") Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $result->id,'temp_type_id' => '']);
			}
				
			$user = Auth::user ()->id;
			$result->created_by = $user;
			$result->save ();
			$result->id = Helpers::getEncodeAndDecodeOfId($result->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.managecare_create_msg"),'data'=>$result->id));
		}
	}
	/********************** End store practice managecare process ***********************************/
	
	/********************** Start show practice managecare edit page ***********************************/
	public function getEditApi($id)
	{
	    $practice 	= Practice::first();	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$managecare = Practicemanagecare::where('id',$id)->first();
				                
		if(Practicemanagecare::where('id', $id )->count()>0 && is_numeric($id))
		{
			$insurances 	= Insurance::pluck('insurance_name','id')->all();
            $insurance_id 	= $managecare->insurance_id;
			$providers 		= Provider::getBillingAndRenderingProvider();   
			$providers 		= array_flip($providers);  
		    $providers	 	= array_flip(array_map(array($this,'myfunction'),$providers));
			$provider_id 	= $managecare->providers_id;
			$provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'encode');
			// $managecare->id = Helpers::getEncodeAndDecodeOfId($managecare->id,'encode');
			//Encode ID for managecare
			$temp = new Collection($managecare);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$managecare = json_decode(json_encode($data), FALSE);
			//Encode ID for managecare
			//Encode ID for practice
			$temp = new Collection($practice);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$practice = json_decode(json_encode($data), FALSE);
			//Encode ID for practice

			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practice','managecare','insurances','insurance_id','providers','provider_id')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/********************** End show practice managecare edit page ***********************************/

	/********************** Start practice managecare update process ***********************************/
	public function getUpdateApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Practicemanagecare::where('id', $id )->count()>0 && is_numeric($id))
		{
			if($request == '')
				$request = Request::all();
			$request['providers_id'] = Helpers::getEncodeAndDecodeOfId($request['providers_id'],'decode');	
			$validator = Validator::make($request, Practicemanagecare::$rules, Practicemanagecare::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{			
				$managecare = Practicemanagecare::find($id);
				//************** changes start ***********//
				if($request['effectivedate'] != '')
					$request['effectivedate']= date("Y-m-d",strtotime($request['effectivedate']));
				if($request['terminationdate'] != '') 
					$request['terminationdate']= date("Y-m-d",strtotime($request['terminationdate']));
				//************** changes end ***********//
				$managecare->update($request);
				$user = Auth::user ()->id;
				$managecare->updated_by = $user;
				$managecare->save ();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.managecare_update_msg"),'data'=>''));					
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/********************** End practice managecare update process ***********************************/

	/********************** Start practice managecare delete process ***********************************/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Practicemanagecare::where('id', $id )->count()>0 && is_numeric($id))
		{
			Practicemanagecare::where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.managecare_delete_msg"),'data'=>''));	
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/********************** End practice managecare delete process ***********************************/
	
	/********************** Start practice managecare details page ***********************************/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Practicemanagecare::where('id', $id )->count()>0 && is_numeric($id))
		{
			$practice = Practice::first();
			$managedcare = Practicemanagecare::with('insurance','provider','provider.degrees')->where('id',$id)->first();
			//Encode ID for managedcare
			$temp = new Collection($managedcare);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$managedcare = json_decode(json_encode($data), FALSE);
			//Encode ID for managedcare
			//Encode ID for practice
			$temp = new Collection($practice);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$practice = json_decode(json_encode($data), FALSE);
			//Encode ID for practice
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('managedcare','practice')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/********************** End practice managecare details page ***********************************/
	
	 function __destruct() 
	{
    }

}
