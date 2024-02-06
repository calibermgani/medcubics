<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.modifier1") }}' />

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" ><!-- Col Starts -->
    <div class="box box-info no-shadow"><!-- Box General Information Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Claim Status</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->
		<input type="hidden" name="id" value="{{ Request::segment(4) }}" />
        <div class="box-body  form-horizontal margin-l-10"><!-- Box Body Starts -->
            <div class="form-group">
                {!! Form::label('categoty', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('code')) error @endif">
                    {!! Form::text('category',$category[0]->name,['maxlength'=>'20','class'=>'form-control','name'=>'category']) !!}
                    {!! $errors->first('category', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>

          
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-6 control-label']) !!} 
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 @if($errors->first('status')) error @endif">
				<?php $active = null; $inactive = null; ?>
				
				@if($category[0]->status == 'Active')
					<?php $active = true;   ?>
				@else
					<?php $inactive = true; ?>
				@endif
				
				{!! Form::radio('status', 'Active',$active,['class'=>'flat-red','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
				
				{!! Form::radio('status', 'Inactive',$inactive,['class'=>'flat-red','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
				{!! $errors->first('status', '<p> :message</p>')  !!}
                </div>                       
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                {!! Form::submit($submitBtn, ['name'=>'','class'=>'btn btn-medcubics']) !!}
                <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                @if(strpos($currnet_page, 'edit') !== false)
                @if($checkpermission->check_url_permission('modifierlevel1/delete/{id}') == 1 )
                <a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure you want to delete?" href="{{ url('modifierlevel1/delete/'.$modifiers->id) }}">Delete</a>
                @endif
                <a href="javascript:void(0)" data-url="{{url('followup/category')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @else

                <a href="javascript:void(0)" data-url="{{url('followup/category')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a> 
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
				excluded: ':disabled',
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
					}
				}
			});
    });
</script>
@endpush