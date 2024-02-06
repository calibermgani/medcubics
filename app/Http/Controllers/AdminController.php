<?php namespace App\Http\Controllers;

class AdminController extends Controller {

	
	public function index()
	{
		return view('pages/admin-user/practice');
	}  
        
        public function customer_add()
	{
		return view('pages/admin-user/create');
	} 
        
        public function practice_add()
	{
		return view('pages/admin-user/createpractice');
	} 
        
        public function user_add()
	{
		return view('pages/admin-user/createuser');
	} 
        
        public function practice_view()
	{
		return view('pages/admin-user/practicelist');
	} 
        
         public function user_view()
	{
		return view('pages/admin-user/userlist');
	} 
        
        public function customer_view()
	{
		return view('pages/admin-user/customerlist');
	} 
        
        public function overview()
	{
		return view('pages/admin-user/overview');
	} 
       
	
}