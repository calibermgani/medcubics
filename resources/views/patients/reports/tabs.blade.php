<div class="col-md-12 margin-t-m-18">
<!-- Sub Menu -->
		
	<?php $routex = explode('/',Route::getFacadeRoot()->current()->uri()); ?>
	
	@if(count($routex) > 0)
		@if($routex[2] == 'reports1')
			<?php $activetab = 'reports'; ?>
		@elseif($routex[2] == 'documentsummary')
			<?php $activetab = 'documentsummary'; ?>
                @elseif($routex[2] == 'reports')
			<?php $activetab = 'reports'; ?>
		@endif
	@endif
    <div class="med-tab nav-tabs-custom space10">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'reports') active @endif"><a href=""><i class="fa {{Config::get('cssconfigs.Practicesmaster.resources')}} i-font-tabs"></i> Reports</a></li>                      
            <li class="@if($activetab == 'list') active @endif"><a href=""><i class="fa {{Config::get('cssconfigs.patient.history')}} i-font-tabs"></i> Patient Aging</a></li>                      
        </ul>
    </div>
</div>