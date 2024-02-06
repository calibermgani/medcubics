<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.insurancetypes") }}' />

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
	<div class="box box-info no-shadow">
		<div class="box-block-header margin-b-10">
			<i class="livicon" data-name="info"></i> <h3 class="box-title"> General Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<!-- form start -->
		
		<div class="box-body  form-horizontal margin-l-10">
			<div class="form-group">
				{!! Form::label('typename', 'Insurance Type', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 @if($errors->first('type_name')) error @endif">
					{!! Form::text('type_name',null,['class'=>'form-control js-letters-caps-format', 'name'=>'type_name', 'maxlength'=>'100']) !!}
					{!! $errors->first('type_name', '<p> :message</p>') !!}
				</div>
				<div class="col-sm-1 col-xs-2"></div>
			</div>
			<div class="form-group">
				{!! Form::label('code', 'POS Code', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 @if($errors->first('code')) error @endif">
					{!! Form::text('code',null,['class'=>'form-control js-letters-caps-format','name'=>'code','maxlength'=>'2']) !!}
					{!! $errors->first('code', '<p> :message</p>') !!}
				</div>
				<div class="col-sm-1 col-xs-2"></div>
			</div>
			<div class="form-group">
				{!! Form::label('cms_type', 'CMS Type', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 @if($errors->first('cms_type')) error @endif">
					{!! Form::select('cms_type', (array)@$cmstypes,  null,['class'=>'form-control select2']) !!}					
					{!! $errors->first('cms_type', '<p> :message</p>') !!}
				</div>
				<div class="col-sm-1 col-xs-2"></div>
			</div>
		</div>
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
			{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
			<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
				
			@if(strpos($currnet_page, 'edit') !== false && $checkpermission->check_adminurl_permission('admin/insurancetypes/delete/{id}') == 1)
				@if(App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($insurancetypes->id,'decode') != Config::get('siteconfigs.insurance_type_id.default_id'))
					<a class="js-delete-confirm" data-text="Are you sure would you like to delete?" href="{{ url('admin/insurancetypes/delete/'.$insurancetypes->id) }}">{!! Form::button('Delete', ['class'=>'btn btn-medcubics']) !!}</a>
				@endif
			@endif
			
			@if(strpos($currnet_page, 'edit') == false)
				<a href="javascript:void(0)" data-url="{{ url('admin/insurancetypes') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>  
			@endif
			
			@if(strpos($currnet_page, 'edit') !== false)
				<a href="javascript:void(0)" data-url="{{ url('admin/insurancetypes/'.$insurancetypes->id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif
		</div>
	</div><!-- /.box -->
</div><!--/.col (left) -->

@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function() {
		/*Bootstrap Code Starts Here*/
		/*--------------------------*/
		$('#js-bootstrap-validator').bootstrapValidator({
			message : 'This value is not valid',
			excluded : ':disabled',
			feedbackIcons : {
				valid : 'glyphicon glyphicon-ok',
				invalid : 'glyphicon glyphicon-remove',
				validating : 'glyphicon glyphicon-refresh'
			},
			fields : {
				type_name: {
					message : '',
					validators : {
						notEmpty : {
							message : '{{ trans("admin/insurancetype.validation.type_name") }}'
						},
						regexp:{
							regexp: /^[A-Za-z ]+$/,
							message: '{{ trans("common.validation.alphaspace") }}'
						}
					}
				},
			}
		});
	});
</script>
@endpush
