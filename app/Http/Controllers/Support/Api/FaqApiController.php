<?php namespace App\Http\Controllers\Support\api;

use Response;
use Request;
use Auth;
use DB;
use View;
use Input;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Faq as FAQ;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class FaqApiController extends Controller {
	public function getIndexApi($export = '')
	{
		$request = Request::all();
		$search_keyword = @$request["search_key"];
		$category = @$request["search_category"];
		$query = FAQ::where('status','Active');
		if(Request::ajax())
        {
			if($category != '')
				$query->where('category',$category);

			if($search_keyword != '')
				$query->whereRaw('(question LIKE "%'.$search_keyword.'%" or answer LIKE "%'.$search_keyword.'%")');
		}
		$faq_category_arr = $query->groupBy('category')->orderBy('category','asc')->get();
		$this->checkPermission();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('faq_category_arr','search_keyword')));
	}
	
	public function checkPermission()
	{
		$dbconnection = new DBConnectionController();	
		View::share ( 'checkpermission', $dbconnection );
	}	
}