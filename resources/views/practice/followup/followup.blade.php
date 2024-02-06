@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Follow-up Template</span></small>
        </h1>
        <ol class="breadcrumb">			        
	<!-- <li><a href="javascript:void(0);l" data-url="{{url('help/codes')}}" class="js-help" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li> -->		            
        </ol>
    </section>
</div>
@stop
@section('practice-info')
	@include ('practice/followup/tabs')
@stop
@section('practice')
<div class="col-lg-12">
	@if(Session::get('message')!== null) 
	<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
	@endif
</div> 
<?php $type = 'followup/category'; ?>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!-- Col Starts -->
	<div class="box box-info no-shadow"><!-- Box Starts Here -->
		<div class="box-header margin-b-10">
			<i class="fa fa-bars"></i> <h3 class="box-title">Claim Status List</h3>
			<div class="box-tools pull-right margin-t-2">
				@if($checkpermission->check_url_permission('followup/create-category') == 1)
			
				<a href="#" id="add_follow_CQ" class="font600 font14" data-toggle="modal" data-target="#myModal"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Claim Status</a>
				@endif
			</div>
		</div><!-- /.box-header -->
		<div class="box-body">
			<div class="table-responsive" id="js_table_search_listing">
				@include('practice/followup/followup-list')
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box ends -->
</div><!-- Col Starts -->
<!--End-->
<?php 
	$filed_type = array('date'=>'Date','number'=>'Number','text'=>'Text');
	$filed_validation = array('number'=>'Number Only','text'=>'Text Only','phone_number'=>'Phone Number','both'=>'Both');
	$date_type = array('single_date'=>'Single Date','double_date'=>'Double Date');
