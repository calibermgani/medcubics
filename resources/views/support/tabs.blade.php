<div class="col-md-12 margin-t-m-15">

    <!-- Sub Menu -->
	<?php
		$activetab = ''; 
		$currnet_page = Route::getFacadeRoot()->current()->uri(); 
		if(strpos($currnet_page, 'searchticket') !== false) {
			$activetab = 'searchticket';
		} elseif(strpos($currnet_page, 'myticket') !== false) {
			$activetab = 'myticket';
		} elseif(strpos($currnet_page, 'ticket') !== false) {
			$activetab = 'ticket';
		}
	?>
    <div class="med-tab nav-tabs-custom  no-bottom">
        <ul class="nav nav-tabs">

            <li class="@if($activetab == 'searchticket') active @endif"><a href="{{ url('searchticket') }}" class="js_next_process"> <i class="fa {{Config::get('cssconfigs.common.search')}} i-font-tabs"></i> Search Ticket</a></li>
            @if(@Auth::user()->id!='')
            <li class="@if($activetab == 'myticket') active @endif"><a href="{{ url('myticket') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} i-font-tabs"></i> My Tickets</a></li>
            @endif

            <li class="@if($activetab == 'ticket') active @endif"><a href="{{ url('ticket') }}" class="js_next_process"> <i class="fa {{Config::get('cssconfigs.admin.ticket')}} i-font-tabs"></i> Post Ticket</a></li>
        </ul>
    </div>

</div><!-- /.box -->