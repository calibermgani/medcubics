@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
@php  $customer->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($customer->id,'encode'); @endphp
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>New Practice</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$customer->id.'/customerpractices') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>

             @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
             @endif
        </ol>
    </section>

</div>
@stop

@section('practice-info')
{!! Form::open(['url'=>'admin/customer/'.$customer->id.'/customerpractices','name'=>'myform','role' => 'form','action' => '','class' => 'medcubicsform', 'files' => true,'id'=>'js-bootstrap-validator']) !!}
{!! Form::hidden('customer_id',$customer->id) !!}
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-15">
    <div class="box-block box-info">
        <div class="box-body">

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-new thumbnail">
                            <div class="safari_rounded">{!! HTML::image('img/noimage.png','Default image',array('class' => 'default')) !!}</div></div>
                        <div class="fileupload-preview fileupload-exists thumbnail"></div>
                        <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new" ><i class="fa fa-camera gray-button-camera" data-placement="bottom" data-toggle="tooltip" data-original-title="Add Logo" ></i></span>
                                <span class="fileupload-exists"><i class="livicon tooltips m-r-0 margin-t-0" data-placement="bottom"  data-name="camera" data-color="#009595" data-size="16" data-title='Change Image' data-hovercolor="#009595"></i></span>
                                {!! Form::file('image',['class'=>'default','accept'=>'image/png, image/gif, image/jpeg']) !!}
                            </span>
							<span><a class="js-delete-confirm js_image_delete hide" data-text="Are you sure would you like to delete?" href=""><i class="fa fa-trash" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete" style="border:1px solid #00877f; padding: 5px 8px 7px 8px; border-radius: 50%;"></i></a>
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
                    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-12 @if($errors->first('practice_name')) error @endif">
                        {!! Form::text('practice_name', null,['placeholder' => 'Enter the Practice Name','class'=>'form-control','id'=>'practice_name']) !!}
                        {!! $errors->first('practice_name', '<p> :message</p>')  !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-12 @if($errors->first('practice_description')) error @endif">
                        {!! Form::textarea('practice_description', null,['placeholder' => 'Enter the Description','class'=>'form-control','id'=>'description']) !!}
                        {!! $errors->first('practice_description', '<p> :message</p>')  !!}
                    </div>
                </div>
            </div>



            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal med-left-border">
                <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                        {!! Form::text('phone', null,['class'=>'dm-phone form-control input-sm-header-billing']) !!}
                    </div>
                    {!! Form::label('ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2">
                        {!! Form::text('phoneext', null,['class'=>'form-control dm-phone-ext input-sm-header-billing']) !!}
                    </div>
                </div>
                
                <div class="form-group">
                    {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                       {!! Form::text('fax', null,['class'=>'dm-fax form-control input-sm-header-billing']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        {!! Form::text('email', null,['class'=>'form-control js-email-letters-lower-format input-sm-header-billing']) !!}
                    </div>                                    
                </div> 
                
                <div class="form-group">
                    {!! Form::label('website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        {!! Form::text('website', null,['class'=>'form-control input-sm-header-billing']) !!}
                    </div>                                    
                </div> 
                
            <!--    <div class="form-group">
                    {!! Form::label('facebook', 'Facebook',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        {!! Form::text('facebook', null,['class'=>'form-control input-sm-header-billing']) !!}
                    </div>                                    
                </div>
                
                <div class="form-group">
                    {!! Form::label('twitter', 'Twitter',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        {!! Form::text('twitter', null,['class'=>'form-control input-sm-header-billing']) !!}
                    </div>                                    
                </div>	-->
            </div>
        </div><!-- /.box-body -->
    </div>
</div>

<!--End Sub Menu-->
@stop


@section('practice')
               @include ('admin/customer/customerpractices/form',['submitBtn'=>'Save'])
{!! Form::close() !!}

@stop

@push('view.scripts1')
{!! HTML::script('js/address_check.js') !!}
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