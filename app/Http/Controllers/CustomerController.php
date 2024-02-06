<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use App\Note as Note;
use App\Customer as Customer;

use Input;
use Validator;
use Redirect;
use Auth;
use Session;

class CustomerController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
                $customers = Customer::all();
		 return view('admin/customer/customerlist',compact('customers'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$customers = Customer::all();
                return view('admin/customer/create',  compact('customers'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$customers = Customer::create(Request::all());
		$customers->save();
                Session::flash('flash_msg','Customer Created Successfully');
		return redirect('admin');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
	$customer = Customer::find($id);
        return view('admin/customer/show', compact('customer'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		
		$customers = Customer::findOrFail($id);		
		return view('admin/customer/edit', compact('customers'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$customers = Customer::findOrFail($id);
        $customers->update(Request::all());
        Session::flash('flash_msg','Customer Updates Successfully');
        return redirect('admin');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//$customers = Customer::withTrashed()->where('id', $id)->get();
                Customer::find($id)->delete();
                Session::flash('flash_msg','Customer Deleted Successfully');
                return redirect('admin');
	}

}
