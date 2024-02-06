<div class="box-body no-padding">
    <?php
		$contact_count = count((array)@$contacts);
    //dd($id);
    ?>

    {!! Form::hidden('contact_same_as_address1',@$patients->address1,['class'=>'form-control','id'=>'contact_same_as_address1']) !!}
    {!! Form::hidden('contact_same_as_address2',@$patients->address2,['class'=>'form-control','id'=>'contact_same_as_address2']) !!}
    {!! Form::hidden('contact_same_as_city',@$patients->city,['class'=>'form-control','id'=>'contact_same_as_city']) !!}
    {!! Form::hidden('contact_same_as_state',@$patients->state,['class'=>'form-control','id'=>'contact_same_as_state']) !!}
    {!! Form::hidden('contact_same_as_zip5',@$patients->zip5,['class'=>'form-control','id'=>'contact_same_as_zip5']) !!}
    {!! Form::hidden('contact_same_as_zip4',@$patients->zip4,['class'=>'form-control','id'=>'contact_same_as_zip4']) !!}
    {!! Form::hidden('contact_count_v2',@$contact_count,['class'=>'form-control','id'=>'contact_count_v2']) !!}

    <input id="self_address1" type="hidden" value="{{@$patients->address1}}">
    <input id="self_address2" type="hidden" value="{{@$patients->address2}}">
    <input id="self_city" type="hidden" value="{{@$patients->city}}">
    <input id="self_state" type="hidden" value="{{@$patients->state}}">
    <input id="self_zip5" type="hidden" value="{{@$patients->zip5}}">
    <input id="self_zip4" type="hidden" value="{{@$patients->zip4}}">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">
        @if($checkpermission->check_url_permission('patients/{id}/edit') == 1)
        <a class="font600 font14 js-addmore_contact_v2 form-cursor pull-right " accesskey="n" id="addmore_contact_v2" ><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Contact</a>
        @endif
    </div>

    @if($contact_count == 0)
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 no-padding">
        <p class="padding-10 font14 bg-white med-gray-dark text-center yes-border border-green">No Records Found </p>
    </div>
    @else
    <?php 
		$guarantor_count_v2 = 0; 
    	$emergency_count_v2 = 0;
    	$employer_count_v2 = 0;
        $attorney_count_v2 = 0;
	 ?>
    @foreach(@$contacts as $contact)
    @if($contact->category == 'Guarantor')
    <?php $guarantor_count_v2++; ?>
    @elseif($contact->category == "Emergency Contact")
    <?php $emergency_count_v2++; ?>
    @elseif($contact->category == "Employer")
    <?php $employer_count_v2++; ?>
    @elseif($contact->category == "Attorney")
    <?php $attorney_count_v2++; ?>
    @endif
    @include('patients/patients/new-contact-form',['contact' => $contact,'claims_list' => $claims_list, 'guarantor_count_v2' => $guarantor_count_v2,'emergency_count_v2' => $emergency_count_v2,'employer_count_v2' => $employer_count_v2,'attorney_count_v2' => $attorney_count_v2])   
    @endforeach
    @endif


    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center no-padding">
        <a href="javascript:void(0);" class="js_arrow" id="insurance"> {!! Form::button('<<', ['class'=>'btn btn-medcubics pull-left']) !!} </a></center>
        <!--<a href="javascript:void(0)" data-url="{{ url('patients/'.$id.'#contact-info') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>-->
        <a href="javascript:void(0);" class="js_arrow" id="authorization">{!! Form::button('>>', ['class'=>'btn btn-medcubics pull-right']) !!} </a></center>
    </div>

</div>
<!-- /.box-body -->


