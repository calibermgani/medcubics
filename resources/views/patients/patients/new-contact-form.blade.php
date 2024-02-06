{!! Form::open(['onsubmit'=>"event.preventDefault();",'name'=>'e2-contacteditform_'.@$contact->id,'id'=>'e2-contacteditform_'.@$contact->id,'class'=>'e2-contact-info-form medcubicsform js-e2-common-info-form js-contact-edit-e2']) !!}
{!! Form::hidden('edit_contact_category_e2-'.$contact->id,$contact->category,['id'=>'edit_contact_category_e2-'.$contact->id]) !!}
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
                                    

                                    <div id="add-form-value">
                                        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12  form-horizontal"><!-- Left side Content Starts -->

                                            <div class="form-group bottom-space-10">
                                                {!! Form::label('Relationship', 'Relationship', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-9">
                                                    <?php
														$s = $contact->guarantor_relationship;
														$e = preg_split('/(?=[A-Z])/', $s, -1, PREG_SPLIT_NO_EMPTY);
														$count = count($e);   
                                                    ?>
                                                    <p class="show-border no-bottom" ><?php if($count < 2){echo @$e[0];}else{echo @$e[0]."".strtolower(@$e[1]);}?></p>
                                                    <input type="hidden" id="guarantor_realationship_e1" value="{{$contact->guarantor_relationship}}">
                                                </div>                    
                                            </div>                                            

                                            <div class="form-group bottom-space-10">
                                                {!! Form::label('last Name', 'Last Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-9">
                                                    <p class="show-border no-bottom" id="guarantor_last_name_e1">{{@$contact->guarantor_last_name}}</p>
                                                </div>                    
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                                <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6">
                                                    <p class="show-border no-bottom" id="guarantor_first_name_e1">{{@$contact->guarantor_first_name}}</p>                                                  
                                                </div>
                                                {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                                <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">
                                                    <p class="show-border no-bottom" id="guarantor_middle_name_e1">{{@$contact->guarantor_middle_name}}</p>                                            
                                                </div>
                                            </div>                                                          
                                        </div><!-- Left side content Ends-->



                                    </div>

                                    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 form-horizontal js-address-class" id="js-address-general-address_{{@$contact->id}}"><!-- Right side Content Starts -->   

                                        <?php
                                        $address_flag['general'] = App\Models\AddressFlag::getAddressFlag('patients', @$contact->id, 'patient_contact_address');
                                        ?>                                    
                                        <div class="form-group @if($contact->same_patient_address=='yes') <?php echo 'show'; ?> @endif @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label"></div>
                                            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                              {!! Form::checkbox('same_as_patient_address', null, (@$contact->same_patient_address=='yes'?true:null), ['class'=>" med-green disabled",'id'=>'sameaddress-gua-contact']) !!} <label for="sameaddress-gua-contact"  class="med-orange disabled font600">Same as patient address</label>
                                            </div>
                                            @if($contact->same_patient_address=='yes')
                                            <input type="hidden" name="edit-sameaddress-insurance" id="edit-sameaddress-insurance" value="yes"> 
                                            @endif
                                        </div>
                                      
                                        <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
                                                <p class="show-border no-bottom" id="guarantor_address1_e1">{{@$contact->guarantor_address1}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>

                                            
                                        </div> 

                                        <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('address2')) error @endif">                            
                                                <p class="show-border no-bottom" id="guarantor_address2_e1">{{@$contact->guarantor_address2}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div> 


                                        <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                            <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6">  
                                                <p class="show-border no-bottom" id="guarantor_city_e1">{{@$contact->guarantor_city}}</p>                                                
                                            </div>
                                            {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                            <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0"> 
                                                <p class="show-border no-bottom" id="guarantor_state_e1">{{@$contact->guarantor_state}}</p>                                                
                                            </div>
                                        </div>                                                                   

                                        <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">                             
                                                <p class="show-border no-bottom" id="guarantor_zip5_e1">{{@$contact->guarantor_zip5}}</p>                                                
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">                             
                                                <p class="show-border no-bottom" id="guarantor_zip4_e1">{{@$contact->guarantor_zip4}}</p>                                                
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
                                                <p class="show-border no-bottom" id="guarantor_home_phone_e1">{{@$contact->guarantor_home_phone}}</p>                                                
                                            </div>                  
                                        </div> 
                                        <div class="form-group self-address @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                <p class="show-border no-bottom" id="guarantor_cell_phone_e1">{{@$contact->guarantor_cell_phone}}</p>                                                
                                            </div>
                                        </div>
                                        <div class="form-group self-address @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                            {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                                <p class="show-border no-bottom" id="guarantor_email_e1">{{@$contact->guarantor_email}}</p>                                                
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
                                

                                <div id="add-form-value">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  form-horizontal margin-t-8"><!-- Left side Content Starts -->
                                         <div class="form-group">
                                            {!! Form::label('emergency_relationship', 'Relationship', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                                <?php
                                                    $string = $contact->emergency_relationship;
                                                    $emer = preg_split('/(?=[A-Z])/', $string, -1, PREG_SPLIT_NO_EMPTY);
                                                    $counts = count($emer);
                                                ?>
                                                <p class="show-border no-bottom" ><?php if($counts < 2){echo @$emer[0];}else{ echo @$emer[0]."".strtolower(@$emer[1]);} ?></p>
                                                <input type="hidden" id="emergency_relationship_e1" value="{{$contact->emergency_relationship}}">                                                 
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('LastName', 'Last Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                                <p class="show-border no-bottom" id="emergency_last_name_e1">{{@$contact->emergency_last_name}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                            <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6">
                                                <p class="show-border no-bottom" id="emergency_first_name_e1">{{@$contact->emergency_first_name}}</p>                                                
                                            </div>
                                            {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                            <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">
                                                <p class="show-border no-bottom" id="emergency_middle_name_e1">{{@$contact->emergency_middle_name}}</p>                                                
                                            </div>
                                        </div>     
                                    </div><!-- Left side content Ends-->
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal js-address-class" id="js-address-general-address_{{@$contact->id}}"><!-- Right side Content Starts -->   

                                    <?php
										$address_flag['general'] = App\Models\AddressFlag::getAddressFlag('patients', @$contact->id, 'patient_contact_address');
                                    ?>                                  
                             
                                    <div class="form-group margin-b-10 @if($contact->same_patient_address=='yes') <?php echo 'show'; ?> @endif @if($contact->guarantor_relationship=='Self') <?php echo 'hide'; ?> @endif">
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label"></div>
                                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 ">
                                             {!! Form::checkbox('same_as_patient_address', null, (@$contact->same_patient_address=='yes'?true:null), ['class'=>" med-green disabled js-same_as_patient_address-v2",'id'=>'sameaddress-emergency-contact']) !!} <label for="sameaddress-emergency-contact" class="med-orange disabled font600">Same as patient address</label>
                                             @if($contact->same_patient_address =='yes')
                                             <input type="hidden" name="emergency-sameaddress-insurance" id="emergency-sameaddress-insurance" value="yes">
                                             @endif  
                                        </div>
                                    </div>
                                 
                                    <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                        {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                          <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
                                              <p class="show-border no-bottom" id="emergency_address1_e1">{{@$contact->emergency_address1}}</p>
                                        </div>
                                       <!-- <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
                                            {!! Form::text('emergency_address1',@$contact->emergency_address1,['maxlength'=>'50','id'=>'emergency_address1','class'=>'form-control js-address-check js-v2-address1']) !!}    
                                        </div>-->
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div> 

                                    <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                        {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('address2')) error @endif">                            
                                     <p class="show-border no-bottom" id="emergency_address2_e1">{{@$contact->emergency_address2}}</p>                          
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div> 

                                    <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                        {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6"> 
                                               <p class="show-border no-bottom" id="emergency_city_e1">{{@$contact->emergency_city}}</p>                                    
                                        </div>
                                        {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                        <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">
                                           <p class="show-border no-bottom" id="emergency_state_e1">
                                            {{@$contact->emergency_state}}</p>                                  
                                        </div>
                                    </div>

                                    <div class="form-group same_address @if($contact->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                        {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6"> 
                                           <p class="show-border no-bottom" id="emergency_zip5_e1">
                                            {{@$contact->emergency_zip5}}</p>                                  
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">                             
                                               <p class="show-border no-bottom" id="emergency_zip4_e1">
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
                                            <p class="show-border no-bottom" id="emergency_home_phone_e1">{{@$contact->emergency_home_phone}}</p>                                            
                                        </div>

                                    </div> 
                                    <div class="form-group self-address">
                                        {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                            <p class="show-border no-bottom" id="emergency_cell_phone_e1">{{@$contact->emergency_cell_phone}}</p>                                            
                                        </div>
                                    </div>
                                    <div class="form-group self-address">
                                        {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                            <p class="show-border no-bottom" id="emergency_email_e1">{{@$contact->emergency_email}}</p>                                            
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
                           

                                <div id="add-form-value">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  form-horizontal margin-t-8"><!-- Left side Content Starts -->

                                        <div class="form-group">
                                            {!! Form::label('employer_status', 'Employment Status', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                                <p class="show-border no-bottom" id="employer_status_e1">{{@$contact->employer_status}}</p>                                                 
                                            </div>
                                        </div>

                                        <!-- start employed added fields -->
                                        <span id="employer_option_sub_field-{{@$contact->id}}" class="employer_option_sub_field-{{@$contact->id}} @if(@$contact->employer_status!='Employed' && @$contact->employer_status!='Self Employed' && @$contact->employer_status!='Employed(Part Time)') hide @endif">
                                            <div class="form-group">
                                                {!! Form::label('EmployerName_label', 'Employer Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10"> 
                                                    <p class="show-border no-bottom" id="employer_name_e1">{{@$contact->employer_name}}</p>                                                    
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('occupation_label', 'Occupation', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('medical_chart_no')) error @endif">
                                                    <p class="show-border no-bottom" id="employer_occupation_e1" >{{@$contact->employer_occupation}}</p>                                                    
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
                                                    <p class="show-border no-bottom" id="employer_student_status_e1">{{@$contact->employer_student_status}}</p>                                                    
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
                                    <div  id = "employer-retired-field-{{@$contact->id}}" class = "js_emp_addr_empty employer-retired-field-{{@$contact->id}} @if(@$contact->employer_status == 'Retired' || @$contact->employer_status == 'Unknown') hide @else sample @endif">
                                        <div class="form-group">
                                            {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 ">
                                                <p class="show-border no-bottom" id="employer_address1_e1">{{@$contact->employer_address1}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div> 

                                        <div class="form-group">
                                            {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 ">                            
                                                <p class="show-border no-bottom" id="employer_address2_e1">{{@$contact->employer_address2}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                            <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6">  
                                                <p class="show-border no-bottom" id="employer_city_e1">{{@$contact->employer_city}}</p>                                                
                                            </div>
                                            {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                            <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">  
                                                <p class="show-border no-bottom" id="employer_state_e1">{{@$contact->employer_state}}</p>                                                
                                            </div>
                                        </div>   
                                        <div class="form-group">
                                            {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">                             
                                                <p class="show-border no-bottom" id="employer_zip5_e1">{{@$contact->employer_zip5}}</p>                                                
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">                             
                                                <p class="show-border no-bottom" id="employer_zip4_e1">{{@$contact->employer_zip4}}</p>                                                
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
                                                <p class="show-border no-bottom" id="employer_work_phone_e1" >{{@$contact->employer_work_phone}}</p>                                                
                                            </div>
                                            {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2 p-l-0"> 
                                                <p class="show-border no-bottom" id="employer_phone_ext_e1" >{{@$contact->employer_phone_ext}}</p>                                                
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

    <div class="col-lg-12 margin-t-10 no-padding js-address-employer"  style="border-bottom: 2px solid #f0f0f0;">
        <div class="box-body form-horizontal">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 no-padding ">                                                                

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                        <?php
                            $status = ($attorney_count_v2 > 1) ? "Previous Attorney" : "Current Attorney";
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
                           

                                <div id="add-form-value">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  form-horizontal margin-t-8"><!-- Left side Content Starts -->

                                        <div class="form-group">
                                            {!! Form::label('Attorney / Adjuster Name', 'Attorney / Adjuster Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                                <p class="show-border no-bottom" id="attorney_adjuster_name_e1">{{@$contact->attorney_adjuster_name}}</p>                                                 
                                            </div>
                                        </div>

                                        <!-- start employed added fields -->
                                        <span>
                                            <div class="form-group">
                                                {!! Form::label('Date of Injury', 'Date of Injury', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10"> 
                                                    <p class="show-border no-bottom" id="attorney_doi_e1">{{ App\Http\Helpers\Helpers::dateFormat(@$contact->attorney_doi,'claimdate')}}</p>                                                    
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('Claim No', 'Claim No', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 ">
													<?php 
													try{
														$claimsList = json_decode(json_encode($claims_list), true);
														$cliamIds = '';
														if(isset($contact->attorney_claim_num) && $contact->attorney_claim_num != ''){
															$selClaims = explode(",", $contact->attorney_claim_num);
															foreach($selClaims as $claimId) {
																$cliamIds .= (isset($claimsList[$claimId]) ? $claimsList[$claimId] : $claimId).", "; 
															}
														}
													} catch(Exception $e){
														
													}
													?>
													<p class="show-border no-bottom">
														{{ $cliamIds }}
													</p>
                                                    <p class="show-border no-bottom hide" id="attorney_claim_num_e1">{{$contact->attorney_claim_num}} </p>                                                    
                                                </div>
                                                <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                            </div>
                                        </span>
                                        <!-- end employed added fields -->

                                 
                                        <!-- end student added fields -->


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
                                     {!! Form::hidden('attorney_general_address2',@$address_flag['general']['address2'],['class'=>'js-address-address2']) !!}
                                    {!! Form::hidden('attorney_general_city',@$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                                    {!! Form::hidden('attorney_general_state',@$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                                    {!! Form::hidden('attorney_general_zip5',@$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                                    {!! Form::hidden('attorney_general_zip4',@$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                                    {!! Form::hidden('attorney_general_is_address_match',@$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                                    {!! Form::hidden('attorney_general_error_message',@$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}

                                    <!-- <div class="form-group">
                                        <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
                                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                            {!! Form::checkbox(' ', null, null, ['class'=>"js-same_as_patient_address-v2 flat-red med-green",'id'=>'sameaddress-insurance']) !!} &nbsp; <span class="med-green font600">Same as patient address</span> 
                                        </div>
                                    </div> -->
                                    <div >
                                        <div class="form-group">
                                            {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 ">
                                                <p class="show-border no-bottom" id="attorney_address1_e1">{{@$contact->attorney_address1}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div> 

                                        <div class="form-group">
                                            {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 ">                            
                                                <p class="show-border no-bottom" id="attorney_address2_e1">{{@$contact->attorney_address2}}</p>                                                
                                            </div>
                                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                            <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6">  
                                                <p class="show-border no-bottom" id="attorney_city_e1">{{@$contact->attorney_city}}</p>                                                
                                            </div>
                                            {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                            <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">  
                                                <p class="show-border no-bottom" id="attorney_state_e1">{{@$contact->attorney_state}}</p>                                                
                                            </div>
                                        </div>   
                                        <div class="form-group">
                                            {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">                             
                                                <p class="show-border no-bottom" id="attorney_zip5_e1">{{@$contact->attorney_zip5}}</p>                                                
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">                             
                                                <p class="show-border no-bottom" id="attorney_zip4_e1">{{@$contact->attorney_zip4}}</p>                                                
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
                                                <p class="show-border no-bottom" id="attorney_work_phone_e1" >{{@$contact->attorney_work_phone}}</p>                                                
                                            </div>
                                            {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2 p-l-0"> 
                                                <p class="show-border no-bottom" id="attorney_phone_ext_e1" >{{@$contact->attorney_phone_ext}}</p>                                                
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            {!! Form::label('Fax', 'Fax', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                                            <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 ">  
                                                <p class="show-border no-bottom" id="attorney_fax_e1" >{{@$contact->attorney_fax}}</p>
                                               <div class="col-md-1 col-sm-1 col-xs-2"></div>                                               
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                                           <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 ">   
                                                <p class="show-border no-bottom" id="attorney_email_e1" >{{@$contact->attorney_email}}</p>
                                                 <div class="col-md-1 col-sm-1 col-xs-2"></div>                                                
                                            </div>
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
    @endif

</div><!-- Box Ends -->

{!! Form::close() !!}