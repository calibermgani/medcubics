<?php namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Config;
use Request;
use DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Controllers\Medcubics\Api\ManageticketApiController as ManageticketApiController;
use App\Models\Medcubics\Users as User;
use App\Models\ReportExport as ReportExport;
use App\Models\ProviderScheduler as ProviderScheduler;
use App\Models\Payments\ClaimInfoV1;
use Auth;
use App;
use File;
use App\Http\Controllers\Reports\Financials\FinancialController;
use App\Http\Controllers\Reports\Appointment\AppointmentController;
use App\Http\Controllers\Reports\Practicesettings\FacilitylistController;
use App\Http\Controllers\Reports\Practicesettings\InsurancelistController;
use App\Http\Controllers\Reports\Practicesettings\CptlistController;
use App\Http\Controllers\Reports\Practicesettings\ProviderlistController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\Reports\CollectionController;
use App\Http\Controllers\Reports\PatientController;
use App\Http\Controllers\Reports\PerformanceController;
use App\Http\Controllers\Claims\Api\ClaimApiControllerV1;
use App\Models\Claims\EdiCron;

class CronController extends Controller {

	public function getpracticelist($database_name)
	{
	    $db = new DBConnectionController();			
		$db->configureConnectionByName($database_name);
		$list = DB::connection($database_name)->select("select id,customer_id,practice_name from practices where status='In Progress'");
		return $list;
	}
	public function getpracticelists($database_name)
	{
	    $db = new DBConnectionController();			
		$db->configureConnectionByName($database_name);
		$list = DB::connection($database_name)->select("select id,customer_id,practice_name from practices where status='Active'");
		return $list;
	}
	
	public function getpractice($database_name, $practice_id)
	{
	    $db = new DBConnectionController();			
		$db->configureConnectionByName($database_name);
		$list = DB::connection($database_name)->select("select id,customer_id,practice_name from practices where status='Active' and id=".$practice_id);
		return $list;
	}
	
	public function AppointmentStatusUpdate()
	{	
    	\Log::info("-----------------Appointment CRON---STARTS---------------------------<br>");
		\Log::info("Appointment Status Update - CRON Initiated<br>");
                echo "Appointment Status Update - CRON Initiated<br>";
		try{
			$database_name = getenv('DB_DATABASE'); 
	        $db = new DBConnectionController();	         
		    $practice_lists = $this->getpracticelists($database_name); // Get practices list from admin database
			if(!empty($practice_lists)) {
                \Log::info("Database List Found: Starting Update");
                echo "Database List Found: Starting Update<br>";    
				foreach($practice_lists as $practice_list) {
					$db_name = $db->getpracticedbname($practice_list->practice_name);
					$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
          			$dbs = DB::select($query, [$db_name]);      
                              
          			if(!empty($dbs)){
                        $db = new DBConnectionController();			 	
                        $db->configureConnectionByName($db_name);
                        $mysqldbconn = DB::Connection($db_name);
                        $current_date =  date('Y-m-d');
                        $list = $mysqldbconn->select("SELECT status,scheduled_on,patient_id,appointment_time,id as appt_id FROM `patient_appointments` WHERE `status` IN ('Scheduled','Rescheduled ') AND `scheduled_on` < '$current_date'");
                        if(!empty($list)){
                        	\Log::info("Updating Practice: ".$db_name);
                        	echo "Updating Practice: ".$db_name."<br>";
                        	$apptCount = 0;
                            foreach($list as $scheduled){
                                $apptCount++;
                            	$mysqldbconn->update("update patient_appointments SET status = 'Encounter' where id=".$scheduled->appt_id);
                            	$mysqldbconn->update("update claim_info_v1 SET status ='Complete' where patient_id = ".$scheduled->patient_id." AND date_of_service=".$scheduled->scheduled_on);	
                            }
                        	\Log::info($apptCount." Appointments updated for Practice: ".$db_name);
                        	echo $apptCount." Appointments updated for Practice: ".$db_name."<br>";
                        } else {
                        \Log::info("No Appointments to update in ".$db_name);
                            echo "No Appointments to update in ".$db_name."<br>";
                        }
                    } else {
                        \Log::info("No practice found");
                        echo "No practice found<br>";
                    }
				}
			} else {
	            \Log::info("No practice found");
                echo "No practice found<br>";
			}
		} catch(Exception $e) {
			\Log::info("Something went wrong on Appointment Status Update CRON: ".$e->getMessage() );
		}
                \Log::info("-----------------Appointment CRON---ENDS----------------------------<br>");
	}	

