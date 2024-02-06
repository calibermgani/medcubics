<div class="col-md-12 space-m-t-22">  
    <!-- Sub Menu -->
    <?php $activetab = 'listinsurancefavourites'; 
		   $routex = explode('.',Route::currentRouteName());
			 $currnet_page = Route::getFacadeRoot()->current()->uri();
	?>
	
    @if(count($routex) > 0)
		@if($routex[0] == 'users')
			<?php $activetab = 'users'; ?>
		@elseif(strpos($currnet_page, 'practices/useractivity') !== false )     
			<?php $activetab = 'useractivity'; ?>
		@elseif(strpos($currnet_page, 'practices/userhistory') !== false )
			<?php $activetab = 'userhistory'; ?>	
		@endif
    @endif

    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
			<li class="@if($activetab == 'users') active @endif"><a href="{{ url('users') }}" ><i class="fa {{Config::get('cssconfigs.common.user')}} i-font-tabs"></i> Users</a></li>
			
			<li class="@if($activetab == 'useractivity') active @endif"><a href="{{ url('practices/useractivity') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.apisettings')}} i-font-tabs"></i> User Activity</a></li>
			
            <li class="@if($activetab == 'userhistory') active @endif"><a href="{{ url('practices/userhistory') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.userapisettings')}} i-font-tabs"></i> User History</a></li>      
        </ul>
    </div>
</div><!-- /.box -->