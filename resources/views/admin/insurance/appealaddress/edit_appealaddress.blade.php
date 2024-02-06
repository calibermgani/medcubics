@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php $insurance->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($insurance->id,'encode'); ?>
<?php $appealaddress->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($appealaddress->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.insurance')}}" data-name="bank"></i>Insurance <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Appeal Address <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Appeal Address</span></small>
        </h1>
        <ol class="breadcrumb">
            
            <li><a href="javascript:void(0)" data-url="{{ url('admin/insurance/'.$insurance->id.'/insuranceappealaddress/'.$appealaddress->id) }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop


@section('practice-info')
<div class="row-fluid">

    @include ('admin/insurance/insurance_tabs')
@stop

@section('practice')
 
	{!! Form::model($appealaddress, array('method' => 'PATCH','class'=>'insurance-info-form','id'=>'js-bootstrap-validator','name'=>'myForm','url' =>'admin/insurance/'.$insurance->id.'/insuranceappealaddress/'.$appealaddress->id,'class'=>'medcubicsform')) !!}
		   
	@include ('admin/insurance/appealaddress/form')    
	<!-- Modal Light Box starts -->  
	<div id="form-address-modal" class="modal fade in">
		@include('practice/layouts/usps_form_modal') 
	</div><!-- Modal Light Box Ends --> 
			
	{!! Form::close() !!}

@stop 