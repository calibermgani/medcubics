<div class="col-md-12 margin-t-m-18">
<!-- Sub Menu -->		
	<?php $routex = explode('/',Route::getFacadeRoot()->current()->uri()); ?>
	
	@if(count($routex) > 0)
		@if($routex[2] == 'eligibility')
			<?php $activetab = 'eligibility'; ?>
		@elseif($routex[2] == 'eligibilitytemplate')
			<?php $activetab = 'eligibilitytemplate'; ?>
		@endif
	@endif
    <div class="med-tab nav-tabs-custom space10">
        <ul class="nav nav-tabs">
             
            <li class="@if($activetab == 'eligibility' && empty($routex[3])) active @endif"><a href="{{ url('patients/'.$patient_id.'/eligibility') }}"><i class="fa {{Config::get('cssconfigs.patient.history')}} i-font-tabs"></i> History</a></li>                      
            
            @if($checkpermission->check_url_permission('patients/{{patientid}}/eligibility/create') == 1)    
	            <li class="@if($activetab == 'eligibilitytemplate' || !empty($routex[3])) active @endif"><a href="{{url('patients/'.$patient_id.'/eligibilitytemplate')}}"  accesskey="v"><i class="fa {{Config::get('cssconfigs.patient.file_text')}} i-font-tabs"></i> <span class="text-underline">V</span>erify Eligibility</a></li>      
	        @endif
        </ul>
    </div>    
</div>