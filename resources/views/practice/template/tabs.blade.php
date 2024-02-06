<div class="col-md-12 margin-t-m-18 print-m-t-30">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
                        {!! HTML::image('img/insurance-avator.jpg',null) !!} 
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-9 col-xs-12">
                <h3>Templates</h3>
                <p class="push">Create your own templates for Insurance and Patient correspondence. Copy example records and customize as per the requirement.</p>
            </div>
            

        </div><!-- /.box-body -->

        <!-- Sub Menu -->
        <?php 
			$activetab = 'templatetypes'; $routex = explode('.',Route::currentRouteName());
			if(count($routex) > 0) {
				if($routex[0] == 'templates') {
					$activetab = 'templates';
				}
			}
		?>       

    </div><!-- /.box -->        

    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
           <li class="@if($activetab == 'templates') active @endif"><a href="{{ url('templates') }}"><i class="fa fa-table i-font-tabs"></i> Templates</a></li> 
        </ul>
    </div>
</div>