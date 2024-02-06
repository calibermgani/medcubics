<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="med-tab nav-tabs-custom no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($selected_tab == 'patientstatementsettings') active @endif"><a href="{{ url('patientstatementsettings') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.apisettings')}}  i-font-tabs"></i> Settings</a></li>
			<li class="@if($selected_tab == 'patientbulkstatement') active @endif @if(@$psettings->bulkstatement == 0) hide @endif"><a class="js_next_process" href="{{ url('bulkstatement') }}" ><i class="fa {{Config::get('cssconfigs.common.bulkstatement')}}  i-font-tabs"></i> Bulk Statement</a></li>
			<li class="@if($selected_tab == 'patientindividualstatement') active @endif"><a class="js_next_process" href="{{ url('individualstatement') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.patientstatement')}}  i-font-tabs"></i> Individual Statement</a></li>			 
			<li class="@if($selected_tab == 'patientstatementhistory') active @endif"><a class="js_next_process" href="{{ url('statementhistory') }}" ><i class="fa {{Config::get('cssconfigs.patient.history')}}  i-font-tabs"></i> Statement History</a></li>
        </ul>
    </div>
</div>  