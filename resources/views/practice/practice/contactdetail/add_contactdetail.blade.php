@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.practice')}} font14"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Contact Details <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
            <li> <a href="javascript:void(0)" data-url="{{ url('contactdetail') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/practice/practice-tabs')  
@stop
@section('practice')

{!! Form::model($contactdetails, array('method' => 'PATCH','id'=>'js-bootstrap-validator','url' =>'contactdetail/'.$contactdetails->id,'name'=>'medcubicsform','files'=>true,'class'=>'medcubicsform')) !!}

    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.practice_contact") }}' />
		
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->
		<div class="box  no-shadow margin-b-10"><!-- Box General Contacts Starts -->
			<div class="box-block-header with-border">
				<i class="livicon" data-name="users"></i> <h3 class="box-title">General Contacts</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" tabindex ="-1" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body form-horizontal margin-l-10"><!-- Box body Starts -->
				<div class="form-group">
					{!! Form::label('PracticeCEO', 'Practice CEO',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('practiceceo')) error @endif">
						{!! Form::text('practiceceo',null,['class'=>'form-control js-letters-caps-format', 'autocomplete'=>'nope','maxlength'=>'51']) !!}
						{!! $errors->first('practiceceo', '<p> :message</p>')  !!}
					</div>
					<div class="col-sm-1 col-md-1 col-xs-2"></div>
				</div>

				<div class="form-group">
					{!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('mobileceo')) error @endif">
						{!! Form::text('mobileceo',null,['class'=>'form-control dm-phone','id'=>'mobileceo', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('mobileceo', '<p> :message</p>')  !!}
					</div>
					<div class="col-sm-1 col-md-1 col-xs-2"></div>
				</div>                                


				<div class="form-group">
					{!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('phoneceo')) error @endif @if($errors->first('phoneceo_ext')) error @endif">  
						{!! Form::text('phoneceo',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('phoneceo', '<p> :message</p>')  !!}
						{!! $errors->first('phoneceo_ext', '<p> :message</p>')  !!}
					</div>
					{!! Form::label('St', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"> 
						{!! Form::text('phoneceo_ext',null,['class'=>'form-control dm-phone-ext', 'autocomplete'=>'nope']) !!}
					</div>
				</div>

				<div class="form-group">
					{!! Form::label('Fax', 'Fax', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('faxceo')) error @endif">
						{!! Form::text('faxceo',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('faxceo', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div> 

				<div class="form-group">
					{!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('emailceo')) error @endif">
						{!! Form::text('emailceo',null,['class'=>'form-control js-email-letters-lower-format', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('emailceo', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div>


			</div><!-- /.box-body ends -->
		</div><!-- Box General Contacts Ends -->

		<div class="box no-shadow margin-b-10"><!-- Box Practice Manager Starts -->
			<div class="box-block-header with-border">
				<i class="livicon" data-name="user-flag"></i> <h3 class="box-title">Practice Manager</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" tabindex ="-1" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body form-horizontal margin-l-10"><!-- Box Body Starts -->
				<div class="form-group">
					{!! Form::label('PracticeManager', 'Practice Manager',   ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('practicemanager')) error @endif">
						{!! Form::text('practicemanager',null,['class'=>'form-control js-letters-caps-format', 'autocomplete'=>'nope','maxlength'=>'51']) !!}
						{!! $errors->first('practicemanager', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div>

				<div class="form-group">
					{!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('mobilemanager')) error @endif">
						{!! Form::text('mobilemanager',null,['class'=>'form-control dm-phone','id'=>'mobilemanager', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('mobilemanager', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div>                                          

				<div class="form-group">
					{!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('phonemanager')) error @endif @if($errors->first('phonemanager_ext')) error @endif">  
						{!! Form::text('phonemanager',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('phonemanager', '<p> :message</p>')  !!}
						{!! $errors->first('phonemanager_ext', '<p> :message</p>')  !!}
					</div>
					{!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"> 
						{!! Form::text('phonemanager_ext',null,['class'=>'form-control dm-phone-ext', 'autocomplete'=>'nope']) !!}
					</div>
				</div>                                                                               

				<div class="form-group">
					{!! Form::label('Fax', 'Fax', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-4 col-md-4 col-sm-7 col-xs-10 @if($errors->first('faxmanager')) error @endif">
						{!! Form::text('faxmanager',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('faxmanager', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div> 

				<div class="form-group">
					{!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('emailmanager')) error @endif">
						{!! Form::text('emailmanager',null,['class'=>'form-control']) !!}
						{!! $errors->first('emailmanager', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div>

			</div><!-- /.box-body -->
		</div><!-- Box Practice Manager Ends -->
	</div><!--  Left side Content Ends -->        

	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-20"><!--  Right side Content Starts -->
		<div class="box no-shadow margin-b-10"><!-- Box Company Information Starts -->
			<div class="box-block-header with-border">
				<i class="livicon" data-name="tag"></i> <h3 class="box-title">Company Details</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" tabindex ="-1" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body form-horizontal js-address-class margin-l-10" id="js-address-primary-address"><!-- Box Body Starts -->
				{!! Form::hidden('ba_address_type','practice',['class'=>'js-address-type']) !!}
				{!! Form::hidden('ba_address_type_id',$practice->id,['class'=>'js-address-type-id']) !!}
				{!! Form::hidden('ba_address_type_category','contact_details',['class'=>'js-address-type-category']) !!}
				{!! Form::hidden('ba_address1',$address_flag['billing_service']['address1'],['class'=>'js-address-address1']) !!}
				{!! Form::hidden('ba_city',$address_flag['billing_service']['city'],['class'=>'js-address-city']) !!}
				{!! Form::hidden('ba_state',$address_flag['billing_service']['state'],['class'=>'js-address-state']) !!}
				{!! Form::hidden('ba_zip5',$address_flag['billing_service']['zip5'],['class'=>'js-address-zip5']) !!}
				{!! Form::hidden('ba_zip4',$address_flag['billing_service']['zip4'],['class'=>'js-address-zip4']) !!}
				{!! Form::hidden('ba_is_address_match',$address_flag['billing_service']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
				{!! Form::hidden('ba_error_message',$address_flag['billing_service']['error_message'],['class'=>'js-address-error-message']) !!}

				<div class="form-group">
					{!! Form::label('CompanyName', 'Company Name',   ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('companyname')) error @endif">
						{!! Form::text('companyname',null,['class'=>'form-control', 'autocomplete'=>'nope','maxlength'=>'51']) !!}
						{!! $errors->first('companyname', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div>          

				<div class="form-group">
					{!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
						{!! Form::text('address1',null,['id'=>'address1','class'=>'form-control dm-address js-address-check', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('address1', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div> 

				<div class="form-group">
					{!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
						{!! Form::text('address2',null,['id'=>'address2','class'=>'form-control dm-address js-address2-tab', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('address2', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div> 

				<div class="form-group">
					{!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('city')) error @endif @if($errors->first('state')) error @endif">  
						{!! Form::text('city',null,['class'=>'form-control js-letters-caps-format js-address-check','id'=>'city', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('city', '<p> :message</p>')  !!}
						{!! $errors->first('state', '<p> :message</p>')  !!}
					</div>
					{!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">  
						{!! Form::text('state',null,['class'=>'form-control js-all-caps-letter-format dm-state js-address-check js-state-tab','id'=>'state', 'autocomplete'=>'nope']) !!}
					</div>
				</div>   

				<div class="form-group">
					{!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('zipcode')) error @endif">  
						{!! Form::text('zipcode5',null,['class'=>'form-control js-address-check dm-zip5','id'=>'zipcode5', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('zipcode5', '<p> :message</p>')  !!}
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4"> 
						{!! Form::text('zipcode4',null,['class'=>'form-control js-address-check dm-zip4','id'=>'zipcode4', 'autocomplete'=>'nope']) !!}
					</div>
					<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2"> 
						<span class="add-on js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
						<span class="js-address-success @if($address_flag['billing_service']['is_address_match'] != 'Yes') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
						<span class="js-address-error @if($address_flag['billing_service']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
						<?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['billing_service']['is_address_match']); ?>   
						<?php echo $value; ?>  

					</div>
				</div>                                                                                                  

			</div><!-- /.box-body ends -->
		</div><!-- Box Company Information Ends -->

		<div class="box no-shadow margin-b-10"><!-- Box Contact Person Starts -->
			<div class="box-block-header with-border">
				<i class="livicon" data-name="user"></i> <h3 class="box-title">Contact Person</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" tabindex ="-1" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body form-horizontal margin-l-10"><!-- Box Body Starts -->
				<div class="form-group">
					{!! Form::label('ContactPerson', 'Contact Person',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('contactperson')) error @endif">
						{!! Form::text('contactperson',null,['class'=>'form-control', 'autocomplete'=>'nope','maxlength'=>'51']) !!}
						{!! $errors->first('contactperson', '<p> :message</p>')  !!}
					</div>
					<div class="col-sm-1 col-md-1 col-xs-2"></div>
				</div>


				<div class="form-group">
					{!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('phone')) error @endif @if($errors->first('phone_ext')) error @endif">  
						{!! Form::text('phone',null,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('phone', '<p> :message</p>')  !!}
						{!! $errors->first('phone_ext', '<p> :message</p>')  !!}
					</div>
					{!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">  
						{!! Form::text('phone_ext',null,['class'=>'form-control dm-phone-ext', 'autocomplete'=>'nope']) !!}
					</div>
				</div>                                                                               

				<div class="form-group">
					{!! Form::label('Fax', 'Fax', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-10 @if($errors->first('fax')) error @endif">
						{!! Form::text('fax',@$contactdetails->fax,['class'=>'form-control dm-phone', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('fax', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div> 

				<div class="form-group">
					{!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('emailid')) error @endif">
						{!! Form::text('emailid',null,['class'=>'form-control', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('emailid', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div>

				<div class="form-group">
					{!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('website')) error @endif">
						{!! Form::text('website',null,['class'=>'form-control', 'autocomplete'=>'nope']) !!}
						{!! $errors->first('website', '<p> :message</p>')  !!}
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"></div>
				</div>
			</div><!-- /.box-body ends -->
		</div><!-- Box Contact Person Ends -->
	</div><!--  Right side Content Ends -->

	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
		{!! Form::submit('Save', ['class'=>'btn btn-medcubics']) !!}
		<a href="javascript:void(0)" data-url="{{ url('contactdetail')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	</div>

   
{!! Form::close() !!}

<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
   @include ('practice/layouts/usps_form_modal')
</div><!-- Modal Light Box Ends --> 
@stop

@push('view.scripts')
<script type="text/javascript">
$(".js-address-check" ).trigger( "blur" );
    $(document).ready(function () {
		$('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                practiceceo: {
                    message: '',
                    validators: {
                        notEmpty: {
							message: 'This field is required and can\'t be empty'
						},
						callback: {
							message: '',
							callback: function (value, validator) {
								var alphaspace = '{{ trans("common.validation.alphaspace") }}';
								var regex = new RegExp(/^[A-Za-z ]+$/);
								var msg = lengthValidation(value,'practiceceo',regex,alphaspace);
								if(value != '' && msg != true){
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
                mobileceo: {
                    message: '',
                    validators: {
                        callback: {
                            message:'',
							callback: function (value, validator,$field) {
								var cell_phone_msg = '{{ trans("common.validation.cell_phone_limit") }}';
								var response = phoneValidation(value,cell_phone_msg);
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
                phoneceo: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator,$field) {
								var work_phone_msg = '{{ trans("common.validation.work_phone_limit") }}';
								var ext_msg = '{{ trans("common.validation.work_phone") }}';
								var ext_length = validator.getFieldElements('phoneceo').closest("div.form-group").find("input:last.dm-phone-ext").val().length;
								var response = phoneValidation(value,work_phone_msg,ext_length,ext_msg);
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
                faxceo: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
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
                emailceo: {
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
                practicemanager: {
                    message: '',
                    validators: {
                        
						callback: {
							message: '',
							callback: function (value, validator) {
								var alphaspace = '{{ trans("common.validation.alphaspace") }}';
								var regex = new RegExp(/^[A-Za-z ]+$/);
								var msg = lengthValidation(value,'practicemanager',regex,alphaspace);
								if(value != '' && msg != true){
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
                mobilemanager: {
                    message: '',
                    validators: {
                       
                        callback: {
							message: '',
							callback: function (value, validator,$field) {
								var cell_phone_msg = '{{ trans("common.validation.cell_phone_limit") }}';
								var response = phoneValidation(value,cell_phone_msg);
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
                phonemanager: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
                           callback: function (value, validator,$field) {
								var work_phone_msg = '{{ trans("common.validation.work_phone_limit") }}';
								var ext_msg = '{{ trans("common.validation.work_phone") }}';
								var ext_length = validator.getFieldElements('phonemanager').closest("div.form-group").find("input:last.dm-phone-ext").val().length;
								var response = phoneValidation(value,work_phone_msg,ext_length,ext_msg);
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
                faxmanager: {
                    message: '',
                    validators: {
                       callback: {
                            message: '',
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
                emailmanager: {
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
                companyname: {
                    message: '',
                    validators: {
                       
						callback: {
							message: '',
							callback: function (value, validator) {
								var alphanumericspac = '{{ trans("common.validation.alphanumericspac") }}';
								var regex = new RegExp(/^[A-Za-z0-9 ]+$/);
								var msg = lengthValidation(value,'companyname',regex,alphanumericspac);
								if(value != '' && msg != true){
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
                contactperson: {
                    message: '',
                    validators: {
                        
						callback: {
							message: '',
							callback: function (value, validator) {
								var alphanumericspac = '{{ trans("common.validation.alphanumericspac") }}';
								var regex = new RegExp(/^[A-Za-z0-9 ]+$/);
								var msg = lengthValidation(value,'contactperson',regex,alphanumericspac);
								if(value != '' && msg != true){
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
                address1: {
                    message: '',
                    validators: {
                        message: '',
						callback: {
							message: '',
							callback: function (value, validator) {
								var msg = addressValidation(value,"");
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
				address2: {
                    message: '',
                    validators: {
                        message: '',
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
                        message: '',
						callback: {
							message: '',
							callback: function (value, validator) {
								var msg = cityValidation(value,"");
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
                        message: '',
						callback: {
							message: '',
							callback: function (value, validator) {
								var msg = stateValidation(value,"");
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
								var msg = zip5Validation(value,"");
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
					trigger: 'change keyup',
					validators: {
					   message: '',
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
                phone: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator,$field) {
								var work_phone_msg = '{{ trans("common.validation.work_phone_limit") }}';
								var ext_msg = '{{ trans("common.validation.work_phone") }}';
								var ext_length = validator.getFieldElements('phone').closest("div.form-group").find("input:last.dm-phone-ext").val().length;
								var response = phoneValidation(value,work_phone_msg,ext_length,ext_msg);
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
                            message: '',
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
                emailid: {
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
				}
            }
        });
    });
</script>
@endpush