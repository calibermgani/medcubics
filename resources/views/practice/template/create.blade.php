@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>New</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('templates') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/templates')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice')
{!! Form::open(['url'=>'templates','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
<div class="col-md-12 space-m-t-7">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-8 col-md-8 col-sm-9 col-xs-12">
                <p class="push">
                <div class="form-horizontal">
				<div class="js-add-new-select" id="js-insurance-type">
                    <div class="form-group">
                        {!! Form::label('name', 'Name', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!} 
                        <div class="col-lg-4 col-md-6 col-sm-6 @if($errors->first('name')) error @endif">
                            {!! Form::text('name',null,['class'=>'form-control js-letters-caps-format','maxlength'=>'100','name'=>'name','autocomplete'=>'off']) !!}
                            {!! $errors->first('name', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
					 <div class="js-add-new-select" id="js-insurance-type">
                    <div class="form-group js_common_ins">
                        {!! Form::label('templatetypes', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!}
                        <div class="col-lg-4 col-md-6 col-sm-6 @if($errors->first('template_type_id')) error @endif"> 
												
                            {!! Form::select('template_type_id', array('' => '-- Select --') + (array)$templatestype,  $template_type_id,['class'=>'form-control select2 js-add-new-select-opt','autocomplete'=>"off"]) !!}
                            {!! $errors->first('template_type_id', '<p> :message</p>')  !!}
                        </div>                        
                    </div> 
                    </div> 
					<div class="form-group hide" id="add_new_span">
                    {!! Form::label('templatetypes', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!}
                    <div class="col-lg-4 col-md-6 col-sm-6 ">
                        {!! Form::text('newadded',null,['maxlength'=>'50','id'=>'newadded','class'=>'form-control','placeholder'=>'Add new Template Type','data-table-name'=>'templatetypes','data-field-name'=>'templatetypes','data-field-id'=>@$template_type_id,'data-label-name'=>'template type']) !!}
                        <p class="js-error help-block hide"></p>
                        <p class="pull-right no-bottom">
                        <i class="fa fa-save med-green" id="add_new_save" data-placement="bottom"  data-toggle="tooltip" data-original-title="Save"></i>
                        <i class="fa fa-ban med-green margin-l-5" id="add_new_cancel" data-placement="bottom"  data-toggle="tooltip" data-original-title="Cancel"></i>                         
                        </p>
                    </div>
                </div>
                </div>

                    <div class="form-group">
                        {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-4 col-md-6 col-sm-6 @if($errors->first('status')) error @endif">
                            {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                            {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
                            {!! $errors->first('status', '<p> :message</p>')  !!}
                        </div>                       
                    </div>	
                </div>
                </p>
            </div>
        </div><!-- /.box-body -->
    </div>
</div>

@include ('practice/template/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop

@push('view.scripts1')
<script type="text/javascript">

	if ($("div").hasClass("js-add-new-select")) {
		$("div.js-add-new-select").find('select:not("#newadded_cms_type")').append('<optgroup label="Others"><option value="0">Add New</option></optgroup>');
	}

    $(document).on('change', '.js-add-new-select-opt', function (event) {
		//$('.js-add-new-select-opt').change(function(){
		var current_divid = $(this).parents('div.js-add-new-select').attr('id');
		var selected_value = $(this).val();
		$('#' + current_divid).find('p.js-error').html('').removeClass('show').addClass('hide');
		if (selected_value == '0') {
			$(this).closest('.js_common_ins').addClass('hide');
			$('#' + current_divid).children("#add_new_span").removeClass('hide').addClass('show');
			$('#' + current_divid).find('#newadded').val('');
			$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
		} else {
			$("#add_new_span").removeClass('show').addClass('hide');
			$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
		}
	});

	$(document).on('keyup', '#newadded', function () {
		$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
		if ($(this).val() != null) {
			var seldivid = $(this).parents('div.js-add-new-select').attr('id');
			$('#' + seldivid).find('p.js-error').removeClass('show').addClass('hide');
		}
	});

	$(document).on("click", 'div.js-add-new-select #add_new_save', function () {
		$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
		//$('.js-add-new-select-opt').parents('div.js-add-new-select').find('#add_new_save').click(function(){
		var lblname = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-label-name');
		var insurance_type = $(this).parents('div.js-add-new-select').find("#newadded").val();
		var regex = new RegExp("^[a-zA-Z ]+$");
		if (!insurance_type || !regex.test(insurance_type)) {
			$(this).parents('div.js-add-new-select').find("#newadded").parent('div').addClass('has-error');
			$(this).parents('div.js-add-new-select').find('p.js-error').html('');
			if (!insurance_type) {
				$(this).parents('div.js-add-new-select').find('p.js-error').html(insurancetype + ' ' + lblname);
			} else {
				$(this).parents('div.js-add-new-select').find('p.js-error').html(only_alpha_lang_err_msg);
			}
			$(this).parents('div.js-add-new-select').find('p.js-error').removeClass('hide').addClass('show');
		} else {
			$(this).parents('div.js-add-new-select').find("#newadded").parent('div').removeClass('has-error');
			var tablename = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-table-name');
			var fieldname = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-field-name');
			var addedvalue = $(this).parents('div.js-add-new-select').find('#newadded').val();
			var seldivid = $(this).parents('div.js-add-new-select').attr('id');
			var pars = 'tablename=' + tablename + '&fieldname=' + fieldname + '&addedvalue=' + addedvalue;

			if (seldivid == 'js-insurance-type' && $('#newadded_cms_type').length) {
				var insCmsType = $(this).parents('div.js-add-new-select').find('#newadded_cms_type').val();
				pars = pars + '&cms_type=' + insCmsType;
			}

			var value = addedvalue.trim();
			var changed_string = value.toLowerCase();
			if (changed_string != 'App' && changed_string != "app") {
				url_path = (window.location.pathname).split("/");
				if (url_path[2] == 'templates') {
					$.ajax({
						url: api_site_url + '/addnewselect',
						type: 'get',
						data: pars,
						success: function (data) {
							if (data == '2') {
								$('#' + seldivid).find("#newadded").parent('div').addClass('has-error');
								$('#' + seldivid).find('p.js-error').html(exist_insurancetype + ' ' + lblname);
								$('#' + seldivid).find('p.js-error').removeClass('hide').addClass('show');
								$('input[name="hold_reason_exist"]').val(1); // For hold readon add new it was added                            
							} else {
								$('#' + seldivid).find("#add_new_span").removeClass('show').addClass('hide');
								$('#' + seldivid).find(".js_common_ins").removeClass('hide').addClass('show');
								$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
								getoptionvalues(tablename, fieldname, seldivid, addedvalue);
							}
						},
						error: function (jqXhr, textStatus, errorThrown) {
							console.log(errorThrown);
						}
					});
				}//Template if
			}//App if

			$.ajax({
				url: api_site_url + '/addnewselect',
				type: 'get',
				data: pars,
				success: function (data) {
					if (data == '2') {
						$('#' + seldivid).find("#newadded").parent('div').addClass('has-error');
						$('#' + seldivid).find('p.js-error').html(exist_insurancetype + ' ' + lblname);
						$('#' + seldivid).find('p.js-error').removeClass('hide').addClass('show');
						$('input[name="hold_reason_exist"]').val(1); // For hold readon add new it was added

					} else {
						//$("#add_new_span").removeClass('show').addClass('hide');                  
						//$('.js_common_ins').removeClass('hide').addClass('show');

						$('#' + seldivid).find("#add_new_span").removeClass('show').addClass('hide');
						$('#' + seldivid).find(".js_common_ins").removeClass('hide').addClass('show');
						$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
						getoptionvalues(tablename, fieldname, seldivid, addedvalue);
					}
				},
				error: function (jqXhr, textStatus, errorThrown) {
					console.log(errorThrown);
				}
			});
		}

		if ($(this).parents('div.js-add-new-select').hasClass('hold-option')) {
			$('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'hold_reason_id', true);
			$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'hold_reason_id');
			var hold_reason_val = $('input[name="hold_reason_exist"]').val();
			setTimeout(function () {
				if ($('input[name="other_reason"]').val() != '' && !hold_reason_val) {
					$('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'other_reason', false);
					$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'other_reason');
				} else {
					$('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'other_reason', true);
					$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'other_reason');
				}
			}, 500);
		}
	});

	$(document).on("click", "div.js-add-new-select #add_new_cancel", function () {
		//$('.js-add-new-select-opt').parents('div.js-add-new-select').find('#add_new_cancel').click(function(){        
		$(this).parents('div.js-add-new-select').find("#newadded").parent('div').removeClass('has-error');
		$(this).parents('div.js-add-new-select').find("#add_new_span").removeClass('show').addClass('hide');
		var seldivid = $(this).parents('div.js-add-new-select').attr('id');
		$(this).parents('#' + seldivid).find('.js-add-new-select-opt').closest('.js_common_ins').removeClass('hide').addClass('show');
		$(this).parents('#' + seldivid).find('.js-add-new-select-opt').select2("val", "");
		if ($(this).parents('div.js-add-new-select').hasClass('hold-option')) {
			$('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'hold_reason_id', true);
			$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'hold_reason_id');
		}
		$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
	});

	function getoptionvalues(tablename, fieldname, seldivid, addedvalue) {
		$.ajax({
			type: "GET",
			url: api_site_url + '/getoptionvalues',
			data: 'tablename=' + tablename + '&fieldname=' + fieldname + '&addedvalue=' + addedvalue,
			success: function (data) {
				$('#' + seldivid).find("select.js-add-new-select-opt").html(data);
				if ($('#' + seldivid).find("select.js-add-new-select-opt").attr('id') == 'js-hold-reason') {
					$('#js-hold-reason').change();
				} else {
					$('#' + seldivid).find("select.js-add-new-select-opt").select2();
				}
			}
		});
	}

</script>
@endpush