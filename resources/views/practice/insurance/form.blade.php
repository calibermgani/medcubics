<div class="col-lg-6 col-md-6 col-xs-12 margin-t-20"><!--  Left side Content Starts -->

    <div class="box no-shadow margin-b-10">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="briefcase"></i> <h3 class="box-title">Business Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body form-horizontal margin-l-10 p-b-20">

            <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
            <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.insurance_details") }}' />
            
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
                    {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label star']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address_1')) error @endif">                                                     
                        {!! Form::text('address_1',null,['id'=>'address_1','class'=>'form-control js-address-check dm-address', 'autocomplete'=>'nope']) !!}                           
                        {!! $errors->first('address_1', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address_2')) error @endif">                            
                        {!! Form::text('address_2',null,['id'=>'address_2','class'=>'form-control dm-address', 'autocomplete'=>'nope']) !!}
                        {!! $errors->first('address_2', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 


                <div class="form-group">
                    {!! Form::label('City', 'City', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label star']) !!}
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 @if($errors->first('city')) error @endif">  
                        {!! Form::text('city',null,['class'=>'form-control js-address-check dm-address','id'=>'city', 'autocomplete'=>'nope']) !!}
                        {!! $errors->first('city', '<p> :message</p>')  !!}
                    </div>
                    {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3  @if($errors->first('state')) error @endif"> 
                        {!! Form::text('state',null,['class'=>'form-control js-address-check dm-state','id'=>'state', 'autocomplete'=>'nope']) !!}
                        {!! $errors->first('state', '<p> :message</p>')  !!}
                    </div>
                </div>   
                <div class="form-group no-bottom">
                   {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label star']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">                             
                        {!! Form::text('zipcode5',null,['class'=>'form-control dm-zip5 zip5 js-address-check','id'=>'zipcode5', 'autocomplete'=>'nope']) !!}                                                      
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">                             
                        {!! Form::text('zipcode4',null,['class'=>'form-control dm-zip4  js-address-check','id'=>'zipcode4', 'autocomplete'=>'nope']) !!}                           
                    </div>
                    <div class="col-md-1 col-sm-2 col-xs-2">            
                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                        <span class="js-address-success @if($address_flag['general']['is_address_match'] != 'Yes') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                        <span class="js-address-error @if($address_flag['general']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>
                        <?php echo $value; ?>                                
                    </div> 
                </div>                    

            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends--> 

    <div class="box no-shadow margin-b-10">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="shield"></i> <h3 class="box-title">Credentials</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10 p-b-12">

            <div class="js-add-new-select" id="js-insurance-type">
                <div class="form-group js_common_ins">
                    {!! Form::label('InsuranceType', 'Insurance Type', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label star']) !!}
                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('insurancetype_id')) error @endif ">
						<?php
                           if(strpos($currnet_page, 'edit') !== false) {
							$insurancetype_id =  $insurancetype_id;
                           } else {
                            $insurancetype_id = '';//'Config::get('siteconfigs.insurance_type_id.default_id')'; 
						   }
                        ?>
                        {!! Form::select('insurancetype_id', array('' => '-- Select --') + (array)$insurancetypes,  $insurancetype_id,['class'=>'form-control select2 js-add-new-select-opt']) !!}
                        {!! $errors->first('insurancetype_id', '<p> :message</p>')  !!}  
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 
                
                <div class="form-group hide" id="add_new_span">
                    {!! Form::label('InsuranceType', 'Insurance Type', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label star']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                        {!! Form::text('newadded',null,['maxlength'=>'50','id'=>'newadded','class'=>'form-control','placeholder'=>'Add new Insurance Type','data-table-name'=>'insurancetypes','data-field-name'=>'type_name','data-field-id'=>$insurancetype_id,'data-label-name'=>'insurance type']) !!}
                        <p class="js-error help-block hide"></p>
                    </div>
                
                    {!! Form::label('cmsType', 'CMS Type', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label star']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                        {!! Form::select('cms_type', (array)@$cmstypes,  null,['class'=>'form-control select2','id'=>'newadded_cms_type','data-table-name'=>'insurancetypes','data-field-name'=>'cms_type','data-field-id'=>$insurancetype_id,'data-label-name'=>'cms type']) !!}
                        <p class="pull-right no-bottom">
                        <i class="fa fa-save med-green" id="add_new_save" data-placement="bottom"  data-toggle="tooltip" data-original-title="Save"></i>
                        <i class="fa fa-ban med-green margin-l-5" id="add_new_cancel" data-placement="bottom"  data-toggle="tooltip" data-original-title="Cancel"></i>                         
                        </p>
                    </div>                  
                </div>
                
            </div>  

            <div class="form-group">
                {!! Form::label('Enrollment', 'Enrollment Required', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="control-group col-lg-8 col-md-8 col-sm-8">
                    {!! Form::radio('enrollment', 'Yes',true,['class'=>'','id'=>'c-e-y']) !!} {!! Form::label('c-e-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('enrollment', 'No',null,['class'=>'','id'=>'c-e-n']) !!}  {!! Form::label('c-e-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                </div>
                <div class="col-sm-1"></div>
            </div> 

            {!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
            <div class="form-group">                
                {!! Form::label('Managedcareid', 'Managed Care ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('managedcareid')) error @endif ">
                    {!! Form::text('managedcareid',null,['class'=>'form-control dm-checkno', 'autocomplete'=>'nope']) !!}  
                    {!! $errors->first('managedcareid', '<p> :message</p>')  !!}                               
                </div>
                <div class="col-sm-1 col-xs-1 hide">
                    <a id="document_add_modal_link_managed_care_id" href="#document_add_modal" @if(strpos($currnet_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/insurance/'.$insurance->id.'/managed_care_id')}}" @else data-url="{{url('api/adddocumentmodal/insurance/0/managed_care_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
                </div>
                <div class="col-sm-1 col-xs-1"></div>
            </div> 

            <div class="form-group">                
                {!! Form::label('Medigapid', 'Medigap ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('medigapid')) error @endif ">
                    {!! Form::text('medigapid',null,['class'=>'form-control dm-medicare', 'autocomplete'=>'nope']) !!}  
                    {!! $errors->first('medigapid', '<p> :message</p>')  !!}                               
                </div>
                <div class="col-sm-1 col-xs-1 hide">
                    <a id="document_add_modal_link_medigap_id" href="#document_add_modal" @if(strpos($currnet_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/insurance/'.$insurance->id.'/medigap_id')}}" @else data-url="{{url('api/adddocumentmodal/insurance/0/medigap_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">                
                {!! Form::label('PayerID', 'Payer ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('payerid')) error @endif ">
                    {!! Form::text('payerid',null,['class'=>'form-control dm-medicare', 'autocomplete'=>'nope']) !!}  
                    {!! $errors->first('payerid', '<p> :message</p>')  !!}                               
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div> 
            <div class="form-group">
                {!! Form::label('ERA payerid', 'ERA Payer ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('era_payerid')) error @endif ">
                    {!! Form::text('era_payerid',null,['class'=>'form-control dm-medicare', 'autocomplete'=>'nope']) !!}   
                    {!! $errors->first('era_payerid', '<p> :message</p>')  !!}                              
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div> 
            <div class="form-group">
                {!! Form::label('Eligibility payerid', 'Eligibility Payer ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                            
                <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('eligibility_payerid')) error @endif ">
                    {!! Form::text('eligibility_payerid',null,['class'=>'form-control dm-medicare', 'autocomplete'=>'nope']) !!}   
                    {!! $errors->first('eligibility_payerid', '<p> :message</p>')  !!}                              
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">        
                {!! Form::label('Fee schedule', 'Fee schedule', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::textarea('feeschedule',null,['class'=>'form-control input-view-border1', 'autocomplete'=>'nope']) !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('Status', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}      
                <div class="control-group col-lg-6 col-md-6 col-sm-6">
                     @if(strpos($currnet_page, 'edit') !== false)
                    <?php 
					$checkInsInfo = App\Http\Controllers\Api\InsuranceApiController::checkPatientInsurance($insurance->id);   
					?>
                
                    {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    <!--@if($checkInsInfo == '')
                        {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red  js-checkins-delete','id'=>'c-inactive']) !!}
                    @else
                        {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','id'=>'c-inactive']) !!}
                    @endif
                    {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}-->
                    @else
                        {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'cc-active']) !!} {!! Form::label('cc-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; <!--
                                                {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','id'=>'cc-inactive']) !!} {!! Form::label('cc-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}-->
                    @endif
                </div>
                <div class="col-sm-1"></div>
            </div>  

            


        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->    

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-20"><!--  Right side Content Starts -->
    <div class="box no-shadow margin-b-10">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10 p-b-15">

            <div class="form-group">        
                {!! Form::label('Primaryfiling', 'Primary Timely Filing Days', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                    {!! Form::text('primaryfiling',null,['class'=>'form-control dm-filing-days', 'autocomplete'=>'nope']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('Secondayfiling', 'Secondary Timely Filing Days', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                    {!! Form::text('secondaryfiling',null,['class'=>'form-control dm-filing-days', 'autocomplete'=>'nope']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('Appealfiling', 'Appeal Filing Days', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                    {!! Form::text('appealfiling',null,['class'=>'form-control dm-filing-days', 'autocomplete'=>'nope']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div> 
            <div class="form-group bottom-space-10">
                {!! Form::label('Claimtype', 'Claim Type', ['class'=>'col-lg-5 col-md-4 col-sm-4 col-xs-12 control-label']) !!}      
                <div class="control-group col-lg-7 col-md-8 col-sm-8 col-xs-12">
                    {!! Form::radio('claimtype', 'Electronic',true,['class'=>'','id'=>'c-electronic']) !!} {!! Form::label('c-electronic', 'Electronic',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('claimtype', 'Paper',null,['class'=>'','id'=>'c-paper']) !!} {!! Form::label('c-paper', 'Paper',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>              
            </div>
            <!--div class="form-group">
                {!! Form::label('ClaimFormat', 'Claim Format', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}       
                <div class="control-group col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    @foreach($claimformats as $key=>$get_format)
                             <?php  $selectformat = ($key==1)? true:null;  ?>
                             @if($key!='2' and $key!='3')
                                    {!! Form::radio('claimformat',$key,$selectformat,['class'=>'flat-red']) !!} {{ $get_format }} &emsp; 
                             @endif 
                    @endforeach
                  
                    {!! Form::radio('claimformat', 'Institutional',true,['class'=>'flat-red']) !!}
                    Institutional 
                </div>                        
            </div-->                    
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->

    <div class="box no-shadow margin-b-10"><!-- Box Additional COntacts Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="notebook"></i> <h3 class="box-title">Additional Contacts</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10 p-b-30"><!-- Box Body Starts -->                     


            <div class="form-group">
                {!! Form::label('Claim Status Phone', 'Claim Status Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('claim_ph',null,['class'=>'form-control claim_phone dm-phone', 'autocomplete'=>'nope']) !!}
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    {!! Form::text('claim_ext',null,['class'=>'form-control dm-phone-ext', 'autocomplete'=>'nope']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Eligibility Phone', 'Eligibility Phone1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('eligibility_ph',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    {!! Form::text('eligibility_ext',null,['class'=>'form-control dm-phone-ext', 'autocomplete'=>'nope']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Eligibility Phone', 'Eligibility Phone2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('eligibility_ph2',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    {!! Form::text('eligibility_ext2',null,['class'=>'form-control dm-phone-ext', 'autocomplete'=>'nope']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Enrollment Phone', 'Enrollment Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('enrollment_ph',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    {!! Form::text('enrollment_ext',null,['class'=>'form-control dm-phone-ext', 'autocomplete'=>'nope']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Prior Auth Phone', 'Prior Auth Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('prior_ph',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    {!! Form::text('prior_ext',null,['class'=>'form-control dm-phone-ext', 'autocomplete'=>'nope']) !!}
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('Claim Status Fax', 'Claim Status Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('claim_fax',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
                </div>                        
            </div>                                           

            <div class="form-group">
                {!! Form::label('Eligibility Fax', 'Eligibility Fax1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('eligibility_fax',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
                </div>                        
            </div>                                           

            <div class="form-group">
                {!! Form::label('Eligibility Fax', 'Eligibility Fax2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('eligibility_fax2',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
                </div>                        
            </div>   

            <div class="form-group">
                {!! Form::label('Enrollment Fax', 'Enrollment Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('enrollment_fax',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
                </div>                        
            </div>

            <div class="form-group">
                {!! Form::label('Prior Auth Fax', 'Prior Auth Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('prior_fax',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
                </div>                        
            </div>   

        </div><!-- Box Body Ends -->
    </div> <!-- Box Additional contacts ends -->
</div><!--  Right side Content Ends -->          

<div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
    {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group submit_button']) !!}
    @if(strpos($currnet_page, 'edit') !== false)
    @if($checkpermission->check_url_permission('insurance/delete/{id}') == 1)
    <?php $checkInsInfo = App\Http\Controllers\Api\InsuranceApiController::checkPatientInsurance($insurance->id);   ?>
    <a @if($checkInsInfo == 1) class="js-delete-confirm"  data-text="Are you sure to delete the entry?" href="{{ url('insurance/delete/'.$insurance->id) }}" @else class="js-checkins-delete" @endif>{!! Form::button('Delete', ['class'=>'btn btn-medcubics']) !!}</a></center>
	@endif
<a href="javascript:void(0)" data-url="{{ url('insurance/'.$insurance->id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
@endif

@if(strpos($currnet_page, 'edit') == false)
<a href="javascript:void(0)" data-url="{{ url('insurance/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
@endif
</div>

<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- Modal Light Box Ends -->       

@push('view.scripts')
<script type="text/javascript">
    /* 
        Unique validation check for Practice insurance short name in INsurance name and state based  
    */
	$(document).ready(function(){
		$(".js-address-check").trigger("blur");
	});
	
	var base = "{{URL::to('/')}}";
    avator_url = base+"/img/insurance-avator.jpg";
    $("#state,#inurance_name " ).blur(function() {
        var insurancename = $('#inurance_name').val();
        if(insurancename !="") {
            var insurance_split = insurancename.split(' ');
            var ins_short_val ='';
            for(i= 0; i< insurance_split.length; i++)
            {
                var ins_short_val = ins_short_val + insurance_split[i].charAt(0);
            }
            var url_ins = api_site_url+'/shortname/ins_short_val/'+ins_short_val;
            $.ajax({        
                    url: url_ins,
                    type:'get',
                    success: function(data) {
                        var sht_name = $('#short_name').val(); 
                       // if((data == 0) && (sht_name.length >= 0 )) {
                        if((data == 0) && (sht_name.length == 0 )) {
                            $('#short_name').val(ins_short_val.toUpperCase());  
                           //  $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="short_name"]'));   
                            $('.ins_sht_name').css("display","none"); 
                        }
                        else if(data >0) 
                        {
                            $('#short_name').val(ins_short_val.toUpperCase());   
                            $('.ins_sht_name').html("Short Name Already Exits ").css('color','red');
                            /*  
                                Insurance name is unique called stateFocus  function called
                            */
                            stateFocus(ins_short_val);

                        }
                    }
            });
        }
        else
        {
                $('#short_name').val("");  
                $('.help-block').css("display","none"); 
                 //$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="short_name"]'));   
        }
        setTimeout(function() {
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="short_name"]'));   
        },1000);
       
    });/*
    $(document).on('ifToggled change','#short_name ',function(){
        var ins_short_val = $('#short_name').val();  
        ins_short_val = ins_short_val.toUpperCase();
        $('#short_name').val(ins_short_val); 
       // if(ins_short_val =="") 
          $('.ins_sht_name').css("display","none");                                       
    }); */
	/*  INsurance short name unique validation check in state based validation*/
	function stateFocus(shortname)
	{
		var state = $("#state").val();
		shortname = shortname+ state;
		 var url_ins = api_site_url+'/shortname/ins_short_val/'+shortname;
			$.ajax({        
					url: url_ins,
					type:'get',
					success: function(data) {
						var sht_name = $('#short_name').val(); 
						if((data == 0) && (sht_name != '')) {
							$('#short_name').val(shortname.toUpperCase());     
							$('.ins_sht_name').css("display","none");          
							$('#short_name .help-block').css("display","none");                                       
						}
						else if(data >0) 
						{
							$('#short_name').val(shortname.toUpperCase());   
							$('.ins_sht_name').html("Short Name Already Exits ").css('color','red');
						}
					}
			});
	}
	
    $(document).on('ifToggled click','.js-checkins-delete', function (event) { 
        if($(this).is(':checked') == true){
            js_alert_popup(error_insurance_del_msg);
            $('.submit_button').attr('disabled','disabled');
        }else{
            $('.submit_button').removeAttr('disabled','disabled');
        }
    });

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

    $(document).on("keyup",".js_all_caps_ins", function(){
        var str_upper = $(this).val().toUpperCase();
        var start = this.selectionStart,
        end = this.selectionEnd;
        $(this).val(str_upper);
        this.setSelectionRange(start, end);
    });
        
    var error_insurance_del_msg = '{{ trans("practice/practicemaster/insurance.validation.insurance_del_msg") }}';

    $(document).ready(function () {
        $('.js-address-check').blur();
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
                        insurance_name: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/insurance.validation.insurance_name") }}'
                                },
                                /* callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var regexp = new RegExp(/^[a-zA-Z0-9 ]+$/);
                                        if (!regexp.test(value) && value.length > 1) {
                                            return {
                                                valid: false,
                                                message: '{{ trans("common.validation.alphanumericspac") }}'
                                            };
                                        }
                                        return true;
                                    }
                                }, */
                            }
                        },
                        image: {
                            validators: {
                                file: {
                                    extension: 'jpeg,png,jpg',
                                    type: 'image/jpg,image/jpeg,image/png',
                                    maxSize: 2048 * 1024,   // 2 MB
                                    message: '{{ trans("common.validation.image_maxsize_valid") }}'
                                }
                            }
                        },
                        short_name: {
                            message: '',
                            validators: {
                               callback: {
                                    message: '{{ trans("common.validation.shortname_regex") }}',
                                    callback: function (value, validator) {
                                        var get_val = $('#short_name').val();
                                         var pattern = new RegExp(/[~`!#$@%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/); 
                                         if(get_val == ''){
                                            //Insurance short name unique validation based on validation here
                                             $('.ins_sht_name').css("display","none"); 
                                            return {
                                                    valid: false,
                                                    message: '{{ trans("practice/practicemaster/insurance.validation.short_name") }}'
                                                };
                                         }

                                        if (get_val != '' && get_val.length < 3){
                                            $('.ins_sht_name').css("display","none"); 
                                            return {
                                                    valid: false,
                                                    message: '{{ trans("practice/practicemaster/insurance.validation.short_name_min") }}'
                                                };
                                        } 
                                        else if(pattern.test(get_val)){
                                            $('.ins_sht_name').css("display","none"); 
                                            return {
                                                    valid: false,
                                                    message: '{{ trans("common.validation.alphanumericspac") }}'
                                                };
                                        }
                                            
                                        return true;
                                    }
                                }
                            }
                        },
                        phone1: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator, $field) {
                                        var phone_msg = '{{ trans("common.validation.phone_limit") }}';
                                        var ext_msg = '{{ trans("common.validation.phone") }}';
                                        $fields = validator.getFieldElements('phone1');
                                        var ext_length = $fields.closest("div").next().next().find("input").val().length;
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
                        claim_ph: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator, $field) {
                                        var phone_msg = '{{ trans("common.validation.phone_limit") }}';
                                        var ext_msg = '{{ trans("common.validation.phone") }}';
                                        $fields = validator.getFieldElements('claim_ph');
                                        var ext_length = $fields.closest("div").next().next().find("input").val().length;
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
                        eligibility_ph: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator, $field) {
                                        var phone_msg = '{{ trans("common.validation.phone_limit") }}';
                                        var ext_msg = '{{ trans("common.validation.phone") }}';
                                        $fields = validator.getFieldElements('eligibility_ph');
                                        var ext_length = $fields.closest("div").next().next().find("input").val().length;
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
                        eligibility_ph2: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator, $field) {
                                        var phone_msg = '{{ trans("common.validation.phone_limit") }}';
                                        var ext_msg = '{{ trans("common.validation.phone") }}';
                                        $fields = validator.getFieldElements('eligibility_ph2');
                                        var ext_length = $fields.closest("div").next().next().find("input").val().length;
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
                        enrollment_ph: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator, $field) {
                                        var phone_msg = '{{ trans("common.validation.phone_limit") }}';
                                        var ext_msg = '{{ trans("common.validation.phone") }}';
                                        $fields = validator.getFieldElements('enrollment_ph');
                                        var ext_length = $fields.closest("div").next().next().find("input").val().length;
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
                        prior_ph: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator, $field) {
                                        var phone_msg = '{{ trans("common.validation.phone_limit") }}';
                                        var ext_msg = '{{ trans("common.validation.phone") }}';
                                        $fields = validator.getFieldElements('prior_ph');
                                        var ext_length = $fields.closest("div").next().next().find("input").val().length;
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
                        claim_fax: {
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
                        eligibility_fax: {
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
                        eligibility_fax2: {
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
                        enrollment_fax: {
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
                        prior_fax: {
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
                        address_1: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var msg = addressValidation(value,"required");
                                        if(msg != true){
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
                                        if(msg != true){
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
                                        var msg = cityValidation(value,"required");
                                        if(msg != true){
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
                                        var msg = stateValidation(value,"required");
                                        if(msg != true){
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
                                        var msg = zip5Validation(value,"required");
                                        if(msg != true){
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
                                        if(msg != true){
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
                        managedcareid: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[0-9]{0,15}$/,
                                    message: '{{ trans("common.validation.numeric") }}'
                                }
                            }
                        },
                        insurancetype_id: {
                             message: '',
                             validators: {
                                 notEmpty: {
                                     message: '{{ trans("practice/practicemaster/insurance.validation.insurancetype_id") }}'
                                 }
                             }
                         }, /*
                        newadded: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[A-Za-z]{0,50}$/,
                                    message: '{{ trans("common.validation.alpha") }}'
                                }
                            }
                        }, */
                        medigapid: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[A-Za-z0-9]{0,50}$/,
                                    message: '{{ trans("common.validation.alphanumeric") }}'
                                }
                            }
                        },
                        payerid: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[A-Za-z0-9]{0,50}$/,
                                    message: '{{ trans("common.validation.alphanumeric") }}'
                                }
                            }
                        },
                        era_payerid: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[A-Za-z0-9]{0,50}$/,
                                    message: '{{ trans("common.validation.alphanumeric") }}'
                                }
                            }
                        },
                        eligibility_payerid: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[A-Za-z0-9]{0,50}$/,
                                    message: '{{ trans("common.validation.alphanumeric") }}'
                                }
                            }
                        },
                        feeschedule: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var msg = lengthValidation(value,'feeschedule');
                                        if(msg != true){
                                            return {
                                                valid: false,
                                                message: msg
                                            };
                                        }
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                });
    });
    
    
// Start to insurance serach option in practice.
function Ins_validateIt(formname,ins,search_by) {
    var validator = $(formname).bootstrapValidator({
        feedbackIcons: {
            validating: "glyphicon glyphicon-refresh"
        },
        fields: {
            insurance_list:{
                message:'',
                validators:{
                    notEmpty:{
                        message: '{{ trans("practice/practicemaster/insurance.validation.insurance_name") }}'
                    }
                }
            }
        }
    }).on('success.form.bv', function(e) {
            $(".js_inscontent").html('<i class="fa fa-spinner fa-spin font20"></i> Processing');
            if(formname == '#js-bootstrap-validator_ins') {
                e.preventDefault();
                //Popup open from layout/popupmodal.blade.php
                $('#patient_statement_modal').find('.modal-title').html('Insurance Search');
                $('#patient_statement_modal').modal('show');
                $('#patient_statement_modal .js_insurance_search_popup').removeClass('modal-md');
                $('#patient_statement_modal .js_insurance_search_popup').addClass('modal-lg');
                var insurancename = $('#insurance_list').val();
                var search_category = $('select.js_insurace_search_category_modal').val();
                var insurancename = insurancename.replace(/[\.\%]/g, '');               
                $.get(api_site_url+"/get_insurancelist/"+insurancename+"/"+search_category,function(data){                    
                    $('.js_inscontent').html(data);
                    //setTimeout(function(){ callicheck(); }, 100);
                    $('.insurance_list').val(insurancename);   
                    console.log(search_category);         
                   $("#js_insurace_search_category_popmodal").select2("val", search_category).trigger("change"); 
                });
            }else if(formname =='#js-bootstrap-validatorpopup_ins'){
                $.get(api_site_url+"/get_insurancelist/"+ins+"/"+search_by,function(data){
                    $('.js_inscontent').html(data);
                    setTimeout(function(){ callicheck(); }, 100);                  
                    $("#js_insurace_search_category_popmodal option[value="+search_by+"]").attr('selected', 'selected');                   
                    $('#insurancepopup_list').val(ins);                      
                    $('#insurance_list').val(ins);
                   $("select.js_insurace_search_category_modal").select2("val", search_by).trigger("change"); 
                });
            }

    })
}

$(document).on('click',"#js-search-ins",function () {
    var bootstrapinsformname = '#js-bootstrap-validator_ins';
    Ins_validateIt(bootstrapinsformname);
    $(bootstrapinsformname).bootstrapValidator('validate');
});

$(document).on('click',"#js-search-popupins",function () {
        var bootstrapinsformname = '#js-bootstrap-validatorpopup_ins';
        var insurancename = $('#insurancepopup_list').val();
        var search_by = $('#js_insurace_search_category_popmodal').val();
        var insurancename = insurancename.replace(/[\.\%]/g, '');
        Ins_validateIt(bootstrapinsformname,insurancename,search_by); 
        $(bootstrapinsformname).bootstrapValidator('validate');
});

$(document).on('ifToggled click',".get_insurance_select",function () {
    var insid = $(this).data('value');
    $(".js_inscontent").html('<i class="fa fa-spinner fa-spin font20"></i> Processing');
    $.get(api_site_url+"/implement_insurance/"+insid,function(data){
        $('#js-bootstrap-validator').bootstrapValidator('resetForm', true);
        $(".js_inscontent").html('');
        $('#inurance_name').val(data[0].insurance_name);
        $('#short_name').val(data[0].short_name);
        $('[name=insurance_desc]').val(data[0].insurance_desc);
        
        $('[name=address_1]').val(data[0].address_1);
        $('[name=address_2]').val(data[0].address_2);
        $('[name=city]').val(data[0].city);
        $('[name=state]').val(data[0].state);
        $('[name=zipcode5]').val(data[0].zipcode5);
        $('[name=zipcode4]').val(data[0].zipcode4);
        
        
        $('[name=phone1]').val(data[0].phone1);
        $('[name=phoneext]').val(data[0].phoneext);
        $('[name=fax]').val(data[0].fax);
        $('[name=email]').val(data[0].email);
        $('[name=website]').val(data[0].website);
        
        $('[name=primaryfiling]').val(data[0].primaryfiling);
        $('[name=secondaryfiling]').val(data[0].secondaryfiling);
        $('[name=appealfiling]').val(data[0].appealfiling);
        
        $('[name=managedcareid]').val(data[0].managedcareid);
        $('[name=medigapid]').val(data[0].medigapid);
        $('[name=payerid]').val(data[0].payerid);
        $('[name=era_payerid]').val(data[0].era_payerid);
        $('[name=eligibility_payerid]').val(data[0].eligibility_payerid);
        $('[name=feeschedule]').val(data[0].feeschedule);
        
        $('[name=claim_ph]').val(data[0].claim_ph);
        $('[name=claim_ext]').val(data[0].claim_ext);
        $('[name=eligibility_ph]').val(data[0].eligibility_ph);
        $('[name=eligibility_ext]').val(data[0].eligibility_ext);
        $('[name=eligibility_ph2]').val(data[0].eligibility_ph2);
        $('[name=eligibility_ext2]').val(data[0].eligibility_ext2);
        $('[name=enrollment_ph]').val(data[0].enrollment_ph);
        $('[name=enrollment_ext]').val(data[0].enrollment_ext);
        $('[name=prior_ph]').val(data[0].prior_ph);
        $('[name=prior_ext]').val(data[0].prior_ext);
        $('[name=claim_fax]').val(data[0].claim_fax);
        $('[name=eligibility_fax]').val(data[0].eligibility_fax);
        $('[name=eligibility_fax2]').val(data[0].eligibility_fax2);
        $('[name=enrollment_fax]').val(data[0].enrollment_fax);
        $('[name=prior_fax]').val(data[0].prior_fax);
        
        $('[name=avatar_name]').val(data[0].avatar_name);
        $('[name=avatar_ext]').val(data[0].avatar_ext);
        
        if(data[0].claimtype == 'Paper'){
            $("[value=Paper]").prop("checked", true)
        }
        else{
            $("[value=Electronic]").prop("checked", true)
        }
        
        if(data[0].enrollment == 'Unknown'){
            $("[name=enrollment]").prop("checked", false)
        }
        else if(data[0].enrollment == 'Yes'){
            $("[name=enrollment][value=Yes]").prop("checked", true)
        }
        else{
           if(data[0].enrollment !='')
                $("[name=enrollment][value=No]").prop("checked", true)
        }
        
        if(data[0].status == 'Active'){
            $("[name=status][value=Active]").prop("checked", true)
        }
        else{
            $("[name=status][value=Inactive]").prop("checked", true)
        }
        
        var ins_default_id = '{{ Config::get("siteconfigs.insurance_type_id.default_id") }}';
        
        if(data[0].insurancetype_id != '0'){
            $("[name=insurancetype_id]").val(data[0].insurancetype_id).trigger("change");
        }
        else{
            //$("[name=insurancetype_id]").val(ins_default_id).trigger("change");
        } 
        
        if(data[1] != 'no-image') {
            $('.js_exist_image').css('line-height','14px');
            $('.js_exist_image').css('word-wrap','break-word');
            $('.js_exist_image').html(data[1]);
            $('.fileupload').removeClass('fileupload-new').addClass('fileupload-exists');
            
            if($('.js-delete-image')[0]==undefined){
                $('.fileupload-remove').removeClass('hide');
            } 
        }
        
        $('input.flat-red').iCheck('update');
        $('#js-bootstrap-validator').bootstrapValidator('validate');
        $('#patient_statement_modal').modal('hide');        
    });
});

$(document).keypress(function (e) {
    if(e.which == 13)
    {
        if($("#insurance_list").is(":focus") == true)
        {
            $( "#js-search-ins" ).trigger( "click" );   
        }
        else if($("#insurancepopup_list").is(":focus") == true)
        {
            $( "#js-search-popupins" ).trigger( "click" );  
        }
        else
        {
            $( "#js-bootstrap-validator" ).submit();
        }
    }
});

// End to insurance serach option in practice.
    
</script>
@endpush