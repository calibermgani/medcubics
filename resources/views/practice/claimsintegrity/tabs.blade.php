<div class="col-md-12 margin-t-m-18 print-m-t-30">    
	<!-- /.box-body -->
	<!--Sub Menu-->
	<?php	
	/*	$routex = Route::getFacadeRoot()->current()->uri();
		if($routex == 'listfavourites') {
			$activetab = 'listfavourites';
		} elseif($routex == 'cpt') {
			$activetab = 'cpt';
		} elseif($routex == 'searchcpt') {
			$activetab = 'searchcpt';
		} elseif($routex == 'cptimport') {
			$activetab = 'cptimport';
		}	
		$accesscpticdinfo = App\Http\Controllers\Medcubics\Api\DBConnectionController::getUserAPIIds('imo_cpt');*/
	?>
	<!-- /.box -->
    <!--<div class="med-tab nav-tabs-custom space10 no-bottom">
		<ul class="nav nav-tabs">
			<li class="@if(@$activetab == 'listfavourites') active @endif"><a href="{{ url('listfavourites') }}" ><i class="fa {{Config::get('cssconfigs.common.favourites')}} i-font-tabs"></i> Favorites</a></li> 
			@if($checkpermission->check_url_permission('cpt') == 1)	
				<li class="@if(@$activetab == 'cpt') active @endif"><a href="{{ url('cpt') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} i-font-tabs"></i> CPT / HCPCS Master</a></li>
			@endif	
			<li class="@if(@$activetab == 'cptimport') active @endif"><a href="{{ url('cptimport') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} i-font-tabs"></i> CPT/HCPCS Work RVU</a></li>
			<?php /*
			<!--@if($accesscpticdinfo == '1')
				<li class="@if(@$activetab == 'searchcpt') active @endif"><a href="{{ url('searchcpt') }}" ><i class="livicon" data-name="search" style="margin-right: 0px;"></i> Search</a></li>
			@endif -->
			*/ ?>
		</ul>
	</div> -->
</div>
<!--End Sub Menu-->