{!! Form::hidden('contact_same_as_address1',@$patients->address1,['class'=>'form-control','id'=>'contact_same_as_address1']) !!}
{!! Form::hidden('contact_same_as_address2',@$patients->address2,['class'=>'form-control','id'=>'contact_same_as_address2']) !!}
{!! Form::hidden('contact_same_as_city',@$patients->city,['class'=>'form-control','id'=>'contact_same_as_city']) !!}
{!! Form::hidden('contact_same_as_state',@$patients->state,['class'=>'form-control','id'=>'contact_same_as_state']) !!}
{!! Form::hidden('contact_same_as_zip5',@$patients->zip5,['class'=>'form-control','id'=>'contact_same_as_zip5']) !!}
{!! Form::hidden('contact_same_as_zip4',@$patients->zip4,['class'=>'form-control','id'=>'contact_same_as_zip4']) !!}
{!! Form::hidden('contact_count_v2',@$contact_count,['class'=>'form-control','id'=>'contact_count_v2']) !!}

{!! Form::hidden('primary_ins_id',$primary_ins_id,['class'=>'form-control','id'=>'primary_ins_id']) !!}
{!! Form::hidden('secondary_ins_id',$secondary_ins_id,['class'=>'form-control','id'=>'secondary_ins_id']) !!}
{!! Form::hidden('tertiary_ins_id',$tertiary_ins_id,['class'=>'form-control','id'=>'tertiary_ins_id']) !!}
{!! Form::hidden('workerscomp_ins_id',$workerscomp_ins_id,['class'=>'form-control','id'=>'workerscomp_ins_id']) !!}
{!! Form::hidden('autoaccident_ins_id',$autoaccident_ins_id,['class'=>'form-control','id'=>'autoaccident_ins_id']) !!}
{!! Form::hidden('attorney_ins_id',$attorney_ins_id,['class'=>'form-control','id'=>'attorney_ins_id']) !!}

{!! Form::model($archiveinsurance, ['name'=>'v2_insurance_form','id'=>'js-bootstrap-validator-insurance','name'=>'myform','class'=>'popupmedcubicsform js-v2-common-info-form']) !!} 