	public function doseedingcron()
	{
        $database_name = getenv('DB_DATABASE');
        $db = new DBConnectionController();				    
	    $practice_lists = $this->getpracticelist($database_name); // Get practices list from admin database
		$tables = DB::connection($database_name)->select('SHOW TABLES');
		$ignore_tables = array('icd_09','adminpage_permission','customernotes','customerusers','employers','facilities','facilityaddresses','facilitydocuments','facilitymanagecares','favouritecpts','feeschedules','insuranceoverrides','insuranceoverrides','patients','patient_contacts','patient_eligibility','patient_insurance','patient_authorizations','practiceoverrides','providers');
		if(!empty($practice_lists)) {
			foreach($practice_lists as $practice_list) {
			   foreach($tables as $table)
				{
				   $db_name = $db->getpracticedbname($practice_list->practice_name);
				   $db->configureConnectionByName($db_name);
			       $table_name =  $table->Tables_in_responsive; // Statically given the database name
				   //$table_name =  $table->Tables_in_jan6_new;	
			       if(!in_array($table_name, $ignore_tables)) {
			       	   if($table_name == 'practices'){
                            $insert_cpt= "INSERT INTO $db_name.$table_name SELECT * FROM $database_name.$table_name WHERE id=".$practice_list->id;
			       	   } elseif($table_name == 'users'){
                            $insert_cpt= "INSERT INTO $db_name.$table_name SELECT * FROM $database_name.$table_name WHERE customer_id=".$practice_list->customer_id." AND practice_user_type = 'customer' OR id=1";
			       	   } elseif($table_name == 'customers'){
                            $insert_cpt= "INSERT INTO $db_name.$table_name SELECT * FROM $database_name.$table_name WHERE id=".$practice_list->customer_id ;
			       	   }else{
			       	   		$insert_cpt= "INSERT INTO $db_name.$table_name SELECT * FROM $database_name.$table_name" ;
			       	   }					       
	                   DB::statement($insert_cpt);
                   }
				}
				 $db->configureConnectionByName($database_name);
				 $this->movemediafolder($practice_list->id);
				 DB::update("update practices SET status='Active' where id=".$practice_list->id);
				 echo 'updated practice'.$practice_list->practice_name;
			}
	    } else {
	    	echo "empty practices";
	    }
			
	}
        
	public function movemediafolder($practice_id){
               $main_dir_name  = md5('P'.$practice_id); 
               $chk_env_site   = getenv('APP_ENV');
		if($chk_env_site==  Config::get('siteconfigs.production.defult_production')){
			$storage_disk = "s3_production";
			$bucket_name  = "medcubicsproduction";
		}
		else{
 			$storage_disk = "s3";
			$bucket_name  = "medcubicslocal";
		} 
                
                $main_dir_arr 	= Storage::disk($storage_disk)->directories();
                
		if(!in_array($main_dir_name,$main_dir_arr))	Storage::disk($storage_disk)->makeDirectory($main_dir_name);
                
                 $main_dir_arrimg 	= Storage::disk($storage_disk)->directories($main_dir_name);
		if(!in_array($main_dir_name."/image",$main_dir_arrimg))	Storage::disk($storage_disk)->makeDirectory($main_dir_name."/image");

                $main_dir_arr_module 	= Storage::disk($storage_disk)->directories($main_dir_name.'/image');
                if(!in_array($main_dir_name."/image/insurance",$main_dir_arr_module))	Storage::disk($storage_disk)->makeDirectory($main_dir_name."/image/insurance"); 
                
                $getimages = Storage::disk($storage_disk)->allFiles('admin/image/insurance');
                foreach($getimages as $imgname) {
                    $imagename = basename($imgname);
                    Storage::disk($storage_disk)->copy($imgname, $main_dir_name."/image/insurance/".$imagename);
		}
	} 
	
