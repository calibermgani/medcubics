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
use PDF;
use Excel;
use App\Exports\BladeExport;

class FacilityController extends Api\FacilityApiController 
{
    public function __construct() 
    {   //Tab selection, Heading, and Icon show  
        View::share('heading','Practice');   
        View::share('selected_tab','facility');
        View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
    }
	
    /*** Display a listing of the resource. ***/
    public function index()
    { 
		//Connection to FacilityApiController
        $api_response       = $this->getIndexApi();
		//Data redrive from FacilityApiController
        $api_response_data  = $api_response->getData();		
        $facilitymodule     = $api_response_data->data->facilitymodule;
		$speciality   		 = $api_response_data->data->speciality;
		$pos     			= $api_response_data->data->pos;

		if(Request::ajax())
        {           
            return view('practice/facility/facility_list',compact('facilitymodule','speciality','pos'));
        }
		else
		{
			return view('practice/facility/facility',compact('facilitymodule','speciality','pos'));	
		}
    }
    
    public function getFacilityExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $facilitymodule = $api_response_data->data->facilitymodule;
        $speciality = $api_response_data->data->speciality;
        $pos = $api_response_data->data->pos;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Facility_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/facility/facility_export_pdf', compact('facilitymodule', 'speciality', 'pos', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/facility/facility_export';
            $data['facilitymodule'] = $facilitymodule;
            $data['speciality'] = $speciality;
            $data['pos'] = $pos;
            $data['export'] = $export;
            ob_end_clean();
            ob_start();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/facility/facility_export';
            $data['facilitymodule'] = $facilitymodule;
            $data['speciality'] = $speciality;
            $data['pos'] = $pos;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	
	

    /*** Show the form for creating a new resource.  @return Response  ***/
    public function create()
    {	
		//Connection to FacilityApiController
        $api_response          = $this->getCreateApi();
		//Data redrive from FacilityApiController
        $api_response_data     = $api_response->getData();
        $facility              = $api_response_data->data->facility;
        $specialities          = $api_response_data->data->specialities;
        $speciality_id         = $api_response_data->data->speciality_id;
        $taxanomies            = $api_response_data->data->taxanomies;
        $taxanomy_id           = $api_response_data->data->taxanomy_id;
        $county                = $api_response_data->data->county;
        $facilityaddress       = $api_response_data->data->facilityaddress;
        $providers             = $api_response_data->data->providers;
        $default_provider_id   = $api_response_data->data->default_provider_id;
        $pos                   = $api_response_data->data->pos;
        $pos_id                = $api_response_data->data->pos_id;
        $address_flags         = (array)$api_response_data->data->addressFlag;
        $address_flag['general']= (array)$address_flags['general'];
        $npi_flag               = (array)$api_response_data->data->npi_flag;
        $time                   = (array)$api_response_data->data->time;
		$claimformats 			= (array)$api_response_data->data->claimformats;
		
        return view('practice/facility/create',compact('claimformats','facility','heading','county','specialities','taxanomies','taxanomy_id','default_provider_id','speciality_id','pos_id','providers','facilityaddress','pos','address_flag','npi_flag','time'));
    }

    /*** Store a newly created resource in storage. @return Response  ***/
    public function store(Request $request)
    { 
        $api_response           = $this->getStoreApi($request::all());
		//Data redrive from FacilityApiController->getStoreApi
        $api_response_data      = $api_response->getData();
        if($api_response_data->status == 'success')
        {
            return Redirect::to('facility/'.$api_response_data->data)->with('success', $api_response_data->message);
        }
        else
        {
            return Redirect::to('facility/create')->withInput()->withErrors($api_response_data->message);
        }	 
    }

    /*** Display the specified resource.  ***/
    public function show($id)
    {
		$api_response            = $this->getShowApi($id);
		//Data redrive from FacilityApiController->getShowApi
        $api_response_data       = $api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			$facility                = $api_response_data->data->facility;
			$address_flags           = (array)$api_response_data->data->addressFlag;
			$address_flag['general'] = (array)$address_flags['general'];
			$npi_flag                = (array)$api_response_data->data->npi_flag;
			$claimformats 			= json_decode(json_encode($api_response_data->data->claimformats), True);
            $documents_fda              = $api_response_data->data->documents_fda;
            $documents_npi              = $api_response_data->data->documents_npi;
            $documents_clia_id          = $api_response_data->data->documents_clia_id;
            $documents_tax_id           = $api_response_data->data->documents_tax_id;
			return view('practice/facility/show',compact('claimformats','facility','address_flag','npi_flag','documents_fda','documents_npi','documents_clia_id','documents_tax_id'));	
		}
		else
		{
			return Redirect::to('facility')->with('error',$api_response_data->message);
		}	
    }

    /*** Show the form for editing the specified resource. ***/
    public function edit($id)
    {		
       
        $api_response               = $this->getEditApi($id);
		//Data redrive from FacilityApiController->getEditApi
        $api_response_data          = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$facility                   = $api_response_data->data->facility;
			$providers                  = $api_response_data->data->providers;
			$default_provider_id        = $api_response_data->data->default_provider_id;
			$county                     = $api_response_data->data->county;
			$specialities               = $api_response_data->data->specialities;
			$speciality_id              = $api_response_data->data->speciality_id;
			$taxanomies                 = $api_response_data->data->taxanomies;
			$taxanomy_id                = $api_response_data->data->taxanomy_id;
			$facilityaddress            = $api_response_data->data->facilityaddress;		
			$pos                        = $api_response_data->data->pos;
			$pos_id                     = $api_response_data->data->pos_id;		
			$address_flags              = (array)$api_response_data->data->addressFlag;
			$claimformats			= $api_response_data->data->claimformats;
			$address_flag['general']    = (array)$address_flags['general'];
			$npi_flag                   = (array)$api_response_data->data->npi_flag;	
			$time                       = (array)$api_response_data->data->time;
            $documents_fda              = $api_response_data->data->documents_fda;
            $documents_npi              = $api_response_data->data->documents_npi;
            $documents_clia_id          = $api_response_data->data->documents_clia_id;
            $documents_tax_id           = $api_response_data->data->documents_tax_id;		
            return view('practice/facility/edit',compact('claimformats','facility','heading','providers','pos_id','default_provider_id','county','specialities','speciality_id','taxanomies','taxanomy_id','facilityaddress','pos','address_flag','npi_flag','time','documents_fda','documents_npi','documents_clia_id','documents_tax_id'));
		}
		else
		{
			return Redirect::to('facility')->with('error',$api_response_data->message);
		}	
    }

    /*** Update the specified resource in storage.  ***/
    public function update($id, Request $request)
    { 
        $api_response       = $this->getUpdateApi(Request::all(), $id);
		//Data redrive from FacilityApiController->getUpdateApi
        $api_response_data  = $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
		
        if($api_response_data->status == 'success')
        {
            return Redirect::to('facility/'.$id)->with('success',$api_response_data->message);
        }
        else
        {
            return Redirect::to('facility/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
        }	
    }

    /*** Remove the specified resource from storage. ***/
    public function destroy($id)
    {	
        $api_response      = $this->getDeleteApi($id);
		//Data redrive from FacilityApiController->getDeleteApi
        $api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
        return Redirect::to('facility')->with('success',$api_response_data->message);     
    }
	public function avatarfacility($id,$picture_name)
	{
		$api_response 		= $this->avatarapipicture($id,$picture_name);
		//Data redrive from FacilityApiController->avatarapipicture
		$api_response_data 	= $api_response->getData();
		return Redirect::to('facility/'.$id.'/edit')->with($api_response_data->message);
	}
}
