@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('insurance/') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
{!! Form::open(['url'=>'insurance','role' => 'form','action' => '','files' => true,'id'=>'js-bootstrap-validator_ins','name'=>'medcubicsform1','class'=>'medcubicsform1']) !!}
<div class="col-md-12 space-m-t-15 margin-b-25">
    <div class="box-block box-info no-shadow"><!-- Box Starts -->
        <div class="box-body"><!-- Box Body Starts -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">
                <div class="form-group no-bottom">  
                    {!! Form::label('Insurance List', 'Insurance Search',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label margin-t-1']) !!} 
                         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        {!! Form::select('insurace_search_category_modal', ['insurance_name'=>'Insurance Name','payerid' => 'Payer ID','address' => 'Address'],'insurance_name',['class'=>'select2 form-control js_insurace_search_category_modal','id'=>'js_insurace_search_category_modal']) !!}
                    </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        {!! Form::text('insurance_list',null,['placeholder'=>'','class'=>'form-control js-letters-caps-format', 'maxlength'=>28,'id' =>'insurance_list','autocomplete'=>'off']) !!}
                    </div>
                </div>  

            </div>

            <div class="col-lg-2 col-md-4 col-sm-4">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                    <input class="btn btn-medcubics-small margin-t-2" data-url="{{ url('get_insurancelist') }}" id="js-search-ins" value="Search" type="button">
                </div>
            </div>
            
        </div>
    </div>
</div>
{!! Form::close() !!}

{!! Form::open(['url'=>'insurance','role' => 'form','action' => '','files' => true,'id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}

<div class="col-md-12 space-m-t-15"><!-- Col 12 starts -->
    <div class="box-block box-info"><!-- Box Starts -->
        <div class="box-body"><!-- Box Body Starts -->
             <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
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

            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 form-horizontal med-right-border">
                <div class="form-group">
                    <div class="col-lg-8 col-md-8 col-sm-11 col-xs-12 @if($errors->first('insurance_name')) error @endif"> 
                        {!! Form::text('insurance_name',null,['placeholder'=>'Insurance Name','class'=>'form-control', 'maxlength'=>29,'id' =>'inurance_name','autocomplete'=>'off']) !!}
                        {!! $errors->first('insurance_name', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-11 col-xs-12 m-t-sm-5 m-t-xs-5">
                        {!! Form::text('short_name',null,['placeholder'=>'Short Name','class'=>'form-control js_all_caps_ins input-sm-modal-billing ','maxlength'=>12,'id'=>'short_name','autocomplete'=>'off']) !!}
                        {!! $errors->first('short_name', '<p class="help-block"> :message</p>')  !!}
                         <?php /* Unique validation Check on show the issue*/?>   
                        <small class=" help-block ins_sht_name">
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 @if($errors->first('insurance_desc')) error @endif"> 
                        {!! Form::textarea('insurance_desc',null,['placeholder'=>'Insurance Description','class'=>'form-control','autocomplete'=>'off']) !!}
                        {!! $errors->first('insurance_desc', '<p> :message</p>')  !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal">                                

                <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7 @if($errors->first('phone')) error @endif">
                        {!! Form::text('phone1',null,['class'=>'form-control input-sm-modal-billing dm-phone','autocomplete'=>'off']) !!}
                        {!! $errors->first('phone1', '<p> :message</p>')  !!}              
                    </div>
                    {!! Form::label('st', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2">
                        {!! Form::text('phoneext', null,['class'=>'form-control dm-phone-ext input-sm-modal-billing','autocomplete'=>'off']) !!}
                    </div>
                </div>   

                <div class="form-group">
                    {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('fax')) error @endif">
                        {!! Form::text('fax',null,['class'=>'form-control input-sm-modal-billing dm-phone','autocomplete'=>'off']) !!}
                        {!! $errors->first('fax', '<p> :message</p>')  !!}
                    </div>                                    
                </div> 

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('email')) error @endif">
                        {!! Form::text('email',null,['class'=>'form-control input-sm-modal-billing js-email-letters-lower-format','autocomplete'=>'off']) !!}
                        {!! $errors->first('email', '<p> :message</p>')  !!}
                    </div>                                    
                </div>

                <div class="form-group">
                    {!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('website')) error @endif">
                        {!! Form::text('website',null,['class'=>'form-control input-sm-modal-billing','autocomplete'=>'off']) !!}                        
                        {!! $errors->first('website', '<p> :message</p>')  !!}
                    </div>                                    
                </div> 

            </div>
        </div><!-- Box body ends -->
    </div><!-- Box Ends -->  
</div>   <!-- Col 12 Ends -->

@stop

@section('practice')
@include ('practice/insurance/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop            