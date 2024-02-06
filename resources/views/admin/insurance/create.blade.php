@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.insurance')}}  font14"></i> Insurance <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Insurance</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('admin/insurance/') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop

@section('practice-info')
{!! Form::open(['url'=>'admin/insurance','name'=>'myform','role' => 'form','action' => '','files' => true,'id'=>'js-bootstrap-validator','class'=>'medcubicsform']) !!}
<div class="col-md-12 margin-t-m-15">
    <div class="box-block box-info">
        <div class="box-body">

            <div class="form-group col-lg-2 col-md-2 col-sm-3 col-xs-12 text-center">
                <div class="text-center">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
						<img class="js_default_img hide"  src="{{URL('/img/insurance-avator.jpg')}}">
                        <div class="fileupload-new thumbnail">
                            <div class="safari_rounded">
								{!! HTML::image('img/insurance-avator.jpg') !!}
                            </div>
                        </div>
                        <div class="fileupload-preview fileupload-exists thumbnail"></div>
                        <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new" ><i class="fa fa-camera gray-button-camera" data-placement="bottom" data-toggle="tooltip" data-original-title="Add Logo" ></i></span>
                                <span class="fileupload-exists"><i class="livicon tooltips m-r-0 margin-t-0" data-placement="bottom"  data-name="camera" data-color="#009595" data-size="16" data-title='Change Image' data-hovercolor="#009595"></i></span>
                                {!! Form::file('avatar_url',['class'=>'default','accept'=>'image/png, image/gif, image/jpeg']) !!}
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
                    <div class="col-lg-8 col-md-8 col-sm-11 col-xs-12 @if($errors->first('insurance_name')) error @endif"> 
                        {!! Form::text('insurance_name',null,['maxlength'=>'28','placeholder'=>'Insurance Name','class'=>'form-control js-letters-caps-format']) !!}
                        {!! $errors->first('insurance_name', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-11 col-xs-12 m-t-sm-5 m-t-xs-5">
                        {!! Form::text('short_name',null,['placeholder'=>'Short Name','class'=>'form-control js_all_caps_ins input-sm-modal-billing ','maxlength'=>7,'id'=>'short_name']) !!}
                    </div>
                </div>
                
                
				
                <div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 @if($errors->first('insurance_desc')) error @endif ">
                        {!! Form::textarea('insurance_desc',null,['placeholder'=>'Insurance Description','class'=>'form-control']) !!}
                        {!! $errors->first('insurance_desc', '<p> :message</p>')  !!}
                    </div>
                </div>

            </div>

     <!--End Address details -->
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal">
            	
                <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7 @if($errors->first('phone')) error @endif">
                          {!! Form::text('phone1',null,['class'=>'form-control dm-phone']) !!}
                        {!! $errors->first('phone1', '<p> :message</p>')  !!}              
                    </div>
                    {!! Form::label('st', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2">
                        {!! Form::text('phoneext', null,['class'=>'form-control dm-phone-ext']) !!}
                    </div>
                </div>   
                
                <div class="form-group">
                     {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('fax')) error @endif">
                        {!! Form::text('fax',null,['class'=>'form-control dm-phone']) !!}
                        {!! $errors->first('fax', '<p> :message</p>')  !!}
                    </div>                                    
                </div> 

              <div class="form-group">
                     {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('email')) error @endif">
                       {!! Form::text('email',null,['class'=>'form-control js-email-letters-lower-format']) !!}
                        {!! $errors->first('email', '<p> :message</p>')  !!}
                    </div>                                    
                </div>

                 <div class="form-group">
                     {!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('website')) error @endif">
                       {!! Form::text('website',null,['class'=>'form-control']) !!}                      
                        {!! $errors->first('website', '<p> :message</p>')  !!}
                    </div>                                    
                </div>                             
             </div>
        </div>
    </div>
<!--Sub Menu-->
</div>
@stop

@section('practice')
@include ('admin/insurance/form',['submitBtn'=>'Save'])
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