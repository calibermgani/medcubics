<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10" >
    <div class="box box-info no-shadow">
        <div class="box-header-view with-border">
            <h3 class="box-title"><i class="fa fa-ticket font16"></i> Post Ticket</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->


        @if($ticket_id != '')
        <div class="box-body form-horizontal margin-t-10 margin-l-10">
            {{ trans("support/ticket.validation.createmsg") }} {{ $ticket_id }}
        </div>	
        @else
        <div class="box-body form-horizontal margin-t-10 margin-l-10">		
            @if(Auth::user ()!='')
            <?php  
				$username = Auth::user ()->name; 
				$user_short_name = Auth::user ()->short_name; 
				$email 	  = Auth::user ()->email; 
			?>		
            @endif
            <div @if(Auth::user ()!='') class="form-group-space" @else class="form-group"  @endif >
                  {!! Form::label('Name', 'Name', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('name')) error @endif">
                    @if(Auth::user ()!='')
                    <span class="font600 med-orange">  <?php	echo $user_short_name; ?></span>
                    {!! Form::hidden('name',$user_short_name,['class'=>'form-control']) !!}
                    @else
                    {!! Form::text('name',null,['class'=>'form-control','maxlength'=>'50']) !!}
                    {!! $errors->first('name', '<p> :message</p>')  !!}	
                    @endif
                </div>						
            </div>
            <div @if(Auth::user ()!='') class="form-group-space" @else class="form-group"  @endif >
                  {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('email_id')) error @endif">

                    @if(Auth::user ()!='')
                    <?php	echo $email; ?>
                    {!! Form::hidden('email_id',$email,['class'=>'form-control']) !!}
                    @else
                    {!! Form::text('email_id',null,['class'=>'form-control']) !!}
                    {!! $errors->first('email_id', '<p> :message</p>')  !!}
                    @endif
                </div>              
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
                {!! Form::label('Attachment', 'Attachment', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
                <div>
                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 fileContainer" @if(@$image_tag != '') style="bottom: 3px;position:relative;left:180px;width:100px no-border" @else style="width:100px; border: none;" @endif>
                         <span class="fileContainer" style="padding:1px 20px; float:left;margin-left: 0px;"> 
                            <input name="ticketfile" id="attachment" type="file" accept="image/png, image/gif, image/jpeg"/> 
                            Upload
                        </span>	
                    </div>
                    <span class="js-display-attachment col-lg-1 col-md-7  col-sm-6 col-xs-10"></span>	 
                </div>               
            </div>

            <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
                {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics']) !!}
                <a href="javascript:void(0)" data-url="{{ url('searchticket')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
            </div>
        </div><!-- /.box-body -->
        @endif
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
					/*  name: {
					 message: '',
					 validators: {
					 notEmpty: {
					 message: '{{ trans("admin/adminuser.validation.name") }}'
					 },
					 regexp: {
					 regexp: /^[a-zA-Z0-9 ]+$/,
					 message: '{{ trans("common.validation.alphanumeric") }}'
					 },
					 stringLength: {
					 message: '{{ trans("admin/adminuser.validation.length") }}',
					 max: function (value, validator, $field) {
					 return 50 - (value.match(/^[a-zA-Z\s]{0,50}$/) || []).length;
					 }
					 }
					 }
					 },*/
					email_id: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("common.validation.email") }}'
							},
							callback: {
								message: '',
								callback: function (value, validator) {
									var response = emailValidation(value);
									if (response != true) {
										return {
											valid: false,
											message: response
										};
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
					ticketfile: {
						message: '',
						validators: {
							file: {
								extension: 'pdf,jpeg,jpg,png,gif,doc,xls,csv,docx,xlsx,txt',
								message: attachment_valid_lang_err_msg
							},
							callback: {
								message: '{{ trans("common.validation.upload_limit") }}',
								callback: function (value, validator) {
									if ($('[name="ticketfile"]').val() != "") {
										var size = parseFloat($('[name="ticketfile"]')[0].files[0].size / 1024).toFixed(2);
										var get_image_size = Math.ceil(size);
										return (get_image_size > filesize_max_defined_length) ? false : true;
									}
									return true;
								}
							}
						}
					}
				}
			});
    });
</script>
@endpush