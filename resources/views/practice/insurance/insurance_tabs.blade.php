<div class="col-md-12 margin-t-m-18 print-m-t-30">
    <div class="box-block"><!-- Box Starts -->
        <div class="box-body">
          
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
                <?php
					$filename = $insurance->avatar_name . '.' . $insurance->avatar_ext;
					$img_details = [];
					$img_details['module_name']='insurance';
					$img_details['file_name']=$filename;
					$img_details['practice_name']="";
					
					$img_details['class']='';
					$img_details['alt']='insurance-image';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>
				{!! $image_tag !!} 
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12"> 
                <h3>{{ $insurance->insurance_name }} <span class="med-orange">{{ $insurance->short_name }}</span> 
				
				

				</h3>				 
                {{ $insurance->insurance_desc }}
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal med-left-border">
				 <div class="form-group">
						{!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                                                <?php $phone_class = (isset($insurance->phone1) && !empty($insurance->phone1))? "js-callmsg-clas cur-pointer": ""?>
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="{{$phone_class}}" data-phone= "{{@$insurance->phone1}}">
                        <span class="@if(@$insurance->phone1 == '') nill @endif"> @if(@$insurance->phone1!=''){{ @$insurance->phone1 }} <span class="@if(@$insurance->phoneext != '')bg-ext @endif">{{ @$insurance->phoneext }}</span>
                            <span class="fa fa-phone-square margin-l-4 med-green" data-placement="bottom"  data-toggle="tooltip" data-original-title="Click"></span>
                            @else - Nil - @endif</span>
                        </span>
					</div>
				 </div>	
				 <div class="form-group">
                     {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$insurance->fax == '') nill @endif">@if(@$insurance->fax != '') {{ @$insurance->fax }} @else - Nil - @endif</span>
                    </div>                                    
                </div> 
				<div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$insurance->email == '') nill @endif">@if(@$insurance->email != ''){{ @$insurance->email }} @else - Nil - @endif </span>
                    </div>                                    
                </div> 
				<div class="form-group no-bottom">
                    {!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$insurance->website == '') nill @endif"> @if(@$insurance->website != '') <a href="{{ @$insurance->website}}" target="_blank">{{ str_limit($insurance ->website, 25, '.....')}}</a> @else - Nil - @endif <span>
                    </div>
                </div>
			   
            </div>
        </div><!-- /.box-body -->

        <!--Sub Menu-->

        <?php $activetab = 'insurance'; 
	       $routex = explode('.',Route::currentRouteName());
        ?>

        @if(count($routex) > 1)
        @if($routex[0] == 'insuranceoverrides')
        <?php $activetab = 'insuranceoverrides'; ?>
        @endif

        @if($routex[0] == 'insuranceappealaddress')
        <?php $activetab = 'insuranceappealaddress'; ?>
        @endif

        @endif
    

    </div><!-- Box Ends -->
    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
		@if($checkpermission->check_url_permission('insurance') == 1)
            <li class="@if($activetab == 'insurance') active @endif"><a href="{{ url('insurance/'.$insurance->id) }}" ><i class="fa {{Config::get('cssconfigs.common.insurance')}} i-font-tabs"></i> Insurance Details</a></li>            
		@endif	
		@if($checkpermission->check_url_permission('insurance/{insuranceid}/insuranceappealaddress') == 1)
            <li class="@if($activetab == 'insuranceappealaddress') active @endif"><a href="{{ url('insurance/'.$insurance->id.'/insuranceappealaddress') }}" ><i class="fa {{Config::get('cssconfigs.common.appealaddress')}}  i-font-tabs"></i> Appeal Address</a></li>            
		@endif
		@if($checkpermission->check_url_permission('insurance/{insurance_id}/insuranceoverrides') ==1)	
         <!--   <li class="@if($activetab == 'insuranceoverrides') active @endif"><a href="{{ url('insurance/'.$insurance->id.'/insuranceoverrides') }}"><i class="fa {{Config::get('cssconfigs.common.ins_overrides')}} i-font-tabs"></i> Overrides</a></li>  -->
		@endif		
        </ul>
    </div>
</div>
@include('practice/layouts/favourite_modal') 
<!--End Sub Menu-->