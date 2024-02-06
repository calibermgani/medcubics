<?php $activetab = ''; 
	$currnet_page = Route::getFacadeRoot()->current()->uri();
	if(count($currnet_page) > 0) {
		if($currnet_page == 'armanagement/myfollowup') {
			$activetab = 'myfollowup';
		} elseif($currnet_page == 'armanagement/otherfollowup') {
			$activetab = 'otherfollowup';
		}
	}
?>

<div class="med-tab nav-tabs-custom space10 no-bottom">
    <ul class="nav nav-tabs">
	
        <li class="@if($activetab == 'myfollowup') active @endif"><a href="{{ url('armanagement/myfollowup') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} i-font-tabs"></i> Assigned Followup List</a></li>   
        
        <li class="@if($activetab == 'otherfollowup') active @endif"><a href="{{ url('armanagement/otherfollowup') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}} i-font-tabs"></i> Total Followup List</a></li> 
        
    </ul>
</div>
<br/>

<div class="box-header">
	<i class="fa fa-bars font14"></i><h3 class="box-title">Followup List</h3>
	<div class="box-tools pull-right margin-t-4">										
	</div>
</div>
<br/>