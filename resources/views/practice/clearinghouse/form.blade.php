<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.edi") }}' />

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-10"><!--  Left side Content Starts -->
	<div class="box  no-shadow margin-b-10">
		<div class="box-block-header with-border">
			<i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body form-horizontal margin-l-10">                   
			<div class="form-group">
				{!! Form::label('Name', 'Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('name')) error @endif">
					{!! Form::text('name',null,['class'=>'form-control','maxlength'=>50]) !!}
					{!! $errors->first('name', '<p> :message</p>')  !!}
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2"> </div>
			</div> 
			<div class="form-group">
				{!! Form::label('Description', 'Description', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('description')) error @endif">
					{!! Form::textarea('description',null,['class'=>'form-control']) !!}
					{!! $errors->first('description', '<p> :message</p>')  !!}
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2"> </div>
			</div>               
					

			<div class="form-group">
				{!! Form::label('Contact Name', 'Contact Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('contact_name')) error @endif">
					{!! Form::text('contact_name',null,['id'=>'contact_name','class'=>'form-control js-letters-caps-format','maxlength'=>50]) !!}
					{!! $errors->first('contact_name', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Contact Phone', 'Contact Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('contact_phone')) error @endif">
					{!! Form::text('contact_phone',null,['id'=>'contact_phone','class'=>'form-control dm-phone']) !!}
					{!! $errors->first('contact_phone', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Contact Fax', 'Contact Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('contact_fax')) error @endif">
					{!! Form::text('contact_fax',null,['id'=>'contact_fax','class'=>'form-control dm-phone']) !!}
					{!! $errors->first('contact_fax', '<p> :message</p>')  !!}
				</div>                        
			</div>  
                    <div class="form-group margin-t-10 margin-b-20">
                        {!! Form::label('status', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 @if($errors->first('status')) error @endif">
                            {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'ed_active']) !!} {!! Form::label('ed_active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                            {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'ed_inactive']) !!} {!! Form::label('ed_inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                            {!! $errors->first('status', '<p> :message</p>')  !!}
                        </div>                        
                    </div> 
			<div class="margin-b-55 hidden-sm hidden-xs">&emsp;</div>                 
		</div><!-- /.box-body -->
	</div><!-- /.box Ends-->   
	<div class="box  no-shadow margin-b-10">
		<div class="box-block-header with-border">
			<i class="livicon" data-name="globe"></i> <h3 class="box-title">Eligibility Web Service</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body form-horizontal margin-l-10 p-b-25">  
			<div class="form-group margin-t-10 margin-b-20">
				{!! Form::label('Enable Eligibility', 'Enable Eligibility', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 @if($errors->first('enable_eligibility')) error @endif">
					{!! Form::radio('enable_eligibility', 'Yes',null,['class'=>'js_status_change','data-valid'=>'eligibility','id'=>'edi_yes']) !!} {!! Form::label('edi_yes', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
					{!! Form::radio('enable_eligibility', 'No',true,['class'=>'js_status_change','data-valid'=>'eligibility','id'=>'edi_no']) !!} {!! Form::label('edi_no', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
					{!! $errors->first('enable_eligibility', '<p> :message</p>')  !!}
				</div>                        
			</div> 

			<div class="form-group">
				{!! Form::label('User ID - ISA02', 'User ID - ISA02', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('eligibility_ISA02')) error @endif">
					{!! Form::text('eligibility_ISA02',null,['id'=>'eligibility_ISA02','class'=>'form-control js_eligibility','maxlength'=>10]) !!}
					{!! $errors->first('eligibility_ISA02', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Password - ISA04', 'Password - ISA04', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('eligibility_ISA04')) error @endif">
					{!! Form::text('eligibility_ISA04',null,['id'=>'eligibility_ISA04','class'=>'form-control js_eligibility','maxlength'=>10]) !!}
					{!! $errors->first('eligibility_ISA04', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Submitter ID - ISA06', 'Submitter ID - ISA06', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('eligibility_ISA06')) error @endif">
					{!! Form::text('eligibility_ISA06',null,['id'=>'eligibility_ISA06','class'=>'form-control js_eligibility','maxlength'=>15]) !!}
					{!! $errors->first('eligibility_ISA06', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Receiver ID - ISA08', 'Receiver ID - ISA08', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('eligibility_ISA08')) error @endif">
					{!! Form::text('eligibility_ISA08',null,['id'=>'eligibility_ISA08','class'=>'form-control js_eligibility','maxlength'=>15]) !!}
					{!! $errors->first('eligibility_ISA08', '<p> :message</p>')  !!}
				</div>                        
			</div>  
			<div class="form-group">
				{!! Form::label('Web Service Url', 'Web Service URL', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('eligibility_web_service_url')) error @endif">
					{!! Form::text('eligibility_web_service_url',null,['id'=>'eligibility_web_service_url','class'=>'form-control js_eligibility']) !!}
					{!! $errors->first('eligibility_web_service_url', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Web Service User ID', 'Web Service User ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('eligibility_web_service_user_id')) error @endif">
					{!! Form::text('eligibility_web_service_user_id',null,['id'=>'eligibility_web_service_user_id','class'=>'form-control js_eligibility','maxlength'=>20]) !!}
					{!! $errors->first('eligibility_web_service_user_id', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Web Service Password', 'Web Service Password', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('eligibility_web_service_password')) error @endif">
					{!! Form::text('eligibility_web_service_password',null,['id'=>'eligibility_web_service_password','class'=>'form-control js_eligibility','maxlength'=>20]) !!}
					{!! $errors->first('eligibility_web_service_password', '<p> :message</p>')  !!}
				</div>                        
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box Ends-->          
</div><!--  Left side Content Ends -->
		
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-10"><!--  Left side Content Starts -->
	<div class="box no-shadow margin-b-10">
		<div class="box-block-header with-border">
			<i class="livicon" data-name="info"></i> <h3 class="box-title">ISA Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body form-horizontal margin-l-10">
			<div class="form-group margin-t-10 margin-b-20">
				{!! Form::label('Enable 837', 'Enable 837', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 @if($errors->first('enable_837')) error @endif">
					{!! Form::radio('enable_837', 'Yes',true,['class'=>'js_status_change','data-valid'=>'enable_field','id'=>'e-yes']) !!} {!! Form::label('e-yes', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
					{!! Form::radio('enable_837', 'No',null,['class'=>'js_status_change','data-valid'=>'enable_field','id'=>'e-no']) !!} {!! Form::label('e-no', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
					{!! $errors->first('enable_837', '<p> :message</p>')  !!}
				</div>                        
			</div> 

			<div class="form-group">
				{!! Form::label('Authorization Information - ISA01', 'Authorization Information - ISA01', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label star']) !!}
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-10 @if($errors->first('ISA01')) error @endif">
					 {!! Form::select('ISA01', ['' => '-- Select --','00' => 'No Authorization information Present(No Meaningful information in 102)','03' => 'Additional Data Identification'], null, ['id'=>'ISA01','class' => 'form-control select2 js_enable_field']) !!}
					{!! $errors->first('ISA01', '<p> :message</p>')  !!}
				</div>                        
			</div>                    
			<div class="form-group">
				{!! Form::label('User ID - ISA02', 'User ID - ISA02', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('ISA02')) error @endif">
					{!! Form::text('ISA02',null,['id'=>'ISA02','class'=>'form-control js_enable_field','maxlength'=>10]) !!}
					{!! $errors->first('ISA02', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Password - ISA04', 'Password - ISA04', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('ISA04')) error @endif">
					{!! Form::text('ISA04',null,['id'=>'ISA04','class'=>'form-control js_enable_field','maxlength'=>10]) !!}
					{!! $errors->first('ISA04', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Submitter ID - ISA06', 'Submitter ID - ISA06', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label star']) !!}
				<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('ISA06')) error @endif">
					{!! Form::text('ISA06',null,['id'=>'ISA06','class'=>'form-control js_enable_field','maxlength'=>15]) !!}
					{!! $errors->first('ISA06', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Receiver ID - ISA08', 'Receiver ID - ISA08', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label star']) !!}
				<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 @if($errors->first('ISA08')) error @endif">
					{!! Form::text('ISA08',null,['id'=>'ISA08','class'=>'form-control js_enable_field','maxlength'=>15]) !!}
					{!! $errors->first('ISA08', '<p> :message</p>')  !!}
				</div>                        
			</div>                    
			<div class="form-group">
			   {!! Form::label('Acknowledgement Request - ISA14', 'Acknowledgement Request - ISA14', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label star']) !!}
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-10 @if($errors->first('ISA14')) error @endif">
				{!! Form::select('ISA14', ['' => '-- Select --','0' => 'No interchange acknowledgment requested','1'   => 'Interchange acknowledgement requested'], null, ['id'=>'ISA14','class' => 'form-control select2 js_enable_field']) !!}
					{!! $errors->first('ISA14', '<p> :message</p>')  !!}
				</div>                        
			</div>
			<div class="form-group">
				{!! Form::label('Submission Mode - ISA15', 'Submission Mode - ISA15', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label star']) !!}
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-10 @if($errors->first('ISA15')) error @endif">
					{!! Form::select('ISA15', ['' => '-- Select --','P'=> 'Production','T' => 'Test'], null, ['id'=>'ISA15','class' => 'form-control select2 js_enable_field']) !!}
					{!! $errors->first('ISA15', '<p> :message</p>')  !!}
				</div>                        
			</div>                 
		</div><!-- /.box-body -->
	</div><!-- /.box Ends-->         
</div><!--  Left side Content Ends -->


        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
            <div class="box no-shadow margin-b-10">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="inbox-in"></i> <h3 class="box-title">Claims FTP Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10">
                    <div class="form-group">
                        {!! Form::label('FTP Address', 'FTP Address', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('ftp_address')) error @endif">
                            {!! Form::text('ftp_address',null,['id'=>'ftp_address','class'=>'form-control','maxlength'=>250]) !!}
                            {!! $errors->first('ftp_address', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
					
					 <div class="form-group">
                        {!! Form::label('FTP Port', 'FTP Port', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('ftp_address')) error @endif">
                            {!! Form::text('ftp_port',(@$clearing_house->ftp_port == '')?'2222':@$clearing_house->ftp_port,['id'=>'ftp_port','class'=>'form-control dm-zip4']) !!}
                            {!! $errors->first('ftp_port', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
					
                    <div class="form-group">
                        {!! Form::label('User ID', 'User ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('ftp_user_id')) error @endif">
                            {!! Form::text('ftp_user_id',null,['id'=>'ftp_user_id','class'=>'form-control','maxlength'=>100]) !!}
                            {!! $errors->first('ftp_user_id', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
                    <div class="form-group">
                        {!! Form::label('Password', 'Password', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('ftp_password')) error @endif">
                            {!! Form::text('ftp_password',null,['id'=>'ftp_password','class'=>'form-control','maxlength'=>50]) !!}
                            {!! $errors->first('ftp_password', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
                    <div class="form-group">
                        {!! Form::label('Upload Path', 'Upload Path', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('ftp_folder')) error @endif">
                            {!! Form::text('ftp_folder',null,['id'=>'ftp_folder','class'=>'form-control','maxlength'=>150]) !!}
                            {!! $errors->first('ftp_folder', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
                    <div class="form-group">
                        {!! Form::label('Download Path', 'Download Path', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('edi_report_folder')) error @endif">
                            {!! Form::text('edi_report_folder',null,['id'=>'edi_report_folder','class'=>'form-control','maxlength'=>150]) !!}
                            {!! $errors->first('edi_report_folder', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
					<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                    <div class="form-group">
                        {!! Form::label('Professional File Extention', 'Professional File Extention', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('ftp_file_extension_professional')) error @endif">
                            {!! Form::text('ftp_file_extension_professional',(strpos($currnet_page, 'create') !== false && @$clearing_house->ftp_file_extension_professional == '')?'txt':@$clearing_house->ftp_file_extension_professional,['id'=>'ftp_file_extension_professional','class'=>'form-control','maxlength'=>5]) !!}
                            {!! $errors->first('ftp_file_extension_professional', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
                    <div class="form-group">
					
                        {!! Form::label('Institutional File Extention', 'Institutional File Extention', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('ftp_file_extension_institutional')) error @endif">
                            {!! Form::text('ftp_file_extension_institutional',(strpos($currnet_page, 'create') !== false && @$clearing_house->ftp_file_extension_institutional == '')?'txt':@$clearing_house->ftp_file_extension_institutional,['id'=>'ftp_file_extension_institutional','class'=>'form-control','maxlength'=>5]) !!}
                            {!! $errors->first('ftp_file_extension_institutional', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
                    <div class="margin-b-10 hidden-sm hidden-xs">&emsp;</div>
                   

                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->

        </div><!--  Right side Content Ends -->


        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
            
            @if(strpos($currnet_page, 'edit') !== false)                
                @if($checkpermission->check_url_permission('edi/delete/{id}') == 1)
                    <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete?" href="{{ url('edi/delete/'.$clearing_house->id) }}">Delete</a>
                @endif
                
                <a href="javascript:void(0)" data-url="{{ url('edi/'.$clearing_house->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
            @else
                <a href="javascript:void(0)" data-url="{{ url('edi')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
            @endif 
        </div>
    
@push('view.scripts')                           
<script type="text/javascript">

    $(document).ready(function() {
		$(document).on('change', '#ISA01', function () {
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="ISA02"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="ISA04"]'));
        });
		$(document).on('ifToggled click', '.js_status_change:checked', function () {
			var valid_class = $(this).attr("data-valid");
			$('input.js_'+valid_class).each(function() {
				var get_name = $(this).attr('name');
				$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="'+get_name+'"]'));
			});
			if($('select.js_'+valid_class).length>0) {
				$('select.js_'+valid_class).each(function() {
					var get_name = $(this).attr('name');
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('[name="'+get_name+'"]'));
				});
			}
        });
        $('#js-bootstrap-validator')
            .bootstrapValidator({
                message: '',
                excluded: ':disabled',
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    name:{
                        message:'',
                        validators:{
                            notEmpty:{
                                message: '{{ trans("admin/clearinghouse.validation.enter_name") }}'
                                },
							regexp:{
								regexp: /^[A-Za-z ]+$/,
								message: '{{ trans("common.validation.alphaspace") }}'
							}
                        }
                    },
                    status:{
                        message:'',
                        validators:{
                            notEmpty:{
								message: '{{ trans("admin/clearinghouse.validation.enter_name") }}'
							}
                        }
                    },
                    contact_name:{
                        message:'',
                        validators:{
                            notEmpty:{
                                message: '{{ trans("admin/clearinghouse.validation.contact_name") }}'
                            },
                            regexp:{
                                regexp: /^[A-Za-z ]+$/,
                                message: '{{ trans("common.validation.alphaspace") }}'
                            }
                        }
                    },
					contact_phone:{
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("common.validation.phone") }}'
							},
							callback: {
								message:'',
								callback: function (value, validator,$field) {
									var cell_phone_msg = '{{ trans("common.validation.phone_limit") }}';
									var response = phoneValidation(value,cell_phone_msg);
									if(value != '' && response !=true) {
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
					contact_fax: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var fax_msg = '{{ trans("common.validation.fax_limit") }}';
									var response = phoneValidation(value,fax_msg);
									if(value != '' && response !=true) {
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
					enable_eligibility: {
						message: '',
						validators: {
							notEmpty:{
								message: '{{ trans("admin/clearinghouse.validation.eligibility") }}'
							},
						}
					},
					eligibility_ISA02: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var eligible 	= $('[name="enable_eligibility"]:checked').val();
									var error_msg 	= '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}';
									var eligible_msg= '{{ trans("admin/clearinghouse.validation.ISA02") }}';
									var get_msg = getValidation(value,error_msg,eligible,eligible_msg)
									if(get_msg != true){
										return {
											valid: true,
											message: get_msg
										};
									}
									return true;
								}
							}
						}
					},
					eligibility_ISA04: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var eligible 	= $('[name="enable_eligibility"]:checked').val();
									var error_msg 	= '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}';
									var eligible_msg= '{{ trans("admin/clearinghouse.validation.ISA04") }}';
									var get_msg = getValidation(value,error_msg,eligible,eligible_msg)
									if(get_msg != true){
										return {
											valid: true,
											message: get_msg
										};
									}
									return true;
								}
							}
						}
					},
					eligibility_ISA06: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var eligible 	= $('[name="enable_eligibility"]:checked').val();
									var error_msg 	= '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}';
									var eligible_msg= '{{ trans("admin/clearinghouse.validation.ISA06") }}';
									var get_msg = getValidation(value,error_msg,eligible,eligible_msg)
									if(get_msg != true){
										return {
											valid: false,
											message: get_msg
										};
									}
									return true;
								}
							}
						}
					},
					eligibility_ISA08: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var eligible 	= $('[name="enable_eligibility"]:checked').val();
									var error_msg 	= '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}';
									var eligible_msg= '{{ trans("admin/clearinghouse.validation.ISA08") }}';
									var get_msg = getValidation(value,error_msg,eligible,eligible_msg)
									if(get_msg != true){
										return {
											valid: false,
											message: get_msg
										};
									}
									return true;
								}
							}
						}
					},
					eligibility_web_service_url: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var eligible 	= $('[name="enable_eligibility"]:checked').val();
									if(eligible == "Yes") {
										if(value =='')
										{
											return {
												valid: false,
												message: '{{ trans("admin/clearinghouse.validation.web_url") }}'
											};
										}
									}
									var regex = new RegExp(/^(http(s)?:\/\/)?(www\.)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/);
									var website_valid = '{{ trans("common.validation.website_valid") }}';
									var msg = lengthValidation(value,'feeschedule',regex,website_valid);
									if(value.length>0 && msg != true){
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
					eligibility_web_service_user_id: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var eligible 	= $('[name="enable_eligibility"]:checked').val();
									var error_msg 	= '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}';
									var eligible_msg= '{{ trans("admin/clearinghouse.validation.web_user_id") }}';
									var get_msg = getValidation(value,error_msg,eligible,eligible_msg)
									if(get_msg != true){
										return {
											valid: false,
											message: get_msg
										};
									}
									return true;
								}
							}
						}
					},
					eligibility_web_service_password: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var eligible 	= $('[name="enable_eligibility"]:checked').val();
									var error_msg 	= '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}';
									var eligible_msg= '{{ trans("admin/clearinghouse.validation.web_password") }}';
									var get_msg = getValidation(value,error_msg,eligible,eligible_msg)
									if(get_msg != true){
										return {
											valid: false,
											message: get_msg
										};
									}
									return true;
								}
							}
						}
					},
					enable_837: {
						message: '',
						validators: {
							notEmpty:{
								message: '{{ trans("admin/clearinghouse.validation.enable_837") }}'
							},
						}
					},
					ISA01: {
						message: '',
						validators: {
							callback: {
								message: '{{ trans("admin/clearinghouse.validation.ISA01") }}',
								callback: function (value, validator) {
									var enable 		= $('[name="enable_837"]:checked').val();
									if(enable == "Yes" && value==''){
										return false;
									}
									return true;
								}
							}
						}
					},
					ISA02: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var enable 		= $('[name="enable_837"]:checked').val();
									var isa01 		= $('#ISA01').val();
									var error_msg 	= '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}';
									var eligible_msg= '{{ trans("admin/clearinghouse.validation.ISA02") }}';
									if(isa01 == "03") 
									{
										var get_msg = getValidation(value,error_msg,enable,eligible_msg);
										if(get_msg != true){
											return {
												valid: false,
												message: get_msg
											};
										}
										return true;
									} 
									return true;
								}
							}
						}
					},
					ISA04: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var enable 		= $('[name="enable_837"]:checked').val();
									var isa01 		= $('#ISA01').val();
									var error_msg 	= '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}';
									var eligible_msg= '{{ trans("admin/clearinghouse.validation.ISA04") }}';
									if(isa01 == "03") 
									{
										var get_msg = getValidation(value,error_msg,enable,eligible_msg)
										if(get_msg != true){
											return {
												valid: false,
												message: get_msg
											};
										}
										return true;
									}
									return true;
								}
							}
						}
					},
					ISA06: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var enable 		= $('[name="enable_837"]:checked').val();
									var error_msg 	= '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}';
									var eligible_msg= '{{ trans("admin/clearinghouse.validation.ISA06") }}';
									var get_msg = getValidation(value,error_msg,enable,eligible_msg)
									if(get_msg != true){
										return {
											valid: false,
											message: get_msg
										};
									}
									return true;
								}
							}
						}
					},
					ISA08: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var enable 		= $('[name="enable_837"]:checked').val();
									var error_msg 	= '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}';
									var eligible_msg= '{{ trans("admin/clearinghouse.validation.ISA08") }}';
									var get_msg = getValidation(value,error_msg,enable,eligible_msg)
									if(get_msg != true){
										return {
											valid: false,
											message: get_msg
										};
									}
									return true;
								}
							}
						}
					},
					ISA14: {
						message: '',
						validators: {
							callback: {
								message: '{{ trans("admin/clearinghouse.validation.ISA14") }}',
								callback: function (value, validator) {
									var enable 		= $('[name="enable_837"]:checked').val();
									if(enable == "Yes" && value==''){
										return false;
									}
									return true;
								}
							}
						}
					},
					ISA15: {
						message: '',
						validators: {
							callback: {
								message: '{{ trans("admin/clearinghouse.validation.ISA15") }}',
								callback: function (value, validator) {
									var enable 		= $('[name="enable_837"]:checked').val();
									if(enable == "Yes" && value==''){
										return false;
									}
									return true;
								}
							}
						}
					},
					ftp_address: {
						message: '',
						validators: {
							notEmpty:{
								message: '{{ trans("admin/clearinghouse.validation.ftp_address") }}'
							},
							callback: {
								message: '',
								callback: function (value, validator) {
									var website_valid = '{{ trans("admin/clearinghouse.validation.ftp_address_regex") }}';
									var regex = new RegExp(/^(ftp?:\/\/)?(www\.)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/);
									var msg = lengthValidation(value,'feeschedule',regex,website_valid);
									if(value.length>0 && msg != true){
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
					ftp_port: {
						message: '',
						validators: {
							notEmpty:{
								message: '{{ trans("admin/clearinghouse.validation.ftp_port") }}'
							},
							regexp:{
                                regexp: /^[0-9]+$/,
                                message: '{{ trans("common.validation.numeric") }}'
                            }
						}
					},
					ftp_user_id: {
						message: '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}',
						validators: {
							notEmpty:{
								message: '{{ trans("admin/clearinghouse.validation.ftp_user_id") }}'
							},
							callback: {
								message: '',
								callback: function (value, validator) {
									var regex = new RegExp(/\S /);
									return (regex.test(value)) ? false:true;
								}
							}
						}
					},
					ftp_password: {
						message: '{{ trans("admin/clearinghouse.validation.space_not_allowed") }}',
						validators: {
							notEmpty:{
								message: '{{ trans("admin/clearinghouse.validation.ftp_password") }}'
							},
							callback: {
								message: '',
								callback: function (value, validator) {
									var regex = new RegExp(/\S /);
									return (value !='' && regex.test(value)) ? false:true;
								}
							}
						}
					},
					ftp_folder: {
						message: '',
						validators: {
							notEmpty:{
								message: '{{ trans("admin/clearinghouse.validation.ftp_folder") }}'
							},
							
						}
					},
					edi_report_folder: {
						message: '',
						validators: {
							notEmpty:{
								message: '{{ trans("admin/clearinghouse.validation.edi_report_folder") }}'
							}
						}
					},
					ftp_file_extension_professional: {
						message: '',
						validators: {
							callback: {
								message: '{{ trans("common.validation.alpha") }}',
								callback: function (value, validator) {
									var regex = new RegExp(/^[A-Za-z]+$/);
									return (value !='' && !regex.test(value)) ? false:true;
								}
							}
						}
					},
					ftp_file_extension_institutional: {
						message: '',
						validators: {
							callback: {
								message: '{{ trans("common.validation.alpha") }}',
								callback: function (value, validator) {
									var regex = new RegExp(/^[A-Za-z]+$/);
									return (value !='' && !regex.test(value)) ? false:true;
								}
							}
						}
					},
                }
            });
    });
	
	function getValidation(value,error_msg,eligible,eligible_msg) {
		var regex = new RegExp(/\S /);
		if(regex.test(value))
			return error_msg;
		if(eligible == "Yes" && value == "") { 
			return eligible_msg;
		}
		return true;
	}
</script>
@endpush