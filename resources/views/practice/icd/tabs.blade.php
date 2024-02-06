<div class="col-md-12 margin-t-m-18 print-m-t-30"><!-- Col 12 Starts -->
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
                    {!! HTML::image('img/insurance-avator.jpg',null) !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                <h3>ICD 10</h3>
                <p class="push">Search in the existing database for your ICD-10 CM list. If not available, admin user can add new codes. Please double check the source data before adding any new codes to prevent preliminary rejections and denials. We will keep our list updated frequently.</p>
            </div>

            
        </div><!-- /.box-body -->

        <!-- Sub Menu -->
        <?php $routex = Route::getFacadeRoot()->current()->uri(); ?>
		@if($routex == 'listicdfavourites')
        <?php $activetab = 'listicdfavourites';  ?>
        @elseif($routex == 'icd')
        <?php $activetab = 'icd'; ?>
        @elseif($routex == 'searchicd')
        <?php $activetab = 'searchicd'; ?> 
		@elseif($routex == 'icd9/import')
        <?php $activetab = 'icd9/import'; ?>
        @endif
		
    </div>
	<?php 
	 $accesscpticdinfo = App\Http\Controllers\Medcubics\Api\DBConnectionController::getUserAPIIds('imo_icd');
	 ?>	  
    
</div><!-- Col 12 Ends -->