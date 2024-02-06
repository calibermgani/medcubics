@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-clock-o med-green med-breadcrum" data-name="list"></i> Support <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> FAQ </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{url('analytics/practice')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12">
    @if(Session::get('message')!== null)
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-18"><!-- Inner Content for full width Starts -->  
	{!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-validator','name'=>'medcubicsform']) !!}
	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0">
		<div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 line-height-26 med-orange font600"> Search :</div>
		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">  
			<?php $faq_category = Config::get('siteconfigs.faq_category'); ?>
			{!! Form::select('category', array('' => '-- Select --') + (array)$faq_category,  null,['class'=>'select2 form-control', 'id' =>'category']) !!}                                     
		</div> 
		<div class="col-lg-8 col-md-8 col-sm-6 col-xs-2"> 
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				{!! Form::text('search',null,['id'=>'search_key','class'=>'form-control']) !!}
			</div>
			<!--button id ="search">Search</button-->
			<span  class="btn btn-medcubics-small margin-t-0" style="line-height: 20px;" id="search" type="submit">
			 
				<i class="glyphicon glyphicon-search"></i> {!! Form::submit('Search', ['class'=>'btn btn-medcubics-small no-padding form-group','style'=>'margin:0px']) !!}
			</span>&nbsp;
			<span  class="btn btn-medcubics-small margin-t-0 js-reset-list" style="line-height: 20px;" onClick="window.location.reload()">
				<i class="fa fa-undo" area-hidden="true"></i> Reset
			</span>
			<div id="js_create_faq_loading" class="modal-footer m-b-m-15 text-centre hide">
				<i class="fa fa-spinner fa-spin med-green text-centre"></i> Processing
			</div>
		</div>
	</div>    
	{!! Form::close() !!}	
	<div class="table-responsive" id="faq_listing_key"> 
		@include ('support/faq/faqlist')
	</div>	             
</div><!-- Inner Content for full width Ends -->
<!--End-->

@stop
@push('view.scripts')
<script type="text/javascript">
    $('#search').click(function () {
		$("#js-bootstrap-validator").data('bootstrapValidator').validate();
    }); 
	$('.js-reset-list').click(function (e) {
		$('#search_key').val('');
		$('#category').select2("val", "");
		$('#search').trigger("click");
		var validator = $( "#js-bootstrap-validator" ).validate();
			validator.resetForm();
    });
	validateFunction();
	function validateFunction(){
		 $('[name="category"]').on('change',function(){
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'search');
		});
		$('[name="search"]').on('keyup',function(){
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'category');
		});
		var validation_start =$('#js-bootstrap-validator')
		.bootstrapValidator({
			message: 'This value is not valid',
			excluded: ':disabled',
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				search: {
					message: '',
					validators: {
						callback: {
							message: '{{ trans("admin/faq.validation.search") }}',
							callback: function (value, validator) {
								var value = value.trim();
								var category = $('#category').val();
								return (value == '' && category == '' )? false: true;  
								
							}
						}
					}
				},
				category: {
				message: '',
				validators: {
					callback: {
						message: '{{ trans("admin/faq.validation.categorys") }}',
						callback: function (value, validator) {
							var value = value.trim();
							var category = $('#search_key').val();
							return (value == '' && category == '' )? false: true; 
						}
					}
				}
			}
		}
	}).on('success.form.bv', function(e) {
		// Prevent form submission
		e.preventDefault();    
		var param = "search_key="+$('#search_key').val()+"&search_category="+$('#category').val();
		$("#js_create_faq_loading").removeClass('hide');
		$.ajax({
			url: api_site_url+'/support',    
			type: 'get',
			data: param,
			success: function( result ){
				
				$('#faq_listing_key').html(result);
				$.AdminLTE.boxWidget.activate();
				$("#js_create_faq_loading").addClass('hide');
			},
			error: function( jqXhr, textStatus, errorThrown ){
					console.log( errorThrown );
			}
		});
	});  
}
</script>
@endpush