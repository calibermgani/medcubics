<div class="col-md-12 space-m-t-15 print-m-t-30">
    <div class="box-block">
        <div class="box-body">
			<?php $sch_current_page = Route::getFacadeRoot()->current()->uri(); ?>
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
                <?php
					$filename = @$provider->avatar_name . '.' . @$provider->avatar_ext;
					$img_details = [];
					$img_details['module_name']='provider';
					$img_details['file_name']=$filename;
					$img_details['practice_name']="";
					$img_details['class']='';
					$img_details['alt']='provider-image';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>
				{!! $image_tag !!}   
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-5 col-sm-9 col-xs-12">
                <h3>{{$provider->provider_name.' '.@$provider->degrees->degree_name}} <span class="med-orange">{{ $provider->short_name }} </span></h3>
                <p class="push">{{$provider->description}}</p>
            </div>
           

         <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 form-horizontal med-left-border">
                
             <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <?php $phone_class = (isset($provider->phone) && !empty($provider->phone))? "js-callmsg-clas cur-pointer": ""?>
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="{{$phone_class}}" data-phone= "{{@$provider->phone}}">
                        <span class="@if(@$provider->phone == '') nill @endif"> @if(@$provider->phone!=''){{ @$provider->phone }} <span class="@if(@$provider->phoneext != '')bg-ext @endif">{{ @$provider->phoneext }}</span> 
                            <span class="fa fa-phone-square margin-l-4 med-green" data-placement="bottom"  data-toggle="tooltip" data-original-title="Click"></span>
                            @else - Nil - @endif</span>
                            </span>
                    </div>
			 </div>
                <div class="form-group">
                     {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if($provider->fax =='') nill @endif">@if($provider->fax !='') {{ $provider->fax }} @else - Nil - @endif</span>
                    </div>                                    
                </div>  

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if($provider->email =='') nill @endif">@if($provider->email !='')<a href="mailto:{{ $provider->email }}">{{ $provider->email }}</a> @else - Nil - @endif </span>
                    </div>                                    
                </div>  

                <div class="form-group no-bottom">
                    {!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if($provider->website =='') nill @endif"> @if($provider->website !='') <a href="{{ $provider->website}}" target="_blank">{{ $provider->website}}</a> @else - Nil - @endif <span>
                    </div>
                </div>
            </div>
            
        </div><!-- /.box-body -->
<!-- Sub Menu -->
    </div><!-- /.box -->            
    
    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
            @if(strpos($sch_current_page, 'practiceproviderscheduler') !== false)
				<?php $hreff_val = url('practicescheduler/provider/'.$provider->id); ?>
			@else
				<?php $hreff_val = url('practiceproviderschedulerlist'); ?>
			@endif
			<li class="active"><a href="{{ $hreff_val }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} i-font-tabs"></i> Provider</a></li>
            <li class=""><a href="{{ url('practicefacilityschedulerlist') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.facility')}} i-font-tabs"></i> Facility</a></li>
        </ul>
    </div>
</div>