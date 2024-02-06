  
<div class="col-md-12 margin-t-m-18 print-m-t-30">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
                        <?php
							$filename = $provider->avatar_name . '.' . $provider->avatar_ext;
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

            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">              
                <h3>{{ $provider->provider_name.' '.@$provider->degrees->degree_name }} <span class="med-orange">{{ $provider->short_name }} </span></h3>

               <div class="col-lg-3 col-md-10 col-sm-10 col-xs-10 no-padding"> 
                    <h6><span class="med-green">NPI</span> : {{ @$provider->npi }} 
                            <?php $npi_flag = App\Models\NpiFlag::getNpiFlag('provider',$provider->id,'Individual');?>
                            <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi'],'provider'); ?>   
                                <?php echo $value; ?>
                            {!! Form::hidden('npi',$provider->npi,['id'=>'npi']) !!}
                                {!! Form::hidden('type','provider',['id'=>'type']) !!}
                                {!! Form::hidden('type_id',null,['id'=>'type_id']) !!}
                                {!! Form::hidden('type_category','Individual',['id'=>'type_category']) !!}
                                @include ('practice/layouts/npi_form_fields')
                                {!! $errors->first('npi', '<p> :message</p>')  !!}                       
                    </h6>
                </div>
                <!-- Added attachment icon for NPI
                 Revision 1 - Ref: MED-2654  06 Augest 2019: Pugazh -->
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <a id="document_add_modal_link_npi" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/npi')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_npi_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div> 
                
                <p class="push">{{ $provider->description }}</p>
                
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal med-left-border">
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
                        <span class="@if(@$provider->fax == '') nill @endif">@if(@$provider->fax != '') {{ @$provider->fax }} @else - Nil - @endif</span>
                    </div>                                    
                </div>                
                
                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$provider->email == '') nill @endif">@if(@$provider->email != '') <a href="mailto:{{ @$provider->email }}">{{ @$provider->email }}</a> @else - Nil - @endif</span>
                    </div>                                    
                </div> 

                <div class="form-group no-bottom">
                    {!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if(@$provider->website == '') nill @endif"> @if(@$provider->website != '')<a href="{{ @$provider->website }}" target="_blank">{{ str_limit( @$provider->website, 25, '...') }}</a> @else - Nil - @endif</span>
                    </div>
                </div>
            </div>
        </div><!-- /.box-body -->

        <?php
            $activetab = 'provider_details';
            $routex = explode('.', Route::currentRouteName());
            if ($routex[0] == 'provider') {
                $activetab = 'provider_details';
            } elseif ($routex[0] == 'providermanagecare') {
                $activetab = 'managedcare';
            } elseif ($routex[0] == 'providerdocuments') {
                $activetab = 'document';
            } elseif ($routex[0] == 'notes') {
                $activetab = 'notes';
            }
        ?>
        
    </div><!-- /.box -->
    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
            @if($checkpermission->check_url_permission('provider') == 1)
            <li class="@if($activetab == 'provider_details') active @endif"><a href="{{ url('provider/'.$provider->id) }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} i-font-tabs"></i> Provider Details</a></li>
            @endif	
            <!--
                        {{--
                        @if($checkpermission->check_url_permission('provider/{providerid}/provideroverrides') == 1)
            <li class="@if($activetab == 'overrides') active @endif"><a href="{{ url('provider/'.$provider->id.'/provideroverrides') }}" ><i class="fa fa-upload i-font-tabs"></i> Overrides</a></li>
            @endif
                        --}}
            -->

            @if($checkpermission->check_url_permission('provider/{provider_id}/providermanagecare') == 1)
				<li class="@if($activetab == 'managedcare') active @endif"><a href="{{ url('provider/'.$provider->id.'/providermanagecare') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} i-font-tabs"></i> Managed Care</a></li>    
            @endif
            @if($checkpermission->check_url_permission('provider/{providerid}/provideroverrides'))		
				<li class="@if($activetab == 'document') active @endif"><a href="{{ url('provider/'.$provider->id.'/providerdocuments') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.document_open')}} i-font-tabs"></i> Documents</a></li>
            @endif	
			@if($checkpermission->check_url_permission('provider/{providerid}/provideroverrides'))	
				<li class="@if($activetab == 'notes') active @endif"><a href="{{ url('provider/'.$provider->id.'/notes') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}} i-font-tabs"></i> Notes</a></li>
			@endif
        </ul>
    </div>
</div>