<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.id_qualifier") }}' />
<div class="col-md-12" >
    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->
        <div class="box-body form-horizontal margin-l-10">
            <div class="form-group">
                {!! Form::label('ID Qualifiers name', 'ID Qualifiers Name', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-7 col-xs-10 @if($errors->first('id_qualifier_name')) error @endif">
                    {!! Form::text('id_qualifier_name',null,['class'=>'form-control js-letters-caps-format','maxlength'=>250]) !!}
                    {!! $errors->first('id_qualifier_name', '<p> :message</p>') !!}
                </div>
            </div>
       </div><!-- /.box-body -->
       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center margin-t-10">
			{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
			<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
			@if(strpos($currnet_page, 'edit') !== false)
				@if($checkpermission->check_adminurl_permission('admin/qualifiers/{qualifier_id}/delete') == 1)
					<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete?" href="{{ url('admin/qualifiers/'.$qualifiers->id.'/delete') }}">Delete</a>
				@endif
				<a href="javascript:void(0)" data-url="{{ url('admin/qualifiers/'.$qualifiers->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@else
				<a href="javascript:void(0)" data-url="{{ url('admin/qualifiers')}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif
		</div>
    </div><!-- /.box -->
</div><!--/.col (left) -->

@push('view.scripts')
	<script type="text/javascript">
		$(document).ready(function () {
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
                        id_qualifier_name: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/qualifier.validation.qualifier") }}'
                                },
                                regexp: {
                                    regexp: /^[A-Za-z0-9 ()\s ]+$/,
                                    message: '{{ trans("common.validation.alphanumericspac") }}'
                                }
                            }
                        },
                    }
                });
		});
	</script>
@endpush