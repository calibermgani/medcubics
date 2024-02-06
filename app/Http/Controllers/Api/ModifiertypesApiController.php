<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Modifier as Modifier;
use App\Models\Modifierstype as Modifierstype;
use Auth;
use Response;
use Request;
use Validator;
use Input;

class ModifiertypesApiController extends Controller {
	public function getIndexApi($export='')
	{
		//$modifierstype = Modifierstype::orderBy('id','DESC')->get();
		//$modifiersnewtype = DB::table('modifierstypes')->select('id')->get();
		///return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('modifierstype','modifiers')));		
		
		$modifierstype = Modifierstype::with('modifier')->whereHas('modifier', function($q){ $q->where('modifiers_type_id', '!=', '0');})->orderBy('id','ASC')->get();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('modifierstype')));		
	}
	
	function __destruct() 
	{
    }
	
}
