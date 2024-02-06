<?php 
namespace App\Http\Controllers;
use View;
use Input;
use Request;
use Redirect;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class InsuranceAppealAddressController extends Api\InsuranceAppealAddressApiController 
{
    public function __construct(Request $request) 
    {
        View::share( 'heading', 'Practice' );
        View::share( 'selected_tab', 'insurance' );
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }
	
	/*** Start to listing the Appeal Address  ***/
    public function index($id)
    {
        $api_response = $this->getIndexApi($id);
        $api_response_data = $api_response->getData();

        if($api_response_data->status == 'success')
        {
			$insurance = $api_response_data->data->insurance;
			 $appealaddress = $api_response_data->data->appealaddress;
			return view('practice/insurance/appealaddress/appealaddress', compact('appealaddress','insurance'));
		}
        else
        {
             return redirect('/insurance')->with('error',$api_response_data->message);
        }
    }
    /*** End to listing the Appeal Address  ***/
	
	public function appealAddressExport($id = '', $export='') {
        $api_response = $this->getIndexApi($id);
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $appealaddress = $api_response_data->data->appealaddress;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Insurance_Appeal_Address_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/insurance/appealaddress/appealaddress_export_pdf', compact('insurance', 'appealaddress', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/insurance/appealaddress/appealaddress_export';
            $data['insurance'] = $insurance;
            $data['appealaddress'] = $appealaddress;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/insurance/appealaddress/appealaddress_export';
            $data['insurance'] = $insurance;
            $data['appealaddress'] = $appealaddress;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	
	/*** Start to Create the Appeal Address	 ***/	
    public function create($id)
    {
        $api_response = $this->getCreateApi($id);
        $api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
        {
			$insurance = $api_response_data->data->insurance;
			$address_flags = (array)$api_response_data->data->addressFlag;
			$address_flag['appeal'] = (array)$address_flags['appeal']; 
			// return 'Showing account with ID:'.$id;
			return view('practice/insurance/appealaddress/create_appealaddress',  compact('insurance','address_flag'));
		}
        else
        {
             return redirect('/insurance')->with('error',$api_response_data->message);
        }
    }
	/*** End to Create the Appeal Address	 ***/
    
	/*** Start to Store the Appeal Address	 ***/
    public function store($id)
    {
        $api_response = $this->getStoreApi($id);
        $api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
				 return Redirect::to('insurance')->with('error', $api_response_data->message);
		}
        
        if($api_response_data->status == 'success')
        {
               return Redirect::to('insurance/'.$id.'/insuranceappealaddress/')->with('success', $api_response_data->message);
        }
        else
        {
               return Redirect::back()->withInput()->withErrors($api_response_data->message);
        }
    }
	/*** End to Store the Appeal Address	 ***/
	
    /*** Start to Edit the Appeal Address	 ***/    
	public function edit($ids,$id)
	{   
		$api_response = $this->getEditApi($ids,$id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status=='error')
		{
			return redirect('/insurance/'.$ids.'/insuranceappealaddress')->with('error',$api_response_data->message);
		}

		if($api_response_data->status=='failure')
		{
			return redirect('/insurance')->with('error',$api_response_data->message);
		}
		
		$appealaddress = $api_response_data->data->appealaddress;
		$insurance     = $api_response_data->data->insurance;
		
		$address_flags = (array)$api_response_data->data->addressFlag;
		$address_flag['appeal'] = (array)$address_flags['appeal'];
                
		return view('practice/insurance/appealaddress/edit_appealaddress', compact('appealaddress','insurance','address_flag'));
	}  
	/*** End to Edit the Appeal Address	 ***/
	
	/*** Start to Update the Appeal Address	 ***/
	public function update($insurance_id, $id,Request $request)
	{
		$api_response = $this->getUpdateApi($insurance_id,$id, Request::all());
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status=='failure_ins')
		{
			return redirect('/insurance')->with('error',$api_response_data->message);
		}
		
		if($api_response_data->status=='failure')
		{
			return redirect('/insurance/'.$insurance_id.'/insuranceappealaddress')->with('error',$api_response_data->message);
		}

		if($api_response_data->status == 'success')
		{
			return Redirect::to('insurance/'.$insurance_id.'/insuranceappealaddress/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('insurance/'.$insurance_id.'/insuranceappealaddress/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}     
	}  
	/*** End to Update the Appeal Address	 ***/
	
	/*** Start to Destory the Appeal Address	 ***/
	public function destroy($insurance_id, $id)
	{
		$api_response = $this->getDeleteApi($insurance_id,$id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status=='failure_ins')
		{
			return redirect('/insurance')->with('error',$api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			return Redirect::to('insurance/'.$insurance_id.'/insuranceappealaddress')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('insurance/'.$insurance_id.'/insuranceappealaddress/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		} 
	}
	/*** End to Destory the Appeal Address	 ***/
	
	/*** Start to Show the Appeal Address	 ***/
	public function show($ids, $id)
	{
		$api_response 		= 	$this->getShowApi($ids, $id);
		$api_response_data 	= 	$api_response->getData();		
                
		if($api_response_data->status=='failure')
		{
			return redirect('/insurance')->with('error',$api_response_data->message);
		}
	
		if($api_response_data->status == 'success')
		{
			$appealaddress		= 	$api_response_data->data->appealaddress;
			$insurance		 	= 	$api_response_data->data->insurance;
			$address_flags = (array)$api_response_data->data->addressFlag;
			$address_flag['general'] = (array)$address_flags['appeal'];
			return view ( 'practice/insurance/appealaddress/show',compact('insurance','appealaddress','address_flag'));
		}
		else
		{
			return redirect('/insurance/'.$ids.'/insuranceappealaddress')->with('error',$api_response_data->message);
		}
	}
	/*** End to Show the Appeal Address	 ***/
}
