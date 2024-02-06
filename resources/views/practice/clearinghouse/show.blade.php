@extends('admin')

@section('toolbar')
    <div class="row toolbar-header">
        <?php $clearing_house->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($clearing_house->id,'encode'); ?>
        <section class="content-header">
            <h1>
                <small class="toolbar-heading"><i class="fa {{@$heading_icon}} font14"></i> {{ $heading }} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Show {{ $heading }}</span></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ url('edi') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>               

                <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

                @if($checkpermission->check_url_permission('help/{type}') == 1)
                    <li><a href="#js-help-modal" data-url="{{url('help/edi')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
                @endif
            </ol>
        </section>
    </div>
@stop

@section('practice')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10 margin-b-10">
      @if($checkpermission->check_url_permission('edi/{id}/edit') == 1)
            <a href="{{ url('edi/'.$clearing_house->id.'/edit') }}"  class=" pull-right font14 font600 margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
        @endif
    </div>    

	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
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
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->name }}</p>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"> </div>
				</div> 
				<div class="form-group">
					{!! Form::label('Description', 'Description', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->description }}</p>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2"> </div>
				</div>  

				<div class="form-group">
					{!! Form::label('Contact Name', 'Contact Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->contact_name }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Contact Phone', 'Contact Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->contact_phone }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Contact Fax', 'Contact Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->contact_fax }}</p>
					</div>                        
				</div>  
				<div class="form-group margin-t-10 margin-b-20">
					{!! Form::label('status', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 @if($errors->first('status')) error @endif">
					@if($clearing_house->status == 'Active')
						{!! Form::radio('status', 'Active',true,['class'=>'flat-red']) !!} Active &emsp;
						{!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','disabled']) !!} Inactive
					@else	
						{!! Form::radio('status', 'Active',null,['class'=>'flat-red','disabled']) !!} Active &emsp;
						{!! Form::radio('status', 'Inactive',true,['class'=>'flat-red']) !!} Inactive
					@endif	
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
					@if($clearing_house->enable_eligibility == 'No')	
						{!! Form::radio('enable_eligibility', 'Yes',null,['class'=>'flat-red js_status_change','data-valid'=>'eligibility','disabled']) !!} Yes &emsp;
						{!! Form::radio('enable_eligibility', 'No',true,['class'=>'flat-red js_status_change','data-valid'=>'eligibility']) !!} No
					@else	
						{!! Form::radio('enable_eligibility', 'Yes',true,['class'=>'flat-red js_status_change','data-valid'=>'eligibility']) !!} Yes &emsp;
						{!! Form::radio('enable_eligibility', 'No',null,['class'=>'flat-red js_status_change','data-valid'=>'eligibility','disabled']) !!} No
					@endif	
						{!! $errors->first('enable_eligibility', '<p> :message</p>')  !!}
					</div>                        
				</div> 

				<div class="form-group">
					{!! Form::label('User ID - ISA02', 'User ID - ISA02', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->eligibility_ISA02 }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Password - ISA04', 'Password - ISA04', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->eligibility_ISA04 }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Submitter ID - ISA06', 'Submitter ID - ISA06', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->eligibility_ISA06 }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Receiver ID - ISA08', 'Receiver ID - ISA08', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->eligibility_ISA08 }}</p>
					</div>                        
				</div>  
				<div class="form-group">
					{!! Form::label('Web Service Url', 'Web Service URL', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->eligibility_web_service_url }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Web Service User ID', 'Web Service User ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->eligibility_web_service_user_id }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Web Service Password', 'Web Service Password', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->eligibility_web_service_password }}</p>
					</div>                        
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->          
	</div><!--  Left side Content Ends -->

	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
		<div class="box no-shadow margin-b-10">
			<div class="box-block-header with-border">
				<i class="livicon" data-name="info"></i> <h3 class="box-title">ISA Details</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body form-horizontal margin-l-10">
				<?php 
					$get_auth_array = ['00'=>'No Authorization information Present(No Meaningful information in 102)', '03'=>'Additional Data Identification','0' => 'No interchange acknowledgment requested','1'   => 'Interchange acknowledgement requested','P'=> 'Production','T' => 'Test',''=>'']; 
				?>
										 
				<div class="form-group margin-t-10 margin-b-20">
					{!! Form::label('Enable 837', 'Enable 837', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
					<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 @if($errors->first('enable_837')) error @endif">
					@if($clearing_house->enable_eligibility == 'Yes')
						{!! Form::radio('enable_837', 'Yes',true,['class'=>'flat-red js_status_change','data-valid'=>'enable_field']) !!} Yes &emsp;
						{!! Form::radio('enable_837', 'No',null,['class'=>'flat-red js_status_change','data-valid'=>'enable_field','disabled']) !!} No                            
					@else	
						{!! Form::radio('enable_837', 'Yes',null,['class'=>'flat-red js_status_change','data-valid'=>'enable_field','disabled']) !!} Yes &emsp;
						{!! Form::radio('enable_837', 'No',true,['class'=>'flat-red js_status_change','data-valid'=>'enable_field']) !!} No
					@endif	
						{!! $errors->first('enable_837', '<p> :message</p>')  !!}
					</div>                        
				</div> 

				<div class="form-group">
					{!! Form::label('Authorization Information - ISA01', 'Authorization Information - ISA01', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label star']) !!}
					<div class="col-lg-5 col-md-6 col-sm-6 col-xs-10">
					   <p class="show-border no-bottom">{{ $get_auth_array[$clearing_house->ISA01] }}</p>
					</div>                        
				</div>                
				<div class="form-group">
					{!! Form::label('User ID - ISA02', 'User ID - ISA02', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
					   <p class="show-border no-bottom">{{ @$clearing_house->ISA02 }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Password - ISA04', 'Password - ISA04', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
						<p class="show-border no-bottom">{{ @$clearing_house->ISA04 }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Submitter ID - ISA06', 'Submitter ID - ISA06', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label star']) !!}
					<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
						<p class="show-border no-bottom">{{ @$clearing_house->ISA06 }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Receiver ID - ISA08', 'Receiver ID - ISA08', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label star']) !!}
					<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
						<p class="show-border no-bottom">{{ @$clearing_house->ISA08 }}</p>
					</div>                        
				</div>                  
				<div class="form-group">
				   {!! Form::label('Acknowledgement Request - ISA14', 'Acknowledgement Request - ISA14', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label star']) !!}
					<div class="col-lg-5 col-md-6 col-sm-6 col-xs-10">
					<p class="show-border no-bottom">{{ @$get_auth_array[$clearing_house->ISA14] }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Submission Mode - ISA15', 'Submission Mode - ISA15', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label star']) !!}
					<div class="col-lg-5 col-md-6 col-sm-6 col-xs-10">
					   <p class="show-border no-bottom">{{ @$get_auth_array[$clearing_house->ISA15] }}</p>
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
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->ftp_address }}</p>
					</div>                        
				</div>
				
				 <div class="form-group">
					{!! Form::label('FTP Port', 'FTP Port', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ @$clearing_house->ftp_port }}</p>
					</div>                        
				</div>
				
				<div class="form-group">
					{!! Form::label('User ID', 'User ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->ftp_user_id }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Password', 'Password', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
					   <p class="show-border no-bottom">{{ $clearing_house->ftp_password }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Upload Path', 'Upload Path', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
					   <p class="show-border no-bottom">{{ $clearing_house->ftp_folder }}</p>
					</div>                        
				</div>
				<div class="form-group">
					{!! Form::label('Download Path', 'Download Path', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->edi_report_folder }}</p>
					</div>                        
				</div>
				
				<div class="form-group">
					{!! Form::label('Professional File Extention', 'Professional File Extention', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->ftp_file_extension_professional }}</p>
					</div>                        
				</div>
				<div class="form-group">
				
					{!! Form::label('Institutional File Extention', 'Institutional File Extention', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $clearing_house->ftp_file_extension_institutional }}</p>
					</div>                        
				</div>
				<div class="margin-b-10 hidden-sm hidden-xs">&emsp;</div>                  

			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->

	</div><!--  Right side Content Ends -->
@stop