<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.insurance") }}' />
<div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->


    <div class="box-body form-horizontal no-padding">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-address-class" id="js-address-general-address">

            <div class="form-group margin-b-10">
                {!! Form::label('Category', 'Category', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-5 col-md-6 col-sm-5 col-xs-10 @if($errors->first('category')) error @endif">
                    {!! Form::select('category', $category,null,['class'=>'select_2 form-control js_select_category_class', 'id' => 'js-category']) !!}
                </div>
            </div>

            {!! Form::hidden('patient_insurance_id',$archiveinsurance->id,['class'=>'form-control js_patient_insurance_id']) !!}

            <input type="hidden" id="js_policyid_chk_{{ $archiveinsurance->id }}" />
            <input type="hidden" id="js_insurancetype_chk_{{ $archiveinsurance->id }}" value="" />

            <?php $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patients->id,'encode'); ?>

            {!! Form::hidden('patient_id',$patient_id,['id'=>'encode_patient_id']) !!}


            <div class="form-group margin-b-10">
                {!! Form::label('insurance name', 'Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">  
                    {!! Form::select('insurance_id', array(''=>'-- Select --')+(array)$insurances,  null,['class'=>'select_2 form-control insurance_id js-sel-modalinsurance-address v2-js-insurance-change','disabled'=>'disabled','id'=>'insurance_id-'. $archiveinsurance->id]) !!} 
                </div>
                {!! Form::hidden('insurance_id',$archiveinsurance->insurance_id) !!}
                <!--div class="col-lg-1 col-md-1 col-sm-2 col-xs-2  p-l-0">
                        <a href="#js-model-insurance-details" data-toggle="modal" data-target="#js-model-insurance-details"  tabindex="-1">
                                <i class="fa fa-search icon-green-form"></i></a>
                </div-->

            </div>

            <div class="js-add-new-select hide" id="js-insurance-type-{{@$archiveinsurance->id}}">
                <div class="form-group margin-b-10 js_common_ins">
                    {!! Form::label('InsuranceType', 'Insurance Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        {!! Form::select('insurancetype_id', array('' => '-- Select --')+(array)$insurancetypes,  null,['class'=>'form-control select_2 js-add-new-select-opt', 'id'=>'insurancetype_id-'.@$archiveinsurance->id]) !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 
                <div class="form-group hide" id="add_new_span">
                    {!! Form::label('InsuranceType', 'Insurance Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        {!! Form::text('newadded',null,['id'=>'newadded','class'=>'form-control','placeholder'=>'Add new Insurance Type','data-table-name'=>'insurancetypes','data-field-name'=>'type_name','data-field-id'=>'','data-label-name'=>'insurance type']) !!}
                        <p class="js-error help-block hide"></p>
                        <p class="pull-right no-bottom">
                            <i class="fa fa-save med-green" id="add_new_save" data-placement="bottom"  data-toggle="tooltip" data-original-title="Save"></i>
                            <i class="fa fa-ban med-green margin-l-5" id="add_new_cancel" data-placement="bottom"  data-toggle="tooltip" data-original-title="Cancel"></i>                         
                        </p>
                    </div>
                </div>
            </div>

            <!-- <div class="form-group margin-b-10">
                    {!! Form::label('Insurance Type', 'Insurance Type', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('type')) error @endif">
                            {!! Form::select('category1', [''=>'-- Select --','Medicare' => 'Medicare','Medcaid' => 'Medcaid','Commercial' => 'Commercial','Others'=>'Others'],null,['class'=>'select2 form-control js_select_category_class']) !!}
                    </div>
            </div> -->

            <input type="hidden" id="js_policyid_chk_0" />
            <input type="hidden" id="js_insurancetype_chk_0" value="" />
            <div class="form-group margin-b-10 js_medicareins_0 hide">
                {!! Form::label('MedicareType', 'If Medicare Secondary ?', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::select('medical_secondary_code', array('' => '-- Select --')+(array)$medical_secondary_list,NULL,['class'=>'form-control','data-id'=>0]) !!}
                    {!! $errors->first('medical_secondary_code', '<p> :message</p>')  !!}  
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group margin-b-10">
                {!! Form::label('policy_id', 'Policy ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 js-bootstrap-policyid-row">
                    {!! Form::text('policy_id',null,['maxlength'=>'15','class'=>'form-control dm-policy-id js-bootstrap-policyid js-all-caps-letter-format', 'id'=>'policy_id-'.$archiveinsurance->id, 'autocomplete'=>'nope']) !!}
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-10">
                    <a href="javascript:void(0);" class="js-patient-eligibility_check eligibility_gray" data-page="pat_ins" style="display:none; width: 7px;" title="Check Eligibility"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-gray font10"></i></a> 
                    <i class="fa fa-spinner fa-spin eligibilityloadingimg font11" style="display:none;"></i>

                    <a class="js_get_eligiblity_details js_eliactive" title="Eligibility Details" data-page="pat_ins" style="display:none; width: 7px;" data-toggle="modal" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-green font10"></i></a>

                    <a class="js_get_eligiblity_details js_eliinactive" title="Eligibility Details" data-page="pat_ins" style="display:none; width: 7px;" data-toggle="modal" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-red font10"></i></a>
                </div>

            </div>

            <div class="form-group margin-b-10 " @if(@$registration->group_name_id !=1) style="display:none;" @endif>
                 {!! Form::label('group_name', 'Group Name / ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                 <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::text('group_name',null,['class'=>'form-control js-all-caps-letter-format dm-group-id', 'autocomplete'=>'nope']) !!}
                </div>
            </div>                           

            <div class="form-group margin-b-10 hide" @if(@$registration->adjustor_ph !=1) style="display:none;" @endif>
                 {!! Form::label('adjustor_ph', 'Adjustor Ph', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                 <div class="col-lg-5 col-md-6 col-sm-7 col-xs-10">
                    {!! Form::text('adjustor_ph',null,['class'=>'form-control js-phone dm-phone','id'=>'phone']) !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div>                      
            <div class="form-group margin-b-10 hide" @if(@$registration->adjustor_fax !=1) style="display:none;" @endif>
                 {!! Form::label('adjustor_fax', 'Adjustor Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                 <div class="col-lg-5 col-md-6 col-sm-7 col-xs-10">
                    {!! Form::text('adjustor_fax',null,['class'=>'form-control js-fax dm-fax','id'=>'fax']) !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div>

            <?php
				$readonly_class =  '';
				
				$medicare_insurance_code = Config::get('siteconfigs.medicare_insurance_type_code');
				if (in_array(@$archiveinsurance->insurance_type_details->code, $medicare_insurance_code) || @$patients->is_self_pay == 'Yes') {
					$pat_rel_disabled = 'disabled';
				} else {
					$pat_rel_disabled = '';
				}
            ?>

            <input id="prev_relationship-0" type="hidden">
            <div class="form-group margin-b-10">
                {!! Form::label('Relationship', 'Insured ', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-5 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::select('relationship', ['Self' => 'Self','Spouse' => 'Spouse','Child' => 'Child','Others'=>'Others '],@$archiveinsurance->relationship,['class'=>'select_2 form-control js-relationship','id'=>'relationship-'.$archiveinsurance->id]) !!}
                </div>
            </div>                        
           

            <?php
				$insurance_part_dis = (@$archiveinsurance->relationship=='Self') ? "hide" : "show";
			
				$first_name = $archiveinsurance->first_name;
				$last_name = $archiveinsurance->last_name;
				$middle_name = $archiveinsurance->middle_name;
				$ssn = $archiveinsurance->insured_ssn;
				$dob = App\Http\Helpers\Helpers::checkAndDisplayDateInInput($archiveinsurance->insured_dob);
				$address1 = @$archiveinsurance->insured_address1;
				$address2 = @$archiveinsurance->insured_address2;
				$city = @$archiveinsurance->insured_city;
				$state = @$archiveinsurance->insured_state;
				$zip5 = @$archiveinsurance->insured_zip5;
				$zip4 = @$archiveinsurance->insured_zip4;
				$address_flag['general'] = App\Models\AddressFlag::getAddressFlag('patients', @$archiveinsurance->id, 'patient_insurance_address');
            ?>	
            <span id="insuredrelation_part-{{@$archiveinsurance->id}}" class="{{$insurance_part_dis}}">
                <div class="form-group margin-b-10">
                    {!! Form::label('LastName', 'Insured Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label  star']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 js-bootstrap-lastname-row">
                        {!! Form::text('insured_last_name',@$last_name,['class'=>'form-control js-bootstrap-lastname js-letters-caps-format','id'=>'insured_last_name-0',$readonly_class, 'autocomplete'=>'nope']) !!}
                    </div>
                </div>
                <div class="form-group margin-b-10">
                    {!! Form::label('First Name', 'Insured First Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">
                        {!! Form::text('insured_first_name',@$first_name,['class'=>'form-control js-bootstrap-firstname js-letters-caps-format','id'=>'insured_first_name-0',$readonly_class, 'autocomplete'=>'nope']) !!}
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 js-bootstrap-firstname-row">
                        {!! Form::text('insured_middle_name',@$middle_name,['class'=>'form-control dm-mi js-letters-caps-format','id'=>'insured_middle_name-0','placeholder'=>'MI',$readonly_class, 'autocomplete'=>'nope']) !!}
                    </div>
                </div>


                <div class="form-group margin-b-10">
                    {!! Form::label('Gender', 'Gender', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10" id="gender-0">
                        {!! Form::radio('gender', 'Male',($archiveinsurance->insured_gender == "Male") ? true:null,['id'=>'gender_m','class'=>'flat-red']) !!} {!! Form::label('gender_m', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('gender', 'Female',($archiveinsurance->insured_gender == "Female") ? true:null,['id'=>'gender_f','class'=>'flat-red']) !!} {!! Form::label('gender_f', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                        {!! Form::radio('gender', 'Other',($archiveinsurance->insured_gender == "Others") ? true:null,['id'=>'gender_o','class'=>'flat-red']) !!} {!! Form::label('gender_o', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!}					                                                
                    </div>                              
                </div>

                <div class="form-group margin-b-10" @if(@$registration->insured_ssn !=1) style="display:none;" @endif>
                     {!! Form::label('insured_ssn', 'Insured SSN', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                     <div class="col-lg-5 col-md-6 col-sm-5 col-xs-10">
                        {!! Form::text('insured_ssn',@$ssn,['class'=>'form-control dm-ssn','id'=>'insured_ssn-0','maxlength'=>'9',$readonly_class]) !!} 
                    </div>					  
                </div>

                <div class="form-group margin-b-10 js-insured-dob-part" @if(@$registration->insured_dob !=1) style="display:none;" @endif>
                     {!! Form::label('insured_dob', 'Insured DOB', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-10 control-label  star']) !!} 
                     <div class="col-lg-5 col-md-6 col-sm-5 col-xs-10 @if($errors->first('insured_dob')) error @endif">
                        <i class="fa fa-calendar-o form-icon"></i> 
                        {!! Form::text('insured_dob',$dob,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor js-insurance_dob dm-date','id'=>'insured_dob-0',$readonly_class, 'autocomplete'=>'nope']) !!} 
                    </div>
                </div>

                {!! Form::hidden('general_address_type','patients',['class'=>'js-address-type']) !!}
                {!! Form::hidden('general_address_type_id','',['class'=>'js-address-type-id']) !!}
                {!! Form::hidden('general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                {!! Form::hidden('general_address1',@$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                {!! Form::hidden('general_city',@$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                {!! Form::hidden('general_state',@$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                {!! Form::hidden('general_zip5',@$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                {!! Form::hidden('general_zip4',@$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                {!! Form::hidden('general_is_address_match',@$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                {!! Form::hidden('general_error_message',@$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}

                <div class="form-group margin-b-10">
                    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                        {!! Form::checkbox('same_as_patient_address', null, (@$archiveinsurance->same_patient_address=='yes'?true:null), ['class'=>"js-same_as_patient_address-v2 med-green",'id'=>'sameaddress-insurance-archive']) !!} <label for="sameaddress-insurance-archive" class="med-orange font600 no-bottom">Same as patient address</label>                       
                    </div>
                </div>                            
                <?php $same_address_class = (@$archiveinsurance->same_patient_address=='yes') ? 'hide':'show' ;?>
                <div class="form-group margin-b-10 same_address {{$same_address_class}}">
                    {!! Form::label('address1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
                        {!! Form::text('insured_address1',@$address1,['maxlength'=>'28','class'=>'form-control js-address-check js-v2-address1','id'=>'insured_address1-0','autocomplete'=>'nope']) !!}
                    </div>
                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                </div> 

                <div class="form-group margin-b-10 same_address {{$same_address_class}}">
                    {!! Form::label('address2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">                            
                        {!! Form::text('insured_address2',@$address2,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2','id'=>'insured_address2-0','autocomplete'=>'nope']) !!}
                    </div>
                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                </div>           

                <div class="form-group margin-b-10 same_address {{$same_address_class}}">
                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                        {!! Form::text('insured_city',@$city,['maxlength'=>'23','class'=>'form-control js-address-check js-v2-city','id'=>'insured_city-0','autocomplete'=>'nope']) !!}
                    </div>
                    {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 p-l-0"> 
                        {!! Form::text('insured_state',@$state,['class'=>'form-control js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'insured_state-0','autocomplete'=>'nope']) !!}
                    </div>
                </div>   
                <div class="form-group margin-b-10 same_address {{$same_address_class}}">
                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('zipcode5')) error @endif ">                             
                        {!! Form::text('insured_zip5',@$zip5,['class'=>'form-control js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'insured_zip5-0','autocomplete'=>'nope']) !!}
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 @if($errors->first('zipcode4')) error @endif ">                             
                        {!! Form::text('insured_zip4',@$zip4,['class'=>'form-control js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'insured_zip4-0','autocomplete'=>'nope']) !!}
                    </div>

                    <div class="col-md-1 col-sm-2 col-xs-2">            
                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                        <?php echo $value; ?>
                    </div> 
                </div>

            </span>

            <div class="form-group margin-b-10">
                {!! Form::label('effective_date_label', 'Effective Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10"> 
                    <i class="fa fa-calendar-o form-icon"></i>                      
                    {!! Form::text('effective_date',null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor js_datepicker dm-date','id'=>'effective_date-0', 'autocomplete'=>'off']) !!}
                </div>
            </div>

            <div class="form-group margin-b-10">
                {!! Form::label('termination_date_label', 'Termination Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">                                        
                    <i class="fa fa-calendar-o form-icon"></i> 
                    {!! Form::text('termination_date',null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor js_datepicker dm-date','id'=>'termination_date-0', 'autocomplete'=>'off']) !!}
                </div>
            </div>

        </div>

    </div><!-- /.box-body -->   
</div><!-- /.box Ends Contact Details-->

<div id="insurance-info-footer" class="modal-footer">
    <input id="js-insuranceform-submit-button-v2" data-id="js-bootstrap-validator-insurance" data-method="move" class="btn btn-medcubics-small" type="submit" value="Save">
    <button class="btn btn-medcubics-small" data-dismiss="modal" type="button">Cancel</button>
</div>
{!! Form::close() !!}