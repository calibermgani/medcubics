<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Models\ClaimChangeLog;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Redirect;
use Hash;
use Lang;
use DB;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Illuminate\Support\Collection;
use Config;

class UsersactivityApiController extends Controller {
	
	public function getIndexApi($export = "")
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$chargeslist 	= ClaimChangeLog::select("*",DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with('users_details','claim_details')->orderBy('created_at','DESC');
		$tabs 		= "yes";
	/*	if($export != "")
		{
			$exportparam 	= 	array(
								'filename'	=>	'Customer',
								'heading'	=>	'Customer',
								'fields' 	=>	array(
												'customer_name'		=> 'Customer Name',
												'customer_type'		=> 'Customer Type',
												'contact_person' 	=> 'Contact Person',
												'designation'       => 'Designation',
                                                'phone'             => 'Phone',
                                                'mobile'            => 'Cell phone',
												)
								);
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $chargeslist, $export);
		}*/
		$paginate_count = 20;
	    $claims_paginate = $chargeslist->paginate($paginate_count);
        $claims_array = @$claims_paginate->toArray();
        $pagination_prt = @$claims_paginate->render();  
        // $claim_id 			= 	$claims['claims']->pluck("claim_id")->first();
        //$total	 			= 	Claims::TotalTransfer($claim_id);
        //Pagination listing the detail
        if ($pagination_prt == '')
            $pagination_prt = '<ul class="pagination"><li class="disabled"><span>← Prev</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">Next →</a></li></ul>';
            $pagination = array('total' => $claims_array['total'], 'per_page' => $claims_array['per_page'], 'current_page' => $claims_array['current_page'], 'last_page' => $claims_array['last_page'], 'from' => $claims_array['from'], 'to' => $claims_array['to'], 'pagination_prt' => $pagination_prt);
        $chargeslist = @$claims_array; 
      	return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('chargeslist','tabs','pagination')));
	}
}
