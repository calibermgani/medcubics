<?php 
    $activetab = '';
    $currnet_page = explode('/', Route::getFacadeRoot()->current()->uri());	
	if(count((array)$currnet_page) > 0) {
		if(isset($currnet_page[1]) && $currnet_page[1] == 'myproblemlist')
			$activetab = 'myproblemlist';
		else 
			$activetab = 'problemlist';
	}
?>
<div class="med-tab nav-tabs-custom space10 no-bottom">
    <ul class="nav nav-tabs">
        <li class="@if($activetab == 'myproblemlist') active @endif"><a href="{{ url('armanagement/myproblemlist') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} i-font-tabs"></i> Assigned Workbench</a></li>
        <li class="@if($activetab == 'problemlist') active @endif"><a href="{{ url('armanagement/problemlist') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}} i-font-tabs"></i> Total Workbench</a></li> 
    </ul>
</div>