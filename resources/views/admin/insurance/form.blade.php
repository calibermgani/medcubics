<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.insurance_details") }}' />

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-13"><!--  Left side Content Starts -->

    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="briefcase"></i> <h3 class="box-title">Business Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body form-horizontal margin-l-10">

            <?php $current_page = Route::getFacadeRoot()->current()->uri(); ?>
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
                    {!! Form::label('Address 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address_1')) error @endif">                                                     
                        {!! Form::text('address_1',null,['id'=>'address_1','class'=>'form-control js-address-check dm-address']) !!}
                        {!! $errors->first('address_1', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>

                <div class="form-group">
                    {!! Form::label('Address 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address_2')) error @endif">                            
                        {!! Form::text('address_2',null,['id'=>'address_2','class'=>'form-control js-address2-tab dm-address']) !!}
                        {!! $errors->first('address_2', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>                 

                <div class="form-group">
                    {!! Form::label('City', 'City', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                  
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">  
                        {!! Form::text('city',null,['class'=>'form-control js-address-check dm-address','id'=>'city']) !!}
                    </div>
                    {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"> 
                        {!! Form::text('state',null,['class'=>'form-control js-address-check js-state-tab dm-state','id'=>'state']) !!}
                    </div>
                </div>  


                <div class="form-group no-bottom">
                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                        {!! Form::text('zipcode5',null,['class'=>'form-control js-address-check dm-zip5','id'=>'zipcode5']) !!}
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">                             
                        {!! Form::text('zipcode4',null,['class'=>'form-control js-address-check dm-zip4','id'=>'zipcode4']) !!}
                    </div>
                    <div class="col-md-1 col-sm-2 col-xs-2">            
                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
                        <?php echo $value; ?>
                    </div>
                </div>

            </div>
            
            <div class="margin-b-1 hidden-sm hidden-xs">&emsp;</div>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->



    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="shield"></i> <h3 class="box-title">Credentials</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10">

            <div class="js-add-new-select" id="js-insurance-type">
                <div class="form-group js_common_ins">
                    {!! Form::label('InsuranceType', 'Insurance Type', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                                                 
                    <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('insurancetype_id')) error @endif ">
						 @if(strpos($current_page, 'edit') !== false)
						   <?php  $insurancetype_id =  $insurancetype_id; ?>
						   @else
						    <?php  $insurancetype_id = Config::get('siteconfigs.insurance_type_id.default_id'); ?>
						   @endif
                        {!! Form::select('insurancetype_id', array('' => '-- Select --') + (array)$insurancetypes,  $insurancetype_id,['class'=>'form-control select2 js-add-new-select-opt']) !!}
                        {!! $errors->first('insurancetype_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>
				<div id="add_new_span" class="hide">
					<div class="form-group">
						{!! Form::label('InsuranceType', 'Insurance Type', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
						<div class="col-lg-4 col-md-6 col-sm-7 col-xs-10">
							{!! Form::text('newadded',null,['id'=>'newadded','class'=>'form-control','placeholder'=>'Add new Insurance Type','data-table-name'=>'insurancetypes','data-field-name'=>'type_name','data-field-id'=>$insurancetype_id,'data-label-name'=>'insurance type','maxlength'=>100]) !!}
							<p class="js-error help-block hide"></p>
							
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('cmsType', 'CMS Type', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label star']) !!} 
						<div class="col-lg-4 col-md-6 col-sm-7 col-xs-10">
							{!! Form::select('cms_type', (array)@$cmstypes,  null,['class'=>'form-control select2','id'=>'newadded_cms_type','data-table-name'=>'insurancetypes','data-field-name'=>'cms_type','data-field-id'=>$insurancetype_id,'data-label-name'=>'cms type']) !!}
							<p class="pull-right no-bottom">	
								 <i class="fa fa-save med-green" id="add_new_save" data-placement="bottom"  data-toggle="tooltip" data-original-title="Save"></i>
								<i class="fa fa-ban med-green margin-l-5" id="add_new_cancel" data-placement="bottom"  data-toggle="tooltip" data-original-title="Cancel"></i> 
							</p>
						</div>
					</div>
				</div>
            </div>

            <div class="form-group">
                {!! Form::label('Enrollment', 'Enrollment Required', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}       
                <div class="control-group col-lg-8 col-md-8 col-sm-8">
                    {!! Form::radio('enrollment', 'Yes',null,['class'=>'','id'=>'en_yes']) !!} {!! Form::label('en_yes', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                    {!! Form::radio('enrollment', 'No',true,['class'=>'','id'=>'en_no']) !!} {!! Form::label('en_no', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div> 
            {!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}

            <div class="form-group">                
                {!! Form::label('Managedcareid', 'Managed Care ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                            
                <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('managedcareid')) error @endif ">
                    {!! Form::text('managedcareid',null,['class'=>'form-control dm-checkno']) !!}
                    {!! $errors->first('managedcareid', '<p> :message</p>')  !!}

                </div>
                <div class="col-sm-1 col-xs-2">
                                                <!-- <a id="document_add_modal_link_managed_care_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/insurance/'.$insurance->id.'/managed_care_id')}}" @else data-url="{{url('api/adddocumentmodal/insurance/0/managed_care_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('app.document_upload_modal_icon')}}"></i></a> -->
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Medigapid', 'Medigap ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                            
                <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('medigapid')) error @endif ">
                    {!! Form::text('medigapid',null,['class'=>'form-control dm-medicare js_space_restrict']) !!}
                    {!! $errors->first('medigapid', '<p> :message</p>')  !!}

                </div>
                <div class="col-sm-1 col-xs-2">
                                                <!-- <a id="document_add_modal_link_medigap_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/insurance/'.$insurance->id.'/medigap_id')}}" @else data-url="{{url('api/adddocumentmodal/insurance/0/medigap_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('app.document_upload_modal_icon')}}"></i></a> -->
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('PayerID', 'Payer ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                            
                <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('payerid')) error @endif ">
                    {!! Form::text('payerid',null,['class'=>'form-control dm-medicare js_space_restrict']) !!}
                    {!! $errors->first('payerid', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('ERApayerid', 'ERA Payer ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                            
                <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('era_payerid')) error @endif ">
                    {!! Form::text('era_payerid',null,['class'=>'form-control dm-medicare js_space_restrict']) !!}
                    {!! $errors->first('era_payerid', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Eligibilitypayerid', 'Eligibility Payer ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                            
                <div class="col-lg-4 col-md-6 col-sm-7 col-xs-10 @if($errors->first('eligibility_payerid')) error @endif ">
                    {!! Form::text('eligibility_payerid',null,['class'=>'form-control dm-medicare js_space_restrict']) !!}
                    {!! $errors->first('eligibility_payerid', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                {!! Form::label('Fee schedule', 'Fee schedule', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                                                                                 
                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                    {!! Form::textarea('feeschedule',null,['class'=>'form-control input-view-border1']) !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                {!! Form::label('Status', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}      
                <div class="control-group col-lg-6 col-md-6 col-sm-6">
                    {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'ins_active']) !!} {!! Form::label('ins_active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'ins_inactive']) !!} {!! Form::label('ins_inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>  

        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-13"><!--  Right side Content Starts -->

    <!--- Additional Address -->
    <!--- Additional Address -->

    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10">

            <div class="form-group">
                {!! Form::label('Primaryfiling', 'Primary Timely Filing Days', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label']) !!}                                                                                 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">
                    {!! Form::text('primaryfiling',null,['class'=>'form-control dm-filing-days']) !!}
                </div>                        
            </div>

            <div class="form-group">
                {!! Form::label('Secondayfiling', 'Secondary Timely Filing Days', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label']) !!}                                                                                 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">
                    {!! Form::text('secondaryfiling',null,['class'=>'form-control dm-filing-days']) !!}
                </div>                        
            </div>

            <div class="form-group">
                {!! Form::label('Appealfiling', 'Appeal Filing Days', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label']) !!}                                                                                 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">
                    {!! Form::text('appealfiling',null,['class'=>'form-control dm-filing-days']) !!}
                </div>                        
            </div>

            <div class="form-group margin-t-20 margin-b-15">
                {!! Form::label('Claimtype', 'Claim Type', ['class'=>'col-lg-5 col-md-4 col-sm-4 col-xs-12 control-label']) !!}      
                <div class="control-group col-lg-6 col-md-6 col-sm-6">
                    {!! Form::radio('claimtype', 'Electronic',true,['class'=>'','id'=>'c_electronic']) !!} {!! Form::label('c_electronic', 'Electronic',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                    {!! Form::radio('claimtype', 'Paper',null,['class'=>'','id'=>'c_paper']) !!} {!! Form::label('c_paper', 'Paper',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                        
            </div>
            <!-- <div class="form-group">
{!! Form::label('ClaimFormat', 'Claim Format', ['class'=>'col-lg-5 col-md-4 col-sm-4 col-xs-12 control-label']) !!}       
<div class="control-group col-lg-7 col-md-8 col-sm-8">
    @foreach($claimformats as $key=>$get_format)
                     <?php  $selectformat = ($key==1)? true:null;  ?>
                      @if($key!='2' and $key!='3')
                            {!! Form::radio('claimformat',$key,$selectformat,['class'=>'flat-red']) !!} {{ $get_format }} &emsp; 
                      @endif	
            @endforeach
                    {!! Form::radio('claimformat', 'Institutional',true,['class'=>'flat-red']) !!}
                    Institutional 
</div>

</div>-->


        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->


    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="notebook"></i> <h3 class="box-title">Additional Contacts</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10">


            <div class="form-group">
                {!! Form::label('Claim Status Phone', 'Claim Status Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('claim_ph',null,['class'=>'form-control dm-phone']) !!}
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    {!! Form::text('claim_ext',null,['class'=>'form-control dm-phone-ext']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Eligibility Phone', 'Eligibility Phone1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('eligibility_ph',null,['class'=>'form-control dm-phone']) !!}
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    {!! Form::text('eligibility_ext',null,['class'=>'form-control dm-phone-ext']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Eligibility Phone', 'Eligibility Phone2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('eligibility_ph2',null,['class'=>'form-control dm-phone']) !!}
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    {!! Form::text('eligibility_ext2',null,['class'=>'form-control dm-phone-ext']) !!}
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('Enrollment Phone', 'Enrollment Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('enrollment_ph',null,['class'=>'form-control dm-phone']) !!}
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    {!! Form::text('enrollment_ext',null,['class'=>'form-control dm-phone-ext']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Prior Auth Phone', 'Prior Auth Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('prior_ph',null,['class'=>'form-control dm-phone']) !!}
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    {!! Form::text('prior_ext',null,['class'=>'form-control dm-phone-ext']) !!}
                </div>
            </div>                                        

            <div class="form-group">
                {!! Form::label('Claim Status Fax', 'Claim Status Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('claim_fax',null,['class'=>'form-control dm-phone']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Eligibility Fax', 'Eligibility Fax1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('eligibility_fax',null,['class'=>'form-control dm-phone']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Eligibility Fax', 'Eligibility Fax2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('eligibility_fax2',null,['class'=>'form-control dm-phone']) !!}
                </div>
            </div>   

            <div class="form-group">
                {!! Form::label('Enrollment Fax', 'Enrollment Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('enrollment_fax',null,['class'=>'form-control dm-phone']) !!}
                </div>                        
            </div>

            <div class="form-group">
                {!! Form::label('Prior Auth Fax', 'Prior Auth Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                    {!! Form::text('prior_fax',null,['class'=>'form-control dm-phone']) !!}
                </div>                        
            </div>   
            <div class="margin-b-1 hidden-sm hidden-xs">&emsp;</div>
        </div>
    </div>

</div><!--  Right side Content Ends -->


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
    {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}


    @if(strpos($current_page , 'edit') !== false && $checkpermission->check_adminurl_permission('admin/insurance/delete/{id}') == 1)
    <a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure you want to delete?" href="{{ url('admin/insurance/delete/'.$insurance->id) }}">Delete</a></center>

<a href="javascript:void(0)" data-url="{{ url('admin/insurance/'.$insurance->id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
@endif


@if(strpos($current_page, 'edit') == false)
<a href="javascript:void(0)" data-url="{{ url('admin/insurance/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
@endif
</div>


<!-- Modal Light Box starts -->
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- Modal Light Box Ends -->

@push('view.scripts')
<script type="text/javascript">
if ($("div").hasClass("js-add-new-select")) {
    $("div.js-add-new-select").find('select:not("#newadded_cms_type")').append('<optgroup label="Others"><option value="0">Add New</option></optgroup>');
}

$(document).on('change', '.js-add-new-select-opt', function (event) {
    //$('.js-add-new-select-opt').change(function(){
    var current_divid = $(this).parents('div.js-add-new-select').attr('id');
    var selected_value = $(this).val();
    $('#' + current_divid).find('p.js-error').html('').removeClass('show').addClass('hide');
    if (selected_value == '0') {
        $(this).closest('.js_common_ins').addClass('hide');
        $('#' + current_divid).children("#add_new_span").removeClass('hide').addClass('show');
        $('#' + current_divid).find('#newadded').val('');
        $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
    } else {
        $("#add_new_span").removeClass('show').addClass('hide');
        $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
    }
});

$(document).on('keyup', '#newadded', function () {
    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
    if ($(this).val() != null) {
        var seldivid = $(this).parents('div.js-add-new-select').attr('id');
        $('#' + seldivid).find('p.js-error').removeClass('show').addClass('hide');
    }
});

$(document).on("click", 'div.js-add-new-select #add_new_save', function () {
    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
    //$('.js-add-new-select-opt').parents('div.js-add-new-select').find('#add_new_save').click(function(){
    var lblname = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-label-name');
    var insurance_type = $(this).parents('div.js-add-new-select').find("#newadded").val();
    var regex = new RegExp("^[a-zA-Z ]+$");
    if (!insurance_type || !regex.test(insurance_type)) {
        $(this).parents('div.js-add-new-select').find("#newadded").parent('div').addClass('has-error');
        $(this).parents('div.js-add-new-select').find('p.js-error').html('');
        if (!insurance_type) {
            $(this).parents('div.js-add-new-select').find('p.js-error').html(insurancetype + ' ' + lblname);
        } else {
            $(this).parents('div.js-add-new-select').find('p.js-error').html(only_alpha_lang_err_msg);
        }
        $(this).parents('div.js-add-new-select').find('p.js-error').removeClass('hide').addClass('show');
    } else {
        $(this).parents('div.js-add-new-select').find("#newadded").parent('div').removeClass('has-error');
        var tablename = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-table-name');
        var fieldname = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-field-name');
        var addedvalue = $(this).parents('div.js-add-new-select').find('#newadded').val();
        var seldivid = $(this).parents('div.js-add-new-select').attr('id');
        var pars = 'tablename=' + tablename + '&fieldname=' + fieldname + '&addedvalue=' + addedvalue;

        if (seldivid == 'js-insurance-type' && $('#newadded_cms_type').length) {
            var insCmsType = $(this).parents('div.js-add-new-select').find('#newadded_cms_type').val();
            pars = pars + '&cms_type=' + insCmsType;
        }

        var value = addedvalue.trim();
        var changed_string = value.toLowerCase();
        if (changed_string != 'App' && changed_string != "app") {
            url_path = (window.location.pathname).split("/");
            if (url_path[2] == 'templates') {
                $.ajax({
                    url: api_site_url + '/addnewselect',
                    type: 'get',
                    data: pars,
                    success: function (data) {
                        if (data == '2') {
                            $('#' + seldivid).find("#newadded").parent('div').addClass('has-error');
                            $('#' + seldivid).find('p.js-error').html(exist_insurancetype + ' ' + lblname);
                            $('#' + seldivid).find('p.js-error').removeClass('hide').addClass('show');
                            $('input[name="hold_reason_exist"]').val(1); // For hold readon add new it was added                            
                        } else {
                            $('#' + seldivid).find("#add_new_span").removeClass('show').addClass('hide');
                            $('#' + seldivid).find(".js_common_ins").removeClass('hide').addClass('show');
                            $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
                            getoptionvalues(tablename, fieldname, seldivid, addedvalue);
                        }
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }//Template if
        }//App if

        $.ajax({
            url: api_site_url + '/addnewselect',
            type: 'get',
            data: pars,
            success: function (data) {
                if (data == '2') {
                    $('#' + seldivid).find("#newadded").parent('div').addClass('has-error');
                    $('#' + seldivid).find('p.js-error').html(exist_insurancetype + ' ' + lblname);
                    $('#' + seldivid).find('p.js-error').removeClass('hide').addClass('show');
                    $('input[name="hold_reason_exist"]').val(1); // For hold readon add new it was added

                } else {
                    //$("#add_new_span").removeClass('show').addClass('hide');                  
                    //$('.js_common_ins').removeClass('hide').addClass('show');

                    $('#' + seldivid).find("#add_new_span").removeClass('show').addClass('hide');
                    $('#' + seldivid).find(".js_common_ins").removeClass('hide').addClass('show');
                    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
                    getoptionvalues(tablename, fieldname, seldivid, addedvalue);
                }
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    if ($(this).parents('div.js-add-new-select').hasClass('hold-option')) {
        $('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'hold_reason_id', true);
        $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'hold_reason_id');
        var hold_reason_val = $('input[name="hold_reason_exist"]').val();
        setTimeout(function () {
            if ($('input[name="other_reason"]').val() != '' && !hold_reason_val) {
                $('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'other_reason', false);
                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'other_reason');
            } else {
                $('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'other_reason', true);
                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'other_reason');
            }
        }, 500);
    }
});

$(document).on("click", "div.js-add-new-select #add_new_cancel", function () {
    //$('.js-add-new-select-opt').parents('div.js-add-new-select').find('#add_new_cancel').click(function(){        
    $(this).parents('div.js-add-new-select').find("#newadded").parent('div').removeClass('has-error');
    $(this).parents('div.js-add-new-select').find("#add_new_span").removeClass('show').addClass('hide');
    var seldivid = $(this).parents('div.js-add-new-select').attr('id');
    $(this).parents('#' + seldivid).find('.js-add-new-select-opt').closest('.js_common_ins').removeClass('hide').addClass('show');
    $(this).parents('#' + seldivid).find('.js-add-new-select-opt').select2("val", "");
    if ($(this).parents('div.js-add-new-select').hasClass('hold-option')) {
        $('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'hold_reason_id', true);
        $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'hold_reason_id');
    }
    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
});

function getoptionvalues(tablename, fieldname, seldivid, addedvalue) {
    $.ajax({
        type: "GET",
        url: api_site_url + '/getoptionvalues',
        data: 'tablename=' + tablename + '&fieldname=' + fieldname + '&addedvalue=' + addedvalue,
        success: function (data) {
            $('#' + seldivid).find("select.js-add-new-select-opt").html(data);
            if ($('#' + seldivid).find("select.js-add-new-select-opt").attr('id') == 'js-hold-reason') {
                $('#js-hold-reason').change();
            } else {
                $('#' + seldivid).find("select.js-add-new-select-opt").select2();
            }
        }
    });
}

$(document).on("keyup",".js_all_caps_ins", function(){
		var str_upper = $(this).val().toUpperCase();
		var start = this.selectionStart,
		end = this.selectionEnd;
		$(this).val(str_upper);
		this.setSelectionRange(start, end);
	});


$(document).on('change', '.btn-file input[type="file"]', function (e) {
	if($(this).val() ==""){
		$(".fileupload.fileupload-exists .fileupload-preview").find("img").attr('src', $(".fileupload .js_default_img").attr('src'));
	}
		e.preventDefault();
		setTimeout(function(){
			var new_file = $(".fileupload").hasClass('fileupload-new'); 
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
				}
				else {
					$('[name="avatar_url"]').val("");
					$(".fileupload").addClass('fileupload-new').removeClass("fileupload-exists");
				}
				$(".fileupload fileupload-preview.fileupload-exists.thumbnail").html('');
			}
		}
	});
	$(document).on('keypress','.js_space_restrict', function (e) {
		var regex = new RegExp("^[a-zA-Z0-9]+$");
		var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
		if (regex.test(str)) {
			return true;
		}

		e.preventDefault();
		return false;
	});
    $(document).ready(function () {
	
    /* $('[name="fax"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('fax', 'NOT_VALIDATED')
     .validateField('fax');
     });
     
     
     $('[name="phone1"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('phone1', 'NOT_VALIDATED')
     .validateField('phone1');
     });
     
     $('[name="claim_ph"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('claim_ph', 'NOT_VALIDATED')
     .validateField('claim_ph');
     });
     
     $('[name="eligibility_ph"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('eligibility_ph', 'NOT_VALIDATED')
     .validateField('eligibility_ph');
     });
     
     $('[name="eligibility_ph2"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('eligibility_ph2', 'NOT_VALIDATED')
     .validateField('eligibility_ph2');
     });
     
     $('[name="enrollment_ph"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('enrollment_ph', 'NOT_VALIDATED')
     .validateField('enrollment_ph');
     });
     
     $('[name="prior_ph"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('prior_ph', 'NOT_VALIDATED')
     .validateField('prior_ph');
     });
     
     $('[name="claim_fax"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('claim_fax', 'NOT_VALIDATED')
     .validateField('claim_fax');
     });
     
     $('[name="eligibility_fax"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('eligibility_fax', 'NOT_VALIDATED')
     .validateField('eligibility_fax');
     });
     
     $('[name="eligibility_fax2"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('eligibility_fax2', 'NOT_VALIDATED')
     .validateField('eligibility_fax2');
     });
     
     $('[name="enrollment_fax"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('enrollment_fax', 'NOT_VALIDATED')
     .validateField('enrollment_fax');
     });
     
     $('[name="prior_fax"]').on('change', function () {
     $('#js-bootstrap-validator')
     .data('bootstrapValidator')
     .updateStatus('prior_fax', 'NOT_VALIDATED')
     .validateField('prior_fax');
     });
     
     */
   
            $('#js-bootstrap-validator')
            .bootstrapValidator({
            message: '',
                    excluded: ':disabled',
                    feedbackIcons: {
                    valid: '',
					invalid: '',
					validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                    insurance_name: {
                    message: '',
                            validators: {
                            notEmpty: {
                            message: '{{ trans("admin/insurance.validation.insurance_name") }}'
                            },
                                    callback: {
                                    message: '',
                                            callback: function (value, validator) {
                                            var regexp = new RegExp(/^[a-zA-Z0-9 ]+$/);
                                                    if (!regexp.test(value) && value.length > 1) {
                                            return {
                                            valid: false,
                                                    message: '{{ trans("common.validation.alphanumericspac") }}'
                                            };
                                            }
                                            else if (value.length > 28) {
                                            return {
                                            valid: false,
                                                    message: '{{ trans("admin/insurance.validation.insurance_name_total") }}'
                                            };
                                            }
                                            return true;
                                            }
                                    }
                            }
                    },
					avatar_url:{
						message:'',
						validators:{
							file: {
								extension: 'jpeg,jpg,png,gif',
								message: attachment_valid_lang_err_msg
							},
							callback: {
								message: attachment_length_lang_err_msg,
								callback: function (value, validator,$field) {
									if($('[name="avatar_url"]').val() !="") {
										var size = parseFloat($('[name="avatar_url"]')[0].files[0].size/1024).toFixed(2);
										var get_image_size = Math.ceil(size);
										return (get_image_size>filesize_max_defined_length)?false : true;
									}
									return true;
								}
							}
						}
					},
					short_name: {
					message: '',
							validators: {
							notEmpty: {
							message: '{{ trans("admin/insurance.validation.short_name") }}'
							},
									callback: {
									message: '{{ trans("common.validation.shortname_regex") }}',
									callback: function (value, validator) {
										var get_val = validator.getFieldElements('short_name').val();
										 var pattern = new RegExp(/[~`!#$@%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/); 
										 
										if (get_val != '' && get_val.length < 3){
											return {
													valid: false,
													message: '{{ trans("practice/practicemaster/insurance.validation.short_name_min") }}'
												};
										} 
										else if(pattern.test(get_val)){
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
									var msg = addressValidation(value, "required");
											if (msg != true){
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
											if (msg != true){
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
											if (msg != true){
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
											if (msg != true){
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
											if (msg != true){
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
											if (msg != true){
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
					/*	insurancetype_id: {
					 message: '',
					 validators: {
					 notEmpty: {
					 message: '{{ trans("admin/insurance.validation.insurancetype_id") }}'
					 }
					 }
					 },
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
									var msg = lengthValidation(value, 'feeschedule');
											if (msg != true){
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
</script>
@endpush
