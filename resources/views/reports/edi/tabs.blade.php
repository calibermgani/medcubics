<div class="col-md-12 space-m-t-22">
  
	<!-- Sub Menu -->
	@php  $activetab = 'edi_report'; 
		   $routex = explode('/',Route::getFacadeRoot()->current()->uri()); 
	@endphp
	@if(count($routex) > 0)
		@if(count($routex) ==2)
			@if($routex[1] == 'edi')
				@php $activetab = 'edi_report'; @endphp
			@endif
		@endif
		@if(count($routex) == 3)
			@if($routex[2] == 'claimsubmissions')
				@php $activetab = 'edi_report'; @endphp
			@elseif($routex[2] == 'rejection')
				@php $activetab = 'rejection_report'; @endphp
			@elseif($routex[2] == 'hold')
				@php $activetab = 'hold_report'; @endphp
			@endif
		@endif
	@endif
  

    <div class="med-tab nav-tabs-custom space10">
        <ul class="nav nav-tabs">
            @if($checkpermission->check_url_permission('edi_report') == 1)
				<li class="@if($activetab == 'edi_report') active @endif"><a href="{{ url('reports/edi/claimsubmissions') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}} i-font-tabs"></i>&nbsp;Claims Submissions</a></li>
			@endif     
            @if($checkpermission->check_url_permission('rejection_report') == 1)
				<li class="@if($activetab == 'rejection_report') active @endif"><a href="{{ url('reports/edi/rejection') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.reason')}} i-font-tabs"></i>&nbsp;Rejection Report</a></li>  
			@endif    
            @if($checkpermission->check_url_permission('hold_report') == 1)
				<li class="@if($activetab == 'hold_report') active @endif"><a href="{{ url('reports/edi/hold') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}} i-font-tabs"></i>&nbsp;Hold Report</a></li>  
			@endif
        </ul>
    </div>
</div><!-- /.box -->
