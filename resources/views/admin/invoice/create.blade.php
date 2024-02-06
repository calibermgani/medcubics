@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports 
            	{{-- <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Billing Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Unbilled Claims Analysis</span> --}}
            </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('admin/invoice') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
        </ol>
    </section>
</div>
@endsection
@section('practice')	
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow ">
            <div class="box-body yes-border border-green border-radius-4">
            	{!! Form::open(['method'=>'post','onsubmit'=>"event.preventDefault();",'id'=>'invoice', 'name'=>'medcubicsform', 'url'=>'admin/invoice/generateInvoice', 'data-url' => 'admin/invoice/create']) !!}
                <div class="box box-info no-shadow">
                    <div class="box-body form-horizontal">
                        <div class="@if($errors->first('type')) error @endif " style="float:left; width: 200px;">
                            {!! Form::label('practice', 'Select Practice', ['class'=>'control-label font600']) !!}
                            {!! Form::select('practice_id', array('' => '-- Select --')+(array)@$practicelist,null,['class'=>'select2 form-control js_select_practice_id', 'id' => 'practice_id']) !!}
                            <span style="display: none;" class="error med-orange">Please select practice</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-10 col-xs-12 no-padding margin-l-10">
                    <input class="btn generate-btn js_filter_search_submit pull-left" tabindex="10" value="Generate Report" type="submit">
            	</div>
            	{!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<div class="js_spin_image hide">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center">
        <i class="fa fa-spinner fa-spin med-green font20"></i> Processing
    </div>
</div>
{!! Form::open(['method'=>'post','id'=>'invoice_val', 'name'=>'medcubicsform', 'url'=>'admin/invoice/report']) !!}
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_invoice hide"></div>
{!! Form::close() !!}
@endsection

@push('view.scripts')

{!! HTML::style('css/search_fields.css') !!}
<script>
var url = $('#invoice').attr("action");
function getAmount(){ 
	var i =1;
	var due_amount = 0;
	var pre = 0;
	$('input[name="previous_amount"]').val(pre.toFixed(2));
	$('input[name="tax"]').val(pre.toFixed(2));
	$('input[name="units[]"]').val(pre.toFixed(2));
	$('input[name="quantity[]"]').val(pre);
	$("tbody > tr.row").each(function(){
		var units = $('input.unit',this).val();
		var quantity = $('input.quan',this).val();
		var total = parseFloat(units) * parseFloat(quantity);
		$('input.total_amount',this).val(total.toFixed(2));
		due_amount = due_amount + total;
	});

	$('input[name="due_amount"]').val(due_amount.toFixed(2));
	var previous_amount = $('input[name="previous_amount"]').val();
	var tax = $('input[name="tax"]').val();
	var total_amount =  parseFloat(due_amount) + parseFloat(previous_amount);
	var net_total = parseFloat(total_amount)+(parseFloat(total_amount)* parseFloat(tax) / 100);
	$('input[name="total_amount"]').val(net_total.toFixed(2));
	$('input[name="total_due_amount"]').val(net_total.toFixed(2));

    $("tbody").on("focusout", "input.unit", function() {
	    var units = ($(this).val() !="")? parseFloat($(this).val()):0;
	    var quantity = parseFloat($(this).closest("td").next().find('input').val());
	    if(units!=""&&quantity!="")
	    {
		    var result = parseFloat(units) * parseFloat(quantity);
		    $(this).closest("td").next().next().find('input').val(result.toFixed(2));
	    	var due_amount = 0;
		    $("input.total_amount").each(function(){
		    	var total = $(this).val();
		    	due_amount = parseFloat(due_amount) + parseFloat(total);
		    });
		    $('input[name="due_amount"]').val(due_amount.toFixed(2));
			var previous_amount = $('input[name="previous_amount"]').val();
			var tax = $('input[name="tax"]').val();
	  		var total_amount =  parseFloat(due_amount) + parseFloat(previous_amount);
	  		var net_total = parseFloat(total_amount)+(parseFloat(total_amount) * parseFloat(tax) / 100);
	  		$(this).val(units.toFixed(2));
	  		$('input[name="total_amount"]').val(net_total.toFixed(2));
		    $('input[name="total_due_amount"]').val(net_total.toFixed(2));
	    }else{
	    	$(this).val(units.toFixed(2));
	    	$(this).closest("td").next().find('input').val(quantity);
	    }
  	});

  	$("tbody").on("focusout", "input.quan", function() {
	    var quantity = ($(this).val() !="")? parseFloat($(this).val()):0;
	    var units = parseFloat($(this).closest("td").prev().find('input').val());
	    if(units!=""&&quantity!="")
	    {
		    var result = parseFloat(units) * parseFloat(quantity);
		    $(this).closest("td").next().find('input').val(result.toFixed(2));
		    var due_amount = 0;
		    $("input.total_amount").each(function(){
		    	var total = $(this).val();
		    	due_amount = parseFloat(due_amount) + parseFloat(total);
		    });

		    $('input[name="due_amount"]').val(due_amount.toFixed(2));
			var previous_amount = $('input[name="previous_amount"]').val();
			var tax = $('input[name="tax"]').val();
	  		var total_amount =  parseFloat(due_amount) + parseFloat(previous_amount);
	  		var net_total = parseFloat(total_amount)+(parseFloat(total_amount) * parseFloat(tax) / 100);
	  		$(this).val(quantity);
	  		$('input[name="total_amount"]').val(net_total.toFixed(2));
		    $('input[name="total_due_amount"]').val(net_total.toFixed(2));
		}else{
			$(this).val(quantity);
			$(this).closest("td").prev().find('input').val(units.toFixed(2));
		}
  	});

  	$(document).on("focusout", 'input[name="previous_amount"]', function() {
  		var amount = ($(this).val() !="")? parseFloat($(this).val()):0;
  		var due_amount = $('input[name="due_amount"]').val();
  		var tax = $('input[name="tax"]').val();
  		var total_amount =  parseFloat(due_amount) + parseFloat(amount);
  		var net_total = parseFloat(total_amount)+(parseFloat(total_amount) * parseFloat(tax) / 100);
  		$(this).val(amount.toFixed(2));
  		$('input[name="total_amount"]').val(net_total.toFixed(2));
	    $('input[name="total_due_amount"]').val(net_total.toFixed(2));
  	});

  	$(document).on("focusout", 'input[name="tax"]', function() {
  		var tax = ($(this).val() !="")? parseFloat($(this).val()):0;
  		var due_amount = $('input[name="due_amount"]').val();
  		var amount = $('input[name="previous_amount"]').val();
  		var total_amount =  parseFloat(due_amount) + parseFloat(amount);
  		var net_total = parseFloat(total_amount)+(parseFloat(total_amount) * parseFloat(tax) / 100);
  		$(this).val(tax.toFixed(2));
  		$('input[name="total_amount"]').val(net_total.toFixed(2));
	    $('input[name="total_due_amount"]').val(net_total.toFixed(2));
  	});
}
/* function for get data for fields Start */
function getData(){
	var practice_id = $('.js_select_practice_id option:selected').val();
	var _token = $('input[name=_token]').val();
	$.ajax({
		type: 'POST',
		url: url,
		data: {practice_id, _token},
		success: function (response) {
			datePickerCall();
			$(".js_invoice").html(response).removeClass("hide");
			$(".js_exit_part").removeClass("hide");
			getAmount();
			invoice_vali();
			$(".js_filter_search_submit").prop("disabled",false);
		}
	});
}
/* function for get data for fields End */

/* Onchange code for field Start */
$(document).on('click','.js_filter_search_submit',function(){
	var practiceId = $("#practice_id").val();
	if(practiceId != ''){
		$("#practice_id").next().hide();
		$(".js_invoice").show();
		getData();
	} else {
		$("#practice_id").next().show();
		$(".js_invoice").hide();
		//js_sidebar_notification("error", 'Please select practice');
		$("#practice_id").focus()
	}
});
</script>
{!! HTML::script('js/invoice.js') !!}
@endpush
