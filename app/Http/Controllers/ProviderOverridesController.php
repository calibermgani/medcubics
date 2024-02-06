<?php namespace App\Http\Controllers;
use View;
use Input;
use Request;
use Redirect;
use Config;
use App\Http\Controllers\Api\ProviderOverridesApiController as ProviderOverridesApiController;

class ProviderOverridesController extends ProviderOverridesApiController 
{
    public function __construct(Request $request) 
    {
        View::share('heading','Provider');
        View::share('selected_tab','provider');
        View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.provider'));
    }
    
    public function index($providerid)
    {
        $api_response  		= $this->getIndexApi($providerid);
        $api_response_data 	= $api_response->getData();
        if($api_response_data->status == 'success')
        {
            $provider 	= $api_response_data->data->provider;
            $overrides 	= $api_response_data->data->overrides;
            return view('practice/provider/overrides/overrides', compact('overrides','provider'));
        }
        else
        {
            return redirect()->back()->with('error',$api_response_data->message);
        }
    }
    
    public function create($providerid)
    {
        $api_response 			= 	$this->getCreateApi($providerid);
        $api_response_data 		= 	$api_response->getData();
        if($api_response_data->status == 'success')
        {
            $provider				= $api_response_data->data->provider;
            $id_qualifiers 			= $api_response_data->data->id_qualifiers;
            $provider_override 		= $api_response_data->data->provider_override;
            $provider_override_id	= $api_response_data->data->provider_override_id;
            $id_qualifiers_id		= $api_response_data->data->id_qualifiers_id;
            return view('practice/provider/overrides/create_overrides',  compact('provider','id_qualifiers','provider_override', 'provider_override_id', 'id_qualifiers_id'));
        }
        else
            return Redirect::to('provider/'.$providerid.'/provideroverrides')->with('error', $api_response_data->message);
    }
    
    public function store($providerid, Request $request)
    {
        $api_response 		= 	$this->getStoreApi($providerid);
        $api_response_data 	= 	$api_response->getData();
        $providerid			=	Input::get('providers_id');
        if($api_response_data->status == 'success')
                return Redirect::to('provider/'.$providerid.'/provideroverrides/'.$api_response_data->data)->with('success', $api_response_data->message);
        elseif($api_response_data->status == 'error_create')
            return Redirect::to('overrides')->with('error', $api_response_data->message);
        else
            return redirect()->back()->withInput()->withErrors($api_response_data->message);
    }
	
    public function show($ids,$id)
    {
        $api_response 		= 	$this->getShowApi($ids,$id);
        $api_response_data 	= 	$api_response->getData();

        if($api_response_data->status=='error')
        {
            return redirect('/provider/'.$ids.'/provideroverrides')->with('message',$api_response_data->message);
        }
        if($api_response_data->status=='failure')
        {
            return redirect('/provider')->with('message',$api_response_data->message);
        }

        $overrides		 	= 	$api_response_data->data->overrides;
        $provider		 	= 	$api_response_data->data->provider;

        if($api_response_data->status == 'success')
        {
            return view ( 'practice/provider/overrides/show',compact('provider','overrides'));
        }
        else
        {
            return redirect()->back()->with('message',$api_response_data->message);
        }
    }
	
    public function edit($providerid,$id)
    {
        $api_response 		= 	$this->getEditApi($providerid,$id);
        $api_response_data 	= 	$api_response->getData();

        if($api_response_data->status == 'success')
        {
			$provider 			= 	$api_response_data->data->provider;
			$overrides 			= 	$api_response_data->data->overrides;
			$id_qualifiers_id               = 	$api_response_data->data->id_qualifiers_id;			
			$id_qualifiers                  = 	$api_response_data->data->id_qualifiers;
			$provider_override		= 	$api_response_data->data->provider_override;
			$provider_override_id		= 	$api_response_data->data->provider_override_id;
			return view('practice/provider/overrides/edit_overrides', compact('overrides','provider','id_qualifiers','provider_override','id_qualifiers_id','provider_override_id'));
        }
        else
        {
            if($api_response_data->status=='error')
            {
				return redirect('/provider/'.$providerid.'/provideroverrides')->with('message',$api_response_data->message);
            }
            if($api_response_data->status=='failure')
            {
                return redirect('/provider')->with('message',$api_response_data->message);
            }
        }
    }
	
    public function update($providerid,$id)
    {
        $api_response 		= 	$this->getUpdateApi($providerid,$id);
        $api_response_data 	= 	$api_response->getData(); 	
        if($api_response_data->status == 'success')
        { 
            return Redirect::to('provider/'.$providerid.'/provideroverrides/'.$id)->with('success', $api_response_data->message);
        }
        else
        { 
            return Redirect::to('provider/'.$providerid.'/provideroverrides/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
        }
    }
	
    public function destroy($providerid,$id)
    {
        $api_response 		= 	$this->getDeleteApi($providerid,$id);
        $api_response_data 	= 	$api_response->getData();

        if($api_response_data->status == 'success')
        {
            return Redirect::to('provider/'.$providerid.'/provideroverrides')->with('success', $api_response_data->message);
        }
        else
        {
            return redirect()->back()->with ( 'message', $api_response_data->message );
        }
    }
	
}
