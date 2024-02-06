@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.ticket')}} font14"></i> Users Activity <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Patients List</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('usersactivity')}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('practice/usersactivity/tabs')
@stop 

@section('practice')
<div class="col-lg-12">
    @if(Session::get('message')!== null)
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!-- Inner Content for full width Starts -->
    <div class="col-xs-12">
        <div class="box box-info no-shadow">
            <div class="box-header margin-b-10">
                <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Patients List</h3>
                <div class="box-tools pull-right margin-t-2">
              
                </div>
            </div><!-- /.box-header -->
             <div class="box-body table-responsive">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box-body table-responsive">
                <div style="border: 1px solid #008E97;border-radius: 4px;">
                <div class="box-header med-bg-green no-padding" style="border-radius: 4px 4px 0px 0px;">
                    <div class="col-lg-3 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Patient Name</h3>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Columns</h3>
                    </div>  

                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">User</h3>
                    </div>
                    <div class="col-lg-3 col-md-1 col-sm-2 col-xs-3" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-0 med-white">Changed Date</h3>
                    </div> 
                    <div class="col-lg-2 col-md-1 col-sm-2 col-xs-3" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-0 med-white">Table Name</h3>
                    </div>                 
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-5"><!--  Left side Content Starts -->  
                        <?php
						$patients_fields = array('last_name'=>'Patient Last Name','middle_name'=>'Patient  MI','first_name'=>'Patient First Name','title'=>'Title','address1'=>'Address 1','address2'=>'Address 2','city'=>'City','state'=>'State','zip5'=>'Zip5','zip4'=>'Zip4','gender'=>'Gender','ssn'=>'SSN','dob'=>'DOB','phone'=>'Phone','mobile'=>'Mobile','email'=>'Email','employment_status'=>'Employment Status','marital_status'=>'Marital Status','statements'=>'Statements','bill_cycle'=>'Bill Cycle','deceased_date'=>'Deceased Date','medical_chart_no'=>'Medical Chart No.','eligibility_verification'=>'Eligibility Verification Status','status'=>'Status','driver_license'=>'Driving License','provider_id'=>'PCP','facility_id'=>'Primary Facility','stmt_category'=>'Statement Category','Statements'=>'statements','hold_reason'=>'Hold Reason','hold_release_date'=>'Hold Release Date', 'referring_provider_id' => 'Referring Provider');
                        $pat_contacts_fields = array('category'=>'Contact Category','guarantor_last_name'=>'Guarantor Last Name','guarantor_middle_name'=>'Guarantor Middle Name','guarantor_first_name'=>'Guarantor First Name','guarantor_relationship'=>'Guarantor Relationship','guarantor_home_phone'=>'Guarantor Home Phone','guarantor_cell_phone'=>'Guarantor Cell Phone','guarantor_email'=>'Guarantor Email','guarantor_address1'=>'Guarantor Address1','guarantor_address2'=>'Guarantor Address2','guarantor_city'=>'Guarantor City','guarantor_state'=>'Guarantor State','guarantor_zip5'=>'Guarantor Zip5','guarantor_zip4'=>'Guarantor Zip4','emergency_last_name'=>'Emergency Last Name','emergency_middle_name'=>'Emergency Middle Name','emergency_first_name'=>'Emergency First Name','emergency_relationship'=>'Emergency Relationship','emergency_home_phone'=>'Emergency Home Phone','emergency_cell_phone'=>'Emergency Cell Phone','emergency_email'=>'Emergency Email','emergency_address1'=>'Emergency Address1','emergency_address2'=>'Emergency Address2','emergency_city'=>'Emergency City','emergency_state'=>'Emergency State','emergency_zip5'=>'Emergency Zip5','emergency_zip4'=>'Emergency Zip4','employer_status'=>'Employment Status','employer_organization_name'=>'Oganization Name','employer_occupation'=>'Occupation','employer_name'=>'Employer Name','employer_work_phone'=>'Employer Work Phone','employer_phone_ext'=>'Employer Phone','employer_address1'=>'Employer Address1','employer_address2'=>'Employer Address2','employer_city'=>'Employer City','employer_state'=>'Employer State','employer_zip5'=>'Employer Zip5','employer_zip4'=>'Employer Zip4','attorney_adjuster_name' => 'Attorney Adjuster Name','attorney_doi' => 'Attorney DOI','attorney_claim_num' => 'Attorney Claim Number','attorney_work_phone' => 'Attorney Work Phone','attorney_phone_ext' => 'Attorney Phone','attorney_fax' => 'Attorney Fax','attorney_email' => 'Attorney Rmail',		'attorney_address1'=>'Attorney Address1','attorney_address2'=>'Attorney Address2',	'attorney_city'=>'Attorney City','attorney_state'=>'Attorney State','attorney_zip5'=>'Attorney Zip5','attorney_zip4'=>'Attorney Zip4','deleted_at'=>'deleted_at'); 
                        $pat_insurance_fields = array('insurance_id'=>'Insurance Name','medical_secondary_code'=>'Secondary Insurace MSP','category'=>'Insurance Category','relationship'=>'Insured Relationship','insured_phone'=>'Insured Phone','insured_gender'=>'Insured Gender','last_name'=>'Insured Lasts Name','first_name'=>'Insured First Name','middle_name'=>'Insured Middle Name','insured_ssn'=>'Insured SSN','insured_dob'=>'Insured DOB','insured_address1'=>'Insured Address 1','insured_address2'=>'Insured Address 2','insured_city'=>'Insured City','insured_state'=>'Insured State','insured_zip5'=>'Insured Zip5','insured_zip4'=>'Insured Zip4','policy_id'=>'Insured Policy ID','group_name'=>'Group Name / ID','effective_date'=>'Effective Date','termination_date'=>'Termination Date','orderby_category'=>'Category ID (Automated)','document_save_id'=>'Document Attachment ID','eligibility_verification'=>'Eligibility','same_patient_address'=>'Same Address','active_from'=>'Insurance Active From','active_to'=>'Insurance Active To','deleted_at'=>'deleted_at'); 
                        $pat_auth_fields = array('authorization_no'=>'Auth No.','requested_date'=>'Requested Date','authorization_contact_person'=>'Authorization Contact Person','alert_appointment'=>'Alert Appointment','allowed_visit'=>'Allowed Visit','insurance_id'=>'Insurance Name','pos_id'=>'POS','start_date'=>'Start Date','end_date'=>'End Date','authorization_phone'=>'Auth Phone','authorization_phone_ext'=>'Auth Phone Ext','alert_billing'=>'Alert Billing','allowed_amt'=>'Allowed Amt','amt_used'=>'Used Amt','amt_remaining'=>'Remaining Amt','alert_amt'=>'Alert Amt','authorization_notes'=>'Auth Notes','deleted_at'=>'deleted_at');
						$pat_other_addr_fields = array('address1'=>'Other Address1','address2'=>'Other Address2','city'=>'City','state'=>'State','zip5'=>'Zip5','zip4'=>'Zip4');
                        $ProvidersArr = App\Http\Helpers\Helpers::getProviderlist(); 
                        $InsuranceArr = App\Http\Helpers\Helpers::getInsuranceFullNameLists();                         //echo $InsuranceArr[248];
                        ?>          
                        @foreach(@$patientslog as $logdata)
                        <?php                   
							$str_arr = preg_split ("/\,/",  $logdata->changed_column);  
							$old_valueArr = preg_split ("/\,/",  $logdata->old_value);  
							$new_valueArr = preg_split ("/\,/",  $logdata->new_value);  
							foreach($str_arr as $key => $value){          
							if(empty($value))
								unset($str_arr[$key]);
							}
                        ?>
                        <div class="box collapsed-box" style="box-shadow:none;margin:0px;"><!--  Box Starts -->
                            <div class="box-header-view-white no-padding" style="color: #fff;border-bottom: 1px solid #CDF7FC;">
                                <div class="col-lg-3 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <h3 class="box-title font12 font-normal">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i></button>
									</h3>
									<?php
										$logdata->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($logdata->id,'encode'); 
									?>
									<span class="med-green">{{ App\Http\Helpers\Helpers::getNameformat(@$logdata->patient_details->last_name,@$logdata->patient_details->first_name,'')}}</span>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;"><?php echo count($str_arr);?></span>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">@if(isset($logdata->users_details)){{ $logdata->users_details->short_name}} - {{$logdata->users_details->name}}@else -Nil-@endif</span>
                                </div>
                                <div class="col-lg-3 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{ App\Http\Helpers\Helpers::dateFormat(@$logdata->created_at,'datetime') }}</span>
                                </div> 
                               <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{ $logdata->table_name}}</span>
                                </div>

                            </div>
                            <div class="box-body form-horizontal">
                                <div class="col-md-12 col-sm-12 col-xs-12 no-padding border-radius-4 yes-border border-b4f7f7">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                        <span class="med-orange margin-l-10 font13 font600 padding-0-4 bg-white">Charges Log Details</span>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10" >
                                        <table class="table margin-t-5 margin-b-10 no-sm-bottom">
                                            <thead>
                                                   <tr>
                                                       <th style="border-right: 1px solid #fff;" class="col-lg-2 col-md-1 col-sm-1 col-xs-2">Columns</th>
                                                       <th style="border-right: 1px solid #fff;" class="col-lg-2 col-md-1 col-sm-1 col-xs-2">Old Value</th>
                                                       <th style="border-right: 1px solid #fff;" class="col-lg-2 col-md-1 col-sm-1 col-xs-2">New Value</th>
                                                   </tr>
                                               </thead>
                                               <tbody>
												<?php 
													try {
												?>
                                                @foreach(@$str_arr as $key=>$list)
                                                   @if($list != '') 
                                                    <tr class="clsCursor">
                                                        @if(isset($pat_insurance_fields[trim($list)]) && $logdata->table_name == 'patient_insurance')
															<td>@if($pat_insurance_fields[trim($list)] != 'deleted_at'){{$pat_insurance_fields[trim($list)]}}@else {{$old_valueArr[$key]}} @endif</td>                                        
                                                        @elseif(isset($pat_auth_fields[trim($list)]) && $logdata->table_name == 'patient_authorizations')
															<td>@if($pat_auth_fields[trim($list)] != 'deleted_at') {{ @$pat_auth_fields[trim($list)]}} @else {{ @$old_valueArr[$key] }} @endif</td>     
                                                        @elseif(isset($pat_contacts_fields[trim($list)]) && $logdata->table_name == 'patient_contacts')
															<td>@if($pat_contacts_fields[trim($list)] != 'deleted_at'){{@$pat_contacts_fields[trim($list)]}}@else {{@$old_valueArr[$key]}} @endif</td>  
                                                        @elseif(isset($patients_fields[trim($list)])  && $logdata->table_name == 'patients')
															<td>{{@$patients_fields[trim($list)]}}</td>
														@elseif(isset($patients_fields[trim($list)])  && $logdata->table_name == 'patient_other_address')
															<td>{{@$pat_other_addr_fields[trim($list)]}}</td>
                                                        @else
															<td>-Nil-</td>
                                                        @endif
                                                        <?php
															if(trim($list) == 'insurance_id'){     
															   $old_valueArr[$key] = (isset($InsuranceArr[$old_valueArr[$key]])) ? @$InsuranceArr[$old_valueArr[$key]] :'-Nil-';
															   $new_valueArr[$key] = (isset($InsuranceArr[$new_valueArr[$key]])) ? @$InsuranceArr[$new_valueArr[$key]] :'-Nil-';    
															}elseif((trim($list) == 'provider_id') || (trim($list) == 'facility_id') || trim($list) == 'referring_provider_id'){
																 $old_valueArr[$key] = (isset($ProvidersArr[$old_valueArr[$key]])) ? @$ProvidersArr[$old_valueArr[$key]] :'-Nil-';
																 $new_valueArr[$key] = (isset($ProvidersArr[trim($new_valueArr[$key])])) ? @$ProvidersArr[trim($new_valueArr[$key])] :'-Nil-'; 
															}elseif(trim($list) == 'start_date' || (trim($list) == 'end_date') || (trim($list) == 'active_from')  || (trim($list) == 'active_to') || (trim($list) == 'deceased_date') || (trim($list) == 'effective_date') || (trim($list) == 'termination_date') || (trim($list) == 'hold_release_date')) {
																 $old_valueArr[$key] = (isset($old_valueArr[$key]) && $old_valueArr[$key] != '0000-00-00' && trim($old_valueArr[$key]) != '0000-00-00 00:00:00'&& $old_valueArr[$key] != '') ? App\Http\Helpers\Helpers::dateFormat(@$old_valueArr[$key],'date') :'-Nil-';
																 $new_valueArr[$key] = ($new_valueArr[$key] != '0000-00-00' && $new_valueArr[$key] != '') ? App\Http\Helpers\Helpers::dateFormat(@$new_valueArr[$key],'date') : '-Nil-';
															}elseif(trim($list) == 'dob' ){
																$old_valueArr[$key] = App\Http\Helpers\Helpers::dateFormat(@$old_valueArr[$key],'dob');
																if($old_valueArr[$key] != '') {
																	$old_valueArr[$key] .= " (Age: ".date_diff(date_create(@$old_valueArr[$key]), date_create('today'))->y.")";
																}
																
																$new_valueArr[$key] = App\Http\Helpers\Helpers::dateFormat(@$new_valueArr[$key],'dob');
																if($new_valueArr[$key] != '') {
																	$new_valueArr[$key] .= " (Age: ".date_diff(date_create(@$new_valueArr[$key]), date_create('today'))->y.")";
																}
															}elseif(trim($list) == 'deleted_at' && $logdata->table_name == 'patient_contacts' ){
																 $old_valueArr[$key] = @$new_valueArr[$key];
																 $new_valueArr[$key] = "Deleted";  
															}elseif(trim($list) == 'deleted_at' && $logdata->table_name == 'patient_insurance' ){
																 $old_valueArr[$key] = App\Http\Helpers\Helpers::getInsuranceName(@$new_valueArr[$key]);
																 $new_valueArr[$key] = "Deleted";  
															}elseif(trim($list) == 'deleted_at' && $logdata->table_name == 'patient_authorizations' ){
																$old_valueArr[$key] = @$new_valueArr[$key];
																$new_valueArr[$key] = "Deleted";  
															}else{
																$old_valueArr[$key] = (isset($old_valueArr[$key]) && $old_valueArr[$key] != '') ? @$old_valueArr[$key] : '-Nil-';
																$new_valueArr[$key] = (isset($new_valueArr[$key]) && $new_valueArr[$key] != '') ? $new_valueArr[$key] : '-Nil-';
															}
                                                        ?>
                                                        <td>{{ @$old_valueArr[$key]}}</td>
                                                        <td>{{ @$new_valueArr[$key]}}</td>
                                                    </tr>
                                                   @endif
                                               @endforeach
											   <?php
													} catch(Exception $e) {
														// $e->getMessage();
													}
											   ?>
                                               </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div><!-- /.box Ends-->
                        </div>
                        @endforeach
                    </div>    
                </div>
            </div>
        </div><!-- /.box -->
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding dataTables_info">
                        Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
                </div>
            </div>
        </div>
        </div><!-- /.box -->
    </div>
</div><!-- Inner Content for full width Ends -->
<!--End-->
@stop