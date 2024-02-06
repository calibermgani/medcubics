<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.codes") }}' />
<?php 
    if(!isset($get_default_timezone)){
       $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
    }      
?>
<div class="col-md-12 col-md-12 col-sm-12 col-xs-12" >
	<div class="box box-info no-shadow">
		<div class="box-block-header margin-b-10">
		<h3 class="box-title"><i class="livicon" data-name="code" data-color='#008e97' data-size='16'></i> Remittance Code Details</h3>
		<div class="box-tools pull-right">
		  <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
		</div><!-- /.box-header -->
		<div class="box-body  form-horizontal margin-t-10 margin-l-10">
			<div class="form-group">
			  {!! Form::label('CodeCategory', 'Code Category', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
			  <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10 @if($errors->first('codecategory_id')) error @endif">
				  {!! Form::select('codecategory_id', array('' => '-- Select --') + (array)$codecategory,  NULL,['class'=>'form-control select2']) !!}
				  {!! $errors->first('codecategory_id', '<p> :message</p>')  !!}
			  </div>
			  <div class="col-sm-1 col-xs-2"></div>
			</div>
			<div class="form-group">
				{!! Form::label('TransactionCode', 'Transaction Code', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-10 @if($errors->first('transactioncode_id')) error @endif">
					{!! Form::text('transactioncode_id',null,['class'=>'form-control','maxlength'=>'5']) !!}
					{!! $errors->first('transactioncode_id', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group">
				{!! Form::label('Description', 'Description', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('description')) error @endif">
					{!! Form::textarea('description',null,['class'=>'form-control']) !!}
					{!! $errors->first('description', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
			@if(strpos($currnet_page, 'edit') !== false)
			<?php 
				$code->start_date= ($code->start_date!='0000-00-00')? date("m/d/Y",strtotime($code->start_date)):'';
				$code->last_modified_date= ($code->last_modified_date!='0000-00-00')? date("m/d/Y",strtotime($code->last_modified_date)):'';
			?>
			@endif
			<div class="form-group">
				{!! Form::label('start date', 'Start Date', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
					<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i>{!! Form::text('start_date',null,['class'=>'form-control dm-date form-cursor','id'=>'start_date','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">
				{!! Form::label('last modified date', 'Last Modified Date', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
					<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i>{!! Form::text('last_modified_date',null,['class'=>'form-control dm-date form-cursor','id'=>'last_modified_date','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">
				{!! Form::label('status', 'Status',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label']) !!}
				<div class="col-lg-5 col-md-5 col-sm-6 col-xs-6 @if($errors->first('status')) error @endif">
					{!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
					{!! $errors->first('status', '<p> :message</p>')  !!}
				</div>						
			</div>
                                
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
				{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics']) !!}
				@if(strpos($currnet_page, 'edit') !== false)
				@if($checkpermission->check_url_permission('code/delete/{code_id}') == 1)
					<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure to delete the entry?"
					href="{{ url('code/delete/'.$code->id) }}">Delete</a>
				@endif
					<a href="javascript:void(0)" data-url="{{url('code/'.$code->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
				@else
					<a href="javascript:void(0)" data-url="{{url('code')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
				@endif
			</div>
                                
		</div><!-- /.box-body -->			
		
	</div><!-- /.box -->
</div><!--/.col (left) -->
	
@push('view.scripts')
<script type="text/javascript">
	$('#start_date').attr('autocomplete','off');
	$('#last_modified_date').attr('autocomplete','off');
	$(document).ready(function() {
		$(function () {
			var eventDates = {};
            eventDates[ new Date( '<?php echo $get_default_timezone; ?>' )] = new Date( '<?php echo $get_default_timezone; ?>' );
			$("#start_date").datepicker({
				changeMonth: true,
				changeYear: true,
				beforeShowDay: function(d) {
	            setTimeout(function() {
	            $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
	             }, 10);
	            var highlight = eventDates[d];
	                if( highlight ) {
	                     return [true, "ui-state-highlight", ''];
	                } else {
	                   
	                     return [true, '', ''];
	                }
	          	},
				onClose: function (selectedDate) {
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="start_date"]'));
				}
			});
			$("#last_modified_date").datepicker({
				changeMonth: true,
				changeYear: true,
				beforeShowDay: function(d) {
	            setTimeout(function() {
	            $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
	             }, 10);
	            var highlight = eventDates[d];
	                if( highlight ) {
	                     return [true, "ui-state-highlight", ''];
	                } else {	                   
	                     return [true, '', ''];
	                }
	          	},
				onClose: function (selectedDate) {
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="last_modified_date"]'));
				}
			});
		});
		$('#js-bootstrap-validator').bootstrapValidator({
			message: 'This value is not valid',
			excluded: ':disabled',
			feedbackIcons: {
				valid: '',
				invalid: '',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				codecategory_id:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("practice/practicemaster/codes.validation.category") }}'
						}
					}
				},
				transactioncode_id:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("practice/practicemaster/codes.validation.tr_code") }}'
						},
						regexp:{
							regexp: /^[a-zA-Z0-9]{0,5}$/,
							message: '{{ trans("practice/practicemaster/codes.validation.tr_code_regex") }}'
						}
					}
				},
				description:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("common.validation.description") }}'
						}
					}
				},
				status:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("practice/practicemaster/codes.validation.status") }}'
						}
					}
				},
				start_date:{
					message: '',
					trigger: 'keyup change',
					validators:{
						date:{
							format: 'MM/DD/YYYY',
							message: '{{ trans("common.validation.date_format") }}'
						}
					}
				},
				last_modified_date:{
					message:'',
					validators:{
						date:{
							format: 'MM/DD/YYYY',
							message: '{{ trans("common.validation.date_format") }}'
						}
					}
				}
			}
		});
	});
</script>
@endpush