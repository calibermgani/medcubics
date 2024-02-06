@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} font14"></i> Provider <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('provider') }}" class="js_next_process hide" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
{!! Form::open(array('url' => 'trail/provider/store','id'=>'js-bootstrap-validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform')) !!}
<div class="col-md-12 space-m-t-15">
    <div class="box-block box-info"><!-- Box Starts -->
        <div class="box-body"><!-- Box Body Starts -->
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-new thumbnail">
                            <div class="safari_rounded">
                                {!! HTML::image('img/noimage.png') !!}
                            </div>
                        </div>
                        <div class="fileupload-preview fileupload-exists thumbnail"></div>
                        <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new" ><i class="fa fa-camera gray-button-camera" data-placement="bottom" data-toggle="tooltip" data-original-title="Add Logo" ></i></span>
                                <span class="fileupload-exists"><i class="livicon tooltips m-r-0 margin-t-0" data-placement="bottom"  data-name="camera" data-color="#009595" data-size="16" data-title='Change Image' data-hovercolor="#009595"></i></span>
                                {!! Form::file('image',['class'=>'default','accept'=>'image/png, image/gif, image/jpeg']) !!}
                            </span>
							<span><a class="js-delete-confirm js_image_delete @if(@$provider->avatar_name =="") hide @endif" data-text="Are you sure to delete the image?" href=""><i class="fa fa-trash" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete" style="border:1px solid #00877f; padding: 5px 8px 7px 8px; border-radius: 50%;"></i></a></span>
                        </div>
                        @if($errors->first('image'))
                        <div class="error" >
                            {!! $errors->first('image', '<p > :message</p>')  !!}
                        </div>
                        @endif
                    </div>
                </div>
            </div>                        

            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 form-horizontal  ">
                <div class="form-group">
                    <div class="col-lg-4 col-md-4 col-sm-10 col-xs-10  @if($errors->first('npi')) error @endif">
                        {!! Form::text('npi', null,['placeholder' => 'NPI','class'=>'form-control input-sm-modal-billing dm-npi js-npi-check','id'=>'npi','autocomplete'=>'off']) !!}
                        {!! Form::hidden('type','provider',['id'=>'type']) !!}
                        {!! Form::hidden('type_id',null,['id'=>'type_id']) !!}
                        {!! Form::hidden('type_category','Individual',['id'=>'type_category']) !!}
                        @include ('practice/layouts/npi_form_fields')
                        {!! $errors->first('npi', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                        <a id="document_add_modal_link_npi" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/0/npi')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
                    </div>   
                    <div class="col-lg-2 col-md-1 col-sm-1 col-xs-1">
                        <span class="js-npi-individual-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                        <span class="js-npi-individual-success hide"><a data-toggle="modal" href="" data-target="#form-npi-modal"><i class="fa fa-check icon-green-form"></i></a></span>
                        <span class="js-npi-individual-error hide"><a data-toggle="modal" href="" data-backdrop="false" data-target="#form-npi-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                        <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi'], 'induvidual'); ?>   
                        <?php echo $value; ?>

                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding @if(Input::old('enumeration_type') == 'NPI-2') hide @endif" id="npi_field_individual">
                    <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-10 p-r-0 @if($errors->first('last_name')) error @endif">
                        {!! Form::text('last_name',null,['placeholder'=>'Last Name','class'=>'form-control input-sm-modal-billing js-letters-caps-format','id'=>'last_name','autocomplete'=>'off']) !!}
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-6 m-t-xs-5 margin-l-20 p-r-0">
                        {!! Form::text('first_name',null,['placeholder'=>'First Name','class'=>'form-control js-letters-caps-format input-sm-modal-billing','id'=>'first_name','autocomplete'=>'off']) !!}
                    </div>
                    <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-4 m-t-xs-5 margin-l-20 p-r-0">
                        {!! Form::text('middle_name',null,['placeholder'=>'MI','class'=>'form-control input-sm-modal-billing dm-mi js-letters-caps-format','id'=>'middle_name','autocomplete'=>'off']) !!}    
                    </div>
                    <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-10 m-t-xs-5 margin-l-20 p-r-0 @if($errors->first('short_name')) error @endif">
                        {!! Form::text('short_name',null,['placeholder'=>'Short Name','data-placement'=>'bottom','data-toggle'=>'tooltip','data-original-title'=>'Short Name','class'=>'form-control input-sm-modal-billing js_all_caps_format dm-shortname','id'=>'short_name','autocomplete'=>'off']) !!}
						{!! $errors->first('short_name', '<p> :message</p>')  !!}
                    </div>
                </div>
                
                <div class="form-group @if(Input::old('enumeration_type') != 'NPI-2') hide @endif" id="npi_field_group">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 @if($errors->first('organization_name')) error @endif">
                        {!! Form::text('organization_name',null,['placeholder'=>'Organization Name','class'=>'form-control','id'=>'organization_name','autocomplete'=>'off']) !!}
                    </div>
                </div>                

                <div class="form-group">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 p-r-15 @if($errors->first('description')) error @endif">
                        {!! Form::textarea('description',null,['placeholder'=>'Description','class'=>'form-control','style'=>'min-height:70px;']) !!}
                        {!! $errors->first('description', '<p> :message</p>')  !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal med-left-border">

                <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                        {!! Form::text('phone', null,['id'=>'phone','class'=>'form-control input-sm-modal-billing dm-phone','autocomplete'=>'off']) !!}                        
                    </div>
                    {!! Form::label('ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2">
                        {!! Form::text('phoneext', null,['class'=>'form-control input-sm-modal-billing dm-phone-ext','autocomplete'=>'off']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10  @if($errors->first('fax')) error @endif">
                        {!! Form::text('fax', null,['id'=>'fax','class'=>'form-control input-sm-modal-billing dm-phone','autocomplete'=>'off']) !!}
                        {!! $errors->first('fax', '<p> :message</p>')  !!}    
                    </div>                                    
                </div>

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('email')) error @endif">
                        {!! Form::text('email', null,['class'=>'form-control input-sm-modal-billing js-email-letters-lower-format','autocomplete'=>'off']) !!}
                        {!! $errors->first('email', '<p> :message</p>')  !!}
                    </div>                                    
                </div>  

                <div class="form-group">
                    {!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('website')) error @endif">
                        {!! Form::text('website', null,['class'=>'form-control input-sm-modal-billing','autocomplete'=>'off']) !!}
                        {!! $errors->first('website', '<p> :message</p>')  !!}
                    </div>
                </div>                              
            </div>
        </div><!-- /.box-body -->
    </div><!-- Box Ends -->
</div><!-- Col 12 Ends -->
@stop

@section('practice')
@include ('practice/provider/trail_form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop            