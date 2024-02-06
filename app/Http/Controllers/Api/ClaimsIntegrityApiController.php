<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cpt as Cpt;
use App\Models\Medcubics\Cpt as CptMaster;
use App\Models\Pos as Pos;
use App\Models\Favouritecpts;
use App\Models\Document as Document;
use App\Models\Medcubics\IdQualifier as IdQualifier;
use App\Models\MultiFeeschedule as MultiFeeschedule;
use App\Models\Modifier as Modifier;
use App\Models\Practice as Practice;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use App;
use Excel;
use App\Http\Helpers\Helpers as Helpers;
use Lang;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class ClaimsIntegrityApiController extends Controller 
{
	/*** Cpt lists page Starts ***/
	public function getIndexApi($export = "")
	{	
		$claim_qry = ClaimInfoV1::where('claim_info_v1.id', '<>', 0)->select('claim_info_v1.id','claim_info_v1.claim_number','claim_cpt_info_v1.cptCount','claim_tx_desc_v1.claimTxnCount','claim_cpt_tx_desc_v1.claimCptTxnCount',DB::raw('(CASE WHEN claim_cpt_info_v1.cptCount * claim_tx_desc_v1.claimTxnCount = claim_cpt_tx_desc_v1.claimCptTxnCount THEN 1 ELSE 0 END) AS is_user'),'claim_tx_desc_v1.claimTXType');
       	 $claim_qry->selectRaw("(claim_cpt_info_v1.cptCount * claim_tx_desc_v1.claimTxnCount) as new_claim_mark");
        
        $claim_qry->leftjoin(DB::raw("(SELECT
            claim_cpt_info_v1.id,claim_cpt_info_v1.claim_id,count(claim_cpt_info_v1.claim_id) as cptCount
         FROM claim_cpt_info_v1
         WHERE claim_cpt_info_v1.deleted_at IS NULL AND claim_cpt_info_v1.id IN (SELECT claim_cpt_info_v1.id FROM claim_cpt_info_v1 GROUP BY claim_cpt_info_v1.claim_id) GROUP BY claim_cpt_info_v1.claim_id
         ) as claim_cpt_info_v1"), function($join) {
           $join->on('claim_cpt_info_v1.claim_id', '=', 'claim_info_v1.id');
       });
        
         $claim_qry->leftjoin(DB::raw("(SELECT
            claim_tx_desc_v1.id,claim_tx_desc_v1.claim_id,count(claim_tx_desc_v1.claim_id) as claimTxnCount,sum( CASE transaction_type WHEN 'Edit Charge' THEN 1 ELSE 0 END) AS claimTXType
         FROM claim_tx_desc_v1
         WHERE claim_tx_desc_v1.deleted_at IS NULL AND claim_tx_desc_v1.id IN (SELECT claim_tx_desc_v1.id FROM claim_tx_desc_v1 GROUP BY claim_tx_desc_v1.claim_id) GROUP BY claim_tx_desc_v1.claim_id
         ) as claim_tx_desc_v1"), function($join) {
           $join->on('claim_tx_desc_v1.claim_id', '=', 'claim_info_v1.id');
       });
        
        $claim_qry->leftjoin(DB::raw("(SELECT
            claim_cpt_tx_desc_v1.id,claim_cpt_tx_desc_v1.claim_id,count(claim_cpt_tx_desc_v1.claim_id) as claimCptTxnCount
         FROM claim_cpt_tx_desc_v1
         WHERE claim_cpt_tx_desc_v1.deleted_at IS NULL AND claim_cpt_tx_desc_v1.id IN (SELECT claim_cpt_tx_desc_v1.id FROM claim_cpt_tx_desc_v1 GROUP BY claim_cpt_tx_desc_v1.claim_id) GROUP BY claim_cpt_tx_desc_v1.claim_id
         ) as claim_cpt_tx_desc_v1"), function($join) {
           $join->on('claim_cpt_tx_desc_v1.claim_id', '=', 'claim_info_v1.id');
       });
        $claim_qry->having('is_user', '!=', 1);       
        $integrity = $claim_qry->get()->toArray();   
        $mismatchedclaims = $integrity;//dd($mismatchedclaims);
		    return Response::json(array('status' => 'success', 'message' => null,'data'=>compact('mismatchedclaims')));	
		
	}
}