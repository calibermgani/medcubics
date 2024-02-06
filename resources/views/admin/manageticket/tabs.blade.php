<div class="col-md-12 margin-t-m-15">
   
<!-- Sub Menu -->
	<?php
		$activetab = 'listinsurancefavourites'; 
		$routex = explode('.',Route::currentRouteName());		
		if(count($routex) > 0 && isset($routex[1])){
			if($routex[0] == 'manageticket' || $routex[0] == 'createnewticket') {
				$activetab = 'manageticket';
			} elseif($routex[0] == 'managemyticket') {
				$activetab = 'myticket';
			}
		}
		$getMyreadTicket = App\Http\Helpers\Helpers::getMyreadTicket(); 
		$getMyTicketCount = '('.$getMyreadTicket.')';		
	?>

    <div class="med-tab nav-tabs-custom  no-bottom">
        <ul class="nav nav-tabs">
            @if($checkpermission->check_adminurl_permission('admin/manageticket') == 1)
				<li class="@if($activetab == 'manageticket') active @endif"><a href="javascript:void(0)" data-url="{{ url('admin/manageticket') }}" class="js_next_process"> <i class="fa fa-ticket i-font-tabs"></i> All Tickets</a></li>            
            @endif
		
			@if($checkpermission->check_adminurl_permission('admin/managemyticket') == 1)
				<li class="@if($activetab == 'myticket') active @endif"><a href="javascript:void(0)" data-url="{{ url('admin/managemyticket') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} i-font-tabs"></i> My Tickets {{ ($getMyreadTicket == '')? '' : $getMyTicketCount }}</a></li>
			 @endif           
        </ul>
    </div>    
</div><!-- /.box -->