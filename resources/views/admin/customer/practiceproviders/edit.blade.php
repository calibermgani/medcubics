@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php 
$provider->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider->id,'encode'); 
$practice_decode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice_name->id,'decode');
?> 
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Provider <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Provider</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers/'.$provider->id) }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>            
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
                <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop

@section('practice-info')
{!! Form::model($provider, ['method'=>'PATCH', 'url'=>'admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers/'.$provider->id,'id'=>'js-bootstrap-validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}

{!! Form::hidden('customer_id',$cust_id) !!}
{!! Form::hidden('practice_id',$practice_id) !!}

<div class="col-md-12 margin-t-m-18">
    <div class="box-block box-info js-edit">
        <div class="box-body">

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-new thumbnail">						 
    						<?php
    							$filename = @$provider->avatar_name.'.'.@$provider->avatar_ext;
    							$unique_practice = md5('P'.$practice_decode_id);
    							$img_details = [];
    							$img_details['module_name']='provider';
    							$img_details['file_name']=$filename;
    							$img_details['practice_name']=$unique_practice;
    							
    							$img_details['class']='img-border';
    							$img_details['alt']='provider-image';
    							$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
    						?>
    						{!! $image_tag !!}            
                        </div>
                        <div class="fileupload-preview fileupload-exists thumbnail"></div>
                        <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new" ><i class="fa fa-camera gray-button-camera" data-placement="bottom" data-toggle="tooltip" data-original-title="Add Logo" ></i></span>
                                <span class="fileupload-exists"><i class="livicon tooltips m-r-0 margin-t-0" data-placement="bottom"  data-name="camera" data-color="#009595" data-size="16" data-title='Change Image' data-hovercolor="#009595"></i></span>
                                {!! Form::file('image',['class'=>'default','accept'=>'image/png, image/gif, image/jpeg']) !!}
                            </span>
							@if(@$provider->avatar_name)
								<span><a class="js-delete-confirm image-preview" data-text="Are you sure would you like to delete this picture ?" href="{{ url('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/provider/'.$provider->id.'/delete/'.$provider->avatar_name) }}"><i 
								class="livicon tooltips m-r-0 margin-t-2" data-placement="bottom"  data-name="trash" data-color="#009595" data-size="16" data-title='Delete Note' data-hovercolor="#009595"></i></a>
								</span>
							@endif
						</div>
						@if($errors->first('image'))
							<div class="error" >
								{!! $errors->first('image', '<p > :message</p>')  !!}
							</div>
						@endif
                    </div>
                </div>
            </div>                        

            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 form-horizontal med-right-border">
                <div class="form-group">
                    <div class="col-lg-4 col-md-8 col-sm-10 col-xs-10 padding-t-5 @if($errors->first('npi')) error @endif">
                        {!! Form::text('npi', null,['placeholder' => 'NPI','class'=>'form-control js-npi-check-master dm-npi','id'=>'npi']) !!}
						{!! Form::hidden('npi',$provider->npi,['class'=>'form-control']) !!}
                        {!! Form::hidden('type','provider',['id'=>'type']) !!}
                        {!! Form::hidden('type_id',null,['id'=>'type_id']) !!}
                        {!! Form::hidden('type_category','Individual',['id'=>'type_category']) !!}
                        @include ('practice/layouts/npi_form_fields')
                        {!! $errors->first('npi', '<p> :message</p>')  !!}
                    </div>
                   
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">                       
                        <span class="js-npi-individual-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                        <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi'], 'induvidual'); ?>   
                        <?php echo $value;?>
                    </div>
                </div>

				<?php
                if (Input::old('enumeration_type') == 'NPI-2' || $npi_flag['enumeration_type'] == 'NPI-2') {
                    $first_name = 'a';
                    $last_name = 'a';
                } elseif (Input::old('first_name') != '' || Input::old('last_name') != '') {
                    $first_name = Input::old('first_name');
                    $last_name = Input::old('last_name');
                } else {
                    $first_name = $provider->first_name;
                    $last_name = $provider->last_name;
                }
                if (Input::old('enumeration_type') == 'NPI-2') {
                    $individual = 'hide';
                    $group = '';
                } elseif ($npi_flag['enumeration_type'] == 'NPI-2' && $npi_flag['is_valid_npi'] == 'Yes' && Input::old('enumeration_type') != 'NPI-1') {
                    $individual = 'hide'; 
                    $group = '';
                } else {
					if($provider->provider_entity_type == 'Person'){
						$individual = '';
						$group = 'hide';
					}else{
						$individual = 'hide';
						$group = '';
					}
                }
				?>
                
                <div class="form-group {{$individual}}" id="npi_field_individual">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10 @if($errors->first('last_name')) error @endif">
                        {!! Form::text('last_name',$last_name,['placeholder'=>'Last Name','class'=>'form-control','id'=>'last_name']) !!}                        
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                        {!! Form::text('first_name',$first_name,['placeholder'=>'First Name','class'=>'form-control','id'=>'first_name']) !!}
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                        {!! Form::text('middle_name',null,['placeholder'=>'Middle Initial','class'=>'form-control dm-mi','id'=>'middle_name']) !!}    
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 @if($errors->first('short_name')) error @endif">
                        {!! Form::text('short_name',null,['placeholder'=>'Provider short Name','class'=>'form-control input-sm-modal-billing js_all_caps_format dm-shortname','id'=>'short_name']) !!}
						{!! $errors->first('short_name', '<p> :message</p>')  !!}
                    </div>
                </div>

                <div class="form-group {{$group}}" id="npi_field_group">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 @if($errors->first('organization_name')) error @endif">
                        {!! Form::text('organization_name',null,['placeholder'=>'Organization Name','class'=>'form-control','id'=>'organization_name','style'=>'margin-bottom:10px;']) !!}                        
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 @if($errors->first('description')) error @endif">
                        {!! Form::textarea('description',null,['placeholder'=>'Description','class'=>'form-control','style'=>'min-height:60px;']) !!}
                        {!! $errors->first('description', '<p> :message</p>')  !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal">
                
                <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                        {!! Form::text('phone', $provider->phone,['id'=>'phone','class'=>'form-control dm-phone']) !!}
                    </div>
                    {!! Form::label('ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2">
                         {!! Form::text('phoneext', null,['class'=>'form-control dm-phone-ext']) !!}
                    </div>
                </div>
                
                <div class="form-group">
                    {!! Form::label('Fax', 'Fax',  ['id'=>'fax','class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                       {!! Form::text('fax', $provider->fax,['id'=>'fax','class'=>'form-control dm-fax']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        {!! Form::text('email', $provider->email,['class'=>'form-control js-email-letters-lower-format']) !!}
                    </div>                                    
                </div>

                <div class="form-group">
                    {!! Form::label('website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        {!! Form::text('website', $provider->website,['class'=>'form-control input-sm-header-billing']) !!}
                    </div>                                    
                </div>                 
            </div>
        </div><!-- /.box-body -->
    </div>
</div>
<!--End Sub Menu-->
@stop

@section('practice')
<!--1st Data-->
@include ('admin/customer/practiceproviders/form',['submitBtn'=>'Save'])    
{!! Form::close() !!}                                                                        
@stop            