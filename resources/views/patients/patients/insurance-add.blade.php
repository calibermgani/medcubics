<div class="box box-info no-border no-shadow no-padding m-b-m-10">
    <div class="box-body no-padding"> 
        <!--input type="button" value="Update" onclick="myCall()" /-->
        <div id="mybox"></div>
        <!---Insurance Information-->   

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-insurence-picker no-padding" id="js-add-picker{{ $count }}" ><!-- Content Starts -->

            <div class="box box-view no-border no-shadow"><!--  Box Starts -->
                <div class="box-header-view no-border">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">New Category</h3>
                    <div class="box-tools pull-right">
                        <?php if ($count != 1) { ?>			
                            <a class="btn add-btn-o js-delete" data-text="Are you sure would you like to delete this patient?" id="delete-js-addmore{{ $count }}"><i class="fa fa-trash-o" data-placement="bottom" data-toggle="tooltip" data-original-title="Delete"></i></a>			
                        <?php } ?>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body"><!--- Start Insurance Box Body-->    
                    <input type="hidden" name="patient_insurance_id[]" value="00">
                    <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
					<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.insurance") }}' />
                    <input id="type" type="hidden" value="add">		
                    <div id="add-form-value">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!-- Left side Content Starts -->
                            <div class="form-horizontal"><!-- Box Starts -->  
                                <div class="form-group">
                                    {!! Form::label('Category', 'Insurance Category ', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 @if($errors->first('category')) error @endif">
                                        {!! Form::select('category[]', [''=>'-- Select --','Primary' => 'Primary','Secondary' => 'Secondary','Tertiary' => 'Tertiary','Workers Comp'=>'Workers Comp','Liability'=>'Liability','Others'=>'Others'],null,['class'=>'select2 form-control js_select_category_class','id'=>'insurance_id'.$count]) !!}
                                        <p class="patient-validation" id="error_category{{ $count }}"></p>

                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('insurance name', 'Insurance', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                                    <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">  
                                        {!! Form::select('insurance_id[]', array(''=>'-- Select --')+(array)$insurances,  $insurance_id,['class'=>'select2 form-control insurance_id js-sel-insurance-address','id'=>'insurance_id']) !!} 
                                        <p id="error_insurance_id{{ $count }}" class="patient-validation"></p>
                                        <p id="sel_insurance_address_id{{ $count }}" class="js-address-part-dis no-bottom med-red hide"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php $count_val = $count - 1; ?>
                                    {!! Form::hidden("temp_doc_id[]",'',['id'=>'temp_doc_id', 'class'=>'jscount-'.$count_val]) !!}

                                    {!! Form::label('policy_id', 'Policy ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                        {!! Form::text('policy_id[]',null,['class'=>'js_no_space form-control dm-policy-id js-bootstrap-policyid js-all-caps-letter-format','maxlength'=>'28']) !!}
                                        <p id="error_policy_id{{ $count }}" class="patient-validation"></p>
                                    </div>
                                    <div class="col-lg-1 col-sm-1 col-xs-2 p-l-0">
                                        <a id="document_add_modal_link_policy_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients::insurance::'.$patients->id.'/0/Patient_Documents_Insurance_Card_Copy')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" class="jscount-{{$count_val}}"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a></div>

                                </div>
                                <div class="form-group" @if(@$registration->group_id !=1) style="display:none;" @endif>
                                     {!! Form::label('group_id', 'Group ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                     <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                        {!! Form::text('group_id[]',null,['class'=>'form-control js-all-caps-letter-format dm-group-id','maxlength'=>'28']) !!}
                                    </div>
                                </div>
                                <div class="form-group" @if(@$registration->group_name !=1) style="display:none;" @endif>
                                     {!! Form::label('group_name', 'Group Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                     <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                        {!! Form::text('group_name[]',null,['class'=>'form-control js-all-caps-letter-format dm-group-id','maxlength'=>'28']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('Relationship', 'Relationship ', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                        {!! Form::select('relationship[]', [''=>'-- Select --','Self' => 'Self','Spouse' => 'Spouse','Child' => 'Child','Father'=>'Father','Mother'=>'Mother','Son'=>'Son','Daughter'=>'Daughter'],'Self',['class'=>'select2 form-control js-relationship']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('LastName', 'Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                        {!! Form::text('last_name[]',@$patients->last_name,['class'=>'form-control','id'=>'insurance_last_name']) !!}
                                        <p id="error_last_name{{ $count }}" class="patient-validation"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">
                                        {!! Form::text('first_name[]',@$patients->first_name,['class'=>'form-control ','id'=>'insurance_first_name']) !!}
                                    </div>
                                     {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                    <div class="col-lg-1 col-md-2 col-sm-1 col-xs-4 p-l-0">
                                        {!! Form::text('middle_name[]',@$patients->middle_name,['class'=>'form-control','id'=>'insurance_mi','maxlength'=>'1']) !!}
                                    </div>
                                </div>
                                
                                
                                <div class="form-group" @if(@$registration->insured_ssn !=1) style="display:none;" @endif>
                                     {!! Form::label('insured_ssn', 'Insured SSN', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                     <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                        {!! Form::text('insured_ssn[]',@$patients->ssn,['class'=>'form-control dm-ssn', 'id' => 'insurance_ssn_'.$count]) !!}
                                        <p id="error_insured_ssn{{ $count }}" class="patient-validation"></p>
                                        <span id="test"></span>
                                    </div>

                                    <a id="document_add_modal_link_insured_ssn" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients::insurance::'.$patients->id.'/0/insured_ssn')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" class="jscount-{{$count_val}}"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
                                </div>


                            </div><!-- /.box Ends-->
                        </div><!--  Left side Content Ends -->  <!--- End Insurance Information--> 
                        <!-- Insured Info-->
                        <!---Insurance Information-->           
                        <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 col-lg-offset-1 col-md-offset-1"><!-- Left side Content Starts -->
                            <div class="form-horizontal"><!-- Box Starts -->

                                <div class="form-group" @if(@$registration->insured_dob !=1) style="display:none;" @endif>
                                     {!! Form::label('insured_dob', 'Insured DOB', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-10 control-label']) !!} 

                                     <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 @if($errors->first('insured_dob')) error @endif">
                                        <i class="fa fa-calendar-o form-icon"></i> 
                                        {!! Form::text('insured_dob[]',@$patients->dob,['placeholder'=>'mm/dd/yyyy','class'=>'form-control form-cursor js-insurance_dob dm-date','id'=>'insurence_date'.$count]) !!} 
                                        {!! $errors->first('insured_dob', '<p> :message</p>')  !!}
                                    </div>
                                </div>
                                <div class="form-group" @if(@$registration->adjustor_ph !=1) style="display:none;" @endif>
                                     {!! Form::label('adjustor_ph', 'Adjustor Ph', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                     <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                        {!! Form::text('adjustor_ph[]',null,['class'=>'form-control js-phone dm-phone','id'=>'phone']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div>                      
                                <div class="form-group" @if(@$registration->adjustor_fax !=1) style="display:none;" @endif>
                                     {!! Form::label('adjustor_fax', 'Adjustor Fax', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                     <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                        {!! Form::text('adjustor_fax[]',null,['class'=>'form-control js-fax dm-fax','id'=>'fax']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('effective_date', 'Effective Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-10 control-label']) !!} 

                                    <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 @if($errors->first('end_date')) error @endif">    
                                        <i class="fa fa-calendar-o form-icon"></i> 						
                                         
                                        {!! $errors->first('effective_date', '<p> :message</p>')  !!}
                                        <p id="error_effective_date{{ $count }}" class="patient-validation"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('termination_date', 'Termination Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-10 control-label']) !!} 

                                    <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 @if($errors->first('end_date')) error @endif">    
                                        <i class="fa fa-calendar-o form-icon"></i> 
                                        {!! Form::text('termination_date[]',null,['id'=>'termination_date'.$count,'placeholder'=>'mm/dd/yyyy','data-inputmask'=>'"mask": "99/99/9999"','data-mask','class'=>'form-control form-cursor js_insurence']) !!}
                                        {!! $errors->first('termination_date', '<p> :message</p>')  !!}					
                                        <p id="error_termination_date{{ $count }}" class="patient-validation"></p>							
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('accept_assignmentS', 'Accept Assignment', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-6 control-label']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-7 col-xs-6">
                                        {!! Form::radio('accept_assignment[]', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('accept_assignment[]', 'No',true,['class'=>'flat-red']) !!} No                                       
                                    </div>            
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('release_of_information', 'Release of Information', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-6 control-label']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-8 col-xs-6">
                                        {!! Form::radio('release_of_information[]', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('release_of_information[]', 'No',true,['class'=>'flat-red']) !!} No 
                                    </div>
                                </div> 

                                <div class="form-group">
                                    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-10">                             
                                        {!! Form::textarea('insurance_notes[]',null,['class'=>'form-control textarea-patient','placeholder'=>'Notes']) !!}  
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  <!--- End Insurance -->                    
                </div>
            </div>
        </div>
    </div>
</div>