
<div class="col-md-12 margin-t-m-18">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
						{!! HTML::image('img/insurance-avator.jpg',null) !!}  
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-9 col-xs-12">
                <h3>ICD</h3>
                <p class="push">Search in the existing database for your ICD-10 CM list. If not available, admin user can add new codes. Please double check the source data before adding any new codes to prevent preliminary rejections and denials. We will keep our list updated frequently.</p>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 med-left-border">

                <ul class="icons push no-padding">
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><i class="fa fa-bookmark i-font-header"  data-name="phone" data-animate="false" ></i>World Health Organization</li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><i class="fa fa-globe i-font-header"  data-name="globe" data-animate="false" ></i><a href="http://www.who.int" target="_blank">www.who.int</a></li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><i class="fa fa-flag i-font-header"  data-name="mail" data-animate="false" ></i>ICD-10</li>
                </ul>
            </div>
        </div><!-- /.box-body -->

        <!-- Sub Menu -->
		<?php  $routex = Route::getFacadeRoot()->current()->uri();
				$search_from	=config('siteconfigs.icdcptsearch.type');
		?>
		@if($routex == 'admin/icd')
			<?php $activetab = 'admin/icd'; ?>
		@elseif($routex == 'admin/searchicd')
			<?php $activetab = 'admin/searchicd'; ?>
		@endif
	</div>
	<div class="med-tab nav-tabs-custom margin-t-10 no-bottom">
		<ul class="nav nav-tabs">
			@if($checkpermission->check_adminurl_permission('admin/icd') == 1)
				<li class="@if($activetab == 'admin/icd') active @endif"><a href="{{ url('admin/icd') }}"><i class="fa {{Config::get('cssconfigs.common.icd')}} i-font-tabs"></i> ICD-10</a></li>
			@endif
			@if($checkpermission->check_adminurl_permission('admin/searchicd') == 1 && $search_from == "imo")
				<li class="@if($activetab == 'admin/searchicd') active @endif"><a href="{{ url('admin/searchicd') }}" ><i class="fa {{Config::get('cssconfigs.common.search')}} i-font-tabs"></i> Search</a></li>
			@endif 
		</ul>
	</div>
</div>
