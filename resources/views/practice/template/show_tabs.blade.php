<div class="col-md-12 margin-t-m-20 print-m-t-30">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-8 col-md-8 col-sm-9 col-xs-12 med-right-border">
                 <h3 class="med-orange">{{ $templates->name }}</h3>
                <p> <span class="med-green font600"> Category : </span>{{ @$templates->templatetype->templatetypes }} </p>
                <p class="no-bottom"><span class="med-green font600">Status : </span><span class=" patient-status-bg-form @if($templates->status == 'Active')label-success @else label-danger @endif">{{ $templates->status }}</span></p>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal">
                <ul class="icons push no-padding">
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Created by </span> <span class="pull-right">{{ App\Http\Helpers\Helpers::shortname($templates->created_by) }}</span></li> 
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Created On </span> <span class="pull-right bg-date">{{ App\Http\Helpers\Helpers::dateFormat($templates->created_at, 'date') }}</span></li>				   
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Updated by </span> <span class="pull-right">@if(@$templates->modifier->name =="")<span class=" nill ">  - Nil - </span>@else {{ App\Http\Helpers\Helpers::shortname($templates->updated_by) }}@endif</span></li>
					<li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Updated On </span> <span class="pull-right">@if(@$templates->updated_at =="" || @$templates->updated_at =="0000-00-00 00:00:00" || @$templates->updated_at =="-0001-11-30 00:00:00")<span class=" nill">  - Nil - </span>@else <span class="pull-right bg-date">{{ App\Http\Helpers\Helpers::dateFormat($templates->updated_at,'date')}}</span>@endif </span></li>				   
                    
                </ul>
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

    <div class="med-tab nav-tabs-custom margin-t-8 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'templates') active @endif"><a href="{{ url('templates') }}"><i class="fa fa-table i-font-tabs"></i> Templates</a></li>    
        </ul>
    </div>

</div>