<?php 
namespace App\Http\Controllers\practiceSettings;
use App\Http\Controllers\Controller;
use Auth;
use View;
use Session;
use Request;
use Config;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTWalletV1;
use App\Http\Controllers\Medcubics\Api\DBConnectionController;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Helpers\Helpers as Helpers;
use Carbon;
use Log;
use DB;
use App\Http\Controllers\Claims\ClaimControllerV1;
class chargeDeleteController extends Controller {

	public function __construct() {
        View::share('heading', 'Practice');
        View::share('selected_tab', 'Charge Delete');
        View::share('heading_icon', 'fa-medkit');
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$ClaimController  = new ClaimControllerV1();  
		View::share('selected_tab', 'Charge Delete');
		View::share('heading', 'Practice');
		View::share('heading_icon', 'fa-medkit');
        $search_fields_data = $ClaimController->generateSearchPageLoad('charge_delete');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];

        return view('practiceSettings/charge_delete', compact('pagination','search_fields','searchUserData'));
	}
	public function search() {
		$request = Request::all();
		$practice_timezone = Helpers::getPracticeTimeZone();  
		$charge = ClaimInfoV1::selectRaw('claim_info_v1.id, claim_info_v1.claim_number, claim_info_v1.facility_id, claim_info_v1.status, facilities.short_name')->leftJoin('facilities','facilities.id','=','claim_info_v1.facility_id')->where('claim_info_v1.claim_submit_count',0)
		->whereNotIn('claim_info_v1.status',['Rejection','Submitted','Paid','Denied'])
		->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= DATE('".Carbon\Carbon::today()->subDays(30)."')")
		->orderBy('claim_info_v1.id','desc');
		$header = [];
		if(isset($request['claim_no']) && !empty($request['claim_no'])){
			$charge->where('claim_info_v1.claim_number',$request['claim_no']);
			//$header['Claim Number'] = $request['claim_no'];
		}
		if(isset($request['status']) && !empty($request['status'])){
			$charge->whereIn('claim_info_v1.status',$request['status']);
			//$header['Status'] = $request['status'];
		} else{
			//$header['Status'] = 'All';
		}
		if(isset($request['charge_delete_transaction_date']) && !empty($request['charge_delete_transaction_date'])){
            $date = explode('-', $request['charge_delete_transaction_date']);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            $charge->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
        }

		$pagination = '';
        // Define for pagination count for per page
        $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
		
		// Get records per page
		$charges = $charge->paginate($paginate_count);
        $charges_pagination = $charges->toArray();
        
        // Pagination navigation
        $pagination_prt = $charges->render();
        
        // Default pagination if single page
        if ($pagination_prt == '')
            $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
        // To set pagination datas
        $pagination = array('total' => $charges_pagination['total'], 'per_page' => $charges_pagination['per_page'], 'current_page' => $charges_pagination['current_page'], 'last_page' => $charges_pagination['last_page'], 'from' => $charges_pagination['from'], 'to' => $charges_pagination['to'], 'pagination_prt' => $pagination_prt);
        
        // Separate data only
        $charges = $charges;	

        return view('practiceSettings/report', compact('charges','header','pagination'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy()
	{
		$request = Request::all();
		if(!empty($request['id'])){
			$claim_id = $request['id'];
			$claim_number = DB::table('claim_info_v1')->where('id', $claim_id)->pluck('claim_number')->first();
			$pmt = PMTClaimTXV1::where('claim_id',$claim_id)->get();
			try{
				DB::beginTransaction();
				if(!empty($pmt)){
					foreach($pmt as $p){
						$check_amt = PMTInfoV1::where('id',$p->payment_id)->select('amt_used')->first();
						
						if($p->pmt_type!='Refund'){
							Log::info('payment ID'.$p->payment_id.'update'.($check_amt->amt_used-$p->total_paid));
							$amt_used = $check_amt->amt_used-$p->total_paid;
						}
						else{
							Log::info('payment ID'.$p->payment_id.'update'.($check_amt->amt_used+$p->total_paid));
							$amt_used = $check_amt->amt_used+$p->total_paid;
						}
						PMTInfoV1::where('id',$p->payment_id)->update(['amt_used' => $amt_used]);

						//30-08-2019
						DB::table('pmt_unposted_notes')->where('pmt_unposted_notes.pmt_id', $p->payment_id)->delete();
						$pmt_wallet = new PMTWalletV1();
						$pmt_wallet->patient_id = $p->patient_id;
						$pmt_wallet->pmt_info_id = $p->payment_id;
						$pmt_wallet->tx_type = 'Credit';
						$pmt_wallet->wallet_Ref_Id = $p->claim_id;
						$pmt_wallet->amount = ($p->pmt_type!='Refund')?$p->total_paid:(-1)*$p->total_paid;
						$pmt_wallet->save();
					}
				}

				DB::table('claim_add_details_v1')->where('claim_id', $claim_id)->delete();
				DB::table('claim_anesthesia_v1')->where('id', $claim_id)->delete();
				DB::table('claim_tx_desc_v1')->  where('claim_id', $claim_id)->delete();
				DB::table('pmt_claim_tx_v1')->where('claim_id', $claim_id)->delete();
				DB::table('pmt_claim_fin_v1')->where('claim_id', $claim_id)->delete();
				DB::table('claim_info_v1')->where('id', $claim_id)->delete();
				
				//30-08-2019
				$cpt_others = DB::table('claim_cpt_others_adjustment_info_v1')->where('claim_id', $claim_id)->pluck('id')->all();
				if(!empty($cpt_others)){
					DB::table('pmt_adj_info_v1')->whereIn('pmt_adj_info_v1.adj_reason_id', $cpt_others)->delete();
				}

				DB::table('claim_cpt_others_adjustment_info_v1')->where('claim_id', $claim_id)->delete();
				DB::table('pmt_claim_cpt_fin_v1')->where('claim_id', $claim_id)->delete();
				DB::table('claim_cpt_tx_desc_v1')->  where('claim_id', $claim_id)->delete();
				DB::table('pmt_claim_cpt_tx_v1')->where('claim_id', $claim_id)->delete();
				DB::table('claim_edi_info_v1')->where('claim_id', $claim_id)->delete();
				DB::table('transmission_claim_details')->where('claim_id', $claim_id)->delete();
				
				$cpt_info = DB::table('claim_cpt_info_v1')->where('claim_id', $claim_id)->pluck('id')->all();
				if(!empty($cpt_info)){
					DB::table('claim_cpt_shaded_info_v1')->whereIn('claim_cpt_shaded_info_v1.claim_cpt_info_v1_id', $cpt_info)->delete();
				}
				DB::table('claim_cpt_info_v1')->where('claim_id', $claim_id)->delete();
				
				$transmission_cpt = DB::table('transmission_cpt_details')->where('transmission_claim_id', $claim_id)->pluck('edi_transmission_id')->all();
				if(!empty($transmission_cpt)){
					DB::table('edi_transmissions')->whereIn('edi_transmissions.id', $transmission_cpt)->delete();
				}
				
				DB::table('transmission_cpt_details')->where('transmission_claim_id', $claim_id)->delete();
				DB::table('patient_notes')->where('claim_id', $claim_id)->delete();
				DB::table('documents')->where('claim_id', $claim_id)->delete();
				DB::table('problem_lists')->where('claim_id', $claim_id)->delete();
				DB::commit();
				
				$current_date = date('Y-m-d');
	            $current_time = date('Y-m-d H:i:s');
	            $fp = fopen("../storage/logs/Charges/Charge_Delete_log_" . $current_date . ".txt", 'a+');
	            fwrite($fp, "\n Current Time => $current_time \n");
	            fwrite($fp, " Claim ID => " . $claim_id . " Deleted \r\n");
				return response()->json(['success'=> 1, 'message'=> "deleted successfully", 'claim_id'=> $claim_number]);
			
			} catch (Exception $e) {
				$current_date = date('Y-m-d');
	            $current_time = date('Y-m-d H:i:s');
	            $fp = fopen("../storage/logs/Charges/Charge_Delete_err_log_" . $current_date . ".txt", 'a+');
	            fwrite($fp, "\n Current Time => $current_time \n");
	            fwrite($fp, " Err Message => " . $e->getMessage() . " \r\n");
				DB::rollBack();
				return response()->json(['success'=> 0, 'message'=> "Claim Id Missing"]);
	        }
		} else{
			return response()->json(['success'=> 0, 'message'=> "Claim Id Missing"]);
		}
	}

}
