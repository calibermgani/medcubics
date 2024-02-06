@extends('admin')

@section('toolbar')
    <div class="row toolbar-header">
        <?php $clearing_house->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($clearing_house->id,'encode'); ?>
        <section class="content-header">
            <h1>
                <small class="toolbar-heading"><i class="fa {{@$heading_icon}} font14"></i> {{ $heading }}</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ url('admin/edi') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>               

                <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>

                @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
                    <li><a href="#js-help-modal" data-url="{{url('help/edi')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
                @endif
            </ol>
        </section>
    </div>
@stop

@section('practice')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10 margin-b-10">
      @if($checkpermission->check_adminurl_permission('admin/edi/{id}/edit') == 1)
            <a href="{{ url('admin/edi/'.$clearing_house->id.'/edit') }}"  class=" pull-right font14 font600 margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
        @endif
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
        <div class="box box-view no-shadow"><!--  Box Starts -->
            <div class="box-header-view">
               <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table-responsive table-striped-view table">
                    <tbody>                           
                        <tr>
                            <td>Practice Name</td>
                            <td>{{ $clearing_house->practice_details->practice_name }}</td>
                        </tr>
						<tr>
                            <td>Name</td>
                            <td>{{ $clearing_house->name }}</td>
                        </tr>  
                        <tr>
                            <td>Description</td>
                            <td>{{ $clearing_house->description }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>{{ $clearing_house->status }}</td>
                        </tr>
                        <tr>
                            <td>Contact Name</td>
                            <td>{{ $clearing_house->contact_name }}</td>
                        </tr>
                        <tr>
                            <td>Contact Phone</td>
                            <td>{{ $clearing_house->contact_phone }}</td>
                        </tr>
                        <tr>
                            <td>Contact Fax</td>
                            <td>{{ $clearing_house->contact_fax }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"><div class="margin-b-05 hidden-sm">&emsp;</div></td>
                        </tr>

                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->

        <div class="box box-view no-shadow"><!--  Box Starts -->
            <div class="box-header-view">
               <i class="livicon" data-name="globe"></i> <h3 class="box-title">Eligibility Web Service</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table-responsive table-striped-view table">
                    <tbody>    
						<tr>
                            <td>Enable Eligibility</td>
                            <td>{{ $clearing_house->enable_eligibility }}</td>
                        </tr>
                        <tr>
                            <td>User ID - ISA02</td>
                            <td>{{ $clearing_house->eligibility_ISA02 }}</td>
                        </tr>  
                        <tr>
                            <td>Password - ISA04</td>
                            <td>{{ $clearing_house->eligibility_ISA04 }}</td>
                        </tr>
                        <tr>
                            <td>Submitter ID - ISA06</td>
                            <td>{{ $clearing_house->eligibility_ISA06 }}</td>
                        </tr>
                        <tr>
                            <td>Receiver ID - ISA08</td>
                            <td>{{ $clearing_house->eligibility_ISA08 }}</td>
                        </tr>
                        <tr>
                            <td>Web Service Url</td>
                            <td>{{ $clearing_house->eligibility_web_service_url }}</td>
                        </tr>
                        <tr>
                            <td>Web Service User ID</td>
                            <td>{{ $clearing_house->eligibility_web_service_user_id }}</td>
                        </tr>
                        <tr>
                            <td>Web Service Password</td>
                            <td>{{ $clearing_house->eligibility_web_service_password }}</td>
                        </tr>
                        
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->

    </div><!--  Left side Content Ends -->


    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
        <div class="box box-view no-shadow"><!--  Box Starts -->
            <div class="box-header-view">
               <i class="livicon" data-name="info"></i> <h3 class="box-title">ISA Information</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table-responsive table-striped-view table">
                    <tbody>
					<?php 
						$get_auth_array = ['00'=>'No Authorization information Present(No Meaningful information in 102)', '03'=>'Additional Data Identification','0' => 'No interchange acknowledgment requested','1'   => 'Interchange acknowledgement requested','P'=> 'Production','T' => 'Test']; 
					?>
						<tr>
                            <td>Enable 837</td>
                            <td>{{ $clearing_house->enable_837 }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Authorization Information - ISA01</td>
                            <td>@if($clearing_house->ISA01 !='') {{ $get_auth_array[$clearing_house->ISA01] }} @endif</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>User ID - ISA02</td>
                            <td>{{ $clearing_house->ISA02 }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Password - ISA04</td>
                            <td>{{ @$clearing_house->ISA04 }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Submitter ID - ISA06</td>
                            <td>{{ @$clearing_house->ISA06 }}</td>
                            <td></td>
                        </tr>                 
                        <tr>
                            <td>Receiver ID - ISA08</td>
                            <td>{{ @$clearing_house->ISA08 }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Acknowledgement Request - ISA14</td>
                            <td>@if($clearing_house->ISA14 !='') {{ @$get_auth_array[$clearing_house->ISA14] }} @endif</td>
                            <td></td>
                        </tr>                        
                        <tr>
                            <td>Submission Mode - ISA15</td>
                            <td>@if($clearing_house->ISA15 !='') {{ @$get_auth_array[$clearing_house->ISA15] }} @endif</td>
                            <td></td>
                        </tr>                       
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->  

        <div class="box box-view no-shadow"><!--  Box Starts -->
            <div class="box-header-view">
               <i class="livicon" data-name="inbox-in"></i> <h3 class="box-title">Claims FTP Information</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table-responsive table-striped-view table">
                    <tbody>
                        <tr>
                            <td>FTP Address</td>
                            <td>{{ $clearing_house->ftp_address }}</td>
                            <td></td>
                        </tr>
						 <tr>
                            <td>FTP Port</td>
                            <td>{{ @$clearing_house->ftp_port }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>User ID</td>
                            <td>{{ $clearing_house->ftp_user_id }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td>{{ $clearing_house->ftp_password }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Upload Path</td>
                            <td>{{ $clearing_house->ftp_folder }}</td>
                            <td></td>
                        </tr>                 
						<tr>
                            <td>Download Path</td>
                            <td>{{ $clearing_house->edi_report_folder }}</td>
                            <td></td>
                        </tr>                 
                        <tr>
                            <td>Professional File Extention</td>
                            <td>{{ $clearing_house->ftp_file_extension_professional }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Institutional File Extention</td>
                            <td>{{ $clearing_house->ftp_file_extension_institutional }}</td>
                            <td></td>
                        </tr> 

                        <tr>
                            <td colspan="2"><div class="margin-b-15 hidden-sm">&emsp;</div></td>
                        </tr>                   
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->     
    </div><!-- Right side Content Ends -->
@stop
