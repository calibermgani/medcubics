@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa font14 {{Config::get('cssconfigs.Practicesmaster.practice')}}"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span></small>
		</h1>
        <ol class="breadcrumb">
            <?php $practice->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice->id,'encode'); ?>
           
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Row Ends -->
@stop

@section('practice-info')

{!! Form::model($practice, ['method'=>'PATCH','id'=>'js-bootstrap-validator','name'=>'myform','files'=>true,'url'=>'practice/'.$practice->id,'class'=>'medcubicsform']) !!}
<div class="col-md-12 space-m-t-15 js-edit"><!-- Col 12 Starts -->
    <div class="box-block"><!-- Box Starts -->
        <div class="box-body"><!-- Box Body Starts -->
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
			<div class="text-center">
                    <div class="fileupload {{ ($practice->avatar_name == '' )? 'fileupload-new' : 'fileupload-exists' }}" data-provides="fileupload">
                        <div class="fileupload-new thumbnail"> 
							 <div class="safari_rounded">
                                {!! HTML::image('img/practice-avatar.jpg') !!}
                            </div>
						</div>
                        <div class="fileupload-preview fileupload-exists thumbnail">
							<?php
								$filename = $practice->avatar_name . '.' . $practice->avatar_ext;
								$img_details = [];
								$img_details['module_name']='practice';
								$img_details['file_name']=$filename;
								$img_details['practice_name']="";
								
								$img_details['class']='';
								$img_details['alt']='practice-image';
								$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
							?>
							{!! $image_tag !!} 
						</div>
                        <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new" ><i class="fa fa-camera gray-button-camera" data-placement="bottom" data-toggle="tooltip" data-original-title="Add Logo" ></i></span>
                                <span class="fileupload-exists"><i class="livicon tooltips m-r-0 margin-t-0" data-placement="bottom"  data-name="camera" data-color="#009595" data-size="16" data-title='Change Image' data-hovercolor="#009595"></i></span>
                                {!! Form::file('image',['class'=>'default js_img_clear','accept'=>'image/png, image/gif, image/jpeg']) !!}
                            </span>
							@if(@$practice->avatar_name)
								<span><a class="js-delete-confirm image-preview js-delete-image" data-text="Are you sure would you like to remove ?" href="{{ url('practice/'.$practice->id.'/delete/'.$practice->avatar_name) }}"><i class="livicon tooltips m-r-0 margin-t-2" data-placement="bottom"  data-name="trash" data-color="#009595" data-size="16" data-title='Delete Note' data-hovercolor="#009595"></i></a>
								</span>
							@endif
							<span class="fileupload-remove hide"><a href="#" class="image-preview" data-toggle="modal" data-target="#superbill_modal"><i class="livicon tooltips m-r-0 margin-t-2" data-placement="bottom"  data-name="trash" data-color="#009595" data-size="16" data-title='Delete Image' data-hovercolor="#009595"></i></a>
							</span>
                        </div>
						@if($errors->first('image'))
							<div class="error" >
								{!! $errors->first('image', '<p > :message</p>')  !!}
							</div>
						@endif
                    </div>
                </div>
                
            </div>

            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
                <h3>{{ $practice->practice_name }}</h3>
                <p class="push">{{ $practice->practice_description }}</p>
                @if($practice->practice_link != '') <a href="{{ $practice->practice_link }}" target="blank"><button class="btn btn-medcubics-small" type="button">Know More</button></a>@endif
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal med-left-border">

                <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                        {!! Form::text('phone', @$practice->phone,['class'=>'dm-phone form-control input-sm-header-billing','autocomplete'=>'off']) !!}
                    </div>
                    {!! Form::label('ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2">
                        {!! Form::text('phoneext', @$practice->phoneext,['class'=>'form-control dm-phone-ext input-sm-header-billing','autocomplete'=>'off']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        {!! Form::text('fax', @$practice->fax,['class'=>'form-control input-sm-header-billing dm-fax','autocomplete'=>'off']) !!}
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
                        {!! Form::text('practice_link', @$practice->practice_link,['class'=>'form-control input-sm-header-billing','autocomplete'=>'off']) !!}
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
        </div><!-- /.box-body ends -->

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
			} elseif($routex[0] == 'contactdetail') {
				$activetab = 'contact_details';
			} elseif($routex[0] == 'document') {
				$activetab = 'document';
			} elseif($routex[0] == 'notes') {
				$activetab = 'notes';
			}
		?>
    </div><!-- /.box ends -->
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
</div><!-- Col 12 Ends -->
@stop

@section('practice')
<?php
	$provider_count = App\Models\Practice::getProviderCount($practice->id); 
	$facility_count = App\Models\Practice::getFacilityCount($practice->id);
?>
@include ('practice/practice/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop
