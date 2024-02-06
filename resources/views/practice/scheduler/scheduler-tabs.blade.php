<div class="col-md-12 space-m-t-15 print-m-t-30">  
     <!-- /.box-body -->
	<?php
		if(isset($scheduler_tab_type)) {
			if($scheduler_tab_type=='facility') {
				$activetab = 'facility_list';
			} else {
				$activetab  = 'scheduler';
			}
		} else {
			$activetab  = 'scheduler';
		}
	?>
    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'scheduler') active @endif"><a href="{{ url('practiceproviderschedulerlist') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} i-font-tabs"></i> Provider</a></li>
            <li class="@if($activetab == 'facility_list') active @endif"><a href="{{ url('practicefacilityschedulerlist') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.facility')}} i-font-tabs"></i> Facility</a></li>
        </ul>
    </div>
</div>