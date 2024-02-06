<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
//use File;
use Auth;
use Request;
use Response;
use Redirect;
use Lang;
use Session;
use Carbon\Carbon;

//use Illuminate\Support\Facades\Storage;
//use Illuminate\Support\Facades\File;
//use Illuminate\Http\Response;
use App\Models\ReportExport as ReportExport;

use Google\Cloud\Storage\StorageClient;
use League\Flysystem\Filesystem;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

if (!defined("DS"))
	DEFINE('DS', DIRECTORY_SEPARATOR); 

class AttachmentController extends Controller
{
    public $storage_disk = '';

    public function index() {
    	$storage_disk = 'gcs';
		$disk = Storage::disk('gcs');
        $bucket_name = "medcubics";

        // Create directory in cloud
		/*$directory = 'documents'.DS.'MED';
		$contents = 'TESTSTSTSTA';
		$result = Storage::disk('gcs')->makeDirectory($directory, 0775);
		$res = Storage::disk('gcs')->put($directory.DS.'file.txt', $contents);
		print_r($res);*/
		//$resp = $disk->makeDirectory($directory);
		$main_dir_arr = Storage::disk('gcs')->allDirectories();
        //echo "<pre>"; print_r($main_dir_arr);
        $filesL =  Storage::disk('gcs')->allFiles();
        //print_r($filesL); 

		$filename = $fileName = 'reports/40/demographic-sheet_01-20-20-00-35-08.xlsx';
		$url = $disk->url($fileName);

		dd($url);
		$exists = $disk->exists($fileName);
		$url = $disk->url($fileName);		
        $contents = $disk->get($fileName);

		//echo "<pre>"($exists."##".$url."##Content".$contents);
		ob_end_clean();      	
    	$org_filename = $fileName;
    	if($exists = $disk->exists($filename)) {
            $headers = [
	            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	            'Content-Disposition' => 'attachment; filename=' . 'demographic-sheet_01-20-20-00-35-08.xlsx',
	            'Cache-Control' => 'max-age=0',
	            'X-Accel-Buffering' => 'no',
	            'Cache-Control' => 'max-age=0',
	            'no-cache' => 'true',
	            'must-revalidate' => 'true'
	        ];
			return Storage::disk('gcs')->download($filename, 'demographic-sheet_01-20-20-00-35-08.xlsx', $headers);            
        } else {                                
            \Log::info("Error occured while download Report File unavailable ".$filename );
            $msg = "File not found !!!";
            dd($msg);
            return Redirect::to('home')->with('message', $msg);
        }
        exit;

        dd("sss");
        foreach ($filesL as $f) {
        	echo "<pre>"; var_dump($f);
        	//echo "<br>". $time = Storage::lastModified($f);
        	$fDet = pathinfo($f); 
        	echo "<pre>"; print_r($fDet);
		    //echo "<br> $filename size " . filesize($filename) . "\n";
		}


        dd();


        $chk_env_site = getenv('APP_ENV'); //Check files from production or local
        $default_view = \Config::get('siteconfigs.production.defult_production');
        if ($chk_env_site == $default_view) {
            $storage_disk = "s3_production";
            $bucket_name = "medcubicsproduction";
        } else {
            $storage_disk = "s3";
            $bucket_name = "medcubicslocal";
        }
        echo "<br>".$storage_disk."## ".$bucket_name;
		$store_domain = Storage::disk($storage_disk)->url('/');            
        $main_dir_arr = Storage::disk('s3')->directories();
        echo "<pre>"; print_r($main_dir_arr);
        
        $filesL =  Storage::disk('s3')->files('documents');

        foreach ($filesL as $f) {
        	echo "<pre>"; var_dump($f);
        	//echo "<br>". $time = Storage::lastModified($f);
        	$fDet = pathinfo($f); 
        	echo "<pre>"; print_r($fDet);
		    //echo "<br> $filename size " . filesize($filename) . "\n";
		}

        dd($filesL);
        echo "<br> Directory List";	
        dd($main_dir_arr);
       	echo "<pre>";  print_r($store_domain);
		echo "<pre>"; print_r($main_dir_arr);
		echo "<br>###############<br>";

		$directories = $disk->allFiles();
		echo "<br>Final";
		dd($directories);

		$fileName = 'first_job.txt';
		$exists = $disk->exists($fileName);
		$url = $disk->url($fileName);
		//$directory = 'SMP';
		//$resp = $disk->makeDirectory($directory);
		$resp = '';
		$disk->prepend($fileName, 'Prepended Text');
		$disk->append($fileName, 'Appended Text');

		$contents = $disk->get($fileName);

		dd($exists."##".$url."##".$resp."##Content".$contents);

		//dd($directories);
	}	

