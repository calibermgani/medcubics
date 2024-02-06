<div class="col-md-12 margin-t-m-18 print-m-t-30">
    <div class="box-block">
        <div class="box-body">
		<?php $sch_current_page = Route::getFacadeRoot()->current()->uri(); ?>
			<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
                   <?php
						$filename = @$facility->avatar_name . '.' .@$facility->avatar_ext;
						$img_details = [];
						$img_details['module_name']='facility';
						$img_details['file_name']=$filename;
						$img_details['practice_name']="";
						
						$img_details['class']='';
						$img_details['alt']='facility-image';
						$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
					?>
					{!! $image_tag !!} 
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-5 col-sm-9 col-xs-12">
                <h3>{{ @$facility->facility_name }} <span class="med-orange">{{ @$facility->short_name }} </span></h3>
                <p class="push">{{@$facility->description}}</p>
            </div>
                        
            <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 form-horizontal med-left-border">
                
             <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <?php $phone_class = (isset($facility->facility_address->phone) && !empty($facility->facility_address->phone))? "js-callmsg-clas cur-pointer": ""?>
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="{{$phone_class}}" data-phone= "{{@$facility->facility_address->phone}}">
                            <span class="@if(@$facility->facility_address->phone == '') nill @endif"> @if(@$facility->facility_address->phone!=''){{ @$facility->facility_address->phone }} <span class="@if(@$facility->facility_address->phoneext != '')bg-ext @endif">{{ @$facility->facility_address->phoneext }}</span> 
                                <span class="fa fa-phone-square margin-l-4 med-green" data-placement="bottom"  data-toggle="tooltip" data-original-title="Click"></span>
                                @else - Nil - @endif</span>
                        </span>
                    </div>
             </div>
                <div class="form-group">
                     {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$facility->facility_address->fax == '') nill @endif">@if(@$facility->facility_address->fax != '') {{ @$facility->facility_address->fax }} @else - Nil - @endif</span>
                    </div>                                    
                </div>  

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$facility->facility_address->email == '') nill @endif">@if(@$facility->facility_address->email != '')<a href="mailto:{{ $facility->facility_address->email }}">{{ $facility->facility_address->email }}</a> @else - Nil - @endif </span>
                    </div>                                    
                </div>  

                <div class="form-group no-bottom">
                    {!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class=" @if(@$facility->website == '') nill @endif">  @if(@$facility->website != '') <a href="{{$facility->website}}" target="_blank">{{$facility->website}}</a> @else - Nil - @endif <span>
                    </div>
                </div>
            </div>

        </div><!-- /.box-body -->

    <!-- Sub Menu -->
    <?php 
		$activetab = 'scheduler'; 
    	$routex = explode('.',Route::currentRouteName());
		if(count($routex) > 0) {
			if($routex[0] == 'settings') {
				$activetab = 'settings';
			}
		}
    ?>   

    </div><!-- /.box -->         
    
    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
            <li class=""><a href="{{ url('practiceproviderschedulerlist') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} i-font-tabs"></i> Provider</a></li>
			
			@if(strpos($sch_current_page, 'practicefacilityscheduler') !== false)
				<?php $encode_facility_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($facility->id,'encode'); ?>	
				<?php $hreff_val = url('facilityscheduler/facility/'.$encode_facility_id); ?>
			@else
				<?php $hreff_val = url('practicefacilityschedulerlist'); ?>
			@endif
			
			<li class="active"><a href="{{ $hreff_val }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.facility')}} i-font-tabs"></i> Facility</a></li>
        </ul>
    </div>
    
</div>