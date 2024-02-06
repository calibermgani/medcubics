<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" >
    <div class="box box-info no-shadow">
        <div class="box-header-view with-border">
            <h3 class="box-title"><i class="fa fa-ticket font16"></i> Create New Ticket</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-t-10 margin-l-10">
			<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.newticket") }}' />
			
            @if($ticket_id != '')
            {{ trans("support/ticket.validation.createmsg") }} {{ $ticket_id }}
            @else
			
			<div class="form-group">
                {!! Form::label('User Type', 'User Type', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('type')) error @endif">
                    {!! Form::radio('usertype', 'guestuser',true,['class'=>'flat-red']) !!} Guest User &emsp; {!! Form::radio('usertype', 'registereduser',null,['class'=>'flat-red']) !!} Registered User
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
			
			<div class="guestuser">
				<div class="form-group">
					  {!! Form::label('Name', 'Name', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
					  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('name')) error @endif">
						{!! Form::text('name',null,['class'=>'form-control','maxlength'=>'50']) !!}
						{!! $errors->first('name', '<p> :message</p>')  !!}	
					</div>						
				</div>
				<div class="form-group">
					  {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
					  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('email_id')) error @endif">
						{!! Form::text('email_id',null,['class'=>'form-control']) !!}
						{!! $errors->first('email_id', '<p> :message</p>')  !!}
					</div>              
				</div>
			</div>
			
			
			<div class="form-group registereduser hide">
                {!! Form::label('User List', 'User List', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('type')) error @endif">
                     {!! Form::select('userlist_id', array('' => '-- Select --')+(array)$userlist,null,['class'=>'select2 form-control js_select_user_id']) !!} 
                    {!! $errors->first('userlist_id', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
			
            <div class="form-group">
                {!! Form::label('Title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('title')) error @endif">
                    {!! Form::text('title',null,['class'=>'form-control','maxlength'=>'50']) !!}
                    {!! $errors->first('title', '<p> :message</p>')  !!}
                </div>                
            </div>

            <div class="form-group">
                {!! Form::label('Description', 'Description', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('description')) error @endif">
                    {!! Form::textarea('description',null,['class'=>'form-control']) !!}
                    {!! $errors->first('description', '<p> :message</p>')  !!}
                </div>                
            </div>
			
			<div class="form-group">
                {!! Form::label('Assign To', 'Assign To', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('type')) error @endif">
                     {!! Form::select('assigneduser_id', array('' => '-- Select --')+(array)$assigneduserlist,null,['class'=>'select2 form-control js_select_user_id']) !!} 
                    {!! $errors->first('assigneduser_id', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                {!! Form::label('Attachment', 'Attachment', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="fileupload fileupload-new" data-provides="fileupload">

                    <span class="col-lg-3 col-md-4 col-sm-6 col-xs-10 fileContainer" @if(@$image_tag != '') style="bottom: 3px;position:relative;left:180px;width:100px" @else style="width:100px;" @endif>
                          <input name="attachmentfield" id="attachment" type="file" 'accept'='image/png, image/gif, image/jpeg' /> Upload
                    </span>
                    <span class="js-display-attachment col-lg-1 col-md-7  col-sm-6 col-xs-10"></span>	 
                </div>               
            </div> 
        </div><!-- /.box-body -->
		
		<div class="box-footer">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
				 <a href="javascript:void(0)" data-url="{{ url('admin/managemyticket')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			</div>
		</div>			

        @endif
    </div><!-- /.box -->
</div><!--/.col (left) -->

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#js-newticket-validator')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    excluded: ':disabled',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        name: {
                            message: '',
                            validators: {
								 regexp: {
                                    regexp: /^[a-zA-Z0-9 ]+$/,
                                    message: '{{ trans("common.validation.alphanumeric") }}'
                                },
								stringLength: {
                                    message: '{{ trans("admin/adminuser.validation.length") }}',
                                    max: function (value, validator, $field) {
                                        return 50 - (value.match(/^[a-zA-Z\s]{0,50}$/) || []).length;
                                    }
                                },
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        if ($('[name="usertype"]:checked').val() == "guestuser") {
											if(value == ''){
												return {
                                                valid: false,
                                                message: '{{ trans("admin/adminuser.validation.name") }}'
												};
											}
										}
										return true;
                                    }
                                }
                            }
                        },
                        email_id: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
										if ($('[name="usertype"]:checked').val() == "guestuser") {
											var response = emailValidation(value);
											if(value == ''){
												return {
													valid: false,
													message: '{{ trans("common.validation.email") }}'
												};
											}
											else if(response != true) {
												return {
													valid: false,
													message: response
												};
											}
											else{
												return true;
											}
										}
										return true;
                                    }
                                }
                            }
                        },
						userlist_id: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        if ($('[name="usertype"]:checked').val() == "registereduser") {
											if(value == ''){
												return {
                                                valid: false,
                                                message: '{{ trans("support/ticket.validation.selectuser") }}'
												};
											}
										}
										return true;
                                    }
                                }
                            }
                        },
                        title: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("common.validation.title") }}'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9 ]+$/,
                                    message: '{{ trans("common.validation.alphanumeric") }}'
                                }
                            }
                        },
                        description: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("common.validation.description") }}'
                                }
                            }
                        },
                        attachmentfield: {
                            message: '',
                            validators: {
                                file: {
                                    extension: 'pdf,jpeg,jpg,png,gif,doc,xls,csv,docx,xlsx,txt',
                                    message: attachment_valid_lang_err_msg
                                },
                                callback: {
                                    message: '{{ trans("common.validation.upload_limit") }}',
                                    callback: function (value, validator) {
                                        if ($('[name="attachmentfield"]').val() != "") {
                                            var size = parseFloat($('[name="attachmentfield"]')[0].files[0].size / 1024).toFixed(2);
                                            var get_image_size = Math.ceil(size);
                                            return (get_image_size > filesize_max_defined_length) ? false : true;
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
						assigneduser_id: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("support/ticket.validation.selectuser") }}'
                                }
                            }
                        }
                    }
                });
    });
</script>
@endpush
