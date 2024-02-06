<div class="col-md-12 space-m-t-15 print-m-t-30">    
	<?php	
		$activetab = 'followup/category'; 
		$routex = Request::segment(2);
		if(!empty($routex)&& $routex == 'question') {
			$activetab = 'followup/question';
		}
	?>   
    
    <div class="med-tab nav-tabs-custom space10 no-bottom">
         <ul class="nav nav-tabs">
        @if($checkpermission->check_url_permission('followup/category') == 1)
        <li class="@if($activetab == 'followup/category') active @endif"><a href="javascript:void(0)" data-url="{{ url('followup/category') }}" class="js_next_process" ><i class="fa fa-bars i-font-tabs"></i> Claim Status</a></li>      
        @endif   	
    </ul>
    </div>
</div>