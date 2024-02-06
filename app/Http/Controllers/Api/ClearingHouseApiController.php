<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClearingHouse as ClearingHouse;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Response;
use Request;
use Validator;
use Lang;

class ClearingHouseApiController extends Controller {
	
	/*** EDI Listing and Export Start ***/
	public function getIndexApi($export = "")
	{
		$clearing_house = ClearingHouse::with('user','updated_user')->get();
		if($export != "")
        {
            $exportparam = 	array(
							'filename' 	=> 'edi',
							'heading' 	=> 'Edi List',
							'fields' 	=> array(
											'name'			=> 'Name',
											'enable_837'	=> 'Enable 837',
											'created_at' 	=> 'Created On',
											'updated_at'	=> 'Updated On',
											'created_by'    => array('table'=>'user' ,'column' => 'name' ,'label' => 'Created by'),
											'updated_by'    => array('table'=>'userupdate' ,'column' => 'name' ,'label' => 'Updated by'),
										)
							);
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $clearing_house, $export);
        }
        return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('clearing_house')));
	}
	/*** EDI Listing and Export End ***/
	
	/*** Store page  EDI start ***/
	public function getStoreApi()
	{
		$request 	= Request::all();
		$validate_rules = ClearingHouse::rules($request);
		 $validator 	= Validator::make($request, $validate_rules, ClearingHouse::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			if($request['status'] == 'Active')
			{
				$result = ClearingHouse::where('status','Active')->update(['status'=>'Inactive']);
			}
			$data = ClearingHouse::create($request);
			$user = Auth::user ()->id;
			$data->created_by = $user;
			$data->updated_at = "";
			$data->save();
			$data->id = Helpers::getEncodeAndDecodeOfId($data->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->id));
		}
	}
	/*** Store page  EDI end ***/
	
	/*** Show page  EDI start ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
        if(ClearingHouse::where('id', $id )->count())
        {
            $clearing_house = ClearingHouse::with('user')->where('id', $id )->first();
            return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>compact('clearing_house')));
        }
        else
        {
            return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
        }
	}
	/*** Show page  EDI end ***/
	
	/*** Edit page  EDI start ***/
	public function getEditApi($id)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id)) && ClearingHouse::where('id', $id)->count())
		{
			$clearing_house = ClearingHouse::findOrFail($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('clearing_house')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** Edit page  EDI end ***/
	
	/*** Update page  EDI start ***/
	public function getUpdateApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
        $request 	= Request::all();
		$validator 	= Validator::make($request, ClearingHouse::rules($request), ClearingHouse::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{
			if($request['status'] == 'Active')
			{
				$result = ClearingHouse::where('status','Active')->update(['status'=>'Inactive']);
			}
			$data = ClearingHouse::findOrFail($id);
			$data->update($request);
			$user = Auth::user()->id;
			$data->updated_by = $user;
			$data->save();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>$data->id));
		}
	}
	/*** Update page  EDI end ***/		
	
	/*** Destroy page  EDI start ***/		
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(ClearingHouse::where('id', $id )->count())
		{
			$result = ClearingHouse::find($id)->delete();
			if($result == 1)
			{
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.error_msg"),'data'=>''));
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** Destroy page  EDI End ***/		
	function __destruct() 
	{
    }

}