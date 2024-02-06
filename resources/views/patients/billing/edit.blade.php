@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.charges.charges')}} font14"></i> Charges <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
            <?php 
				$id = Route::getCurrentRoute()->parameter('id');
            	$url = !(empty($id))?url('patients/'.$id.'/billing'):url("charges");
           		$uniquepatientid = !empty($patient_id)?$patient_id:$id; 
			?>    
            @include ('patients/layouts/swith_patien_icon')
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
             <li><a accesskey="b" class= "js_next_process" href="{{ $url}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> 
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
	{!! Form::open(['url'=>'patients/billing/update','id' => 'js-bootstrap-validator','name'=>'patients-form', 'files' => true,'class'=>'medcubicsform','name' => "chargeform"]) !!}
	@include ('patients/billing/billing_edit')
	{!! Form::close() !!} 
	@include ('patients/problemlist/commonproblemlist')
@stop
<!--End-->
@push('view.scripts') 
<script type="text/javascript">
   $(document).ready(function() {  
  		// Enable save button after page render complete.
  		// Rev. 1 - Ref.MEDV2-244 - Ravi - 09-10-2019
  		Pace.on('done', function() {
  			setTimeout(function(){
  				$('form#js-bootstrap-validator .js-charge_save').prop('disabled', false);
  				$('form#js-bootstrap-validator .js-charge_savesbt').prop('disabled', false);
  			}, 1000);
  		});
		
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
        });
		
        $("input[name='discharge_date']").on('change', function(){ 
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'discharge_date');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'admit_date');
        });
        
        $('[name="doi"]').on('change',function(){
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'doi');            
        });  
		
        $(document).delegate(".js_charge_amt", 'change', function(){
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'js_charge_amt'); 
        }); 
		
        $(document).delegate(".icd_pointer", 'change', function(){           
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'icd_pointer'); 
        });
        
		$(document).delegate(".box_24_atoj", 'change', function(){      
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'box_24_AToG[]'); 
        });  
    });
</script> 
@endpush