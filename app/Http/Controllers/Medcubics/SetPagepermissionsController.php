<?php namespace App\Http\Controllers\Medcubics;

use Request; 
use Redirect;
use Session;
use View;
use Config;

class SetPagepermissionsController extends Api\SetPagepermissionsApiController {

    public function __construct() 
    {      
        View::share ( 'heading', 'Roles' );   
        View::share ( 'selected_tab', 'admin/medcubicsrole' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.role'));
    }  
    
    public function edit($id)
    {		
        $api_response = $this->getEditApi($id);
        $api_response_data = $api_response->getData();        
        $role_name = $api_response_data->data->role_name;
        $menus = $api_response_data->data->menus;
        $modules = $api_response_data->data->module;        
	   $setpagepermissions = $api_response_data->data->setpagepermissions;
        return view('admin/setpagepermissions/edit', compact('id','setpagepermissions','role_name','menus', 'modules'));
    }
    public function update($id, Request $request)
    {        
        $api_response = $this->getUpdateApi($id, Request::all());
        $api_response_data = $api_response->getData();
        if($api_response_data->status == 'success')
        {
            return Redirect::to('admin/practicerole')->with('success',$api_response_data->message);
        }
        else
        {
            return Redirect::to('admin/practicerole')->with('error',$api_response_data->message);
        }	
    }
}
