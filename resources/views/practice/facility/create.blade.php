@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('facility') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/facility')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Row Ends -->
@stop

@section('practice-info')
{!! Form::open(['url'=>'facility','role' => 'form','action' => '','class' => 'medcubicsform js_address_limit', 'files' => true,'id'=>'js-bootstrap-validator','name'=>'medcubicsform']) !!}     


<div class="col-md-12 space-m-t-15"><!-- Col-12 Starts -->
    <div class="box-block box-info"><!-- Box Starts -->
        <div class="box-body">
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-new thumbnail">
                            <div class="safari_rounded">
                            {!! HTML::image('img/facility-avator.jpg') !!}
                            </div>
                        </div>
                        <div class="fileupload-preview fileupload-exists thumbnail"></div>
                        <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new" ><i class="fa fa-camera gray-button-camera" data-placement="bottom" data-toggle="tooltip" data-original-title="Add Logo" ></i></span>
                                <span class="fileupload-exists"><i class="livicon tooltips m-r-0 margin-t-0" data-placement="bottom"  data-name="camera" data-color="#009595" data-size="16" data-title='Change Image' data-hovercolor="#009595"></i></span>
                                {!! Form::file('image',['class'=>'default','accept'=>'image/png, image/gif, image/jpeg']) !!}
                            </span>
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

            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 form-horizontal ">
                <div class="form-group">                    
                    <div class="col-lg-8 col-md-8 col-sm-11 col-xs-12 @if($errors->first('facility_name')) error @endif">
                        {!! Form::text('facility_name', null,['placeholder' => 'Enter the Facility Name','class'=>'form-control js-letters-caps-format','id'=>'employer_name','autocomplete'=>'off']) !!}
                        {!! $errors->first('facility_name', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-11 col-xs-12 m-t-sm-5 m-t-xs-5 @if($errors->first('short_name')) error @endif">
                        {!! Form::text('short_name',null,['placeholder'=>'Short Name','class'=>'form-control input-sm-modal-billing js_all_caps_format dm-shortname','id'=>'short_name','autocomplete'=>'off']) !!}
						 {!! $errors->first('short_name', '<p> :message</p>')  !!}
                    </div>
                </div>
				
                <div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-12 @if($errors->first('description')) error @endif">
                        {!! Form::textarea('description', null,['placeholder' => 'Enter the Description','class'=>'form-control','id'=>'description']) !!}
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
                    {!! Form::label('st', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2">
                        {!! Form::text('phoneext', null,['class'=>'form-control input-sm-modal-billing dm-phone-ext','autocomplete'=>'off']) !!}
                    </div>
                </div>
                <div class="form-group">
                     {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        {!! Form::text('fax', null,['class'=>'form-control input-sm-modal-billing dm-phone','autocomplete'=>'off']) !!}
                    </div>                                    
                </div>  

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        {!! Form::text('email', null,['class'=>'form-control input-sm-modal-billing js-email-letters-lower-format','autocomplete'=>'off']) !!}
                    </div>                                    
                </div>  

                <div class="form-group">
                    {!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        {!! Form::text('website', null,['class'=>'form-control input-sm-modal-billing','autocomplete'=>'off']) !!}
                    </div>
                </div>
            </div>
        </div><!-- /.box-body ends -->
    </div><!-- Box Ends -->
</div><!-- Col-12 Ends -->

<!--End Sub Menu-->
@stop


@section('practice')
@include ('practice/facility/form',['submitBtn'=>'Save'])
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
