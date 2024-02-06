{!! Form::open(['onsubmit'=>"event.preventDefault();",'name'=>'v2-contacteditform_'.@$contact->id,'id'=>'v2-contacteditform_'.@$contact->id,'class'=>'v2-contact-info-form medcubicsform js-v2-common-info-form js-contact-edit-v2']) !!}

{!! Form::hidden('edit_contact_category_v2-'.$contact->id,$contact->category,['id'=>'edit_contact_category_v2-'.$contact->id]) !!}
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.contact") }}' />


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-border no-shadow p-l-2 p-r-2 bg-white">

    @if($contact->category == 'Guarantor')
    <div class="col-lg-12 margin-t-10 no-padding"  style="border-bottom: 2px solid #f0f0f0;">
        <div class="box-body form-horizontal">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">                                                                

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                       
                        <div class="col-lg-5 no-padding">
                            <h4 class="med-darkgray margin-t-5"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> {{ $contact->category }} {{$guarantor_count_v2}}</h4>
                        </div>
                        <div class="col-lg-6 no-padding">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 text-center margin-l-m-20" id="v2-contacteditffooter_{{@$contact->id}}">
                                <a class="margin-r-5 edit-contact-form-model form-cursor" id="edit-contact-form-model" data-id='{{@$contact->id}}' data-category-type='{{$contact->category}}'><i class="fa fa-edit"></i></a>
                                <a class=" js-v2-delete-contact margin-l-5" data-id='{{@$contact->id}}'> <i class="fa fa-trash form-cursor"></i> </a>                                
                            </div>
                        </div>
                        
                        
                        <div class="box-body form-horizontal no-padding margin-b-15 margin-t-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">


                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
                                    <input type="hidden" value="nooo" name="emergency_last_name" maxlength="50">
                                    <input type="hidden" value="nooo" name="emergency_first_name" maxlength="50">
                                    <input type="hidden" value="" name="emergency_middle_name">
                                    <input type="hidden" value="" name="emergency_relationship">
                                    <input type="hidden" value="" name="emergency_home_phone" maxlength="14">
                                    <input type="hidden" value="" name="emergency_cell_phone" maxlength="14">
                                    <input type="hidden" value="" name="emergency_email">
                                    <input type="hidden" value="" name="emergency_address1" maxlength="50">
                                    <input type="hidden" value="" name="emergency_address2" maxlength="50">
                                    <input type="hidden" value="" name="emergency_city" maxlength="50">
                                    <input type="hidden" value="" name="emergency_state" maxlength="2">
                                    <input type="hidden" value="" name="emergency_zip5" maxlength="5">
                                    <input type="hidden" value="" name="emergency_zip4" maxlength="4">
                                    <input type="hidden" value="nooo" name="employer_name">
                                    <input type="hidden" value="nooo" name="employer_status" id="employer_status-{{@$contact->id}}">
                                    <input type="hidden" value="nooo" name="employer_organization_name" maxlength="50" id="employer_organization_name-{{@$contact->id}}">
                                    <input type="hidden" value="nooo" name="employer_occupation" maxlength="50" id="employer_occupation-{{@$contact->id}}">
                                    <input type="hidden" value="nooo" name="employer_student_status" id="employer_student_status-{{@$contact->id}}">
                                    <input type="hidden" value="" name="employer_work_phone" maxlength="14">
                                    <input type="hidden" value="" name="employer_phone_ext" maxlength="4">
                                    <input type="hidden" value="" name="employer_address1" maxlength="50">
                                    <input type="hidden" value="" name="employer_address2" maxlength="50">
                                    <input type="hidden" value="" name="employer_city" maxlength="50">
                                    <input type="hidden" value="" name="employer_state" maxlength="2">
                                    <input type="hidden" value="" name="employer_zip5" maxlength="5">
                                    <input type="hidden" value="" name="employer_zip4" maxlength="4">
                                    <input type="hidden" value="nooo" name="attorney_adjuster_name" maxlength="50">
                                    <input type="hidden" value="" name="attorney_doi">
                                    <input type="hidden" value="" name="attorney_claim_num" maxlength="15">
                                    <input type="hidden" value="" name="attorney_work_phone" maxlength="14">
                                    <input type="hidden" value="" name="attorney_phone_ext" maxlength="4">
                                    <input type="hidden" value="" name="attorney_fax" maxlength="14">
                                    <input type="hidden" value="" name="attorney_email">
                                    <input type="hidden" value="" name="attorney_address1" maxlength="50">
                                    <input type="hidden" value="" name="attorney_address2" maxlength="50">
                                    <input type="hidden" value="" name="attorney_city" maxlength="50">
                                    <input type="hidden" value="" name="attorney_state" maxlength="2">
                                    <input type="hidden" value="" name="attorney_zip5" maxlength="5">
                                    <input type="hidden" value="" name="attorney_zip4" maxlength="4">

                                    <div id="add-form-value">
                                        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12  form-horizontal"><!-- Left side Content Starts -->

                                            <div class="form-group bottom-space-10">
                                                {!! Form::label('Relationship', 'Relationship', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-9">
                                                    <?php
														$s = $contact->guarantor_relationship;
														$e = preg_split('/(?=[A-Z])/', $s, -1, PREG_SPLIT_NO_EMPTY);
														$count = count((array)$e);   
                                                    ?>
                                                    <p class="show-border no-bottom" ><?php if($count < 2){echo @$e[0];}else{echo @$e[0]." ".@$e[1];}?></p>
                                                    <input type="hidden" id="guarantor_realationship" value="{{$contact->guarantor_relationship}}">
                                                </div>                    
                                            </div>                                            

                                            <div class="form-group bottom-space-10">
                                                {!! Form::label('last Name', 'Last Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-9">
                                                    <p class="show-border no-bottom" id="guarantor_last_name">{{@$contact->guarantor_last_name}}</p>
                                                </div>                    
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                                <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6">
                                                    <p class="show-border no-bottom" id="guarantor_first_name">{{@$contact->guarantor_first_name}}</p>                                                  
                                                </div>
                                                {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                                <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">
                                                    <p class="show-border no-bottom" id="guarantor_middle_name">{{@$contact->guarantor_middle_name}}</p>                                                     
                                                </div>
                                            </div>                                                          
                                        </div><!-- Left side content Ends-->



                                    </div>

                                    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 form-horizontal js-address-class" id="js-address-general-address_{{@$contact->id}}"><!-- Right side Content Starts -->   

                                        <?php
                                        $address_flag['general'] = App\Models\AddressFlag::getAddressFlag('patients', @$contact->id, 'patient_contact_address');
                                        ?>

                                        {!! Form::hidden('guarantor_general_address_type','patients',['class'=>'js-address-type']) !!}
                                        {!! Form::hidden('guarantor_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                        {!! Form::hidden('guarantor_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                        {!! Form::hidden('guarantor_general_address1',@$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                                        {!! Form::hidden('guarantor_general_city',@$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                                        {!! Form::hidden('guarantor_general_state',@$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                                        {!! Form::hidden('guarantor_general_zip5',@$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                                        {!! Form::hidden('guarantor_general_zip4',@$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                                        {!! Form::hidden('guarantor_general_is_address_match',@$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                                        {!! Form::hidden('guarantor_general_error_message',@$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}
                                    
                                        <div class="form-group">
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label"></div>
                                            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                              {!! Form::checkbox('same_as_patient_address', null, (@$contact->same_patient_address=='yes'?true:null), ['class'=>" med-green",'id'=>'sameaddress-gua-contact']) !!} <label for="sameaddress-gua-contact" style="display:none;" class="med-orange font600">Same as patient address</label>
                                            </div>
                                            @if($contact->same_patient_address=='yes')
                                            <input type="hidden" name="edit-sameaddress-insurance" id="edit-sameaddress-insurance" value="yes"> 
                                            @endif
                                        </div>
                                      
                                        <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
                                                <p class="show-border no-bottom" id="guarantor_address1">{{@$contact->guarantor_address1}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div> 

                                        <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('address2')) error @endif">                            
                                                <p class="show-border no-bottom" id="guarantor_address2">{{@$contact->guarantor_address2}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div> 


                                        <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                            <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6">  
                                                <p class="show-border no-bottom" id="guarantor_city">{{@$contact->guarantor_city}}</p>                                                
                                            </div>
                                            {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                            <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0"> 
                                                <p class="show-border no-bottom" id="guarantor_state">{{@$contact->guarantor_state}}</p>                                                
                                            </div>
                                        </div>                                                                   

                                        <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">                             
                                                <p class="show-border no-bottom" id="guarantor_zip5">{{@$contact->guarantor_zip5}}</p>                                                
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">                             
                                                <p class="show-border no-bottom" id="guarantor_zip4">{{@$contact->guarantor_zip4}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2">            
                                                <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                               
                                            </div> 
                                            <div class="col-md-1 col-sm-1 col-xs-2">            
                                            </div> 
                                        </div>
                                        <div class="form-group self-address @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('Home Phone', 'Home Phone', ['class'=>' col-lg-3 col-md-2 col-sm-3 col-xs-12 control-label']) !!}                                                  
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">  
                                                <p class="show-border no-bottom" id="guarantor_home_phone">{{@$contact->guarantor_home_phone}}</p>                                                
                                            </div>                  
                                        </div> 
                                        <div class="form-group self-address @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                <p class="show-border no-bottom" id="guarantor_cell_phone">{{@$contact->guarantor_cell_phone}}</p>                                                
                                            </div>
                                        </div>
                                        <div class="form-group self-address @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                                <p class="show-border no-bottom" id="guarantor_email">{{@$contact->guarantor_email}}</p>                                                
                                            </div>                 
                                        </div>

                                    </div><!-- Right side Content Ends --> 
                                </div>



                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">

                            </div>

                        </div><!-- /.box-body -->   
                    </div>                                
                </div>
            </div>
        </div>
    </div>
    @elseif($contact->category == "Emergency Contact")


    <div class="col-lg-12 margin-t-10 no-padding"  style="border-bottom: 2px solid #f0f0f0;">
        <div class="box-body form-horizontal">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">                                                                

                    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 table-responsive ">
                        
                        <div class="col-lg-6 no-padding">
                            <h4 class="med-darkgray margin-t-5"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> {{ $contact->category }} {{$emergency_count_v2}}</h4>
                        </div>
                        <div class="col-lg-6 no-padding">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 text-center" id="v2-contacteditffooter_{{@$contact->id}}">
                                <a class="margin-r-5 edit-contact-form-model form-cursor" id="edit-contact-form-model" data-id='{{@$contact->id}}' data-category-type='{{$contact->category}}'><i class="fa fa-edit"></i></a>
                                <a class=" js-v2-delete-contact margin-l-5" data-id='{{@$contact->id}}'> <i class="fa fa-trash form-cursor"></i> </a>                                
                            </div>
                        </div>
                        
                        
                        <div class="box-body form-horizontal no-padding margin-b-15 margin-t-20">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <input type="hidden" value="nooo" name="guarantor_last_name" maxlength="50">
                                <input type="hidden" value="nooo" name="guarantor_first_name" maxlength="50">
                                <input type="hidden" value="" name="guarantor_middle_name">
                                <input type="hidden" value="" name="guarantor_relationship">
                                <input type="hidden" value="" name="guarantor_home_phone" maxlength="14">
                                <input type="hidden" value="" name="guarantor_cell_phone" maxlength="14">
                                <input type="hidden" value="" name="guarantor_email">
                                <input type="hidden" value="" name="guarantor_address1" maxlength="50">
                                <input type="hidden" value="" name="guarantor_address2" maxlength="50">
                                <input type="hidden" value="" name="guarantor_city" maxlength="50">
                                <input type="hidden" value="" name="guarantor_state" maxlength="2">
                                <input type="hidden" value="" name="guarantor_zip5" maxlength="5">
                                <input type="hidden" value="" name="guarantor_zip4" maxlength="4">
                                <input type="hidden" value="nooo" name="employer_name">
                                <input type="hidden" value="nooo" name="employer_status" id="employer_status-{{@$contact->id}}">
                                <input type="hidden" value="nooo" name="employer_organization_name" maxlength="50" id="employer_organization_name-{{@$contact->id}}">
                                <input type="hidden" value="nooo" name="employer_occupation" maxlength="50" id="employer_occupation-{{@$contact->id}}">
                                <input type="hidden" value="nooo" name="employer_student_status" id="employer_student_status-{{@$contact->id}}">
                                <input type="hidden" value="" name="employer_work_phone" maxlength="14">
                                <input type="hidden" value="" name="employer_phone_ext" maxlength="4">
                                <input type="hidden" value="" name="employer_address1" maxlength="50">
                                <input type="hidden" value="" name="employer_address2" maxlength="50">
                                <input type="hidden" value="" name="employer_city" maxlength="50">
                                <input type="hidden" value="" name="employer_state" maxlength="2">
                                <input type="hidden" value="" name="employer_zip5" maxlength="5">
                                <input type="hidden" value="" name="employer_zip4" maxlength="4">
                                <input type="hidden" value="nooo" name="attorney_adjuster_name" maxlength="50">
                                <input type="hidden" value="" name="attorney_doi">
                                <input type="hidden" value="" name="attorney_claim_num" maxlength="15">
                                <input type="hidden" value="" name="attorney_work_phone" maxlength="14">
                                <input type="hidden" value="" name="attorney_phone_ext" maxlength="4">
                                <input type="hidden" value="" name="attorney_fax" maxlength="14">
                                <input type="hidden" value="" name="attorney_email">
                                <input type="hidden" value="" name="attorney_address1" maxlength="50">
                                <input type="hidden" value="" name="attorney_address2" maxlength="50">
                                <input type="hidden" value="" name="attorney_city" maxlength="50">
                                <input type="hidden" value="" name="attorney_state" maxlength="2">
                                <input type="hidden" value="" name="attorney_zip5" maxlength="5">
                                <input type="hidden" value="" name="attorney_zip4" maxlength="4">

                                <div id="add-form-value">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  form-horizontal margin-t-8"><!-- Left side Content Starts -->

                                        <div class="form-group">
                                            {!! Form::label('LastName', 'Last Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                                <p class="show-border no-bottom" id="emergency_last_name">{{@$contact->emergency_last_name}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                            <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6">
                                                <p class="show-border no-bottom" id="emergency_first_name">{{@$contact->emergency_first_name}}</p>                                                
                                            </div>
                                            {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                            <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">
                                                <p class="show-border no-bottom" id="emergency_middle_name">{{@$contact->emergency_middle_name}}</p>                                                
                                            </div>
                                        </div>                                                          

                                        <div class="form-group">
                                            {!! Form::label('emergency_relationship', 'Relationship', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                                <?php
                                                    $string = $contact->emergency_relationship;
                                                    $emer = preg_split('/(?=[A-Z])/', $string, -1, PREG_SPLIT_NO_EMPTY);
                                                    $counts = count((array)$emer);
                                                ?>
                                                <p class="show-border no-bottom" ><?php if($counts < 2){echo @$emer[0];}else{ echo @$emer[0]." ".@$emer[1];} ?></p>
                                                <input type="hidden" id="emergency_relationship" value="{{$contact->emergency_relationship}}">                                                 
                                            </div>
                                        </div>



                                    </div><!-- Left side content Ends-->
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal js-address-class" id="js-address-general-address_{{@$contact->id}}"><!-- Right side Content Starts -->   

                                    <?php
                                    $address_flag['general'] = App\Models\AddressFlag::getAddressFlag('patients', @$contact->id, 'patient_contact_address');
                                    ?>

                                    {!! Form::hidden('emergency_contact_general_address_type','patients',['class'=>'js-address-type']) !!}
                                    {!! Form::hidden('emergency_contact_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                    {!! Form::hidden('emergency_contact_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                    {!! Form::hidden('emergency_contact_general_address1',@$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                                    {!! Form::hidden('emergency_contact_general_city',@$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                                    {!! Form::hidden('emergency_contact_general_state',@$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                                    {!! Form::hidden('emergency_contact_general_zip5',@$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                                    {!! Form::hidden('emergency_contact_general_zip4',@$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                                    {!! Form::hidden('emergency_contact_general_is_address_match',@$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                                    {!! Form::hidden('emergency_contact_general_error_message',@$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}
                             
                                    <div class="form-group margin-b-10">
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label"></div>
                                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                             {!! Form::checkbox('same_as_patient_address', null, (@$contact->same_patient_address=='yes'?true:null), ['class'=>" med-green js-same_as_patient_address-v2",'id'=>'sameaddress-emergency-contact']) !!} <label for="sameaddress-emergency-contact" style="display:none;" class="med-orange font600">Same as patient address</label>
                                             @if($contact->same_patient_address =='yes')
                                             <input type="hidden" name="emergency-sameaddress-insurance" id="emergency-sameaddress-insurance" value="yes">
                                             @endif  
                                        </div>
                                    </div>
                                 
                                    <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                        {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                          <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
                                              <p class="show-border no-bottom" id="emergency_address1">{{@$contact->emergency_address1}}</p>
                                        </div>
                                       <!-- <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
                                            {!! Form::text('emergency_address1',@$contact->emergency_address1,['maxlength'=>'50','id'=>'emergency_address1','class'=>'form-control js-address-check js-v2-address1']) !!}    
                                        </div>-->
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div> 

                                    <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                        {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('address2')) error @endif">                            
                                     <p class="show-border no-bottom" id="emergency_address2">{{@$contact->emergency_address2}}</p>                          
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div> 

                                    <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                        {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6"> 
                                               <p class="show-border no-bottom" id="emergency_city">{{@$contact->emergency_city}}</p>                                    
                                        </div>
                                        {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                        <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">
                                           <p class="show-border no-bottom" id="emergency_state">
                                            {{@$contact->emergency_state}}</p>                                  
                                        </div>
                                    </div>

                                    <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                        {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6"> 
                                           <p class="show-border no-bottom" id="emergency_zip5">
                                            {{@$contact->emergency_zip5}}</p>                                  
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">                             
                                               <p class="show-border no-bottom" id="emergency_zip4">
                                                 {{@$contact->emergency_zip4}}</p>                                  
                                        </div>                         
                                       
                                        <div class="col-md-1 col-sm-1 col-xs-2">            
                                            <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>                                           
                                        </div>                                        
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div> 
                                    </div>

                                    <div class="form-group self-address">
                                        {!! Form::label('Home Phone', 'Home Phone', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}                                                  
                                        <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                            <p class="show-border no-bottom" id="emergency_home_phone">{{@$contact->emergency_home_phone}}</p>                                            
                                        </div>

                                    </div> 
                                    <div class="form-group self-address">
                                        {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                            <p class="show-border no-bottom" id="emergency_cell_phone">{{@$contact->emergency_cell_phone}}</p>                                            
                                        </div>
                                    </div>
                                    <div class="form-group self-address">
                                        {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                            <p class="show-border no-bottom" id="emergency_email">{{@$contact->emergency_email}}</p>                                            
                                        </div>                 
                                    </div>
                                </div><!-- Right side Content Ends --> 
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">

                            </div>                            
                        </div><!-- /.box-body -->   
                    </div>                                
                </div>
            </div>
        </div>
    </div>

    @elseif($contact->category == "Employer")

    <div class="col-lg-12 margin-t-10 no-padding js-address-employer"  style="border-bottom: 2px solid #f0f0f0;">
        <div class="box-body form-horizontal">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 no-padding ">                                                                

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                        <?php
                            $status = ($employer_count_v2 > 1) ? "Previous Employer" : "Current Employer";
                        ?>
                        <div class="col-lg-6 no-padding">
                            <h4 class="med-darkgray margin-t-5 margin-b-15"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> {{$status}}</h4>
                        </div>
                        <div class="col-lg-6 no-padding">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 text-center" id="v2-contacteditffooter_{{@$contact->id}}">
                                <a class="margin-r-5 edit-contact-form-model form-cursor" id="edit-contact-form-model" data-id='{{@$contact->id}}' data-category-type='{{$contact->category}}'><i class="fa fa-edit"></i></a>
                                <a class="js-v2-delete-contact margin-l-10" data-id='{{@$contact->id}}'> <i class="fa fa-trash form-cursor"></i> </a>                                
                            </div>
                        </div>
                        
                        <div class="box-body form-horizontal no-padding margin-b-15 margin-t-20">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <input type="hidden" value="nooo" name="guarantor_last_name" maxlength="50">
                                <input type="hidden" value="nooo" name="guarantor_first_name" maxlength="50">
                                <input type="hidden" value="" name="guarantor_middle_name">
                                <input type="hidden" value="" name="guarantor_relationship">
                                <input type="hidden" value="" name="guarantor_home_phone" maxlength="14">
                                <input type="hidden" value="" name="guarantor_cell_phone" maxlength="14">
                                <input type="hidden" value="" name="guarantor_email">
                                <input type="hidden" value="" name="guarantor_address1" maxlength="50">
                                <input type="hidden" value="" name="guarantor_address2" maxlength="50">
                                <input type="hidden" value="" name="guarantor_city" maxlength="50">
                                <input type="hidden" value="" name="guarantor_state" maxlength="2">
                                <input type="hidden" value="" name="guarantor_zip5" maxlength="5">
                                <input type="hidden" value="" name="guarantor_zip4" maxlength="4">
                                <input type="hidden" value="nooo" name="emergency_last_name" maxlength="50">
                                <input type="hidden" value="nooo" name="emergency_first_name" maxlength="50">
                                <input type="hidden" value="" name="emergency_middle_name">
                                <input type="hidden" value="" name="emergency_relationship">
                                <input type="hidden" value="" name="emergency_home_phone" maxlength="14">
                                <input type="hidden" value="" name="emergency_cell_phone" maxlength="14">
                                <input type="hidden" value="" name="emergency_email">
                                <input type="hidden" value="" name="emergency_address1" maxlength="50">
                                <input type="hidden" value="" name="emergency_address2" maxlength="50">
                                <input type="hidden" value="" name="emergency_city" maxlength="50">
                                <input type="hidden" value="" name="emergency_state" maxlength="2">
                                <input type="hidden" value="" name="emergency_zip5" maxlength="5">
                                <input type="hidden" value="" name="emergency_zip4" maxlength="4">
                                <input type="hidden" value="nooo" name="attorney_adjuster_name" maxlength="50">
                                <input type="hidden" value="" name="attorney_doi">
                                <input type="hidden" value="" name="attorney_claim_num" maxlength="15">
                                <input type="hidden" value="" name="attorney_work_phone" maxlength="14">
                                <input type="hidden" value="" name="attorney_phone_ext" maxlength="4">
                                <input type="hidden" value="" name="attorney_fax" maxlength="14">
                                <input type="hidden" value="" name="attorney_email">
                                <input type="hidden" value="" name="attorney_address1" maxlength="50">
                                <input type="hidden" value="" name="attorney_address2" maxlength="50">
                                <input type="hidden" value="" name="attorney_city" maxlength="50">
                                <input type="hidden" value="" name="attorney_state" maxlength="2">
                                <input type="hidden" value="" name="attorney_zip5" maxlength="5">
                                <input type="hidden" value="" name="attorney_zip4" maxlength="4">

                                <div id="add-form-value">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  form-horizontal margin-t-8"><!-- Left side Content Starts -->

                                        <div class="form-group">
                                            {!! Form::label('employer_status', 'Employment Status', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                                <p class="show-border no-bottom" id="employer_status">{{@$contact->employer_status}}</p>                                                 
                                            </div>
                                        </div>

                                        <!-- start employed added fields -->
                                        <span id="employer_option_sub_field-{{@$contact->id}}" class="employer_option_sub_field-{{@$contact->id}} @if(@$contact->employer_status!='Employed' && @$contact->employer_status!='Self Employed' && @$contact->employer_status!='Employed(Part Time)') hide @endif">
                                            <div class="form-group">
                                                {!! Form::label('EmployerName_label', 'Employer Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10"> 
                                                    <p class="show-border no-bottom" id="employer_name">{{@$contact->employer_name}}</p>                                                    
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('occupation_label', 'Occupation', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('medical_chart_no')) error @endif">
                                                    <p class="show-border no-bottom" id="employer_occupation" >{{@$contact->employer_occupation}}</p>                                                    
                                                </div>
                                                <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                            </div>
                                        </span>
                                        <!-- end employed added fields -->

                                        <!-- start student added fields -->
                                        <span id="student_option_sub_field-{{@$contact->id}}" class="student_option_sub_field-{{@$contact->id}} @if(@$contact->employer_status!='Student') hide @endif">
                                            <div class="form-group">
                                                {!! Form::label('student_status', 'Student Status', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">  
                                                    <?php @$contact->employer_student_status = (@$contact->employer_student_status!='')? @$contact->employer_student_status:'Unknown' ?>
                                                    <p class="show-border no-bottom" id="employer_student_status">{{@$contact->employer_student_status}}</p>                                                    
                                                </div>                
                                            </div>
                                        </span>
                                        <!-- end student added fields -->


                                    </div><!-- Left side content Ends-->
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal js-address-class" id="js-address-general-address_{{@$contact->id}}"><!-- Right side Content Starts -->   

                                    <?php
                                    $address_flag['general'] = App\Models\AddressFlag::getAddressFlag('patients', @$contact->id, 'patient_contact_address');
                                    ?>

                                    {!! Form::hidden('employer_general_address_type','patients',['class'=>'js-address-type']) !!}
                                    {!! Form::hidden('employer_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                    {!! Form::hidden('employer_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                    {!! Form::hidden('employer_general_address1',@$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                                    {!! Form::hidden('employer_general_city',@$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                                    {!! Form::hidden('employer_general_state',@$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                                    {!! Form::hidden('employer_general_zip5',@$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                                    {!! Form::hidden('employer_general_zip4',@$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                                    {!! Form::hidden('employer_general_is_address_match',@$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                                    {!! Form::hidden('employer_general_error_message',@$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}

                                    <!-- <div class="form-group">
                                        <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
                                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                            {!! Form::checkbox(' ', null, null, ['class'=>"js-same_as_patient_address-v2 flat-red med-green",'id'=>'sameaddress-insurance']) !!} &nbsp; <span class="med-green font600">Same as patient address</span> 
                                        </div>
                                    </div> -->
                                    <div  id = "employer-retired-field-{{@$contact->id}}" class = "js_emp_addr_empty employer-retired-field-{{@$contact->id}} @if(@$contact->employer_status == 'Retired') hide @else sample @endif">
                                        <div class="form-group">
                                            {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 ">
                                                <p class="show-border no-bottom" id="employer_address1">{{@$contact->employer_address1}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div> 

                                        <div class="form-group">
                                            {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 ">                            
                                                <p class="show-border no-bottom" id="employer_address2">{{@$contact->employer_address2}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                            <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6">  
                                                <p class="show-border no-bottom" id="employer_city">{{@$contact->employer_city}}</p>                                                
                                            </div>
                                            {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                            <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">  
                                                <p class="show-border no-bottom" id="employer_state">{{@$contact->employer_state}}</p>                                                
                                            </div>
                                        </div>   
                                        <div class="form-group">
                                            {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">                             
                                                <p class="show-border no-bottom" id="employer_zip5">{{@$contact->employer_zip5}}</p>                                                
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">                             
                                                <p class="show-border no-bottom" id="employer_zip4">{{@$contact->employer_zip4}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2">            
                                                <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                                
                                            </div> 
                                            <div class="col-md-1 col-sm-1 col-xs-2">            
                                            </div> 
                                        </div>
                                        <div class="form-group has-feedback">
                                            {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">  
                                                <p class="show-border no-bottom" id="employer_work_phone" >{{@$contact->employer_work_phone}}</p>                                                
                                            </div>
                                            {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2 p-l-0"> 
                                                <p class="show-border no-bottom" id="employer_phone_ext" >{{@$contact->employer_phone_ext}}</p>                                                
                                            </div>
                                        </div>
                                    </div>

                                </div><!-- Right side Content Ends --> 

                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">

                            </div>

                        </div><!-- /.box-body -->   
                    </div>                                
                </div>
            </div>
        </div>
    </div>

    @elseif($contact->category == "Attorney")

    <div class="col-lg-12 margin-t-10 no-padding"  style="border-bottom: 2px solid #f0f0f0;">
        <div class="box-body form-horizontal">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">                                                                

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">

                        <h4 class="med-darkgray margin-t-5"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> {{ $contact->category }}</h4>
                        <div class="box-body form-horizontal no-padding margin-b-15 margin-t-10">

                            <input type="hidden" value="nooo" name="guarantor_last_name" maxlength="50">
                            <input type="hidden" value="nooo" name="guarantor_first_name" maxlength="50">
                            <input type="hidden" value="" name="guarantor_middle_name">
                            <input type="hidden" value="" name="guarantor_relationship">
                            <input type="hidden" value="" name="guarantor_home_phone" maxlength="14">
                            <input type="hidden" value="" name="guarantor_cell_phone" maxlength="14">
                            <input type="hidden" value="" name="guarantor_email">
                            <input type="hidden" value="" name="guarantor_address1" maxlength="50">
                            <input type="hidden" value="" name="guarantor_address2" maxlength="50">
                            <input type="hidden" value="" name="guarantor_city" maxlength="50">
                            <input type="hidden" value="" name="guarantor_state" maxlength="2">
                            <input type="hidden" value="" name="guarantor_zip5" maxlength="5">
                            <input type="hidden" value="" name="guarantor_zip4" maxlength="4">
                            <input type="hidden" value="nooo" name="emergency_last_name" maxlength="50">
                            <input type="hidden" value="nooo" name="emergency_first_name" maxlength="50">
                            <input type="hidden" value="" name="emergency_middle_name">
                            <input type="hidden" value="" name="emergency_relationship">
                            <input type="hidden" value="" name="emergency_home_phone" maxlength="14">
                            <input type="hidden" value="" name="emergency_cell_phone" maxlength="14">
                            <input type="hidden" value="" name="emergency_email">
                            <input type="hidden" value="" name="emergency_address1" maxlength="50">
                            <input type="hidden" value="" name="emergency_address2" maxlength="50">
                            <input type="hidden" value="" name="emergency_city" maxlength="50">
                            <input type="hidden" value="" name="emergency_state" maxlength="2">
                            <input type="hidden" value="" name="emergency_zip5" maxlength="5">
                            <input type="hidden" value="" name="emergency_zip4" maxlength="4">
                            <input type="hidden" value="nooo" name="employer_name">
                            <input type="hidden" value="nooo" name="employer_status" id="employer_status-{{@$contact->id}}">
                            <input type="hidden" value="nooo" name="employer_organization_name" maxlength="50" id="employer_organization_name-{{@$contact->id}}">
                            <input type="hidden" value="nooo" name="employer_occupation" maxlength="50" id="employer_occupation-{{@$contact->id}}">
                            <input type="hidden" value="nooo" name="employer_student_status" id="employer_student_status-{{@$contact->id}}">
                            <input type="hidden" value="" name="employer_work_phone" maxlength="14">
                            <input type="hidden" value="" name="employer_phone_ext" maxlength="4">
                            <input type="hidden" value="" name="employer_address1" maxlength="50">
                            <input type="hidden" value="" name="employer_address2" maxlength="50">
                            <input type="hidden" value="" name="employer_city" maxlength="50">
                            <input type="hidden" value="" name="employer_state" maxlength="2">
                            <input type="hidden" value="" name="employer_zip5" maxlength="5">
                            <input type="hidden" value="" name="employer_zip4" maxlength="4">

                            <div id="add-form-value">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  form-horizontal"><!-- Left side Content Starts -->

                                    <div class="form-group">
                                        {!! Form::label('Adjustor Name', 'Adjustor Name', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                  
                                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10"> 
                                            {!! Form::text('attorney_adjuster_name',@$contact->attorney_adjuster_name,['class'=>'form-control js-letters-caps-format','maxlength'=>'50']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('attorney_doi', 'DOI', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                           
                                        <div class="col-lg-4 col-md-7 col-sm-4 col-xs-10">
                                            <i class="fa fa-calendar-o form-icon"></i>          
                                            {!! Form::text('attorney_doi',App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$contact->attorney_doi),['class'=>'form-control form-cursor doi dm-date','placeholder'=>'mm/dd/yyyy','id'=>'attorney_doi']) !!}
                                        </div>
                                        <div class="col-sm-1 col-xs-2"></div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('Claim Number', 'Claim Number', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                  
                                        <div class="col-lg-4 col-md-7 col-sm-4 col-xs-10"> 
                                            {!! Form::text('attorney_claim_num',@$contact->attorney_claim_num,['class'=>'form-control','maxlength'=>'15']) !!}
                                        </div>
                                    </div>      
                                    <div class="form-group">
                                        {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                  
                                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6"> 
                                            {!! Form::text('attorney_work_phone',@$contact->attorney_work_phone,['class'=>'form-control js-number dm-phone p-r-0','maxlength'=>'14']) !!}
                                        </div>
                                        {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2 p-l-0"> 
                                            {!! Form::text('attorney_phone_ext',@$contact->attorney_phone_ext,['class'=>'form-control js-number dm-phone-ext','maxlength'=>'4']) !!}
                                        </div>
                                    </div>                                        

                                    <div class="form-group">
                                        {!! Form::label('Fax', 'Fax', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                  
                                        <div class="col-lg-4 col-md-7 col-sm-4 col-xs-10">
                                            {!! Form::text('attorney_fax',@$contact->attorney_fax,['class'=>'form-control dm-fax','maxlength'=>'14']) !!}
                                        </div>
                                    </div>

                                </div><!-- Left side content Ends-->
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal js-address-class" id="js-address-general-address_{{@$contact->id}}"><!-- Right side Content Starts -->   

                                <?php
                                $address_flag['general'] = App\Models\AddressFlag::getAddressFlag('patients', @$contact->id, 'patient_contact_address');
                                ?>

                                {!! Form::hidden('attorney_general_address_type','patients',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('attorney_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('attorney_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('attorney_general_address1',@$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('attorney_general_city',@$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                                {!! Form::hidden('attorney_general_state',@$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                                {!! Form::hidden('attorney_general_zip5',@$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('attorney_general_zip4',@$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('attorney_general_is_address_match',@$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('attorney_general_error_message',@$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}

                                <div class="form-group">
                                    {!! Form::label('Email', 'Email', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('attorney_email',@$contact->attorney_email,['class'=>'form-control']) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
                                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::checkbox('same_as_patient_address', null, null, ['class'=>"js-same_as_patient_address-v2 flat-red med-green",'id'=>'sameaddress-contactform']) !!} <label for="sameaddress-contactform" class="no-bottom med-orange font600">Same as patient address</label> 
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 ">
                                        {!! Form::text('attorney_address1',@$contact->attorney_address1,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-address1','id'=>'attorney_address1']) !!}                          
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 ">
                                        {!! Form::text('attorney_address2',@$contact->attorney_address2,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2','id'=>'attorney_address2']) !!}                          
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                  
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                                        {!! Form::text('attorney_city',@$contact->attorney_city,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-city','id'=>'attorney_city']) !!}
                                    </div>
                                    {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                    <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">          
                                        {!! Form::text('attorney_state',@$contact->attorney_state,['class'=>'form-control p-r-0 js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'attorney_state']) !!}
                                    </div>
                                </div>  

                                <div class="form-group">
                                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                  
                                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">                             
                                        {!! Form::text('attorney_zip5',@$contact->attorney_zip5,['class'=>'form-control js-number js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'attorney_zip5']) !!}                                                      
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
                                        {!! Form::text('attorney_zip4',@$contact->attorney_zip4,['class'=>'form-control js-number js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'attorney_zip4']) !!}                           
                                    </div>
                                    <div class="col-md-1 col-sm-2">            
                                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                      
                                    </div> 
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div> 
                                </div>

                            </div><!-- Right side Content Ends --> 

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">

                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 text-center" id="v2-contacteditffooter_{{@$contact->id}}">
                                <input data-id="v2-contacteditform_{{@$contact->id}}" class="btn btn-medcubics js-v2-edit-contact" type="submit" value="Save">
                                <a class="btn btn-medcubics js-v2-delete-contact" data-id='{{@$contact->id}}'> Delete </a>
                            </div>
                        </div>

                    </div><!-- /.box-body -->   
                </div>                                
            </div>
        </div>
    </div>
    @endif
</div><!-- Box Ends -->
{!! Form::close() !!}