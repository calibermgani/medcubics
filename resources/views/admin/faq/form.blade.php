<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.faq") }}' />

<div class="col-md-12 col-md-12 col-sm-12 col-xs-12" >

    <div class="box box-info no-shadow">
        <div class="box-block-header margin-b-10">
            <h3 class="box-title"><i class="livicon" data-name="info" data-color='#008e97' data-size='16'></i>General Question</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body form-horizontal margin-l-10">

            <div class="form-group">
                {!! Form::label('question', 'Question', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('question')) error @endif">
                    {!! Form::text('question',null,['class'=>'form-control','maxlength' => '250']) !!}
                    {!! $errors->first('question', '<p> :message</p>')  !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('answer', 'Answer', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('answer')) error @endif">
                    {!! Form::textarea('answer', null,['class'=>'form-control']) !!}
                    {!! $errors->first('answer', '<p> :message</p>')  !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('category', 'Category', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}                                                 
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10">  
                    <?php $faq_category = Config::get('siteconfigs.faq_category'); ?>
                    {!! Form::select('category', array('' => '-- Select --') + (array)$faq_category,  null,['class'=>'select2 form-control  ']) !!}                                     
                </div>                
            </div>
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label']) !!}                                                  
                <div class="col-lg-8 col-md-7 col-sm-8 col-xs-9">  
                    {!! Form::radio('status', 'Active','true',['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red']) !!} Inactive                                       
                </div>                
            </div>
        </div><!-- /.box-body -->


        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}

            <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
            @if(strpos($currnet_page, 'edit') !== false)
            @if($checkpermission->check_adminurl_permission('admin/faq/delete/{id}') == 1)
            <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete?" href="{{ url('admin/faq/delete/'.$faq->id) }}">Delete</a>
            @endif
            <a href="javascript:void(0)" data-url="{{ url('admin/faq/'.$faq->id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
            @else
            <a href="javascript:void(0)" data-url="{{ url('admin/faq')}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
            @endif
        </div>


    </div><!-- /.box -->
</div><!--/.col (left) -->


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
                        question: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/faq.validation.question") }}'
                                },
                            }
                        },
                        status: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/faq.validation.status") }}'
                                },
                            }
                        },
                        answer: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/faq.validation.answer") }}'
                                }
                            }
                        },
                        category: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/faq.validation.categorys") }}'
                                }
                            }
                        }
                    }
                });
    });
</script>
@endpush
