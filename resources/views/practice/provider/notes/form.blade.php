<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
            <div class="box box-info no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="pin-on"></i> <h3 class="box-title">New Note</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <!-- form start -->

                <div class="box-body  form-horizontal">
                    <div class="form-group">
                        {!! Form::label('Title', 'Title', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-4 col-md-7 col-sm-12 @if($errors->first('title')) error @endif">
                            {!! Form::text('title',null,['class'=>'form-control']) !!}
							{!! $errors->first('title', '<p>:message</p>') !!}
						</div>
                    </div>  
                    <div class="form-group">
                        {!! Form::label('content', 'Content', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-4 col-md-7 col-sm-12 @if($errors->first('content')) error @endif">
                            {!! Form::textarea('content',null,['class'=>'form-control']) !!}
							{!! $errors->first('content', '<p>:message</p>') !!}
						</div>
                    </div>
                    
                    <div class="box-footer">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                        {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
                        <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
						@if(strpos($currnet_page, 'facility') !== false)
						<a href="javascript:void(0)" data-url="{{ url('facility/'.$facility->id.'/notes') }}">
						{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}
						</a>
						@else
						{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site','onclick' => 'history.back(-1)']) !!}
						@endif
                    </div>
                </div><!-- /.box-footer -->
                </div><!-- /.box-body -->
                
            </div><!-- /.box -->
        </div><!--/.col (left) -->
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->        

@push('view.scripts')
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
                }
			},
			content:{
                message:'Content field is invalid',
                validators:{
                    notEmpty:{
                        message: '{{ trans("common.validation.content") }}'
                   	},
                }
            },
		}
	});
});
</script>
@endpush