	public function documentcron()
	{
		$database_name	= getenv('DB_DATABASE');
		$chk_env_site   = getenv('APP_ENV');
		$db 			= new DBConnectionController();			
		$db->configureConnectionByName($database_name);
		$mysqldbconn 	= DB::Connection($database_name);
		
		$list = $mysqldbconn->select("select * from documents where `temp_type_id` != '' AND type_id = '0' AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR) ");
		
		if(count($list)>0)
		{
			if($chk_env_site==  Config::get('siteconfigs.production.defult_production'))
			{
				$storage_disk = "s3_production";
				$bucket_name  = "medcubicsproduction";
            }
            else
			{
				$storage_disk = "s3";
				$bucket_name  = "medcubicslocal";
            }
			$list = json_decode(json_encode($list), true);
			foreach($list as $list_key=>$list_val)
			{
				Storage::disk($storage_disk)->delete($list_val['document_path'].$list_val['filename']);
				$mysqldbconn->delete("delete from documents where `id` = '{$list_val['id']}'");
			}
			echo "One hour before created temporary document deleted successfully";
			exit;
		}
		else
		{
			echo "No temporary document availble";
			exit;
		}
	}	

	public function userLogginCron()
	{
		$database_name= getenv('DB_DATABASE');
		$db = new DBConnectionController();			
		$db->configureConnectionByName($database_name);
		$id = Auth::User()->id;
		$last_update =User::where('id',$id)->first();
		$last_access_time = $last_update['last_access_date'];
		$last_out_time = strtotime($last_access_time);
		$diff =time()-$last_out_time;
		if($diff <=3600000){
			$list =DB::connection($database_name)->update("update users set is_logged_in='0'  where `is_logged_in` != 1 ");
		}
		return Redirect::to('/');
	}

	public function getProvideralert()
	{
		$alertby = Config::get('siteconfigs.providerAlertCron.alert_on');
		$alertbefore = Config::get('siteconfigs.providerAlertCron.alert_before');
		$result=ProviderScheduler::getProviderRecord($alertby,$alertbefore);
		dd($result);
	}
	/***  Start user ticket status update email based ***/
	public static function ticketNotificationSend()
	{
		$ticket_detail = Config::get('siteconfigs.userticket');
		$alertby = $ticket_detail['alert_on'];
		$alert_before = $ticket_detail['alert_before'];
		$status_change_before_count = $ticket_detail['status_change_before'];
		$result = ManageticketApiController::getNotificationSendApi($alertby,$alert_before,$status_change_before_count);
		
	}
	/***  End user ticket status update email based ***/
	
	/***  Start user statment hold releasae date ***/

