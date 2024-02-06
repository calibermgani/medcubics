/*/////////////////////////////////////////////////////////////////////////////
 Author:    Sridhar
 Date:      18 Mar 2019
 
Daterangepicker handling for Search relevant page
 ----------- INDEX -------------
 1. Common daterangepicker, datepicker for invoice search
 2. Validation for invoice fields
 3. Add, Remove rows for invoice perticulars
/*/////////////////////////////////////////////////////////////////////////////


$(document).ready(function () {
	/* Transaction Date Label */
	disableAutoFill('.search_fields_container');
	datePickerCall();
	$('.js_invoice_date .invoice_range').keydown(function(){
		return false;
	});
	$(document).on('click', '#add', function () {
		var pre = 0;
		var total_amount = $('.total_amount:last').val();
		var add = '<tr class="row"><td class="col-md-1">\
	    <input data-field-type="" readonly="readonly" data-bv-field ="start_date" class="start_date form-control js_invoice_date dm-date form-cursor " name="start_date[]" type="text"></td>\
	    <td class="col-md-1">\
	    <input data-field-type="" readonly="readonly" data-bv-field ="end_date" class="end_date form-control js_invoice_date dm-date form-cursor " name="end_date[]" type="text"></td>\
	    <td class="col-md-6">\
	    <input data-field-type="" data-bv-field ="product" class="product form-control form-cursor" name="product[]" type="text"></td>\
	    <td class="unit col-md-1">\
	    <input data-field-type="number" data-bv-field ="units" class="unit form-control text-right form-cursor units" name="units[]" type="text" value="0.00"></td>\
	    <td class="quantity col-md-1">\
	    <input data-field-type="number" data-bv-field ="quantity" class="quan quant form-control text-right form-cursor" name="quantity[]" value="0" type="text"></td>\
	    <td class="col-md-2">\
	    <input class="total_amount form-control text-right form-cursor" readonly="readonly" name="total[]" type="text"></td><tr>';
		if (total_amount > 0) {
			var in_validation = $('#invoice_val').data('bootstrapValidator');
			$("#invoiceBody:last").append(add);
			$("#invoiceBody tr:last").remove();
			$('input[name="units[]:last"]').val(0.00);
			$('input[name="quantity[]:last"]').val(0);
			invoice_vali();
			$("#invoiceBody tr:last td").each(function(){
				in_validation.addField($(this).children('input'));
			});
		}
		return false;
	});
	$(document).on('click', '#remove', function () {
		var length = $("tbody > tr.row").length;
		if (length > 1) {
			$("#invoiceBody tr:last").remove();
			unit();
		}
	});
});
function unit() {
	var pre = 0;
	var units = $("input[name='units[]']:last").val();
	var quantity = $("input[name='units[]']:last").closest("td").next().find('input').val();
	if (units != "" && quantity != "") {
		var result = parseFloat(units) * parseFloat(quantity);
		$("input[name='units[]']:last").closest("td").next().next().find('input').val(result.toFixed(2));
		var due_amount = 0;
		$("input.total_amount").each(function () {
			var total = $(this).val();
			due_amount = parseFloat(due_amount) + parseFloat(total);
		});

		$('input[name="due_amount"]').val(due_amount.toFixed(2));
		var previous_amount = $('input[name="previous_amount"]').val();
		var tax = $('input[name="tax"]').val();
		var total_amount = parseFloat(due_amount) + parseFloat(previous_amount);
		var net_total = parseFloat(total_amount) + (parseFloat(total_amount) * parseFloat(tax) / 100);
		$('input[name="total_amount"]').val(net_total.toFixed(2));
		$('input[name="total_due_amount"]').val(net_total.toFixed(2));
	} else { $("input[name='units[]']:last").closest("td").next().next().find('input').val(pre.toFixed(2)); }
}
$(document).on('focusin', '.invoice_range', function () {
	//datePickerCall();
});
function is_null(evt) {return false;}
function isNumber(evt) {
	evt = (evt) ? evt : window.event;
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if(charCode !=46)
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}
	return true;
}
function datePickerCall() {
	$('input[name="invoice_period"]').daterangepicker({
		startDate: moment().startOf('month'),
		endDate: moment(),
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		locale: {
			cancelLabel: 'Clear'
		},
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment()],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
			'This Year': [moment().startOf("year"), moment()],
			'Last Year': [moment().subtract(1, "y").startOf("year"), moment().subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="invoice_period"]').on('apply.daterangepicker', function (ev, picker) {
		$(this).keydown(function(){ return false; });
		$(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
		invoice_vali();
		$('#invoice_val').bootstrapValidator('revalidateField', 'invoice_period');
	});

	$('input[name="invoice_period"]').on('cancel.daterangepicker', function (ev, picker) {
		$(this).keydown(function(){ return false; });
		$(this).val('');
		invoice_vali();
		$('#invoice_val').bootstrapValidator('revalidateField', 'invoice_period');
	});
}
$(document).on('focusin', '.js_invoice_date', function () {
	$('input[name="invoice_date"]').datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		yearRange: '1901:+0',
		maxDate: 0,
		onClose: function (selectedDate) { this.focus(); }
	});
	var dateFormat = "mm/dd/yy";
	var from = $(".start_date").datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		yearRange: '1901:+0',
		maxDate: 0,
		onClose: function (selectedDate) { this.focus(); }
	}).on('change', function(){
		to.datepicker( "option", "minDate", getDate($(this).val()) );
	});
	var to = $(".end_date").datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		yearRange: '1901:+0',
		maxDate: 0,
		onClose: function (selectedDate) { this.focus(); }
	}).on( "change", function() {
        from.datepicker( "option", "maxDate", getDate($(this).val()) );
	});
	function getDate(element) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element);
      } catch( error ) {
        date = null;
      }
      return date;
    }
});
function invoice_vali(){
	    $(document).ready(function () {
                    $('#invoice_val').bootstrapValidator({
                        message: 'This value is not valid',
                        excluded: ':disabled',
                        feedbackIcons: {
                            valid: 'glyphicon glyphicon-ok',
                            invalid: 'glyphicon glyphicon-remove',
                            validating: 'glyphicon glyphicon-refresh'
                        },
                        fields: {
                            invoice_date: {
                                trigger: 'change keyup',
                                message: 'Please select invoice date',
                                validators: {
                                    notEmpty: {
                                        message: 'Please select invoice date'
                                    }
                                }
                            },
                            invoice_period: {
                                trigger: 'toggle',
                                validators: {
                                    notEmpty: {
                                        message: 'Please select invoice period'
                                    }
                                }
                            },
                            start_date: {
                                trigger: 'change keyup',
                                    selector: '.start_date',
                                validators: {
                                	callback: {
		                                message: 'Please select start date',
		                                callback: function (value, validator,$field) {
		                                    if(value == ''){
		                                        return false;
		                                    }                                   
		                                    return true;
		                                }
		                            }
                                }
                            },
                            end_date: {
                                trigger: 'change keyup',
                                    selector: '.end_date',
                                validators: {
                                	callback: {
		                                message: 'Please select end date',
		                                callback: function (value, validator,$field) {
		                                	if(value == '')
		                                        return false;
		                                    return true;
		                                }
		                            }
                                }
                            },
                            product: {
                            	trigger: 'change keyup',
                                    selector: '.product',
                                validators: {
                                	callback: {
		                                message: 'Product field cannot be empty',
		                                callback: function (value, validator,$field) {
		                                    if(value == '')
		                                        return false;
		                                    return true;
		                                }
		                            }
                                }
                            },
                            units: {
                                message:'',
                                trigger: 'change keyup',
                                	selector: '.units',
                                validators: {
                                    callback:{
                                        message: 'Units required',
                                        callback:function(value){
                                            if(value <= 0)
                                                return false;
                                            return true;
                                        }
                                    }
                                }
                            },
                            quantity: {
                                message:'',
                                trigger: 'change keyup',
                                selector: '.quant',
                                validators: {
                                    callback:{
                                        message: 'Quantity required',
                                        callback:function(value){
                                            if(value <= 0)
                                                return false;
                                            return true;
                                        }
                                    }
                                }
                            // },
                            // previous_amount: {
                            //     trigger: 'change keyup',
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Quantity required'
                            //         }
                            //     }
                            // },
                            // tax: {
                            //     trigger: 'change keyup',
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Quantity required'
                            //         }
                            //     }
                            }
                        }
                    }).on('success.form.bv', function(e) {
                        $('#errors').html('');
                        $('#errors1').html('');
                        $(document).on('click','#js_exit_part_report',function(){
                            var url = api_site_url+'/admin/reports';
                            setTimeout(function(){ window.location.href = api_site_url; }, 3000);
                        });
                    }).on('error.field.bv', function(e, data) {
                        var messages = data.bv.getMessages(data.element);
                        $('#errors').find('span[data-field="' + data.field + '"]').remove();
                        $('#errors').find('br[data-field="' + data.field + '"]').remove();
                        $('#errors1').find('span[data-field="' + data.field + '"]').remove();
                        $('#errors1').find('br[data-field="' + data.field + '"]').remove();
                        for (var i in messages) {
                            if(data.field == "invoice_date" ||data.field == "invoice_period"){
                                $('<span/><br/>')
                                .attr('data-field', data.field)
                                .html(messages[i])
                                .attr('class',"med-orange")
                                .attr('style', 'list-style:none;')
                                .appendTo('#errors');
                            }
                            else{
                                $('<span/><br>')
                                .attr('data-field', data.field)
                                .html(messages[i])
                                .attr('class',"med-orange")
                                .attr('style', 'list-style:none;')
                                .appendTo('#errors1');
                            }
                        }
                        data.element
                        .data('bv.messages')
                        .find('.help-block[data-bv-for="' + data.field + '"]')
                        .hide();
                    }).on('success.field.bv', function(e, data) {
                        $('#errors').find('span[data-field="' + data.field + '"]').remove();
                        $('#errors').find('br[data-field="' + data.field + '"]').remove();
                        $('#errors1').find('span[data-field="' + data.field + '"]').remove();
                        $('#errors1').find('br[data-field="' + data.field + '"]').remove();
                    });
            });
}