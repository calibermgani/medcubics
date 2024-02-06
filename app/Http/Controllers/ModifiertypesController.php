<?php namespace App\Http\Controllers;
use Request;
use Input;
use Redirect;
use Auth;
use View;

class ModifiertypesController extends Api\ModifiertypesApiController {

	public function __construct() {      
      
       View::share ( 'heading', 'Modifiers' );  
	   View::share ( 'selected_tab', 'modifiers' ); 
	   View::share( 'heading_icon', 'anchor');
    }  
	
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$modifierstype 		= 	$api_response_data->data->modifierstype;
		
		return view ( 'practice/modifier/modifiertypes/modifiertypes', compact ( 'modifierstype') );
		
	}

	

}