	public function stmtHoldReleaseUpdate() {
		ini_set('memory_limit', '-1');
		\Log::info("Statement hold releasae date cron started");
		try {
			$database_name = getenv('DB_DATABASE'); 
	        $db = new DBConnectionController();	         
		    $practice_lists = $this->getpracticelists($database_name); // Get practices list from admin database
			if(!empty($practice_lists)) {
                \Log::info("Database List Found: Starting Update");
                echo "Database List Found: Starting Update<br>";    
				foreach($practice_lists as $practice_list) {
					$db_name = $db->getpracticedbname($practice_list->practice_name);
					$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
          			$dbs = DB::select($query, [$db_name]);      
          			echo "<br> Start processing :".$db_name."<br>";
          			if(!empty($dbs)){
                        $db = new DBConnectionController();			 	
                        $db->configureConnectionByName($db_name);
                        $mysqldbconn = DB::Connection($db_name);
                        $current_date =  date('Y-m-d');

                        // Update the statement category to statement as 'Yes;, hold_release_date as '0000-00-00', hold_reason as 0
                        $catList = $mysqldbconn->select("SELECT id FROM `stmt_category` WHERE `status` IN ('Active') AND `stmt_option` = 'Hold' AND `hold_release_date` <> '0000-00-00' AND `hold_release_date` <= '$current_date'");
                        if(!empty($catList)){
                        	\Log::info("Updating Category. Practice: ".$db_name);
	                        echo "Updating Category. Practice: ".$db_name."<br>";
	                        $catCount = 0;
	                        foreach($catList as $cats){
	                        	$catCount++;
                            	$mysqldbconn->update("update stmt_category SET stmt_option = 'Yes', hold_reason = 0, hold_release_date = '0000-00-00' where id=".$cats->id);
                            	// Here we can add activity log
                            	\Log::info(" Category #".$cats->id." updated to statment as Yes ");
	                        }
	                        \Log::info($catCount." Category updated for Practice: ".$db_name);
	                        echo $catCount." Category updated for Practice: ".$db_name."<br>";
                        } else {
                        	\Log::info("No Category to update statements in ".$db_name);
                            echo "No Category to update statements in ".$db_name."<br>";
                        }		

                        // Update the patients statments statement as 'Yes;, hold_release_date as '0000-00-00', hold_reason as 0
                        $list = $mysqldbconn->select("SELECT id FROM `patients` WHERE `status` IN ('Active') AND `statements` = 'Hold' AND `hold_release_date` <> '0000-00-00' AND `hold_release_date` <= '$current_date'");
                        
                        if(!empty($list)){                        	
	                        \Log::info("Updating Practice: ".$db_name);
	                        echo "Updating Practice: ".$db_name."<br>";
	                        $patCount = 0;
                            foreach($list as $pats){
                                $patCount++;
                            	$mysqldbconn->update("update patients SET statements = 'Yes', hold_reason = 0, hold_release_date = '0000-00-00' where id=".$pats->id);

                            	//Update payment hold reason as empty when statement hold released.
                            	$mysqldbconn->update("update claim_info_v1 SET payment_hold_reason = '' where patient_id=".$pats->id." AND payment_hold_reason = 'patient'");
                            	// Here we can add activity log
                            	\Log::info(" Patient #".$pats->id." updated to statment as Yes ");
                            }
	                        \Log::info($patCount." Patients updated for Practice: ".$db_name);
	                        echo $patCount." Patients updated for Practice: ".$db_name."<br>";
                        } else {
                        	\Log::info("No Patients to update statements in ".$db_name);
                            echo "No Patients to update statements in ".$db_name."<br>";
                        }						

                    } else {
                        \Log::info("No practice found");	echo "No practice found<br>";
                    }
				}
			} else {
				\Log::info("No practice found");	echo "No practice found<br>";
			}
		} catch(Exception $e) {
			\Log::info("Something went wrong on Statement Hold Release update CRON: ".$e->getMessage() );
		}
	}
	/***  End user statment hold releasae date ***/

	public function storeReportFile(){ 
		ini_set('memory_limit', '-1');
		try{
			$database_name = getenv('DB_DATABASE'); 
	        $db = new DBConnectionController();	
	        // If any cron not completed set as Pending in order to restart
	        $current_date =  date('Y-m-d');
			$prePendingReport = ReportExport::where('status','Inprocess')->where('updated_at', '<',$current_date)->get()->toArray();			
			if(!empty($prePendingReport)) {
				ReportExport::where('status','Inprocess')->where('updated_at', '<',$current_date)->update(['status'=>'pending']);
			}
			// Taken only one report at a time 
			$domain = explode('/',Request::root());
			if($domain[2] == 'avec.medcubics.com'){
				$getReportExport = ReportExport::with('createdUser')->where('practice_id','40')->where('status','Pending')->orderBy('id', 'asc')->skip(0)->take(1)->get()->toArray();
			}else{
				$getReportExport = ReportExport::with('createdUser')->where('practice_id','!=','40')->where('status','Pending')->orderBy('id', 'asc')->skip(0)->take(1)->get()->toArray();
			}
			foreach($getReportExport as $list){
				ReportExport::where('id',$list['id'])->update(['status'=>'Inprocess']);
				$db->connectPracticeDB($list['practice_id']);
				$temp_data = parse_url($list['report_url']);
				$temp_path = explode('/',$temp_data['path']);
				$temp_query = explode('&',@$temp_data['query']);
				$download_type = $temp_path[count($temp_path)-1];
				if(!empty($temp_query)) {
					foreach($temp_query as $querylist){
						$urlData = explode('=',@$querylist);
						$key = str_replace("[]","",$urlData[0]);
						$data[@$key] = @$urlData[1];
					}
				}
				$data['export_id'] = $list['id'];
                // To avoid created by parameter overwrite issue commented below one
                // Rev.1 Anjukaselvan : 13/08/2019
				//$data['created_by'] = $list['created_by'];
				$data['practice_id'] = $list['practice_id'];
				$data['created_user'] = isset($list['created_user']['short_name'])?$list['created_user']['short_name']:'';				
				$this->functionCall($data,$list['report_controller_name'],$list['report_controller_func'],$download_type);
				echo 'Called Report - '.$list['report_name'];
			}
		} catch(Exception $e) {
			\Log::info("Something went wrong on Report Export CRON: ".$e->getMessage() );
		}
		
	}
	
