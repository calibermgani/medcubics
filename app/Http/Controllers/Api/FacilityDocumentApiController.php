<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\Facilitydocument as Facilitydocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Lang;

class FacilityDocumentApiController extends Controller {

	public function getIndexApi($facility_id)
	{
		$facility = Facility::with('facility_address')->where('id',$facility_id)->first();
		$pictures =	Facilitydocument::where ( 'facilities_id', $facility_id )->get ();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facility', 'pictures')));
	}
		
	public function getCreateApi($facility_id)
	{
		$facility = Facility::with('facility_address')->where('id',$facility_id)->first();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facility')));
	}

	public function getAddDocumentApi($request='')
	{
		$file = Request::file('filefield');
		if($request == '')
			$request = Request::all();	
			
		$validator = Validator::make($request, Facilitydocument::$rules, Facilitydocument::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
	{
		$picture = new Facilitydocument ();
		$picture->facilities_id = Request::input ( 'facilities_id' );
		$picture->title = Request::input ( 'title' );
		$picture->description = Request::input ( 'description' );
		$picture->category = Request::input ( 'category' );
		$ids = $picture->facilities_id;
		$user = Auth::user ()->name;
		$user_email = Auth::user ()->email;
		$picture->user_email = $user_email;
		$picture->created_by = $user;
		$picture->mime = $file->getClientMimeType ();
		$picture->original_filename = $file->getClientOriginalName ();
		$picture->filename = $file->getFilename () . '.' .  $file->getClientOriginalExtension ();;
		$picture->save ();
		if (Input::hasFile('filefield'))
		{
			$extension = $file->getClientOriginalExtension();
			Storage::disk('local')->put($file->getFilename().'.'.$extension,  File::get($file));
		}
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('ids')));
	} 
	}
	
	
	public function getShowApi($facility_id,$id)
	{
		if((isset($facility_id) && is_numeric($facility_id))  && (isset($id) && is_numeric($id)) && Facility::where('id', $facility_id)->count())
		{
			if(Facilitydocument::where('id', $id )->where('facility_id', $facility_id)->count())
			{
				$picture = Facilitydocument::where ( 'id', '=', $id)->firstOrFail ();
				return Response::json(array('status'=>'success', 'message' => null, 'data' => compact('picture')));
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	} 

	public function getGetApi($filename)
	{
		$picture = Facilitydocument::where ( 'filename', '=', $filename )->firstOrFail ();
		return Response::json(array('status'=>'success', 'message' => null, 'data' => compact('picture')));
	}
	public function getDeleteApi($id)
	{
		$result = Facilitydocument::where('id',$id)->delete();
		
		if($result == 1){		
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
		}
		else{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.error_msg"),'data'=>''));
		}
	}
	
	function __destruct() 
	{
    }

}
