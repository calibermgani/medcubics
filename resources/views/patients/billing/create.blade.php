@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
		<h1>
			<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.charges.charges')}} font14"></i> Charges <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
		</h1>
		<?php $id = Route::current()->parameters['id']; ?>
		<ol class="breadcrumb">
			<?php $uniquepatientid = $id; ?>  
			@include ('patients/layouts/swith_patien_icon')
			<!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			<li><a accesskey="b" class= "js_next_process" href="{{ url('patients/'.$id.'/billing') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>	
			<li><a href="#js-help-modal" data-url="{{url('help/charges')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
		</ol>
	</section>
</div>
@stop

@section('practice-info')
	@include ('patients/billing/tabs', ['tabpatientid' => $id]) 
@stop

@section('practice')
	@include ('patients/billing/model-inc')
	{!! Form::open(['url'=>'patients/billing','id' => 'js-bootstrap-validator', 'class'=>'medcubicsform js_billingform', 'name' => "chargeform"]) !!}
		<input type="hidden" name="edit_activity" value="{{ Request::url('/') }}">
		<input type="hidden" name="appointment_id" value="{{ $appointment_id }}">
		@include ('patients/billing/billing_create')
	{!! Form::close() !!}
	@include ('patients/problemlist/commonproblemlist') 

@stop

@push('view.scripts1') 
<script type="text/javascript">
$(document).ready(function() {  
	// Enable save button after page render complete.
	// Rev. 1 - Ref.MEDV2-244 - Ravi - 09-10-2019
	Pace.on('done', function() {
		setTimeout(function(){
			$('form#js-bootstrap-validator .js-charge_save').prop('disabled', false); 
		}, 1000);
	});
	
 <?php
  if(isset($insured_details))
  if(!empty($insured_details[0]) && $insured_details[0]->termination_date!='0000-00-00'){ 
    ?> var termination_date="{{$insured_details[0]->termination_date}}"; <?php
    if($insured_details[0]->termination_date<App\Http\Helpers\Helpers::timezone(date('Y-m-d H:i:s'),'Y-m-d')){
    ?>
  js_sidebar_notification("error","Insurance Expired");              
 <?php } ?>
	$(document).on('change', ".from_dos", function(){
		dos_date = new Date($(this).val());
		from_date = dos_date.getFullYear()+'-'+("0" + (dos_date.getMonth() + 1)).slice(-2)+'-'+dos_date.getDate();
		var date1 = new Date(from_date);
		var date2 = new Date(termination_date);

		if(date1 > date2) {
			js_sidebar_notification("error","Insurance Expired compare to DOS"); 
			$('form#js-bootstrap-validator .js-charge_save').prop('disabled', true); 
		}else{
			$('form#js-bootstrap-validator .js-charge_save').prop('disabled', false); 
		}
	});
 <?php } ?>
  
	$( "#date_of_injury" ).on('change', function(){
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'doi');
	});
	
	$('#anesthesia_start').on('change', function(){
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'anesthesia_start');
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'anesthesia_stop');
	});
	
	$('#anesthesia_stop').on('change', function(){
       $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'anesthesia_start');
       $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'anesthesia_stop');
	});
	
	$("input[name='admit_date']").on('change', function(){ 
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'discharge_date');
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'admit_date');
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'doi');
		$('.to_dos').change(); 
		$('.from_dos').change(); 
		is_from_admit_date = "admit";
	});
	
	$("input[name='discharge_date']").on('change', function(){ 
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'discharge_date');
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'admit_date');

		$('.to_dos').change(); 
		$('.from_dos').change(); 
	});
	
	$("input[name='check_date']").on('change', function(){ 
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_date');
	}); 
	
	$('[name="doi"]').on('change',function(){
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'doi');
		$('.to_dos').change(); 
		$('.from_dos').change();  
	});   
	
	$(document).delegate("input[name='copay_amt']", 'change', function(){
		$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'copay'); 
		$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'copay_amt');
	}); 
	
	$(document).delegate(".icd_pointer", 'change', function(){           
		setTimeout(function(){
			$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'icd_pointer'); 
			$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false); 
		}, 500);           
	}); 
	
	$(document).delegate(".js_charge_amt", 'change', function(){      
		$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'js_charge_amt'); 
	}); 
	
	$(document).delegate(".box_24_atoj", 'change', function(){      
		$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'box_24_AToG[]'); 
	});
});
</script>
@endpush 