@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Provider <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Provider</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
           @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
           @endif
        </ol>
    </section>

</div>
@stop

@section('practice-info')
{!! Form::open(array('url' => 'admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers','id'=>'js-bootstrap-validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform')) !!}

{!! Form::hidden('customer_id',$cust_id) !!}
{!! Form::hidden('practice_id',$practice_id) !!}

<div class="col-md-12 margin-t-m-18">
    <div class="box-block box-info">
        <div class="box-body">

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-new thumbnail">{!! HTML::image('img/noimage.png') !!}</div>
                        <div class="fileupload-preview fileupload-exists thumbnail"></div>
                        <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new" ><i class="fa fa-camera gray-button-camera" data-placement="bottom" data-toggle="tooltip" data-original-title="Add Logo" ></i></span>
                                <span class="fileupload-exists"><i class="livicon tooltips m-r-0 margin-t-0" data-placement="bottom"  data-name="camera" data-color="#009595" data-size="16" data-title='Change Image' data-hovercolor="#009595"></i></span>
                                {!! Form::file('image',['class'=>'default','accept'=>'image/png, image/gif, image/jpeg']) !!}
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

            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 form-horizontal med-right-border">
                <div class="form-group">
                    <div class="col-lg-4 col-md-8 col-sm-10 col-xs-10 @if($errors->first('npi')) error @endif">
                        {!! Form::text('npi', null,['placeholder' => 'NPI','class'=>'form-control js-npi-check-master dm-npi','id'=>'npi']) !!}
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

                

                <div class="form-group @if(Input::old('enumeration_type') == 'NPI-2') hide @endif" id="npi_field_individual">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10 @if($errors->first('last_name')) error @endif">
                        {!! Form::text('last_name',null,['placeholder'=>'Last Name','class'=>'form-control js-letters-caps-format','id'=>'last_name']) !!}                        
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 m-t-xs-5">
                        {!! Form::text('first_name',null,['placeholder'=>'First Name','class'=>'form-control js-letters-caps-format','id'=>'first_name']) !!}
					</div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 m-t-xs-5">
                        {!! Form::text('middle_name',null,['placeholder'=>'MI','title'=>'Middle Initial','class'=>'form-control dm-mi js-letters-caps-format','id'=>'middle_name']) !!}
						{!! $errors->first('middle_name', '<p> :message</p>')  !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 @if($errors->first('short_name')) error @endif">
                        {!! Form::text('short_name',null,['placeholder'=>'Provider short Name','class'=>'form-control input-sm-modal-billing js_all_caps_format dm-shortname','id'=>'short_name']) !!}
						{!! $errors->first('short_name', '<p> :message</p>')  !!}
                    </div>
                </div>
                <div class="form-group @if(Input::old('enumeration_type') != 'NPI-2') hide @endif" id="npi_field_group">
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

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal ">
                
                
                <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                        {!! Form::text('phone', null,['id'=>'phone','class'=>'form-control dm-phone']) !!}
                    </div>
                    {!! Form::label('ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2">
                         {!! Form::text('phoneext', null,['class'=>'form-control dm-phone-ext']) !!}
                    </div>
                </div>
                
                <div class="form-group">
                    {!! Form::label('Fax', 'Fax',  ['id'=>'fax','class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                       {!! Form::text('fax', null,['id'=>'fax','class'=>'form-control dm-fax']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('website')) error @endif">
                        {!! Form::text('email', null,['class'=>'form-control js-email-letters-lower-format']) !!}
                        {!! $errors->first('email', '<p> :message</p>')  !!}
                    </div>                                    
                </div>
                
                <div class="form-group">
                    {!! Form::label('website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('website')) error @endif">
                        {!! Form::text('website', null,['class'=>'form-control input-sm-header-billing']) !!}
                        {!! $errors->first('website', '<p> :message</p>')  !!}
                    </div>                                    
                </div>                
            </div>
        </div><!-- /.box-body -->
    </div>
</div>

@stop

@section('practice')
    <!--1st Data-->
    @include ('admin/customer/practiceproviders/form',['submitBtn'=>'Save'])
    <!--End-->
	{!! Form::close() !!}
@stop            

@push('view.scripts1')
<script type="text/javascript">
$(document).on('keyup', '.js-email-letters-lower-format', function (e) {
    if (!(e.keyCode == 8) && !(e.keyCode == 16) && !(e.keyCode == 35) && !(e.keyCode == 36) && !(e.keyCode == 37) && !(e.keyCode == 38) && !(e.keyCode == 39) && !(e.keyCode == 40)) {
        var str = $(this).val();
        var str1 = str.replace(/\w\S*/g, function (txt) {
            return txt.charAt(0).toLowerCase() + txt.substr(1).toLowerCase();
        });
        var start = this.selectionStart,
            end = this.selectionEnd;
        $(this).val(str1);
        this.setSelectionRange(start, end);
    }
});
</script>
@endpush