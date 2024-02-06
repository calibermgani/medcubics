<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Request;
use Validator;
use App\Models\Medcubics\Users as User;
use App\Models\Medcubics\UsersAppDetails as UsersAppDetails;
use Hash;
use Response;

class AdminPasswordApiController extends Controller {

	public function postchangepasswordApi()
	{
		$request = Request::all();
		$db_pwd = Auth::user()->password;
		$db_email = Auth::user()->email;
		$validation_rule = array('cpassword' => 'required','password' => 'required|min:6|same:password', 'con_password' => 'required|min:6|same:password');
		$validator = Validator::make($request, $validation_rule, User::messages());
		//Data base value and current password is same
		$table_val=Hash::check( $request['cpassword'],$db_pwd);
		##validation error or Not error##
		if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));
			}
		else
		{
			if($table_val== true){ 
			$user1 = User::where('email',$db_email)->first();
			##update in databse##
			$user1->update(['password'=>Hash::make($request['password']),'password_change_time'=>date('Y-m-d H:i:s')]);
			UsersAppDetails::where('user_id',$user1['id'])->update(['authentication_id'=>'']);
			return Response::json(array('status'=>'success', 'message'=>'password update successfully','data'=>''));
			}
			else{ 
				##Erorr status##
				return Response::json(array('status'=>'error', 'message'=>'Old password is not match','data'=>''));
			}
		}
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
