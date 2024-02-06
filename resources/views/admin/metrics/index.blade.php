@extends('admin')
@section('toolbar')
<div class="row toolbar-header" >
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.practice')}}" data-name="dashboard"></i>  Practice Analytics </small>
        </h1>
        <ol class="breadcrumb">
            {{-- <li><a href="#" data-url="" class="js-refresh-data"><i class="fa fa-refresh" data-placement="bottom"  data-toggle="tooltip" data-original-title="Refresh Data"></i></a></li> --}}
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>            
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="box box-view no-shadow">        
            <div class="box-body yes-border" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
                <!-- SEARCH FIELDS FILE -->
                {!! Form::open(['url'=>'reports/search/charges','onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform']) !!}
                @include('layouts.search_fields', ['search_fields'=>$search_fields])
                <div class="col-lg-11 col-md-11 col-sm-12 col-xs-8 no-padding margin-l-10">                                            
                    <input class="btn btn-medcubics-small js_filter_search_submit pull-left m-r-m-3" value="Search Report" type="submit">
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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_claim_list_part hide"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_exit_part text-center hide">
    <input class="btn btn-medcubics-small" id="js_exit_part" value="Exit" type="button">
</div>
@stop
@push('view.scripts') 

{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}

<script type="text/javascript">
    var api_site_url = '{{url("/")}}';   
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/admin/metrics/search_customers"; 
//--------------------------------- FORM SUBMIT ----------------------

	$(".js_filter_search_submit").on("click",function(){
		getAjaxResponse(listing_page_ajax_url, $("#js-bootstrap-searchvalidator").serialize());
	});

	$(document).ready(function(){
		getMoreFieldData();
		$('[id^=insurance_id]').hide();
	});

	//Search Fields validation starts

	$(document).ready(function() {
		$("[data-field=Month]").on('change', function(){
			var month = $(this).val();
			var year = $("[data-field=Year]").val();

		});
	});
	var form_validator = $('#js-bootstrap-searchvalidator').bootstrapValidator({
		message: 'This value is not valid',
		excluded: ':disabled, :hidden, :not(:visible)',
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: ''
		},
		fields: {
			Month: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
                            var month = value;
                            var year = $("[data-field=Year]").val();
                            if(value != '') {
                                if(year == '') {
                                    $("[data-field=Year]").on('change', function(){
                                        var yr = $(this).val();
                                        
                                        if(yr !== '') {
                                           var bootstrapValidator = $('#js-bootstrap-searchvalidator').data('bootstrapValidator');
                                            bootstrapValidator.enableFieldValidators('Month', false);
                                        }
                                        else {
                                            return false;
                                        }
                                    });
                                    return {
                                        valid: false,
                                        message: 'Please select year'
                                    };
                                } else {
                                    return true;
                                }
                            } else {
                                return true;
                            }
							// var validation_type = $field.attr('data-field-type');
							// if (value != '') {
							// 	if (validation_type == 'number') {

							// 		if (!new RegExp(/^[0-9]\d*(\.\d+)?$/).test(value)) {

							// 			return {
							// 				valid: false,
							// 				message: ''
							// 			};
							// 		}
							// 		else {
							// 			return true;
							// 		}
							// 	} else if (validation_type == 'text') {
							// 		if (!new RegExp(/^[a-zA-Z\s]*$/).test(value)) {
							// 			return {
							// 				valid: false,
							// 				message: 'Alphabet value Only'
							// 			};
							// 		} else {
							// 			return true;
							// 		}
							// 	} else if (validation_type == 'phone_number') {
							// 		var response = phoneValidation(value, home_phone_limit_lang_err_msg);
							// 		if (response != true) {
							// 			return {
							// 				valid: false,
							// 				message: response
							// 			};
							// 		}
							// 		else {
							// 			return true;
							// 		}
							// 	} else if (validation_type == 'both') {
							// 		/* if(!new RegExp(/^[a-zA-Z0-9 ]+$/).test(value)){
							// 			return {
							// 				valid: false, 
							// 				message: 'Special characters not allowed'
							// 			};
							// 		}else{ */
							// 		return true;
							// 		/* } */
							// 	} else {
							// 		return true;
							// 	}
							// } else {
							// 	return true;
							// }
						}
					}
                }
            },
            Year: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function(value, validator) {
                            var monthval = $("[data-field=Month]").val();
                            var yearval = value;
                            if(monthval !== '' && yearval == '') {
                                return {
                                    valid: false,
                                    message: 'Year is required'
                                };
                            } else {
                                return true;
                            }
                        }
					}
				}
			}
        }
    });
	//Search Fields validation ends
	$(".help-block").css("margin-top", "52px");

	/* Onchange code for more field Start */
	$(document).on('change','select.more_generate',function(){ 
		getMoreFieldData();
	});
	/* Onchange code for more field End */ 


	$("#insurance_charge.js_select_basis_change").on("click", function(){
		//$("#insurance_id").hide();
		if($(this).val()=='insurance'){
		   $('[id^=insurance_id]').show();   
		}
		else{
		   $('[id^=insurance_id]').hide();  
		}
		//console.log($(this).val());
	});
</script>
@endpush
<!-- Server script end -->