<input type="text" name="cust_id" value="{{ Request::segment(3) }}" style="display:none;"/>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.customer_notes") }}' />
<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
            <div class="box box-info no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="notebook"></i> <h3 class="box-title">{{ $label }} Note</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <!-- form start -->

                <div class="box-body  form-horizontal">
                    <div class="form-group">
                        {!! Form::label('Title', 'Title', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-4 col-md-7 col-sm-12 @if($errors->first('title')) error @endif">
                            {!! Form::text('title',null,['class'=>'form-control','maxlength'=>100]) !!}
                            {!! $errors->first('title', '<p> :message</p>')  !!}
                        </div>
                    </div>  

                    <div class="form-group">
                        {!! Form::label('Contents', 'Contents', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-4 col-md-7 col-sm-12 @if($errors->first('content')) error @endif">
                            {!! Form::textarea('content',null,['class'=>'form-control','id'=>'editor1']) !!}
                            {!! $errors->first('content', '<p> :message</p>')  !!}
                        </div>
                    </div>               
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-lg-7 col-md-7 col-sm-7">
                        {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
                        <a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$customer->id.'/customernotes')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                    </div>
                </div><!-- /.box-footer -->
            </div><!-- /.box -->
        </div><!--/.col (left) -->
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->        


@push('view.scripts')
{!! HTML::script('js/address_check.js') !!}
<script type="text/javascript">
$(document).ready(function() {
		
    $('#js-bootstrap-validator').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            title:{
                message:'Title field is invalid',
                validators:{
                     notEmpty:{
                        message: '{{ trans("common.validation.title") }}'
                   	},
					regexp: {
						regexp: /^[a-zA-Z ]+$/,
						 message: '{{ trans("common.validation.alphaspace") }}'
					}
                }
            },
            content:{
                message:'',
                validators:{
                    notEmpty:{
                        message: '{{ trans("common.validation.content") }}'
                   	}
                }
            },
        }
    });
});
</script>
@endpush