<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.clinicalcategory") }}' />
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
	<div class="box box-info no-shadow">
		<div class="box-block-header with-border">
			<i class="livicon" data-name="info"></i> <h3 class="box-title"> General Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<!-- form start -->

		<div class="box-body form-horizontal margin-l-10">  
		   
			<div class="form-group">
				{!! Form::label('Category', 'Category',  ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
				<div class="col-lg-3 col-md-4 col-sm-6 col-xs-8 @if($errors->first('category_value')) error @endif">
					{!! Form::text('category_value',@$clinicalcategories->category_value,['class'=>'form-control','name'=>'category_value']) !!}
					{!! $errors->first('category_value', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
					
		</div><!-- /.box -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label">&emsp;</div>
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 @if($errors->first('category_value')) error @endif">
					{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics']) !!} 
					<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
					@if(strpos($currnet_page, 'edit') !== false)
					<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete?" href="{{ url('clinicalnotescategory/delete/'.$clinicalcategories->id) }}">Delete</a>
					<a href="javascript:void(0)" data-url="{{ url('clinicalnotescategory')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
					@else
					<a href="javascript:void(0)" data-url="{{ url('clinicalnotescategory')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
					@endif
				</div>
			</div>	
	</div><!--/.col (left) -->
</div><!--Background color for Inner Content Ends -->
   
@push('view.scripts')  
    <script type="text/javascript">
        $(document).ready(function () {
            /*Bootstrap Code Starts Here*/
            /*--------------------------*/
            $('#js-bootstrap-validator')
				.bootstrapValidator(
				{
					message: 'This value is not valid',
					excluded: ':disabled',
					feedbackIcons: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
					},
					fields: {
						category_value: {
							message: '',
							validators: {
								notEmpty: {
									message: '{{ trans("practice/practicemaster/template.validation.category") }}'
								},
								callback: {
									message: '',
									callback: function (value, validator) {
										var mesg = '{{ trans("common.validation.alphanumericspac") }}';
										var regex = new RegExp(/^[A-Za-z0-9 ]+$/);
										var msg = lengthValidation(value,'feeschedule',regex,mesg);
										if(value !='' && msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
							}
						},
					}
				});
        });
    </script>
@endpush