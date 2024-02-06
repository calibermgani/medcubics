<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Icd09 as Icd09;
use Input;
use Auth;
use Response;
use Request;
use Validator;

class Icd09ApiController extends Controller {
	
	public function getIndexApi($export = "")
	{
		$icd_arr = "";
		if($export != "")
		{
			$icd_arr = Icd09::with('user','userupdate')->get();
			$exportparam 	= 	array(
				'filename'=>	'Icd 09',
				'heading'=>	'Icd 09',
				'fields' =>	array(
								'code'	=> 'Code',
								'change_indicator'	=> 'Change Indicator',
								'short_desc' =>	'ICD 09 Short Description',
								'code_status'	=> 'Code Status',
                                                                'created_by' =>	array('table'=>'user' ,	'column' => 'name' ,	'label' => 'Created By'),
                                                                'updated_by' =>	array('table'=>'userupdate' ,	'column' => 'name' ,	'label' => 'Updated By'),
								
				));
			$callexport = new CommonExportApiController();
                         return $callexport->generatemultipleExports($exportparam, $icd_arr, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('icd_arr')));
	}
	
	public function getCreateApi()
	{			
		$icd = Icd09::all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('icd')));
	}
	
	public function getStoreApi($request='')
	{
		if($request == '')
		$request = Request::all();
		$validator = Validator::make($request, Icd09::$rules, Icd09::$messages);
		if ($validator->fails())
			{

				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
		else
			{	
				$icd = Icd09::create(Request::all());
				$user = Auth::user ()->id;
				$icd->created_by = $user;
				$icd->save ();

				return Response::json(array('status'=>'success', 'message'=>'ICD-9 added successfully','data'=>''));					
			}
	}
	
	public function getShowApi($id)
	{
		$icd = Icd09::with('user','userupdate')->where('id',$id)->first();	
		return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('icd')));	
	}

	public function getEditApi($id)
	{
		$icd = Icd09::findOrFail($id);
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('icd')));
	}
	
	public function getUpdateApi($type, $id, $request='')
	{
		$request = Request::all();

		$validator = Validator::make($request, Icd09::$rules, Icd09::$messages );
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{			
			$icd = Icd09::findOrFail($id);
			$icd->update(Request::all());
			$user = Auth::user ()->id;
			$icd->updated_by = $user;
			$icd->save ();
			return Response::json(array('status'=>'success', 'message'=>'ICD-9 updated successfully','data'=>''));					
		}
	}
	
	public function getDeleteApi($id)
	{
		Icd09::Where('id',$id)->delete();
		return Response::json(array('status'=>'success', 'message'=>'ICD-9 deleted successfully','data'=>''));	
	}
	
	public function geticd9tablevaluesAdmin()
	{		
		$request = Request::all();				
		$start = $request['start'];
		$len = $request['length'];
		$cloum = intval($request["order"][0]["column"]);
		$order = $request['columns'][$cloum]['data'];
		if($request['columns'][$cloum]['data'] == 'favourite')
		{
			$order = 'id';
		}	
		$order_decs = $request["order"][0]["dir"];
        $search = '';
		if(!empty($request['search']['value']))
		{
			$search= $request['search']['value'];
		}	
		$icd_arr = Icd09::with('user','userupdate')
		   ->where('code', 'LIKE', '%'.$search.'%')
		   ->orWhere('change_indicator', 'LIKE', '%'.$search.'%')
		   ->orWhere('code_status', 'LIKE', '%'.$search.'%')
		   ->orWhere('short_desc', 'LIKE', '%'.$search.'%')
		   ->orderBy($order,$order_decs)->skip($start)->take($len)->get()->toArray();		
		//$icd_arr = Icd09::with('user','userupdate')->orderBy('id','DESC')->skip($start)->take($len)->get()->toArray();				
		$total_rec_count = Icd09::with('user','userupdate')->count();		
		$data['data'] = $icd_arr;
		$data = array_merge($data,$request);
		$data['recordsTotal'] = $total_rec_count;
		$data['recordsFiltered'] = $total_rec_count;		
		return Response::json($data);	
	}
	
	function __destruct() 
	{
    }
}
