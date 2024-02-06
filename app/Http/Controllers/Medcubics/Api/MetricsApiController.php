<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App;
use Request;
use Config;
use Response;
use Input;
use DB;
use Session;
use DateTime;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Practice as Practice;
use App\Models\Medcubics\Users as User;
use App\Models\Practice as Practices;

class MetricsApiController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndexApi()
	{
		$ClaimController  = new ClaimControllerV1();  
		// dd($ClaimController);
		$search_fields_data = $ClaimController->generateSearchPageLoad('metrics');
        $searchUserData = $search_fields_data['searchUserData'];
		$search_fields = $search_fields_data['search_fields'];

		return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('search_fields')));
	}
	public function getpracticelists($database_name)
	{
	    $db = new DBConnectionController();			
		$db->configureConnectionByName($database_name);
		$list = DB::connection($database_name)->select("select id,customer_id,practice_name from practices where status='Active'");
		return $list;
	}

	public function getcustomersApi($export = '',$data = '') {
		
		if(isset($data) && !empty($data)) {
			$request = $data;
		}
		else {
			$request = Request::All();
		}
		$page = "customerdetails";
		//Fetching Entire Details of all customer and Practice starts
		try {
			if(isset($request['Customer_Name']) && $request['Customer_Name'] == "" ) {
				//Customer Details Fetching as per month and year starts 
				if($request['Month'] !== "" && $request['Year'] !== "") {
						$date = new DateTime();
						$month = $request['Month'];
						if($month != '') {
							$month = $request['Month'];
						}
						else {
							$month = 01;
						}
						$year = $request['Year'];
						$date->setDate($year, $month, 01);
						$date->setTime(00, 00, 00);
						$fetch_date = $date->format('Y-m-d H:i:s');
						@$customers = Customer::where('created_at', '>=', $fetch_date)->get();
				}
				//Customer Details Fetching as per month and year ends 
				//Entire Customer Details Fetching starts 
				else {
					$customers = Customer::all()->toArray();
				}
				//Entire Customer Details Fetching ends
				//Including DB connection controller starts
				$database_name = getenv('DB_DATABASE');
				$db = new DBConnectionController();
				//Including DB connection controller ends
				$i = 0;
				//Fetching Practices as per the customers starts 
				foreach ($customers as $customer) {
				$cust[$i] = $customer;
				$customer_id = $customer['id'];
				//Selecting Practices as per the customers starts 
				@$practices = Practice::where('customer_id',$customer_id)->get();
				//Selecting Practices as per the customers starts 
				//Connecting and fetching values from corresponding practice starts
				if(!empty(@$practices)) {
					foreach($practices as $practice) {
						$practice_id = $practice->id;
						$cust_fetch_id[$i][$i] = $customer_id;
						//Practice DB name fetch starts
						$db_name = $db->getpracticedbname($practice->practice_name);
						//Practice DB name fetch ends

						//Practice DB existence check starts
						$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
						$dbs = DB::select($query, [$db_name]);
						//Practice DB existence check ends
						
						if(!empty($dbs)) {
							//Practice DB connection starts
							$db = new DBConnectionController();
							$db->connectPracticeDB($practice_id);
							//Practice DB connection ends

							//Metrics Details fetching starts
							$practice_details[$i] = DB::table('practices')
							->leftjoin('providers', 'providers.practice_id', '=', 'practices.id')
							->leftjoin('provider_types', 'providers.provider_types_id', '=', 'provider_types.id')
							->select(DB::raw('practices.practice_name, practices.id, sum(case providers.provider_types_id when "1" then 1 else 0 end) Rendering, sum(case providers.provider_types_id when "2" then 1 else 0 end) Referring, sum(case providers.provider_types_id when "3" then 1 else 0 end) Ordering, sum(case providers.provider_types_id when "4" then 1 else 0 end) Supervising, sum(case providers.provider_types_id when "5" then 1 else 0 end) Billing'))
							->get()->toArray();
							// dd($practice_details);
							
							$payment_details[$i] = DB::table('pmt_claim_fin_v1')->select(DB::raw('count(total_charge) as total_charge_count, sum(total_charge) total_charge_sum, sum(insurance_paid) insurance_paid_sum, sum(patient_paid) patient_paid_sum'))->get()->toArray();
							$statements_sent[$i] = DB::table('patientstatement_track')->select(DB::raw('sum(statements) statements_total'))->get()->toArray();
							$tickets[$i] = DB::table('ticket')->select(DB::raw('count(name) as ticket_count'))->get()->toArray();
							$user_count[$i] = User::where('customer_id', $customer_id)->get()->toArray();
							$user[$i][$i] = count((array)$user_count[$i]);			
							$metrics[$i] = array_merge($practice_details[$i], $payment_details[$i], $statements_sent[$i], $tickets[$i], $user[$i], $cust_fetch_id[$i]);
							//Metrics Details fetching ends	
						}
						else {
							\Log::info("No practice found");
						}
						$db->disconnectPracticeDB();
						$i++;
					}
				}
				else {
					\Log::info("No practice found");
					$metrics = array();
				}
				//Connecting and fetching values from practice corresponding ends
				}
				//Fetching Practices as per the customers ends
				}
				//Fetching Entire Details of all customer and Practice ends

				//Fetching particularDetails of all customer and Practice starts
				else {
					//Customer Details Fetching ends
					$customers =  Customer::where('id', $request['Customer_Name'])->first()->toArray();
					$cusomerDet[] = $customers; 
					$customer_createdby = User::where('id',$customers['created_by'])->first();
					$customers['created_by'] = $customer_createdby->name;
					//Customer Details Fetching ends
					if(!empty($customers)) {
						$customer_id = $customers['id'];
						$date = new DateTime();
							//Getting practice data as per month and year starts
							if($request['Month'] !== '' || $request['Year'] !== ''){
								$month = $request['Month'];
								if($month != '') {
									$month = $request['Month'];
								}
								else {
									$month = 01;
								}
								$year = $request['Year'];
								$date->setDate($year, $month, 01);
								$date->setTime(00, 00, 00);
								$fetch_date = $date->format('Y-m-d H:i:s');
								@$practices = Practice::where('customer_id',$customer_id)->where('created_at', '>=', $fetch_date)->get();
							}
							else {
								@$practices = Practice::where('customer_id',$customer_id)->get();
							}
							//Getting practice data as per month and year ends
					//Including DB connection controller starts
					$database_name = getenv('DB_DATABASE');
					$db = new DBConnectionController();
					//Including DB connection controller ends
					$i = 0;
					// dd($practices);
					$customers = $cusomerDet; 
					//Connecting and fetching values from corresponding practice starts
					if(!empty(@$practices)) {
						try {
							foreach($practices as $practice) {//dd("11111");
								$practice_id = $practice->id;
								//Practice DB name fetch starts
								$db_name = $db->getpracticedbname($practice->practice_name);
								//Practice DB name fetch ends

								//Practice DB existence check starts
								$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
								$dbs = DB::select($query, [$db_name]);
								//Practice DB existence check ends

									if(!empty($dbs)) {
										//Practice DB connection starts
										$db = new DBConnectionController();
										$db->connectPracticeDB($practice_id);
										//Practice DB connection ends

										//Metrics Details fetching starts
										$practice_details[$i] = DB::table('practices')
										->leftjoin('providers', 'providers.practice_id', '=', 'practices.id')
										->leftjoin('provider_types', 'providers.provider_types_id', '=', 'provider_types.id')
										->select(DB::raw('practices.practice_name, sum(case providers.provider_types_id when "1" then 1 else 0 end) Rendering, sum(case providers.provider_types_id when "2" then 1 else 0 end) Referring, sum(case providers.provider_types_id when "3" then 1 else 0 end) Ordering, sum(case providers.provider_types_id when "4" then 1 else 0 end) Supervising, sum(case providers.provider_types_id when "5" then 1 else 0 end) Billing'))
										->get()->toArray();
										
										$payment_details[$i] = DB::table('pmt_claim_fin_v1')->select(DB::raw('count(total_charge) as total_charge_count, sum(total_charge) total_charge_sum, sum(insurance_paid) insurance_paid_sum, sum(patient_paid) patient_paid_sum'))->get()->toArray();
										$statements_sent[$i] = DB::table('patientstatement_track')->select(DB::raw('sum(statements) statements_total'))->get()->toArray();
										$tickets[$i] = DB::table('ticket')->select(DB::raw('count(name) as ticket_count'))->get()->toArray();
										$user_count[$i] = User::where('customer_id', $customer_id)->get()->toArray();
										$user[$i][$i] = count($user_count[$i]);			
										$metrics[$i] = array_merge($practice_details[$i], $payment_details[$i], $statements_sent[$i], $tickets[$i], $user[$i]);
										//Metrics Details fetching ends
										
									}
									else {
										\Log::info("No practice found");
									}
									$db->disconnectPracticeDB();
									$i++;
								}
						}
						catch(Exception $e) {
							\Log::info("Something went wrong !!!: ".$e->getMessage() );
						}
					}
					else {
						\Log::info("No practice found");
						$metrics = array();
					}
					//Connecting and fetching values from corresponding practice ends
					}
				else {
					\Log::info("No practice found");
					$customers = array();
				}
					
			}

			//Fetching particularDetails of all customer and Practice ends
			if(isset($metrics)) {
				return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('customers', 'page', 'metrics')));
			}
			else {
				// dd($customers);
				$metrics = array();
				return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('customers', 'page', 'metrics')));
			}
		}

		catch(Exception $e) {
			\Log::info("Something went wrong !!!: ".$e->getMessage() );
			$customers = array();
			$metrics = array();
			return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('customers', 'page', 'metrics')));
		}
		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