	public function __construct() {
		$this->logged_user_id = (Auth::user()) ? Auth::user()->id : 0;			
		$chk_env_site = getenv('APP_ENV');
        if ($chk_env_site == "production")
            $this->storage_disk = "gcs";        
        else
            $this->storage_disk = "local";
	} 

	/**
	$uploadFor = 'patient_import' / 'profile_picture' / 
	*/
	public function uploadFile($detailsArr = [], $uplodFor, $file){
		// baszed on upload for files needs to be handled
		if ($file->isValid()) {
			try {
				
				$resp = [];

				$path = $file->getRealPath();
	            $ofilename = $file->getClientOriginalName(); 
	            $fileext = $file->getClientOriginalExtension(); 

				$destPath = '';
				if($uplodFor == 'patient_import') {
					$uDir = md5(Auth::user()->customer_id);		
					$destPath = storage_path(DS.'Imports'.DS.$uDir);
					// $destPath = public_path().DS.'USERFILESATTACHMENTS' .DS. 'imports'. DS .$uDir;	
				} else if($uplodFor == 'user_profile'){
					$uDir = md5(Auth::user()->customer_id);		
					$destPath = storage_path('Profile'.DS.$uDir);
				} elseif($uplodFor == 'client_img') {
					$uDir = md5(Auth::user()->customer_id);		
					$destPath = storage_path('Profile'.DS.$uDir);
				}
				
				$targ_filename = isset($detailsArr['targ_filename']) ? $detailsArr['targ_filename'] : (time().'.'.$fileext) ; 

				// Check folder exists, if not create a new one.
				if(!File::exists($destPath)) {
					File::makeDirectory($destPath, $mode = 0777, true, true);
				}

				// Check file already exists
				if(File::exists($destPath.DS.$targ_filename)) {							
					File::delete($destPath.DS.$targ_filename);
				}			
				// Move the file with in that folder.
				if($file->move($destPath, $targ_filename )){

					$resp['status'] = 'success';
					$resp['filePath'] = $destPath;
					$resp['fileName'] = $targ_filename;
					$resp['orgFileName'] = $ofilename;
					$resp['fileExt'] = $fileext;

					/* No need to store into storage for now
					// Create directory in cloud
					$disk = Storage::disk('gcs');		
						
					$directory = $uDir . DS. 'profile';
					$resp = $disk->makeDirectory($directory);

					// Store file into cloud
					$store_file_val = file_get_contents($destPath.DS.$tmp_filename);
					Storage::disk('gcs')->put($directory.DS.$tmp_filename,  $store_file_val);
					*/
					return $resp;

				} else {
					$resp['status'] = 'error';
					$resp['filePath'] = $destPath;
					$resp['fileName'] = $targ_filename;
					$resp['orgFileName'] = $ofilename;
					$resp['fileExt'] = $fileext;
					return $resp;
					exit;
				}
			} catch(Exception $e) {
				\Log::info("Upload failed due to error. Msg: ".$e->getMessage() );
				$resp['status'] = 'error';
				return $resp;
			}

		} else {
			// Invalid File
			\Log::info("Upload failed due to Invalid File. Msg: ".$e->getMessage() );
			$resp['status'] = 'error';
			return $resp;
		}
	}


