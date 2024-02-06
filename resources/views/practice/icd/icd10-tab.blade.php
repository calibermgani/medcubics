<div class="col-md-12 space-m-t-15 print-m-t-30">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                <div class="text-center med-circle font12">
                    {{ $icd->icd_code }}
                </div>
            </div>
            <div class="col-lg-7 col-md-6 col-sm-9 col-xs-12">
                <h4 class="med-green no-margin">{{ $icd->icd_code }} </h4>
                <p class="push">{{substr($icd->medium_description, 0, 125)}}</p>
              </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 med-left-border">                                			
                <div>                    
                    <p><span class="med-green font600">ICD Type : </span>@if($icd->icd_type != '') {{ $icd->icd_type }} @else Not selected @endif</p>
                </div>
                <div>                    
                    <p><span class="med-green font600">Header : </span>
                        @if($icd->header == 'V')V- Valid for Submission 
                        @elseif($icd->header == 'H')H - Header, Not valid for Submission 
                        @elseif($icd->header == 'C')C - Chapter, Not valid for Submission 
                        @endif
                    </p>
                </div>                				                                                
            </div>
        </div><!-- /.box-body -->

        <!-- Sub Menu -->

        <?php 
			$activetab = 'listicdfavourites'; 
			$routex = explode('.',Route::currentRouteName());
			$currnet_page = Route::getFacadeRoot()->current()->uri();
		?>

        @if($currnet_page == 'listicd9favourites')
        <?php $activetab = 'listicd9favourites'; ?>
        @elseif(count($routex) > 1)
        @if($routex[0] == 'icd')
        <?php $activetab = 'icd'; ?>
        @elseif($routex[0] == 'icd9')
        <?php $activetab = 'icd9'; ?>
        @endif
        @endif

    </div>    
</div>