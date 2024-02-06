<div class="col-md-12 margin-t-m-18">
<!-- Sub Menu -->		
	<?php $routex = explode('/',Route::getFacadeRoot()->current()->uri()); 
		if(count($routex) > 0) {
			if($routex[2] == 'correspondence') {
				$activetab = 'correspondence';
			} elseif($routex[2] == 'correspondencehistory') {
				$activetab = 'correspondencehistory';
			}
		}	
	?>
    <div class="med-tab nav-tabs-custom space10">
        <ul class="nav nav-tabs">             
            <li class="@if($activetab == 'correspondencehistory') active @endif"><a href="{{ url('patients/'.$patient_id.'/correspondencehistory') }}"><i class="fa {{Config::get('cssconfigs.patient.history')}} i-font-tabs"></i> History</a></li>                      
            <li class="@if($activetab == 'correspondence') active @endif"><a href="{{ url('patients/'.$patient_id.'/correspondence') }}" accesskey="r" ><i class="fa {{Config::get('cssconfigs.common.search')}} i-font-tabs"></i> Sea<span class="text-underline">r</span>ch / Add Templates</a></li>      
        </ul>
    </div>    
</div>