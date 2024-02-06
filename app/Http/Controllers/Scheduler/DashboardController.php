<?php namespace App\Http\Controllers\Scheduler;

use Auth;
use View;
use Redirect;
use Request;
use App\Http\Controllers\Controller;
use App\Models\Provider;

class DashboardController extends Controller {
    public function __construct()
    {
        View::share ( 'heading', 'Dashboard' );
        View::share ( 'selected_tab', 'dashboard' );
        View::share( 'heading_icon', 'fa-dashboard');
    }
    public function index()
    {        
        return view ( 'scheduler/dashboard/index' );
    }	
}