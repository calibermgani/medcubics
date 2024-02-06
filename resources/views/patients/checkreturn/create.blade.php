@extends('admin')

@section('toolbar')
	<?php  $uniquepatientid = $patient_id;  ?>
<div class="row toolbar-header">
    <section class="content-header">
	
        <h1>
           <small class="toolbar-heading"><i class="fa fa-money"></i> Wallet History <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Return Check <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
		   
        </h1>
        <ol class="breadcrumb">
			<li><a accesskey="b" href="javascript:void(0)" data-url="{{ url('patients/'.$patient_id.'/returncheck')}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			
			@include ('patients/layouts/swith_patien_icon')	
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href=""><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
           <li><a href="#js-help-modal" data-url="{{url('help/return_check')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
	<?php  $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id,'decode');  ?>
	@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'no'])
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
		<?php 
			$activetab = 'return check';
			$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id,'encode'); 
			$id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$returncheck->id,'encode'); 
		?>
		<div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
			@include ('patients/checkreturn/tab')
		</div>
	</div>
	
	{!! Form::open(['url'=>'patients/'.$patient_id.'/returncheck','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}   
		@include ('patients/checkreturn/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop