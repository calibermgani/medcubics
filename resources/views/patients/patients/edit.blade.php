@extends('admin')
@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
		<h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Registration <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
			<?php $uniquepatientid = $id; ?>
			<li><a href="{{App\Http\Helpers\Helpers::patientBackButton($id)}}" accesskey="b" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a style="cursor:pointer;" accesskey="a" onClick="window.open('{{url('/patients/create')}}', '_blank')"> <i class="fa {{Config::get('cssconfigs.common.plus_circle')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="New Patient"></i></a></li>	
			@include ('patients/layouts/swith_patien_icon')
            <?php /*?>
	            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <?php */?>
            <li><a href="#js-help-modal" data-url="{{url('help/patients')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop
@section('practice-info')
    @include ('patients/layouts/tabs',['tabpatientid'=>@$id,'needdecode'=>'yes'])  
@stop
@section('practice')
    @include ('patients/patients/forms',['submitBtn'=>'Save']) 
@stop