?>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-md">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
			<button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">
				Add Claim Status & Question
			</h4>
		</div>
      <div class="modal-body">
		{!! Form::open(['url'=>'followup/store/category','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform popupmedcubicsform']) !!}
           <!-- Modal Body -->
        <div class="modal-body form-horizontal">
            <div class="form-group">
				{!! Form::label('categoty', 'Claim Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('category',null,['maxlength'=>'20','class'=>'form-control','name'=>'category','id'=>'add_category']) !!}
                    {!! $errors->first('category', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div> 
			
			
			<div class="form-group">
                {!! Form::label('question', 'Question', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::textarea('question',null,['class'=>'form-control','name'=>'question','id'=>'add_question']) !!}
                    {!! $errors->first('question', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group">
                {!! Form::label('hint', 'Hint', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('hint',null,['class'=>'form-control','name'=>'hint','id'=>'add_question','maxlength'=>25, 'autocomplete'=>'off']) !!}
                    {!! $errors->first('question', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group">
                {!! Form::label('field_type', 'Field Type', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('field_type', array('' => '-- Select --') + (array)$filed_type,  null,['class'=>'form-control select2','id'=>'add_field_type']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group hide" id="date_type">
                {!! Form::label('date_type', 'Date Type', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('date_type', array('' => '-- Select --') + (array)$date_type,  null,['class'=>'form-control select2']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group hide" id="field_validation">
                {!! Form::label('field_validation', 'Field Validation', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('field_validation', array('' => '-- Select --') + (array)$filed_validation,  null,['class'=>'form-control select2']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			
			<div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active_1']) !!} {!! Form::label('c-active_1', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive_1']) !!} {!! Form::label('c-inactive_1', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
                    {!! $errors->first('status', '<p> :message</p>')  !!}
                </div>                       
            </div>
		</div>
		<div id="footer_part" class="modal-footer">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small js-submit-btn','id'=>'frmsubmit']) !!}
                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small close_popup']) !!}
            </div>
        </div>
		{!! Form::close() !!}
      </div>
     
    </div>

  </div>
</div>

<!-- Add Question Modal -->
<div id="addQuestion" class="modal fade" role="dialog">
  <div class="modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
			<button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">
				Add Claim Status & Question
			</h4>
		</div>
      <div class="modal-body">
		{!! Form::open(['url'=>'followup/store/question','id'=>'js-bootstrap-validator-addQuestion','name'=>'medcubicsform','class'=>'medcubicsform popupmedcubicsform']) !!}
           <!-- Modal Body -->
        <div class="modal-body form-horizontal">
            <div class="form-group">
                {!! Form::label('category', 'Claim Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('category', array('' => '-- Select --') + (array)$categorylist,  null,['class'=>'form-control select2','id'=>'followup_category','id'=>'add_category_Q']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			
			<div class="form-group">
                {!! Form::label('question', 'Question', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::textarea('question',null,['class'=>'form-control','name'=>'question','id'=>'add_question_Q']) !!}
                    {!! $errors->first('question', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group">
                {!! Form::label('hint', 'Hint', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('hint',null,['class'=>'form-control','name'=>'hint','maxlength'=>25, 'autocomplete'=>'off']) !!}
                    {!! $errors->first('question', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			
			<div class="form-group">
                {!! Form::label('field_type', 'Field Type', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('field_type', array('' => '-- Select --') + (array)$filed_type,  null,['class'=>'form-control select2','id'=>'addQ_field_type']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group hide" id="addQ_date_type">
                {!! Form::label('date_type', 'Date Type', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('date_type', array('' => '-- Select --') + (array)$date_type,  null,['class'=>'form-control select2']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group hide" id="addQ_field_validation">
                {!! Form::label('field_validation', 'Field Validation', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('field_validation', array('' => '-- Select --') + (array)$filed_validation,  null,['class'=>'form-control select2']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			
			<div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
                    {!! $errors->first('status', '<p> :message</p>')  !!}
                </div>                       
            </div>
		</div>
		<div id="footer_part" class="modal-footer">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small js-submit-btn','id'=>'frmsubmit']) !!}
                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small close_popup']) !!}
            </div>
        </div>
		{!! Form::close() !!}
      </div>
     
    </div>

  </div>
</div>

<!-- Edit Followup Question  Modal -->
<div id="edit_followup" class="modal fade" role="dialog"></div>

<!-- Edit Followup Category  Modal -->
<div id="edit_catQ" class="modal fade" role="dialog"></div>

@stop   

@push('view.scripts')  
<script type="text/javascript">
    $('#add_category').attr('autocomplete','off');
    $('#add_question').attr('autocomplete','off');
    $(document).ready(function () {
        $('#js-bootstrap-validator')
			.bootstrapValidator({
				message: 'This value is not valid',
				excluded: [':disabled', ':hidden', ':not(:visible)', '.group'],
				feedbackIcons: {
					valid: '',
					invalid: '',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					category: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Enter Category'
							}
						}
					},
					question: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Enter Question'
							}
						}
					},
					hint: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Enter Hint'
							}
						}
					},
					date_type: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Choose Date Type'
							}
						}
					},
					field_type: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Choose Field Type'
							}
						}
					},
					field_validation: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Choose Field Validation'
							}
						}
					}
				}
			});
    });
	
	$(document).ready(function () {
        $('#js-bootstrap-validator-addQuestion')
			.bootstrapValidator({
				message: 'This value is not valid',
				excluded: [':disabled', ':hidden', ':not(:visible)', '.group'],
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					category: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Enter Category'
							}
						}
					},
					question: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Enter Question'
							}
						}
					},
					hint: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Enter Hint'
							}
						}
					},
					date_type: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Choose Date Type'
							}
						}
					},
					field_type: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Choose Field Type'
							}
						}
					},
					field_validation: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Choose Field Validation'
							}
						}
					}
				}
			});
    });
	
	
	$(document).on('change','#add_field_type',function(){ 
		if($(this).val() == 'date'){
			$('#field_validation').addClass('hide');
			$('#date_type').removeClass('hide');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'field_validation');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'question');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'date_type');
		}else if($(this).val() == 'text' || $(this).val() == 'number'){
			$('#field_validation').removeClass('hide');
			$('#date_type').addClass('hide');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'field_validation');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'date_type');
		}
	});
	
	$(document).on('change','#addQ_field_type',function(){ 
		if($(this).val() == 'date'){
			$('#addQ_field_validation').addClass('hide');
			$('#addQ_date_type').removeClass('hide');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'field_validation');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'question');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'date_type');
		}else if($(this).val() == 'text' || $(this).val() == 'number'){
			$('#addQ_field_validation').removeClass('hide');
			$('#addQ_date_type').addClass('hide');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'field_validation');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'date_type');
		}
	});
			 
	$(document).on('change','#edit_field_type',function(){ 
		var value = $('#edit_field_type').val(); 
		if($(this).val() == 'date'){
			$('#edit_field_validation').addClass('hide');
			$('#edit_date_type').removeClass('hide');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'field_validation');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'question');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'date_type');
		}else if($(this).val() == 'text' || $(this).val() == 'number'){ 	
			$('#edit_field_validation').removeClass('hide');
			$('#edit_date_type').addClass('hide');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'field_validation');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'date_type');
		}
		
	});
	
	$(document).on('click','#add_follow_CQ',function(){
		$('#add_category').val('');
		$('#add_question').val('');
		$('#add_field_type').val('').select2();
		$('#js-bootstrap-validator').bootstrapValidator('resetForm', true);
		$('.snackbar-div').removeClass('show');
	});
	
	$(document).on('click','.clear-question-add',function(){
		$('#js-bootstrap-validator-addQuestion').bootstrapValidator('resetForm', true);
		$('#add_category_Q').select2('val',$(this).attr('data-category-id')).trigger('change');
		$('#add_question_Q').val('');
		$('#addQ_field_type').val('').select2();
		$('.snackbar-div').removeClass('show');
	});	
	
	$(document).on('click','.edit_followup',function(){
		$('.snackbar-div').removeClass('show');
	});
</script>
@endpush