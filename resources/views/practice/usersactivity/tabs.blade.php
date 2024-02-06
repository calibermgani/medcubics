<div class="col-md-12 margin-t-m-15">
   
<!-- Sub Menu -->
	<?php
		$activetab = 'usersactivity'; 
		$routex = explode('.',Route::currentRouteName());
		if(count($routex) > 0 && isset($routex[1])){
			if($routex[0] == 'usersactivity' || $routex[0] == 'chargeslog') {
				$activetab = 'usersactivity';
			} elseif($routex[0] == 'patientslog') {
				$activetab = 'patientslog';
			}
		}
		$getMyreadTicket = App\Http\Helpers\Helpers::getMyreadTicket(); 
		$getMyTicketCount = '('.$getMyreadTicket.')';		
	?>
    <div class="med-tab nav-tabs-custom  no-bottom">
        <ul class="nav nav-tabs hidden-xs">      
			<li class="@if($activetab == 'usersactivity') active @endif"><a href="javascript:void(0)" data-url="{{ url('usersactivity') }}" class="js_next_process"> <i class="fa fa-ticket i-font-tabs"></i> Charges log</a></li>    
			<li class="@if($activetab == 'patientslog') active @endif"><a href="javascript:void(0)" data-url="{{ url('patientslog') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} i-font-tabs"></i> Patients log </a></li>
        </ul>
    </div>    
</div><!-- /.box -->