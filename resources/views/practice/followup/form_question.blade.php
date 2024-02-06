<?php 
	$filed_type = array('date'=>'Date','text'=>'Text');
	$filed_validation = array('number'=>'Number Only','text'=>'Text Only','both'=>'Both');
	$date_type = array('single_date'=>'Single Date','double_date'=>'Double Date');
?>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.modifier1") }}' />

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" ><!-- Col Starts -->
    <div class="box box-info no-shadow"><!-- Box General Information Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Claim Status Question</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->

        <div class="box-body  form-horizontal margin-l-10"><!-- Box Body Starts -->
			<div class="form-group">
                {!! Form::label('category', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('code')) error @endif">
                   {!! Form::select('category', array('' => '-- Select --') + (array)$category,  null,['class'=>'form-control select2']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group">
                {!! Form::label('question', 'Question', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('code')) error @endif">
                    {!! Form::text('question',null,['class'=>'form-control','name'=>'question']) !!}
                    {!! $errors->first('question', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			<div class="form-group">
                {!! Form::label('field_type', 'Field Type', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('code')) error @endif">
                   {!! Form::select('field_type', array('' => '-- Select --') + (array)$filed_type,  null,['class'=>'form-control select2']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group hide" id="date_type">
                {!! Form::label('date_type', 'Date Type', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('code')) error @endif">
                   {!! Form::select('date_type', array('' => '-- Select --') + (array)$date_type,  null,['class'=>'form-control select2']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group hide" id="field_validation">
                {!! Form::label('field_validation', 'Field Validation', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('code')) error @endif">
                   {!! Form::select('field_validation', array('' => '-- Select --') + (array)$filed_validation,  null,['class'=>'form-control select2']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-6 control-label']) !!} 
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 @if($errors->first('status')) error @endif">
                    {!! Form::radio('status', 'Active',true,['class'=>'flat-red','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
                    {!! $errors->first('status', '<p> :message</p>')  !!}
                </div>                       
            </div>
			
			<div class="box-body  form-horizontal margin-l-10"><!-- Box Body Starts -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                {!! Form::submit($submitBtn, ['name'=>'','class'=>'btn btn-medcubics']) !!}
                <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                @if(strpos($currnet_page, 'edit') !== false)
                @if($checkpermission->check_url_permission('modifierlevel1/delete/{id}') == 1 )
                <a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure you want to delete?" href="{{ url('modifierlevel1/delete/'.$modifiers->id) }}">Delete</a>
                @endif
                <a href="javascript:void(0)" data-url="{{url('followup/question')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @else

                <a href="javascript:void(0)" data-url="{{url('followup/question')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a> 
                @endif
            </div>
        </div> 
    </div><!-- Box General Information Ends -->
</div><!--/.col ends -->

@push('view.scripts')  
<script type="text/javascript">
	
    $(document).ready(function () {
        $('#js-bootstrap-validator')
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
	
	$(document).on('change','#field_type',function(){
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
</script>
@endpush