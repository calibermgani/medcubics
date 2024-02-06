@extends('admin')
@section('toolbar')
<div class="row toolbar-header" >
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="dashboard"></i>Dashboard </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Database Connection -->
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-database"></i> Database Connection</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">Active</h3>
        <p class="med-gray-dark font600 no-bottom"><span class="med-orange fa fa-retweet"></i> </span> re-check</p>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6  m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-users"></i> Active Users</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">$0.00</h3>
        <p class="med-gray-dark font600 no-bottom"><a href="adminuser/create" target="_blank"><span class="med-orange fa fa-plus-square-o"></span> add user</a></p>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-space-shuttle"></i> Last Backup</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">30 Days</h3>
        <p class="med-gray-dark font600 no-bottom"><span class="med-orange fa fa-upload"></i> </span> backup now</p>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-bug"></i> Last Error</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">$0.00</h3>
        <p class="med-gray-dark font600 no-bottom"><span class="med-orange fa fa-eye"></i> </span> <a href="errorlog">view error log</a></p>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6  m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-ticket"></i> Tickets</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">$0.00</h3>
        <p class="med-gray-dark font600 no-bottom"><span class="med-orange fa fa-eye"></i> </span> <a href="manageticket">view tickets</a></p>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6  m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-users"></i> Avg. Visits</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">242</h3>
        <p class="med-gray-dark font600 no-bottom"><span class="med-orange fa fa-chevron-down"></i> </span> from last month</p>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">
    <div class="box box-info no-shadow">
        <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="dash-headings"><i class="fa fa-list-alt"></i> Practices</h4>
                        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box-body table-responsive">
                <div style="border: 1px solid #008E97;border-radius: 4px; max-height: 500px; overflow-y: scroll;">
                <div class="box-header med-bg-green no-padding" style="border-radius: 4px 4px 0px 0px;">
                    <div class="col-lg-6 col-md-6 col-sm-2 col-xs-4" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Practice Name</h3>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-2 col-xs-4" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Status</h3>
                    </div>

                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-5"><!--  Left side Content Starts -->              
                        @foreach($pra_list as $praName=>$praStatus)
                         <?php
	                        $pra_detail = App\Models\Medcubics\Practice::practiceID($praName);
	                        $pra_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($pra_detail->id, 'encode');
	                        $cus_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($pra_detail->customer_id, 'encode');
                            $praStats = @$practice_details[$praName];
	                     ?>
                        <div class="box collapsed-box" style="box-shadow:none;margin:0px;">
                            <div class="box-header-view-white no-padding" style="color: #fff;border-bottom: 1px solid #CDF7FC;">
                                <div class="col-lg-6 col-md-6 col-sm-2 col-xs-4" style="border-right: 1px solid #fff;">
                                    <h3 class="box-title font12 font-normal">
                                         <button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i></button></h3>
                                    <a href=""><span style="color: #868686;">{{ $praName }}</span></a>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{ $praStatus }}</span>
                                </div>
                                @if($praStatus == 'Active')
                                <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <i class="fa fa-database med-green font16 p-r-5 margin-r-10 dash-icon cur-pointer" title="Backup now" style="margin-top: 5px;"></i>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                    <i class="fa fa-check-square-o med-green font16 margin-r-10 dash-icon" title="Database Connected" style="margin-top: 5px;"></i>
                                </div>
                                @elseif($praStatus == 'inactive')
                                <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <i class="fa fa-refresh med-red font16 margin-r-10 dash-icon cur-pointer" title="Refresh Connection" style="margin-top: 5px;"></i>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                    <i class="fa fa-close med-red font16 margin-r-10 dash-icon" title="Database not found" style="margin-top: 5px;"></i>
                                </div>
                                @elseif($praStatus == 'In Progress')
                                <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <i class="fa fa-refresh med-red font16 margin-r-10 dash-icon cur-pointer" title="Refresh Connection" style="margin-top: 5px;"></i>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                    <i class="fa fa-close med-red font16 margin-r-10 dash-icon" title="Database not found" style="margin-top: 5px;"></i>
                                </div>
                                @endif
                            </div>
                            <div class="box-body form-horizontal">
                                <div class="col-md-12 col-sm-12 col-xs-12 no-padding border-radius-4 yes-border border-b4f7f7">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                        <span class="med-orange margin-l-10 font13 font600 padding-0-4 bg-white">Practice Details</span>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10" >   
                                        <div class="box-body">
										    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal print-m-t-m-5">
										        <div class="form-group">
										            <label for="Phone" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Patient Statement sent</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['patient_statement_sent']!==0 ?$praStats['patient_statement_sent']: '<span class=" nill "> - Nil - </span>' ?> 
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Fax" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Patient statement API usage</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['patient_statement_api_usage']!==0 ?$praStats['patient_statement_api_usage']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>                                    
										        </div> 

										        <div class="form-group">
										            <label for="Email" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Document Upload Size</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										               <?php echo($praStats)&&$praStats['document_upload_size']!==0 ?$praStats['document_upload_size']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>                                    
										        </div> 

										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Document Count</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['document_count']!==0 ?$praStats['document_count']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Eligibility API usage</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['eligibility_api_usage']!==0 ?$praStats['eligibility_api_usage']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Twilio Usage for SMS, Calls and Fax</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)?$praStats['twilio_sms_usage']." / ".$praStats['twilio_call_usage']." / ".$praStats['twilio_fax_usage']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Patient Count</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['total_patients']!==0 ?$praStats['total_patients']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Charges($)
										            </label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['total_charges']!==0 ? App\HTTP\Helpers\Helpers::priceFormat($praStats['total_charges']): '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Payments($)</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['total_payments']!==0 ? App\HTTP\Helpers\Helpers::priceFormat($praStats['total_payments']): '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Patient Payments / Insurance Payments</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)?@$praStats['patient_payments'].' / '.@$praStats['insurance_payments']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Adjustments($)</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['total_adjustment']!==0 ? App\HTTP\Helpers\Helpers::priceFormat($praStats['total_adjustment']): '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Denials</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['total_denial']!==0 ?$praStats['total_denial']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										      </div>
										      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal med-left-border print-m-t-m-5">
										      	<div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Rejections($)</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['total_rejections']!==0 ?$praStats['total_rejections']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Phone" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Submitted claims</label> 
										             <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['total_submitted_claims']!==0 ?$praStats['total_submitted_claims']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Fax" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Frequently Generated reports</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['frequently_generated_reports']!==0 ?$praStats['frequently_generated_reports']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>                                    
										        </div> 

										        <div class="form-group">
										            <label for="Email" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Total Reports Generated</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['total_reports_generated']!==0 ?$praStats['total_reports_generated']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>                                    
										        </div> 

										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Templates sent</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['templates_sent']!==0 ?$praStats['templates_sent']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Patient Intake Usage</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['patient_intake_usage']!==0 ?$praStats['patient_intake_usage']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Charge Capture Usage</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['charge_capture_usage']!==0 ?$praStats['charge_capture_usage']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Users</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['users']!==0 ?$praStats['users']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Providers</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['providers']!==0 ?$praStats['providers']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Address/NPI -API</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['address_npi_api']!==0 ?$praStats['address_npi_api']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Appointments</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['appointments']!==0 ?$praStats['appointments']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										        <div class="form-group">
										            <label for="Website" class="col-lg-6 col-md-4 col-sm-3 col-xs-12 control-label">Error Log Count</label> 
										            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-10">
										                <?php echo($praStats)&&$praStats['error_log_count']!==0 ?$praStats['error_log_count']: '<span class=" nill "> - Nil - </span>' ?>
										            </div>
										        </div>
										      </div>
									   </div>                         
                                    </div>
                                </div>
                            </div><!-- /.box Ends-->
                        </div>
                        @endforeach
                    </div>    
                </div>
            </div>
        </div><!-- /.box -->
        </div>
        </div>
    </div><!-- /.box -->
</div>
@stop

@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function(){
		$('.fa-plus').on('click',function(){
			$('.practice_details').slideDown('slow');
		});
	});
</script>
@endpush