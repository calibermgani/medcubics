@extends('admin')


@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-file-text-o font14"></i> Budget Plan </small>
        </h1>
        <ol class="breadcrumb">
			<?php $uniquepatientid = $patient_id; ?>	
			<li><a href={{App\Http\Helpers\Helpers::patientBackButton($patient_id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> 

			@include ('patients/layouts/swith_patien_icon')	
		   
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
            <li><a href="#js-help-modal" data-url="{{url('help/budgetplan')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop


@section('practice-info')
<?php 
$activetab = 'budget plan';
 ?>
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'yes'])

@stop

@section('practice')

{!! Form::open(['url'=>'patients/'.$patient_id.'/budgetplan','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
@include ('patients/patients/budgetplan/form',['submitBtn'=>'Save'])
{!! Form::close() !!}

@stop            