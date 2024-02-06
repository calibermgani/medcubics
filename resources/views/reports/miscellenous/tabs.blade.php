<div class="col-md-12 space-m-t-22">
  
	<!-- Sub Menu -->
	@php  $activetab = 'cpt_report'; 
		   $routex = explode('/',Route::getFacadeRoot()->current()->uri()); 
	@endphp
	@if(count($routex) > 0)
		@if(count($routex) ==2)
			@if($routex[1] == 'miscellenous')
				@php $activetab = 'cpt_report'; @endphp
			@endif
		@endif
		@if(count($routex) == 3)
			@if($routex[2] == 'cpt')
				@php $activetab = 'cpt_report'; @endphp
			@elseif($routex[2] == 'icd')
				@php $activetab = 'icd_report'; @endphp
			@elseif($routex[2] == 'insurance')
				@php $activetab = 'insurance_list'; @endphp
			@elseif($routex[2] == 'facility')
				@php $activetab = 'facility_list'; @endphp
			 @elseif($routex[2] == 'provider')
				@php $activetab = 'provider_list'; @endphp
			@elseif($routex[2] == 'useractivity')
				@php $activetab = 'user_activity'; @endphp 
			@endif
		@endif
	@endif
	<div class="med-tab nav-tabs-custom space10">
        <ul class="nav nav-tabs">
            @if($checkpermission->check_url_permission('cpt_report') == 1)
				<li class="@if($activetab == 'cpt_report') active @endif"><a href="{{ url('reports/miscellenous/cpt') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}} i-font-tabs"></i>&nbsp;CPT Report</a></li>
			@endif     
            @if($checkpermission->check_url_permission('icd_report') == 1)
				<li class="@if($activetab == 'icd_report') active @endif"><a href="{{ url('reports/miscellenous/icd') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.reason')}} i-font-tabs"></i>&nbsp;ICD Report</a></li>  
			@endif    
            @if($checkpermission->check_url_permission('insurance_list') == 1)
				<li class="@if($activetab == 'insurance_list') active @endif"><a href="{{ url('reports/miscellenous/insurance') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}} i-font-tabs"></i>&nbsp;Insurance List</a></li>  
			@endif
			@if($checkpermission->check_url_permission('facility_list') == 1)
				<li class="@if($activetab == 'facility_list') active @endif"><a href="{{ url('reports/miscellenous/facility') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}} i-font-tabs"></i>&nbsp;Facility List</a></li>  
			@endif
			@if($checkpermission->check_url_permission('provider_list') == 1)
				<li class="@if($activetab == 'provider_list') active @endif"><a href="{{ url('reports/miscellenous/provider') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}} i-font-tabs"></i>&nbsp;Provider List</a></li>  
			@endif
			@if($checkpermission->check_url_permission('user_activity') == 1)
				<li class="@if($activetab == 'user_activity') active @endif"><a href="{{ url('reports/miscellenous/useractivity') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}} i-font-tabs"></i>&nbsp;User Activity</a></li>  
			@endif
        </ul>
    </div>
</div><!-- /.box -->