@extends('admin')

@section('toolbar')

<div class="row toolbar-header"><!-- Toolbar Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.speciality')}} " data-name="users-add"></i> Eligibility <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> New Benifit Verification</span></small>
        </h1>
        <ol class="breadcrumb">
			 <li><a href="javascript:void(0)" data-url="{{url('patients/'.$patient_id.'/eligibility')}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			 <?php $uniquepatientid = $patient_id;
					$patientid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id,'decode'); ?>
			 			
			@include ('patients/layouts/swith_patien_icon')
                        <?php $id = $uniquepatientid;?>
            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/eligibility')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Ends --> 
@stop

@section('practice-info')
	@include ('patients/layouts/tabs',['tabpatientid'=>@$patientid,'needdecode'=>'no'])
    @include ('patients/eligibility/tabs')
@stop

@section('practice')
{!! Form::open(['url'=>'patients/'.$patient_id.'/eligibility','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
	@include ('patients/eligibility/forms',['submitBtn'=>'Save'])
{!! Form::close() !!}	
@stop