	public function functionCall($data,$controller,$function,$download_type){
		try{
			\Log::info("CRON Function Call Initiated: Controller".$controller. "Function: ".$function." Download Type: ".$download_type);
			if($controller == 'FinancialController'){
				$func = new FinancialController();
				switch($function){
					case 'unbilledexport':
						$func->unbilledexport($download_type,$data);
						break;
					case 'agingDetailsReportExport':
						$func->agingDetailsReportExport($download_type,$data);
						break;	
					case 'endDayExport':
						$func->endDayExport($download_type,$data);
						break;	
					case 'workrvusearchExport':
						$func->workrvusearchExport($download_type,$data);
						break;	
					case 'workbenchsearchExport':
						$func->workbenchSearchExport($download_type,$data);
						break;	
					case 'denialAnalysisSearchExport':
						$func->denialAnalysisSearchExport($download_type,$data);
						break;
					case 'chargecategorysearchExport':
						$func->chargecategorysearchExport($download_type,$data);
						break;		
					default:
						\Log::info("CRON Function Not Assigned: Controller".$controller. "Function: ".$function);	
				}
			}else if($controller == 'ReportController'){
				$func = new ReportController();
				switch($function){
					case 'chargesearchexport':
						$func->chargesearchexport($download_type,$data);
						break;
					case 'chargepaymentsearch':
						$func->chargepaymentsearch($download_type,$data);
						break;
					case 'patientIcdWorksheetExport':
						$func->patientIcdWorksheetExport($download_type,$data);
						break;
					case 'paymentsearchexport':
						$func->paymentsearchexport($download_type,$data);
						break;
					case 'adjustmentSearchexport':
						$func->adjustmentSearchexport($download_type,$data);
						break;
					case 'financialSearchExport':
						$func->financialSearchExport($download_type,$data);
						break;
					case 'patientDemographicsExport':
						$func->patientDemographicsExport($download_type,$data);
						break;	
					case 'patientAddressListExport':
						$func->patientAddressListExport($download_type,$data);
						break;	
					case 'refundsearchexport':
						$func->refundsearchexport($download_type,$data);
						break;	
					case 'patientStatementHistoryExport':
						$func->patientStatementHistoryExport($download_type,$data);
						break;	
					case 'patientWalletHistoryExport':
						$func->patientWalletHistoryExport($download_type,$data);
						break;	
					case 'getAgingReportSearchExport':
						$func->getAgingReportSearchExport($download_type,$data);
						break;
					case 'employerListExport':
						$func->employerListExport($download_type,$data);
						break;	
					case 'patientStatementStatusExport':
						$func->patientStatementStatusExport($download_type,$data);
						break;
					case 'proceduresearchExport':
						$func->proceduresearchExport($download_type,$data);
						break;						
					default:
						\Log::info("CRON Function Not Assigned: Controller".$controller. "Function: ".$function);				
				}
			}elseif($controller == 'FacilitylistController'){
				$func = new FacilitylistController();
				switch($function){
					case 'facilityListSummaryExport':
						$func->facilityListSummaryExport($download_type,$data);
						break;
					default:
						\Log::info("CRON Function Not Assigned: Controller".$controller. "Function: ".$function);		
				}
			}elseif($controller == 'InsurancelistController'){
				$func = new InsurancelistController();
				switch($function){
					case 'insuranceListExport':
						$func->insuranceListExport($download_type,$data);
						break;
					default:
						\Log::info("CRON Function Not Assigned: Controller".$controller. "Function: ".$function);		
				}
			}elseif($controller == 'CptlistController'){
				$func = new CptlistController();
				switch($function){
					case 'cptListExport':
						$func->cptListExport($download_type,$data);
						break;
					default:
						\Log::info("CRON Function Not Assigned: Controller".$controller. "Function: ".$function);		
				}
			}elseif($controller == 'ProviderlistController'){
				$func = new ProviderlistController();
				switch($function){
					case 'providerListExport':
						$func->providerListExport($download_type,$data);
						break;
					default:
						\Log::info("CRON Function Not Assigned: Controller".$controller. "Function: ".$function);		
				}
			}elseif($controller == 'AppointmentController'){
				$func = new AppointmentController();
				switch($function){
					case 'appointmentanalysisExport':
						$func->appointmentanalysisExport($download_type,$data);
						break;
					default:
						\Log::info("CRON Function Not Assigned: Controller".$controller. "Function: ".$function);		
				}
			}
			elseif($controller == 'CollectionController'){
				$func = new CollectionController();
				switch($function){
					case 'insuranceOverPaymentSearchexport':
						$func->insuranceOverPaymentSearchexport($download_type,$data);
						break;
					case 'patientInsurancePaymentSearchexport':
						$func->patientInsurancePaymentSearchexport($download_type,$data);
						break;
					default:
						\Log::info("CRON Function Not Assigned: Controller".$controller. "Function: ".$function);		
				}
			}
			elseif($controller == 'PatientController'){
				$func = new PatientController();
				switch($function){
					case 'walletBalanceSearchExport':
						$func->walletBalanceSearchExport($download_type,$data);
						break;
					default:
						\Log::info("CRON Function Not Assigned: Controller".$controller. "Function: ".$function);		
				}
			}
			elseif($controller == 'PerformanceController'){
				$func = new PerformanceController();
				switch($function){
					case 'monthendperformanceExport':
						$func->monthendperformanceExport($download_type,$data);
						break;
					case 'providerSummaryExport':
						$func->providerSummaryExport($download_type,$data);
						break;
					case 'denialsSummaryExport':
						$func->denialsSummaryExport($download_type,$data);
						break;
					default:
						\Log::info("CRON Function Not Assigned: Controller".$controller. "Function: ".$function);		
				}
			}
			\Log::info("CRON Function Call Finished: Controller".$controller. "Function: ".$function." Download Type: ".$download_type);
		} catch(Exception $e) {
			ReportExport::where('id',$data['id'])->update(['status'=>'Error']);
			\Log::info("Something went wrong on Report Export CRON: ".$e->getMessage() );
		}
	}


