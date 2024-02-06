<input type="hidden" name="valid_npi_bootstrap" value="" />

    <?php $current_page = Route::getFacadeRoot()->current()->uri(); ?>

		<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.practice_provider") }}' />
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-20"><!--  Left side Content Starts -->

            <div class="box no-shadow">
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="address-book"></i> <h3 class="box-title">Personal Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10">

                    <div class="form-group">
                        {!! Form::label('ProviderType', 'Provider Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('provider_types_id')) error @endif">
                            <?php
								if (strpos($current_page, 'edit') === false) {
									$provider_class = ' js-provider-change-master';
								} else {
									$provider_class = ' js-provider-change-master';
								}
                            ?>
                            {!! Form::select('provider_types_id', array(''=>'-- Select --')+(array)$provider_type,  $provider_type_id,['class'=>'select2 form-control'.$provider_class,'id'=>'provider_types_id']) !!}    
                            {!! $errors->first('provider_types_id', '<p> :message</p>') !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div> 
                    @if(strpos($current_page, 'edit') === false)
                    <div class="form-group js-other-provider-options @if(Input::old('provider_types_id') == '')hide @endif">
                        {!! Form::label('', '', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('additional_provider_type')) error @endif">

                            @foreach($provider_type as $key=>$type)
                            <span class='js-other-provider-span @if((Input::old('provider_types_id') == $key) || ((Input::old('enumeration_type') == 'NPI-2') && ($key=='2'||$key=='3'||$key=='4'))) hide @endif' id='js-provider_type_{{$key}}'>{!! Form::checkbox('additional_provider_type[]',$key,null,["class" => "flat-red"])!!} {{$type}}</span>
                            @endforeach
                            {!! $errors->first('additional_provider_type', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>
                    @endif
                       <?php if(strpos($current_page, 'edit') === false  && @$group =='')
                            $disabled = '';
                        else if(!(strpos($current_page, 'edit') === false)  && @$group =='')
                            $disabled = 'disabled';
                        else
                            $disabled = '';
                         ?> 
                    <div class="form-group">
                        {!! Form::label('ProviderDOB', 'DOB', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                       
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 ">  
                            <i class="fa fa-calendar-o form-icon" onclick="iconclick('provider_dob')"></i> 
                            {!! Form::text('provider_dob',null,['id'=>'providerdob','readonly','class'=>'form-control form-cursor js_provider_dob']) !!}  
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>
                    <div class="form-group @if(Input::old('enumeration_type') == 'NPI-2') hide @endif" id="npi_field_group">
                        {!! Form::label('Gender', 'Gender'	, ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-2 control-label']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-8 @if($errors->first('gender')) error @endif" >
                            {!! Form::radio('gender', 'Male',true,['id'=>'gender_m','class'=>'flat-red js_provider_gender js_male']) !!} Male &emsp; {!! Form::radio('gender', 'Female',null,['id'=>'gender_f','class'=>'flat-red js_provider_gender',$disabled]) !!} Female &emsp; 
                            {!! Form::radio('gender', 'Others',null,['id'=>'gender_o','class'=>'flat-red js_provider_gender',$disabled]) !!} Others
                            {!! $errors->first('gender', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('SSN', 'SSN', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('ssn')) error @endif">
                            {!! Form::text('ssn',null,['class'=>'js_provider_ssn dm-ssn form-control',$disabled]) !!}  
                            {!! $errors->first('ssn', '<p> :message</p>')  !!}     
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Degree', 'Degree', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('provider_degrees_id')) error @endif">
                            {!! Form::select('provider_degrees_id', array(''=>'-- Select --')+(array)$provider_degree,  $degree_id,['class'=>'select2 form-control js_provider_degree','id'=>'provider_degrees_id',$disabled]) !!} 
                            {!! $errors->first('provider_degrees_id', '<p> :message</p>')  !!}      
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Job Title', 'Job Title', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                            {!! Form::text('job_title',null,['id'=>'job_title','class'=>'form-control',$disabled]) !!}  
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>
                    <div class=" js-address-class" id="js-address-general-address">
                        {!! Form::hidden('general_address_type','employer',['class'=>'js-address-type']) !!}
                        {!! Form::hidden('general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                        {!! Form::hidden('general_address_type_category','general_information',['class'=>'js-address-type-category']) !!}
                        {!! Form::hidden('general_address1',$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                        {!! Form::hidden('general_city',$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                        {!! Form::hidden('general_state',$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                        {!! Form::hidden('general_zip5',$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                        {!! Form::hidden('general_zip4',$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                        {!! Form::hidden('general_is_address_match',$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                        {!! Form::hidden('general_error_message',$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}

                        <div class="form-group">
                            {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                            <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('address_1')) error @endif">                                                     
                                {!! Form::text('address_1',null,['maxlength'=>'50','id'=>'address_1','class'=>'form-control js-address-check']) !!}                           
                                {!! $errors->first('address_1', '<p> :message</p>')  !!}
                            </div>
                            <div class="col-sm-1 col-xs-2"></div>
                        </div> 

                        <div class="form-group">
                            {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                            <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('address_2')) error @endif">
                                {!! Form::text('address_2',null,['maxlength'=>'50','id'=>'address_2','class'=>'form-control js-address2-tab']) !!}
                                {!! $errors->first('address_2', '<p> :message</p>')  !!}
                            </div>
                            <div class="col-sm-1 col-xs-2"></div>
                        </div> 


                        <div class="form-group">
                            {!! Form::label('City / State', 'City / State', ['class'=>'col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                                {!! Form::text('city',null,['maxlength'=>'50','class'=>'form-control js-address-check','id'=>'city']) !!}
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-2 col-xs-4"> 
                                {!! Form::text('state',null,['class'=>'form-control js-address-check js-state-tab','maxlength'=>'2','id'=>'state']) !!}
                            </div>
                        </div>   

                        <div class="form-group">
                            {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                {!! Form::text('zipcode5',null,['class'=>'form-control js-address-check dm-zip5','id'=>'zipcode5']) !!}
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-2 col-xs-4">
                                {!! Form::text('zipcode4',null,['class'=>'form-control js-address-check dm-zip4','id'=>'zipcode4']) !!}
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-2">            
                                <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green"></i></span>
                                <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
								<?php echo $value; ?>                            
                            </div> 
                        </div>
                        <div class="bottom-space-20 hidden-sm hidden-xs">&emsp;</div>
                        <div class="bottom-space-20 hidden-sm hidden-xs">&emsp;</div>
                        <div class="bottom-space-15 hidden-sm hidden-xs">&emsp;</div>
                        <div class="bottom-space-15 hidden-sm hidden-xs">&emsp;</div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->     

            <div class="box no-shadow">
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="inbox"></i> <h3 class="box-title">Credentials</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10"> 

                    <div class="form-group">
                        {!! Form::label('Medicare PTAN', 'Medicare PTAN', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('medicareptan')) error @endif">                                                     
                            {!! Form::text('medicareptan',null,['id'=>'medicareptan','class'=>'form-control dm-medicare']) !!}
                            {!! $errors->first('medicareptan', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div> 

                    <div class="form-group">
                        {!! Form::label('Medicaid ID', 'Medicaid ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('medicaidid')) error @endif">
                            {!! Form::text('medicaidid',null,['id'=>'medicaidid','class'=>'form-control dm-medicaid']) !!}
                            {!! $errors->first('medicaidid', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div> 

                    <div class="form-group">
                        {!! Form::label('BCBS_ID / Aetna_ID', 'BCBS_ID / Aetna_ID',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6 @if($errors->first('bcbsid')) error @endif @if($errors->first('aetnaid')) error @endif">  
                            {!! Form::text('bcbsid',null,['class'=>'form-control dm-bcbsid','id'=>'bcbsid']) !!}                   
                            {!! $errors->first('bcbsid', '<p> :message</p>')  !!} 	
                            {!! $errors->first('aetnaid', '<p> :message</p>')  !!} 	
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4" >
                            {!! Form::text('aetnaid',null,['class'=>'form-control dm-bcbsid']) !!}   
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('UHC_ID', 'UHC ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('uhcid')) error @endif">  
                            {!! Form::text('uhcid',null,['class'=>'form-control dm-bcbsid']) !!} 
                            {!! $errors->first('uhcid', '<p> :message</p>')  !!} 
                        </div>
                    </div>   

                    <div class="form-group">
                        {!! Form::label('Other ID', 'Other ID / Ins Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">  
                            {!! Form::text('otherid',null,['class'=>'form-control dm-bcbsid']) !!}
                        </div>    
                        <div class="col-lg-3 col-md-4 col-sm-3 col-xs-4">
                            {!! Form::select('otherid_ins', array(''=>'-- Select --')+(array)$insurances, null,['class'=>'select2 form-control','id'=>'otherid_ins']) !!} 
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Other ID', 'Other ID / Ins Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">  
                            {!! Form::text('otherid2',null,['class'=>'form-control dm-bcbsid']) !!}
                        </div>    
                        <div class="col-lg-3 col-md-4 col-sm-3 col-xs-4">
                            {!! Form::select('otherid_ins2', array(''=>'-- Select --')+(array)$insurances, null,['class'=>'select2 form-control','id'=>'otherid_ins2']) !!} 
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Other ID', 'Other ID / Ins Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">  
                            {!! Form::text('otherid3',null,['class'=>'form-control dm-bcbsid']) !!}
                        </div>    
                        <div class="col-lg-3 col-md-4 col-sm-3 col-xs-4">
                            {!! Form::select('otherid_ins3', array(''=>'-- Select --')+(array)$insurances, null,['class'=>'select2 form-control','id'=>'otherid_ins3']) !!} 
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->
        <div class="col-lg-6 col-md-6 col-xs-12 margin-t-20"><!--  Right side Content Starts -->
            <div class="box no-shadow">
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="medal"></i> <h3 class="box-title">Professional Identifications</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10 p-b-12">               
                    <div class="form-group">
                        {!! Form::label('etin_type_label', 'ETIN Type', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-2 control-label']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                            <span id="etin_type_ssn" class="">{!! Form::radio('etin_type', 'SSN',null,['class'=>'flat-red']) !!} SSN &emsp;</span> 
                            {!! Form::radio('etin_type', 'TAX ID',true,['class'=>'flat-red']) !!} TAX ID                                    
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div> 
                    <div class="form-group">
                        {!! Form::label('etin_type_number', 'SSN or TAX ID',['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('etin_type_number')) error @endif">                            
                            <!--{!! Form::text('etin_type_number',null,['data-inputmask'=>(@$provider->etin_type == 'SSN') ? '"mask": "999-99-9999"':'"mask": "99-9999999"' ,'data-mask','class'=>'form-control']) !!}    -->
                            {!! Form::text('etin_type_number',null,['class'=>(@$provider->etin_type == 'SSN') ? 'form-control dm-ssn' : 'form-control dm-tax-id']) !!} 
                            {!! $errors->first('etin_type_number', '<p> :message</p>')  !!}							
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Specialty 1', 'Specialty 1', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('speciality_id')) error @endif">
                            {!! Form::select('speciality_id', array(''=>'-- Select --')+(array)$specialities,  $speciality_id,['class'=>'select2 form-control', 'id'=>'js-speciality-change']) !!} 
                            {!! $errors->first('speciality_id', '<p> :message</p>')  !!}   
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Taxanomy 1', 'Taxonomy 1', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('taxanomy_id')) error @endif">
                            {!! Form::select('taxanomy_id', array(''=>'-- Select --')+(array)$taxanomies, $taxanomy_id, ['id' => 'taxanomies-list','class'=>'select2 form-control']) !!}
                            {!! $errors->first('taxanomy_id', '<p> :message</p>')  !!}    
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Specialty 2', 'Specialty 2', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('speciality_id2')) error @endif">
                            {!! Form::select('speciality_id2', array(''=>'-- Select --')+(array)$specialities,  $speciality_id2,['class'=>'select2 form-control', 'id'=>'js-speciality2-change']) !!}
                            {!! $errors->first('speciality_id2', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Taxanomy 2', 'Taxonomy 2', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('taxanomy_id2')) error @endif">
                            {!! Form::select('taxanomy_id2', array(''=>'-- Select --')+(array)$taxanomies2, $taxanomy_id2, ['id' => 'taxanomies2-list','class'=>'select2 form-control']) !!}
                            {!! $errors->first('taxanomy_id2', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('State License', 'State License / State', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::text('statelicense',null,['class'=>'form-control dm-bcbsid']) !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                            {!! Form::select('state_1', array(''=>'--')+(array)$states, null,['class'=>'select2 form-control','id'=>'state_1']) !!}   
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('State License', 'State License / State', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::text('statelicense_2',null,['class'=>'form-control dm-bcbsid']) !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                            {!! Form::select('state_2', array(''=>'--')+(array)$states, null,['class'=>'select2 form-control','id'=>'state_2']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('State License', 'State License / State', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::text('specialitylicense',null,['class'=>'form-control dm-bcbsid']) !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                            {!! Form::select('state_speciality', array(''=>'--')+(array)$states, null,['class'=>'select2 form-control','id'=>'state_speciality']) !!} 
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('DEA Number', 'DEA Number / State', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('deanumber')) error @endif">
                            {!! Form::text('deanumber',null,['class'=>'form-control dm-careplan']) !!}
                            {!! $errors->first('deanumber', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                            {!! Form::select('state_dea', array(''=>'--')+(array)$states, null,['class'=>'form-control select2','id'=>'state_dea']) !!} 
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('TAT', 'TAT', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('tat')) error @endif">
                            {!! Form::text('tat',null,['class'=>'form-control dm-careplan']) !!}
                            {!! $errors->first('tat', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('MammographyCert#', 'Mammography Cert#', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                            {!! Form::text('mammography',null,['class'=>'form-control dm-careplan']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('CarePlanOversight#', 'Care Plan Oversight#', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                            {!! Form::text('careplan',null,['class'=>'form-control dm-careplan']) !!}
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
            <div class="box no-shadow">
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10">

                    <div class="form-group">
                        {!! Form::label('Requires Supervision', 'Requires Supervision', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}                                                  
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">  
                            {!! Form::radio('req_super', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('req_super', 'No',true,['class'=>'flat-red']) !!} No 
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Default_Facility', 'Default Facility', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('def_facility')) error @endif">  
                            {!! Form::select('def_facility', array(''=>'-- Select --')+(array)$facilities,  $facility_id,['class'=>'select2 form-control','id'=>'def_facility']) !!}   
                            {!! $errors->first('def_facility', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Statement Address', 'Statement Address', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('stmt_add')) error @endif">  
                            {!! Form::select('stmt_add', [''=>'-- Select --','Pay to Address' => 'Pay to Address','Mailing Address' => 'Mailing Address','Primary Location' => 'Primary Location',],null,['class'=>'select2 form-control']) !!}
                            {!! $errors->first('stmt_add', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group bottom-space-15">
                        {!! Form::label('Hospice_Employed', 'Hospice Employed', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}                                                  
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">  
                            {!! Form::radio('hospice_emp', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('hospice_emp', 'No',true,['class'=>'flat-red']) !!} No 
                        </div>
                    </div>

                    <div class="form-group bottom-space-15">
                        {!! Form::label('status_label', 'Status',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}                                                  
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">  
                            {!! Form::radio('status', 'Active',true,['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red']) !!} Inactive 
                        </div>
                    </div>
					
					<div class="form-group bottom-space-15">
                        {!! Form::label('status_label', 'Provider',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">  
                            {!! Form::radio('provider_entity_type', 'Person',true,['class'=>'flat-red']) !!} Person &emsp; {!! Form::radio('provider_entity_type', 'NonPersonEntity',null,['class'=>'flat-red']) !!} Non-Person Entity 
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-upload margin-t-10">
							{!! Form::label('Digital_Signature', 'Digital Signature',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
							<div class="col-lg-6 col-md-6 col-sm-7 col-xs-6 no-padding @if($errors->first('filefield')) error @endif">
								<span class="fileContainer" style="padding:1px 20px;"> 
								{!! Form::file('digital_sign',['class'=>'default uploadFile','id'=>'digital_sign','accept'=>'image/png, image/gif, image/jpeg','style'=>'height: 30px; width: 20px;']) !!}Upload  </span>
								<span class="error" >{!! $errors->first('digital_sign',  '<p> :message</p>')  !!} </span>
								&emsp;<span class="js-display-error"></span>
                                <span><i class="fa fa-times-circle cur-pointer removeFile" data-placement="bottom" data-toggle="tooltip" title="Remove" data-original-title="Tooltip on bottom" style="display:none;"></i></span>
							</div>
						</div>
					</div>
                    
                    <div class="margin-b-5 hidden-sm hidden-xs">&emsp;</div>
                       <div class="margin-b-5 hidden-sm hidden-xs">&emsp;</div>
                    
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Right side Content Ends -->

        
            <div class="col-lg-12col-md-12 col-sm-12 col-xs-12 text-center">
                {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
                @if(strpos($current_page, 'edit') !== false && $checkpermission->check_adminurl_permission('admin/customer/{customer_id}/practice/{practice_id}/providers/{id}/delete') == 1)
                <a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure would you like to delete?" href="{{ url('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers/'.$provider->id.'/delete') }}">Delete</a></center>
                @endif

                @if(strpos($current_page, 'edit') === false)
                <a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers/')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @endif

                @if(strpos($current_page, 'edit') !== false)
                <a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers/'.$provider->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @endif

            </div>
       

<!-- Modal Light Box starts -->  
<div id="form-content" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Doing Business As</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-list">
                    <li class="nav-header">Upload</li>
                    <li><input class="input-xlarge" value="" type="file" name="upload"></li>
                    <li class="nav-header">Message</li>
                    <li><textarea class="form-control" placeholder="Description"></textarea></li>
                </ul> 
            </div>
            <div class="modal-footer">
                <button class="btn btn-medcubics-small">Submit</button>
                <a href="#" class="btn btn-medcubics-small" data-dismiss="modal">Close</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->  

<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- Modal Light Box Ends -->  

@if(strpos($current_page, 'edit') !== false)
<div id="image-content" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Digital Signature</h4>
            </div>

            <div class="modal-body">
                <center>
                    <?php
						$filename = @$provider->digital_sign_name . '.' .@$provider->digital_sign_ext;
						$unique_practice = md5('P' . $practice_name->id);
						$img_details = [];
						$img_details['module_name']='provider';
						$img_details['file_name']=$filename;
						$img_details['practice_name']=$unique_practice;
						
						$img_details['class']='img-border';
						$img_details['alt']='provider-image';
						$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
					?>
                    {!! $image_tag !!}  
                </center>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
@endif


@include ('practice/layouts/npi_form_modal')

@push('view.scripts')
<script type="text/javascript">

    $(document).on('ifChecked', "input[name='etin_type']", function () {
        if (this.value == 'SSN') {
            $("#etin_type_number").removeClass('dm-tax-id');
            $("#etin_type_number").addClass('dm-ssn');
        }
        else {
            $("#etin_type_number").removeClass('dm-ssn');
            $("#etin_type_number").addClass('dm-tax-id');
        }
    });
	
	$(document).on('ifToggled', "input[name='additional_provider_type[]']",function () {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="etin_type_number"]'));
		if($(this).val()==5){
			if($(this).is(':checked')){
				$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'etin_type_number', true);
				$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'speciality_id', true);
				$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'taxanomy_id', true);
			}
			else{
				$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'etin_type_number', false);
				$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'taxanomy_id', false);
				$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'speciality_id', false);
			}
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="etin_type_number"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="speciality_id"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="taxanomy_id"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="phone"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="address_1"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="city"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="state"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="zipcode5"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="zipcode4"]'));
		}
		if($(this).val()==2){
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="first_name"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="last_name"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="middle_name"]'));
		}
	});

    $(document).ready(function () {
		
		$('[name="address_1"],[name="address_2"],[name="state"],[name="city"],[name="zipcode5"],[name="zipcode4"],[name="last_name"],[name="first_name"],[name="middle_name"]').on( 'keyup' , function () {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="address_1"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="address_2"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="city"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="state"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="zipcode5"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="zipcode4"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="first_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="last_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="middle_name"]'));
	});

        $("#providerdob").datepicker({
            yearRange: '1900:+0',
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true,
            maxDate: '0',
            onClose: function (selectedDate) {
                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="provider_dob"]'));
            }
        });

        $('#js-bootstrap-validator')
                .bootstrapValidator({
                    excluded: ':disabled',
                    message: 'This value is not valid',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        short_name: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/provider.validation.provider_name") }}'
                                },
                                regexp: {
                                    regexp: /^[A-Za-z\s]+$/,
                                    message: '{{ trans("common.validation.alphaspace") }}'
                                },
								stringLength: {
										max:3,
										min:3,
										message: '{{ trans("common.validation.shortname_regex") }}'
								}
                            }
                        },
						organization_name: {
						     message:'',
                            enabled:false,
                            validators:{
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/provider.validation.organization_name") }}'
                                }
                            }
						},
                        last_name: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/provider.validation.lastname") }}'
                                },
                                regexp: {
                                    regexp: /^[A-Za-z\s ]+$/,
                                    message: '{{ trans("common.validation.alphaspace") }}'
                                },
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
										var lastName_value = value.trim();
                                        if(lastName_value.length !=0) {
											var return_option = referprovidernameValidation();
											if(return_option == false) {
												return {
													valid: false,
													message: '{{ trans("common.validation.provider_name_limit") }}'
												}; 
											} 
											var total_length = nameAddvalidation();
											var return_option =  (total_length>87) ? false : true;
											if(return_option == false) {
												return {
														valid: false,
														message: '{{ trans("common.validation.address_limit") }}'
													}; 
											}
										}
										return true;
                                    }
                                }
                            }
                        },
                        first_name: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/provider.validation.firstname") }}'
                                },
                                regexp: {
                                    regexp: /^[A-Za-z\s ]+$/,
                                    message: '{{ trans("common.validation.alpha_regex") }}'
                                },
								callback: {
                                    message: '',
                                    callback: function (value, validator) {
										var firstName_value = value.trim();
										if(firstName_value.length !=0) {
											var return_option = referprovidernameValidation();
											if(return_option == false) {
												return {
													valid: false,
													message: '{{ trans("common.validation.provider_name_limit") }}'
												}; 
											}
											var total_length = nameAddvalidation();
											var return_option =  (total_length>87) ? false : true;
											if(return_option == false) {
												return {
														valid: false,
														message: '{{ trans("common.validation.address_limit") }}'
													}; 
											}
										}
										return true;
                                    }
                                }
							}
                        },
                        middle_name: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var middleName_value = value.trim();
										if(middleName_value.length !=0) {
											var return_option = referprovidernameValidation();
											if(return_option == false) {
												return {
													valid: false,
													message: '{{ trans("common.validation.provider_name_limit") }}'
												}; 
											}
											var total_length = nameAddvalidation();
											var return_option =  (total_length>87) ? false : true;
											if(return_option == false) {
												return {
														valid: false,
														message: '{{ trans("common.validation.address_limit") }}'
													}; 
											}
										}
										return true;
                                    }
                                }
                            }
                        },
                        description: {
                            message: '',
                            validators: {
                            }
                        },
                        email: {
                            message: '',
                            validators: {
                               callback: {
									message: '',
									callback: function (value, validator) {
											var response = emailValidation(value);
											if(response !=true) {
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
                        website: {
							message: '',
							validators: {
								regexp: {
									regexp: /^((http|https):\/\/|(www\.))?([a-zA-Z0-9]+(\.[a-zA-Z0-9]+)+.*)$/,
									message: '{{ trans("common.validation.website_valid") }}'
								},
								callback: {
									message: '{{ trans("common.validation.website_valid") }}',
									callback: function(value, validator, $field) {
										if (value.indexOf("www") >= 0){
											if((value.endsWith(".")) == false){
												 var words = value.split('.');
												if(words.length < 3){
													$('small[data-bv-for="website"]').not('small[data-bv-validator="callback"]').css("display","none");
													return false;
												}
											}else{
												$('small[data-bv-for="website"]').not('small[data-bv-validator="callback"]').css("display","none");
												return false;
											}
										}
										return true;
									}
								}
							}
						},
                        provider_types_id: {
                            message: '',
							trigger: 'change keyup',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/provider.validation.providertype") }}'
                                }, callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        if (value == 5) {
                                            $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'etin_type_number', true);
                                            $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'speciality_id', true);
                                            $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'taxanomy_id', true);
                                        }
                                        else {
                                            $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'etin_type_number', false);
											$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'taxanomy_id', false);
                                            $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'speciality_id', false);
                                        }
                                        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="etin_type_number"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="speciality_id"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="taxanomy_id"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="phone"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="address_1"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="city"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="state"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="zipcode5"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="zipcode4"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="last_name"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="first_name"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="middle_name"]'));
										return true;
                                    }
                                }
                            }
                        },
                        ssn: {
							message:'',
                            validators: {
                                regexp: {
                                    regexp: /^[0-9]{9}$/,
                                    message: '{{ trans("admin/provider.validation.ssn") }}'
                                }
                            }
                        },
                        provider_degrees_id:{
                            message:'',
                            enabled:false,
                            validators:{
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/provider.validation.provider_degree") }}'
                                }
                            }
                        },
                        npi: {
                            trigger: 'change keyup',
                            validators: {
                                callback: {
                                    message: '{{ trans("common.validation.npi_regex") }}',
                                    callback: function (value, validator) {
                                        if (value == "") {
                                            $('input[type=hidden][name="valid_npi_bootstrap"]').val('');
                                            return {
                                                valid: false,
                                                message: '{{ trans("common.validation.npi") }}'
                                            };
                                        }
                                        else if (value.search("[0-9]{10}") == -1) {
                                            $('input[type=hidden][name="valid_npi_bootstrap"]').val('');
                                            return {
                                                valid: false,
                                                message: '{{ trans("common.validation.npi_regex") }}'
                                            };
                                        }
                                        else {
                                            if ($('input[type=hidden][name="valid_npi_bootstrap"]').val() != '') {
                                                return {
                                                    valid: false,
                                                    message: '{{ trans("common.validation.npi_validcheck") }}'
                                                };
                                            }
                                        }
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="first_name"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="last_name"]'));
										$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="middle_name"]'));
                                        return true;
                                    }
                                }
                            }
                        },
                        address_1: {
                            message: '',
                            trigger: 'change keyup',
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z0-9\s]{0,50}$/,
                                    message: '{{ trans("common.validation.address1_regex") }}'
                                },
								callback: {
                                    message: '{{ trans("common.validation.address_limit") }}',
                                    callback: function (value, validator) {
										var provider_type_billing = "no";
										if($('#provider_types_id').val() == 5) {
											provider_type_billing = "yes";
										}
										else{
											$.each($("input[name='additional_provider_type[]']:checked"), function() {
												if($(this).val()==5){
													provider_type_billing = "yes";
												}
											});
										}
										if(provider_type_billing == "yes" && value=='') {
											return {
												valid: false, 
												message: 'Enter address1'
											};
										}
										else if(value!=''){
											var add_length = nameAddvalidation();
											return (add_length>87) ? false : true;
										}
										return true;
									}
                                }
							}
                        },
                        address_2: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z0-9\s]{0,50}$/,
                                    message: '{{ trans("common.validation.address2_regex") }}'
                                }
                            }
                        },
                        city: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[A-Za-z\s]+$/,
                                    message: '{{ trans("common.validation.alpha_regex") }}'
                                },
								callback: {
                                    message: '{{ trans("common.validation.address_limit") }}',
                                    callback: function (value, validator) {
										var provider_type_billing = "no";
										if($('#provider_types_id').val() == 5) {
											provider_type_billing = "yes";
										}
										else{
											$.each($("input[name='additional_provider_type[]']:checked"), function() {
												if($(this).val()==5){
													provider_type_billing = "yes";
												}
											});
										}
										if(provider_type_billing == "yes" && value=='') {
											return {
												valid: false, 
												message: 'Enter city'
											};
										}
										else if(value!=''){
											var add_length = nameAddvalidation();
											return (add_length>87) ? false : true;
										}
										return true;
									}
                                }
							}
                        },
                        state: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[A-Za-z\s]+$/,
                                    message: '{{ trans("common.validation.alpha_regex") }}'
                                },
                                callback: {
                                    message: '{{ trans("common.validation.address_limit") }}',
                                    callback: function (value, validator) {
										var provider_type_billing = "no";
										if($('#provider_types_id').val() == 5) {
											provider_type_billing = "yes";
										}
										else{
											$.each($("input[name='additional_provider_type[]']:checked"), function() {
												if($(this).val()==5){
													provider_type_billing = "yes";
												}
											});
										}
										if(provider_type_billing == "yes" && value=='') {
											return {
												valid: false, 
												message: 'Enter state'
											};
										}
										else if(value!=''){
											var add_length = nameAddvalidation();
											return (add_length>87) ? false : true;
										}
										return true;
									}
                                }
                            }
                        },
                        zipcode5: {
                            message: 'This field is invalid',
                            validators: {
                                regexp: {
                                    regexp: /^[0-9]{5}$/,
                                    message: 'Enter valid zip code'
                                },
                                callback: {
                                    message: '{{ trans("common.validation.address_limit") }}',
                                    callback: function (value, validator) {
										var provider_type_billing = "no";
										if($('#provider_types_id').val() == 5) {
											provider_type_billing = "yes";
										}
										else{
											$.each($("input[name='additional_provider_type[]']:checked"), function() {
												if($(this).val()==5){
													provider_type_billing = "yes";
												}
											});
										}
										if(provider_type_billing == "yes" && value=='') {
											return {
												valid: false, 
												message: 'Enter zipcode'
											};
										}
										else if(value!=''){
											var add_length = nameAddvalidation();
											return (add_length>87) ? false : true;
										}
										return true;
									}
                                }
                            }
                        },
                        zipcode4: {
                            message: 'This field is invalid',
                            validators: {
                                callback: {
                                    message: '{{ trans("common.validation.address_limit") }}',
									callback: function (value, validator) {
										var provider_type_billing = "no";
										if($('#provider_types_id').val() == 5) {
											provider_type_billing = "yes";
										}
										else{
											$.each($("input[name='additional_provider_type[]']:checked"), function() {
												if($(this).val()==5){
													provider_type_billing = "yes";
												}
											});
										}
										if(provider_type_billing == "yes" && value=='') {
											return {
												valid: false, 
												message: 'Enter zipcode4'
											};
										}
										var msg = zip4Validation(value);
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										var add_length = nameAddvalidation();
										return (add_length>87) ? false : true;
									}
                                }
                            }
                        },
                        phone: {
							message: '',
							validators: {
								callback: {
                                    message: '',
                                    callback: function (value, validator) {
										var provider_type_billing = "no";
										if($('#provider_types_id').val() == 5) {
											provider_type_billing = "yes";
										}
										else{
											$.each($("input[name='additional_provider_type[]']:checked"), function() {
												if($(this).val()==5){
													provider_type_billing = "yes";
												}
											});
										}
										if(provider_type_billing == "yes" && value=='') {
											return {
												valid: false, 
												message: 'Enter phone number'
											};
										}
										else if(value!=''){
											var phone_msg = '{{ trans("common.validation.phone_limit") }}';
											var ext_msg = '{{ trans("common.validation.phone") }}';
											$fields = validator.getFieldElements('phone');
											var ext_length = $fields.closest("div").next().next().find("input").val().length;
											var response = phoneValidation(value,phone_msg,ext_length,ext_msg);
											if(response !=true) {
												return {
													valid: false, 
													message: response
												};
											}
											return true;
										}
										return true;
									}
                                }
							}
						},
                        etin_type_number: {
                            message: '',
                            enabled: true,
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
										if((value.length == 9) && (value == 000000000 || value == 999999999)){
											return {
												valid: false,
												message: '{{ trans("common.validation.taxid_validcheck") }}'
											}
										}
										var provider_type_billing = "no";
										if($('#provider_types_id').val() == 5) {
											provider_type_billing = "yes";
										}
										else{
											$.each($("input[name='additional_provider_type[]']:checked"), function() {
												if($(this).val()==5){
													provider_type_billing = "yes";
												}
											});
										}
										if(provider_type_billing == "yes" && value=='') {
											return {
												valid: false, 
												message: '{{ trans("practice/practicemaster/provider.validation.etin_type_number") }}'
											};
										}
										else if(value!=''){
											var etin_type_val = $('input[type=radio][name="etin_type"]:checked').val();
											if (etin_type_val == "SSN") {
												if (value.search("[0-9]{9}") == -1) {
													return {
														valid: false, 
														message: '{{ trans("admin/provider.validation.etin_type") }}'
													};
												}
											}
											else if (etin_type_val == "TAX ID") {
												if (value.search("[0-9]{9}") == -1) {
													return {
														valid: false, 
														message: '{{ trans("admin/provider.validation.etin_type") }}'
													};
												}
											}
										}
										return true;
									}
                                }
                            }
                        },
                        medicareptan: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z0-9]{0,15}$/,
                                    message: '{{ trans("common.validation.alphanumericspac") }}'
                                }
                            }
                        },
                        medicaidid: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z0-9]{0,15}$/,
                                    message: '{{ trans("common.validation.alphanumericspac") }}'
                                }
                            }
                        },
                        fax: {
							message: '',
							validators: {
								callback: {
									message:'',
									callback: function (value, validator) {
										var fax_msg = '{{ trans("common.validation.fax_limit") }}';
										var response = phoneValidation(value,fax_msg);
										if(response !=true) {
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
                        speciality_id: {
                            message: '',
                            enabled: true,
                            validators: {
								callback: {
                                    message: '',
                                    callback: function (value, validator) {
										var provider_type_billing = "no";
										if($('#provider_types_id').val() == 5) {
											provider_type_billing = "yes";
										}
										else{
											$.each($("input[name='additional_provider_type[]']:checked"), function() {
												if($(this).val()==5){
													provider_type_billing = "yes";
												}
											});
										}
										if(provider_type_billing == "yes" && value=='') {
											return {
												valid: false, 
												message: '{{ trans("practice/practicemaster/provider.validation.speciality_id") }}'
											};
										}
										return true;
									}
                                }
							}
                        },
                        taxanomy_id: {
                            message: '',
                            enabled: true,
                            validators: {
								callback: {
                                    message: '',
                                    callback: function (value, validator) {
										var provider_type_billing = "no";
										if($('#provider_types_id').val() == 5) {
											provider_type_billing = "yes";
										}
										else{
											$.each($("input[name='additional_provider_type[]']:checked"), function() {
												if($(this).val()==5){
													provider_type_billing = "yes";
												}
											});
										}
										if(provider_type_billing == "yes" && value=='') {
											return {
												valid: false, 
												message: '{{ trans("practice/practicemaster/provider.validation.taxanomy_id") }}'
											};
										}
										return true;
									}
                                }
                            }
                        },
                        provider_dob: {
                            message: '',
                            validators: {
                                date: {
                                    format: 'MM/DD/YYYY',
                                    message: '{{ trans("common.validation.date_format") }}'
                                },
                                callback: {
                                    message: '{{ trans("common.validation.date_format") }}',
                                    callback: function (value, validator, $field) {
                                        var dob = $('#js-bootstrap-validator').find('[name="provider_dob"]').val();
                                        var current_date = new Date(dob);
                                        var d = new Date();
                                        return (dob != '' && d.getTime() < current_date.getTime()) ? false : true;
                                    }
                                }
                            }
                        },
						job_title: {
							message: '',
							validators: {
								regexp: {
									regexp: /^[a-zA-Z0-9\s ]{0,50}$/,
									message: '{{ trans("common.validation.alphanumericspac") }}'
								}
							}
						},
						digital_sign: {
							validators: {
								file: {
									extension: 'png,jpg,jpeg',
									type: 'image/png,image/jpg,image/jpeg',
									maxSize: 1024*1024, // 5 MB
									message: 'The selected file is not valid, it should be (png, jpg) and 1 MB at maximum.'
								}
							}
						}
                    }
                });

        var org_name = $('#organization_name').val();
		if(org_name==''){
			$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'provider_degrees_id',true);
		}
		
		$('[name="etin_type_number"]').on('change', function () {
            $('#js-bootstrap-validator')
				.data('bootstrapValidator')
				.updateStatus('etin_type_number', 'NOT_VALIDATED')
				.validateField('etin_type_number');
        });

        $('[name="ssn"]').on('change', function () {
            $('#js-bootstrap-validator')
				.data('bootstrapValidator')
				.updateStatus('ssn', 'NOT_VALIDATED')
				.validateField('ssn');
        });

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

        $('[name="npi"]').on('change', function () {
            $('#js-bootstrap-validator')
				.data('bootstrapValidator')
				.updateStatus('npi', 'NOT_VALIDATED')
				.validateField('npi');
        });
		
        $('[name="provider_types_id"]').on('change', function () {
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="etin_type_number"]'));
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="speciality_id"]'));
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="taxanomy_id"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="phone"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="address_1"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="city"]'));
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="state"]'));
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="zipcode5"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="last_name"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="first_name"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="middle_name"]'));
        });
    });
	
function referprovidernameValidation() {
	var provider_type_refer = "no";
	var sel_provider_types_id = $('#provider_types_id').val();
	if(sel_provider_types_id==2){
		provider_type_refer = "yes";
	}
	$.each($("input[name='additional_provider_type[]']:checked"), function() {
		if($(this).val()==2){
			provider_type_refer = "yes";
		}
	});
	var lastName 	= $("#last_name").val();
	var firstName 	= $("#first_name").val();
	var middleName 	= $("#middle_name").val();
	var lastName_value 	= lastName.trim();
	var firstName_value = firstName.trim();
	var middleName_value = middleName.trim();
	var add_length = lastName_value.length + middleName_value.length + firstName_value.length ;
	var return_option = (add_length>24 && provider_type_refer=='yes') ? false : true;
	return return_option;
}	

function nameAddvalidation() {
	var address1 = $("#address_1").val();
	var city = $("#city").val();
	var state = $("#state").val();
	var zip5 = $("#zipcode5").val();
	var zip4 = $("#zipcode4").val();
	var last_name = $("#last_name").val();
	var first_name = $("#first_name").val();
	var middle_name = $("#middle_name").val();
	
	var address1_value = address1.trim();
	var city_value = city.trim();
	var state_value = state.trim();
	var zip5_value = zip5.trim();
	var zip4_value = zip4.trim();
	
	var lst = last_name.trim();
	var fst = first_name.trim();
	var mid = middle_name.trim();
	var add_length = lst.length +fst.length +mid.length +address1_value.length + city_value.length + state_value.length + zip5_value.length + zip4_value.length ;
	return add_length;
}
	
</script> 
@endpush