@extends('admin')
@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.patient.superbill')}} "></i> E-Superbills <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Superbill</span></small>
        </h1>
        <ol class="breadcrumb">
			<li><a href={{App\Http\Helpers\Helpers::patientBackButton($patient_id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> 
					
			<?php $uniquepatientid = $patient_id; ?>	
			@include ('patients/layouts/patientstatement_icon')
			
			@include ('patients/layouts/swith_patien_icon')
            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/patients')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Ends -->
@stop
@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'yes'])
@stop 

@section('practice')
@include ('patients/superbill/form',['submitBtn'=>'Next']) 
@stop 