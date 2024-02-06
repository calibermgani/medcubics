@extends('admin')
<?php 
	$id = Route::current()->parameters['id'];
	$patintid = !(empty($patintid))?$patintid:App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$id,'decode'); 
?>
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.payments.payments')}} font14"></i> Payments <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('patients/'.$id.'/payments') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
             <?php $uniquepatientid = $id; ?>   
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info') 
<?php $patintid_new = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patintid,'encode'); 
$claims =@$claims_lists;?>
 @include ('patients/billing/tabs', ['tabpatientid' => @$patintid_new, 'tab' => 'payment'])  
@stop
@section('practice')
{!! Form::open(['url'=>'patients/payments', 'id' => 'js-payment','class' => 'medcubicsform', 'name' => "patient-form"]) !!}
@include('payments/payments/patient/common_patient_payment')
{!! Form::close() !!}
<div id="post_payments" class="modal fade in">
    <div class="modal-md-800">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Posting</h4>
            </div>
            <div class="modal-body no-padding" >
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->  
</div><!-- /.modal-dialog --> 
<!--End-->
@stop 
@push('view.scripts')                           
<script type="text/javascript">
    $(document).ready(function () {		
		// Show submit button after page render completed.
		Pace.on('done', function() {
			setTimeout(function(){
				$(".selFnBtn").removeClass("hide");
				//$('form#js-bootstrap-validator .js-charge_save').prop('disabled', false);
				//$('form#js-bootstrap-validator .js-charge_savesbt').prop('disabled', false);
			}, 1000);
		});
		
		$('input[name="check_date"]').on('change', function(){
            $('form#js-payment').bootstrapValidator('revalidateField', 'check_date'); 
        });
		
        $('input[name="cardexpiry_date"]').on('change', function(){
            $('form#js-payment').bootstrapValidator('revalidateField', 'cardexpiry_date'); 
        });  
		
        $('select[name="card_type"]').on('change', function(){ 
            $('form#js-payment').bootstrapValidator('revalidateField', 'card_type');
        }); 
		
        $('.js_pateint_paid').on('change', function(){ 
            $('form#js-payment').bootstrapValidator('revalidateField', 'js_pateint_paid');
        });            
      });
</script>
@endpush