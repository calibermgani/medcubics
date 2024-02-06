<?php 
namespace App\Http\Controllers\Medcubics\Api;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Roles as Roles;
use App\Models\Medcubics\AdminPagePermissions as AdminPagePermissions;
use App\Models\Medcubics\SetAdminPagePermissions as SetAdminPagePermissions;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Redirect;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class AdminpermissionApiController extends Controller {
	
        public function getCreateApi($id)
        {
			$id 		= Helpers::getEncodeAndDecodeOfId($id,'decode');
            if(Roles::where('id', $id)->count()>0) {
                $setadminpagepermissions = SetAdminPagePermissions::where('role_id', $id)->first();
                $roles = Roles::where('id', $id)->first();
                $menus = AdminPagePermissions::groupby('menu')->orderBy('menu','ASC')->get();
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('setadminpagepermissions','roles', 'menus')));
            }
            else
            {
                return Response::json(array('status'=>'error', 'message'=>"No Roles found",'data'=>null));
            }
        }
        
        public function getStoreApi($request='')
        {
            if($request == '')
		$request = Request::all();
		
                $role_id = $request['role_id'];				
                $page_permission_id = '';
                $numItems = count($request);
                $i = 0;
                foreach($request as $key=>$val){					
                        $page_permission_values = explode('_',$key);					
                        if(count($page_permission_values) ==3)   {					
                        $menu = $page_permission_values[0];
                        $submenu = $page_permission_values[1];
                        $title = $page_permission_values[2];										
                        $pagepermission_details = AdminPagePermissions::where('menu', $menu )->where('submenu', $submenu)->where('title',$title)->first();					
                      
                        if(++$i === $numItems)
                                $page_permission_id .= $pagepermission_details->id;
                        else
                                $page_permission_id .= $pagepermission_details->id.','; 
                        }
                }	
                
                $data['role_id'] = $role_id;
                $data['page_permission_id'] = $page_permission_id;
                
                
               if(SetAdminPagePermissions::where('role_id',$role_id)->count()>0){
                   SetAdminPagePermissions::where('role_id', '=', $role_id)->update(['page_permission_id' => $page_permission_id]);
               } 
               else {
                    SetAdminPagePermissions::create($data);				
               }
                
                return Response::json(array('status'=>'success', 'message'=>'Role permissions updated successfully','data'=>''));					
        }
		
		function __destruct() 
		{
		}
        
}
