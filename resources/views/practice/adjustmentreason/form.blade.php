<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.adjustmentreason") }}' />

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!--  Left side Content Starts -->   
    <div class="box box-info no-shadow"><!-- General Info Box Starts -->
        <div class="box-block-header with-border">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Add Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
		  <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
        <div class="box-body form-horizontal margin-l-10">
            <div class="form-group">
                {!! Form::label('Adjustment Type', 'Adjustment Type', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}   						
                <div class="col-lg-5 col-md-5 col-sm-8 col-xs-12 @if($errors->first('adjustment_type')) error @endif">
                    {!! Form::radio('adjustment_type', 'Insurance','true',['class'=>'','id'=>'c-ins']) !!} {!! Form::label('c-ins', 'Insurance',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('adjustment_type', 'Patient',null,['class'=>'','id'=>'c-pat']) !!} {!! Form::label('c-pat', 'Patient',['class'=>'med-darkgray font600 form-cursor']) !!}
					{!! $errors->first('adjustment_type', '<p> :message</p>')  !!}							
                </div>                
            </div> 
			<div class="form-group">
                {!! Form::label('Adjustment ShortName', 'Adjustment ShortName', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}                          
                <div class="col-lg-5 col-md-5 col-sm-8 col-xs-12 @if($errors->first('adjustment_shortname')) error @endif">
                   {!! Form::text('adjustment_shortname',null,['class'=>'form-control js_all_caps_format','maxlength'=>'6']) !!}
                    {!! $errors->first('adjustment_shortname', '<p> :message</p>')  !!}                          
                </div>                
            </div> 
             <div class="form-group">
                {!! Form::label('Adjustment Reason', 'Adjustment Reason', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}                          
                <div class="col-lg-5 col-md-5 col-sm-8 col-xs-12 @if($errors->first('adjustment_reason')) error @endif">
					{!! Form::textarea('adjustment_reason',null,['class'=>'form-control js-firstletter-caps-format','maxlength'=>'25']) !!}
                    {!! $errors->first('adjustment_reason', '<p> :message</p>')  !!}                          
                </div>                
            </div> 
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">  
                    {!! Form::radio('status', 'Active','true',['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                
            </div>
            
            <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
            {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}
            @if(strpos($currnet_page, 'edit') !== false)
				@if($checkpermission->check_url_permission('adjustmentreason/{adjustmentreason_id}/delete') == 1)
					<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure to delete the entry?" href="{{ url('adjustmentreason/'.$adjustmentreason->id.'/delete') }}">Delete</a>
				@endif
            <a href="javascript:void(0)" data-url="{{ url('adjustmentreason/'.$adjustmentreason->id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
            @else
				<a href="javascript:void(0)" data-url="{{ url('adjustmentreason') }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
            @endif
        </div>	
        </div><!-- /.box-body -->
        
    </div><!-- General info box Ends-->
</div><!--  Left side Content Ends -->   

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $("input[name='adjustment_shortname']").attr('autocomplete','off');
        $('#js-bootstrap-validator').bootstrapValidator({
		
			message : 'This value is not valid',
			excluded : ':disabled',
			feedbackIcons : {
				valid : '',
				invalid : '',
				validating : 'glyphicon glyphicon-refresh'
			},
            fields: {
                adjustment_reason: {
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/adjustmentreason.validation.adjustmentreason") }}'
                        }
                    }
                },
				adjustment_shortname: {
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/adjustmentreason.validation.adjustment_shortname") }}'
                        },
						regexp: {
                        regexp: /^[a-z\s0-9]+$/i,
                        message: 'Special characters not allowed'
						}
                    }
                },
                 adjustment_type: {
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/adjustmentreason.validation.adjustmenttype") }}'
                        }
                    }
                }
            }
        });
    });
</script>

@endpush