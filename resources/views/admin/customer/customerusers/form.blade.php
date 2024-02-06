<input type="hidden" name="customer_id" value="{{ Request::segment(3) }}" />
<input type="hidden" name="name" value="" />
<input type="hidden" name="user_type" value="practice" />
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.customer_users") }}' />
<input type ='hidden' value="<?php echo @$customerusers->id; ?>" name='user_id' >
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20">
        <div class="box box-info no-shadow">
            <div class="box-block-header margin-b-10">
                <i class="livicon" data-name="user"></i> <h3 class="box-title"> User Details</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->

            <div class="box-body  form-horizontal margin-l-10">
                <div class="form-group">
                    {!! Form::label('Lastname', 'Last name', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label ']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('lastname')) error @endif">
                        {!! Form::text('lastname',null,['class'=>'form-control js-letters-caps-format', 'maxlength'=>50]) !!}
                        {!! $errors->first('lastname', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>  	

                <div class="form-group">
                    {!! Form::label('Firstname', 'First name', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('firstname')) error @endif">
                        {!! Form::text('firstname',null,['class'=>'form-control js-letters-caps-format', 'maxlength'=>50]) !!}
                        {!! $errors->first('firstname', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div> 
				<div class="form-group">
                    {!! Form::label('ShortName', 'Short name', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('short_name')) error @endif">
                        {!! Form::text('short_name',null,['class'=>'form-control js_all_caps_format dm-shortname','id'=>'short_name','maxlength'=>'3']) !!}
                        {!! $errors->first('short_name', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('Dob', 'Date of birth',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                          
                    <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12"> 
                        <i class="fa fa-calendar-o form-icon"></i>
						{!! Form::text('dob',null,['id'=>'dateofbirth','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor dm-date']) !!}                           
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">
                    {!! Form::label('gender', 'Gender',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('gender')) error @endif">
                        {!! Form::radio('gender', 'Male',true,['class'=>'','id'=>'cu-male']) !!} {!! Form::label('cu-male', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('gender', 'Female',null,['class'=>'','id'=>'cu-female']) !!} {!! Form::label('cu-female', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!}
                        {!! $errors->first('gender', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">
                    {!! Form::label('Designation', 'Designation', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('designation')) error @endif">
                        {!! Form::text('designation',null,['class'=>'form-control']) !!}
                        {!! $errors->first('designation', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('Department', 'Department', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('department')) error @endif">
                        {!! Form::text('department',null,['class'=>'form-control']) !!}
                        {!! $errors->first('department', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">
                    {!! Form::label('Language', 'Language', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('language_id')) error @endif">
                        {!! Form::select('language_id', array('' => '-- Select --') + (array)$language,  $language_id,['class'=>'form-control select2']) !!}
                        {!! $errors->first('language_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">
                    {!! Form::label('Ethnicity', 'Ethnicity', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('ethnicity_id')) error @endif">
                        {!! Form::select('ethnicity_id', array('' => '-- Select --') + (array)$ethnicity,  $ethnicity_id,['class'=>'form-control select2']) !!}
                        {!! $errors->first('ethnicity_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>
				
                <div class="form-group">
                    {!! Form::label('Email', 'Email', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('email')) error @endif">
                        {!! Form::text('email',null,['class'=>'form-control js-email-letters-lower-format']) !!}
                        {!! $errors->first('email', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-md-1 col-sm-2"></div>
                </div>  
				
                <div class="form-group">
                    {!! Form::label('Password', 'Password', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('password')) error @endif">
                        {!! Form::input('password', 'password', null,['class'=>'form-control', 'maxlength'=>20]) !!}
                        {!! $errors->first('password', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>

                <div class="form-group">
                    {!! Form::label('Confirm Password', 'Confirm Password', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 @if($errors->first('confirmpassword')) error @endif">
                        {!! Form::password('confirmpassword',['class'=>'form-control','maxlength'=>20]) !!}
                        {!! $errors->first('confirmpassword', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>
				
                <div class="form-group margin-t-15">
                    {!! Form::label('upload_type', 'Upload', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-4 control-label']) !!} 
					<div class="fileupload fileupload-new" data-provides="fileupload">
                        @if(@$user->avatar_name != "")
                            <div class="fileupload-new thumbnail">
                                <?php
									$filename = @$user->avatar_name . '.' . @$user->avatar_ext;
									$img_details = [];
									$img_details['module_name']='user';
									$img_details['file_name']=$filename;
									$img_details['practice_name']="admin";
									
									$img_details['class']='img-border';
									$img_details['alt']='user-image';
									$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
								?>
                                {!! $image_tag !!}        
                            </div>
                        @endif
                        
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 fileContainer">
                             {!! Form::file('filefield',['class'=>'form-control default', 'accept'=>'image/png, image/gif, image/jpeg']) !!} Upload
                        </div>
                        @if($errors->first('filefield'))
							<div class="error" >
								{!! $errors->first('filefield', '<p > :message</p>')  !!}
							</div>
                        @endif
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 fileupload-preview thumbnail"></div>
                </div>
            </div>  
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!--/.col (left) -->

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20" >
    <div class="box box-info no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="address-book"></i> <h3 class="box-title"> Contact details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body  form-horizontal margin-l-10">
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
                    {!! Form::label('AddressLine1', 'Address Line 1', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 @if($errors->first('addressline1')) error @endif">
                        {!! Form::text('addressline1',null,['class'=>'form-control js-address-check','maxlength'=>'50','id'=>'addressline1']) !!}
                        {!! $errors->first('addressline1', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                <div class="form-group">
                    {!! Form::label('AddressLine2', 'Address Line 2', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 @if($errors->first('addressline2')) error @endif">
                        {!! Form::text('addressline2',null,['class'=>'form-control js-address2-tab','maxlength'=>'50','id'=>'addressline2']) !!}
                        {!! $errors->first('addressline2', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>
				
                <div class="form-group">
                    {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-7 @if($errors->first('city')) error @endif">  
                        {!! Form::text('city',null,['maxlength'=>'50','class'=>'form-control js-address-check','id'=>'city']) !!}
                        {!! $errors->first('city', '<p> :message</p>')  !!}
                    </div>
                    {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-2 col-md-2 col-sm-1 col-xs-4 @if($errors->first('state')) error @endif"> 
                        {!! Form::text('state',null,['class'=>'form-control js-address-check js-state-tab dm-state','maxlength'=>'2','id'=>'state']) !!}
						{!! $errors->first('state', '<p> :message</p>')  !!}
                    </div>
                </div> 

                <div class="form-group">
                    {!! Form::label('zip Code', 'Zip Code', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                    <div class="col-lg-3 col-md-4 col-sm-3 col-xs-7 @if($errors->first('zipcode5')) error @endif">  
                        {!! Form::text('zipcode5',null,['class'=>' form-control js-address-check dm-zip5','id'=>'zipcode5','maxlength'=>'5']) !!}
						 {!! $errors->first('zipcode5', '<p> :message</p>')  !!} 
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 @if($errors->first('zipcode4')) error @endif"> 
                        {!! Form::text('zipcode4',null,['class'=>' form-control js-address-check dm-zip4','id'=>'zipcode4','maxlength'=>'4']) !!} 
						{!! $errors->first('zipcode4', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-md-1 col-sm-2">
                        <span class="add-on js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
                        <?php echo $value; ?>                                 
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-3 col-md-7 col-sm-6 col-xs-12 @if($errors->first('phone')) error @endif">
                    {!! Form::text('phone',null,['class'=>'form-control dm-phone']) !!}
                    {!! $errors->first('phone', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-2"></div>
            </div>  

            <div class="form-group">
                {!! Form::label('Fax', 'Fax', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-3 col-md-7 col-sm-6 col-xs-12 @if($errors->first('fax')) error @endif">
                    {!! Form::text('fax',null,['class'=>'form-control dm-phone']) !!}
                    {!! $errors->first('fax', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-2"></div>
            </div>  

           <!-- <div class="form-group">
                {!! Form::label('facebook_ac', 'Facebook', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 @if($errors->first('facebook_ac')) error @endif">
                    {!! Form::text('facebook_ac',null,['class'=>'form-control']) !!}
                    {!! $errors->first('facebook_ac', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-2"></div>
            </div>  
            <div class="form-group">
                {!! Form::label('Twitter', 'Twitter', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 @if($errors->first('twitter')) error @endif">
                    {!! Form::text('twitter',null,['class'=>'form-control']) !!}
                    {!! $errors->first('twitter', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-2"></div>
            </div>  
            <div class="form-group">
                {!! Form::label('Linkedin', 'Linkedin', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 @if($errors->first('linkedin')) error @endif">
                    {!! Form::text('linkedin',null,['class'=>'form-control']) !!}
                    {!! $errors->first('linkedin', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-2"></div>
            </div> 
            <div class="form-group">
                {!! Form::label('Google+', 'Google+', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 @if($errors->first('googleplus')) error @endif">
                    {!! Form::text('googleplus',null,['class'=>'form-control']) !!}
                    {!! $errors->first('googleplus', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-2"></div>
            </div>  --> 
			 <div class="form-group margin-t-15">
                {!! Form::label('Status', 'Status', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-4 control-label']) !!}      
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-8">
                    {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'cu-active']) !!} {!! Form::label('cu-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'cu-inactive']) !!} {!! Form::label('cu-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                       
            </div> 
            <div class="form-group">
                {!! Form::label('useraccess', 'User Access',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 ">
                    {!! Form::radio('useraccess', 'web',true,['data-id' => 'js_access_web','class' => 'js_useraccess','id'=>'cu_web']) !!}  {!! Form::label('cu_web', 'Web',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                    {!! Form::radio('useraccess', 'app',null,['data-id' => 'js_access_app','class' => 'js_useraccess','id'=>'cu_app']) !!} {!! Form::label('cu_app', 'App',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                                                          
                <div class="col-sm-1"></div>
            </div>
            <div class="@if(@$user->useraccess =='app')show @else hide @endif js_access" id="js_access_app">
                 <div class="form-group">
                    {!! Form::label('Select App', 'Select App', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 ">
                        {!! Form::select('app_name',["WEB" => "Patient Intake","CHARGECAPTURE" => 'Charge Capture'],  (Input::old('app_name') !="")? Input::old('app_name'):@$user->app_name ,['class'=>'form-control select2', 'id' => 'js-selet-app']) !!}
                        {!! $errors->first('app_name', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <div class="form-group">
                    {!! Form::label('Practice', 'Practice', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12">
                        {!! Form::select('practice_access_id', array('' => '-- Select --') + (array)$customer_practices_list, (Input::old('practice_access_id') !="")? Input::old('practice_access_id'):@$user->practice_access_id ,['class'=>'form-control select2 practice_useraccess_name']) !!}
                        {!! $errors->first('practice_access_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <?php 
                    if(@$user->app_name == "CHARGECAPTURE"){
                        $charge_class= '';
                        $web_class = 'style = display:none';
                    } else {
                        $web_class= '';
                        $charge_class = 'style = display:none;';
                    }
                ?>
                <div class="form-group js-app-data" id="WEB" {{$web_class}}>
                    {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 ">
                        {!! Form::select('facility_access_id', array('' => '-- Select --')+(array)$facility,  (Input::old('facility_access_id') !="")? Input::old('facility_access_id'):@$user->facility_access_id ,['class'=>'form-control select2 choose_facility','id'=>'selected_list']) !!}
                        {!! $errors->first('facility_access_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <?php
                if(isset($provider) && !empty($provider))
                    $providers = array('' => '-- Select --')+(array)$provider;
                else
                    $providers = array('' => '-- Select --');
                ?>
                 <div class="form-group js-app-data"  id="CHARGECAPTURE" {{$charge_class}}>
                    {!! Form::label('Provider', 'Provider', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 ">
                        {!! Form::select('provider_access_id', $providers, (Input::old('provider_access_id') !="")? Input::old('provider_access_id'):@$user->provider_access_id ,['class'=>'form-control select2 choose_facility','id'=>'selected_provider_list']) !!}
                        {!! $errors->first('provider_access_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>
            </div>	
			<div id="js_access_web" class="@if(@$user->useraccess =='app') hide @endif  js_access">
                <div class="form-group">
                    {!! Form::label('practice', 'Choose user type',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <input type="radio" class="" id="practice_user_a" name="practice_user_type" value="practice_admin" {{ (@$customerusers->practice_user_type =='practice_admin') ?  'checked':'' }} /> {!! Form::label('practice_user_a', 'Practice Admin',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                        <input type="radio" class="" id="practice_user_u" name="practice_user_type" value="practice_user" {{ (@$customerusers->practice_user_type =='practice_user' || @$customerusers->practice_user_type =='') ?  'checked':'' }} /> {!! Form::label('practice_user_u', 'Practice User',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>
                </div>	

                <?php                     
                    $customer_practice_ids = !empty($customerusers->admin_practice_id)?explode(',',$customerusers->admin_practice_id):[];
                   
                ?> 
                        				
                <div class="form-group js-practice-user @if(!empty($customer_practice_ids)) show @endif">
                    {!! Form::label('admin_practice_id', 'Select practice', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 @if($errors->first('admin_practice_id')) error @endif">
                        {!! Form::select('admin_practice_id', $customer_practices,$customer_practice_ids, ['multiple'=>'multiple','name'=>'admin_practice_id[]', 'class' => 'form-control select2 js_admin_practice_id']) !!}
                        {!! $errors->first('admin_practice_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-md-1 col-sm-2"></div>
                </div>
               <?php $practice_ids = array(); $show_div = ''; ?>
                @if(!empty($practices) && isset($practices))
                <?php $practice_ids = array(); ?>
                @foreach($practices as $practice)
                <?php  $practice_ids[] = $practice->id; ?>
                @endforeach
                @else
                  <?php $show_div = 'hide'; ?>
                @endif
                <div class="form-group js-permission-user {{$show_div}}">
                    {!! Form::label('admin_practice_permission', 'Select Permission', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 ">
                        {!! Form::select('admin_practice_permission', $customer_practices,$practice_ids, ['multiple'=>'multiple','name'=>'admin_practice_permission[]', 'class' => 'form-control select2 js_admin_practice_id','id'=>'admin_practice_permission']) !!}
                        {!! $errors->first('admin_practice_permission', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-md-1 col-sm-2"></div>
                </div>
              
            </div>			
        </div>                                                          
    </div><!-- /.box-body -->
</div><!-- /.box -->

<div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
    {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
    @if(strpos($currnet_page, 'edit') !== false && $checkpermission->check_adminurl_permission('admin/customer/{cust_id}/customerusers/delete/{customerusers_id}') == 1)
		<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure you want to delete?" href="{{ url('admin/customer/'.$customer->id.'/customerusers/delete/'.$customerusers->id) }}">Delete</a>
    @endif 

    @if(strpos($currnet_page, 'edit') !== false)
		<a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$customer->id.'/customerusers/'.$customerusers->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
    @endif 

    @if(strpos($currnet_page, 'edit') == false)
		<a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$customer->id.'/customerusers')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
    @endif 
</div>
    
<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- /.modal  Ends-->

@push('view.scripts')                           
<script type="text/javascript">
    $(document).on('keydown', '[name="password"],[name="confirmpassword"]', function (e) {
        if (e.keyCode == 32)
            return false;		 
    }); 
	$('[name="password"]').on('keyup',function() {
		$('#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'confirmpassword');
	});
	$('[name="confirmpassword"]').on('keyup',function() {
		$('#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'password');
	});
	$(document).on('ifToggled click','#useraccess:checked', function (event) {
		$('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('[name="practice_access_id"]'));
		$('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('[name="facility_access_id"]'));
        $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('[name="provider_access_id"]'));
	});

    $(document).on("change", "#js-selet-app", function(){
        $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('[name="practice_access_id"]'));
        $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('[name="provider_access_id"]'));
    });

    $(document).ready(function () {
		var id = $('').attr('dateofbirth');
        $("#dateofbirth").datepicker({
            yearRange: '1900:+0',
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true,
            maxDate: '0',
            onClose: function (selectedDate) {
                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="dob"]'));
            }
        });
        
        $("#dob_new").datepicker({
            yearRange: '1900:+0',
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true,
            maxDate: '0',
            onClose: function (selectedDate) {
                $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('input[name="dob"]'));
            }
        });

        $('[name="phone"]').on('change', function () {
            $('#js-bootstrap-validator1')
                .data('bootstrapValidator')
                .updateStatus('phone', 'NOT_VALIDATED')
                .validateField('phone');
        });

        $('[name="fax"]').on('change', function () {
            $('#js-bootstrap-validator1')
                .data('bootstrapValidator')
                .updateStatus('fax', 'NOT_VALIDATED')
                .validateField('fax');
        });

        $("#practice_user_a").on('ifToggled change', function () {
			$('#admin_practice_id').select2();
            $('form#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('#admin_practice_id'));
        });

        $('#js-bootstrap-validator1')
            .bootstrapValidator({
                message: 'This value is not valid',
                excluded: ':disabled',
                feedbackIcons: {
                    valid: '',
                    invalid: '',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    firstname: {
                        message: '',
                        validators: {
                            notEmpty: {
                                message: '{{ trans("admin/user.validation.firstname") }}'
                            },
                            regexp: {
                                regexp: /^[a-zA-Z ]{0,50}$/,
                                message: '{{ trans("common.validation.alphaspace") }}'
                            }
                        }
                    },
                    lastname: {
                        message: '',
                        validators: {
                            notEmpty: {
                                message: '{{ trans("admin/user.validation.lastname") }}'
                            },
                            regexp: {
                                regexp: /^[a-zA-Z ]{0,50}$/,
                                message: '{{ trans("common.validation.alphaspace") }}'
                            }
                        }
                    },
					short_name: {
						message: '',
						trigger: 'change keyup',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/provider.validation.short_name") }}'
                            },
                            remote: {
                                    message: 'Short name already exist',
                                    url: api_site_url+'/admin/adminuser/userShortNameValidate',
                                    data: {
                                        'short_name':$('input[name="short_name"]'),
                                        'user_id':$('input[name="user_id"]').val(),
                                        '_token':$('input[name="_token"]').val()},
                                    type: 'POST'
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
                    language_id: {
                        message: '',
                        validators: {
                            notEmpty: {
                                message: '{{ trans("admin/user.validation.language") }}'
                            }
                        }
                    },
                    ethnicity_id: {
                        message: '',
                        validators: {
                            
                        }
                    },
                    designation: {
                        message: '',
                        validators: {
                            regexp: {
                                regexp: /^[A-Za-z ]+$/,
                                message: '{{ trans("common.validation.alphaspace") }}'
                            }
                        }
                    },
                    department: {
                        message: '',
                        validators: {
                            regexp: {
                                regexp: /^[A-Za-z ]+$/,
                                message: '{{ trans("common.validation.alphaspace") }}'
                            }
                        }
                    },
                    practice_user_type: {
                        message: '{{ trans("admin/user.validation.practice_user_type") }}',
                        validators: {
                            callback: {
                                message: '{{ trans("admin/user.validation.practice_user_type") }}',
                                callback: function (value, validator, $field) {
                                    $('form#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'admin_practice_id');
                                    return true;
                                },
                                notEmpty: {
                                    message: '{{ trans("admin/user.validation.practice_user_type") }}'
                                }
                            }
                        }
                    },
                    admin_practice_id: {
                        message: '{{ trans("admin/user.validation.admin_practice") }}',
                        selector: '#admin_practice_id',
                        validators: {
                            callback: {
                                message: '{{ trans("admin/user.validation.admin_practice") }}',
                                callback: function (value, validator, $field) {
                                    radio = $('input[name=practice_user_type]:checked').val();
                                    admin_val = $('.js_admin_practice_id').val();
                                    if (value == null && radio == 'practice_admin' && (admin_val == '' || admin_val == 'undefined')) {
                                        return false;
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        }
                    },
                    addressline1: {
                        message: '',
                        validators: {
                            regexp: {
                                    regexp: /^[a-zA-Z0-9 ]{0,50}$/,
                                    message: '{{ trans("common.validation.alphanumericspac") }}'
                                }
                            // callback: {
							// 	message: '',
							// 	callback: function (value, validator) {
							// 		var msg = addressValidation(value,"required");
							// 		if(msg != true){
							// 			return {
							// 				valid: false,
							// 				message: msg
							// 			};
							// 		}
							// 		return true;
							// 	}
							// }
                        }
                    },
                    addressline2: {
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
                            regexp: {
                                    regexp: /^[A-Za-z ]+$/,
                                    message: '{{ trans("common.validation.alphaspace") }}'
                                }
                            // callback: {
							// 	message: '',
							// 	callback: function (value, validator) {
							// 		var msg = cityValidation(value,"required");
							// 		if(msg != true){
							// 			return {
							// 				valid: false,
							// 				message: msg
							// 			};
							// 		}
							// 		return true;
							// 	}
							// }
                        }
                    },
                    state:{
						message:'',
						validators:{
                            regexp: {
                                    regexp: /^[A-Za-z]{2}$/,
                                    message: '{{ trans("admin/adminuser.validation.state_limit") }}'
                                }
							// callback: {
							// 	message: '',
							// 	callback: function (value, validator) {
							// 		var msg = stateValidation(value,"required");
							// 		if(msg != true){
							// 			return {
							// 				valid: false,
							// 				message: msg
							// 			};
							// 		}
							// 		return true;
							// 	}
							// }
						}
					},
                    zipcode5: {
                        message: '',
                        validators: {
                            regexp: {
                                    regexp: /^[0-9]{5}$/,
                                    message: '{{ trans("admin/adminuser.validation.admin_zip_regex") }}'
                                }
                            // callback: {
							// 	message: '',
							// 	callback: function (value, validator) {
							// 		var msg = zip5Validation(value,"required");
							// 		if(msg != true){
							// 			return {
							// 				valid: false,
							// 				message: msg
							// 			};
							// 		}
							// 		return true;
							// 	}
							// }
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
					/*useraccess: {
						 message: '',
                        validators: {
						 notEmpty: {
                                message: '{{ trans("common.validation.zipcode5_required") }}'
                            },
							callback: {
                                message: '',
                                callback: function (value, validator) {
									var value = $('input[name=useraccess]:checked').val();
									if (value == 'app') {
										$('#js-bootstrap-validator1').bootstrapValidator('enableFieldValidators', 'facility_access_id', true);
                                    }
                                    else {
                                        $('#js-bootstrap-validator1').bootstrapValidator('enableFieldValidators', 'facility_access_id', false);
                                    }
                                    $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', "facility_access_id");
                                  return true;
                                }
                            }
						}
					},*/
					practice_access_id: {
                        message: '',
                        validators: {
                           callback: {
                                message: '{{ trans("admin/customer.validation.app_user_practice") }}',
                                callback: function (value, validator) {
									var useraccess = $('input[name="useraccess"]:checked').val();
									return (useraccess=="app" && value == '') ? false :true;
                                }
                            }
                        }
                    },
					facility_access_id: {
                        message: '',
                        validators: {
                           callback: {
                                message: '{{ trans("admin/customer.validation.app_user_facility") }}',
                                callback: function (value, validator) {
                                    var app_data = $('#js-selet-app').val();
                                    console.log("app data"+app_data);
									var useraccess = $('input[name="useraccess"]:checked').val();
									return (useraccess=="app" && value == '' && app_data == "WEB") ? false :true;
                                }
                            }
                        }
                    },
                    provider_access_id: {
                        message: '',
                        validators: {
                           callback: {
                                message: 'Select provider',
                                callback: function (value, validator) {
                                    var app_data = $('#js-selet-app').val();
                                    console.log("app data"+app_data);
                                    var useraccess = $('input[name="useraccess"]:checked').val();
                                    return (useraccess=="app" && value == '' && app_data == "CHARGECAPTURE") ? false :true;
                                }
                            }
                        }
                    }, 
                     phone: {
                        message: '',
                        validators: {
                            callback: {
								message: '',
								callback: function (value, validator,$field) {
									var phone_msg = '{{ trans("common.validation.cell_phone_limit") }}';
									var response = phoneValidation(value,phone_msg);
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
                    email:{
						message: '',
						validators: {
							notEmpty: {
                                message: '{{ trans("common.validation.email") }}'
                            },
                            remote: {
                                    message: 'Email ID already exist',
                                    url: api_site_url+'/admin/adminuser/userEmailValidate',
                                    data: {
                                        'email':$('input[name="email"]'),
                                        'user_id':$('input[name="user_id"]').val(),
                                        '_token':$('input[name="_token"]').val()},
                                    type: 'POST'
                            },
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
					password: {
						validators: {
							callback: {
                                message: '',
                                callback: function (value, validator) {
                                    var value_length = $(".js-delete-confirm").length;
                                    var pwd = value;
                                    var c_pwd = validator.getFieldElements('confirmpassword').val();
                                    if (pwd == '' && value_length == "0") {
                                        return {
                                            valid: false,
                                            message: '{{ trans("admin/adminuser.validation.password") }}'
                                        };
                                    }
                                    else if (c_pwd != '' && pwd != c_pwd) {
                                        return {
                                            valid: false,
                                            message: '{{ trans("admin/adminuser.validation.passwordidentical") }}'
                                        };
                                    }
									password = password_name(value);
									if(password !=true) {
										return {
											valid: false, 
											message: password
										};
									}
                                    return true;
                                }
                            }
						}
					},						
					confirmpassword: {
						validators: {
							callback: {
                                message: '',
                                callback: function (value, validator) {
                                    var pwd = validator.getFieldElements('password').val();
                                    var c_pwd = value;
									if (pwd != '') {
										if (c_pwd == ''){
											var msg = '{{ trans("admin/adminuser.validation.confirmpassword") }}';
										}
										else if(pwd != c_pwd)
											var msg = '{{ trans("admin/adminuser.validation.passwordidentical") }}';
										else
											return true;
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
					dob: {
                        message: '',
                        validators: {
                            date: {
                                format: 'MM/DD/YYYY',
                                message: '{{ trans("common.validation.date_format") }}'
                            },
                            callback: {
                                message: '{{ trans("admin/adminuser.validation.valid_dob_format") }}',
                                callback: function (value, validator, $field) {
                                    var dob = $('#js-bootstrap-validator').find('[name="dob"]').val();
                                    var current_date = new Date(dob);
                                    var d = new Date();
                                    return (dob != '' && d.getTime() < current_date.getTime()) ? false : true;
                                }
                            }
                        }
                    },
                   /*dob: {
                        message: '',
                        validators: {
                            date: {
                                format: 'MM/DD/YYYY',
                                message: '{{ trans("common.validation.date_format") }}'
                            },
                            callback: {
                                message: '{{ trans("common.validation.date_format") }}',
                                callback: function (value, validator, $field) {
                                    var dob = $('#js-bootstrap-validator').find('[name="dob"]').val();
                                    var current_date = new Date(dob);
                                    var d = new Date();
                                    return (dob != '' && d.getTime() < current_date.getTime()) ? false : true;
                                }
                            }
                        }
                    },*/
					filefield: {
						validators: {
							file: {
								extension: 'jpeg,jpg,png',
								message: attachment_valid_lang_err_msg
							},
							callback: {
								message: attachment_length_lang_err_msg,
								callback: function (value, validator,$field) {
									if($('[name="filefield"]').val() !="") {
										var size = parseFloat($('[name="filefield"]')[0].files[0].size/1024).toFixed(2);
										var get_image_size = Math.ceil(size);
										return (get_image_size>filesize_max_defined_length)?false : true;
									}
									return true;
								}
							}
						}	
					},
                    facebook_ac: {
                        message: '',
                        validators: {
                            regexp: {
                                regexp: /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/,
                                message: '{{ trans("common.validation.url") }}'
                            }
                        }
                    },
                    twitter: {
                        message: '',
                        validators: {
                            regexp: {
                                regexp: /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/,
                                message: '{{ trans("common.validation.url") }}'
                            }
                        }
                    },
                    linkedin: {
                        message: '',
                        validators: {
                            regexp: {
                                regexp: /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/,
                                message: '{{ trans("common.validation.url") }}'
                            }
                        }
                    },
                    googleplus: {
                        message: '',
                        validators: {
                            regexp: {
                                regexp: /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/,
                                message: '{{ trans("common.validation.url") }}'
                            }
                        }
                    }
                    }
            });
    });
</script>
@endpush