<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use App\Models\Note as Note;
use App\Models\Practice as Practice;
use App\Models\Facility as Facility;
use App\Models\Provider as Provider;  
use App\Models\Employer as Employer;
use App\Http\Helpers\Helpers as Helpers;
use Lang;
use DB;
use Illuminate\Support\Collection;

class NotesApiController extends Controller 
{
	/********************** Start Display a listing of the notes ***********************************/
	public function getIndexApi($type, $id='', $export = "")
	{
		$practice_timezone = Helpers::getPracticeTimeZone();	
		$type_details = '';
		if($type == 'practice')
		{
			$type_details = Practice::first();
			$id = $type_details->id;
		} 
		else 
		{
			$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
			if($type== 'facility') 
			{
				if(Facility::where('id', $id )->count())
				{
					$type_details = Facility::with('facility_address')->where('id',$id)->first();
				}
                else 
				{
					return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
                }
			}
			elseif($type== 'provider')
			{
				if(Provider::where('id', $id )->count())
				{
					$type_details = Provider::with('degrees')->where('id',$id)->first();
				}
                else 
				{
					return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
                }
            }
			elseif($type== 'employer')
			{
				if(Employer::where('id', $id )->count())
				{
					$type_details = Employer::where('id',$id)->first();
				}
				else
				{
					return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
				}
			}
		}
		$notes = Note::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('user')->where('notes_type',$type)->where('notes_type_id',$id)->orderBy('id','DESC')->get();
		if($export != "")
		{
			$exportparam 	= 	array(
								'filename'=>	'Notes',
								'heading'=>	$type .'notes',
								'fields' =>	array(
											'title' 	=> 'Title',
											'content' 	=> 'Content',
											'user' 		=>	array('table'=>'user' ,	'column' => 'short_name' ,	'label' => 'Created By')
											)
								);
                                        
			$callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $notes, $export);
		}	
		//Encode ID for type_details
		$temp = new Collection($type_details);
		$temp_id = $temp['id'];
		$temp->pull('id');
		$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
		$temp->prepend($temp_encode_id, 'id');
		$data = $temp->all();
		$type_details = json_decode(json_encode($data), FALSE);
		//Encode ID for type_details
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('notes','type_details')));
	}
	/********************** End Display a listing of the notes ***********************************/
	
	/********************** Start Display note created page ***********************************/
	public function getCreateApi($type,$id='')
	{
		$id = ($id !='') ? Helpers::getEncodeAndDecodeOfId($id,'decode') : '';
		$type_details = '';
		if($type == 'practice')
			$type_details = Practice::first();
		elseif($type== 'facility')
		{
			if(Facility::where('id',$id)->count())
			{
				$type_details = Facility::with('facility_address')->where('id',$id)->first();
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
			}
		}
		elseif($type== 'provider')
		{
            if(Provider::where('id',$id)->count())
			{
				$type_details = Provider::with('degrees')->where('id',$id)->first();
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
			}
		}	
        elseif($type== 'employer')
		{
			if(Employer::where('id', $id )->count())
			{
				$type_details = Employer::where('id',$id)->first();
			}
			else{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
			}
		}
		//Encode ID for type_details
		$temp = new Collection($type_details);
		$temp_id = $temp['id'];
		$temp->pull('id');
		$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
		$temp->prepend($temp_encode_id, 'id');
		$data = $temp->all();
		$type_details = json_decode(json_encode($data), FALSE);
		//Encode ID for type_details
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('type_details')));
	}
	/********************** End Display note created page ***********************************/
	
	/********************** Start note added process ***********************************/
	public function getStoreApi($type, $request='', $id='')
	{
		if($request == '')
			$request = Request::all();
		
		$validator = Validator::make($request, Note::$rules, Note::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{			
			$request['notes_type'] = $type;	
			$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
			if($type == 'practice')
			{
				$type_details = Practice::first();
				$id = $type_details->id;
			}
			if($type== 'facility')
			{
				if(Facility::where('id',$id)->count() == 0)
				{
					return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
				}
			}	
			elseif($type== 'provider')
			{
				if(Provider::where('id',$id)->count()== 0)
				{
					return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
				}
			}	
			elseif($type== 'employer')
			{
				if(Employer::where('id', $id )->count()== 0)
				{
					return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
				}
			}
			
			$request['notes_type_id'] = $id;	
			$request['created_by'] = Auth::user()->id;
			Note::create($request);
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.note_create_msg"),'data'=>''));
		}
	}
	/********************** End note added process ***********************************/
	
	/********************** Start Display note edit page ***********************************/
	public function getEditApi($type,$type_id,$id)
	{
		$id  		= Helpers::getEncodeAndDecodeOfId($id,'decode');
		$type_id 	= ($type_id !='') ? Helpers::getEncodeAndDecodeOfId($type_id,'decode') : '';
		
		if($type == 'practice')
			$type_details = Practice::first();
		elseif($type== 'facility')
		{
			if(Facility::where('id',$type_id)->count())
			{
				$type_details = Facility::with('facility_address')->where('id',$type_id)->first();
            }
			else
			{
				return Response::json(array('status'=>'failure_facility', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
            }
		}	
		elseif($type== 'provider')
		{
			if(Provider::where('id',$type_id)->count())
			{
				$type_details = Provider::with('degrees')->where('id',$type_id)->first();
            }
			else
			{
				return Response::json(array('status'=>'failure_provider', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
            }
		}	
		elseif($type== 'employer')
		{
			if(Employer::where('id', $type_id )->count())
			{
				$type_details = Employer::where('id',$type_id)->first();
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
			}
		}
		if(Note::where('id', $id)->count())
		{
			$notes = Note::where('id',$id)->first();
			//Encode ID for notes
			$temp = new Collection($notes);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$notes = json_decode(json_encode($data), FALSE);
			//Encode ID for notes
			//Encode ID for type_details
			$temp = new Collection($type_details);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$type_details = json_decode(json_encode($data), FALSE);
			//Encode ID for type_details
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('notes','type_details')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/********************** End Display note edit page ***********************************/
	
	/********************** Start note update process ***********************************/
	public function getUpdateApi($type, $request='', $type_id, $id)
	{ 
		$id  = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$type_id 	= ($type_id !='') ? Helpers::getEncodeAndDecodeOfId($type_id,'decode') : '';
		
		if($type== 'facility')
		{
			if(Facility::where('id',$type_id)->count() == 0)
			{
				return Response::json(array('status'=>'failure_facility', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
            }
		}	
		elseif($type== 'provider')
		{
			if(Provider::where('id',$type_id)->count()== 0)
			{
				return Response::json(array('status'=>'failure_provider', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
            }
		}	
		elseif($type== 'employer')
		{
			if(Employer::where('id', $type_id )->count()== 0)
			{
				return Response::json(array('status'=>'failure_employer', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
			}
		}
		
		if(Note::where('id', $id)->count())
		{
			if($request == '')
				$request = Request::all();
			$validator = Validator::make($request, Note::$rules , Note::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{			
				$notes = Note::find($id);
				$notes->update($request);
				$user = Auth::user ()->id;
				$notes->updated_by = $user;
				$notes->save ();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.note_update_msg"),'data'=>''));					
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/********************** End note update process ***********************************/
	
	/********************** Start note deleted process ***********************************/
	public function getDeleteApi($type,$id,$type_id='')
	{
		$id  = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$type_id 	= ($type_id !='') ? Helpers::getEncodeAndDecodeOfId($type_id,'decode') : '';
		
		if($type== 'facility')
		{
			if(Facility::where('id',$type_id)->count() == 0)
			{
				return Response::json(array('status'=>'failure_facility', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
            }
		}	
		elseif($type== 'provider')
		{
			if(Provider::where('id',$type_id)->count()== 0)
			{
				return Response::json(array('status'=>'failure_provider', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
            }
		}	
		elseif($type== 'employer')
		{
			if(Employer::where('id', $type_id )->count()== 0)
			{
				return Response::json(array('status'=>'failure_employer', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
			}
		}
		
		if(Note::where('id', $id)->count())
		{
			Note::where('notes_type',$type)->where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.note_delete_msg"),'data'=>''));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}	
	}
	/********************** End note deleted process ***********************************/
	
	function __destruct() 
	{
    }
	
}