<div id="add_new_contact" class="modal fade in" data-keyboard="false"></div><!-- Modal Light Box Ends --> 
<div class="js_add_new_contact_form hide">
    <div class="modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title js-category-title-v2">Contact Category</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['onsubmit'=>"event.preventDefault();" ,'name'=>'v2_contact_form','id'=>'js-bootstrap-validator-contact','class'=>'v2-contact-info-form popupmedcubicsform js-v2-common-info-form']) !!}
                {!! Form::hidden('add_type','new',['id'=>'add_type']) !!}
                {!! Form::hidden('edit_type_id',null,['id'=>'edit_type_id']) !!}
                <div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->              
                    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.contact") }}' />
                    <div class="box-body form-horizontal no-padding">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-address-employer" id="js-address-general-address">

                            <div class="form-group">
                                {!! Form::label('Category', 'Category', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label med-green star']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                    {!! Form::select('contact_category', array(''=>'-- Select --')+(array)@$selectbox,null,['class'=>'select_2 form-control js_contact_category_v2','id'=>'contact_category-0']) !!}
                                </div>
                                <input type="hidden" name="guarantor_count" value="{!! $selectbox_count->Guarantor !!}">
                                <input type="hidden" name="employer_count" value="{!! $selectbox_count->Employer !!}">
                                <input type="hidden" name="emergency_count" value="{!! $selectbox_count->Emergency_Contact !!}">
                                <input type="hidden" name="attorney_count" value="{!! $selectbox_count->Attorney !!}">
                                <div class="col-md-1 col-sm-1 col-xs-2"></div>
                            </div>
                            <span id="show_error_msgs"></span>
                            <span id="v2-guarantor" class="js-address-class hide">

                                {!! Form::hidden('guarantor_general_address_type','patients',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('guarantor_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('guarantor_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('guarantor_general_address1',null,['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('guarantor_general_city',null,['class'=>'js-address-city']) !!}
                                {!! Form::hidden('guarantor_general_state',null,['class'=>'js-address-state']) !!}
                                {!!     Form::hidden('guarantor_general_zip5',null,['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('guarantor_general_zip4',null,['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('guarantor_general_is_address_match',null,['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('guarantor_general_error_message',null,['class'=>'js-address-error-message']) !!}
                                <div class="form-group">
                                    {!! Form::label('guarantor_relationship', 'Relationship', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::select('guarantor_relationship', [''=>'-- Select --','Self'=>'Self','Child' => 'Child','Father' => 'Father','Mother' => 'Mother','Spouse'=>'Spouse','Neighbor'=>'Neighbor','Grandmother'=>'Grandmother','Grandfather'=>'Grandfather','Grandchild'=>'Grandchild','Friend'=>'Friend','Brother'=>'Brother','Sister'=>'Sister','Guardian'=>'Guardian','Others'=>'Others'],null,['class'=>'select_2 form-control guarantor_relationship_chk','id' => 'guarantor_relationship']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('LastName', 'Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('guarantor_last_name',null,['class'=>'form-control js-letters-caps-format','maxlength'=>'50','id' => 'guarantor_last_name', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div>
                                 <div class="form-group">
                                    {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">
                                        {!! Form::text('guarantor_first_name',null,['class'=>'form-control js-letters-caps-format','maxlength'=>'50','id' => 'guarantor_first_name', 'autocomplete'=>'off']) !!}
                                    </div>
                                    {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                    <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0">
                                        {!! Form::text('guarantor_middle_name',null,['class'=>'form-control p-r-0 dm-mi js-letters-caps-format','id' => 'guarantor_middle_name', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>
                                <div class="form-group margin-b-10 address-class">
                                    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
                                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::checkbox('same_as_patient_address', null, null, ['class'=>"js-same_as_patient_address-v2 flat_red med-green",'id'=>'gua-sameaddress-insurance']) !!} <label for="gua-sameaddress-insurance" class="med-green font600">Same as patient address</label>
                                    </div>
                                </div>

                                <div class="form-group same_address">
                                    {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('guarantor_address1',null,['maxlength'=>'50','id'=>'guarantor_address1','class'=>'form-control js-address-check js-v2-address1', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div> 

                                <div class="form-group same_address">
                                    {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">                            
                                        {!! Form::text('guarantor_address2',null,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2', 'autocomplete'=>'nope','id'=>'guarantor_address2']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div> 

                                <div class="form-group same_address">
                                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                                        {!! Form::text('guarantor_city',null,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-city','id'=>'guarantor_city', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                    <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0"> 
                                        {!! Form::text('guarantor_state',null,['class'=>'form-control js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'guarantor_state', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>

                                <div class="form-group same_address">
                                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">                             
                                        {!! Form::text('guarantor_zip5',null,['class'=>'form-control js-number js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'guarantor_zip5', 'autocomplete'=>'nope']) !!}
                                    </div>
									
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                                        {!! Form::text('guarantor_zip4',null,['class'=>'form-control js-number js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'guarantor_zip4', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2">            
                                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                        <span class="js-address-success hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                                        <span class="js-address-error hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                                        <?php echo $value; ?>
                                    </div> 
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div> 
                                </div>

                                <div class="form-group self-address">
                                    {!! Form::label('Home Phone', 'Home Phone', ['class'=>'col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">  
                                        {!! Form::text('guarantor_home_phone',null,['class'=>'form-control js-number dm-phone','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                    </div>                  
                                </div> 
                                <div class="form-group self-address">
                                    {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('guarantor_cell_phone',null,['class'=>'form-control js-number dm-phone','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>

                                <div class="form-group self-address">
                                    {!! Form::label('Email', 'Email', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('guarantor_email',null,['class'=>'form-control', 'autocomplete'=>'nope']) !!}
                                    </div>                 
                                </div>
                            </span>

                            <span id="v2-emergency_contact" class="js-address-class hide">
                                {!! Form::hidden('emergency_contact_general_address_type','patients',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('emergency_contact_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('emergency_contact_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('emergency_contact_general_address1',null,['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('emergency_contact_general_city',null,['class'=>'js-address-city']) !!}
                                {!! Form::hidden('emergency_contact_general_state',null,['class'=>'js-address-state']) !!}
                                {!! Form::hidden('emergency_contact_general_zip5',null,['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('emergency_contact_general_zip4',null,['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('emergency_contact_general_is_address_match',null,['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('emergency_contact_general_error_message',null,['class'=>'js-address-error-message']) !!}
                                <div class="form-group">
                                    {!! Form::label('emergency_relationship', 'Relationship', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::select('emergency_relationship', [''=>'-- Select --','Child' => 'Child','Father' => 'Father','Mother' => 'Mother','Spouse'=>'Spouse','Neighbor'=>'Neighbor','Grandmother'=>'Grandmother','Grandfather'=>'Grandfather','Grandchild'=>'Grandchild','Friend'=>'Friend','Brother'=>'Brother','Sister'=>'Sister','Guardian'=>'Guardian','Others'=>'Others'],null,['class'=>'select_2 form-control','id'=>'emergency_relationship']) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('LastName', 'Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('emergency_last_name',null,['class'=>'form-control js-letters-caps-format','maxlength'=>'50', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">
                                        {!! Form::text('emergency_first_name',null,['class'=>'form-control js-letters-caps-format','maxlength'=>'50', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                    <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0">
                                        {!! Form::text('emergency_middle_name',null,['class'=>'form-control p-r-0 dm-mi js-letters-caps-format', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>  

                                <div class="form-group margin-b-10 ">
                                    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
                                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::checkbox('same_as_patient_address', null, null, ['class'=>"js-same_as_patient_address-v2 flat_red med-green",'id'=>'add-sameaddress-insurance']) !!} <label for="add-sameaddress-insurance" class="med-green font600">Same as patient address</label> 
                                    </div>
                                </div>

                                <div class="form-group same_address">
                                    {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('emergency_address1',null,['maxlength'=>'50','id'=>'emergency_address1','class'=>'form-control js-address-check js-v2-address1', 'autocomplete'=>'nope']) !!}    
                                        {!! $errors->first('address1', '<p> :message</p>')  !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div> 

                                <div class="form-group same_address">
                                    {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">                            
                                        {!! Form::text('emergency_address2',null,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2', 'autocomplete'=>'nope','id'=>'emergency_address2']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div> 

                                <div class="form-group same_address">
                                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                                        {!! Form::text('emergency_city',null,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-city','id'=>'emergency_city', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                    <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0"> 
                                        {!! Form::text('emergency_state',null,['class'=>'form-control p-r-0 js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'emergency_state', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>

                                <div class="form-group same_address">
                                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">                             
                                        {!! Form::text('emergency_zip5',null,['class'=>'form-control js-number js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'emergency_zip5', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
                                        {!! Form::text('emergency_zip4',null,['class'=>'form-control js-number js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'emergency_zip4', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2">            
                                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                        <span class="js-address-success hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                                        <span class="js-address-error hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                                        <?php echo $value; ?>
                                    </div> 
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div> 
                                </div>

                                <div class="form-group self-address">
                                    {!! Form::label('Home Phone', 'Home Phone', ['class'=>'col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('emergency_home_phone',null,['class'=>'form-control js-number dm-phone','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div> 
                                <div class="form-group self-address">
                                    {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('emergency_cell_phone',null,['class'=>'form-control js-number dm-phone','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>
                                <div class="form-group self-address">
                                    {!! Form::label('Email', 'Email', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('emergency_email',null,['class'=>'form-control', 'autocomplete'=>'nope']) !!}
                                    </div>                 
                                </div>
                            </span>

                            <span id="v2-employer" class="js-address-class hide">
                                {!! Form::hidden('employer_general_address_type','patients',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('employer_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('employer_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('employer_general_address1',null,['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('employer_general_city',null,['class'=>'js-address-city']) !!}
                                {!! Form::hidden('employer_general_state',null,['class'=>'js-address-state']) !!}
                                {!! Form::hidden('employer_general_zip5',null,['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('employer_general_zip4',null,['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('employer_general_is_address_match',null,['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('employer_general_error_message',null,['class'=>'js-address-error-message']) !!}

                                <div class="form-group">
                                    {!! Form::label('employer_status', 'Employment Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::select('employer_status',[''=>'-- Select --','Employed' => 'Employed','Self Employed' => 'Self Employed','Retired' => 'Retired','Active Military Duty'=>'Active Military Duty','Unknown'=>'Unknown'],'Unknown',['class'=>'select_2 form-control v2-js-employment_status','id'=>'employer_status-0']) !!}
                                    </div>
                                </div>

                                <!-- start employed added fields -->
                                <span id="employed_option_sub_field-0" class="employed_option_sub_field-0 hide">
									<?php /*
                                    <!--div class="form-group">
                                            {!! Form::label('organization_name_label', 'Organization Name', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                                {!! Form::text('employer_organization_name',null,['class'=>'form-control  -border1','maxlength'=>'50','id'=>'employer_organization_name-0']) !!}
                                            </div>
                                            <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                    </div-->
									*/ ?>
                                    <div class="form-group">
                                        {!! Form::label('EmployerName_label', 'Employer Name', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10"> 
                                            {!! Form::text('employer_name', null, array('placeholder' => 'Search Text','class' => 'form-control emp_search_texts js-letters-caps-format','id'=>'employer_name-0', 'autocomplete'=>'nope')) !!}
                                            <!--{!! Form::text('employer_name',null,['class'=>'form-control js-letters-caps-format','id'=>'employer_name-0']) !!}-->
                                            <input id="invisible_id" name="invisible" type="hidden" value="$patients->id">
                                            {!! Form::hidden('patient_id', $patients->id,['id'=>"sss"]) !!}
                                            <div id="container" style = "display:none;""><small class="help-block"   style="">Results not Found</small></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('occupation_label', 'Occupation', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                            {!! Form::text('employer_occupation',null,['class'=>'form-control  -border1','maxlength'=>'50','id'=>'employer_occupation-0','autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                    </div>
                                </span>
                                <!-- end employed added fields -->

                                <!-- start student added fields -->
                                <span id="student_option_sub_field-0" class="student_option_sub_field-0 hide">
                                    <div class="form-group">
                                        {!! Form::label('student_status', 'Student Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">  
                                            {!! Form::select('employer_student_status', [''=>'-- Select --','Full Time' => 'Full Time','Part Time' => 'Part Time','Unknown'=>'Unknown'],'Unknown',['class'=>'select_2 form-control','id'=>'employer_student_status-0']) !!}
                                        </div>
                                    </div>
                                </span>
                                <!-- end student added fields -->
								<?php /*
                                <!--span id="employer_option_sub_field-0" class="employer_option_sub_field-0 hide">
                                        <div class="form-group">
                                                {!! Form::label('EmployerName_label', 'Employer Name', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10"> 
                                                        {!! Form::text('employer_name',null,['class'=>'form-control js-letters-caps-format','id'=>'employer_name-0']) !!}
                                                </div>
                                        </div>
                                </span-->

                                <!-- <div class="form-group margin-b-10">
                                        <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
                                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                                {!! Form::checkbox('same_as_patient_address', null, null, ['class'=>"js-same_as_patient_address-v2 flat-red med-green",'id'=>'sameaddress-insurance']) !!} &nbsp; <span class="med-green font600">Same as patient address</span> 
                                        </div>
                                </div> -->
								*/ ?>
                                <div id = "employer-retired-field-0" class = "employer-retired-field-0">
                                    <div class="form-group">
                                        {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 ">
                                            {!! Form::text('employer_address1',null,['maxlength'=>'50','id'=>'employer_address1','class'=>'form-control js-address-check js-v2-address1', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div> 

                                    <div class="form-group">
                                        {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 ">                            
                                            {!! Form::text('employer_address2',null,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2', 'autocomplete'=>'nope']) !!}                            
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div>                               

                                    <div class="form-group">
                                        {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                                            {!! Form::text('employer_city',null,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-city','id'=>'employer_city', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                        <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0">  
                                            {!! Form::text('employer_state',null,['class'=>'form-control p-r-0 js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'employer_state', 'autocomplete'=>'nope']) !!}
                                        </div>
                                    </div>   
                                    <div class="form-group">
                                        {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">                             
                                            {!! Form::text('employer_zip5',null,['class'=>'form-control js-number js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'employer_zip5', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                                            {!! Form::text('employer_zip4',null,['class'=>'form-control js-number js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'employer_zip4', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2">            
                                            <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                            <span class="js-address-success hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                                        <span class="js-address-error hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                                            <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                                            <?php echo $value; ?>
                                        </div> 
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div> 
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                                            {!! Form::text('employer_work_phone',null,['class'=>'form-control js-number dm-phone p-r-0','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 p-l-0"> 
                                            {!! Form::text('employer_phone_ext',null,['class'=>'form-control p-r-0 js-number dm-phone-ext','maxlength'=>'4', 'autocomplete'=>'nope']) !!}
                                        </div>
                                    </div>
                                </div>
                            </span>
                             <span id="v2-attorney" class="js-address-class hide">
                                {!! Form::hidden('attorney_general_address_type','patients',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('attorney_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('attorney_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('attorney_general_address1',null,['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('attorney_general_city',null,['class'=>'js-address-city']) !!}
                                {!! Form::hidden('attorney_general_state',null,['class'=>'js-address-state']) !!}
                                {!! Form::hidden('attorney_general_zip5',null,['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('attorney_general_zip4',null,['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('attorney_general_is_address_match',null,['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('attorney_general_error_message',null,['class'=>'js-address-error-message']) !!}
                            
                       
                                    <div class="form-group">
                                        {!! Form::label('attorney_adjuster_name', 'Attorney / Adjuster Name', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label star']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                            {!! Form::text('attorney_adjuster_name',null,['class'=>'form-control  -border1','maxlength'=>'50','id'=>'attorney_adjuster_name-0','autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                    </div>
                                     <div class="form-group">
                                        {!! Form::label('attorney_doi', 'Date of Injury', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                            {!! Form::text('attorney_doi',null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor dm-date','id'=>'attorney_doi-0', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                    </div>
                                           
                                  <div class="form-group">
                                        {!! Form::label('attorney_claim_num', 'Claim No', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                            {!! Form::select('attorney_claim_number[]',(array)$claims_list,'Unknown',['class'=>'select_2 form-control ','multiple'=>'multiple']) !!}
                                        </div>
                                        <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                 </div> 
                         
                                <div class="form-group">
                                        {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                                            {!! Form::text('attorney_work_phone',null,['class'=>'form-control js-number dm-phone p-r-0','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 p-l-0"> 
                                            {!! Form::text('attorney_phone_ext',null,['class'=>'form-control p-r-0 js-number dm-phone-ext','maxlength'=>'4', 'autocomplete'=>'nope']) !!}
                                        </div>
                                </div>
                               <div class="form-group">
                                    {!! Form::label('attorney_fax', 'Fax', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('attorney_fax',null,['class'=>'form-control  -border1 dm-fax','maxlength'=>'50','id'=>'attorney_doi-0','autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('attorney_email ', 'Email', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('attorney_email',null,['class'=>'form-control  -border1 ']) !!}
                                    </div>
                                    <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                </div>
                                <!-- end employed added fields -->                          
                                <div>
                                    <div class="form-group">
                                        {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 ">
                                            {!! Form::text('attorney_address1',null,['maxlength'=>'50','id'=>'attorney_address1','class'=>'form-control js-address-check js-v2-address1', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div> 

                                    <div class="form-group">
                                        {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 ">                            
                                            {!! Form::text('attorney_address2',null,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div>                               

                                    <div class="form-group">
                                        {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                                            {!! Form::text('attorney_city',null,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-city','id'=>'attorney_city', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                        <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0">  
                                            {!! Form::text('attorney_state',null,['class'=>'form-control p-r-0 js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'attorney_state', 'autocomplete'=>'nope']) !!}
                                        </div>
                                    </div>   
                                    <div class="form-group">
                                        {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">                             
                                            {!! Form::text('attorney_zip5',null,['class'=>'form-control js-number js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'attorney_zip5', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
                                            {!! Form::text('attorney_zip4',null,['class'=>'form-control js-number js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'attorney_zip4', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2">            
                                            <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                            <span class="js-address-success hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                                        <span class="js-address-error hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                                            <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                                            <?php echo $value; ?>
                                        </div> 
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div> 
                                    </div>

                                </div>
                            </span>
                        </div>

                    </div><!-- /.box-body -->   
                </div><!-- /.box Ends Contact Details-->

                <div id="contact-info-footer" class="modal-footer">
                    <input id="js-form-submit-button-v2" accesskey="s" data-id="js-bootstrap-validator-contact" class="btn btn-medcubics-small" type="submit" value="Save">
                    <button class="btn btn-medcubics-small" data-dismiss="modal" type="button">Cancel</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<!-- Start Edit model contacts -->
<div id="add_edit_contact" class="modal fade in" data-keyboard="false"></div><!-- Modal Light Box Ends --> 
<div class="js_add_edit_contact_form hide">
    <div class="modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title js-category-title-e2"></h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['onsubmit'=>"event.preventDefault();" ,'name'=>'v2_edit_contact_form','id'=>'','class'=>'v2-contact-info-form v2-contact-info-form-edit popupmedcubicsform js-v2-common-info-form js-bootstrap-validator-contact-edit']) !!}
                {!! Form::hidden('add_type','new',['id'=>'add_type']) !!}
                {!! Form::hidden('edit_type_id',null,['id'=>'edit_type_id']) !!}
                <div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->

                    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.contact") }}' />
                    <div class="box-body form-horizontal no-padding">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-address-employer" id="js-address-general-address">

                            <div class="form-group">
                              <?php /*
                               <!-- <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                  {!! Form::label('Category', 'Category', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label med-green']) !!} 
                                    {!! Form::select('contact_category', array(''=>'-- Select --')+(array)@$selectbox,null,['class'=>'select_2 form-control js_contact_category_v2']) !!}
                                </div>-->
								*/ ?>
                                <input type="hidden" name="guarantor_count" value="{!! $selectbox_count->Guarantor !!}">
                                <input type="hidden" name="employer_count" value="{!! $selectbox_count->Employer !!}">
                                <input type="hidden" name="emergency_count" value="{!! $selectbox_count->Emergency_Contact !!}">
                                <input type="hidden" name="attorney_count" value="{!! $selectbox_count->Attorney !!}">
                                <div class="col-md-1 col-sm-1 col-xs-2"></div>
                            </div>
                            <span id="edit_show_error_msgs"></span>
                            <span id="v2-edit-guarantor" class="js-address-class hide">

                                {!! Form::hidden('guarantor_general_address_type','patients',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('guarantor_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('guarantor_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('guarantor_general_address1',null,['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('guarantor_general_city',null,['class'=>'js-address-city']) !!}
                                {!! Form::hidden('guarantor_general_state',null,['class'=>'js-address-state']) !!}
                                {!!     Form::hidden('guarantor_general_zip5',null,['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('guarantor_general_zip4',null,['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('guarantor_general_is_address_match',null,['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('guarantor_general_error_message',null,['class'=>'js-address-error-message']) !!}
                                <div class="form-group">
                                    {!! Form::label('guarantor_relationship', 'Relationship', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::select('guarantor_relationship', [''=>'-- Select --','Self'=>'Self','Child' => 'Child','Father' => 'Father','Mother' => 'Mother','Spouse'=>'Spouse','Neighbor'=>'Neighbor','GrandMother'=>'Grandmother','GrandFather'=>'Grandfather','GrandChild'=>'Grandchild','Friend'=>'Friend','Brother'=>'Brother','Sister'=>'Sister','Guardian'=>'Guardian','Others'=>'Others'],null,['class'=>'select_2 form-select form-control guarantor_relationship_chk_edit','id' => 'guarantor_relationship']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('LastName', 'Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('guarantor_last_name',null,['class'=>'form-control js-letters-caps-format','maxlength'=>'50', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div>
                                
                                <div class="form-group">
                                    {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">
                                        {!! Form::text('guarantor_first_name',null,['class'=>'form-control js-letters-caps-format','maxlength'=>'50','autocomplete'=>'nope']) !!}
                                    </div>
                                    {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                    <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0">
                                        {!! Form::text('guarantor_middle_name',null,['class'=>'form-control p-r-0 dm-mi js-letters-caps-format', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>

                                <div class="form-group margin-b-10 address-class">
                                    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
                                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                    <input id = "same-address-guarantor" type="checkbox" name="same_as_patient_address" class="js-same_as_patient_address-v2"><label for="same-address-guarantor" class="med-green font600 no-bottom">&nbsp;Same as patient address</label>  
                                    </div>
                                </div>

                                <div class="form-group same_address">
                                    {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('guarantor_address1',null,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-address1','id'=>'guarantor_edit_address1', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div> 

                                <div class="form-group same_address">
                                    {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">                            
                                        {!! Form::text('guarantor_address2',null,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2','id'=>'guarantor_edit_address2', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div> 

                                <div class="form-group same_address">
                                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                                        {!! Form::text('guarantor_city',null,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-city','id'=>'guarantor_edit_city', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                    <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0"> 
                                        {!! Form::text('guarantor_state',null,['class'=>'form-control js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'guarantor_edit_state', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>                                                                   

                                <div class="form-group same_address">
                                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">                             
                                        {!! Form::text('guarantor_zip5',null,['class'=>'form-control js-number js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'guarantor_edit_zip5', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
                                        {!! Form::text('guarantor_zip4',null,['class'=>'form-control js-number js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'guarantor_edit_zip4', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2">            
                                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                                        <?php echo $value; ?>
                                    </div> 
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div> 
                                </div>

                                <div class="form-group self-address">
                                    {!! Form::label('Home Phone', 'Home Phone', ['class'=>'col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">  
                                        {!! Form::text('guarantor_home_phone',null,['class'=>'form-control js-number dm-phone','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                    </div>                  
                                </div> 
                                <div class="form-group self-address">
                                    {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('guarantor_cell_phone',null,['class'=>'form-control js-number dm-phone','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>

                                <div class="form-group self-address">
                                    {!! Form::label('Email', 'Email', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('guarantor_email',null,['class'=>'form-control', 'autocomplete'=>'nope']) !!}
                                    </div>                 
                                </div>
                            </span>

                            <span id="v2-edit-emergency" class="js-address-class hide">
                                {!! Form::hidden('emergency_contact_general_address_type','patients',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('emergency_contact_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('emergency_contact_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('emergency_contact_general_address1',null,['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('emergency_contact_general_city',null,['class'=>'js-address-city']) !!}
                                {!! Form::hidden('emergency_contact_general_state',null,['class'=>'js-address-state']) !!}
                                {!! Form::hidden('emergency_contact_general_zip5',null,['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('emergency_contact_general_zip4',null,['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('emergency_contact_general_is_address_match',null,['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('emergency_contact_general_error_message',null,['class'=>'js-address-error-message']) !!}
                               <div class="form-group">
                                    {!! Form::label('emergency_relationship', 'Relationship', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::select('emergency_relationship', [''=>'-- Select --','Child' => 'Child','Father' => 'Father','Mother' => 'Mother','Spouse'=>'Spouse','Neighbor'=>'Neighbor','GrandMother'=>'Grandmother','GrandFather'=>'Grandfather','GrandChild'=>'Grandchild','Friend'=>'Friend','Brother'=>'Brother','Sister'=>'Sister','Guardian'=>'Guardian','Others'=>'Others'],null,['class'=>'select_2 form-control','id'=>'emergency_relationship']) !!}
                                    </div>
                                </div>


                                <div class="form-group">
                                    {!! Form::label('LastName', 'Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('emergency_last_name',null,['class'=>'form-control js-letters-caps-format','maxlength'=>'50', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">
                                        {!! Form::text('emergency_first_name',null,['class'=>'form-control js-letters-caps-format','maxlength'=>'50', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    {!! Form::label('Title', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                    <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0">
                                        {!! Form::text('emergency_middle_name',null,['class'=>'form-control p-r-0 dm-mi js-letters-caps-format']) !!}
                                    </div>
                                </div>

                                <div class="form-group margin-b-10 address-class">
                                    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
                                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                        <input id = "same-address-emergency" type="checkbox" name="same_as_patient_address" class="js-same_as_patient_address-v2"><label for="same-address-emergency" class="no-bottom med-orange font600"> Same as patient address</label>
                                    </div>
                                </div>

                                <div class="form-group same_address">
                                    {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('emergency_address1',null,['maxlength'=>'50','id'=>'emergency_address1','class'=>'form-control js-address-check js-v2-address1', 'autocomplete'=>'nope']) !!}    
                                        {!! $errors->first('address1', '<p> :message</p>')  !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div> 

                                <div class="form-group same_address">
                                    {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">                            
                                        {!! Form::text('emergency_address2',null,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div> 

                                <div class="form-group same_address">
                                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                                        {!! Form::text('emergency_city',null,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-city','id'=>'emergency_city', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                    <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0"> 
                                        {!! Form::text('emergency_state',null,['class'=>'form-control p-r-0 js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'emergency_state', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>

                                <div class="form-group same_address">
                                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">                             
                                        {!! Form::text('emergency_zip5',null,['class'=>'form-control js-number js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'emergency_zip5', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
                                        {!! Form::text('emergency_zip4',null,['class'=>'form-control js-number js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'emergency_zip4', 'autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2">            
                                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                        <span class="js-address-success hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                                        <span class="js-address-error hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                                        <?php echo $value; ?>
                                    </div> 
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div> 
                                </div>

                                <div class="form-group self-address">
                                    {!! Form::label('Home Phone', 'Home Phone', ['class'=>'col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('emergency_home_phone',null,['class'=>'form-control js-number dm-phone','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div> 
                                <div class="form-group self-address">
                                    {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('emergency_cell_phone',null,['class'=>'form-control js-number dm-phone','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                    </div>
                                </div>
                                <div class="form-group self-address">
                                    {!! Form::label('Email', 'Email', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('emergency_email',null,['class'=>'form-control', 'autocomplete'=>'nope']) !!}
                                    </div>                 
                                </div>
                            </span>

                            <span id="v2-edit-employer" class="js-address-class hide">
                                {!! Form::hidden('employer_general_address_type','patients',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('employer_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('employer_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('employer_general_address1',null,['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('employer_general_city',null,['class'=>'js-address-city']) !!}
                                {!! Form::hidden('employer_general_state',null,['class'=>'js-address-state']) !!}
                                {!! Form::hidden('employer_general_zip5',null,['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('employer_general_zip4',null,['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('employer_general_is_address_match',null,['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('employer_general_error_message',null,['class'=>'js-address-error-message']) !!}

                                <div class="form-group">
                                    {!! Form::label('employer_status', 'Employment Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::select('employer_status',[''=>'-- Select --','Employed' => 'Employed','Self Employed' => 'Self Employed','Retired' => 'Retired','Active Military Duty'=>'Active Military Duty','Unknown'=>'Unknown'],'Unknown',['class'=>'select_2 form-control v2-js-employment_status-edit','id'=>'edit_employer_status']) !!}
                                    </div>
                                </div>

                                <!-- start employed added fields -->
                                <span id="employed_option_sub_field" class="employed_option_sub_field hide">
									<?php /*
                                    <!--div class="form-group">
										{!! Form::label('organization_name_label', 'Organization Name', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
										<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
											 {!! Form::text('employer_organization_name',null,['class'=>'form-control  -border1','maxlength'=>'50','id'=>'employer_organization_name-0']) !!}
										</div>
										<div class="col-md-2 col-sm-1 col-xs-2"></div>
                                    </div-->
									*/ ?>
									
                                    <div class="form-group emp-status-class">
                                        {!! Form::label('EmployerName_label', 'Employer Name', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10"> 
                                            {!! Form::text('employer_name', null, array('placeholder' => 'Search Text','class' => 'form-control emp_search_texts js-letters-caps-format','id'=>'edit_employer_name', 'autocomplete'=>'nope')) !!}
                                            <!--{!! Form::text('employer_name',null,['class'=>'form-control js-letters-caps-format','id'=>'employer_name-0']) !!}-->
                                            <input id="invisible_id" name="invisible" type="hidden" value="$patients->id">
                                            {!! Form::hidden('patient_id', $patients->id,['id'=>"sss"]) !!}
                                            <div id="container" style = "display:none;""><small class="help-block"   style="">Results not Found</small></div>
                                        </div>
                                    </div>
                                    <div class="form-group emp-status-class">
                                        {!! Form::label('occupation_label', 'Occupation', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                            {!! Form::text('employer_occupation',null,['class'=>'form-control  -border1','maxlength'=>'50','id'=>'employer_occupation-0', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                    </div>
                                </span>
                                <!-- end employed added fields -->

                                <!-- start student added fields -->
                                <span id="student_option_sub_field-0" class="student_option_sub_field-0 hide">
                                    <div class="form-group">
                                        {!! Form::label('student_status', 'Student Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">  
                                            {!! Form::select('employer_student_status', [''=>'-- Select --','Full Time' => 'Full Time','Part Time' => 'Part Time','Unknown'=>'Unknown'],'Unknown',['class'=>'select_2 form-control','id'=>'employer_student_status-0']) !!}
                                        </div>
                                    </div>
                                </span>
                                <!-- end student added fields -->
								<?php /*	
                                <!--span id="employer_option_sub_field-0" class="employer_option_sub_field-0 hide">
									<div class="form-group">
										{!! Form::label('EmployerName_label', 'Employer Name', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}
										<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10"> 
											{!! Form::text('employer_name',null,['class'=>'form-control js-letters-caps-format','id'=>'employer_name-0']) !!}
										</div>
									</div>
                                </span-->

                                <!-- <div class="form-group margin-b-10">
									<div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
									<div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
										{!! Form::checkbox('same_as_patient_address', null, null, ['class'=>"js-same_as_patient_address-v2 flat-red med-green",'id'=>'sameaddress-insurance']) !!} &nbsp; <span class="med-green font600">Same as patient address</span> 
									</div>
                                </div> -->
								*/ ?>
                                <div id = "employer-retired-field" class = "employer-retired-field">
                                    <div class="form-group">
                                        {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 ">
                                            {!! Form::text('employer_address1',null,['maxlength'=>'50','id'=>'employer_address1','class'=>'form-control js-address-check js-v2-address1', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div> 

                                    <div class="form-group">
                                        {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 ">                            
                                            {!! Form::text('employer_address2',null,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2','id'=>'employer_address2', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div>                               

                                    <div class="form-group">
                                        {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                                            {!! Form::text('employer_city',null,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-city','id'=>'employer_city', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                        <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0">  
                                            {!! Form::text('employer_state',null,['class'=>'form-control p-r-0 js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'employer_state', 'autocomplete'=>'nope']) !!}
                                        </div>
                                    </div>   
                                    <div class="form-group">
                                        {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">                             
                                            {!! Form::text('employer_zip5',null,['class'=>'form-control js-number js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'employer_zip5', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
                                            {!! Form::text('employer_zip4',null,['class'=>'form-control js-number js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'employer_zip4', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2">            
                                            <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                            <span class="js-address-success hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                                        <span class="js-address-error hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                                            <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                                            <?php echo $value; ?>
                                        </div> 
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div> 
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                                            {!! Form::text('employer_work_phone',null,['class'=>'form-control js-number dm-phone p-r-0','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 p-l-0"> 
                                            {!! Form::text('employer_phone_ext',null,['class'=>'form-control p-r-0 js-number dm-phone-ext','maxlength'=>'4', 'autocomplete'=>'nope']) !!}
                                        </div>
                                    </div>
                                </div>
                            </span>
                            <span id="v2-edit-attorney" class="js-address-class hide">
                           
                                {!! Form::hidden('attorney_general_address_type','patients',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('attorney_general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('attorney_general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('attorney_general_address1',null,['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('attorney_general_city',null,['class'=>'js-address-city']) !!}
                                {!! Form::hidden('attorney_general_state',null,['class'=>'js-address-state']) !!}
                                {!! Form::hidden('attorney_general_zip5',null,['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('attorney_general_zip4',null,['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('attorney_general_is_address_match',null,['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('attorney_general_error_message',null,['class'=>'js-address-error-message']) !!}
                      
                                    <div class="form-group">
                                        {!! Form::label('attorney_adjuster_name', 'Attorney / Adjuster Name', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label star']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                            {!! Form::text('attorney_adjuster_name',null,['class'=>'form-control  -border1','maxlength'=>'50','id'=>'attorney_adjuster_name','autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                    </div>
                                     <div class="form-group">
                                        {!! Form::label('attorney_doi', 'Date of Injury', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                            {!! Form::text('attorney_doi',null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor dm-date','id'=>'attorney_doi', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                    </div>
                                           
                                  <div class="form-group">
                                        {!! Form::label('attorney_claim_num', 'Claim No', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                            {!! Form::select('attorney_claim_number[]',(array)$claims_list,'Unknown',['class'=>'select_2 form-control ','multiple'=>'multiple','id'=>'edit_attorney_claim_number']) !!}
                                        </div>
                                        <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                 </div> 
                         
                                <div class="form-group">
									{!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
										{!! Form::text('attorney_work_phone',null,['class'=>'form-control js-number dm-phone p-r-0','maxlength'=>'14', 'autocomplete'=>'nope']) !!}
									</div>
									{!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 p-l-0"> 
										{!! Form::text('attorney_phone_ext',null,['class'=>'form-control p-r-0 js-number dm-phone-ext','maxlength'=>'4', 'autocomplete'=>'nope']) !!}
									</div>
                                </div>
                               <div class="form-group">
                                    {!! Form::label('attorney_fax', 'Fax', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('attorney_fax',null,['class'=>'form-control  -border1 dm-fax','maxlength'=>'50','id'=>'attorney_doi-0','autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('attorney_email ', 'Email', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('attorney_email',null,['class'=>'form-control  -border1 ']) !!}
                                    </div>
                                    <div class="col-md-2 col-sm-1 col-xs-2"></div>
                                </div>
                                <!-- end employed added fields -->                          
                                <div>
                                    <div class="form-group">
                                        {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 ">
                                            {!! Form::text('attorney_address1',null,['maxlength'=>'50','id'=>'attorney_address1','class'=>'form-control js-address-check js-v2-address1', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div> 

                                    <div class="form-group">
                                        {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 ">                            
                                            {!! Form::text('attorney_address2',null,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2', 'autocomplete'=>'nope']) !!}                            
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                    </div>                               

                                    <div class="form-group">
                                        {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
                                            {!! Form::text('attorney_city',null,['maxlength'=>'50','class'=>'form-control js-address-check js-v2-city','id'=>'attorney_city', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                                        <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2 p-l-0">  
                                            {!! Form::text('attorney_state',null,['class'=>'form-control p-r-0 js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'attorney_state', 'autocomplete'=>'nope']) !!}
                                        </div>
                                    </div>   
                                    <div class="form-group">
                                        {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">                             
                                            {!! Form::text('attorney_zip5',null,['class'=>'form-control js-number js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'attorney_zip5', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                                            {!! Form::text('attorney_zip4',null,['class'=>'form-control js-number js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'attorney_zip4', 'autocomplete'=>'nope']) !!}
                                        </div>
                                        <div class="col-md-1 col-sm-1 col-xs-2">            
                                            <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                            <span class="js-address-success hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                                        <span class="js-address-error hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                                            <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                                            <?php echo $value; ?>
                                        </div> 
                                        <div class="col-md-1 col-sm-1 col-xs-2"></div> 
                                    </div>

                                </div>
                            </span>

                        </div>

                    </div><!-- /.box-body -->   
                </div><!-- /.box Ends Contact Details-->

                <div id="edit-contact-info-footer" class="modal-footer">
                    <input id="" data-id="" accesskey="s" class="btn btn-medcubics-small js-e2-edit-contact" type="submit" value="Save">
                    <button class="btn btn-medcubics-small close_popup cancel_popup"  type="button">Cancel</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- End Edit model contacts -->
@push('view.script')
<script type="text/javascript">
	$(".js-address-check").trigger("blur");
	disableAutoFill('.v2-contact-info-form');    
</script>
@endpush