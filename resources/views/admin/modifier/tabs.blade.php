<div class="col-md-12 space-m-t-15"><!-- Main Header Starts -->
    <div class="box-block"><!-- Box Starts -->
        <div class="box-body"><!-- Box body Starts -->
			<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
						{!! HTML::image('img/insurance-avator.jpg',null) !!}  
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-9 col-xs-12 ">
                <h3>Modifier</h3>
                <p class="push">Search in the existing database for your CPT/HCPCS modifiers list. If not available, admin user can add new codes. Please double check the source data before adding any new codes to prevent preliminary rejections and denials. We will keep our list updated frequently.</p>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 med-left-border">
                <ul class="icons push no-padding">
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><i class="fa fa-bookmark i-font-header"></i>American Medical Association</li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><i class="fa fa-globe i-font-header"></i><a href="https://www.ama-assn.org/ama" target="_blank">ama-assn.org</a></li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><i class="fa fa-flag i-font-header"></i>Version 2014</li>
                </ul>
            </div>
        </div><!-- /.box-body ends -->
		<?php 	$activetab = 'modifierlevel1'; 
				$routex = explode('/',Route::current()->uri());
		?>
		
		@if(count($routex) > 0)
			@if($routex[1] == 'modifierlevel2')
				<?php $activetab = 'modifierlevel2'; ?>
			@endif
		@endif
    </div><!-- /.box Ends -->
    <div class="med-tab nav-tabs-custom space10 no-bottom"><!-- Tab Starts -->
        <ul class="nav nav-tabs">
			@if($checkpermission->check_adminurl_permission('admin/modifierlevel1') == 1)
				<li class="@if($activetab == 'modifierlevel1') active @endif"> <a href="javascript:void(0)" data-url="{{ url('admin/modifierlevel1') }}" class="js_next_process"><i class="fa fa-bars i-font-tabs"></i> Level I</a></li>      
			@endif
			@if($checkpermission->check_adminurl_permission('admin/modifierlevel2') == 1)	
				<li class="@if($activetab == 'modifierlevel2') active @endif"> <a href="javascript:void(0)" data-url="{{ url('admin/modifierlevel2') }}" class="js_next_process"><i class="fa fa-bars i-font-tabs"></i> Level II</a></li>        
			@endif		
        </ul>
    </div><!-- Tab Ends -->
</div><!-- Main Header Ends -->
