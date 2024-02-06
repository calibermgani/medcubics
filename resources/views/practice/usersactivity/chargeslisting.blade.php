@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.ticket')}} font14"></i> Users Activity <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Charges List</span></small>
        </h1>
        <ol class="breadcrumb">
           <!--  <li><a href="javascript:void(0)" data-url="{{ url('practice/usersactivity/chargeslog')}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> -->
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
                <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Charges List</h3>
                <div class="box-tools pull-right margin-t-2">                
                </div>
            </div><!-- /.box-header -->
             <div class="box-body table-responsive">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box-body table-responsive">
                <div style="border: 1px solid #008E97;border-radius: 4px;">
                <div class="box-header med-bg-green no-padding" style="border-radius: 4px 4px 0px 0px;">
                    <div class="col-lg-3 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Charge Number</h3>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-1 hidden-xs" style="border-right: 1px solid #fff;">
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
                        $claims_fields = array('claim_number'=>'Claim No.','patient_id'=>'Patient Name','date_of_service'=>'DOS','icd_codes'=>'ICD 1 to ICD 12','primary_cpt_code'=>'CPT','rendering_provider_id'=>'Rendering Provider','refering_provider_id'=>'Referring Provider','billing_provider_id'=>'Billing Provider','facility_id'=>'Facility Name','insurance_id'=>'Billed To','pos_id'=>'POS','self_pay'=>'Billed To Self','insurance_category'=>'Insurance Category','patient_insurance_id'=>'Insurance tagged with Policy ID','auth_no'=>'Autherization No.','doi'=>'DOI','admit_date'=>'Admission (From)','discharge_date'=>'Discharge Date','total_charge'=>'Total Charges','hold_reason_id'=>'Hold Reason','status'=>'Claim Status','sub_status_id'=>'Sub Status','reason_type'=>'Reason for Rule Engine (Billing or Coding)','claim_type'=>'Claim Type','claim_armanagement_status'=>'Claim Armanagement Status','deleted_at'=>'Deleted Date');
                        $claims_add_fields = array('is_provider_employed'=>'Provider Employed in Hospice?','is_employment'=>'Employment Status (Box 10a)','is_autoaccident'=>'Auto Accident (Box 10b)','autoaccident_state'=>'Auto Accident (Box 10b) State','is_otheraccident'=>'Other Accident (Box 10c)','otherclaimid_qual'=>'Other Claim ID (Box 11b) First Box','otherclaimid'=>'Other Claim ID (Box 11b) Second Box','provider_qualifier'=>'Provider Qual (Box 17a)','provider_otherid'=>'Provider Qual (Box 17a) Identifier','lab_charge'=>'Outside Lab Charges (Box 20)','print_signature_onfile_box12'=>'Print Signature on File (Box 12)','print_signature_onfile_box13'=>'Print Signature on File (Box 13)','illness_box14'=>'Date of LMP (Box 14)','other_date_qualifier'=>'Other Date QUAL','other_date'=>'Other Date (Box 15)','service_facility_qual'=>'Facility Qual (Box 32b)','facility_otherid'=>'Facility Other ID (Box 32b)','billing_provider_qualifier'=>'Billing Provider Qual (Box 33b)','billing_provider_otherid'=>'Billing Provider Other ID (Box 33b)','rendering_provider_qualifier'=>'Rendering provider Qual(Box 24I)','rendering_provider_otherid'=>'Rendering provider OtherId (Box 24J)','unable_to_work_from'=>'Unable to Work (Box 16) From','unable_to_work_to'=>'Unable to Work (Box 16) To','additional_claim_info'=>'Additional Claim Info (Box 19)','resubmission_code'=>'Resubmission Code (Box 22)','original_ref_no'=>'Original Reference No. (Box 22)','emergency'=>'Emergency (Box 24c)','box23_type'=>'Prior Authorization','box_23'=>'Prior Authorization (Box 23)','outside_lab'=>'Outside Lab (Box 20)','accept_assignment'=>'Accept Assignments (Box 27)','epsdt'=>'EPSDT (Box 24h)');  
                        $ProvidersArr = App\Http\Helpers\Helpers::getProviderlist(); 
                        $InsuranceArr = App\Http\Helpers\Helpers::getInsuranceFullNameLists(); 
                        //echo $InsuranceArr[248];
                        
                        ?>   
                        @if($chargeslog != '')
                        @foreach(@$chargeslog as $logdata)
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
										<button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i></button></h3>
									 <?php 
										$logdata->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($logdata->id,'encode');
									?>
                                    <span class="med-green">
										@if(isset($logdata->claim_details->claim_number))
											{{ @$logdata->claim_details->claim_number}}
										@else
											{{ @$logdata->claim_id}}
										@endif
									</span>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;"><?php echo count($str_arr);?></span>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">@if(isset($logdata->users_details) && ($logdata->users_details != '')){{ @$logdata->users_details->short_name}} - {{@$logdata->users_details->name}}@else -Nil-@endif</span>
                                </div>
                                <div class="col-lg-3 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{ App\Http\Helpers\Helpers::dateFormat(@$logdata->created_at,'datetime') }}</span>
                                </div> 
                               <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{ @$logdata->table_name}}</span>
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
                                                @foreach(@$str_arr as $key=>$list)
                                                   @if($list != '') 
                                                    <tr  class="clsCursor">   
                                                        @if(isset($claims_fields[trim($list)]))
                                                        <td>{{$claims_fields[trim($list)]}}</td>
                                                        @elseif(isset($claims_add_fields[trim($list)]))
                                                        <td>{{$claims_add_fields[trim($list)]}}</td>
                                                        @else
                                                        <td>-Nil-</td>
                                                        @endif
                                                        <?php 
														$old_valueArr[$key] = trim($old_valueArr[$key]);
														$new_valueArr[$key] = trim($new_valueArr[$key]);
                                                        if(trim($list) == 'facility_id' || (trim($list) == 'rendering_provider_id') || (trim($list) == 'billing_provider_id') || trim($list) =='refering_provider_id'){
                                                            $old_valueArr[$key] = (isset($ProvidersArr[$old_valueArr[$key]])) ? @$ProvidersArr[$old_valueArr[$key]] :'-Nil-';
                                                            $new_valueArr[$key] = (isset($ProvidersArr[$new_valueArr[$key]])) ? @$ProvidersArr[$new_valueArr[$key]] :'-Nil-';
                                                        }elseif(trim($list) == 'insurance_id' || (trim($list) == 'patient_insurance_id')){
                                                            $old_valueArr[$key] = (isset($InsuranceArr[$old_valueArr[$key]])) ? @$InsuranceArr[$old_valueArr[$key]] :'-Nil-';
                                                            $new_valueArr[$key] = (isset($new_valueArr[$old_valueArr[$key]])) ? @$new_valueArr[$old_valueArr[$key]] :'-Nil-';
                                                        }elseif(trim($list) == 'anestesia_id'){
                                                            $old_valueArr[$key] = $old_valueArr[$key];
                                                            $new_valueArr[$key] = $new_valueArr[$key];
                                                        }elseif(trim($list) == 'deleted_at'){
                                                            $old_valueArr[$key] = App\Http\Helpers\Helpers::dateFormat(@$old_valueArr[$key],'date');
                                                            $new_valueArr[$key] = App\Http\Helpers\Helpers::dateFormat(@$new_valueArr[$key],'date');
                                                        }elseif(trim($list) == 'hold_reason_id'){
                                                            $old_valueArr[$key] = $old_valueArr[$key]; 
                                                            $new_valueArr[$key] = $new_valueArr[$key];
                                                        }else{
                                                            $old_valueArr[$key] = $old_valueArr[$key];
                                                            $new_valueArr[$key] = $new_valueArr[$key];
                                                        }
                                                        ?>
                                                        <td>{{$old_valueArr[$key]}}</td>
                                                        <td>{{$new_valueArr[$key]}}</td>
                                                    </tr>
                                                   @endif
                                               @endforeach
                                               </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div><!-- /.box Ends-->
                        </div>
                        @endforeach
                        @else
                        <div class="box collapsed-box" style="box-shadow:none;margin:0px;"><!--  Box Starts -->
                             <div class="box-header-view-white no-padding" style="color: #fff;border-bottom: 1px solid #CDF7FC;">
                                <div class="col-lg-3 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
x`x`
                                    <a href="#"><span class="med-green"></span>No Records Found</a>
                                </div>

                            </div>
                        </div>
                        @endif
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