@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
		<h1>
			<small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Patient Statement <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Individual Statement</span></small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="{{ url('patientstatementsettings') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			
			<!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			<li><a href="#js-help-modal" data-url="{{url('help/patientstatement')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
		</ol>
	</section>
</div>
@stop

@section('practice-info')
	@include ('practice/patientstatementsettings/tabs')
@stop

@section('practice')
	
	<div class="col-lg-12 col-md-12 col-xs-12 margin-t-20"><!--  Col-12 Starts -->
		<div class="box no-shadow"><!--  Left side Content Starts -->
			<div class="box-header-view">
				<i class="fa {{Config::get('cssconfigs.Practicesmaster.patientstatement')}}"></i> <h3 class="box-title">Individual Statement</h3>
				 <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			@if(!isset($psettings))
			<div class="box-body form-horizontal p-b-25 margin-l-10">    	
				{{ trans("practice/practicemaster/patientstatementsettings.validation.unknownsettingsmsg") }}
			</div>	
			@else
				{!! Form::open(array('id'=>'js-bootstrap-validator')) !!}
				<div class="box-body form-horizontal p-b-25 margin-l-10">   	
					<div class="form-group">
					{!! Form::label('Patient Search', 'Patient Search', ['class'=>'col-lg-2 col-md-5 col-sm-5 col-xs-5 control-label star']) !!}
						<div class="col-lg-4 col-md-3 col-sm-4 col-xs-5">                                                                        
							{!! Form::text('patient_search', null, ['id'=>'patient_search', 'class'=>'form-control', 'maxlength'=>'100', 'autocomplete'=>'off', 'tabindex'=>'1']) !!}              
						</div>
						
						<div class="col-lg-3 col-md-3 col-sm-4 col-xs-5 no-padding" >  
						<div class="col-lg-4 col-md-3 col-sm-4 col-xs-5 no-padding">
							{!! Form::button('Go', ['class'=>'btn btn-medcubics js_individual_go col-lg-2 col-md-5 col-sm-5 col-xs-5 no-margin','tabindex'=>'2']) !!} 
							
						</div>
						&emsp; 
						{!! Form::button('Reset', ['class'=>'btn btn-medcubics js_reset col-lg-2 col-md-5 col-sm-5 col-xs-5 no-margin','tabindex'=>'3']) !!} 
						</div>
					</div> 
				</div>		
				{!! Form::close() !!}
			@endif
		</div><!--  Left side Content Ends -->
	</div><!--Background color for Inner Content Ends -->

	<div class="js_loading hide text-center">
		<i class="fa fa-spinner fa-spin font20"></i> Processing
	</div>		
	<div class="js_indpatientlist"></div>
@stop 

@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function() {
		$(window).keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				$( ".js_individual_go" ).trigger( "click" );
				return false;
			}
		});
		  
		$('#js-bootstrap-validator')
			.bootstrapValidator({
			message: 'This value is not valid',
			excluded: ':disabled',
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
			  patient_search:{
				message: '',
					validators: {
						callback: {
							message: '',
							callback: function (value, validator, element) {
								
								if(value == ''){
									return {
										valid: false,
										message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.patientname") }}'
									};	
								} 
								else if(/^[a-zA-Z0-9- ,.]*$/.test(value) == false){
									return {
										valid: false,
										message: '{{ trans("common.validation.alpha") }}'
									};
								}
								else if (value.length < 3){
									return {
										valid: false,
										message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.patientsearchlimit") }}'
									};
								}
								return true;
							}
						}
					}
				}
			}	
		});
	});
</script>
@endpush