@extends('admin')

@section('toolbar')

<div class="row toolbar-header"><!-- Toolbar Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.speciality')}}" data-name="checked-on"></i>  Eligibility <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Edit Benifit verification</span></small>
        </h1>
        <ol class="breadcrumb">
			<?php $uniquepatientid = $patient_id; ?>	
			<li><a href="javascript:void(0)" data-url="{{url('patients/'.$patient_id.'/eligibility/'.$eligibility->id)}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
		
			@include ('patients/layouts/swith_patien_icon')
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/eligibility')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Ends -->
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$eligibility->patients_id,'needdecode'=>'no'])
@stop

@section('practice')	
{!! Form::model($eligibility, ['method'=>'PATCH','id'=>'js-bootstrap-validator', 'url'=>'patients/'.$patient_id.'/eligibility/'.$eligibility->id,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
	@include ('patients/eligibility/forms',['submitBtn'=>'Save'])
{!! Form::close() !!}	
@stop