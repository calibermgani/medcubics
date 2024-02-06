<div class="col-md-12 space20"><!-- Inner Content for full width Starts -->
    <div id="is_provider"></div>
    <?php $current_page = Route::getFacadeRoot()->current()->uri(); ?>
	{!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
    <div class="box-body-block "><!--Background color for Inner Content Starts -->        
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->

            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="address-book"></i> <h3 class="box-title">Personal Information</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal">
                                                        
                    <div class="form-group">
                        {!! Form::label('ProviderDOB', 'DOB', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}

                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 ">
                          <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i>   {!! Form::text('provider_dob',null,['id'=>'date_of_birth','readonly','class'=>'dm-date form-control form-cursor']) !!}  
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                         {!! Form::label('Gender', 'Gender', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-2 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-8 @if($errors->first('gender')) error @endif">
                           {!! Form::radio('gender', 'Male',null,['id'=>'gender_m','class'=>'flat-red']) !!} Male &emsp; {!! Form::radio('gender', 'Female',null,['id'=>'gender_f','class'=>'flat-red']) !!} Female
                           {!! $errors->first('gender', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                         {!! Form::label('SSN', 'SSN', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('ssn')) error @endif">
                            {!! Form::text('ssn',null,['class'=>'dm-ssn form-control']) !!}
                            {!! $errors->first('ssn', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
							 <a id="document_add_modal_link_ssn" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/ssn')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/ssn')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                         {!! Form::label('Degree', 'Degree', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('provider_degrees_id')) error @endif">
                            {!! Form::select('provider_degrees_id', array(''=>'-- Select --')+(array)$provider_degree,  $degree_id,['class'=>'select2 form-control','id'=>'provider_degrees_id']) !!}
                            {!! $errors->first('provider_degrees_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Job Title', 'Job Title', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                            {!! Form::text('job_title',null,['id'=>'job_title','class'=>'form-control']) !!}
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
								{!! Form::text('address_1',null,['maxlength'=>'25','id'=>'address_1','class'=>'form-control js-address-check']) !!}
								{!! $errors->first('address_1', '<p> :message</p>')  !!}
							</div>
							<div class="col-sm-1 col-xs-2"></div>
						</div>

						<div class="form-group">
							{!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
							<div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('address_2')) error @endif">
								{!! Form::text('address_2',null,['maxlength'=>'25','id'=>'address_2','class'=>'form-control js-address2-tab']) !!}
								{!! $errors->first('address_2', '<p> :message</p>')  !!}
							</div>
							<div class="col-sm-1 col-xs-2"></div>
						</div>


						<div class="form-group">
							{!! Form::label('City / State', 'City / State', ['class'=>'col-md-4 col-sm-4 col-xs-12 control-label']) !!}
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
								{!! Form::text('city',null,['class'=>'form-control js-address-check','id'=>'city']) !!}
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
								{!! Form::text('zipcode4',null,['class'=>'dm-zip5 form-control js-address-check','id'=>'zipcode4']) !!}
							</div>
							<div class="col-md-1 col-sm-2 col-xs-2">
								<span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green"></i></span>
								<?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
								<?php echo $value;?> 
							</div>
						</div>
						<div class="bottom-space-20 hidden-sm hidden-xs">&emsp;</div>
						<div class="bottom-space-15 hidden-sm hidden-xs">&emsp;</div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->

            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="inbox"></i> <h3 class="box-title">Credentials</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal">

                    <div class="form-group">
                        {!! Form::label('Medicare PTAN', 'Medicare PTAN', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('medicareptan')) error @endif">
                            {!! Form::text('medicareptan',null,['maxlength'=>'10','id'=>'medicareptan','class'=>'form-control']) !!}
                            {!! $errors->first('medicareptan', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_medicare_ptan" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/medicare_ptan')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/medicare_ptan')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Medicaid ID', 'Medicaid ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('medicareptan')) error @endif">
                            {!! Form::text('medicaidid',null,['maxlength'=>'10','id'=>'medicareptan','class'=>'form-control']) !!}
                            {!! $errors->first('medicaidid', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_medicaid_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/medicaid_id')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/medicaid_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('BCBS_ID / Aetna_ID', 'BCBS_ID / Aetna_ID',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6 @if($errors->first('bcbsid')) error @endif @if($errors->first('aetnaid')) error @endif">
                            {!! Form::text('bcbsid',null,['class'=>'form-control','id'=>'bcbsid']) !!}
							<a id="document_add_modal_link_bcbs_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/bcbs_id')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/bcbs_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
                            {!! $errors->first('bcbsid', '<p> :message</p>')  !!}
                            {!! $errors->first('aetnaid', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4" >
                            {!! Form::text('aetnaid',null,['class'=>'form-control']) !!}
							<a id="document_add_modal_link_aetna_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/aetna_id')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/aetna_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('UHC_ID', 'UHC ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('uhcid')) error @endif">
                            {!! Form::text('uhcid',null,['class'=>'form-control']) !!}
							{!! $errors->first('uhcid', '<p> :message</p>')  !!}
                        </div>
						<div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_uhc_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/uhc_id')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/uhc_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>


                    <div class="form-group">
                         {!! Form::label('Other ID', 'Other ID / Ins Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('otherid',null,['class'=>'form-control']) !!}
                        </div>
                         <div class="col-lg-3 col-md-4 col-sm-3 col-xs-4">
                         {!! Form::select('otherid_ins', array(''=>'-- Select --')+(array)$insurances, null,['class'=>'select2 form-control','id'=>'otherid_ins']) !!}
                         </div>
						 <div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_other_id1" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/other_id1')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/other_id1')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                         {!! Form::label('Other ID', 'Other ID / Ins Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('otherid2',null,['class'=>'form-control']) !!}
                        </div>
                         <div class="col-lg-3 col-md-4 col-sm-3 col-xs-4">
                         {!! Form::select('otherid_ins2', array(''=>'-- Select --')+(array)$insurances, null,['class'=>'select2 form-control','id'=>'otherid_ins2']) !!}
                         </div>
						 <div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_other_id2" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/other_id2')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/other_id2')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                         {!! Form::label('Other ID', 'Other ID / Ins Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('otherid3',null,['class'=>'form-control']) !!}
                        </div>
                         <div class="col-lg-3 col-md-4 col-sm-3 col-xs-4">
                         {!! Form::select('otherid_ins3', array(''=>'-- Select --')+(array)$insurances, null,['class'=>'select2 form-control','id'=>'otherid_ins3']) !!}
                         </div>
						<div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_other_id3" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/other_id3')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/other_id3')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->
        <div class="col-lg-6 col-md-6 col-xs-12"><!--  Right side Content Starts -->

            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="medal"></i> <h3 class="box-title">Professional Identifications</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal">
                    <div class="form-group">
                        {!! Form::label('etin_type', 'ETIN Type', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-2 control-label']) !!}
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                            {!! Form::radio('etin_type', 'SSN',null,['class'=>'etin_type','id'=>'etin_ssn']) !!} SSN &emsp; {!! Form::radio('etin_type', 'TAX ID',null,['class'=>'etin_type','id'=>'etin_tax']) !!} TAX ID
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('etin_type_number', 'SSN or TAX ID',['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 import_etintype @if($errors->first('etin_type_number')) error @endif">

							@if(strpos($current_page, 'edit') !== false)
							  <span class="etin_type_number">
								@if($provider->etin_type=='SSN')
									{!! Form::text('etin_type_number',null,['class'=>'dm-ssn form-control input-sm']) !!}
								@else
									{!! Form::text('etin_type_number',null,['class'=>'dm-etin_type_no form-control input-sm']) !!}
								@endif
								</span>
							@else
								{!! Form::text('etin_type_number',null,['class'=>'dm-ssn form-control input-sm']) !!}
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
                                <a id="document_add_modal_link_ssn" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/ssn')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/ssn')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
							</span>
							
							<span id="document_add_modal_link_tax_id_part" @if($add_modal_doc_var=='ssn') style="display:none;" @endif>
                               <a id="document_add_modal_link_tax_id" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/tax_id')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/tax_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
							</span>
							
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
                        {!! Form::label('Taxonomy 1', 'Taxonomy 1', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
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
                        {!! Form::label('Taxonomy 2', 'Taxonomy 2', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('taxanomy_id2')) error @endif">
                           {!! Form::select('taxanomy_id2', array(''=>'-- Select --')+(array)$taxanomies2, $taxanomy_id2, ['id' => 'taxanomies2-list','class'=>'select2 form-control']) !!}
                            {!! $errors->first('taxanomy_id2', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>
                    
                    <div class="form-group">
                        {!! Form::label('State License', 'State License / State', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::text('statelicense',null,['class'=>'form-control','maxlength'=>15]) !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                            {!! Form::select('state_1', array(''=>'--')+(array)$states, null,['class'=>'select2 form-control','id'=>'state_1']) !!}
                        </div>
						<div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_state_license1" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/state_license1')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/state_license1')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('State License', 'State License / State', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::text('statelicense_2',null,['class'=>'form-control','maxlength'=>15]) !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                            {!! Form::select('state_2', array(''=>'--')+(array)$states, null,['class'=>'select2 form-control','id'=>'state_2']) !!}
                        </div>
						<div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_state_license2" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/state_license2')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/state_license2')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('State License', 'State License / State', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::text('specialitylicense',null,['class'=>'form-control','maxlength'=>15]) !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                            {!! Form::select('state_speciality', array(''=>'--')+(array)$states, null,['class'=>'select2 form-control','id'=>'state_speciality']) !!}
                        </div>
						<div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_state_license3" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/state_license3')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/state_license3')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('DEA Number', 'DEA Number / State', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('deanumber')) error @endif">
                            {!! Form::text('deanumber',null,['class'=>'form-control','maxlength'=>25]) !!}
                            {!! $errors->first('deanumber', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                            {!! Form::select('state_dea', array(''=>'--')+(array)$states, null,['class'=>'form-control select2','id'=>'state_dea']) !!}
                        </div>
						<div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_dea_number" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/dea_number')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/dea_number')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('UPIN', 'UPIN / State', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::text('upin',null,['class'=>'form-control','maxlength'=>25]) !!}
                            {!! $errors->first('upin', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                            {!! Form::select('state_upin', array(''=>'--')+(array)$states, null,['class'=>'form-control select2','id'=>'state_upin']) !!}
                        </div>
						<div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_upin_number" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/upin_number')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/upin_number')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('TAT', 'TAT', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('tat')) error @endif">
                            {!! Form::text('tat',null,['class'=>'form-control','maxlength'=>25]) !!}
                            {!! $errors->first('tat', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('MammographyCert#', 'Mammography Cert#', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                            {!! Form::text('mammography',null,['class'=>'form-control','maxlength'=>25]) !!}
                        </div>
						<div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_mammography_cert" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/mammography_cert')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/mammography_cert')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						</div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('CarePlanOversight#', 'Care Plan Oversight#', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                            {!! Form::text('careplan',null,['class'=>'form-control','maxlength'=>25]) !!}
                        </div>
						<div class="col-sm-1 col-xs-2">
							<a id="document_add_modal_link_care_plan" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/care_plan')}}" @else data-url="{{url('api/adddocumentmodal/provider/0/care_plan')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" style="position:relative;top: 15px;"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal">

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
                           {!! Form::select('stmt_add', ['Pay to Address' => 'Pay to Address','Mailing Address' => 'Mailing Address','Primary Location' => 'Primary Location',],null,['class'=>'select2 form-control']) !!}
                            {!! $errors->first('stmt_add', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group bottom-space-10">
                      {!! Form::label('Hospice_Employed', 'Hospice Employed', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                           {!! Form::radio('hospice_emp', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('hospice_emp', 'No',true,['class'=>'flat-red']) !!} No
                        </div>
                    </div>

                    <div class="form-group bottom-space-10">
                      {!! Form::label('sign_file', 'Signature on File', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                           {!! Form::radio('sign_file', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('sign_file', 'No',true,['class'=>'flat-red']) !!} No
                        </div>
                    </div>

                    <div class="form-group bottom-space-10">
                       {!! Form::label('status', 'Status',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                           {!! Form::radio('status', 'Active',true,['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red']) !!} Inactive
                        </div>
                    </div>

                    <div class="form-group">
						{!! Form::label('Digital_Signature', 'Digital Signature', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                            @if (Request::segment(2) == 'create')
								{!! Form::file('digital_sign',['accept'=>'image/png, image/gif, image/jpeg']) !!}
							@else
								{!! Form::file('digital_sign',['accept'=>'image/png, image/gif, image/jpeg']) !!}
								<a data-toggle="modal" href="#form-content"><span class="add-on med-upload"><i class="icon-camera"></i></span></a>
							@endif
							@if(strpos($current_page, 'edit') !== false)
								@if($provider->digital_sign_name!='')
									<a data-toggle="modal" href="#image-content">{!! HTML::image('img/preview.png') !!}  </a>
								@endif
							@endif
                        </div>
                    </div>

                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Right side Content Ends -->

		<div class="box-footer">
			<div class="col-md-12  col-sm-12">
				{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
				@if(strpos($current_page, 'edit') !== false)
					<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure would you like to delete provider?" href="{{ url('provider/'.$provider->id.'/delete') }}">Delete</a></center>
					<a href="{{ url('provider/'.$provider->id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
				@endif

				@if(strpos($current_page, 'edit') == false)
					<a href="{{ url('provider') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
				@endif
			</div>
		</div><!-- /.box-footer -->
    </div>
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
                    {!! HTML::image('media/sign/'.$provider->digital_sign_name.'.'.$provider->digital_sign_ext) !!}
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">USPS Address Information</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-list" id="modal_show_success_message" @if($address_flag['general']['is_address_match'] != 'Yes') class="hide" @endif>
                <li class="nav-header">Address : <span id="modal_address">{{$address_flag['general']['address1']}}</span></li>
                <li class="nav-header">City : <span id="modal_city">{{$address_flag['general']['city']}}</span></li>
                <li class="nav-header">State : <span id="modal_state">{{$address_flag['general']['state']}}</span></li>
                <li class="nav-header">Zipcode : <span id="modal_zip5">{{$address_flag['general']['zip5']}}-{{$address_flag['general']['zip4']}}</span></li>
            </ul>

            <p id="modal_show_error_message" @if($address_flag['general']['is_address_match'] != 'No') class="hide" @endif>{{$address_flag['general']['error_message']}}</p>
        </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
@include ('practice/layouts/npi_form_modal')

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {

        $('#js-bootstrap-validator')
       /*        .find('[name="provider_types_id"]')
                .change(function (e) {
                    $('#js-bootstrap-validator')
                            .data('bootstrapValidator')
                            .updateStatus('provider_types_id', 'NOT_VALIDATED')
                            .validateField('provider_types_id');
                })
                .end() */
			.bootstrapValidator({
				excluded: ':disabled',
				message: 'This value is not valid',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					last_name: {
						 message: '',
						validators: {
							notEmpty: {
								message: 'This field is required and can\'t be empty'
							}
						}
					},
					first_name: {
						 message: '',
						validators: {
							notEmpty: {
								message: 'This field is required and can\'t be empty'
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
							regexp:{
								regexp: /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
								message: 'Enter valid email!'
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
						validators: {
							notEmpty: {
								message: 'This field is required and can\'t be empty'
							}
						}
					},
					ssn:{
						validators: {
								regexp: {
									regexp: /^(?!000|666)(?:[0-6][0-9]{2}|7(?:[0-6][0-9]|7[0-2]))-(?!00)[0-9]{2}-(?!0000)[0-9]{4}$/,
									message: 'This field can contain only 9 digits'
								}
							}                                
					},
					/*
				   address_1: {
						 message: '',
						validators: {
							notEmpty: {
								message: 'This field is required and can\'t be empty'
							}
						}
					},
					city: {
						 message: '',
						validators: {
							notEmpty: {
								message: 'This field is required and can\'t be empty'
							}
						}
					},
					state: {
						 message: '',
						validators: {
							notEmpty: {
								message: 'This field is required and can\'t be empty'
							}
						}
					}, */
					etin_type_number: {
						validators: {
							callback: {
								message: 'This field can contain only 9 digits',
								callback: function(value, validator, $field) {
									var entity_type = $('#js-bootstrap-validator').find('[name="etin_type"]:checked').val();
									var pos = value.lastIndexOf('_');
									if(entity_type=='TAX ID'){
									   if(pos=='-1') { return true; } else { return false; }
									}   
									if(entity_type=='SSN'){
										if(pos=='-1') { return true; } else { return false; }
									}
								}
							}
						}
					},
					zipcode5: {
						 message: 'This field is invalid',
						validators: {
							notEmpty: {
								message: 'This field is required and can\'t be empty'
							},
							regexp: {
								regexp: /^[0-9]{5}$/,
								message: 'Enter valid zip code!'
							}
						}
					},
					phone: {
						message: '',
						validators: {
							callback: {
								message: 'Enter phone!',
								callback: function (value, validator) {
										if (value.search("\\(\[0-9]{3}\\\)\\s[0-9]{3}\-\[0-9]{4}") == -1)
											return false;
										return true;
								}
							}
						}
					},
					fax: {
						message: '',
						validators: {
							callback: {
								message: 'Enter fax!',
								callback: function (value, validator) {
										if (value.search("\\(\[0-9]{3}\\\)\\s[0-9]{3}\-\[0-9]{4}") == -1)
											return false;
										return true;
								}
							}
						}
					},
					npi:{
						validators:{
							notEmpty:{
								message: 'Provider NPI field is required and can\'t be empty'
								},
								regexp:{
								regexp: /^[0-9]{10}$/,
								message: 'This field can contain only 10 digits'
							}
						}
					}
				}
			});

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

		$('[name="phone"]').on('change',function(){
				$('#js-bootstrap-validator')
						.data('bootstrapValidator')
						.updateStatus('phone', 'NOT_VALIDATED')
						.validateField('phone');
		});

		$('[name="fax"]').on('change',function(){
				$('#js-bootstrap-validator')
						.data('bootstrapValidator')
						.updateStatus('fax', 'NOT_VALIDATED')
						.validateField('fax');
		});

		$('[name="npi"]').on('change',function(){
				$('#js-bootstrap-validator')
						.data('bootstrapValidator')
						.updateStatus('npi', 'NOT_VALIDATED')
						.validateField('npi');
		});
    });
</script>
@endpush