	public function ediSubmit()
	{
		$claim_id=[];
        $result='';

		$pending_claims=EdiCron::where('status','Pending')->orWhere('status','Inprogress')->get();

		foreach($pending_claims as $claims_data)
		{
			$limit =3;
			$i=0;
			$processed=0;
            $claims=json_decode($claims_data['claims'],true);
            $claims= explode(",",$claims);
            $processed_claims=EdiCron::where('id',$claims_data['id'])->value('processed_claims');
            if($processed_claims == null)
            {
                $processed_claims=0;
            }

            $claim_nos=count($claims);
            $to_process=$claim_nos-$processed_claims[0];
            $claims=array_slice($claims,$processed_claims[0],$claim_nos);
            $processed=$processed_claims[0];
            if($to_process <= $limit )
            {
                $claims = implode(',', $claims);
				$result=$function->checkAndSubmitEdiClaim($claims);
				$processed+=$to_process;
            }
            else if($to_process > $limit ){
                    $chunks=array_chunk($claims,$limit );
                   
                    foreach($chunks as $arrays)
                    {
                        $arrays = implode(',', $arrays);
						$result=$function->checkAndSubmitEdiClaim($arrays);
                        $i++;
                        $processed+=$limit ;
						break;
                    }
			}
			if($result =='success')
			{
				EdiCron::where('id', $claims_data['id'])->update(['processed_claims' => $processed,'status'=>'Inprogress']);
				if($processed == $claim_nos)
				{
					EdiCron::where('id', $claims_data['id'])->update(['status'=>'Completed']);
				}
			}
			break;
		}
	}
		
}