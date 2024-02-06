<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use Auth;
use View;
use Config;

class AdminpermissionController extends Api\AdminpermissionApiController {

public function __construct() {      

        View::share ( 'heading', 'Roles' );  
        View::share ( 'selected_tab', 'admin/medcubicsrole' );
        View::share( 'heading_icon', Config::get('cssconfigs.admin.role'));
    }  

        public function show($id)
        {
           $api_response = $this->getCreateApi($id);
            $api_response_data = $api_response->getData();
            
            if($api_response_data->status=='error')
            {
                return redirect('/admin/medcubicsrole')->with('message',$api_response_data->message);
            }
		
            $roles = $api_response_data->data->roles;
            $menus = $api_response_data->data->menus;
            $setadminpagepermissions = $api_response_data->data->setadminpagepermissions;

            return view('admin/role/setpermission',  compact('setadminpagepermissions','roles','adminpagepermissions', 'menus'));
        }
        
        public function store(Request $request)
        {
            $api_response = $this->getStoreApi($request::all());
            $api_response_data = $api_response->getData();

            if($api_response_data->status == 'success')
                {				
                    return Redirect::to('admin/medcubicsrole/')->with('success', $api_response_data->message);
                }
           	 
        }
        
        
}
