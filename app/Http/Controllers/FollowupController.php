<?php namespace App\Http\Controllers;

use Request; 
use Input;
use Validator;
use Redirect;
use Auth;
use Session;
use View;
use DB;
use Config;


class FollowupController extends Api\FollowupApiController 
{
    public function __construct() 
    {   //Tab selection, Heading, and Icon show  
        View::share('heading','Practice');   
        View::share('selected_tab','followup');
        View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
    }
	
    /*** Display a listing of the resource. ***/
	
    public function index()
    { 	
		$api_response = $this->getAllCategoryApi();
		$api_response_data = $api_response->getData();
		$category = $api_response_data->data->category;
		$categorylist = $api_response_data->data->categorylist;
		return view('practice/followup/followup',compact('category','categorylist'));	
    }
	
	public function create_category()
	{
		return view('practice/followup/create');	
	}
	
	public function store_category()
	{
		$api_response = $this->getCreateCategoryApi();
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'Success'){
			return Redirect::to('followup/category')->with('success',$api_response_data->message);
		}elseif($api_response_data->status == 'Error'){
			return Redirect::to('followup/category')->with('error',$api_response_data->message);
		}
	}
	
	public function view_category($id)
	{
		$api_response = $this->getViewCategoryApi($id);
		$api_response_data = $api_response->getData();
		$category = $api_response_data->data->category;
		return view('practice/followup/edit',compact('category'));	
	}
	
	public function edit_category(){
		$api_response = $this->getEditCategoryApi();
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'Success'){
			return Redirect::to('followup/category')->with('success',$api_response_data->message);
		}elseif($api_response_data->status == 'Error'){
			return Redirect::to('followup/category')->with('error',$api_response_data->message);
		}
	}
	
	
	
	public function question()
	{	
		$api_response = $this->getAllQuestionApi();
		$api_response_data = $api_response->getData();
		$question = $api_response_data->data->question;
		return view('practice/followup/question',compact('question'));	
	}
	
	public function create_question()
	{
		$api_response = $this->getCategoryApi();
		$api_response_data = $api_response->getData();
		$category = $api_response_data->data->category;
		return view('practice/followup/create-question',compact('category'));
	}
	
	public function store_question()
	{
		$api_response = $this->getCreateQuestionApi();
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'Success'){
			return Redirect::to('followup/category')->with('success',$api_response_data->message);
		}elseif($api_response_data->status == 'Error'){
			return Redirect::to('followup/category')->with('error',$api_response_data->message);
		}
	}
	
	public function view_question($id)
	{
		$api_response = $this->getEediCategoryApi($id);
		$api_response_data = $api_response->getData();
		$category = $api_response_data->data->category;
		$question = $api_response_data->data->question;
		
		return view('practice/followup/edit_question',compact('question','category'));
	}
	
	public function edit_question(){
		$api_response = $this->getEditQuestionApi();
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'Success'){
			return Redirect::to('followup/category')->with('success',$api_response_data->message);
		}elseif($api_response_data->status == 'Error'){
			return Redirect::to('followup/category')->with('error',$api_response_data->message);
		}
	}
	
	
	public function category_question($id){
		$api_response = $this->getCategoryQuestionApi($id);
		$api_response_data = $api_response->getData();
		$question = $api_response_data->data->question;
		return view('practice/followup/question',compact('question'));	
	}

    
}
