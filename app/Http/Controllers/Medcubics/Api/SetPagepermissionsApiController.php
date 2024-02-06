<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Roles as Roles;
use App\Models\Medcubics\PagePermissions as PagePermissions;
use App\Models\Medcubics\SetPagepermissions as SetPagepermissions;
use Input;
use File;
use Auth;
use Response;
use Request;
use Validator;
use Schema;
use DB;
use Lang;
use App\Http\Helpers\Helpers as Helpers;

class SetPagepermissionsApiController extends Controller {
	public function getEditApi($id)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$role_name = Roles::where('role_type','practice')->where('id',$id)->pluck('role_name')->first(); 
        $module = PagePermissions::groupby('module')->orderBy('menu','ASC')->get();        
		$menus = PagePermissions::groupby('menu')->orderBy('menu','ASC')->get();
		$setpagepermissions = SetPagepermissions::where('role_id',$id)->first();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('id','role_name','menus','setpagepermissions', 'module')));
	}
	
	public function getUpdateApi($id, $request='')
	{		
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '')
		$request = Request::all();
		if((Roles::where('id', $id )->count()) == 0)
		{
                    return Response::json(array('status'=>'error', 'message'=>'Invalid Id','data'=>''));	
                }
		else
                {	
                    $page_permission_id = '';
                    SetPagepermissions::where('role_id',$id)->delete();	 
                    $numItems = count($request);
                    $i = 0;				
                    unset($request['_method']);				
                    unset($request['_token']);
                    unset($request['select_all']);
                    unset($request['permission_menu']);
                    unset($request['permission_module']);                   
                    foreach($request as $key=>$val){								
                            $page_permission_values = explode('|',$key);
                            $page_id = isset($page_permission_values[1])?$page_permission_values[1]:"";
                            $page_permission_id_val = Helpers::getEncodeAndDecodeOfId($page_id,'decode');                                                     
                            if(++$i === $numItems)
                                    $page_permission_id .= $page_permission_id_val;
                            elseif(count($page_permission_values)>0)
                                    $page_permission_id .= $page_permission_id_val.',';
                            
                    }	                                 
                    $data['role_id'] = $id;
                    $data['page_permission_id'] = $page_permission_id;					
                    SetPagepermissions::create($data);				
                    return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
                }
	}
}
