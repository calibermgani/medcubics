@extends('admin')
@section('toolbar')
<?php
	$id = Route::getCurrentRoute()->parameter('id');
	$patient_id =  (isset($patient_id))?$patient_id:App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($id,'decode'); 
?>
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.payments.payments')}} font14"></i> Payments <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Create Payment</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a accesskey="b" href="{{ url('patients/'.$id.'/payments')}}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
	<?php
		$patintid_new = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id,'encode'); 
		$claims =@$claims_list;
	?>
	@include ('patients/billing/tabs', ['tabpatientid' => $patintid_new, 'tab' => 'payment'])    
@stop

@section('practice')
	{!! Form::open(['url'=>'patients/'.$id.'/payments/insurancepost', 'id' => 'js-insurance-form', 'class' => 'js_insurance_create_form medcubicsform', 'name' => "insurance-form"]) !!}  
		@include ('payments/payments/common_insurance')
	{!! Form::close() !!}
	@include ('patients/problemlist/commonproblemlist') 
@stop 

@push('view.scripts')                           
<script type="text/javascript">	

	$(window).load(function() {
		displayLoadingImage();
	});
	
    $(document).ready(function () {		
		// Shwo submit button after page render completed.
		Pace.on('done', function() {
			setTimeout(function(){
				$(".selFnBtn").removeClass("hide");
				hideLoadingImage();
				//$('form#js-bootstrap-validator .js-charge_save').prop('disabled', false);
				//$('form#js-bootstrap-validator .js-charge_savesbt').prop('disabled', false);
			}, 1000);
		});
		
        $('input[name="check_date"]').on('change', function(){
            $('form#js-insurance-form').bootstrapValidator('revalidateField', 'check_date'); 
        }); 
        $('input[name="payment_amt"]').on('blur', function(){
            $('form#js-insurance-form').bootstrapValidator('revalidateField', 'payment_amt'); 
        });       
    });
</script>
@endpush 