@extends('admin')
@section('toolbar')
<div class="row toolbar-header">

   <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}} font14"></i> Clinical Notes <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Edit Clinical Note</span></small>
        </h1>
        <ol class="breadcrumb">
			<li> <a href="javascript:void(0)" data-url="{{ url('patients/'.$patient_id.'/clinicalnotes')}}" class="js_next_process"> <i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			
			 <?php $uniquepatientid = $patient_id; ?>	
			
			@include ('patients/layouts/swith_patien_icon')
			
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
            <li><a href="#js-help-modal" data-url="{{url('help/clinical_notes')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'yes'])
@stop
@section('practice')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
	{!! Form::model($clinical_detail, ['method'=>'PATCH','id'=>'js-bootstrap-validator', 'files' => true ,'url'=>'patients/'.$patient_id.'/clinicalnotes/'.$clinical_detail->id,'class'=>'medcubicsform','data-id'=>$patient_id,'data-page'=>"edit"]) !!} 
		@include ('patients/clinicalnotes/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
    </div><!--/.col (left) -->
   
@stop 