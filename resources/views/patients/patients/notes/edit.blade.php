@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
           <small class="toolbar-heading"><i class="fa fa-sticky-note font14"></i> Notes <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
           <li><a href="javascript:void(0)" data-url="{{  url('patients/'.$patients->id.'/notes') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
		   
			 <?php $uniquepatientid = $patients->id; ?>	

			@include ('patients/layouts/swith_patien_icon')	
		   
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
            <li><a href="#js-help-modal" data-url="{{url('help/patients')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
    @include ('patients/layouts/tabs',['tabpatientid'=>@$patients->id,'needdecode'=>'yes'])
@stop

@section('practice')
   
	{!! Form::model($notes, ['method'=>'PATCH','id'=>'js-bootstrap-validator', 'url'=>'patients/'.$type_detailsID.'/notes/'.$notesID,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
	@include ('patients/patients/notes/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
                
@stop            