<div class="col-md-12 margin-t-m-18">
<!-- Sub Menu -->
		
	<?php $routex = explode('/',Route::getFacadeRoot()->current()->uri()); ?>
	
	@if(count($routex) > 0)
		@if($routex[2] == 'patientstatements')
			<?php $activetab = 'patientstatements'; ?>
		@elseif($routex[2] == 'budgetplan')
			<?php $activetab = 'budgetplan'; ?>
		@endif
	@endif
    <div class="med-tab nav-tabs-custom space10">
        <ul class="nav nav-tabs">
             
            <li class="@if($activetab == 'patientstatements') active @endif"><a href="{{ url('patients/'.@$patintid.'/patientstatements') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.resources')}} i-font-tabs"></i> Summary</a></li>                      
            <li class="@if($activetab == 'budgetplan') active @endif"><a href="{{ url('patients/'.@$patintid.'/budgetplan') }}"><i class="fa {{Config::get('cssconfigs.patient.history')}} i-font-tabs"></i> Budget Plan</a></li>                      
           
             
        </ul>
    </div>
    
</div>