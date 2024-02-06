<div class="col-md-12 margin-t-m-18">
    <div class="box-block">
        <div class="box-body">

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
				<?php 
				$filename = $insurance->avatar_name . '.' . $insurance->avatar_ext;
				$img_details = [];
				$img_details['module_name']='insurance';
				$img_details['file_name']=$filename;
				$img_details['practice_name']="admin";
				$img_details['record_id']=$insurance->id;
				$img_details['class']='';
				$img_details['alt']='insurance-image';
				$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>
                {!! $image_tag !!}
                </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
                <h3>{{ $insurance->insurance_name }} <span class="med-orange"> {{ $insurance->short_name }}</span></h3>                
                 {{ $insurance->insurance_desc }}
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 med-left-border">


                <ul class="icons push no-padding">
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Phone </span>@if($insurance ->phone1 != '') <span class="pull-right">{{ $insurance ->phone1 }}  <span class="@if($insurance->phoneext == '')  @else bg-ext @endif"> {{ $insurance->phoneext }}</span> </span>@endif  </li>
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Fax </span>  @if($insurance ->fax != '')<span class="pull-right">{{ $insurance ->fax }}</span>@endif</li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">E-mail </span>@if($insurance ->email != '') <span class="pull-right"><a href="mailto:{{ $insurance ->email }}">{{ $insurance ->email }}</a></span>@endif</li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Website </span> @if($insurance ->website != '')<span class="pull-right"><a href="{{$insurance ->website}}" target="_blank">{{ $insurance ->website }}</a></span>@endif</li>

                </ul>

            </div>

        </div><!-- /.box-body -->

        <!--Sub Menu-->

        <?php  $activetab = 'insurance';
			$routex = explode('/',Route::current()->uri());            
		?>

        @if(count($routex) > 3)
	        @if($routex[3] == 'insuranceoverrides')
	        	<?php $activetab = 'insuranceoverrides'; ?>
	        @endif
                
                @if($routex[3] == 'insuranceappealaddress')
                <?php $activetab = 'insuranceappealaddress'; ?>
        @endif
                
        @endif
    </div>




    <div class="med-tab nav-tabs-custom margin-t-10 no-bottom">
        <ul class="nav nav-tabs">
          @if($checkpermission->check_adminurl_permission('admin/insurance/{insurance}') == 1)
            <li class="@if($activetab == 'insurance') active @endif"><a href="javascript:void(0)" data-url="{{ url('admin/insurance/'.$insurance->id) }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.insurance')}} m-r-0" data-name="bank"></i> Insurance Details</a></li>
            @endif

             @if($checkpermission->check_adminurl_permission('admin/insurance/{insurance_id}/insuranceappealaddress') == 1)
            <li class="@if($activetab == 'insuranceappealaddress') active @endif"> <a href="javascript:void(0)" data-url="{{ url('admin/insurance/'.$insurance->id.'/insuranceappealaddress') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.appealaddress')}}  m-r-0" data-name="mail"></i> Appeal Address</a></li>            
            @endif
            <!--
            @if($checkpermission->check_adminurl_permission('admin/insurance/{insurance_id}/insuranceoverrides') == 1)
            <li class="@if($activetab == 'insuranceoverrides') active @endif"><a href="javascript:void(0)" data-url="{{ url('admin/insurance/'.$insurance->id.'/insuranceoverrides') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.ins_overrides')}} m-r-0" data-name="download-alt"></i> Overrides</a></li>
            @endif				
					-->
    </div>

</div>
<!--End Sub Menu-->

