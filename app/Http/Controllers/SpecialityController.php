<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Speciality as Speciality;

use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use Session;

class SpecialityController extends Controller {
	
    /////////////////////////////////
	
	public function index()
	{
		$specialities = Speciality::all();
		//return $specialities;
       return view('speciality.add_speciality',  compact('specialities'));
		//return view('speciality.add_speciality');
	}

    ////////////////////////
	
	public function create()
	{
		 return view('speciality.create_speciality');
	}

    /////////////////////////////////
	
	public function store()
	{
				Speciality::create(Request::all());
                return redirect('speciality');
	}

    /////////////////////////////////
	
	public function show($id)
	{
		//
	}
	
    /////////////////////////////////
	 
	public function edit($id)
	{
		$speciality = Speciality::findOrFail($id);
		return view('speciality.edit_speciality', compact('speciality'));
	}

    /////////////////////////////////
	
	public function update($id)
	{
		$speciality = Speciality::findOrFail($id);
        $speciality->update(Request::all());
        return redirect('speciality');
	}

    /////////////////////////////////
	
	public function destroy($id)
	{
		Speciality::find($id)->delete();
       return redirect('speciality');
           
	}
}
