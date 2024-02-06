<?php namespace App\Http\Controllers\Medcubics\Api;

use Response;
use Validator;
use Request;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use DB;
use App;
use Lang;


class LogApiController extends Controller {
	
	/**** LOG List page start ***/
	public function getIndexApi($export='')
	{
		$log_path = storage_path('logs/');
		$log_data = array();
		$count = 0;
		foreach(glob($log_path."*.log") as $list){
			$filename = explode($log_path,$list);
			$log_data[$count]['file_name'] = $filename[1];
			$log_data[$count]['file_date'] = date("m/d/y",strtotime(substr($filename[1],8,10)));
			$log_data[$count]['file_created_time'] = date("m/d/y h:i:s",filectime($list));
			$log_data[$count]['file_last_update'] = date("m/d/y h:i:s",filemtime($list));
			$log_data[$count]['file_size'] = filesize($list);
			$count++;
		}
		return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('log_data')));
	}
	/**** LOG list page end ***/
	
	
	/**** LOG View page start ***/
	
	public function getViewLogApi($file_name){
		try{
			$log_path = storage_path('logs/');
			$file_content = @file($log_path.$file_name);
		} catch(Exception $e){
			$file_content = '';
		}
		return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('file_content')));
	}
	
	public function getViewErrorLogApi($file_name, $errType){

		try{
			$log_path = storage_path('logs/');
			$file_content = @file($log_path.$file_name);
			//$file_content = file_get_contents($log_path.$file_name);
			$resp = [];
			foreach ($file_content as $line_num => $line) {
				$lineTxt = htmlspecialchars($line);	
				if($errType == 'error') {
					if (($tmp = stristr($lineTxt, '.ERROR:')) !== false) {	//if (strpos($lineTxt, '.ERROR:') !== false) {
						if(trim(str_replace("\n", '', $lineTxt)) != ''){
							$lineTxt = str_ireplace('local.ERROR:', ' - ', $lineTxt);
							array_push($resp, $lineTxt);				
						}	
					}
				} else {					
					if (($tmp = stristr($lineTxt, '.ERROR:')) !== false || ($tmp = stristr($lineTxt, '.INFO:')) !== false) {	
						if(trim(str_replace("\n", '', $lineTxt)) != ''){
							$lineTxt = str_ireplace('local.ERROR:', ' - ', $lineTxt);
							array_push($resp, $lineTxt);				
						}	
					}
				}
			    //echo "<br>Line #<b>{$line_num}</b> : " .$lineTxt . "<br />\n";
			}
			arsort($resp);					// Get Log Details as Decending Order
			$resp = array_slice($resp, 0, 20);	// Get Last 20 Errors
			$file_content = $resp;
		} catch(Exception $e){
			$file_content = '';
		}
		return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('file_content')));
	}	

	
	
	/**** LOG View page end ***/
}
