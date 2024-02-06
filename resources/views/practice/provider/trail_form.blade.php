<input type="hidden" name="valid_npi_bootstrap" value="" />
	<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.provider_details") }}' />
    <?php 
        if(!isset($get_default_timezone)){
           $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
        }                  
    ?>
    <div id="is_provider"></div>
    <?php $current_page = Route::getFacadeRoot()->current()->uri(); ?>
    {!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
           
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-20"><!--  Left side Content Starts -->
 
            <div class="box no-shadow margin-b-10"><!-- Personal Information Box Starts -->
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="address-book"></i> <h3 class="box-title">Personal Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10 p-b-20"><!-- Box Body Starts -->
                    <?php
                    /*if ($npi_flag['enumeration_type'] == 'NPI-2' || Input::old('enumeration_type') == 'NPI-2')
                        $provider_type_id = 5;*/
					if ($provider_type_id!='') 
						$provider_type_id_val_chk = $provider_type_id;
					else
						$provider_type_id_val_chk = 0;
                    ?>
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="form-group">
						
                        {!! Form::label('ProviderType', 'Provider Type', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('provider_types_id')) error @endif">
                            <?php
                            if (strpos($current_page, 'edit') === false) {
                                $provider_class = ' js-provider-change';
                            } else {
                                $provider_class = '';
                            }
							?>
                            @if($provider_type_id != 1 && $provider_type_id != 5)
                            @if($npi_flag['enumeration_type'] == 'NPI-2' || Input::old('enumeration_type') == 'NPI-2')
                            {!! Form::select('provider_types_id', array(''=>'-- Select --')+(array)$provider_type,  $provider_type_id,['class'=>'select2 form-control'.$provider_class,'id'=>'provider_types_id','disabled'=>'disabled']) !!}
                            @else
                            {!! Form::select('provider_types_id', array(''=>'-- Select --')+(array)$provider_type,  $provider_type_id,['class'=>'select2 form-control'.$provider_class,'id'=>'provider_types_id']) !!}
                            @endif
                            @elseif($provider_type_id == 1)
                            {!! Form::select('provider_types_id', array($provider_type_id =>'Rendering'),  $provider_type_id,['class'=>'select2 form-control','id'=>'provider_types_id']) !!}
                            @elseif($provider_type_id == 5)
                            {!! Form::select('provider_types_id', array($provider_type_id =>'Billing'),  $provider_type_id,['class'=>'select2 form-control','id'=>'provider_types_id']) !!}
                            @endif
                            {!! $errors->first('provider_types_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>
                    <?php if(!(strpos($current_page, 'edit') === false)  && @$group =='')
                            $disabled = 'disabled';
                        else
                            $disabled = ''; ?>
                    @if(strpos($current_page, 'edit') === false)
                    <div class="form-group js-other-provider-options @if(Input::old('provider_types_id') == '')hide @endif">
                        {!! Form::label('', '', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10 @if($errors->first('additional_provider_type')) error @endif">
                            @foreach($provider_type as $key=>$type)
                            <span class='js-other-provider-span @if(Input::old('provider_types_id') == $key) hide @endif' id='js-provider_type_{{$key}}'>{!! Form::checkbox('additional_provider_type[]',$key,null,["class" => "flat-red"])!!} {{$type}}</span>
                            @endforeach
                            {!! $errors->first('additional_provider_type', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
                    @endif

                    <div class="form-group margin-b-20">
                        {!! Form::label('ProviderDOB', 'DOB', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 ">
                            <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i>   {!! Form::text('provider_dob',null,['id'=>'dateofbirth','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control dm-date form-cursor',$disabled]) !!}  
                        </div>                        
                    </div>

                    <div class="form-group margin-b-20">
                        {!! Form::label('Gender', 'Gender', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-3 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-7 @if($errors->first('gender')) error @endif">
                            {!! Form::radio('gender', 'Male',true,['id'=>'gender_m','class'=>'']) !!} {!! Form::label('gender_m', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                            {!! Form::radio('gender', 'Female',null,['id'=>'gender_f','class'=>'',$disabled]) !!} {!! Form::label('gender_f', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                            {!! Form::radio('gender', 'Others',null,['id'=>'gender_o','class'=>'',$disabled]) !!} {!! Form::label('gender_o', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!}
                            {!! $errors->first('gender', '<p> :message</p>')  !!}
                        </div>                        
                    </div>

                    <div class="form-group">
                        {!! Form::label('SSN', 'SSN', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('ssn')) error @endif">
                            {!! Form::text('ssn',null,['class'=>'form-control dm-ssn','autocomplete'=>'nope',$disabled]) !!}
							{!! $errors->first('ssn', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <a id="document_add_modal_link_ssn" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/personal_ssn')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/personal_ssn')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_personal_ssn->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Degree', 'Degree', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('provider_degrees_id')) error @endif">
                            {!! Form::select('provider_degrees_id', array(''=>'-- Select --')+(array)$provider_degree,  $degree_id,['class'=>'select2 form-control','id'=>'provider_degrees_id',$disabled]) !!}
                            {!! $errors->first('provider_degrees_id', '<p> :message</p>')  !!}
                        </div>                        
                    </div>

                    <div class="form-group">
                        {!! Form::label('Job Title', 'Job Title', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                            {!! Form::text('job_title',null,['id'=>'job_title','class'=>'form-control', 'maxlength'=>50,'autocomplete'=>'nope',$disabled]) !!}
                        </div>                        
                    </div>
                    <div class=" js-address-class" id="js-address-general-address"><!-- Address Div Starts -->
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
                            {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                            <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('address_1')) error @endif">
                                {!! Form::text('address_1',null,['id'=>'address_1','class'=>'form-control js-address-check dm-address','autocomplete'=>'nope']) !!}
                                {!! $errors->first('address_1', '<p> :message</p>')  !!}
                            </div>                           
                        </div>

                        <div class="form-group">
                            {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                            <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('address_2')) error @endif">
                                {!! Form::text('address_2',null,['id'=>'address_2','class'=>'form-control js-address2-tab dm-address','autocomplete'=>'nope']) !!}
                                {!! $errors->first('address_2', '<p> :message</p>')  !!}
                            </div>                           
                        </div>


                        <div class="form-group">
                            {!! Form::label('City / State', 'City ', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                            <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                                {!! Form::text('city',null,['class'=>'form-control js-address-check dm-address','id'=>'city','autocomplete'=>'nope']) !!}
                            </div>
                            {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                                {!! Form::text('state',null,['class'=>'form-control js-address-check dm-state js-state-tab','id'=>'state','autocomplete'=>'nope']) !!}
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
                                {!! Form::text('zipcode5',null,['class'=>'form-control js-address-check dm-zip5','id'=>'zipcode5','autocomplete'=>'nope']) !!}
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-2 col-xs-4">
                                {!! Form::text('zipcode4',null,['class'=>'form-control js-address-check dm-zip4','id'=>'zipcode4','autocomplete'=>'nope']) !!}
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-2">
                                <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                <span class="js-address-success @if($address_flag['general']['is_address_match'] != 'Yes') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                                <span class="js-address-error @if($address_flag['general']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                                <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
                                <?php echo $value; ?> 
                            </div>
                        </div>
                        <div class="bottom-space-20 hidden-sm hidden-xs">&emsp;</div>
                        <div class="margin-b-23 hidden-sm hidden-xs">&emsp;</div>
                    </div><!-- Address Div Ends -->
                </div><!-- /.box-body -->
            </div><!-- Personal Information Box Ends -->

            <div class="box no-shadow margin-b-10 hide"><!-- Credentials Box Starts -->
                <div class="box-block-header with-border margin-b-10">
                    <i class="livicon" data-name="shield"></i> <h3 class="box-title">Enrollment Status</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10 p-b-25"><!-- Box Body Starts -->
                    <div class="form-group">
                        {!! Form::label('Medicare PTAN', 'Medicare PTAN', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('medicareptan')) error @endif">
                            {!! Form::text('medicareptan',null,['id'=>'medicareptan','class'=>'form-control dm-medicare','autocomplete'=>'nope']) !!}
                            {!! $errors->first('medicareptan', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <a id="document_add_modal_link_medicare_ptan" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/medicare_ptan')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/medicare_ptan')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_PTAN->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Medicaid ID', 'Medicaid ID', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('medicaidid')) error @endif">
                            {!! Form::text('medicaidid',null,['id'=>'medicaidid','class'=>'form-control dm-medicaid','autocomplete'=>'nope']) !!}
                            {!! $errors->first('medicaidid', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <a id="document_add_modal_link_medicaid_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/medicaid_id')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/medicaid_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_medicaid_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('BCBS_ID ', 'BCBS ID',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('bcbsid')) error @endif @if($errors->first('aetnaid')) error @endif">
                            {!! Form::text('bcbsid',null,['class'=>'form-control dm-bcbsid','id'=>'bcbsid','autocomplete'=>'nope']) !!}                            
                            {!! $errors->first('bcbsid', '<p> :message</p>')  !!}                            
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <a id="document_add_modal_link_bcbs_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/bcbs_id')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/bcbs_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_bcbs_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Aetna_ID', 'Aetna ID',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('uhcid')) error @endif">
                            {!! Form::text('aetnaid',null,['class'=>'form-control dm-bcbsid','autocomplete'=>'nope']) !!}       
                            {!! $errors->first('aetnaid', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <a id="document_add_modal_link_aetna_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/aetna_id')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/aetna_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_aetna_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('UHC_ID', 'UHC ID', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('uhcid')) error @endif">
                            {!! Form::text('uhcid',null,['class'=>'form-control dm-bcbsid','autocomplete'=>'nope']) !!}
                            {!! $errors->first('uhcid', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <a id="document_add_modal_link_uhc_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/uhc_id')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/uhc_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_uhc_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Other ID', 'Other ID 1', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10">
                            {!! Form::text('otherid',null,['class'=>'form-control dm-bcbsid','autocomplete'=>'nope']) !!}
                        </div>

                        {!! Form::label('Ins Type', 'Ins', ['class'=>'col-lg-1 col-md-4 col-sm-1 col-xs-12 m-t-md-5 control-label']) !!}
                        <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10 m-t-md-5">
                            {!! Form::select('otherid_ins', array(''=>'-- Select --')+(array)$insurances, null,['class'=>'select2 form-control','id'=>'otherid_ins']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2 m-t-md-5 p-l-0">
                            <a id="document_add_modal_link_other_id1" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/other_id1')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/other_id1')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_other_id1->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>                        
                    </div>

                    <div class="form-group">
                        {!! Form::label('Other ID', 'Other ID 2', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12  control-label']) !!}
                        <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10">
                            {!! Form::text('otherid2',null,['class'=>'form-control dm-bcbsid','autocomplete'=>'nope']) !!}
                        </div>
                        {!! Form::label('Ins Type', 'Ins', ['class'=>'col-lg-1 col-md-4 col-sm-1 col-xs-12 m-t-md-5 control-label']) !!}
                        <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10 m-t-md-5">
                            {!! Form::select('otherid_ins2', array(''=>'-- Select --')+(array)$insurances, null,['class'=>'select2 form-control','id'=>'otherid_ins2']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2 m-t-md-5 p-l-0">
                            <a id="document_add_modal_link_other_id2" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/other_id2')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/other_id2')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_other_id2->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Other ID', 'Other ID 3', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10">
                            {!! Form::text('otherid3',null,['class'=>'form-control dm-bcbsid','autocomplete'=>'nope']) !!}
                        </div>
                        {!! Form::label('Ins Type', 'Ins', ['class'=>'col-lg-1 col-md-4 col-sm-1 col-xs-12 m-t-md-5 control-label']) !!}
                        <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10 m-t-md-5">
                            {!! Form::select('otherid_ins3', array(''=>'-- Select --')+(array)$insurances, null,['class'=>'select2 form-control','id'=>'otherid_ins3']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2 m-t-md-5 p-l-0">
                            <a id="document_add_modal_link_other_id3" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/other_id3')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/other_id3')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_other_id3->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->
        
        <div class="col-lg-6 col-md-6 col-xs-12 margin-t-20"><!--  Right side Content Starts -->
            <div class="box no-shadow margin-b-10"><!-- Professional Identifications Box Starts -->
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="medal"></i> <h3 class="box-title">Professional Identifications</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10 p-b-12"><!-- Box Body Starts -->
                    <div class="form-group">
                        {!! Form::label('etin_type', 'ETIN Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-2 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                            {!! Form::radio('etin_type', 'SSN',null,['class'=>' etin_type','id'=>'etin_ssn']) !!} {!! Form::label('etin_ssn', 'SSN',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                            {!! Form::radio('etin_type', 'TAX ID',true,['class'=>' etin_type','id'=>'etin_tax']) !!} {!! Form::label('etin_tax', 'TAX ID',['class'=>'med-darkgray font600 form-cursor']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('etin_type_number', 'SSN or TAX ID',['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 import_etintype @if($errors->first('etin_type_number')) error @endif">
                            @if(strpos($current_page, 'edit') !== false)
                            <span class="etin_type_number">
                                @if($provider->etin_type=='SSN')
                                {!! Form::text('etin_type_number',null,['class'=>'dm-ssn form-control','autocomplete'=>'nope']) !!}

                                @else
                                {!! Form::text('etin_type_number',null,['class'=>'form-control dm-tax-id','autocomplete'=>'nope']) !!}
                                @endif
                            </span>
                            @else
                            {!! Form::text('etin_type_number',null,['class'=>'form-control dm-tax-id','autocomplete'=>'nope']) !!}
                            @endif
                            {!! $errors->first('etin_type_number', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            @if(strpos($current_page, 'edit') !== false)
                            @if($provider->etin_type=='SSN')
                            <?php $add_modal_doc_var = "ssn"; ?>
                            @else
                            <?php $add_modal_doc_var = "tax_id"; ?>
                            @endif
                            @else
                            <?php $add_modal_doc_var = "tax_id"; ?>
                            @endif

                            <span id="document_add_modal_link_ssn_part" @if($add_modal_doc_var=='tax_id') style="display:none;" @endif>
                                  <a id="document_add_modal_link_ssn" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/professional_ssn')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/professional_ssn')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
                            </span>

                            <span id="document_add_modal_link_tax_id_part" @if($add_modal_doc_var=='ssn') style="display:none;" @endif>
                                  <a id="document_add_modal_link_tax_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/tax_id')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/tax_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_tax_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                            </span>

                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Specialty 1', 'Specialty 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('speciality_id')) error @endif">
                            {!! Form::select('speciality_id', array(''=>'-- Select --')+(array)$specialities,  $speciality_id,['class'=>'select2 form-control', 'id'=>'js-speciality-change']) !!}
                            {!! $errors->first('speciality_id', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Taxonomy 1', 'Taxonomy 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('taxanomy_id')) error @endif">

                            {!! Form::select('taxanomy_id', array(''=>'-- Select --')+(array)$taxanomies, $taxanomy_id, ['id' => 'taxanomies-list','class'=>'select2 form-control']) !!}
                            {!! $errors->first('taxanomy_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Specialty 2', 'Specialty 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('speciality_id2')) error @endif">
                            {!! Form::select('speciality_id2', array(''=>'-- Select --')+(array)$specialities,  $speciality_id2,['class'=>'select2 form-control', 'id'=>'js-speciality2-change']) !!}
                            {!! $errors->first('speciality_id2', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Taxonomy 2', 'Taxonomy 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('taxanomy_id2')) error @endif">

                            {!! Form::select('taxanomy_id2', array(''=>'-- Select --')+(array)$taxanomies2, $taxanomy_id2, ['id' => 'taxanomies2-list','class'=>'select2 form-control']) !!}
                            {!! $errors->first('taxanomy_id2', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('State License', 'State License 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                            {!! Form::text('statelicense',null,['class'=>'form-control dm-bcbsid','autocomplete'=>'nope']) !!}
                        </div>
                        {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                            {!! Form::select('state_1', array(''=>'--')+(array)$states, null,['class'=>'select2 form-control','id'=>'state_1']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2 p-l-0">
                            <a id="document_add_modal_link_state_license1" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/state_license1')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/state_license1')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_state_license1->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('State License', 'State License 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                            {!! Form::text('statelicense_2',null,['class'=>'form-control dm-bcbsid','autocomplete'=>'nope']) !!}
                        </div>
                        {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                            {!! Form::select('state_2', array(''=>'--')+(array)$states, null,['class'=>'select2 form-control','id'=>'state_2']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2 p-l-0">
                            <a id="document_add_modal_link_state_license2" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/state_license2')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/state_license2')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_state_license2->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('State License', 'State License 3', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                            {!! Form::text('specialitylicense',null,['class'=>'form-control dm-bcbsid','autocomplete'=>'nope']) !!}
                        </div>
                        {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                            {!! Form::select('state_speciality', array(''=>'--')+(array)$states, null,['class'=>'select2 form-control','id'=>'state_speciality']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2 p-l-0">
                            <a id="document_add_modal_link_state_license3" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/state_license3')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/state_license3')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_state_license3->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('DEA Number', 'DEA Number', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6 @if($errors->first('deanumber')) error @endif">
                            {!! Form::text('deanumber',null,['class'=>'form-control dm-careplan']) !!}
                            {!! $errors->first('deanumber', '<p> :message</p>')  !!}
                        </div>
                        {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                            {!! Form::select('state_dea', array(''=>'--')+(array)$states, null,['class'=>'form-control select2','id'=>'state_dea']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2 p-l-0">
                            <a id="document_add_modal_link_dea_number" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/dea_number')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/dea_number')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_dea_number->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('TAT', 'TAT', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('tat')) error @endif">
                            {!! Form::text('tat',null,['class'=>'form-control dm-careplan','autocomplete'=>'nope']) !!}
                            {!! $errors->first('tat', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('MammographyCert#', 'Mammography Cert#', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                            {!! Form::text('mammography',null,['class'=>'form-control dm-careplan','autocomplete'=>'nope']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2 p-l-0">
                            <a id="document_add_modal_link_mammography_cert" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/mammography_cert')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/mammography_cert')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_mammography_cert->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('CarePlanOversight#', 'Care Plan Oversight#', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                            {!! Form::text('careplan',null,['class'=>'form-control dm-careplan','autocomplete'=>'nope']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2 p-l-0">
                            <a id="document_add_modal_link_care_plan" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider_id.'/care_plan')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/care_plan')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_care_plan_oversight->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
            <div class="box no-shadow margin-b-10 hide"><!-- General Information Box Starts -->
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10 p-b-20"><!-- Box Body Starts -->
                    <div class="form-group margin-b-20">
                        {!! Form::label('Requires Supervision', 'Requires Supervision', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                            {!! Form::radio('req_super', 'Yes',null,['class'=>'','id'=>'c-r-y']) !!} {!! Form::label('c-r-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                            {!! Form::radio('req_super', 'No',true,['class'=>'','id'=>'c-r-n']) !!} {!! Form::label('c-r-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Default_Facility', 'Default Facility', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-10 @if($errors->first('def_facility')) error @endif">
                            {!! Form::select('def_facility', array(''=>'-- Select --')+(array)$facilities,  $facility_id,['class'=>'select2 form-control','id'=>'def_facility']) !!}
                            {!! $errors->first('def_facility', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Statement Address', 'Statement Address', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-10 @if($errors->first('stmt_add')) error @endif">
                            {!! Form::select('stmt_add', [''=>'-- Select --','Pay to Address' => 'Pay to Address','Mailing Address' => 'Mailing Address','Primary Location' => 'Primary Location',],null,['class'=>'select2 form-control']) !!}
                            {!! $errors->first('stmt_add', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group margin-b-20">
                        {!! Form::label('Hospice_Employed', 'Hospice Employed', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                            {!! Form::radio('hospice_emp', 'Yes',null,['class'=>'','id'=>'c-h-y']) !!} {!! Form::label('c-h-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                            {!! Form::radio('hospice_emp', 'No',true,['class'=>'','id'=>'c-h-n']) !!} {!! Form::label('c-h-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                        </div>
                    </div>
					<div class="form-group margin-b-15">
                        {!! Form::label('status', 'Status',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                            {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
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
                    
                    <div class="bottom-space-10 hidden-sm hidden-xs">&emsp;</div>
                    <div class="margin-b-6 hidden-sm hidden-xs">&emsp;</div>                        
                    
                </div><!-- /.box-body -->
            </div><!-- General Information box Ends-->
        </div><!--  Right side Content Ends -->

        
		<div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
			{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
			@if(strpos($current_page, 'edit') !== false)
			@if($checkpermission->check_url_permission('provider/{provider_id}/delete') == 1)
			<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure to delete the entry?" href="{{ url('provider/'.$provider_id.'/delete') }}">Delete</a></center>
			@endif
			
			<a href="javascript:void(0)" data-url="{{ url('provider/'.$provider_id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif

			@if(strpos($current_page, 'edit') == false)
			<a href="javascript:void(0)" data-url="{{ url('provider') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif
		</div>
        
    
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
						$filename = $provider->digital_sign_name . '.' . $provider->digital_sign_ext;
                        if(!empty($filename)){
						$img_details = [];
						$img_details['module_name']='provider';
						$img_details['file_name']=$filename;
						$img_details['practice_name']="";
						
						$img_details['class']='img-border';
						$img_details['alt']='provider-image';
						$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
    					echo $image_tag;
                        }
					?>
                </center>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
@endif

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
                    <li><textarea class="form-control" placeholder="Description">
                        </textarea></li>
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
    @include ('practice/layouts/usps_form_modal')
</div><!-- Modal Light Box Ends -->
@include ('practice/layouts/npi_form_modal')

@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function(){
		$(".js-address-check").trigger("blur");
	});
	var base = "{{URL::to('/')}}";
	avator_url = base+"/img/noimage.jpg";
    $(document).on('change', '.btn-file input[type="file"]', function (e) {
    		e.preventDefault();
    		setTimeout(function(){
    			var new_file = $(".fileupload").hasClass('fileupload-new'); 
    			if(new_file) {
    				$(".js-delete-confirm").addClass('hide'); 
    			}
    			else {
    				$(".js-delete-confirm").removeClass('hide'); 
    			}
    		}, 10);    		
    	});
	
	$(document).on('change', '.btn-file input[type="file"]', function (e) {
    	if($(this).val() ==""){
    		$(".fileupload.fileupload-exists .fileupload-preview").find("img").attr('src', $(".fileupload .js_default_img").attr('src'));
    	}
		var img_file = $(this).val();
		img_file = img_file.split(".");
		var file_type = img_file[img_file.length-1];
		if((file_type !="jpg") || (file_type !="png") || (file_type !="jpeg") )
		{
			$('.fileupload-preview').html('<img class="js_default_img" src="'+avator_url+'">');
			$( ".thumbnail .fileupload-preview" ).html();
			//$(".js_default_img").removeClass('hide')
		}
		e.preventDefault();
		setTimeout(function(){
			var new_file = $(".fileupload").hasClass('fileupload-new'); 
			var value = $(".fileupload").val();
			if(new_file) {
				$(".fileupload .js-delete-confirm").addClass('hide'); 
			}
			else {
				$(".fileupload .js-delete-confirm").removeClass('hide'); 
			}
		}, 50);		
	});

	$(document).on('click', '.confirm', function (e) {
		if ($(this).text() == 'Yes') {
			var new_file = $(".fileupload").hasClass('fileupload-new');
			if(new_file) {
				$(".fileupload.fileupload-new img").attr('src', $(".fileupload .js_default_img").attr('src'));
				$(".fileupload .js-delete-confirm").addClass('hide'); 
				$(".safari_rounded img").addClass('default'); 
				$(".fileupload.fileupload-new .fileupload-preview").html('<input type="hidden" name="imagefile" value="" >');
			}
			else {
				if($(".safari_rounded img").hasClass('default')) {
					$(".fileupload .js-delete-confirm").addClass('hide'); 
					$(".fileupload").addClass('fileupload-new').removeClass("fileupload-exists");
					$('[name="avatar_url"]').val("");
					$('.fileupload-preview').html('<img class="js_default_img" src="'+avator_url+'">');
				}
				else {
					$('[name="avatar_url"]').val("");
					$(".fileupload").addClass('fileupload-new').removeClass("fileupload-exists");
				}
				$(".fileupload fileupload-preview.fileupload-exists.thumbnail").html('<img class="js_default_img" src="'+avator_url+'">');
			}
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
  
    var eventDates = {};
    eventDates[ new Date( '<?php echo $get_default_timezone; ?>' )] = new Date( '<?php echo $get_default_timezone; ?>' );
	$( "#dateofbirth" ).datepicker({
		yearRange:'1900:+0',
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		maxDate: new Date( '<?php echo $get_default_timezone; ?>' ),  
        beforeShowDay: function(d) {
        setTimeout(function() {
        $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
         }, 10);

        var highlight = eventDates[d];
            if( highlight ) {
                 return [true, "ui-state-highlight", ''];
            } else {
               
                 return [true, '', ''];
            }
        },
		onClose: function (selectedDate) {
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="provider_dob"]'));
		}
	});
		
	$('#js-bootstrap-validator')
    .bootstrapValidator({
		excluded: ':disabled',
		message: 'This value is not valid',
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			short_name: {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: '{{ trans("practice/practicemaster/provider.validation.short_name") }}'
					},
					callback: {
						message: '{{ trans("common.validation.shortname_regex") }}',
						callback: function (value, validator) {
							var get_val = validator.getFieldElements('short_name').val();
							if (get_val != '' && get_val.length < 3 ) 
								return false;
							return true;
						}
					}
				}
			},
			last_name: {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: '{{ trans("practice/practicemaster/provider.validation.last_name") }}'
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
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
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: '{{ trans("practice/practicemaster/provider.validation.first_name") }}'
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: '{{ trans("common.validation.alphaspace") }}'
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
				trigger: 'change keyup',
				validators: {
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: '{{ trans("common.validation.alpha") }}'
					},
					callback: {
						message: 'Name allowed 24 characters',
						callback: function (value, validator) {
							var middle_name_value = value.trim();
							if(middle_name.length !=0) {
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
			image: {
				validators: {
					file: {
						extension: 'png,jpg,jpeg',
						type: 'image/png,image/jpg,image/jpeg',
						maxSize: 1024*1024, // 1 MB
						message: '{{ trans("common.validation.image_maxsize_valid") }}'
					}
				}
			},
			address_1: {
				message: '',
				trigger: 'change keyup',
				validators: {
					/* regexp: {
						regexp: /^[a-zA-Z0-9\s ]{0,50}$/,
						message: '{{ trans("common.validation.alphanumericspac") }}'
					}, */
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
				 /* validators: {
					regexp: {
						regexp: /^[a-zA-Z0-9\s]{0,50}$/,
						message: '{{ trans("common.validation.alphanumericspac") }}'
					}
				 } */
			},
			city: {
				message: '',
				validators: {
					regexp: {
						regexp: /^[A-Za-z ]+$/,
						message: '{{ trans("common.validation.alphaspace") }}'
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
						regexp: /^[A-Za-z]{2}$/,
						message: '{{ trans("common.validation.state_limit") }}'
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
						message: '{{ trans("common.validation.zipcode5_limit") }}'
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
					message: '',
					validators: {
					 regexp: {
						 regexp: /^[0-9]{4}$/,
						 message: '{{ trans("common.validation.zipcode4_limit") }}'
					},
					callback: {
						message: '{{ trans("common.validation.address_limit") }}',
						callback: function (value, validator) {
							var add_length = nameAddvalidation();
							return (add_length>87) ? false : true;
						}
					}
				}
			 },
			provider_types_id:{
				message: '',
				trigger: 'change keyup',
				 validators: {
					 notEmpty: {
						message: '{{ trans("practice/practicemaster/provider.validation.provider_types_id") }}'
					},
					callback: {
						message: '',
						callback: function (value, validator) {
							if (value == 5) {
								$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'etin_type_number', true);
								$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'speciality_id', true);
								$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'taxanomy_id', true);
							} else {
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
							$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="last_name"]'));
							$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="first_name"]'));
							$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="middle_name"]'));
							$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="short_name"]'));
							return true;
						}
					}
				}
			},
			email:{
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
			ssn: {
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
					 notEmpty: {
                        message: '{{ trans("practice/practicemaster/provider.validation.provider_npi") }}'
                    },
					callback: {
						message: '',
						callback: function (value, validator) {
							if($(".js-delete-confirm").length ==0){
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
							return true;							
						}
					}
				}
			},
			etin_type_number: {
				message:'',
				enabled:true,
				validators:{
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
			provider_dob:{
				message:'',
				validators:{
					date:{
						  format:'MM/DD/YYYY',
						  message: '{{ trans("common.validation.date_format") }}'
						},
					callback: {
						message: '',
						callback: function(value, validator, $field) {
							var dob = $('#js-bootstrap-validator').find('[name="provider_dob"]').val();
							var current_date=new Date(dob);
							var d=new Date();	
							var min_date = new Date('01/01/1900'); // Restriced the dob value not below 1900
							if(dob !='' && validateDateformadd(dob)){
								if(d.getTime() < current_date.getTime()){
									return {
										valid: false, 
										message: '{{ trans("practice/practicemaster/provider.validation.provider_dob") }}'
									};
								}
								else if(min_date.getTime() > current_date.getTime()){
									return {
										valid: false, 
										message: 'Enter Valid Date'
									};
								}
							}
							return true;
						}
					}
				}
			},
			taxanomy_id:{
				message:'',
				enabled:true,
				validators:{
					callback: {
						message: '',
						callback: function (value, validator) {
							var provider_type_billing = "no";
                            if($('#js-speciality-change').val() !='' && value=='') {
                                return {
                                    valid: false, 
                                    message: '{{ trans("practice/practicemaster/provider.validation.taxanomy_id") }}'
                                };
                            }
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
			},taxanomy_id2:{
                message:'',
                enabled:true,
                validators:{
                    callback: {
                        message: '',
                        callback: function (value, validator) {
                            if($('#js-speciality2-change').val() !='' && value=='') {
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
			speciality_id:{
				message:'',
				enabled:true,
				validators:{
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
						message: '{{ trans("common.validation.image_maxsize_valid") }}'
					}
				}
			},
		}
	});
	var org_name = $('#organization_name').val();
	if(org_name==''){
		$('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'provider_degrees_id',true);
	}
});

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

$(document).on('ifToggled', "input[name='additional_provider_type[]']",function () {
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
	}
	if($(this).val()==2){
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="first_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="last_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="middle_name"]'));
	}
});

function validateDateformadd(dateValue)
{
    var selectedDate = dateValue;
    if(selectedDate == '')
        return false;

    var regExp = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/; //Declare Regex
    var dateArray = selectedDate.match(regExp); // is format OK?

    if (dateArray == null){
        return false;
    }

    month = dateArray[1];
    day= dateArray[3];
    year = dateArray[5];        

    if (month < 1 || month > 12){
        return false;
    }else if (day < 1 || day> 31){ 
        return false;
    }else if ((month==4 || month==6 || month==9 || month==11) && day ==31){
        return false;
    }else if (month == 2){
        var isLeapYear = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
        if (day> 29 || (day ==29 && !isLeapYear)){
            return false
        }
    }
    return true;
}
</script>

<style>
.closeBtn:hover {
  color: red;
}
</style>
@endpush