<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.insurance_appeal_addr") }}' />


    
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-20" >
       
        <!-- form start -->
        <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">
            <div class="box box-info no-shadow">
             <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Appeal Address</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
            
            <div class="box-body form-horizontal margin-l-10"> 
                <div class=" js-address-class" id="js-address-appeal-address">

                    {!! Form::hidden('appeal_address_type','insurance',['class'=>'js-address-type']) !!}
                    {!! Form::hidden('appeal_address_type_id',null,['class'=>'js-address-type-id']) !!}
                    {!! Form::hidden('appeal_address_type_category','appeal_address',['class'=>'js-address-type-category']) !!}
                    {!! Form::hidden('appeal_address1',$address_flag['appeal']['address1'],['class'=>'js-address-address1']) !!}
                    {!! Form::hidden('appeal_city',$address_flag['appeal']['city'],['class'=>'js-address-city']) !!}
                    {!! Form::hidden('appeal_state',$address_flag['appeal']['state'],['class'=>'js-address-state']) !!}
                    {!! Form::hidden('appeal_zip5',$address_flag['appeal']['zip5'],['class'=>'js-address-zip5']) !!}
                    {!! Form::hidden('appeal_zip4',$address_flag['appeal']['zip4'],['class'=>'js-address-zip4']) !!}
                    {!! Form::hidden('appeal_is_address_match',$address_flag['appeal']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                    {!! Form::hidden('appeal_error_message',$address_flag['appeal']['error_message'],['class'=>'js-address-error-message']) !!}

                    <div class="form-group">
                        {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-5 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address_1')) error @endif">                                                     
                            {!! Form::text('address_1',null,['id'=>'address_1','class'=>'form-control js-address-check dm-address']) !!}                           
                            {!! $errors->first('address_1', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>  

                    <div class="form-group">
                        {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-5 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">                            
                            {!! Form::text('address_2',null,['id'=>'address_2','class'=>'form-control js-address2-tab dm-address']) !!}                            
                        </div>
                        <div class="col-sm-1"></div>
                    </div> 


                    <div class="form-group">
                        {!! Form::label('City / State', 'City / State', ['class'=>'col-lg-5 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-4 col-md-5 col-sm-5 col-xs-6 @if($errors->first('city')) error @endif">  
                            {!! Form::text('city',null,['class'=>'form-control js-address-check dm-address','id'=>'city']) !!}
                            {!! $errors->first('city', '<p> :message</p>')  !!}
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 @if($errors->first('state')) error @endif"> 
                            {!! Form::text('state',null,['class'=>'form-control js-address-check js-state-tab dm-state','id'=>'state']) !!}
                            {!! $errors->first('state', '<p> :message</p>')  !!}
                        </div>

                    </div>   


                    <div class="form-group">
                        {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-5 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('zipcode5')) error @endif">                             
                            {!! Form::text('zipcode5',null,['class'=>'form-control js-address-check dm-zip5','id'=>'zipcode5']) !!}                                                      
                            {!! $errors->first('zipcode5', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 @if($errors->first('zipcode4')) error @endif">                             
                            {!! Form::text('zipcode4',null,['class'=>'form-control js-address-check dm-zip4','id'=>'zipcode4']) !!}                           
                            {!! $errors->first('zipcode4', '<p> :message</p>')  !!}    
                        </div>

                        <div class="col-md-1 col-sm-2">            
                            <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                            <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['appeal']['is_address_match']); ?>   
                            <?php echo $value; ?>                                
                        </div> 
                    </div>


                    <div class="form-group">
                        {!! Form::label('work phone', 'Work Phone', ['class'=>'col-lg-5 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                                                 
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6  @if($errors->first('phone')) error @endif">
                            {!! Form::text('phone',null,['class'=>'form-control dm-phone']) !!}
                            {!! $errors->first('phone', '<p> :message</p>')  !!}
                        </div>

                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 @if($errors->first('phoneext')) error @endif">                             
                            {!! Form::text('phoneext',null,['class'=>'form-control dm-phone-ext','id'=>'phone-ext']) !!}                           
                        </div>

                        <div class="col-sm-1 col-xs-2"></div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('fax', 'Fax', ['class'=>'col-lg-5 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                                                 
                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('fax')) error @endif ">
                            {!! Form::text('fax',null,['class'=>'form-control dm-phone']) !!}
                            {!! $errors->first('fax', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div> 


                    <div class="form-group">
                        {!! Form::label('email', 'Email', ['class'=>'col-lg-5 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                                                 
                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('email')) error @endif ">
                            {!! Form::text('email',null,['class'=>'form-control']) !!}
                            {!! $errors->first('email', '<p> :message</p>')  !!}
                        </div>

                        <div class="col-sm-1 col-xs-2"></div>
                    </div> 

                    {!! Form::hidden('insurance_id',$insurance->id,['class'=>'form-control','id'=>'insurance_id', 'tabindex'=>'-1']) !!}
                </div>  







            </div><!--/.col (left) -->
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            {!! Form::submit('Save', ['class'=>'btn btn-medcubics']) !!}
            <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
            @if(strpos($currnet_page, 'edit') !== false  && $checkpermission->check_adminurl_permission('admin/insurance/{insuranceid}/insuranceappealaddress/delete/{id}') == 1)

            <a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure you want to delete?" 
               href="{{ url('admin/insurance/'.$insurance->id.'/insuranceappealaddress/delete/'.$appealaddress->id) }}">Delete</a>

            <a href="javascript:void(0)" data-url="{{ url('admin/insurance/'.$insurance->id.'/insuranceappealaddress/'.$appealaddress->id) }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
            @endif

            @if(strpos($currnet_page, 'edit') == false)
            <a href="javascript:void(0)" data-url="{{ url('admin/insurance/'.$insurance->id.'/insuranceappealaddress/') }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>   
            @endif
        </div>
            
        </div>
</div>
        

    <!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->   


@push('view.scripts')
<script type="text/javascript">

    $(document).ready(function () {
        /*
         $('[name="phone"]').on('change', function () {
         $('#js-bootstrap-validator')
         .data('bootstrapValidator')
         .updateStatus('phone', 'NOT_VALIDATED')
         .validateField('phone');
         });
         
         $('[name="fax"]').on('change', function () {
         $('#js-bootstrap-validator')
         .data('bootstrapValidator')
         .updateStatus('fax', 'NOT_VALIDATED')
         .validateField('fax');
         }); 
         */
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
                        address_1: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
										var msg = addressValidation(value, "required");
                                        if (msg != true) {
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
                        address_2: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var msg = addressValidation(value);
                                        if (msg != true) {
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
                        city: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var msg = cityValidation(value, "required");
                                        if (msg != true) {
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
                        state: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var msg = stateValidation(value, "required");
                                        if (msg != true) {
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
                        zipcode5: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var msg = zip5Validation(value, "required");
                                        if (msg != true) {
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
                        zipcode4: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var msg = zip4Validation(value);
                                        if (msg != true) {
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
                        email: {
                            message: '',
                            validators: {
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
                        phone: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator, $field) {
                                        var phone_msg = '{{ trans("common.validation.phone_limit") }}';
                                        var ext_msg = '{{ trans("common.validation.phone") }}';
                                        $fields = validator.getFieldElements('phone');
                                        var ext_length = $fields.closest("div").next().find("input").val().length;
                                        var response = phoneValidation(value, phone_msg, ext_length, ext_msg);
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
                        fax: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var fax_msg = '{{ trans("common.validation.fax_limit") }}';
                                        var response = phoneValidation(value, fax_msg);
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
                    }
                });

    });
</script>
@endpush