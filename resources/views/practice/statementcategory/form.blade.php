<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.holdoption") }}' />
<?php  if(!isset($get_default_timezone)){
    $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
}?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!--  Left side Content Starts -->   
    <div class="box box-info no-shadow"><!-- General Info Box Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Add Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
        <div class="box-body form-horizontal margin-l-10">
            <div class="form-group margin-b-15">
                {!! Form::label('stmt_option', 'Statement Option', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}  
                <div class="col-lg-4 col-md-6 col-sm-8 col-xs-12 @if($errors->first('stmt_option')) error @endif">
                    
                    {!! Form::radio('stmt_option', 'Yes','true',['class'=>'js_stmt_opt','id'=>'stmt_option_y']) !!} 
					{!! Form::label('stmt_option_y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
					
                    {!! Form::radio('stmt_option', 'Hold',null,['class'=>'js_stmt_opt','id'=>'stmt_option_h']) !!} 
					{!! Form::label('stmt_option_h', 'Hold',['class'=>'med-darkgray font600 form-cursor']) !!}  &emsp; 
					
					{!! Form::radio('stmt_option', 'Insurance Only',null,['class'=>'js_stmt_opt','id'=>'stmt_option_insuranceonly']) !!} 
					{!! Form::label('stmt_option_insuranceonly', 'Insurance Only',['class'=>'med-darkgray font600 form-cursor']) !!}  &emsp; 
					
                    {!! $errors->first('stmt_option', '<p> :message</p>')  !!}			
                </div>   				
            </div> 			
			
			
			<div class="form-group">
                {!! Form::label('category', 'Statement Category', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}  
                <div class="col-lg-4 col-md-6 col-sm-8 col-xs-12 @if($errors->first('category')) error @endif">
                    <?php // text box change to textarea the reason is open pop-up but, At the same time form is submited so changed
                    ?>                    
					{!! Form::text('category',null,['class'=>'form-control','maxlength'=>'100']) !!}
                    {!! $errors->first('category', '<p> :message</p>')  !!}							
                </div>                
            </div>
			
			<?php									
				$disabled_class = 'disabled';
				if(isset($statementcategory->stmt_option) && $statementcategory->stmt_option == 'Hold') {										
					$disabled_class = '';
				}
				$hold_rel_date = isset($statementcategory->hold_release_date)? App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$statementcategory->hold_release_date): null;									
			?>
			<div class="form-group">
                {!! Form::label('hold_reason', 'Hold Reason', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label ']) !!}  
                <div class="col-lg-4 col-md-6 col-sm-8 col-xs-12 @if($errors->first('hold_reason')) error @endif">
                        
					{!! Form::select('hold_reason', array(''=>'-- Select --')+(array)@$stmt_holdreason,null,['class'=>'select2 form-control js_hold_blk','tabindex'=>'53', $disabled_class]) !!}
                    {!! $errors->first('hold_reason', '<p> :message</p>')  !!}							
                </div>                
            </div>

			<div class="form-group">
                {!! Form::label('hold_release_date', 'Hold Release Date', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label ']) !!}  
                <div class="col-lg-4 col-md-6 col-sm-8 col-xs-12 @if($errors->first('hold_release_date')) error @endif">
                    <?php // text box change to textarea the reason is open pop-up but, At the same time form is submited so changed
                    ?>                    
					{!! Form::text('hold_release_date',$hold_rel_date,['class'=>'form-control js_hold_blk','maxlength'=>'100', $disabled_class]) !!}
                    {!! $errors->first('hold_release_date', '<p> :message</p>')  !!}							
                </div>                
            </div>

            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-3 control-label star']) !!}
                <div class="col-lg-8 col-md-7 col-sm-8 col-xs-9">  
                    {!! Form::radio('status', 'Active','true',['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                
            </div>         
            
            <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
			{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}
			@if(strpos($currnet_page, 'edit') !== false)

				@if($checkpermission->check_url_permission('statementcategory/{id}/delete') == 1)
					<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure you want to delete?" href="{{ url('statementcategory/'.$statementcategory->id.'/delete') }}">Delete</a>
				@endif
				<a href="javascript:void(0)" data-url="{{ url('statementcategory/'.$statementcategory->id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@else
				<a href="javascript:void(0)" data-url="{{ url('statementcategory') }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif
		</div>	   
        </div><!-- /.box-body -->
        
             
    </div><!-- General info box Ends-->
</div><!--  Left side Content Ends -->   


@push('view.scripts')
<script type="text/javascript">
    <?php if(isset($get_default_timezone)){?>
        var get_default_timezone = '<?php echo $get_default_timezone;?>';    
    <?php }?>
    $(document).ready(function () {
        $('#category').attr('autocomplete','off');
        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                stmt_options: {
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/holdoption.validation.option") }}'
                        },
                        /*regexp: {
                            regexp: /^[A-Za-z \s]+$/,
                            message: '{{ trans("common.validation.alphaspace") }}'
                        },*/
                    }
                },
				category: {
                    validators: {
                        notEmpty: {
                            message: 'Please select the category'
                        },
                        /*regexp: {
                            regexp: /^[A-Za-z \s]+$/,
                            message: '{{ trans("common.validation.alphaspace") }}'
                        },*/
                    }
                }
            }
        });

		$(document).on( 'change', '.js_stmt_opt', function () { 
			var stmtVal = $(this).val();
			if(stmtVal == 'Hold') {
				$('.js_hold_blk').prop("disabled", false);
			} else {
				$('#hold_reason').select2("val","").prop("disabled", true);
				$('#hold_release_date').val("").prop("disabled", true);
			}
		});
		
		$(document).on('focus','#hold_release_date', function(){ 
            var tomdate = new Date(get_default_timezone);tomdate.setDate(tomdate.getDate() + 1);      
			var id_name = $(this).attr('id');
			$("#"+id_name).datepicker({
				dateFormat: 'mm/dd/yy',
				changeMonth: true,
				changeYear: true,
				minDate: tomdate,
				onClose: function (selectedDate) {
					//
				}
			});
		});

    });

</script>
@endpush