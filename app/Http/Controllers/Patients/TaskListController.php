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

class TaskListController extends Controller {

    public function __construct() {
        View::share('heading', 'Task List');
        View::share('selected_tab', 'tasklist');
        View::share('heading_icon', 'fa-tasks');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {        
        return view('patients/tasklist/index');
    }
    
}
