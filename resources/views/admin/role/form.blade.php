
        <div class="col-md-12" >
            <div class="box no-shadow">
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">Role Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <!-- form start -->


                <div class="box-body form-horizontal margin-l-10">

                    <div class="form-group">
                        {!! Form::label('RoleName', 'Role Name', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('role_name')) error @endif">
                            {!! Form::text('role_name',null,['class'=>'form-control js-letters-caps-format']) !!}
                            {!! $errors->first('role_name', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                     <div class="form-group">
                            {!! Form::label('roletype', 'Role Type',  ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-4 control-label']) !!}
                            <div class="col-lg-8col-md-8 col-sm-8 col-xs-8 @if($errors->first('role_type')) error @endif">
                                 @if(end($get_prev)=='practicerole')
                                    {!! Form::radio('role_type', 'Medcubics',null,['class'=>'','id'=>'ad_medcubics']) !!} {!! Form::label('ad_medcubics', 'Medcubics',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                                    {!! Form::radio('role_type', 'Practice',true,['class'=>'','id'=>'ad_practice']) !!} {!! Form::label('ad_practice', 'Practice',['class'=>'med-darkgray font600 form-cursor']) !!}
                                 @else
                                    {!! Form::radio('role_type', 'Medcubics',true,['class'=>'','id'=>'adm_medcubics']) !!} {!! Form::label('adm_medcubics', 'Medcubics',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                                    {!! Form::radio('role_type', 'Practice',null,['class'=>'','id'=>'adm_practice']) !!} {!! Form::label('adm_practice', 'Practice',['class'=>'med-darkgray font600 form-cursor']) !!}
                                 @endif
                            </div>
                        </div>

                     <div class="form-group">
                        {!! Form::label('status', 'Status',  ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-4 control-label']) !!}
                        <div class="col-lg-8col-md-8 col-sm-8 col-xs-8 @if($errors->first('status')) error @endif">
                            {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'r_active']) !!} {!! Form::label('r_active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                            {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'r_inactive']) !!} {!! Form::label('r_inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                            {!! $errors->first('status', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div><!-- /.box-body -->                
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center margin-t-10">
                    {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
                    <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                        @if(strpos($currnet_page, 'edit') !== false)
                            @if($checkpermission->check_adminurl_permission('admin/role/delete/{id}') == 1)
                            <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete this role?" href="{{ url('admin/role/delete/'.$roles->id) }}">Delete</a>
                            @endif
                            
                                @if($roles->role_type == 'Medcubics')
                                    <a href="javascript:void(0)" data-url="{{ url('admin/medcubicsrole/'.$roles->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
						@else
                                    <a href="javascript:void(0)" data-url="{{ url('admin/practicerole/'.$roles->id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                                @endif
						@else
							
                           <a href="javascript:void(0)" data-url="{{ url('admin/medcubicsrole')}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
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

                        role_name:{
                            message:'',
                            validators:{
                                notEmpty:{
                                    message: '{{ trans("admin/role.validation.role") }}'
                                },
								regexp:{
									regexp: /^[A-Za-z ]+$/,
									message: '{{ trans("common.validation.alphaspace") }}'
								}
                            }
                        },
                }
             })
    });
</script>
@endpush
