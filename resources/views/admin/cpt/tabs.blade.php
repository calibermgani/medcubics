
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
                <h3>CPT/HCPCS Codes</h3>
                <p class="push">Search in the existing database for your CPT/HCPCS codes and add to your favorites list. If not available, admin user can add new codes. Please double check the source data before adding any new codes to prevent preliminary rejections and denials. We will keep our list updated frequently.</p>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 med-left-border">
                <ul class="icons push no-padding">
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-6"><i class="fa fa-bookmark i-font-header"></i>American Medical Association</li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-6"><i class="fa fa-globe i-font-header"></i><a href="https://www.ama-assn.org" target="_blank">ama-assn.org</a></li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-6"><i class="fa fa-flag i-font-header"></i>Version 2014</li>
                </ul>
            </div>
        </div><!-- /.box-body -->

        <!--Sub Menu-->
		<?php $routex = Route::getFacadeRoot()->current()->uri();
				$search_from	=config('siteconfigs.icdcptsearch.type');
		?>
		@if($routex == 'admin/cpt')
                <?php $activetab = 'cpt'; ?>
		@elseif($routex == 'admin/searchcpt')
			<?php $activetab = 'searchcpt'; ?>
        @endif
	</div><!-- /.box -->
        
        <div class="med-tab nav-tabs-custom margin-t-10 no-bottom">
            <ul class="nav nav-tabs">
			@if($checkpermission->check_adminurl_permission('admin/cpt') == 1)
                <li class="@if($activetab == 'cpt') active @endif"><a href="{{ url('admin/cpt') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} i-font-tabs"></i> CPT</a></li> 
			@endif
			@if($checkpermission->check_adminurl_permission('admin/searchcpt') == 1 && $search_from == "imo")	
				<li class="@if($activetab == 'searchcpt') active @endif"><a href="{{ url('admin/searchcpt') }}" ><i class="fa {{Config::get('cssconfigs.common.search')}} i-font-tabs"></i> Search</a></li>
			@endif 	
            </ul>
        </div>
	</div>
<!--End Sub Menu-->

