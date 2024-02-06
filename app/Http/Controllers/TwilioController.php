<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use View;

class TwilioController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}
	public function create()
	{
		//
	}
	public function callandmessage($phone_number)
	{
		$phone_number = base64_decode($phone_number);
		View::share('phone_number', $phone_number);
		$image_tag = "https://clouddesigners.in/medcubic/img/patient_noimage.png";
		$allCalls = $messageList  = [];
		return view('layouts/twilio',compact('image_tag','allCalls','messageList'));
	}
	
	public function destroy($id)
	{
		//
	}

}
