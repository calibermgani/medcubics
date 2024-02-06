<?php namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App;
use Auth;
use Illuminate\Http\Request;
use Response;
use Config;
class CommonWebcamApiController extends Controller {
	/**
	 *
	 * This function generates a csv document as Report and
	 * returns it as file download option.
	 *
	 * @author Sriram Balasubramanian
	 * @param array $param
	 * @param Object $data
	 */
        
   public function getwebcamimageApi($type = null)
	{
		if(is_null($type))
			$type = 'tmp_dir';
		$auth = Auth::user()->id;
		$filename = 'document_'.date('YmdHis') . '.jpg';		
		if(App::environment() ==  Config::get('siteconfigs.production.defult_production'))
	    	$path = $_SERVER['DOCUMENT_ROOT'].'/medcubic/';
        else
	     	$path = public_path().'/';
	    $file_path = $path.'/media/'.$type.'/'.$auth;	
	    $files = glob($file_path.'/*.jpg');
		foreach($files as $file) {
		    unlink($file);
		}		
		if (!file_exists($file_path)) {
			mkdir($file_path, 0777, true);
		}		
		$result = file_put_contents($file_path.'/'.$filename, file_get_contents('php://input') );
		if (!$result) {
			print "ERROR: Failed to write data to $filename, check permissions\n";
			exit();
		}
		$url =  url('/') .'/media/'.$type.'/'.$auth.'/'.$filename;
		return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('filename', 'url')));
	}
	
	public function savewebcameimageApi($type, $filename)
	{
		if(App::environment() ==  Config::get('siteconfigs.production.defult_production'))
	    	$path = $_SERVER['DOCUMENT_ROOT'].'/medcubic/';
        else
	    	$path = public_path().'/';	
	    $folder  = 	 $path.'/media/webcamdocuments/'.$type.'/'.Auth::user()->id;
		if(!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}
		$src = $path.'/media/'.$type.'/'.Auth::user()->id.'/'.$filename;
		$destination = $folder.'/'.$filename;
		if(copy($src, $destination)) {
				return Response::json(array('status'=>'success', 'message'=>'saved','data'=>''));
		} else {
                return Response::json(array('status'=>'error', 'message'=>'not saved','data'=>''));
		}
	}
}