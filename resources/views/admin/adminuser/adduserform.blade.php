<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.admin_users") }}' />
<span style="display:none;">
    {{ $segment = Request::segment(3) }}
</span>

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >
    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Login Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->
        <div class="box-body  form-horizontal margin-l-10">
            <div class="form-group">
                {!! Form::label('practice_user_type', 'User Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                    <input type="radio" class="js_user_pra_type" id="admin_user" name="practice_user_type" value="customer" checked="checked" {{ (@$adminusers->practice_user_type =='customer') ?  'checked':'' }} /> {!! Form::label('admin_user', 'Medcubics',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                    <input type="radio" class="js_user_pra_type" id="customer_practice_admin" name="practice_user_type" value="practice_admin" {{ (@$adminusers->practice_user_type =='practice_admin') ?  'checked':'' }} /> {!! Form::label('customer_practice_admin', 'Practice Admin',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                    <input type="radio" class="js_user_pra_type" id="customer_practice_user"  name="practice_user_type" value="practice_user" {{ (@$adminusers->practice_user_type =='practice_user' ) ?  'checked':'' }} /> {!! Form::label('customer_practice_user', 'Practice User',['class'=>'med-darkgray font600 form-cursor']) !!}
					<input type="radio" class="js_user_pra_type" id="customer_provider"  name="practice_user_type" value="provider" {{ (@$adminusers->practice_user_type =='provider' ) ?  'checked':'' }} /> {!! Form::label('customer_provider', 'Provider',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>
            </div>
            <input type ='hidden' class ='js_user_type' value=<?php echo @$adminusers->practice_user_type; ?>  >
            <input type ='hidden' value="<?php echo @$adminusers->id; ?>" name='user_id' >
            <div class="form-group js_customer">
                {!! Form::label('js-customer', 'Select Customer', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 ">
                    {!! Form::select('customer_id',array('' => '-- Select --')+(array)@$customers,@$adminusers->customer_id, ['class' => 'form-control select2 customer_practice_name','id'=>'js-customer']) !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group js_user_access">
                {!! Form::label('useraccess', 'User Accsess',  ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 ">
                    <input type="radio" class="js_user_access_type" id="js_useraccess_web" name="useraccess" value="web" checked="checked" {{ (@$adminusers->useraccess =='web') ?  'checked':'' }} /> {!! Form::label('js_useraccess_web', 'Web',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                    <input type="radio" class="js_user_access_type" id="js_useraccess_app"  name="useraccess" value="app"  {{ (@$adminusers->useraccess =='app') ?  'checked':'' }} /> {!! Form::label('js_useraccess_app', 'App',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <?php
				$customer_practice_ids = !empty($adminusers->admin_practice_id) ? explode(',', $adminusers->admin_practice_id) : [];
            ?>
            <div class="form-group js_customer_practice" >
                {!! Form::label('customer_practice_list', 'Select practice', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 ">
                    {!! Form::select('admin_practice_id',$customer_practices,@$customer_practice_ids, ['multiple'=>'multiple','name'=>'admin_practice_id[]', 'class' => 'form-control select2 js_admin_practice_id','id'=>'customer_practice_list']) !!}
                    {!! $errors->first('admin_practice_id', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
			
			<div class="form-group js_provider" >
                {!! Form::label('Provider', 'Provider', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
				<div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
					{!! Form::select('provider_access_id', array('' => '-- Select --')+(array)@$provider, (Input::old('provider_access_id') !="")? Input::old('provider_access_id'):@$adminusers->provider_access_id ,['class'=>'form-control select2','id'=>'selected_pra_pro_list']) !!}
					{!! $errors->first('provider_access_id', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1 col-xs-2"></div>
            </div>
			
            <div class="js_prac_access_app">
                <div class="form-group">
                    {!! Form::label('Select App', 'Select App', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 ">
                        {!! Form::select('app_name',["WEB" => "Patient Intake","CHARGECAPTURE" => 'Charge Capture'],  (Input::old('app_name') !="")? Input::old('app_name'):@$adminusers->app_name ,['class'=>'form-control select2', 'id' => 'js_selet_app']) !!}
                        {!! $errors->first('app_name', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>
                <div class="form-group">
                    {!! Form::label('Practice', 'Practice', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
                        {!! Form::select('practice_access_id', array('' => '-- Select --')+(array)$customer_practices_list, (Input::old('practice_access_id') !="")? Input::old('practice_access_id'):@$adminusers->practice_access_id ,['class'=>'form-control select2 practice_useraccess_name','id'=>'selected_practice_list']) !!}
                        {!! $errors->first('practice_access_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>
                <?php
					if (@$adminusers->app_name == "CHARGECAPTURE") {
						$charge_class = '';
						$web_class = 'style = display:none';
					} else {
						$web_class = '';
						$charge_class = 'style = display:none;';
					}
                ?>
                <div class="form-group js_app_data" id="WEB" {{$web_class}}>
                    {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 ">
                        {!! Form::select('facility_access_id', array('' => '-- Select --')+(array)@$facility,  (Input::old('facility_access_id') !="")? Input::old('facility_access_id'):@$adminusers->facility_access_id ,['class'=>'form-control select2','id'=>'selected_pra_fac_list']) !!}
                        {!! $errors->first('facility_access_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>
                <div class="form-group js_app_data" id="CHARGECAPTURE" {{$charge_class}}>
                    {!! Form::label('Provider', 'Provider', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
                        {!! Form::select('provider_access_id', array('' => '-- Select --')+(array)@$provider, (Input::old('provider_access_id') !="")? Input::old('provider_access_id'):@$adminusers->provider_access_id ,['class'=>'form-control select2','id'=>'selected_pra_pro_list']) !!}
                        {!! $errors->first('provider_access_id', '<p> :message</p>') !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>
            </div>
            <div class="form-group medcubics_role">
                {!! Form::label('Role Type', 'Role Type', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 ">
                    {!! Form::select('med_role_id', array('' => '-- Select --') + (array)$adminrolls,null,['class'=>'form-control select2 js_med_role']) !!}
					{!! $errors->first('med_role_id', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group practice_role">
                {!! Form::label('Role Type', 'Role Type', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 ">
                    {!! Form::select('pra_role_id', array('' => '-- Select --') + (array)$practicerolls,null,['class'=>'form-control select2 js_pra_role'] ) !!}
					{!! $errors->first('pra_role_id', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Name', 'User Name', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('name')) error @endif">
                    {!! Form::text('name',null,['class'=>'form-control js-letters-caps-format']) !!}
                    {!! $errors->first('name', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Email', 'Email', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('email')) error @endif">
                    {!! Form::text('email',null,['class'=>'form-control js-email-letters-lower-format']) !!}
                    {!! $errors->first('email', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Password', 'Password', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('password')) error @endif">
                    @if($segment == 'create')
                    {!! Form::input('password', 'password', null,['class'=>'form-control', 'maxlength'=>20]) !!}
                    @else	
                    {!! Form::input('password', 'password', null,['class'=>'form-control', 'maxlength'=>20]) !!}
                    @endif
                    {!! $errors->first('password', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Confirm Password', 'Confirm Password', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('confirmpassword')) error @endif">
                    @if($segment == 'create')
                    {!! Form::input('password', 'confirmpassword', null,['class'=>'form-control', 'maxlength'=>20]) !!}
                    @else	
                    {!! Form::input('password', 'confirmpassword', null,['class'=>'form-control', 'maxlength'=>20]) !!}
                    @endif
                    {!! $errors->first('confirmpassword', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Status', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}      
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
                    {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'ad-active']) !!} {!! Form::label('ad-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'ad-inactive']) !!} {!! Form::label('ad-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>  
            <?php 
				if(@$adminusers->login_attempt >= '0'){
					$checked = array('null','true');
				}else{
					$checked = array('true','null');
				}
            ?>
            <div class="form-group">
                {!! Form::label('Logged Setting', 'Logged Setting', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}      
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
                    {!! Form::radio('logged_setting', 'yes',$checked[0],['class'=>'','id'=>'log-yes']) !!} {!! Form::label('log-yes', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('logged_setting', 'no',$checked[1],['class'=>'','id'=>'log-no']) !!} {!! Form::label('log-no', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div> 


			<div class="form-group js_customer_practice" >
                {!! Form::label('ip_group_list', 'Ip Group', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 ">
                    {!! Form::select('ip_group',$ip_group,@$ip_user_group, ['multiple'=>'multiple','name'=>'ip_group[]', 'class' => 'form-control select2 js_admin_practice_id','id'=>'ip_group_list']) !!}
                    {!! $errors->first('ip_group', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!--/.col (left) -->
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >
    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">User Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->
        <div class="box-body  form-horizontal margin-l-10">
            <div class="form-group">
                {!! Form::label('Last Name', 'Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
                    {!! Form::text('lastname',null,['class'=>'form-control js-letters-caps-format']) !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
                    {!! Form::text('firstname',null,['class'=>'form-control js-letters-caps-format']) !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Short Name', 'Short Name', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
                    {!! Form::text('short_name',null,['class'=>'form-control js_all_caps_format dm-shortname', 'id'=>'short_name', 'maxlength'=>'3']) !!}
                    {!! $errors->first('short_name', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('dob', 'DOB', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 ">                             
                    <i class="fa fa-calendar-o form-icon"></i>  {!! Form::text('dob',null,['id'=>'dateofbirth','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor dm-date']) !!} 
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Gender', 'Gender', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-8">
                    {!! Form::radio('gender', 'Male',true,['id'=>'gender_m','class'=>'']) !!} {!! Form::label('gender_m', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp;
                    {!! Form::radio('gender', 'Female',null,['id'=>'gender_f','class'=>'']) !!} {!! Form::label('gender_f', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp;
                    {!! Form::radio('gender', 'Others',null,['id'=>'gender_o','class'=>'']) !!} {!! Form::label('gender_o', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Language', 'Language', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('language_id')) error @endif">
                    {!! Form::select('language_id', array('' => '-- Select --') + (array)$language,  $language_id,['class'=>'form-control select2']) !!}
                    {!! $errors->first('language_id', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Department', 'Department', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
                    {!! Form::text('department',null,['class'=>'form-control js-letters-caps-format']) !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!--/.col (left) -->
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
    <div class="box no-shadow">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >
            <div class="box-body  form-horizontal">
                <div class="form-group">
                    {!! Form::label('Designation', 'Designation', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
                        {!! Form::select('designation', [
                        '' => '-- Select --',
                        'CEO'   => 'CEO',
                        'Director'   => 'Director',
                        'Associate'   => 'Associate',
                        'Administrator'   => 'Administrator',
                        'Office Manager'   => 'Office Manager',
                        'Billing Manager'  => 'Billing Manager'],null,['class'=>'form-control select2']
                        ) !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>
                <div class="form-group">
                    {!! Form::label('Ethnicity', 'Ethnicity', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10">
                        {!! Form::select('ethnicity_id', array('' => '-- Select --') + (array)$ethnicity,  $ethnicity_id,['class'=>'form-control select2']) !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>
                {!! Form::hidden('user_type','Practice',['class'=>'form-control']) !!}
                <div class=" js-address-class" id="js-address-general-address">
                    {!! Form::hidden('general_address_type','adminuser',['class'=>'js-address-type']) !!}
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
                        {!! Form::label('AddressLine1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('addressline1')) error @endif">
                            {!! Form::text('addressline1',null,['class'=>'form-control js-address-check','maxlength'=>'50','id'=>'addressline1']) !!}
                            {!! $errors->first('addressline1', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('AddressLine2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('addressline2')) error @endif">
                            {!! Form::text('addressline2',null,['class'=>'form-control js-address2-tab', 'maxlength'=>'50', 'id'=>'addressline2']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4  col-sm-3 col-xs-5">  
                            {!! Form::text('city',null,['class'=>' form-control js-address-check','id'=>'city','maxlength'=>'50']) !!}
                        </div>
                        {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!}
                        <div class="col-lg-2 col-md-2  col-sm-2 col-xs-3"> 
                            {!! Form::text('state',null,['class'=>' form-control js-address-check js-state-tab dm-state','maxlength'=>'2','id'=>'state']) !!} 
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('zip Code', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4  col-sm-3 col-xs-5 @if($errors->first('zipcode5')||($errors->first('zipcode4'))) error @endif"> 
                            {!! Form::text('zipcode5',null,['class'=>' form-control js-address-check dm-zip5', 'id'=>'zipcode5', 'maxlength'=>'5']) !!}
                        </div>
                        <div class="col-lg-3 col-md-3  col-sm-3 col-xs-5"> 
                            {!! Form::text('zipcode4',null,['class'=>' form-control js-address-check dm-zip4','id'=>'zipcode4','maxlength'=>'4']) !!} 
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <span class="add-on js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                            <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?> 
                            <?php echo $value; ?> 
                        </div>
                    </div>
                </div>
                
            </div><!-- /.box-body -->
        </div><!--/.col (left) -->
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >
            <div class="box-body  form-horizontal">
                <div class="form-group">
                    {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!}
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('phone')) error @endif">
                        {!! Form::text('phone',null,['class'=>'form-control dm-phone']) !!}
                        {!! $errors->first('phone', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-md-1 col-sm-2"></div>
                </div> 
                <div class="form-group">
                    {!! Form::label('Fax', 'Fax', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('fax')) error @endif">
                        {!! Form::text('fax',null,['class'=>'form-control dm-phone']) !!}
                        {!! $errors->first('fax', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-md-1 col-sm-2"></div>
                </div>
                <div class="form-group ">
                    {!! Form::label('upload_type', 'Profile Picture', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 

                    <div class="fileupload" data-provides="fileupload" style="display: flex;">
                        @if(@$adminusers->avatar_name != "")
                        <div class="fileupload-new thumbnail ">
                            <?php
								$filename = @$adminusers->avatar_name . '.' . @$adminusers->avatar_ext;
								$img_details = [];
								$img_details['module_name']='user';
								$img_details['file_name']=$filename;
								$img_details['practice_name']="admin";
								
								$img_details['class']='img-border';
								$img_details['style']='margin-left:0px !important;display: block;';
								$img_details['alt']='user-image';
								$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
							?>
                            {!! $image_tag !!}        
                        </div>
                        @endif

                        <div class="col-lg-3 col-md-6 col-sm-6" @if(@$image_tag != '') style="bottom: 3px;position:relative;left:155px;width:100px; margin-top: 15%;" @endif>
                             <span class="fileContainer" style="padding:1px 10px;"> 
                                {!! Form::file('image',['class'=>'default','id'=>'image','accept'=>'image/png, image/gif, image/jpeg','style'=>'height: 30px; width: 20px;']) !!}Upload  </span>
                            <span class="error" >{!! $errors->first('image',  '<p> :message</p>')  !!} </span> 
                        </div>
                        <div class="fileupload-preview thumbnail"></div>

                    </div>
                    @if($errors->first('image'))
                    <div class="form-group">
                        {!! Form::label('', '', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('image')) error @endif">
                            {!! $errors->first('image',  '<p> :message</p>')  !!} 
                        </div>
                    </div>
                    @endif

                </div>  

                <div class="form-group hidden">
                    {!! Form::label('Facebook', 'Facebook', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('facebook_ac')) error @endif">
                        {!! Form::text('facebook_ac',null,['class'=>'form-control']) !!}
                        {!! $errors->first('facebook_ac', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-md-1 col-sm-2"></div>
                </div>
                <div class="form-group hidden">
                    {!! Form::label('Twitter', 'Twitter', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('twitter')) error @endif">
                        {!! Form::text('twitter',null,['class'=>'form-control']) !!}
                        {!! $errors->first('twitter', '<p> :message</p>') !!}
                    </div>
                    <div class="col-md-1 col-sm-2"></div>
                </div>
                <div class="form-group hidden">
                    {!! Form::label('Linkedin', 'Linkedin', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('linkedin')) error @endif">
                        {!! Form::text('linkedin',null,['class'=>'form-control']) !!}
                        {!! $errors->first('linkedin', '<p> :message</p>') !!}
                    </div>
                    <div class="col-md-1 col-sm-2"></div>
                </div>
                <div class="form-group hidden">
                    {!! Form::label('Google+', 'Google+', ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('googleplus')) error @endif">
                        {!! Form::text('googleplus',null,['class'=>'form-control']) !!}
                        {!! $errors->first('googleplus', '<p> :message</p>') !!}
                    </div>
                    <div class="col-md-1 col-sm-2"></div>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
        <div class="box-footer">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
                <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                @if(strpos($currnet_page, 'edit') !== false)
					@if($checkpermission->check_adminurl_permission('admin/adminuser/delete/{id}') == 1)
						<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete?" href="{{ url('admin/adminuser/delete/'.$adminusers->id) }}">Delete</a>
					@endif
					<a href="javascript:void(0)" data-url="{{ url('admin/adminuser/'.$adminusers->id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @else
					<a href="javascript:void(0)" data-url="{{ url('admin/adminuser')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @endif
            </div>
        </div><!-- /.box-footer -->
    </div>
</div><!-- General information Ends -->

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
	
    $(document).on('ifToggled click change', '.js_user_pra_type:checked', function (event) {
		$('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('[name="customer_id"]'));
		$('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('[name="admin_practice_id"]'));
	});

    $(document).on('ifToggled click change', '.js_user_access_type:checked', function (event) {
        $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('[name="practice_access_id"]'));
        $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('[name="facility_access_id"]'));
        $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('[name="provider_access_id"]'));
    });
	
    $(document).on("change", "#js_selet_app", function () {
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
		
        $('[name="password"]').on('keyup', function () {
            $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'confirmpassword');
        });
		
        $('[name="confirmpassword"]').on('keyup', function () {
            $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'password');
        });       

        $(".js_user_pra_type").trigger("change");

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
					practice_user_type: {
						message: '{{ trans("admin/adminuser.validation.practice_user_type") }}',
						validators: {
							callback: {
								message: '{{ trans("admin/adminuser.validation.practice_user_type") }}',
								callback: function (value, validator, $field) {
									$('form#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'customer_id');
									$('form#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'admin_practice_id');
									return true;
								},
								notEmpty: {
									message: '{{ trans("admin/adminuser.validation.practice_user_type") }}',
								}
							}
						}
					},
					customer_id: {
						message: '{{ trans("admin/adminuser.validation.customer") }}',
						selector: '#js-customer',
						validators: {
							callback: {
								message: '{{ trans("admin/adminuser.validation.customer") }}',
								callback: function (value, validator, $field) {
									radio = $('input[name=practice_user_type]:checked').val();
									admin_cust_val = $('.customer_practice_name').val();
									if (value == '' && (radio == 'practice_admin' || radio == 'practice_user' || radio == 'provider') && (admin_cust_val == '' || admin_cust_val == 'undefined')) {
										return {
											valid: false,
											message: '{{ trans("admin/adminuser.validation.customer") }}'
										};
									}else {
										return true;
									}
								}
							}
						}
					},
					admin_practice_id: {
						message: '{{ trans("admin/adminuser.validation.admin_practice") }}',
						selector: '#customer_practice_list',
						validators: {
							callback: {
								message: '{{ trans("admin/adminuser.validation.admin_practice") }}',
								callback: function (value, validator, $field) {
									radio = $('input[name=practice_user_type]:checked').val();
									admin_prac_val = $('.js_admin_practice_id').val();
									radio_useraccess = $('input[name=useraccess]:checked').val();
									if (value == null && radio == 'practice_admin' && (admin_prac_val == '' || admin_prac_val == 'undefined')) {
										return {
											valid: false,
											message: '{{ trans("admin/adminuser.validation.admin_practice") }}'
										};
									} else if (value == null && radio == 'practice_user' && radio_useraccess != 'app' && (admin_prac_val == '' || admin_prac_val == 'undefined')) {
										return {
											valid: false,
											message: '{{ trans("admin/adminuser.validation.admin_practice") }}'
										};
									} else {
										return true;
									}
								}
							}
						}
					},
					practice_access_id: {
						message: '',
						validators: {
							callback: {
								message: '{{ trans("admin/customer.validation.app_user_practice") }}',
								callback: function (value, validator, $field) {
									var useraccess = $('input[name="useraccess"]:checked').val();
									return (useraccess == "app" && value == '') ? false : true;
								}
							}
						}
					},
					facility_access_id: {
						message: '',
						validators: {
							callback: {
								message: '{{ trans("admin/customer.validation.app_user_facility") }}',
								callback: function (value, validator, $field) {
									var app_data = $('#js_selet_app').val();
									var useraccess = $('input[name="useraccess"]:checked').val();
									return (useraccess == "app" && value == '' && app_data == "WEB") ? false : true;
								}
							}
						}
					},
					provider_access_id: {
						message: '',
						validators: {
							callback: {
								message: 'Select provider',
								callback: function (value, validator, $field) {
									var app_data = $('#js_selet_app').val();
									var useraccess = $('input[name="useraccess"]:checked').val();
									return (useraccess == "app" && value == '' && app_data == "CHARGECAPTURE") ? false : true;
								}
							}
						}
					},
//                        role_id: {
//                            message: '',
//                            validators: {
//                                notEmpty: {
//                                    message: '{{ trans("admin/adminuser.validation.roletype") }}'
//                                }
//                            }
//                        },
					name: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("admin/adminuser.validation.name") }}'
							},
							regexp: {
								regexp: /^[A-Za-z ]+$/,
								message: '{{ trans("common.validation.alphaspace") }}'
							},
							stringLength: {
								message: '{{ trans("admin/adminuser.validation.length") }}',
								max: function (value, validator, $field) {
									return 50 - (value.match(/^[a-zA-Z\s]{0,50}$/) || []).length;
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
									var focus = $('[name="password"]').is(':focus');
									if (pwd == '' && value_length == "0") {
										return {
											valid: false,
											message: '{{ trans("admin/adminuser.validation.password") }}'
										};
									}
									else if (focus == true && c_pwd != '' && pwd != c_pwd) {
										return {
											valid: false,
											message: '{{ trans("admin/adminuser.validation.passwordidentical") }}'
										};
									}
									password = password_name(value);
									if (password != true) {
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
									if (pwd != "") {
										if (c_pwd == '' && pwd != "") {
											return {
												valid: false,
												message: '{{ trans("admin/adminuser.validation.confirmpassword") }}'
											};
										}
										else if ($('[name="confirmpassword"]').is(':focus') == true && pwd != '' && pwd != c_pwd) {
											return {
												valid: false,
												message: '{{ trans("admin/adminuser.validation.passwordidentical") }}'
											};
										}
										return true;
									}
									return true;
								}
							}
						}
					},
					email: {
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
									'_token':$('input[name="_token"]').val()
									},
								type: 'POST'
							},
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
									var dob = $('#js-bootstrap-validator1').find('[name="dob"]').val();
									var current_date = new Date(dob);
									var d = new Date();
									return (dob != '' && d.getTime() < current_date.getTime()) ? false : true;
								}
							}
						}
					},
					image: {
						validators: {
							file: {
								extension: 'jpeg,jpg,png',
								message: attachment_valid_lang_err_msg
							},
							callback: {
								message: attachment_length_lang_err_msg,
								callback: function (value, validator, $field) {
									if ($('[name="image"]').val() != "") {
										var size = parseFloat($('[name="image"]')[0].files[0].size / 1024).toFixed(2);
										var get_image_size = Math.ceil(size);
										return (get_image_size > filesize_max_defined_length) ? false : true;
									}
									return true;
								}
							}
						}
					},
					language_id: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("admin/adminuser.validation.language") }}'
							}
						}
					},
					lastname: {
						message: '',
						validators: {
							regexp: {
								regexp: /^[A-Za-z ]+$/,
								message: '{{ trans("common.validation.alphaspace") }}'
							},
							stringLength: {
								message: '{{ trans("admin/adminuser.validation.length") }}',
								max: function (value, validator, $field) {
									return 50 - (value.match(/^[a-zA-Z\s]{0,50}$/) || []).length;
								}
							}
						}
					},
					firstname: {
						message: '',
						validators: {
							regexp: {
								regexp: /^[A-Za-z]+$/,
								message: '{{ trans("common.validation.alphaspace") }}'
							},
							stringLength: {
								message: '{{ trans("admin/adminuser.validation.length") }}',
								max: function (value, validator, $field) {
									return 50 - (value.match(/^[a-zA-Z\s]{0,50}$/) || []).length;
								}
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
									if (get_val != '' && get_val.length < 3)
										return false;
									return true;
								}
							}
						}
					},
					department: {
						message: '',
						validators: {
							regexp: {
								regexp: /^[A-Za-z0-9]+$/,
								message: '{{ trans("common.validation.alphanumeric") }}'
							},
							stringLength: {
								message: '{{ trans("admin/adminuser.validation.length") }}',
								max: function (value, validator, $field) {
									return 50 - (value.match(/^[0-9a-zA-Z\s]{0,50}$/) || []).length;
								}
							}
						}
					},
					addressline1: {
						message: '',
						validators: {
							// notEmpty: {
							//     message: '{{ trans("common.validation.address1_required") }}'
							// },
							regexp: {
								regexp: /^[a-zA-Z0-9 ]{0,50}$/,
								message: '{{ trans("common.validation.alphanumericspac") }}'
							}
						}
					},
					addressline2: {
						message: '',
						validators: {
							regexp: {
								regexp: /^[a-zA-Z0-9 ]{0,50}$/,
								message: '{{ trans("common.validation.alphanumericspac") }}'
							}
						}
					},
					city: {
						message: '',
						validators: {
							// notEmpty: {
							//     message: '{{ trans("common.validation.city_required") }}'
							// },
							regexp: {
								regexp: /^[A-Za-z ]+$/,
								message: '{{ trans("common.validation.alphaspace") }}'
							}
						}
					},
					state: {
						message: '',
						validators: {
							// notEmpty: {
							//     message: '{{ trans("common.validation.state_required") }}'
							// },
							regexp: {
								regexp: /^[A-Za-z]{2}$/,
								message: '{{ trans("admin/adminuser.validation.state_limit") }}'
							}
						}
					},
					zipcode5: {
						message: '',
						validators: {
							// notEmpty: {
							//     message: '{{ trans("common.validation.zipcode5_required") }}'
							// },
							regexp: {
								regexp: /^[0-9]{5}$/,
								message: '{{ trans("admin/adminuser.validation.admin_zip_regex") }}'
							}
						}
					},
					zipcode4: {
						message: '',
						trigger: 'change keyup',
						validators: {
							message: '',
							callback: {
								message: '',
								callback: function (value, validator) {
									var msg = zip4Validation(value);
									if (msg != true) {
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
					image: {
						validators: {
							file: {
								extension: 'png,jpg,jpeg',
								type: 'image/png,image/jpg,image/jpeg',
								maxSize: 1024 * 1024, // 1 MB
								message: 'The selected file is not valid, it should be (png, jpg) and 1 MB at maximum.'
							}
						}
					},
					phone: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var cell_phone_msg = '{{ trans("common.validation.cell_phone_limit") }}';
									var response = phoneValidation(value, cell_phone_msg);
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
					facebook_ac: {
						message: '',
						validators: {
							uri: {
								message: '{{ trans("admin/adminuser.validation.facebook") }}'
							}
						}
					},
					twitter: {
						message: '',
						validators: {
							uri: {
								message: '{{ trans("admin/adminuser.validation.twitter") }}'
							}
						}
					},
					linkedin: {
						message: '',
						validators: {
							uri: {
								message: '{{ trans("admin/adminuser.validation.linkedin") }}'
							}
						}
					},
					googleplus: {
						message: '',
						validators: {
							uri: {
								message: '{{ trans("admin/adminuser.validation.googleplus") }}'
							}
						}
					}
				}
			});
    });
</script>
@endpush