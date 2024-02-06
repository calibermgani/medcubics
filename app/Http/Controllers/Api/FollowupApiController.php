<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FollowupCategory as FollowupCategory;
use App\Models\FollowupQuestion as FollowupQuestion;
use Input;
use File;
use Auth;
use Response;
use Request;
use Validator;
use Schema;
use DB;
use Config;
use App\Models\Document as Document;
use App\Http\Helpers\Helpers as Helpers;
use Session;
use Lang;

class FollowupApiController extends Controller 
{
	public function getAllCategoryApi()
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$category = FollowupCategory::with(['question' => function($query)use($practice_timezone){
					$query->select("*",DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'));
				}])->where('deleted_at',null)->select('name','id','status',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->get()->toArray();
		$categorylist = FollowupCategory::where('deleted_at',null)->pluck('name','id')->all();
		return Response::json(array('status' => 'success', 'message' => null,'data'=>compact('category','categorylist')));
	}
	
	public function getViewCategoryApi($id){
		$category = FollowupCategory::where('deleted_at',null)->where('id',$id)->select('name','id','status')->first();
		return Response::json(array('status' => 'success', 'message' => null,'data'=>compact('category')));
	}
	
	public function getCreateCategoryApi()
	{
		DB::beginTransaction();
		try{
			$request = Request::all();
			$data['name'] = trim($request['category']);
			$category_count = FollowupCategory::where('name',$request['category'])->get()->count();
			if($category_count == 0){
				$remove[] = "'";
				$remove[] = '"';
				$remove[] = "-"; 
				$remove[] = "/"; 
				$remove[] = "("; 
				$remove[] = ")"; 
				$remove[] = " "; 
				$data['label_name'] = str_replace($remove,"_",trim($request['category']));
				$data['status'] = $request['status'];
				$category = FollowupCategory::create($data);
				$catgory_id = $category->id;
				$dataArr['question'] = trim($request['question']);
				$removes = "'";
				$dataArr['question_label'] = str_replace($removes,"",$dataArr['question']);
				$removes = '"';
				$dataArr['question_label'] = str_replace($removes,"",$dataArr['question']);
				$removeArr[] = "-"; 
				$removeArr[] = "/"; 
				$removeArr[] = "("; 
				$removeArr[] = ")"; 
				$removeArr[] = " "; 
				$dataArr['question_label'] = str_replace($removeArr,"_",$dataArr['question']);
				$dataArr['category_id'] = trim($catgory_id);
				$dataArr['field_type'] = trim($request['field_type']);
				$dataArr['field_validation'] = trim($request['field_validation']);
				$dataArr['hint'] = trim($request['hint']);
				$data['user_id'] = Auth::user()->id;
				$dataArr['date_type'] = trim($request['date_type']);
				$dataArr['status'] = trim($request['status']);
				FollowupQuestion::create($dataArr);
				DB::commit();
				$status = 'Success';
				$message = 'New category added successfully';
			}else{
				$status = 'Error';
				$message = 'This type of category already available';
			}
		}catch(\Exception $e){
			$status = 'Error';
			$message = $e->getMessage();
			DB::rollback();
		}
		return Response::json(array('status' => $status, 'message' => $message,'data'=>''));
	}
	
	public function getEditCategoryApi()
	{		
		DB::beginTransaction();
		try{
			$request = Request::all();
			$category_count = FollowupCategory::where('name',$request['category'])->where('id','!=',$request['id'])->count();
			if($category_count == 0){
				$data['name'] = trim($request['category']);
				$remove[] = "'";
				$remove[] = '"';
				$remove[] = "-"; 
				$remove[] = "/"; 
				$remove[] = "("; 
				$remove[] = ")"; 
				$remove[] = " "; 
				$data['label_name'] = str_replace($remove,"_",trim($request['category']));
				$data['status'] = $request['status'];
				$data['updated_at'] = date('Y-m-d H:i:s');
				FollowupCategory::where('id',$request['id'])->update($data);
				DB::commit();
				$status = 'Success';
				$message = 'New category updated successfully';
			}else{
				$status = 'Error';
				$message = 'This type of category already available';
			}
		}catch(\Exception $e){
			$status = 'Error';
			$message = $e->getMessage();
			DB::rollback();
		}
		return Response::json(array('status' => $status, 'message' => $message,'data'=>''));
	}
	
	public function getCategoryApi(){ 
		$category = FollowupCategory::where('deleted_at',null)->pluck('name','id')->all();
		return Response::json(array('status' => 'success', 'message' => null,'data'=>compact('category')));
	}
	
	public function getEediCategoryApi($id){ 
		$category = FollowupCategory::where('deleted_at',null)->pluck('name','id')->all();
		$question = FollowupQuestion::where('id',$id)->get()->toArray();
		return Response::json(array('status' => 'success', 'message' => null,'data'=>compact('category','question')));
	}
	
	
	public function getAllQuestionApi(){
		$question = FollowupQuestion::with('category')->where('deleted_at',null)->select('id','question','created_at','category_id','field_type','field_validation','date_type')->get();
		return Response::json(array('status' => 'success', 'message' => null,'data'=>compact('question')));
	}
	
	public function getCategoryQuestionApi($id){
		$question = FollowupQuestion::with('category')->where('deleted_at',null)->where('category_id',$id)->select('id','question','created_at','category_id','field_type','field_validation','date_type')->get();
		return Response::json(array('status' => 'success', 'message' => null,'data'=>compact('question')));
	}
	
	
	public function getCreateQuestionApi()
	{
		DB::beginTransaction();
		try{
			$request = Request::all();
			$question_count = FollowupQuestion::where('question',$request['question'])->where('category_id',trim($request['category']))->get()->count();
			if($question_count == 0){
				$data['question'] = trim($request['question']);
				$removes = "'";
				$data['question_label'] = str_replace($removes,"",$data['question']);
				$removes = '"';
				$data['question_label'] = str_replace($removes,"",$data['question']);
				$remove[] = "-"; 
				$remove[] = "/"; 
				$remove[] = "("; 
				$remove[] = ")"; 
				$remove[] = " "; 
				$data['question_label'] = str_replace($remove,"_",$data['question']);
				$data['category_id'] = trim($request['category']);
				$data['field_type'] = trim($request['field_type']);
				$data['field_validation'] = trim($request['field_validation']);
				$data['date_type'] = trim($request['date_type']);
				$data['hint'] = trim($request['hint']);
				$data['user_id'] = Auth::user()->id;
				$data['status'] = trim($request['status']);
				FollowupQuestion::create($data);
				DB::commit();
				$status = 'Success';
				$message = 'Question addded successfully';
			}else{
				$status = 'Error';
				$message = 'Question Already Available';
			}
		}catch(\Exception $e){
			$status = 'Error';
			$message = $e->getMessage();
			DB::rollback();
		}
		return Response::json(array('status' => $status, 'message' => $message,'data'=>''));
	}
	
	public function getEditQuestionApi()
	{
		DB::beginTransaction();
		try{
			$request = Request::all();
			$question_count = FollowupQuestion::where('question',$request['question'])->where('id','!=',$request['id'])->where('category_id',trim($request['category']))->get()->count();
			if($question_count == 0){
				$data['question'] = trim($request['question']);
				$removes = "'";
				$data['question_label'] = str_replace($removes,"",$data['question']);
				$removes = '"';
				$data['question_label'] = str_replace($removes,"",$data['question']);
				$remove[] = "-"; 
				$remove[] = "/"; 
				$remove[] = "("; 
				$remove[] = ")"; 
				$remove[] = " "; 
				$data['question_label'] = str_replace($remove,"_",$data['question']);
				$data['category_id'] = trim($request['category']);
				$data['field_type'] = trim($request['field_type']);
				$data['field_validation'] = trim($request['field_validation']);
				if(trim($request['field_type']) == 'date')
					$data['field_validation'] = '';
				$data['date_type'] = trim($request['date_type']);
				$data['hint'] = trim($request['hint']);
				$data['user_id'] = Auth::user()->id;
				$data['status'] = trim($request['status']);
				FollowupQuestion::where('id',$request['id'])->update($data);
				DB::commit();
				$status = 'Success';
				$message = 'Question updated successfully';
			}else{
				$status = 'Error';
				$message = 'Question Already Available';
			}
		}catch(\Exception $e){
			$status = 'Error';
			$message = $e->getMessage();
			DB::rollback();
		}
		return Response::json(array('status' => $status, 'message' => $message,'data'=>''));
	}
}
