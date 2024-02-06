<div class="col-md-12 margin-t-m-15">
    <div class="box-block">
        <div class="box-body">
            		
            <?php 
				$filename = "img/noimage.png";
				$img_details = [];
				$img_details['module_name']='insurance';
				$img_details['file_name']=$filename;
				$img_details['practice_name']="admin";
				
				$img_details['class']='';
				$img_details['alt']='insurance-image';
				$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
			?>
                                
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
                    {!! HTML::image('img/insurance-avator.jpg',null) !!}  
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-11 col-xs-12">
                <h3>Insurance</h3>
                <p class="push">Search in the existing database for your insurance list. If not available, admin user can add new insurance and complete the required fields in the page. We will keep our list updated with the new insurances very frequently.</p>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 med-left-border"> </div>

        </div><!-- /.box-body -->

        <!-- Sub Menu -->
        <?php  $activetab = 'listinsurancefavourites';
	$routex = explode('.',Route::currentRouteName());
?>
        @if(count($routex) > 0 && isset($routex[1]))
        @if($routex[1] == 'insurance')
        <?php $activetab = 'insurance'; ?>
        @elseif($routex[1] == 'insurance')
        <?php $activetab = 'insurance'; ?>
        @endif
        @endif
    </div>
</div><!-- /.box -->
