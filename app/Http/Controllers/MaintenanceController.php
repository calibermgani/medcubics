<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Redirect;
use Auth;
use View;
use Config;
use App\Maintenance_sql;
use Validator;
use DB;
use App\Models\Practice as Practice;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
class MaintenanceController extends Controller {

	public function __construct()
    {
        $this->middleware('auth');
        View::share('heading', 'Maintenance');
        View::share('selected_tab', 'maintenance');
        View::share('heading_icon', Config::get('cssconfigs.maintenance.sql'));
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data['maintenance'] = Maintenance_sql::orderBy('id','desc')->get();
		return view('admin/sql/maintenance',$data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */

	//-------------------------------------------- MAINTENANCE SQL CREATE ---------------------------

	public function create(Request $request)
	{
		try{
			$validate = Validator::make($request->all(),
						['query'=>'required',
						'developer_name'=>'required',
						'developed_date'=>'required',
						]);
			if($validate->fails()==true){
				return response()->json(['success'=>0, "message"=>$validate->errors()]);
			}
			else{
				$maintenance = new Maintenance_sql();
				$request['status'] = "Incomplete";
				$request['developed_date'] = date("Y-m-d",strtotime($request['developed_date']));
				$request['user'] = Auth::id();
				$maintenance->create(array_except($request->all(), ['_token']));
				return response()->json(['success'=>1, "message"=>'SQL query added successfully']);
			}
		} 
		catch(\Illuminate\Database\QueryException $ex){ 
			if($ex->getSql()){
				\Log::info($ex);
				return response()->json(['success'=>0, "message"=>'SQL Query wrong']);
			} else { 
				return response()->json(['success'=>0, "message"=>'Internal server error']);
			}
		}
	}

	//------------------------------ MAINTENANCE SQL EXECUTION --------------------------------

	public function execute(Request $request)
	{
		try{
			$validate = Validator::make($request->all(), ['id'=>'required',]);
			if($validate->fails()==true){
				return response()->json(['success'=>0, "message"=>$validate->errors()]);
			} else {
				$success = $fail = 0;
				$maintenance = Maintenance_sql::find($request['id']);

				/*PRACTICES DATABASE CONNECTVITY*/

		        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
		        $practices = Practice::where("status","Active")->pluck('practice_name', 'id')->all();
		        $practice_list = [];
		        foreach ($practices as $key => $pra) {
		            $tenantDBs = str_replace(' ', '_', strtolower($pra));
		            $stats = DB::select($query, [$tenantDBs]);
		            if(!empty($stats))
			            $practice_list[$key] = $tenantDBs;   
		        }
		        // Included admin db name for execute query.
		        $adminDB = env('DB_DATABASE');		        
		        array_push($practice_list, $adminDB);

				if(!empty($practice_list)){
					$success_practice = $failure_practice = $message = [];
					foreach ($practice_list as $k=>$value) {
						try {
							$db = new DBConnectionController();
							if (!config('siteconfigs.is_enable_provider_add')) {
					            $value = config('siteconfigs.connection_database');
					        }
					        DB::disconnect();
					        $db->configureConnectionByName($value);
					        Config::set('database.default', $value);
					        //\Log::info(\DB::getDatabaseName());
							try {
								$link = mysqli_connect(getenv('DB_HOST'),getenv('DB_USERNAME'),getenv('DB_PASSWORD'),$value);
								try {
									$result=mysqli_multi_query($link, $maintenance->query);
								}catch(\Exception $e){
									\Log::info("############Error Msg: ".$e->getMessage() );
								}
				            	if(!empty($result)){
				            		$success++;
				            		$success_practice[] = $value;
				            		$message[$success]["Success_Practice"] = $value;
				            	} else {
				            		$fail++;
				            		$failure_practice[] = $value;
				            	}
							} catch(\Exception $e) {
								$fail++;
			            		$failure_practice[] = $value;
			            		$message[$fail]["Failure_Practice"] = $value;
			            		//$message[$fail]["Failure_Practice_Error"] = $e->getMessage();
			            		\Log::info("SQL Maintenance Error: on ".$value." Error: ".$e->getMessage());
							}
			            } catch (\PDOException $e) {
			            	$fail++;
		            		$failure_practice[] = $value;
		            		//$message[] = $value." ".$e->getMessage();
			            	\Log::info("DB not connected. Error Msg: ".$e->getMessage() );
						}
					}
					$update = new Maintenance_sql;
					if($fail==0){
						$status = 'Success';
						$success_practice = json_encode($success_practice);
						$failure_practice = NULL;
					} elseif($success==0) {
						$status = 'Error';
						$success_practice = NULL;
						$failure_practice = json_encode($failure_practice);	
					} else {
						$status = 'Error';
						$success_practice = json_encode($success_practice);
						$failure_practice = json_encode($failure_practice);	
					}
					$update->setconnection('mysql')->where('id',$request['id'])->update(["status"=>$status,"success_practice"=>$success_practice,"failure_practice"=>$failure_practice,"applied_date"=>date('Y-m-d H:i:s')]);
					if($fail==0){
						return response()->json(['success'=>1, "message"=>'SQL Query is executed successfully '.json_encode($success_practice)]);
					} else {
						return response()->json(['success'=>2, "message"=>json_encode($message)]);
					}
				} else {
					return response()->json(['success'=>0, "message"=>'Active practices not found']);
				}
			}
		} catch(\Exception $e){
			return response()->json(['success'=>0, "message"=>$e->getMessage()]);
		}
	}

	public function storedProcedure()
	{
		$data['maintenance'] = StoredProcedure::orderBy('id','desc')->get();
		return view('admin/sql/maintenance',$data);
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
