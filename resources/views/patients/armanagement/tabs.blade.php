<div class="col-md-12 margin-t-m-18">
<!-- Sub Menu -->
 <!-- Tab Starts  -->
    <?php 
		$activetab = 'payments_list'; 
        $routex = explode('.',Route::currentRouteName());
    ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom js-dynamic-tab-menu">
        <ul class="nav nav-tabs">
            <?php /*    
            <!--<li class="@if($activetab == 'financials') active @endif"><a href="{{ url('patients/'.$id.'/armanagement/armanagement') }}" ><i class="fa fa-bars i-font-tabs"></i> Financials</a></li>-->           	                      	           
            */ ?>
            <li class="@if($activetab == 'payments_list') active @endif"><a href="javascript:void(0);" ><i class="fa fa-bars i-font-tabs"></i><span id="claimdetlink_main0" class="js_claimdetlink"> List</span></a></li>             
            
        </ul>
    </div>
    <!-- Tab Ends -->
	<?php 
		$routex = explode('/',Route::getFacadeRoot()->current()->uri()); 
		if(count((array)$routex) > 0) {
			if($routex[2] == 'eligibility') {
				$activetab = 'eligibility';
			} elseif($routex[2] == 'eligibilitytemplate') {
				$activetab = 'eligibilitytemplate';
			}	
		}
	?>	
	@endif
    <div class="med-tab nav-tabs-custom space10">
        <ul class="nav nav-tabs">             
            <li class="@if($activetab == 'eligibility') active @endif"><a href="{{ url('patients/'.$patient_id.'/eligibility') }}"><i class="fa {{Config::get('cssconfigs.patient.history')}} i-font-tabs"></i> History</a></li>            
            @if($checkpermission->check_url_permission('patients/{{patientid}}/eligibility/create') == 1)    
				<li class="@if($activetab == 'eligibilitytemplate') active @endif"><a href="{{url('patients/'.$patient_id.'/eligibilitytemplate')}}" ><i class="fa {{Config::get('cssconfigs.patient.file_text')}} i-font-tabs"></i> Verify Eligibility / Benefits</a></li>
			@endif
        </ul>
    </div>    
</div>