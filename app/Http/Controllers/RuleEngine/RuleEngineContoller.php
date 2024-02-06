<?php namespace App\Http\Controllers\RuleEngine;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Request;
use Validator;
use DB;
use App\Models\CodesRuleEngine;
use App\Models\Code;
use App\Http\Helpers\Helpers as Helpers;

class RuleEngineContoller extends Controller {

	public function updateCodeRuleEngine(){
		$request = Request::all();
		
		foreach($request['code_type'] as $list){
			$dataArr['code_type'] = $list['code_type'];
			$dataArr['claim_status'] = $list['claim_status'];
			$dataArr['next_resp'] = $list['next_resp'];
			$dataArr['priority'] = $list['priority'];
			$dataArr['reason_type'] = $request['reason_type'];
			$dataArr['transactioncode_id'] = $request['code_id'];
			$engine = CodesRuleEngine::where('transactioncode_id',$request['code_id'])->where('code_type',$list['code_type'])->first();
			if(isset($engine) && !empty($engine)){
				$engine->update($dataArr);
			}
			else
				CodesRuleEngine::create($dataArr);
		}
		return 'success';
	}
	
	
	public function getRuleEngine($data = ''){
		$remarkCodes = '';
		$defaultTempData = [];
		
		$defaultTempData = [];
		if(isset($data) && !empty($data)){
			$data = call_user_func_array('array_merge', $data);
			$remarkCode = array_unique($data);
			
			$codesPrefix = ['CO', 'PR', 'OA', 'PI'];
			$temp = [];
			foreach($remarkCode as $codelist){
				$code = substr(trim($codelist), 0, 2);
				$codeValue = substr(trim($codelist), 2);
				$temp[$code.$codeValue] = $this->getCodeDetails($code, $codeValue);	
			}
			
			$highArr = $mediumArr = $lowArr = $tempData = [];
			$highCount = $mediumCount = $lowCount = $deniedCount = 0;
			foreach($temp as $key => $Arrlist){
				$priority = $Arrlist['priority'];
				$claim_status = $Arrlist['claim_status'];
				$next_resp = $Arrlist['next_resp'];
				$reason_type = $Arrlist['reason_type'];
				if($Arrlist['claim_status'] == 'Denied'){
					$deniedCount++;
				}
				switch ($priority) {
					case "High":
						$highCount++;
						$highArr[$key]['claim_status'] = $claim_status;
						$highArr[$key]['next_resp'] = $next_resp;
						$highArr[$key]['reason_type'] = $reason_type;
						break;
					case "Medium":
						$mediumCount++;
						$mediumArr[$key]['claim_status'] = $claim_status;
						$mediumArr[$key]['next_resp'] = $next_resp;
						$mediumArr[$key]['reason_type'] = $reason_type;
						break;
					case "Low":
						$lowCount++;
						$lowArr[$key]['claim_status'] = $claim_status;
						$lowArr[$key]['next_resp'] = $next_resp;
						$lowArr[$key]['reason_type'] = $reason_type;
						break;
					default:
						echo "default";
				}
			}
			if($highCount > 0){
				if($deniedCount > 1){ 
					$defaultTempData['claim_status'] = 'Denied';
					$defaultTempData['next_resp'] = 'Same';
					$defaultTempData['reason_type'] = 'Billing';
					return $defaultTempData;
				}
				if($highCount == 1){
					return call_user_func_array('array_merge', $highArr);
				}else{					
					$highArrFlip = array_map("array_flip", $highArr);
					$highArrSingle = call_user_func_array('array_merge', $highArrFlip);
					if(count($highArrSingle) == 3){
						return array_flip($highArrSingle);
					}else{
						$defaultTempData['claim_status'] = 'Denied';
						$defaultTempData['next_resp'] = 'Same';
						$defaultTempData['reason_type'] = 'Billing';
						return $defaultTempData;
					}	
				}
			}elseif($mediumCount > 0){
				if($deniedCount > 1){ 
					$defaultTempData['claim_status'] = 'Denied';
					$defaultTempData['next_resp'] = 'Same';
					$defaultTempData['reason_type'] = 'Billing';
					return $defaultTempData;
				}
				if($mediumCount == 1){
					return call_user_func_array('array_merge', $mediumArr);
				}else{					
					$mediumArrFlip = array_map("array_flip", $mediumArr);
					$mediumArrSingle = call_user_func_array('array_merge', $mediumArrFlip);
					if(count($mediumArrSingle) == 3){
						return array_flip($mediumArrSingle);
					}else{
						$defaultTempData['claim_status'] = 'Denied';
						$defaultTempData['next_resp'] = 'Same';
						$defaultTempData['reason_type'] = 'Billing';
						return $defaultTempData;
					}
				}
			}elseif($lowCount > 0){
				if($deniedCount > 1){ 
					$defaultTempData['claim_status'] = 'Denied';
					$defaultTempData['next_resp'] = 'Same';
					$defaultTempData['reason_type'] = 'Billing';
					return $defaultTempData;
				}
				if($lowCount == 1){
					return call_user_func_array('array_merge', $lowArr);
				}else{				
					$lowArrFlip = array_map("array_flip", $lowArr);
					$lowArrSingle = call_user_func_array('array_merge', $lowArrFlip);
					if(count($lowArrSingle) == 3){
						return array_flip($lowArrSingle);
					}else{
						$defaultTempData['claim_status'] = 'Denied';
						$defaultTempData['next_resp'] = 'Same';
						$defaultTempData['reason_type'] = 'Billing';
						return $defaultTempData;
					}
				}
			}
		}else{
			return $defaultTempData;
		}
	}
	
	public function getCodeDetails($codes, $codeValue){
		$codeDetails = CodesRuleEngine::where('transactioncode_id',$codeValue)->where('code_type',$codes)->get()->first();
		if(isset($codeDetails) && !empty($codeDetails)){ 
			$dataArr['claim_status'] = $codeDetails->claim_status;
			$dataArr['next_resp'] = $codeDetails->next_resp;
			$dataArr['priority'] = $codeDetails->priority;
			$dataArr['reason_type'] = $codeDetails->reason_type;
		}else{ 
			$dataArr['claim_status'] = 'Transfer';
			$dataArr['next_resp'] = 'Next';
			$dataArr['priority'] = 'Low';
			$dataArr['reason_type'] = 'Billing';
		}
		return $dataArr;
	}
	
	
	

}
