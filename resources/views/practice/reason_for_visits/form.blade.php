<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.reasonforvisit") }}' />

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
            <div class="form-group">
                {!! Form::label('Reason', 'Reason', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label star']) !!}  
					
                <div class="col-lg-4 col-md-5 col-sm-8 col-xs-12 @if($errors->first('reason')) error @endif">
                    {!! Form::text('reason',null,['class'=>'form-control','maxlength'=>'25', 'autocomplete'=>'off']) !!}
					{!! $errors->first('reason', '<p> :message</p>')  !!}							
                </div>                
            </div>   

            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-3 control-label']) !!}                                                  
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-9">  
                    {!! Form::radio('status', 'Active','true',['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                
            </div>
            
            <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
            {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}
            @if(strpos($currnet_page, 'edit') !== false)
				@if($checkpermission->check_url_permission('reason/{id}/delete') == 1)
					<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure to delete the entry?" href="{{ url('reason/'.$reason->id.'/delete') }}">Delete</a>
				@endif
				<a href="{{ url('reason/'.$reason->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
            @else
				<a href="{{ url('reason') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
            @endif
            </div>	
        </div><!-- /.box-body -->
        
    </div><!-- General info box Ends-->
</div><!--  Left side Content Ends -->   
		
@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $("input[name='reason']").attr('autocomplete','off')
        $('#js-bootstrap-validator').bootstrapValidator({
		
			message : 'This value is not valid',
			excluded : ':disabled',
			feedbackIcons : {
				valid : 'glyphicon glyphicon-ok',
				invalid : 'glyphicon glyphicon-remove',
				validating : 'glyphicon glyphicon-refresh'
			},
            fields: {
                reason: {
					 message: 'The reason is invalid',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/reason.validation.reason") }}'
                        },
                        /*regexp: {
                                regexp: /^[A-Za-z' ]+$/,
                                message: alphaspace_lang_err_msg
                            },*/
						callback: {
							message: '',
							callback: function (value, validator) {
								if(value.length > 250){
									return {
										valid: false,
										message: '{{ trans("common.validation.limit_char") }}'
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