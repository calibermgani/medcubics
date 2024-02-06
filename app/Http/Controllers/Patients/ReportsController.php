<?php namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use Auth;
use View;
use Input;
use Session;
use Request;
use Response;
use Redirect;
use Validator;

class ReportsController extends Controller {

    public function __construct() {
        View::share('heading', 'Reports');
        View::share('selected_tab', 'reports');
        View::share('heading_icon', 'fa-sticky-note');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {        
        return view('patients/reports/index');
    }
    
}
