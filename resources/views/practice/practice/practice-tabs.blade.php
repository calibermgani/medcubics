<div class="col-md-12 space-m-t-15">
    <div class="box-block print-m-t-30">
        <div class="box-body">

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
                        <?php
							$filename = $practice->avatar_name . '.' . $practice->avatar_ext;
							$img_details = [];
							$img_details['module_name']='practice';
							$img_details['file_name']=$filename;
							$img_details['practice_name']="";
							$img_details['record_id']=$practice->id;
							$img_details['class']='';
							$img_details['alt']='practice-image';
							$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
						?>
                        {!! $image_tag !!}   
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
                <h3>{{ $practice->practice_name }}</h3>
                <p class="push">{{ $practice->practice_description }}</p>
                @if($practice->practice_link != '')
					@if(strpos($practice->practice_link, "http://") !== false)
						<a href="{{ $practice->practice_link }}" target="_blank"><button class="btn btn-medcubics-small hidden-md hide" type="button">Know More</button></a>
					@else
						<a href="http://{{ $practice->practice_link }}" target="_blank"><button class="btn btn-medcubics-small hidden-md hide" type="button">Know More</button></a>
					@endif
                @endif
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal med-left-border print-m-t-m-5">

                <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <?php $phone_class = (isset($practice->phone) && !empty($practice->phone))? "js-callmsg-clas cur-pointer": ""; ?>
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="{{$phone_class}}" data-phone= "{{@$practice->phone}}" data-user_id="{{@$practice->id}}" data-user_type="practice">
                            <span class="@if(@$practice->phone == '') nill @endif"> @if(@$practice->phone!=''){{ @$practice->phone }} <span class="@if(@$practice->phoneext != '')bg-ext @endif">{{ @$practice->phoneext }}</span> 
                                <span class="fa fa-phone-square margin-l-4 med-green" data-placement="bottom"  data-toggle="tooltip" data-original-title="Click"></span>
                                @else - Nil - @endif</span>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$practice->fax == '') nill @endif">@if(@$practice->fax != '') {{ @$practice->fax }} @else - Nil - @endif</span>
                    </div>                                    
                </div> 

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$practice->email == '') nill @endif">@if(@$practice->email != '') <a href="mailto:{{ @$practice->email }}">{{ @$practice->email }}</a> @else - Nil - @endif</span>
                    </div>                                    
                </div> 

                <div class="form-group">
                    {!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$practice->practice_link == '') nill @endif">
							@if(@$practice->practice_link !='')
								@if(strpos($practice->practice_link, "http://") !== false)
									<a href="{{ @$practice->practice_link}}" target="_blank">{{ str_limit( @$practice->practice_link, 25, '...') }}</a>
								@else
									<a href="http://{{ $practice->practice_link }}" target="_blank">{{ str_limit( @$practice->practice_link, 25, '...') }}</a>
								@endif
							@else 	
								- Nil - 
							@endif
						</span>
                    </div>
                </div>

                @if(@$practice->facebook != '')
                <div class="form-group">
                    {!! Form::label('Facebook', 'Facebook',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$practice->facebook == '') nill @endif"> @if(@$practice->facebook !='')<a href="{{ @$practice->facebook}}" target="_blank">{{ @$practice->facebook }}</a> @else - Nil - @endif</span>
                    </div>
                </div>
                @endif
            </div>
        </div><!-- /.box-body -->

        <!-- Sub Menu -->
        <?php 
			$activetab = 'practice_details'; 
        	$routex = explode('.',Route::currentRouteName());
			if($routex[0] == 'practice') {
				$activetab = 'practice_details';
			} elseif($routex[0] == 'overrides') {
				$activetab = 'overrides';
			} elseif($routex[0] == 'managecare') {
				$activetab = 'managedcare';
			} elseif($routex[0] == 'contactdetail'){
				$activetab = 'contact_details';
			} elseif($routex[0] == 'document') {
				$activetab = 'document';
			} elseif($routex[0] == 'notes') {
				$activetab = 'notes';
			}
        ?>

    </div><!-- /.box -->

    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">

            @if($checkpermission->check_url_permission('practice/{practice}') == 1) 
            <li class="@if($activetab == 'practice_details') active @endif"><a href="javascript:void(0)" data-url="{{ url('practice/'.$practice->id) }}" class="js_next_process"><i class="fa i-font-tabs {{Config::get('cssconfigs.Practicesmaster.practice')}}"></i> Practice Details</a></li>
            @endif	
            {{-- <!--<li class="@if($activetab == 'overrides') active @endif"><a href="{{ url('overrides') }}" ><i class="fa fa-upload i-font-tabs"></i> Overrides</a></li>--> --}}
            @if($checkpermission->check_url_permission('managecare') == 1) 		
            <li class="@if($activetab == 'managedcare') active @endif"><a href="javascript:void(0)" data-url="{{ url('managecare') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} i-font-tabs"></i> Managed Care</a></li>
            @endif
            @if($checkpermission->check_url_permission('contactdetail') == 1) 	
            <li class="@if($activetab == 'contact_details') active @endif"><a href="javascript:void(0)" data-url="{{ url('contactdetail') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} i-font-tabs"></i> Contact Details</a></li>
            @endif	
            @if($checkpermission->check_url_permission('document') == 1) 	
            <li class="@if($activetab == 'document') active @endif hide"><a href="javascript:void(0)" data-url="{{ url('document') }}" class="js_next_process"> <i class="fa {{Config::get('cssconfigs.Practicesmaster.document_open')}} i-font-tabs"></i> Documents</a></li>
            @endif	
            @if($checkpermission->check_url_permission('notes') == 1) 
            <li class="@if($activetab == 'notes') active @endif"><a href="javascript:void(0)" data-url="{{ url('notes') }}" class="js_next_process"> <i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}} i-font-tabs"></i> Notes</a></li>
            @endif	
        </ul>
    </div>
</div>  