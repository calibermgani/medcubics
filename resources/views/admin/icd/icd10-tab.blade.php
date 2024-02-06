
<div class="col-md-12 margin-t-m-18">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center med-circle">
                    {{ $icd->icd_code }}
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
                <h3>{{ $icd->icd_code }}</h3>
                <p class="push">{{substr($icd->medium_description, 0, 125)}}</p>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 med-left-border">               
                <ul class="icons push no-padding">
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">ICD Type </span> <span class="pull-right">@if($icd->icd_type != '') {{ $icd->icd_type }} @else <span class="nill"> - Nil -</span> @endif </span>  </li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Header </span> <span class="pull-right"> @if($icd->header == 'V')V- Valid for Submission
                            @elseif($icd->header == 'H')H - Header, Not valid for Submission
                            @elseif($icd->header == 'C')C - Chapter, Not valid for Submission
                            @endif</span>  </li>
                </ul>   
            </div>
        </div><!-- /.box-body -->

        <!-- Sub Menu -->
        <?php  $activetab = 'admin/icd09';
			$routex = explode('.',Route::currentRouteName());
			$currnet_page = Route::getFacadeRoot()->current()->uri();
		?>

        @if($currnet_page == 'admin/icd09')
			<?php $activetab = 'admin/icd09'; ?>
        @elseif(count($routex) > 1)
			@if($routex[0] == 'admin/icd')
				<?php $activetab = 'admin/icd'; ?>
			@endif
        @endif
    </div> 
</div>
