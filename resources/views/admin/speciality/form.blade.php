<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.speciality") }}' />

<div class="col-md-12 col-md-12 col-sm-12 col-xs-12 " >
	<div class="box box-info no-shadow">
		<div class="box-block-header margin-b-10">
			<h3 class="box-title"><i class="livicon" data-name="info" data-color='#008e97' data-size='16'></i> General Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		
		<div class="box-body form-horizontal margin-l-10">
			<div class="form-group">
				{!! Form::label('Specialty', 'Specialty', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!} 
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-10 @if($errors->first('speciality')) error @endif">
					{!! Form::text('speciality',null,['class'=>'form-control','maxlength'=>'100']) !!}
					{!! $errors->first('speciality', '<p> :message</p>')  !!}
				</div>                        
			</div>                                         
		</div><!-- /.box-body -->
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
			{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
			@php $currnet_page = Route::getFacadeRoot()->current()->uri(); @endphp
			@if(strpos($currnet_page, 'edit') !== false)
				@if($checkpermission->check_adminurl_permission('admin/speciality/delete/{id}') == 1)
					<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete?" href="{{ url('admin/speciality/delete/'.$speciality->id) }}">Delete</a>
				 @endif
				<a href="javascript:void(0)" data-url="{{ url('admin/speciality/'.$speciality->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@else
				<a href="javascript:void(0)" data-url="{{ url('admin/speciality')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif 
		</div>
	</div><!-- /.box -->
</div><!--/.col (left) -->
    
@push('view.scripts')                           
<script type="text/javascript">
    $(document).ready(function() {
        $('#js-bootstrap-validator')
            .bootstrapValidator({
                message: '',
                        excluded: ':disabled',
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    speciality:{
                        message:'',
                        validators:{
                            notEmpty:{
                                message: '{{ trans("admin/speciality.validation.speciality") }}'
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