	public function getDownloadLink($recId, $type="patient_import", $details) {
		$resp = [];
		if($type == "patient_import") {
			$resDet =  PatientImports::where('id', $recId)->first();
			if (!empty($resDet) > 0) {
				$uDir = md5(Auth::user()->customer_id);
	            $fName = $resDet['file_name'];	            
	            $destPath = storage_path(DS.'Imports'.DS.$uDir);
            	$filename = $destPath. DS . $fName;
            	$org_filename = $resDet['org_filename'];
		    	if(File::exists($filename)) {
		    		$resp['status'] = 'success';
		    		$resp['file_link'] = $filename;
		    		$resp['org_filename'] = $org_filename;
		    	} else {
		    		$resp['status'] = 'error';
		    		$resp['err_msg'] = 'File not found';
		    	}
			} else {
				$resp['status'] = 'error';
		    	$resp['err_msg'] = 'Invalid ID';
			}	
		}else if($type == "user_profile"){
			$userdet = Auth::User();
			if (!empty($userdet) > 0) {
				$uDir = md5(Auth::user()->customer_id);
	            $fName = $userdet['avatar_name'];	            
	            $destPath = storage_path('Profile'.DS.$uDir);
            	$filename = $destPath. DS . $fName;
            	$org_filename = $userdet['avatar_org'];
		    	if(File::exists($filename)) {
		    		$resp['status'] = 'success';
		    		$resp['file_link'] = $filename;
		    		$resp['org_filename'] = $org_filename;
		    	} else {
		    		$resp['status'] = 'error';
		    		$resp['err_msg'] = 'File not found';
		    	}
			} else {
				$resp['status'] = 'error';
		    	$resp['err_msg'] = 'Invalid ID';
			}	
		}
		return $resp;
	}


	/*
    * /{category}/{id}/{file_name}
    */
    public function downloadResourceFile($category, $id, $file_name='') {
    	//dd("Cat: ".$category." ID: ".$id." Filename: ".$file_name);
        $request = Request::all();
        try {
            $userdetails = Auth::user();
            $prefix = $practice_id = Session::get('practice_dbid');
            $recId = ($id != '') ?  Helpers::getEncodeAndDecodeOfId($id,'decode') : "";
            // Check invalid ID handling here
            if(!is_numeric($recId)) {
                \Log::info(" User ".$userdetails['username']. " Unauthorized Access Module:".$category." #".$id." File: ".$file_name);
                return Redirect::to('reports/generated_reports')->with('message', Lang::get("common.invalid_id_msg"));
            }

            $disk = Storage::disk('gcs');
            switch ($category) {

                case 'reports':

                    $reportInfo = ReportExport::where('practice_id', $practice_id)->where('id', $recId)->first();
                    
                    if (!empty($reportInfo) > 0) {
                        // Check in google cloud
                        $targetName = str_replace("Downloads/", "", $reportInfo->report_file_name);
                        //$filename = 'reports' . DS . $prefix. DS . $targetName;                        
                        $filename = $reportInfo->practice_id.DS.'reports'.DS. $reportInfo->created_by.DS.
                            date('my', strtotime($reportInfo->created_at)).DS. $targetName;                                                
                        //\Log::info('fileName:'.$filename);
                        ob_end_clean(); 
                        if($exists 	= $disk->exists($filename)) {
                            $file 	= $disk->get($filename); 
                            $fSize 	= $disk->size($filename);   

                            $headers = [
					            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					            'Content-Disposition' => 'attachment; filename=' . $targetName,
					            'Cache-Control' => 'max-age=0',
					            'X-Accel-Buffering' => 'no',
					            'Cache-Control' => 'max-age=0',
					            'no-cache' => 'true',
					            'must-revalidate' => 'true'
					        ];
                			return Storage::disk('gcs')->download($filename, $targetName, $headers);
                            exit;
                        } else {                                
                            \Log::info("Error occured while download Report File unavailable ".$filename." in GCS");
                            $msg = "File not found !!!";
                            $headers = [
					            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					            'Content-Disposition' => 'attachment; filename=' . $targetName,
					            'Cache-Control' => 'max-age=0',
					            'X-Accel-Buffering' => 'no',
					            'Cache-Control' => 'max-age=0',
					            'no-cache' => 'true',
					            'must-revalidate' => 'true'
					        ];
	                        $localFile = public_path().DS.'Downloads'.DS.$targetName;
                            if (!file_exists($localFile)) { 
                            	\Log::info($localFile." - Not Exists");                            	
                            	return Redirect::to('reports/generated_reports')->with('message', $msg);
                            } else {
                            	\Log::info($localFile." - Exists");
	                            return response()->download($localFile, $targetName, $headers);
	                        }
                        }
                        exit;
                        
                    } else {
                        \Log::info(" User ".$userdetails['username']. " tried to access Report File. Invalid File ID:".$recId);
                        return Redirect::to('reports/generated_reports')->with('message', Lang::get("common.invalid_id_msg"));
                    } 
                    break;

                case 'documents':
                    /*
                    $dDet =  Documents::where('id', $recId);
                    $dDet = $dDet->where('is_active', 1)->first();
                    if (count($dDet) > 0) {

                        $id = $dDet->id;
                        $docType = $dDet->category;
                        $prefix_dir = $userPractice['prefix'];
                        $file_name = $dDet->pdf_attachment;
                                              
                        // Check existing location end                                              
                        $filename = 'documents'.DS.$prefix_dir.DS.$docType.DS.$dDet->pdf_attachment;
                        if($exists = $disk->exists($filename)) {
                            $file = $disk->get($filename); 
                            $fSize = $disk->size($filename);                    
                            header ( 'Content-Length: ' . $fSize );
                            header('Content-type: application/pdf');
                            echo $file;
                        } else {
                            \Log::info(" User ".$userdetails['username']. " tried to access unavailable DOC-resource : ".$filename);
                            $msg = "File not found !!!";
                            return Redirect::to('home')->with('message', $msg);                             
                        }
                        
                        exit;
                    }
                    */
                    break;  

                default:                    
                    break;
            }
        } catch(Exception $e) {
            \Log::info("Error occured while downloadResourceFile. Error ".$e->getMessage() );
        }   
    }

