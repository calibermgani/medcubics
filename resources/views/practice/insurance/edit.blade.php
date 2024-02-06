@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('insurance/'.$insurance->id) }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
{!! Form::model($insurance, ['method'=>'PATCH','id'=>'js-bootstrap-validator','role' => 'form','action' => '', 'files' => true ,'url'=>'insurance/'.$insurance->id,'name'=>'medcubicsform','class'=>'medcubicsform']) !!} 

<div class="col-md-12 space-m-t-15"><!-- Col starts -->
    <div class="box-block box-info"><!-- Box Ends -->
        <div class="box-body"><!-- Box Body Starts -->

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="fileupload {{ ($insurance->avatar_name == '' )? 'fileupload-new' : 'fileupload-exists' }}" data-provides="fileupload">
                        <div class="fileupload-new thumbnail"> 
							 <div class="safari_rounded">
                                {!! HTML::image('img/insurance-avator.jpg') !!}
                            </div>
						</div>
                        <div class="fileupload-preview fileupload-exists thumbnail">
							
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
                        <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new" ><i class="fa fa-camera gray-button-camera" data-placement="bottom" data-toggle="tooltip" data-original-title="Add Logo" ></i></span>
                                <span class="fileupload-exists"><i class="livicon tooltips m-r-0 margin-t-0" data-placement="bottom"  data-name="camera" data-color="#009595" data-size="16" data-title='Change Image' data-hovercolor="#009595"></i></span>
                                {!! Form::file('image',['class'=>'default js_img_clear','accept'=>'image/png, image/gif, image/jpeg']) !!}
                            </span>
							@if(@$insurance->avatar_name)
								<span><a class="js-delete-confirm image-preview js-delete-image" data-text="Are you sure to delete the image?" href="{{ url('insurance/'.$insurance->id.'/delete/'.$insurance->avatar_name) }}"><i class="livicon tooltips m-r-0 margin-t-2" data-placement="bottom"  data-name="trash" data-color="#009595" data-size="16" data-title='Delete Note' data-hovercolor="#009595"></i></a>
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

            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 med-right-border form-horizontal">
                <div class="form-group">
                    <div class="col-lg-8 col-md-8 col-sm-11 col-xs-12 @if($errors->first('insurance_name')) error @endif "> 
                        {!! Form::text('insurance_name',null,['placeholder'=>'Insurance Name','class'=>'form-control', 'maxlength'=>29, 'id' =>'inurance_name','autocomplete'=>'nope']) !!}
                        {!! $errors->first('insurance_name', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-11 col-xs-12 m-t-sm-5 m-t-xs-5">
                        {!! Form::text('short_name',null,['placeholder'=>'Short Name','class'=>'form-control js_all_caps_ins input-sm-modal-billing ','maxlength'=>12,'id'=>'short_name','autocomplete'=>'nope']) !!}
                        {!! $errors->first('short_name', '<p class="help-block"> :message</p>')  !!}
                         <?php /* Unique validation Check on show the issue*/?>   
                        <small class=" help-block ins_sht_name">
                        </small>
                    </div>
                </div>
				
                <div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 @if($errors->first('insurance_desc')) error @endif "> 
                        {!! Form::textarea('insurance_desc',null,['placeholder'=>'Insurance Description','class'=>'form-control','autocomplete'=>'nope']) !!}
                        {!! $errors->first('insurance_desc', '<p> :message</p>')  !!}
                    </div>
                </div>
            </div>   
            <!--End Address details -->             
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal">                                                
                
                <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7 @if($errors->first('phone')) error @endif">
                          {!! Form::text('phone1', null,['id'=>'phone','class'=>'form-control input-sm-modal-billing dm-phone','autocomplete'=>'nope']) !!}
                    </div>
                    {!! Form::label('st', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2">
                        {!! Form::text('phoneext', null,['class'=>'form-control input-sm-modal-billing dm-phone-ext','autocomplete'=>'nope']) !!}
                    </div>
                </div>   
                
                <div class="form-group">
                     {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('fax')) error @endif">
                        {!! Form::text('fax',null,['class'=>'form-control input-sm-modal-billing dm-phone','autocomplete'=>'nope']) !!}
                        {!! $errors->first('fax', '<p> :message</p>')  !!}
                    </div>                                    
                </div> 
                
                <div class="form-group">
                     {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('email')) error @endif">
                       {!! Form::text('email',null,['class'=>'form-control input-sm-modal-billing js-email-letters-lower-format','autocomplete'=>'nope']) !!}
                        {!! $errors->first('email', '<p> :message</p>')  !!}
                    </div>                                    
                </div>

                <div class="form-group">
                     {!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('website')) error @endif">
                       {!! Form::text('website',null,['class'=>'form-control input-sm-modal-billing','autocomplete'=>'nope']) !!}
                        {!! $errors->first('website', '<p> :message</p>')  !!}
                    </div>                                    
                </div>
                               
            </div>
        </div><!-- Box Body Ends -->
    </div><!-- Box Ends -->
</div><!-- Col ends -->
@stop

@section('practice')
@include ('practice/insurance/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop            