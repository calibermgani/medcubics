<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.pos") }}' />
<div class="col-md-12 col-md-12 col-sm-12 col-xs-12" >
	<div class="box box-info no-shadow">
		<div class="box-block-header margin-b-10">
			<h3 class="box-title"><i class="livicon" data-name="info" data-color='#008e97' data-size='16'></i>General Details</h3>
			<div class="box-tools pull-right">
			  <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->

		<div class="box-body form-horizontal margin-l-10">
			<div class="form-group">
				{!! Form::label('code', 'Code', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
				<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('code')) error @endif">
					{!! Form::text('code',null,['class'=>'form-control','maxlength' => '2']) !!}
					{!! $errors->first('code', '<p> :message</p>') !!}
				</div>
			</div>

			<div class="form-group">
				{!! Form::label('pos', 'Place of Service', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
				<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('pos')) error @endif">
					{!! Form::text('pos',null,['class'=>'form-control','maxlength'=>255]) !!}
					{!! $errors->first('pos', '<p> :message</p>') !!}
				</div>
			</div>
		</div><!-- /.box-body -->

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center margin-t-10">
		  {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}

			<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
			@if(strpos($currnet_page, 'edit') !== false)
				@if($checkpermission->check_adminurl_permission('admin/placeofservice/delete/{id}') == 1)
					<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete?" href="{{ url('admin/placeofservice/delete/'.$pos->id) }}">Delete</a>
				@endif
				<a href="javascript:void(0)" data-url="{{ url('admin/placeofservice/'.$pos->id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@else
				<a href="javascript:void(0)" data-url="{{ url('admin/placeofservice')}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif
		</div>
	</div><!-- /.box -->
</div><!--/.col (left) -->

@push('view.scripts')
	<script type="text/javascript">
		$(document).ready(function() {
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
					code:{
						message:'',
						validators:{
							callback: {
								callback: function (value, validator) {
									var re = /^[0-9]+$/i;
									var message='';
									if(value ==' ' || value =='')
										 message = '{{ trans("admin/pos.validation.code") }}';
									else if(value !='' &&  value ==0)
											message= '{{ trans("admin/pos.validation.code_regex") }}';
									else if(value !='' && !re.test(value))
											message = '{{ trans("common.validation.numeric") }}';
									if(message=='')
										return true;
									else 
										return {
											valid: false,
											message: message
										};
								}
							}
						}
					},
					pos:{
						message:'',
						validators:{
							callback: {
								callback: function (value, validator) {
									var re = /^[A-Za-z0-9 ]+$/i;
									var message='';
									if(value ==' ' || value =='')
										 message = '{{ trans("admin/pos.validation.pos") }}';
									else if(value !='' &&  value ==0)
											message= '{{ trans("admin/pos.validation.pos_regex") }}';
									else if(value !='' && !re.test(value))
											message = '{{ trans("common.validation.alphanumericspac") }}';
									if(message=='')
										return true;
									else 
										return {
											valid: false,
											message: message
										};
									
								}
							}
						}
					}
				}
			});
		});
	</script>
@endpush