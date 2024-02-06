<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Http\Controllers\Api\CommonExportApiController;
use App\Models\QuestionnariesTemplate as QuestionnariesTemplate;
use App\Models\QuestionnariesOption as QuestionnariesOption;
use App\Models\QuestionnariesAnswer as QuestionnariesAnswer;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Response;
use Request;
use Validator;
use Lang;

class QuestionnariesTemplateApiController extends Controller {

	public function getIndexApi($export='')
	{
		
		//Get Questionnaries list query start
		$questionnaries 		= QuestionnariesTemplate::with('creator','modifier')->orderBy("created_at","DESC")->groupBy('template_id')->get(); 
		
		/*** Export option starts here ***/
		if($export != "")
		{   
			$exprt = array(
						'filename'	=>	'Questionnaries List',
						'heading'	=>	'',
						'fields' 	=>	array('title'=>	'Title',
									'Created By'	=>	array('table' =>'creator','column' => 'short_name','label' =>'Created By'),
									'created_at'	=>	 "Created On",
									'Updated By'	=>	array('table' =>'modifier','column' => 'short_name','label' => 'Updated By'),
									'updated_at'	=>	'Updated On'));		
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exprt, $questionnaries, $export); 
		}
		/*** Export option ends here ***/
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('questionnaries')));
	}
	
	/*** Store Function Start ***/
	public function getStoreApi($request='')
	{	
		if($request == '')
			$request 	= Request::all();
		if((isset($request['ques'])) && (isset($request['ans'])) && (isset($request['ans_values'])))
		{
			$request['question'] = $request['ques'];
			$request['input_type'] = $request['ans'];
			$request['input_type_values'] = $request['ans_values'];
			unset($request['ques']);
			unset($request['ques_answer']);
			unset($request['ans']);
			unset($request['ans_values']);
			if(isset($request['text'])) 
				unset($request['text']);
			if(isset($request['radio'])) 
				unset($request['radio']);
			if(isset($request['checkbox'])) 
				unset($request['checkbox']);
			
				
			$validation_rules =	[
				'title' => 'required|unique:questionnaries_template,title,NULL,id,deleted_at,NULL',
				'question' => 'required',
			];
			$validator = Validator::make($request, $validation_rules, QuestionnariesTemplate::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$request['user']= Auth::user()->id;
				$request['template_id']=QuestionnariesTemplate::getLastid();//Getting last id
				$result = $this->storeNewValue($request);
				$ques_form_id  = Helpers::getEncodeAndDecodeOfId($result,'encode');
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$ques_form_id));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.error_msg"),'data'=>'null'));
		}
	}
	/*** Store Function End ***/
	
	/*** Show Function Start ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		
		if(QuestionnariesTemplate::where('template_id', $id)->count()>0 && is_numeric($id)==1)
		{
			$questionaries = QuestionnariesTemplate::with('questionnaries_option','creator','modifier')->where('template_id', $id)->orderBy('question_order', 'ASC')->get();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('questionaries')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** Show Function End ***/
	
	/*** Store access Function Start ***/
	public function storeNewValue($request)
	{
		$res =[];
		$user =$request['user'];
		$res['title']=$request['title'];
		$res['template_id']=$request['template_id'];
		$res['created_by']=$user;
		foreach($request['order'] as $order => $id)
		{
			$res['question'] = $request['question'][$id]; 
			$res['answer_type'] = $request['input_type'][$id]; 
			$res['question_order'] = $order; 
			$res['created_at'] = date('Y-m-d h:i:s');
			$data = QuestionnariesTemplate::create($res);
			$questionnaries_id = $data->id;
			$ques_form_id = $data->template_id;
			$option = explode(",",$request['input_type_values'][$id]);
			foreach($option as $key => $val)
			{
				$value = explode("::",$val);
				$ans_insert = QuestionnariesOption::create(['template_id'=>$ques_form_id,'questionnaries_template_id'=>$questionnaries_id,'option'=>$value[1],'created_by'=>$user]);
			}
		}
		return $ques_form_id;
	}
	/*** Show Function End ***/
	
	/*** Edit page Start ***/ 
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(QuestionnariesTemplate::where('template_id', $id)->count()>0 && is_numeric($id)==1)
		{
			$questionaries 	= 	QuestionnariesTemplate::with('questionnaries_option')->where('template_id', $id)->orderBy('question_order', 'ASC')->get();
			$questionaries_title =$questionaries[0]->title;
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('questionaries','questionaries_title')));
		}
		else
		{
			$title 	= 	QuestionnariesTemplate::withTrashed()->with('questionnaries_option')->where('template_id', $id)->orderBy('deleted_at', 'DESC')->value('title');
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>$title));
		}
	}
	/*** Edit page End ***/
	
	/*** Update Function Start ***/
	public function getUpdateApi($temp_id)
	{
		$template_id = Helpers::getEncodeAndDecodeOfId($temp_id,'decode');
		$request 	= Request::all();
		$request['question'] = $request['ques'];
		$request['input_type'] = $request['ans'];
		$request['input_type_values'] = $request['ans_values'];
		unset($request['ques']);
		unset($request['ques_answer']);
		unset($request['ans']);
		unset($request['ans_values']);
		if(isset($request['text'])) 
			unset($request['text']);
		if(isset($request['radio'])) 
			unset($request['radio']);
		if(isset($request['checkbox'])) 
			unset($request['checkbox']);
		$rules = array('title' => 'required|unique:questionnaries_template,title,'.$template_id.',template_id,deleted_at,NULL');
		$messages = array('title.unique' 	=> Lang::get("practice/practicemaster/questionnaries.validation.questionnaries_unique"));
		
		$validator 		= Validator::make($request,$rules,$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$user = Auth::user()->id;
			$send_request = [];
			
			foreach($request['order'] as $order => $id)
			{
				if(strpos($id, 'new') == false && QuestionnariesTemplate::where('id',$id)->count())
				{
					$ques_tem = QuestionnariesTemplate::where('id',$id)->where('template_id',$template_id);
					$ques_tem->update(["title"=>$request['title'],"question"=>$request['question'][$id],"answer_type"=>$request['input_type'][$id],"question_order"=>$order,"updated_by"=>$user,"updated_at"=>date('Y-m-d h:i:s')]);
					
					$option = explode(",",$request['input_type_values'][$id]);
					foreach($option as $chk_option)
					{
						$option_val = explode("::",$chk_option);
						if(strpos($option_val[0],'add') == false && QuestionnariesOption::where('id',$option_val[0])->count())
						{
							$ques_opt = QuestionnariesOption::where('id',$option_val[0])->where('template_id',$template_id);
							$ques_opt->update(['questionnaries_template_id'=>$id,'option'=>$option_val[1],'updated_by'=>$user]);
						}
						else 
						{
							$ans_insert = QuestionnariesOption::create(['template_id'=>$template_id,'questionnaries_template_id'=>$id,'option'=>$option_val[1],'created_by'=>$user]);
						}
					}
				}
				else
				{
					$send_request['title'] = $request['title'];
					$send_request['order'][$order] = $order;
					$send_request['question'][$order] = $request['question'][$id];
					$send_request['input_type'][$order] = $request['input_type'][$id];
					$send_request['input_type_values'][$order] = $request['input_type_values'][$id];
					$send_request['template_id'] = $template_id;
					$send_request['user'] = $user;
				}
			}
			$result = (count($send_request)>0) ? $this->storeNewValue($send_request):1;
			if($result)
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>$temp_id));
			else
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.error_msg"),'data'=>'null'));
		}
	}
			
	/*** Update Function End ***/
	
	/*** Validation starts here ***/
	public function getValidationApi($title,$id)
	{
		$id = ($id !="template") ? Helpers::getEncodeAndDecodeOfId($id,'decode'):'NULL';
		$rules = array('title' => 'required|unique:questionnaries_template,title,'.$id.',template_id,deleted_at,NULL');
		$messages = array('title.unique' 	=> Lang::get("practice/practicemaster/questionnaries.validation.title_unique"));
		$request['title'] = $title;
		$validator 		= Validator::make($request, $rules, $messages);
		if ($validator->fails())
		{
			$errors = $validator->errors()->toArray();
			$msg =  $errors['title'][0];	
			return $msg;	
		}
		else 
		{
			return 0;
		}
	}
	/*** Validation end here ***/
	
	/*** Delete Function Start ***/
	public function getDestroyApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		//Checking whether it is used or not in this form
		if(QuestionnariesAnswer::where('template_id',$id)->count())
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.questionnaire_alert_msg"),'data'=>''));	
		}
		else
		{
			$ques_tem = QuestionnariesTemplate::where('template_id',$id)->delete();
			$ques_tem = QuestionnariesOption::where('template_id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
		}
		
	}
	/*** Delete Function End ***/
	
	/*** Individual Records Delete Ajax Function Start ***/
	public function getQuesansdeleteApi($request='')
	{
		$request 	= Request::all();
		$id = $request['delete_id'];
		$delete_from = $request['delete_from'];
		if($delete_from =="delete_question")
		{
			//Checking whether it is used or not in this form
			if(QuestionnariesAnswer::where('questionnaries_template_id',$id)->count())
			{
				$msg = "alert-danger::::".Lang::get("common.validation.questionnaire_alert_msg");
			}
			else
			{
				$ques_tem = QuestionnariesTemplate::where('id',$id)->delete();
				$ques_tem = QuestionnariesOption::where('questionnaries_template_id',$id)->delete();
				$msg = "alert-success::::".Lang::get("common.validation.delete_msg");
			}
		}
		elseif($delete_from =="delete_single_option")
		{
			//Checking whether it is used or not in this form
			if(QuestionnariesAnswer::where('questionnaries_option_id',$id)->count())
			{
				$msg = "alert-danger::::".Lang::get("common.validation.questionnaire_alert_msg");
			}
			else
			{
				$ques_tem = QuestionnariesOption::where('id',$id)->delete();
				$msg = "alert-success::::".Lang::get("common.validation.delete_msg");
			}
		}
		elseif($delete_from =="delete_all_option")
		{
			//Checking whether it is used or not in this form
			if(QuestionnariesAnswer::where('questionnaries_option_id',$id)->count())
			{
				$msg = "alert-danger::::".Lang::get("common.validation.questionnaire_alert_msg");
			}
			else
			{
				$ques_tem = QuestionnariesOption::where('questionnaries_template_id',$id)->delete();
				$msg = "alert-success::::".Lang::get("common.validation.delete_msg");
			}
		}
		return Response::json(array('status'=>'success', 'message'=>$msg,'data'=>''));	
	}
	/*** Delete Individual Records Delete Ajax Function End ***/
	function __destruct() 
	{
    }
}