	public function downloadResourceFile_ex($recId, $type="patient_import", $details) {
		// Download handled here
		try {
			$errUrl = $details['error_url'];
			if($type == "patient_import") {
				$resDet =  PatientImports::where('id', $recId)->first();
				if (!empty($resDet) > 0) { 
	            	$uDir = md5(Auth::user()->customer_id);
	            	$fName = $resDet['file_name'];
	            	$destPath = public_path().DS.'USERFILESATTACHMENTS' .DS. 'imports'. DS .$uDir;
	            	$filename = $destPath. DS . $fName;
	            	$org_filename = $resDet['org_filename'];
			    	if(File::exists($filename)) {
			    		$headers = array(
			             'Content-Type: text/plain'
			            );   			
			   			//$this->downloadFile($filename);
			    		return response()->download($filename, $org_filename);
				    } else {
						\Log::info("Error occured while download ResourceFile unavailable ".$filename );
						$msg = "File not found !!!";
						return Redirect::to($errUrl)->withInput()->withErrors($msg);
				    }

	            } else {                        
	                \Log::info(" User ".$userdetails['username']. " tried to access resource. Invalid File ID:".$recId);
	                return Redirect::to($errUrl)->withInput()->withErrors(Lang::get("common.invalid_id_msg"));
	            }
	        }

		} catch(Exception $e) {
			\Log::info("Error occured while downloadResourceFile. Error ".$e->getMessage() );
		}
	}
	
	public function removeOldAttachements() {
		try { 
			$old_reports = ReportExport::select("id", "practice_id", "created_by", "created_at", "report_file_name", "status")
							->whereRaw('(created_at <= CURRENT_DATE() - INTERVAL 30 DAY)')
							->where('export_type', 'reports')->orderBy('created_at', 'DESC')
							->limit(100)->get();
			
			foreach($old_reports as $rep) {
				$gcs_file = $rep->practice_id.DS.'reports'.DS.$rep->created_by.DS.date('my', strtotime($rep->created_at)).DS.$rep->report_file_name;
				// for status with 'Pending', 'Completed' records will be check storage and if file exists delete. otherwise reportexport task deleted_at alone will be updated.
				if(isset($rep->report_file_name) && $rep->report_file_name !='' && ( $rep->status == 'Pending' || $rep->status == 'Completed' ) ) {
					if(Storage::disk('gcs')->exists($gcs_file)) { 
						// \Log::info("Exists in GCS".$gcs_file);
						Storage::disk('gcs')->delete($gcs_file);
					} else {
						// \Log::info("Not Exists in GCS".$gcs_file);
					}
				}
				ReportExport::where('id',$rep->id)->update(['deleted_at'=>Carbon::now()]);
			}
		} catch(Exception $e) {
			\Log::info("Error occured while remove Old ResourceFile. Error ".$e->getMessage() );
		}
		return "success";
	}
}