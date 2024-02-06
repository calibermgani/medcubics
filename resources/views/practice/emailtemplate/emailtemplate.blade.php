@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.apisettings')}} font14"></i>User Settings <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Email Template</span></small>
			</h1>
			<ol class="breadcrumb">
				  <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
				<li><a href="#js-help-modal" data-url="{{url('help/email_template')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
			</ol>
		</section>

	</div>
@stop

@section('practice-info')
	@include ('practice/apisettings/tabs')
@stop

@section('practice')
	<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.emailtemplate") }}' />
	{!! Form::model($emailtemplate, ['method'=>'POST','id'=>'js-bootstrap-validator', 'files' => true ,'url'=>'emailtemplate/1','class'=>'medcubicsform']) !!}
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  margin-t-20"><!-- Col Starts -->
		<div class="box box-info no-shadow"><!-- Box Starts Here -->
			<div class="box-header margin-b-10">				
				<i class="fa fa-envelope"></i> <h3 class="box-title"> Email Template </h3>
				<div class="box-tools pull-right">
					<button tabindex="-1" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body form-horizontal margin-l-10"><!-- Box Body Ends -->
				@foreach (@$emailtemplate as $emailtemplate)		
				<div class="form-group">
					<div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
						<span class='med-orange font600'>{{ucwords(str_replace('_', ' ',$emailtemplate->template_for))}}</span>
					</div>	
					<div class="col-lg-5 col-md-6 col-sm-5 col-xs-2"></div>
				</div>
				<div class="form-group @if($errors->first('subject.'.$emailtemplate->id)) error @endif">
					<div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
						{!! Form::label('Subject', 'Subject', ['class'=>' control-label']) !!} 
					</div>
					<div class="col-lg-6 col-md-3 col-sm-3 col-xs-10">
						{!! Form::text('subject['.@$emailtemplate->id.']', $emailtemplate->subject,['class'=>'mysubject form-control','maxlength'=>'100'])  !!}	
						{!! $errors->first('subject.'.$emailtemplate->id, '<p> :message</p>')  !!}
					</div> 
					<div class="col-lg-6 col-md-6 col-sm-5 col-xs-2">
					</div>
				</div> 
				<div class="form-group @if($errors->first('content.'.$emailtemplate->id)) error @endif">
					<div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
						{!! Form::label('content', 'Content', ['class'=>' control-label']) !!} 
					</div>
					<div class="col-lg-6 col-md-3 col-sm-3 col-xs-10">
						{!! Form::textarea('content['.@$emailtemplate->id.']',$emailtemplate->content, ['class' =>'mycontent form-control']) !!}
						{!! $errors->first('content.'.$emailtemplate->id, '<p> :message</p>')  !!}
					</div> 
					<div class="col-lg-3 col-md-6 col-sm-5 col-xs-2">
					</div>
				</div> 
				@endforeach
									
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20 text-center">
					{!! Form::submit('Save', ['class'=>' btn btn-medcubics']) !!}
					<a href="{{ url('emailtemplate') }}">
					{!! Form::button('Cancel', ['class'=>'  btn btn-medcubics']) !!}</a>
				</div>	
			</div> 
			
		{!! Form::close() !!}	
		</div> 
	</div> 
@stop

@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function () {
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
				mysubject: {
					selector: '.mysubject',
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/correspondence.validation.subject") }}'
						}
					}
				},
				mycontent: {
					selector: '.mycontent',
					validators: {
						notEmpty: {
							message: '{{ trans("common.validation.content") }}'
						}
					}
				}
			}
		});
	});
</script>
@endpush		