<?php $category = str_replace(' ', '_', strtolower($category)); ?>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.contact") }}' />
@if($category == 'guarantor')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-contact-option" id="js-contact_guarantor_{{ $cur_count }}"><!-- guarantor Starts --> 	
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!-- Left side Content Starts -->
        <div class="form-horizontal"><!-- Box Starts -->
            <?php
            if (@$contact->guarantor_last_name != '')
                $guarantor_last_name = $contact->guarantor_last_name;
            else
                $guarantor_last_name = @$patients->guarantor_last_name;

            if (@$contact->guarantor_first_name != '')
                $guarantor_first_name = $contact->guarantor_first_name;
            else
                $guarantor_first_name = @$patients->guarantor_first_name;

            if (@$contact->guarantor_middle_name != '')
                $guarantor_middle_name = $contact->guarantor_middle_name;
            else
                $guarantor_middle_name = @$patients->guarantor_middle_name;

            if (@$contact->guarantor_relationship != '')
                $guarantor_relationship = $contact->guarantor_relationship;
            else
                $guarantor_relationship = @$patients->guarantor_relationship;
            ?>
            <div class="form-group">
                {!! Form::label('LastName', 'Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::text('guarantor_last_name[]',$guarantor_last_name,['class'=>'form-control js-letters-caps-format','maxlength'=>'50','id' => 'guarantor_last_name_'.$cur_count]) !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">
                    {!! Form::text('guarantor_first_name[]',$guarantor_first_name,['class'=>'form-control js-letters-caps-format','maxlength'=>'50','id' => 'guarantor_first_name_'.$cur_count]) !!}
                </div>
                {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">
                    {!! Form::text('guarantor_middle_name[]',$guarantor_middle_name,['class'=>'form-control p-r-0 dm-mi js-letters-caps-format','id' => 'guarantor_middle_name_'.$cur_count]) !!}
                </div>
            </div>                                                          

            <div class="form-group">
                {!! Form::label('guarantor_relationship', 'Relationship', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::select('guarantor_relationship[]', [''=>'-- Select --','Self'=>'Self','Child' => 'Child','Father' => 'Father','Mother' => 'Mother','Spouse'=>'Spouse','Neighbor'=>'Neighbor','Grandmother'=>'Grandmother','Grandfather'=>'Grandfather','Grandchild'=>'Grandchild','Friend'=>'Friend','Brother'=>'Brother','Sister'=>'Sister','Guardian'=>'Guardian','Others'=>'Others'],$guarantor_relationship,['class'=>'select2 form-control','id' => 'guarantor_relationship_'.$cur_count]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('Home Phone', 'Home Phone', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">  
                    {!! Form::text('guarantor_home_phone[]',@$contact->guarantor_home_phone,['class'=>'form-control js-number dm-phone','maxlength'=>'14']) !!}
                </div>					
            </div> 
            <div class="form-group">
                {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::text('guarantor_cell_phone[]',@$contact->guarantor_cell_phone,['class'=>'form-control js-number dm-phone','maxlength'=>'14']) !!}
                </div>
            </div>				
        </div>
    </div><!--  Left side Content Ends --> 

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal"><!-- Right side Content Starts -->        
        <div class="">


            <div class="form-group">
                {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::text('guarantor_email[]',@$contact->guarantor_email,['class'=>'form-control']) !!}
                    <p class="patient-validation" id="error_guarantor_email{{ $cur_count }}"></p>
                </div>				   
            </div>

            <div class="form-group">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                    {!! Form::checkbox('same_as_patient_address', null, null, ['class'=>" js-same_as_patient_address",'id'=>'sameaddress-guarantor-'.$cur_count]) !!} &nbsp;Same as patient address 
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
                    {!! Form::text('guarantor_address1[]',@$contact->guarantor_address1,['maxlength'=>'50','id'=>'guarantor_address1'.$cur_count,'class'=>'form-control js-address-check']) !!}                            
                    {!! $errors->first('address1', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address2')) error @endif">                            
                    {!! Form::text('guarantor_address2[]',@$contact->guarantor_address2,['maxlength'=>'50','class'=>'form-control js-address2-tab']) !!}                            
                    {!! $errors->first('address2', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div> 


            <div class="form-group">
                {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                    {!! Form::text('guarantor_city[]',@$contact->guarantor_city,['maxlength'=>'50','class'=>'form-control js-address-check','id'=>'guarantor_city'.$cur_count ]) !!}
                </div>
                {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0"> 
                    {!! Form::text('guarantor_state[]',@$contact->guarantor_state,['class'=>'form-control js-address-check js-state-tab','maxlength'=>'2','id'=>'guarantor_state'.$cur_count ]) !!}
                </div>
            </div>                                                                   

            <div class="form-group">
                {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">                             
                    {!! Form::text('guarantor_zip5[]',@$contact->guarantor_zip5,['class'=>'form-control js-number js-address-check dm-zip5','maxlength'=>'5','id'=>'guarantor_zip5'.$cur_count]) !!}
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
                    {!! Form::text('guarantor_zip4[]',@$contact->guarantor_zip4,['class'=>'form-control js-number js-address-check dm-zip4','maxlength'=>'4','id'=>'guarantor_zip4'.$cur_count]) !!}
                </div>
                <div class="col-md-1 col-sm-2">            
                    <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                    <?php echo $value; ?>
                </div> 
                <div class="col-md-1 col-sm-1 col-xs-2">            
                </div> 
            </div>  
        </div>              
    </div><!-- Right Side Ends -->
</div><!-- guarantor Ends -->
@elseif($category == 'emergency_contact')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-contact-option" id="js-contact_emergency_contact_{{$cur_count}}"><!-- emergency Starts --> 
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal"><!-- Left side Content Starts -->            
        <div class="form-group">
            {!! Form::label('LastName', 'Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                {!! Form::text('emergency_last_name[]',@$contact->emergency_last_name,['class'=>'form-control js-letters-caps-format','maxlength'=>'50']) !!}
            </div>
            <div class="col-md-1 col-sm-1 col-xs-2"></div>
        </div>

        <div class="form-group">
            {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
            <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">
                {!! Form::text('emergency_first_name[]',@$contact->emergency_first_name,['class'=>'form-control js-letters-caps-format','maxlength'=>'50']) !!}
            </div>
            {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
            <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">
                {!! Form::text('emergency_middle_name[]',@$contact->emergency_middle_name,['class'=>'form-control p-r-0 dm-mi js-letters-caps-format']) !!}
            </div>
        </div>                                        

        <div class="form-group">
            {!! Form::label('emergency_relationship', 'Relationship', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                {!! Form::select('emergency_relationship[]', [''=>'-- Select --','Child' => 'Child','Father' => 'Father','Mother' => 'Mother','Spouse'=>'Spouse','Neighbour'=>'Neighbour','Grandmother'=>'Grandmother','Grandfather'=>'Grandfather','Grandchild'=>'Grandchild','Friend'=>'Friend','Brother'=>'Brother','Sister'=>'Sister','Guardian'=>'Guardian','Others'=>'Others'],@$contact->emergency_relationship,['class'=>'select2 form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('Home Phone', 'Home Phone', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                {!! Form::text('emergency_home_phone[]',@$contact->emergency_home_phone,['class'=>'form-control js-number dm-phone','maxlength'=>'14']) !!}
            </div>

        </div> 
        <div class="form-group">
            {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                {!! Form::text('emergency_cell_phone[]',@$contact->emergency_cell_phone,['class'=>'form-control js-number dm-phone','maxlength'=>'14']) !!}
            </div>
        </div>				         
    </div><!--  Left side Content Ends --> 

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal"><!-- Right side Content Starts -->          
        <div><!-- Address Col Starts -->

            <div class="form-group">
                {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::text('emergency_email[]',@$contact->emergency_email,['class'=>'form-control']) !!}
                    <p class="patient-validation" id="error_emergency_email{{ $cur_count }}"></p>
                </div>				   
            </div>

            <div class="form-group">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                    {!! Form::checkbox('same_as_patient_address', null, null, ['class'=>" js-same_as_patient_address",'id'=>'sameaddress-emergency-'.$cur_count]) !!} &nbsp;Same as patient address 
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
                    {!! Form::text('emergency_address1[]',@$contact->emergency_address1,['maxlength'=>'50','id'=>'emergency_address1'.$cur_count,'class'=>'form-control js-address-check']) !!}    
                    {!! $errors->first('address1', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address2')) error @endif">
                    {!! Form::text('emergency_address2[]',@$contact->emergency_address2,['maxlength'=>'50','class'=>'form-control js-address2-tab']) !!}                            
                    {!! $errors->first('address2', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                    {!! Form::text('emergency_city[]',@$contact->emergency_city,['maxlength'=>'50','class'=>'form-control js-address-check','id'=>'emergency_city'.$cur_count]) !!}
                </div>
                {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0"> 
                    {!! Form::text('emergency_state[]',@$contact->emergency_state,['class'=>'form-control p-r-0 js-address-check js-state-tab','maxlength'=>'2','id'=>'emergency_state'.$cur_count]) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">                             
                    {!! Form::text('emergency_zip5[]',@$contact->emergency_zip5,['class'=>'form-control js-number js-address-check dm-zip5','maxlength'=>'5','id'=>'emergency_zip5'.$cur_count]) !!}
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
                    {!! Form::text('emergency_zip4[]',@$contact->emergency_zip4,['class'=>'form-control js-number js-address-check dm-zip4','maxlength'=>'4','id'=>'emergency_zip4'.$cur_count]) !!}
                </div>
                <div class="col-md-1 col-sm-2">            
                    <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                    <?php echo $value; ?>
                </div> 
                <div class="col-md-1 col-sm-1 col-xs-2"></div> 
            </div> 

        </div><!-- Address Col Ends -->              
    </div><!-- Right Side Content Ends -->
</div><!-- Emergency Contact Ends -->
@elseif($category == 'employer')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-contact-option" id="js-contact_employer_{{ $cur_count }}"><!-- employer Starts --> 	
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal"><!-- Left side Content Starts -->         
        <div class="form-group">
            {!! Form::label('employer_status', 'Employer Status', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                {!! Form::select('employer_status[]', [''=>'-- Select --','Employed' => 'Employed','Self Employed' => 'Self Employed','Retired' => 'Retired','Active Military Duty'=>'Active Military Duty','Employed(Full Time)'=>'Employed(Full Time)','Employed(Part Time)'=>'Employed(Part Time)','Student'=>'Student','Unknown'=>'Unknown'],@$contact->employer_status,['class'=>'select2 form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('Employer Name', 'Employer Name', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10"> 
                {!! Form::text('employer_name[]',@$contact->employer_name,['class'=>'form-control js-letters-caps-format']) !!}
            </div>
        </div> 
        <div class="form-group">
            {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">  
                {!! Form::text('employer_work_phone[]',@$contact->employer_work_phone,['class'=>'form-control js-number dm-phone','maxlength'=>'14']) !!}
            </div>
            {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 p-l-0"> 
                {!! Form::text('employer_phone_ext[]',@$contact->employer_phone_ext,['class'=>'form-control p-r-0 js-number dm-phone-ext','maxlength'=>'4']) !!}
            </div>
        </div>                                                            
        <!--div class="form-group">
                {!! Form::label('Email', 'Email', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                        {!! Form::text('emergency_email',null,['class'=>'form-control']) !!}
                </div>                  
        </div-->           
    </div><!--  Left side Content Ends --> 

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal"><!-- Right side Content Starts -->         
        <div><!-- Address Col Starts -->

            <div class="form-group">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                    {!! Form::checkbox('same_as_patient_address', null, null, ['class'=>" js-same_as_patient_address",'id'=>'sameaddress-employer-'.$cur_count]) !!} &nbsp;Same as patient address  
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 ">
                    {!! Form::text('employer_address1[]',@$contact->employer_address1,['maxlength'=>'50','id'=>'employer_address1'.$cur_count,'class'=>'form-control js-address-check']) !!}                          
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 ">                            
                    {!! Form::text('employer_address2[]',@$contact->employer_address2,['maxlength'=>'50','class'=>'form-control js-address2-tab']) !!}                            

                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div>                               

            <div class="form-group">
                {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                    {!! Form::text('employer_city[]',@$contact->employer_city,['maxlength'=>'50','class'=>'form-control js-address-check','id'=>'employer_city'.$cur_count]) !!}
                </div>
                {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">  
                    {!! Form::text('employer_state[]',@$contact->employer_state,['class'=>'form-control p-r-0 js-address-check js-state-tab','maxlength'=>'2','id'=>'employer_state'.$cur_count]) !!}
                </div>
            </div>   
            <div class="form-group">
                {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">                             
                    {!! Form::text('employer_zip5[]',@$contact->employer_zip5,['class'=>'form-control js-number js-address-check dm-zip5','maxlength'=>'5','id'=>'employer_zip5'.$cur_count]) !!}
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
                    {!! Form::text('employer_zip4[]',@$contact->employer_zip4,['class'=>'form-control js-number js-address-check dm-zip4','maxlength'=>'4','id'=>'employer_zip4'.$cur_count]) !!}
                </div>
                <div class="col-md-1 col-sm-2">            
                    <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                    <?php echo $value; ?>
                </div> 
                <div class="col-md-1 col-sm-1 col-xs-2">            
                </div> 
            </div> 

        </div><!-- Address Col Ends -->               
    </div><!-- Right Side Ends -->
</div><!-- Employer Ends -->
@elseif($category == 'attorney')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-contact-option" id="js-contact_attorney_{{ $cur_count }}"><!-- Attorney Starts --> 	
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal"><!-- Left side Content Starts -->            
        <div class="form-group">
            {!! Form::label('Adjustor Name', 'Adjustor Name', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10"> 
                {!! Form::text('attorney_adjuster_name[]',@$contact->attorney_adjuster_name,['class'=>'form-control js-letters-caps-format','maxlength'=>'50']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('attorney_doi', 'DOI', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('insured_dob')) error @endif">
                <i class="fa fa-calendar-o form-icon"></i>          
                {!! Form::text('attorney_doi[]',App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$contact->attorney_doi),['class'=>'form-control form-cursor doi dm-date','placeholder'=>'mm/dd/yyyy','id'=>'attorney_doi'.$cur_count]) !!}
            </div>
            <div class="col-sm-1 col-xs-2"></div>
        </div>
        <div class="form-group">
            {!! Form::label('Claim Number', 'Claim Number', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10"> 
                {!! Form::text('attorney_claim_num[]',@$contact->attorney_claim_num,['class'=>'form-control','maxlength'=>'15']) !!}
            </div>
        </div> 		
        <div class="form-group">
            {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6"> 
                {!! Form::text('attorney_work_phone[]',@$contact->attorney_work_phone,['class'=>'form-control js-number dm-phone','maxlength'=>'14']) !!}
            </div>
            {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 p-l-0"> 
                {!! Form::text('attorney_phone_ext[]',@$contact->attorney_phone_ext,['class'=>'form-control js-number dm-phone-ext','maxlength'=>'4']) !!}
            </div>
        </div>                                        

        <div class="form-group">
            {!! Form::label('Fax', 'Fax', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                {!! Form::text('attorney_fax[]',@$contact->attorney_fax,['class'=>'form-control dm-fax','maxlength'=>'14']) !!}
            </div>
        </div>            
    </div><!--  Left side Content Ends --> 

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal"><!-- Right side Content Starts -->           
        <div><!-- Address Col Starts -->

            <div class="form-group">
                {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::text('attorney_email[]',@$contact->attorney_email,['class'=>'form-control']) !!}
                    <p class="patient-validation" id="error_attorney_email{{ $cur_count }}"></p>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                    {!! Form::checkbox('same_as_patient_address', null, null, ['class'=>"js-same_as_patient_address",'id'=>'sameaddress-attorney-'.$cur_count]) !!} &nbsp;Same as patient address  
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 ">
                    {!! Form::text('attorney_address1[]',@$contact->attorney_address1,['maxlength'=>'50','class'=>'form-control js-address-check','id'=>'attorney_address1'.$cur_count]) !!}                          
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div> 
            <div class="form-group">
                {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 ">                            
                    {!! Form::text('attorney_address2[]',@$contact->attorney_address2,['maxlength'=>'50','class'=>'form-control js-address2-tab']) !!}                            
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div> 
            <div class="form-group">
                {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                    {!! Form::text('attorney_city[]',@$contact->attorney_city,['maxlength'=>'50','class'=>'form-control js-address-check','id'=>'attorney_city'.$cur_count]) !!}
                </div>
                {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                <div class="col-lg-1 col-md-2 col-sm-1 col-xs-2 p-l-0">          
                    {!! Form::text('attorney_state[]',@$contact->attorney_state,['class'=>'form-control p-r-0 js-address-check js-state-tab','maxlength'=>'2','id'=>'attorney_state'.$cur_count]) !!}
                </div>
            </div>  

            <div class="form-group">
                {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">                             
                    {!! Form::text('attorney_zip5[]',@$contact->attorney_zip5,['class'=>'form-control js-number js-address-check dm-zip5','maxlength'=>'5','id'=>'attorney_zip5'.$cur_count]) !!}
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
                    {!! Form::text('attorney_zip4[]',@$contact->attorney_zip4,['class'=>'form-control js-number js-address-check dm-zip4','maxlength'=>'4','id'=>'attorney_zip4'.$cur_count]) !!}
                </div>
                <div class="col-md-1 col-sm-2">            
                    <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                    <?php echo $value; ?>
                </div> 
                <div class="col-md-1 col-sm-1 col-xs-2"></div> 
            </div> 

        </div><!-- Address Col Ends -->   
    </div><!-- Right Side Ends -->
</div><!-- Attorney Ends -->
@endif

@if($category != 'guarantor')
{!! Form::hidden('guarantor_last_name[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_first_name[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_middle_name[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_relationship[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_home_phone[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_cell_phone[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_email[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_address1[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_address2[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_city[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_state[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_zip5[]',null,['disabled']) !!}
{!! Form::hidden('guarantor_zip4[]',null,['disabled']) !!}
@endif

@if($category != 'emergency_contact')
{!! Form::hidden('emergency_last_name[]',null,['disabled']) !!}
{!! Form::hidden('emergency_first_name[]',null,['disabled']) !!}
{!! Form::hidden('emergency_middle_name[]',null,['disabled']) !!}
{!! Form::hidden('emergency_relationship[]',null,['disabled']) !!}
{!! Form::hidden('emergency_home_phone[]',null,['disabled']) !!}
{!! Form::hidden('emergency_cell_phone[]',null,['disabled']) !!}
{!! Form::hidden('emergency_email[]',null,['disabled']) !!}
{!! Form::hidden('emergency_address1[]',null,['disabled']) !!}
{!! Form::hidden('emergency_address2[]',null,['disabled']) !!}
{!! Form::hidden('emergency_city[]',null,['disabled']) !!}
{!! Form::hidden('emergency_state[]',null,['disabled']) !!}
{!! Form::hidden('emergency_zip5[]',null,['disabled']) !!}
{!! Form::hidden('emergency_zip4[]',null,['disabled']) !!}
@endif

@if($category != 'employer')
{!! Form::hidden('employer_status[]',null,['disabled']) !!}
{!! Form::hidden('employer_name[]',null,['disabled']) !!}
{!! Form::hidden('employer_work_phone[]',null,['disabled']) !!}
{!! Form::hidden('employer_phone_ext[]',null,['disabled']) !!}
{!! Form::hidden('employer_address1[]',null,['disabled']) !!}
{!! Form::hidden('employer_address2[]',null,['disabled']) !!}
{!! Form::hidden('employer_city[]',null,['disabled']) !!}
{!! Form::hidden('employer_state[]',null,['disabled']) !!}
{!! Form::hidden('employer_zip5[]',null,['disabled']) !!}
{!! Form::hidden('employer_zip4[]',null,['disabled']) !!}
@endif

@if($category != 'attorney')
{!! Form::hidden('attorney_adjuster_name[]',null,['disabled']) !!}
{!! Form::hidden('attorney_doi[]',null,['disabled']) !!}
{!! Form::hidden('attorney_claim_num[]',null,['disabled']) !!}
{!! Form::hidden('attorney_work_phone[]',null,['disabled']) !!}
{!! Form::hidden('attorney_phone_ext[]',null,['disabled']) !!}
{!! Form::hidden('attorney_fax[]',null,['disabled']) !!}
{!! Form::hidden('attorney_email[]',null,['disabled']) !!}
{!! Form::hidden('attorney_address1[]',null,['disabled']) !!}
{!! Form::hidden('attorney_address2[]',null,['disabled']) !!}
{!! Form::hidden('attorney_city[]',null,['disabled']) !!}
{!! Form::hidden('attorney_state[]',null,['disabled']) !!}
{!! Form::hidden('attorney_zip5[]',null,['disabled']) !!}
{!! Form::hidden('attorney_zip4[]',null,['disabled']) !!}
@endif