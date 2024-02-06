<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Roles as Roles;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Redirect;
use Lang;

class RoleApiController extends Controller 
{
	/*** Start to Medcubics Role Listing & Export  ***/
	public function getIndexApi($export = "")
	{
		$roles = Roles::with('created_user','updated_user')->where('role_type', 'Medcubics')->where('id', '<>', 1)->where('status', '<>', 'Inactive')->orderBy('id', 'Asc')->get();
        if($export != "")
		{
			$exportparam = array(
					'filename'	=> 'Role',
					'heading'	=> 'Role list',
					'fields'	=> array(
				'role_name'		=> 'Role Name',
				'role_type'		=> 'Role Type',
				'status'		=> 'Status',
				'created_by'	=>	array('table'=>'created_user' ,	'column' => 'short_name' ,	'label' => 'Created By'),
				'updated_by' 	=>	array('table'=>'updated_user' ,	'column' => 'short_name' ,	'label' => 'Updated By'),
								));
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $roles, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('roles')));
	}
	/*** End to Medcubics Role Listing & Export  ***/
       
	/*** Start to Practice Role Listing & Export  ***/	
	public function getPracticePermissionApi($export = "")
	{
		$roles = Roles::with('created_user','updated_user')->where('role_type', 'Practice')->where('id', '<>', 1)->where('status', '<>', 'Inactive')->orderBy('id', 'Asc')->get();
		if($export != "")
		{
			$exportparam = array(
				'filename'	=> 'Role',
				'heading'	=> 'Role list',
				'fields'	=> array(
					'role_name'		=> 'Role Name',
					'role_type'		=> 'Role Type',
					'status'		=> 'Status',
					'created_by' 	=>	array('table'=>'created_user' ,	'column' => 'name' ,	'label' => 'Created By'),
					'updated_by' 	=>	array('table'=>'updated_user' ,	'column' => 'name' ,	'label' => 'Updated By'),
				));
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $roles, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('roles')));
	}
	/*** End to Practice Role Listing & Export  ***/
      
	/*** Start to Create the Role	 ***/	
    public function getCreateApi()
	{
		$roles = array();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('roles')));
	}
	/*** End to Create the Role	 ***/
    
	/*** Start to Store the Role ***/    
    public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();

		if($request['role_type']=='Medcubics')
			$validate_roles    = Roles::$rules+array('role_name'=>'required|not_in:Super admin|unique:roles,role_name,Null,id,role_type,Medcubics,deleted_at,NULL');
		if($request['role_type']=='Practice')
			$validate_roles    = Roles::$rules+array('role_name'=>'required|not_in:Super admin|unique:roles,role_name,Null,id,role_type,Practice,deleted_at,NULL');
		$validator = Validator::make($request, $validate_roles, Roles::messages());
        if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{		
			$roles = Roles::create($request);
			$user = Auth::user ()->id;
			$roles->created_by = $user;
			$roles->deleted_at = Null;
			$roles->save ();
			$insertedId = Helpers::getEncodeAndDecodeOfId($roles->id,'encode'); 
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$insertedId));					
		}
	}
	/*** End to Store the Role	 ***/
	
	/*** Start to Show the Role	 ***/	
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Roles::where('id', $id )->count())
		{
			$roles = Roles::with('created_user','updated_user')->where('id',$id)->first(); 
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('roles')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		}
	}
	/*** End to Show the Role	 ***/
    
	/*** Start to Edit the Role	 ***/	
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Roles::where('id', $id )->count())
		{
			$roles = Roles::findOrFail($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('roles')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		}
	}
	/*** End to Edit the Role	 ***/
    
	/*** Start to Update the Role	 ***/	
	public function getUpdateApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '')
			$request = Request::all();
		if($request['role_type']=='Medcubics')
			$validate_roles    = Roles::$rules+array('role_name'=>'required|not_in:super admin,Super Admin,Super admin|unique:roles,role_name,'.$id.',id,role_type,Medcubics,deleted_at,NULL');
		if($request['role_type']=='Practice')
			$validate_roles    = Roles::$rules+array('role_name'=>'required|not_in:super admin,Super Admin,Super admin|unique:roles,role_name,'.$id.',id,role_type,Practice,deleted_at,NULL');
        $validator = Validator::make($request, $validate_roles, Roles::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{		
			$roles = Roles::findOrFail($id);
			$roles->update($request);
			$user  = Auth::user ()->id;
			$roles->updated_by = $user;
			$roles->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
		}
	}
    /*** End to Update the Role	 ***/
	
	/*** Start to Destory the Role	 ***/	
	public function getDestroyApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		Roles::find($id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	 
	}
    /*** End to Destory the Role	***/
    
    function __destruct() 
	{
    }    
}
