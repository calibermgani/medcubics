<div class="col-md-12 space-m-t-22">
    <!-- Sub Menu -->
    <?php $activetab = 'listinsurancefavourites'; 
		   $routex = explode('.',Route::currentRouteName()); 
        if(count($routex) > 0) {
            if($routex[0] == 'apisettings') {
                $activetab = 'apisettings';
            } elseif($routex[0] == 'userapisettings') {
                $activetab = 'userapisettings';
            }
        }
	?>
    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'apisettings') active @endif"><a href="{{ url('apisettings') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.api')}} i-font-tabs"></i> API Settings</a></li>
			
            <li class="@if($activetab == 'userapisettings') active @endif"><a href="{{ url('userapisettings') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.userapisettings')}} i-font-tabs"></i> User API Settings</a></li>      
					
        </ul>
    </div>
</div><!-- /.box -->