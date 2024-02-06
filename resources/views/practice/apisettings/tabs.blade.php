<div class="col-md-12 space-m-t-22">
      <!-- Sub Menu -->
    <?php $activetab = 'listinsurancefavourites'; 
		   $routex = explode('.',Route::currentRouteName()); 
	
    if(count($routex) > 0) {
		if($routex[0] == 'holdoption') {
			$activetab = 'holdoption';
		} elseif($routex[0] == 'reason') {
			$activetab = 'reason_for_visit';
		} elseif($routex[0] == 'adjustmentreason') {
			$activetab = 'adjustmentreason';
		} elseif($routex[0] == 'emailtemplate') {
			$activetab = 'emailtemplate';
		} elseif($routex[0] == 'clinicalnotescategory') {
			$activetab = 'clinicalcategories';
		} elseif($routex[0] == 'insurancetypes') {
			$activetab = 'insurancetypes';
		} elseif($routex[0] == 'statementcategory') {
			$activetab = 'statementcategory';
		} elseif($routex[0] == 'statementholdreason') {
			$activetab = 'statementholdreason';
		} elseif($routex[0] == 'procedurecategory') {
			$activetab = 'procedurecategory';
		} elseif($routex[0] == 'claimsubstatus') {
			$activetab = 'claimsubstatus';
		}
    }
	?>
    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
                  
			@if($checkpermission->check_url_permission('reason') == 1)<li class="@if($activetab == 'reason_for_visit') active @endif"><a href="{{ url('reason') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.reason')}} i-font-tabs"></i> Reason For Visit</a></li>  @endif
			
            @if($checkpermission->check_url_permission('holdoption') == 1)<li class="@if($activetab == 'holdoption') active @endif"><a href="{{ url('holdoption') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}} i-font-tabs"></i> Hold Reason</a></li>@endif     
                
            @if($checkpermission->check_url_permission('adjustmentreason') == 1)<li class="@if($activetab == 'adjustmentreason') active @endif"><a href="{{ url('adjustmentreason') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.adjust')}} i-font-tabs"></i> Adjustment Reason</a></li>  @endif    
			<?php /*
			@if($checkpermission->check_url_permission('clinicalnotescategory') == 1)
				<li class="@if($activetab == 'clinicalcategories') active @endif"><a href="{{ url('clinicalnotescategory') }}" class="js_next_process"><i class="fa fa-bars i-font-tabs i-font-tabs"></i> Clinical Notes Category</a></li> 
			@endif	
			*/ ?>
			@if($checkpermission->check_url_permission('insurancetypes') == 1)
				<li class="@if($activetab == 'insurancetypes') active @endif"><a href="{{ url('insurancetypes') }}" class="js_next_process"><i class="fa fa-bars i-font-tabs i-font-tabs"></i> Insurance Type</a></li> 
			@endif
			
			<?php /* MR-1809 - Practice:user setting: Hide the Email template tab
			@if($checkpermission->check_url_permission('emailtemplate') == 1)
            <li class="@if($activetab == 'emailtemplate') active @endif"><a href="{{ url('emailtemplate') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.appealaddress')}} i-font-tabs"></i> Email Template</a></li>
			@endif
			*/ ?>
			
			@if($checkpermission->check_url_permission('statementholdreason') == 1)
				<li class="@if($activetab == 'statementholdreason') active @endif"><a href="{{ url('statementholdreason') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}}"></i> Statement Hold Reason</a></li> 
			@endif
			
			@if($checkpermission->check_url_permission('statementcategory') == 1)
            <li class="@if($activetab == 'statementcategory') active @endif"><a href="{{ url('statementcategory') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.appealaddress')}} i-font-tabs"></i> Statement Category</a></li>
			@endif		

			@if($checkpermission->check_url_permission('procedurecategory') == 1)
				<li class="@if($activetab == 'procedurecategory') active @endif"><a href="{{ url('procedurecategory') }}" class="js_next_process"><i class="fa fa-bars i-font-tabs i-font-tabs"></i> Procedure Category </a></li> 
			@endif
			
			@if($checkpermission->check_url_permission('claimsubstatus') == 1)
				<li class="@if($activetab == 'claimsubstatus') active @endif"><a href="{{ url('claimsubstatus') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.sub-status')}}"></i> Claim Sub Status </a></li> 
			@endif			
        </ul>
    </div>
</div><!-- /.box -->