<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.holdoption") }}' />

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
                {!! Form::label('procedurecategory', 'Category Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}  
                <div class="col-lg-4 col-md-6 col-sm-8 col-xs-12 @if($errors->first('hold_reason')) error @endif">
                    <?php // text box change to textarea the reason is open pop-up but, At the same time form is submited so changed
                    ?>
                    {!! Form::text('procedure_category',null,['class'=>'form-control','maxlength'=>'100','autocomplete'=>'off']) !!}
                    {!! $errors->first('procedure_category', '<p> :message</p>')  !!}							
                </div>                
            </div>  
			
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-3 control-label']) !!}                                                  
                <div class="col-lg-8 col-md-7 col-sm-8 col-xs-9">  
                    {!! Form::radio('status', 'Active','true',['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                
            </div>         
            
            <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
			{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}
			@if(strpos($currnet_page, 'edit') !== false)
				<?php /* Delete option commented.
				@if($checkpermission->check_url_permission('statementholdreason/{id}/delete') == 1)
				<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure you want to delete?" href="{{ url('statementholdreason/'.$statementholdreason->id.'/delete') }}">Delete</a>
				@endif
				*/?>
				<a href="javascript:void(0)" data-url="{{ url('procedurecategory/'.$procedurecategory_id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@else
				<a href="javascript:void(0)" data-url="{{ url('procedurecategory') }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif
		</div>	   
        </div><!-- /.box-body -->
        
             
    </div><!-- General info box Ends-->
</div><!--  Left side Content Ends -->   


@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {

        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                procedure_category: {
                    validators: {
                        notEmpty: {
                            message: 'Enter Category Name'
                        },
                        /*regexp: {
                            regexp: /^[A-Za-z \s]+$/,
                            message: '{{ trans("common.validation.alphaspace") }}'
                        },*/
                    }
                }
            }
        });
    });

</script>
@endpush