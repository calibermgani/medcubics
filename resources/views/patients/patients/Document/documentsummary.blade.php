@extends('admin')

@section('toolbar')

<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Documents Summary </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($patients->id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> 

            <?php $uniquepatientid = $patients->id; $id = $patients->id; ?>	
           <?php /* @include ('patients/layouts/patientstatement_icon') */?>
            @include ('patients/layouts/swith_patien_icon')

<!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

            <li><a href="#js-help-modal" data-url="{{url('help/patients')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patients->id,'needdecode'=>'yes'])
@include ('patients/patients/Document/tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box-info no-shadow margin-t-m-10">
        <div class="box-body form-horizontal  padding-4">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-13">
                    <span class=" med-orange margin-l-10 font13 padding-0-4 font600">&emsp;</span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive margin-b-5 no-padding">
                        <table class="popup-table-wo-border table margin-b-1">                    
                            <tbody>
                                <tr>
                                    <td class="font600" style="width:50%">Total Documents</td>
                                    <td><span class="font600">{{ $total_document_count }}</span></td> 
                                </tr>                            
                                <tr>
                                    <td class="font600">Assigned</td>
                                    <td><span class="font600">{{ $assigned_document_count }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive tab-l-b-1 p-l-0  md-display tabs-lightgreen-border">
                        <table class="popup-table-wo-border table margin-b-1">                    
                            <tbody>
                                <tr>
                                    <td class="font600">Total In Process </td>
                                    <td><span class="font600">{{ $inprocess_document_count }}</span></td>
                                </tr>  
                                <tr>
                                    <td class="font600">Total Review</td>
                                    <td class="font600">{{ $review_document_count }}</td>                                              
                                </tr>
                            </tbody>
                        </table>
                    </div>                                
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive p-l-0 tab-l-b-1  md-display tabs-lightgreen-border">
                        <table class="popup-table-wo-border table margin-b-1">                    
                            <tbody>
                                <tr>                                               
                                    <td class="font600">Total Pending</td>
                                    <td class="med-orange font600">{{ $pending_document_count }} </td>
                                </tr>
                                <tr>
                                    <td class="font600">Total Completed</td>
                                    <td class="font600">{{ $completed_document_count }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>                                
                </div>
            </div>
        </div>
    </div>
</div>

<div id="show_assigned_msg" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden-print">
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
    <div class="box box-info no-shadow">
        <div class="box-header">
            <i class="fa fa-bars font14"></i><h3 class="box-title">List</h3>
            <div class="box-tools pull-right margin-t-2">
                <a class="font600 font14 js-new-dcoument" href="#create_document" data-url="" data-toggle="modal" accesskey="m"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Docu<span class="text-underline">m</span>ent</a>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden-print ">
            @if(Session::get('message')!== null) 
            <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
            @endif
        </div>
        <div class="box-body monitor-scroll">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding table-responsive ">    
                @if(count($assigned_document) > 0)
                <table id="" class="table table-bordered table-striped table-responsive">
                    <thead>
                        <tr>
                            <th style="border-bottom-width: 0px !important;" colspan="2" >Created Info</th>
                            <th style="border-bottom-width: 0px !important;" colspan="5">Document Info</th>
                            <th style="border-bottom-width: 0px !important;" colspan="2"></th>
                            <th style="border-bottom-width: 0px !important;" colspan="4">Check / EFT Info</th>
                            <th style="border-bottom-width: 0px !important;" colspan="4">Assigned Info</th>
                            <th style="border-bottom-width: 0px !important;" class="td-c-6"></th>
                        </tr>
                        <tr>
                            <!-- Created Info -->
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Date</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                            <!-- Document Info -->
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Category</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Sub Category</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Title</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Pages</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">File Type</th>
                            <!-- Other Info -->
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Patient</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Claim No</th>
                            <!-- Check / EFT Info -->
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Payer</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check No</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check Date</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check Amount</th>
                            <!-- Assigned Info -->
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Follow up</th>
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Status</th>                                            
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                            <!-- Actions -->
                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assigned_document as $list)
                        <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                            <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                            <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                            <td>{{ @$list->document_categories->category_value }}</td>
                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                            <td>{{ $list->page }}</td>
                            <td>{{ $list->document_extension }}</td>

                            <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                            <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                            <td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
                            <td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
                            <td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
                            <td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
                            <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                            <td>
                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                @if(date("m/d/y") == $fllowup_date)
                                <span class="med-orange">{{$fllowup_date}}</span>
                                @elseif(date("m/d/y") >= $fllowup_date)
                                <span class="med-red">{{$fllowup_date}}</span>
                                @else
                                <span class="med-gray">{{$fllowup_date}}</span>
                                @endif
                            </td>
                            <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>                        
                            <td>
                                <span class="{{@$list->document_followup->priority}}">
                                    @if(@$list->document_followup->priority == 'High')
                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                    @elseif(@$list->document_followup->priority == 'Low')
                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                    @endif							
                                </span>
                            </td>                        
                            <td class="">
                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span>|	

                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
        						@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
        							|
                                <span class="document-delete">
                                    <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                </span>	
        						@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                No Assigned Document Found
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="accordion">
                    <!-- Patient Document Accordion Group -->
                    <div class="accordion-group">
                        <div class="accordion-heading js_accordion_header">
                            <a class="accordion-toggle">
                                <h4 class="@if(count($patient_document) == 0) med-gray  @endif margin-t-0 no-bottom font16"><i class="fa fa-plus-circle margin-r-10"></i> Patient Documents : <span class="normal-font font13 med-darkgray"><span class="font-italic">Registration Documents, Insurance Card Copy, Driving License, Consent Forms...</span></span><span class="text-right pull-right font600 normal-font font14 margin-r-10 js-count">{{ count($patient_document) }}</span></h4>
                            </a>
                        </div> 
                        <div class="accordion-body js_accordion_content collapse">
                            <div class="accordion-inner margin-t-m-8"> 
                                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">
                                    <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                                       class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>
                                </div> 
                                <div class="table-responsive">    
                                    @if(count($patient_document) > 0)
                                    <table id="patient-doc" class="table table-separate no-bottom popup-table-wo-border">
                                        <thead>
                                            <tr>
                                                <th class="med-green" style="background: #e7f5f4">Created On</th> 
                                                <th class="med-green" style="background: #e7f5f4">User</th> 
                                                <th class="med-green" style="background: #e7f5f4">Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Sub Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Title</th>           
                                                <th class="med-green" style="background: #e7f5f4">Patient Name</th>           
                                                <th class="med-green" style="background: #e7f5f4">Claim No</th>
                                                <th class="med-green" style="background: #e7f5f4">Assigned To</th>
                                                <th class="med-green" style="background: #e7f5f4">Follow up Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Status</th>
                                                <th class="med-green" style="background: #e7f5f4">Pages</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>
                                                <th class="med-green" style="background: #e7f5f4">File Type</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($patient_document as $list)
                                            <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                                                <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                                <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                                                <td>{{ @$list->document_categories->category_value }}</td>
                                                <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                                <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                                <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                                <td>
                                                    <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                    @if(date("m/d/y") == $fllowup_date)
                                                    <span class="med-orange">{{$fllowup_date}}</span>
                                                    @elseif(date("m/d/y") >= $fllowup_date)
                                                    <span class="med-red">{{$fllowup_date}}</span>
                                                    @else
                                                    <span class="med-gray">{{$fllowup_date}}</span>
                                                    @endif
                                                </td>
                                                <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                                <td>{{ $list->page }}</td>
                                                <td>
                                                    <span class="{{@$list->document_followup->priority}}">
                                                        @if(@$list->document_followup->priority == 'High')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Low')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Moderate')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                        @endif							
                                                    </span>
                                                </td>
                                                <td> 
                                                    <?php
                                                    $file_type = explode('.', $list->filename);
                                                    $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
                                                    ?>{{ $file_type[1] }}</td>


                                                <td>
                                                    <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download">| </i></a></span>	

                                                    <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"> </i></a></span>
													@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
														|
                                                    <span class="document-delete">
                                                        <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                                    </span>	
													@endif

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    No Document Found
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Eligibility Document Accordion Group -->
                    <div class="accordion-group">
                        <div class="accordion-heading js_accordion_header">
                            <a class="accordion-toggle">
                                <h4 class="@if(count($eligibility_document) == 0) med-gray  @endif margin-t-0 no-bottom font16"><i class="fa fa-plus-circle margin-r-10"></i> Eligibility & Benefits : <span class="normal-font font13 med-darkgray"><span class="font-italic">Payer Eligibility Reports, Benefit Verification Forms...</span></span><span class="text-right pull-right font600 normal-font font14 margin-r-10 js-count"> {{ count($eligibility_document) }}</span></h4>
                            </a>
                        </div> 
                        <div class="accordion-body js_accordion_content collapse">
                            <div class="accordion-inner margin-t-m-8"> 
                                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">                                       

                                    <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                                       class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>


                                </div> 
                                <div class="table-responsive">                                
                                    @if(count($eligibility_document) > 0)
                                    <table id="eligibility-doc" class="table table-separate no-bottom popup-table-wo-border">
                                        <thead>
                                            <tr>
                                                <th class="med-green" style="background: #e7f5f4">Created On</th> 
                                                <th class="med-green" style="background: #e7f5f4">User</th> 
                                                <th class="med-green" style="background: #e7f5f4">Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Sub Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Title</th>           
                                                <th class="med-green" style="background: #e7f5f4">Patient Name</th>           
                                                <th class="med-green" style="background: #e7f5f4">Claim No</th>
                                                <th class="med-green" style="background: #e7f5f4">Assigned To</th>
                                                <th class="med-green" style="background: #e7f5f4">Follow up Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Status</th>
                                                <th class="med-green" style="background: #e7f5f4">Pages</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>
                                                <th class="med-green" style="background: #e7f5f4">File Type</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($eligibility_document as $list)
                                            <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                                                <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                                <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                                                <td>{{ @$list->document_categories->category_value }}</td>
                                                <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                                <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                                <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                                <td>
                                                    <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                    @if(date("m/d/y") == $fllowup_date)
                                                    <span class="med-orange">{{$fllowup_date}}</span>
                                                    @elseif(date("m/d/y") >= $fllowup_date)
                                                    <span class="med-red">{{$fllowup_date}}</span>
                                                    @else
                                                    <span class="med-gray">{{$fllowup_date}}</span>
                                                    @endif
                                                </td>
                                                <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                                <td>{{ $list->page }}</td>
                                                <td>
                                                    <span class="{{@$list->document_followup->priority}}">
                                                        @if(@$list->document_followup->priority == 'High')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Low')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Moderate')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                        @endif							
                                                    </span>
                                                </td>
                                                <td> 
                                                    <?php
                                                    $file_type = explode('.', $list->filename);
                                                    $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
                                                    ?>{{ $file_type[1] }}</td>
                                                <td class="">

                                                    <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download">| </i></a></span>	

                                                    <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"> </i></a></span>
													@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
														|
                                                    <span class="document-delete">
                                                        <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                                    </span>	
													@endif

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    No Document Found
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Authorization Document Accordion Group -->
                    <div class="accordion-group">
                        <div class="accordion-heading js_accordion_header">
                            <a class="accordion-toggle">
                                <h4 class="@if(count($authorization_document) == 0) med-gray  @endif margin-t-0 no-bottom font16"><i class="fa fa-plus-circle margin-r-10"></i> Authorization Forms : <span class="normal-font font13 med-darkgray"><span class="font-italic">Authorization Forms, Referral Forms...</span></span><span class="text-right pull-right font600 normal-font font14 margin-r-10 js-count">{{ count($authorization_document) }}</span></h4>
                            </a>
                        </div> 
                        <div class="accordion-body js_accordion_content collapse">
                            <div class="accordion-inner margin-t-m-8"> 
                                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">                                       

                                    <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                                       class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>


                                </div> 
                                <div class="table-responsive">                                
                                    @if(count($authorization_document) > 0)
                                    <table id="auth-table" class="table table-separate no-bottom popup-table-wo-border">
                                        <thead>
                                            <tr>
                                                <th class="med-green" style="background: #e7f5f4">Created On</th> 
                                                <th class="med-green" style="background: #e7f5f4">User</th> 
                                                <th class="med-green" style="background: #e7f5f4">Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Sub Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Title</th>           
                                                <th class="med-green" style="background: #e7f5f4">Patient Name</th>           
                                                <th class="med-green" style="background: #e7f5f4">Claim No</th>
                                                <th class="med-green" style="background: #e7f5f4">Assigned To</th>
                                                <th class="med-green" style="background: #e7f5f4">Follow up Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Status</th>
                                                <th class="med-green" style="background: #e7f5f4">Pages</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>
                                                <th class="med-green" style="background: #e7f5f4">File Type</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($authorization_document as $list)
                                            <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                                                <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                                <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                                                <td>{{ @$list->document_categories->category_value }}</td>
                                                <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                                <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                                <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                                <td>
                                                    <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                    @if(date("m/d/y") == $fllowup_date)
                                                    <span class="med-orange">{{$fllowup_date}}</span>
                                                    @elseif(date("m/d/y") >= $fllowup_date)
                                                    <span class="med-red">{{$fllowup_date}}</span>
                                                    @else
                                                    <span class="med-gray">{{$fllowup_date}}</span>
                                                    @endif
                                                </td>
                                                <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                                <td>{{ $list->page }}</td>
                                                <td>
                                                    <span class="{{@$list->document_followup->priority}}">
                                                        @if(@$list->document_followup->priority == 'High')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Low')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Moderate')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                        @endif							
                                                    </span>
                                                </td>
                                                <td> 
                                                    <?php
                                                    $file_type = explode('.', $list->filename);
                                                    $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
                                                    ?>{{ $file_type[1] }}</td>
                                                <td class="">

                                                    <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download">| </i></a></span>	

                                                    <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"> </i></a></span>
													@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
														|
                                                    <span class="document-delete">
                                                        <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                                    </span>	
													@endif

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    No Document Found
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Procedure Document Accordion Group -->
                    <div class="accordion-group">
                        <div class="accordion-heading js_accordion_header">
                            <a class="accordion-toggle">
                                <h4 class="@if(count($procedure_document) == 0) med-gray  @endif margin-t-0 no-bottom font16"><i class="fa fa-plus-circle margin-r-10"></i> Procedure Documents : <span class="normal-font font13 med-darkgray"><span class="font-italic">Superbills, Surgery Reports, Procedure Reports, Medical Records...</span></span><span class="text-right pull-right font600 normal-font font14 margin-r-10 js-count">{{ count($procedure_document) }}</span></h4>
                            </a>
                        </div> 
                        <div class="accordion-body js_accordion_content collapse">
                            <div class="accordion-inner margin-t-m-8"> 
                                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">                                       

                                    <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                                       class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>


                                </div> 

                                <div class="table-responsive">                                
                                    @if(count($procedure_document) > 0)
                                    <table id="proc-doc" class="table table-separate no-bottom popup-table-wo-border">
                                        <thead>
                                            <tr>
                                                <th class="med-green" style="background: #e7f5f4">Created On</th> 
                                                <th class="med-green" style="background: #e7f5f4">User</th> 
                                                <th class="med-green" style="background: #e7f5f4">Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Sub Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Title</th>           
                                                <th class="med-green" style="background: #e7f5f4">Patient Name</th>           
                                                <th class="med-green" style="background: #e7f5f4">Claim No</th>
                                                <th class="med-green" style="background: #e7f5f4">Assigned To</th>
                                                <th class="med-green" style="background: #e7f5f4">Follow up Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Status</th>
                                                <th class="med-green" style="background: #e7f5f4">Pages</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>
                                                <th class="med-green" style="background: #e7f5f4">File Type</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($procedure_document as $list)
                                            <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                                                <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                                <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                                                <td>{{ @$list->document_categories->category_value }}</td>
                                                <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                                <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                                <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                                <td>
                                                    <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                    @if(date("m/d/y") == $fllowup_date)
                                                    <span class="med-orange">{{$fllowup_date}}</span>
                                                    @elseif(date("m/d/y") >= $fllowup_date)
                                                    <span class="med-red">{{$fllowup_date}}</span>
                                                    @else
                                                    <span class="med-gray">{{$fllowup_date}}</span>
                                                    @endif
                                                </td>
                                                <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                                <td>{{ $list->page }}</td>
                                                <td>
                                                    <span class="{{@$list->document_followup->priority}}">
                                                        @if(@$list->document_followup->priority == 'High')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Low')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Moderate')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                        @endif							
                                                    </span>
                                                </td>
                                                <td> 
                                                    <?php
                                                    $file_type = explode('.', $list->filename);
                                                    $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
                                                    ?>{{ $file_type[1] }}</td>
                                                <td class="">

                                                    <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download">| </i></a></span>	

                                                    <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"> </i></a></span>
													@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
														|
                                                    <span class="document-delete">
                                                        <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                                    </span>
													@endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    No Document Found
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- EDI Document Accordion Group -->					
                    <div class="accordion-group">
                        <div class="accordion-heading js_accordion_header">
                            <a class="accordion-toggle">
                                <h4 class="@if(count($edi_document) == 0) med-gray  @endif margin-t-0 no-bottom font16"><i class="fa fa-plus-circle margin-r-10"></i> EDI Reports : <span class="normal-font font13 med-darkgray"><span class="font-italic">Clearinghouse Reports, Payer Acknowledgments, Rejections...</span></span><span class="text-right pull-right font600 normal-font font14 margin-r-10 js-count">{{ count($edi_document) }}</span></h4>
                            </a>
                        </div> 
                        <div class="accordion-body js_accordion_content collapse">
                            <div class="accordion-inner margin-t-m-8"> 
                                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">                                       

                                    <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                                       class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>


                                </div> 
                                <div class="table-responsive">                                
                                    @if(count($edi_document) > 0)
                                    <table id="edi-doc" class="table table-separate no-bottom popup-table-wo-border">
                                        <thead>
                                            <tr>
                                                <th class="med-green" style="background: #e7f5f4">Created On</th> 
                                                <th class="med-green" style="background: #e7f5f4">User</th> 
                                                <th class="med-green" style="background: #e7f5f4">Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Sub Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Title</th>           
                                                <th class="med-green" style="background: #e7f5f4">Patient Name</th>           
                                                <th class="med-green" style="background: #e7f5f4">Claim No</th>
                                                <th class="med-green" style="background: #e7f5f4">Assigned To</th>
                                                <th class="med-green" style="background: #e7f5f4">Follow up Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Status</th>
                                                <th class="med-green" style="background: #e7f5f4">Pages</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>
                                                <th class="med-green" style="background: #e7f5f4">File Type</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($edi_document as $list)
                                            <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                                                <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                                <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                                                <td>{{ @$list->document_categories->category_value }}</td>
                                                <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                                <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                                <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                                <td>
                                                    <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                    @if(date("m/d/y") == $fllowup_date)
                                                    <span class="med-orange">{{$fllowup_date}}</span>
                                                    @elseif(date("m/d/y") >= $fllowup_date)
                                                    <span class="med-red">{{$fllowup_date}}</span>
                                                    @else
                                                    <span class="med-gray">{{$fllowup_date}}</span>
                                                    @endif
                                                </td>
                                                <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                                <td>{{ $list->page }}</td>
                                                <td>
                                                    <span class="{{@$list->document_followup->priority}}">
                                                        @if(@$list->document_followup->priority == 'High')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Low')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Moderate')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                        @endif							
                                                    </span>
                                                </td>
                                                <td> 
                                                    <?php
                                                    $file_type = explode('.', $list->filename);
                                                    $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
                                                    ?>{{ $file_type[1] }}</td>
                                                <td class="">

                                                    <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download">| </i></a></span>	

                                                    <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"> </i></a></span>
													@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
														|
                                                    <span class="document-delete">
                                                        <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                                    </span>
													@endif

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    No Document Found
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Payer Document Accordion Group -->                   
                    <div class="accordion-group">
                        <div class="accordion-heading js_accordion_header">
                            <a class="accordion-toggle">
                                <h4 class="@if(count($payer_document) == 0) med-gray  @endif margin-t-0 no-bottom font16"><i class="fa fa-plus-circle margin-r-10"></i> Payer Reports : <span class="normal-font font13 med-darkgray"><span class="font-italic">ERA/EOB, Correspondence Letter, Appeal Letters...</span></span><span class="text-right pull-right font600 normal-font font14 margin-r-10 js-count">{{ count($payer_document) }}</span></h4>
                            </a>
                        </div> 
                        <div class="accordion-body js_accordion_content collapse">
                            <div class="accordion-inner margin-t-m-8"> 
                                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">                                       

                                    <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                                       class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>


                                </div> 
                                <div class="table-responsive">                                
                                    @if(count($payer_document) > 0)
                                    <table id="payer-doc" class="table table-separate no-bottom popup-table-wo-border">
                                        <thead>
                                            <tr>
                                                <th class="med-green" style="background: #e7f5f4">Created On</th> 
                                                <th class="med-green" style="background: #e7f5f4">User</th> 
                                                <th class="med-green" style="background: #e7f5f4">Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Sub Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Title</th>           
                                                <th class="med-green" style="background: #e7f5f4">Patient Name</th>           
                                                <th class="med-green" style="background: #e7f5f4">Claim No</th>
                                                <th class="med-green" style="background: #e7f5f4">Payer</th>
                                                <th class="med-green" style="background: #e7f5f4">Check No</th>
                                                <th class="med-green" style="background: #e7f5f4">Check Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Check Amount</th>
                                                <th class="med-green" style="background: #e7f5f4">Assigned To</th>
                                                <th class="med-green" style="background: #e7f5f4">Follow up Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Status</th>
                                                <th class="med-green" style="background: #e7f5f4">Pages</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>
                                                <th class="med-green" style="background: #e7f5f4">File Type</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payer_document as $list)
                                            <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                                                <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                                <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                                                <td>{{ @$list->document_categories->category_value }}</td>
                                                <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                                <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                                <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
                                                <td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
                                                <td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
                                                <td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                                <td>
                                                    <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                    @if(date("m/d/y") == $fllowup_date)
                                                    <span class="med-orange">{{$fllowup_date}}</span>
                                                    @elseif(date("m/d/y") >= $fllowup_date)
                                                    <span class="med-red">{{$fllowup_date}}</span>
                                                    @else
                                                    <span class="med-gray">{{$fllowup_date}}</span>
                                                    @endif
                                                </td>
                                                <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                                <td>{{ $list->page }}</td>
                                                <td>
                                                    <span class="{{@$list->document_followup->priority}}">
                                                        @if(@$list->document_followup->priority == 'High')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Low')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Moderate')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                        @endif							
                                                    </span>
                                                </td>
                                                <td> 
                                                    <?php
                                                    $file_type = explode('.', $list->filename);
                                                    $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
                                                    echo @$file_type[1];
                                                    ?></td>
                                                <td class="">

                                                    <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download">| </i></a></span>	

                                                    <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"> </i></a></span>
													@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
														|
                                                    <span class="document-delete">
                                                        <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                                    </span>		
												@endif

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    No Document Found
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Clinical Document Accordion Group -->
                    <div class="accordion-group">
                        <div class="accordion-heading js_accordion_header">
                            <a class="accordion-toggle">
                                <h4 class="@if(count($clinical_document) == 0) med-gray  @endif margin-t-0 no-bottom font16"><i class="fa fa-plus-circle margin-r-10"></i> Clinical Documents : <span class="normal-font font13 med-darkgray"><span class="font-italic">Signed Clinical Notes, CT/MRI Reports, X-ray Reports, Lab Results...</span></span><span class="text-right pull-right font600 normal-font font14 margin-r-10 js-count">{{ count($clinical_document) }}</span></h4>
                            </a>
                        </div> 
                        <div class="accordion-body js_accordion_content collapse">
                            <div class="accordion-inner margin-t-m-8"> 
                                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">                                       

                                    <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                                       class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>


                                </div> 
                                <div class="table-responsive">                                
                                    @if(count($clinical_document) > 0)
                                    <table id="clicnical-doc" class="table table-separate no-bottom popup-table-wo-border">
                                        <thead>
                                            <tr>
                                                <th class="med-green" style="background: #e7f5f4">Created On</th> 
                                                <th class="med-green" style="background: #e7f5f4">User</th> 
                                                <th class="med-green" style="background: #e7f5f4">Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Sub Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Title</th>           
                                                <th class="med-green" style="background: #e7f5f4">Patient Name</th>           
                                                <th class="med-green" style="background: #e7f5f4">Claim No</th>
                                                <th class="med-green" style="background: #e7f5f4">Assigned To</th>
                                                <th class="med-green" style="background: #e7f5f4">Follow up Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Status</th>
                                                <th class="med-green" style="background: #e7f5f4">Pages</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>
                                                <th class="med-green" style="background: #e7f5f4">File Type</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($clinical_document as $list)
                                            <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                                                <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                                <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                                                <td>{{ @$list->document_categories->category_value }}</td>
                                                <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                                <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                                <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                                <td>
                                                    <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                    @if(date("m/d/y") == $fllowup_date)
                                                    <span class="med-orange">{{$fllowup_date}}</span>
                                                    @elseif(date("m/d/y") >= $fllowup_date)
                                                    <span class="med-red">{{$fllowup_date}}</span>
                                                    @else
                                                    <span class="med-gray">{{$fllowup_date}}</span>
                                                    @endif
                                                </td>
                                                <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                                <td>{{ $list->page }}</td>
                                                <td>
                                                    <span class="{{@$list->document_followup->priority}}">
                                                        @if(@$list->document_followup->priority == 'High')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Low')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Moderate')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                        @endif							
                                                    </span>
                                                </td>
                                                <td> 
                                                    <?php
                                                    $file_type = explode('.', $list->filename);
                                                    $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
                                                    ?>{{ $file_type[1] }}</td>
                                                <td class="">

                                                    <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download">| </i></a></span>	

                                                    <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"> </i></a></span>
													@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
														|
                                                    <span class="document-delete">
                                                        <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                                    </span>			
													@endif

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    No Document Found
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Patient Corres Document Accordion Group -->
                    <div class="accordion-group">
                        <div class="accordion-heading js_accordion_header">
                            <a class="accordion-toggle">
                                <h4 class="@if(count($patient_corresp_document) == 0) med-gray  @endif margin-t-0 no-bottom font16"><i class="fa fa-plus-circle margin-r-10"></i> Patient Letter : <span class="normal-font font13 med-darkgray"><span class="font-italic">Patient Statements, Patient Payments Letter, Collection Letter...</span></span><span class="text-right pull-right font600 normal-font font14 margin-r-10 js-count">{{ count($patient_corresp_document) }}</span></h4>
                            </a>
                        </div> 
                        <div class="accordion-body js_accordion_content collapse">
                            <div class="accordion-inner margin-t-m-8"> 
                                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">                                       

                                    <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                                       class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>


                                </div> 
                                <div class="table-responsive">                                
                                    @if(count($patient_corresp_document) > 0)
                                    <table id="pat-cor-doc" class="table table-separate no-bottom popup-table-wo-border">
                                        <thead>
                                            <tr>
                                                <th class="med-green" style="background: #e7f5f4">Created On</th> 
                                                <th class="med-green" style="background: #e7f5f4">User</th> 
                                                <th class="med-green" style="background: #e7f5f4">Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Sub Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Title</th>           
                                                <th class="med-green" style="background: #e7f5f4">Patient Name</th>           
                                                <th class="med-green" style="background: #e7f5f4">Claim No</th>
                                                <th class="med-green" style="background: #e7f5f4">Assigned To</th>
                                                <th class="med-green" style="background: #e7f5f4">Follow up Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Status</th>
                                                <th class="med-green" style="background: #e7f5f4">Pages</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>
                                                <th class="med-green" style="background: #e7f5f4">File Type</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($patient_corresp_document as $list)
                                            <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                                                <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                                <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                                                <td>{{ @$list->document_categories->category_value }}</td>
                                                <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                                <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                                <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                                <td>
                                                    <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                    @if(date("m/d/y") == $fllowup_date)
                                                    <span class="med-orange">{{$fllowup_date}}</span>
                                                    @elseif(date("m/d/y") >= $fllowup_date)
                                                    <span class="med-red">{{$fllowup_date}}</span>
                                                    @else
                                                    <span class="med-gray">{{$fllowup_date}}</span>
                                                    @endif
                                                </td>
                                                <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                                <td>{{ $list->page }}</td>
                                                <td>
                                                    <span class="{{@$list->document_followup->priority}}">
                                                        @if(@$list->document_followup->priority == 'High')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Low')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Moderate')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                        @endif							
                                                    </span>
                                                </td>
                                                <td> 
                                                    <?php
                                                    $file_type = explode('.', $list->filename);
                                                    $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
                                                    ?>{{ $file_type[1] }}</td>
                                                <td class="">

                                                    <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download">| </i></a></span>	

                                                    <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"> </i></a></span>
													@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
														|
                                                    <span class="document-delete">
                                                        <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                                    </span>	
													@endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    No Document Found
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Prescription Document Accordion Group -->
                    <div class="accordion-group">
                        <div class="accordion-heading js_accordion_header">
                            <a class="accordion-toggle">
                                <h4 class="@if(count($prescription_document) == 0) med-gray  @endif margin-t-0 no-bottom font16"><i class="fa fa-plus-circle margin-r-10"></i> Prescription : <span class="normal-font font13 med-darkgray"><span class="font-italic">Prescriptions, E-prescriptions Logs, Medications...</span></span><span class="text-right pull-right font600 normal-font font14 margin-r-10 js-count">{{ count($prescription_document) }}</span></h4>
                            </a>
                        </div> 
                        <div class="accordion-body js_accordion_content collapse">
                            <div class="accordion-inner margin-t-m-8"> 
                                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">                                       

                                    <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                                       class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>


                                </div> 
                                <div class="table-responsive">                                
                                    @if(count($prescription_document) > 0)
                                    <table id="pres-doc" class="table table-separate no-bottom popup-table-wo-border">
                                        <thead>
                                            <tr>
                                                <th class="med-green" style="background: #e7f5f4">Created On</th> 
                                                <th class="med-green" style="background: #e7f5f4">User</th> 
                                                <th class="med-green" style="background: #e7f5f4">Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Sub Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Title</th>           
                                                <th class="med-green" style="background: #e7f5f4">Patient Name</th>           
                                                <th class="med-green" style="background: #e7f5f4">Claim No</th>
                                                <th class="med-green" style="background: #e7f5f4">Assigned To</th>
                                                <th class="med-green" style="background: #e7f5f4">Follow up Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Status</th>
                                                <th class="med-green" style="background: #e7f5f4">Pages</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>
                                                <th class="med-green" style="background: #e7f5f4">File Type</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($prescription_document as $list)
                                            <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                                                <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                                <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                                                <td>{{ @$list->document_categories->category_value }}</td>
                                                <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                                <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                                <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                                <td>
                                                    <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                    @if(date("m/d/y") == $fllowup_date)
                                                    <span class="med-orange">{{$fllowup_date}}</span>
                                                    @elseif(date("m/d/y") >= $fllowup_date)
                                                    <span class="med-red">{{$fllowup_date}}</span>
                                                    @else
                                                    <span class="med-gray">{{$fllowup_date}}</span>
                                                    @endif
                                                </td>
                                                <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                                <td>{{ $list->page }}</td>
                                                <td>
                                                    <span class="{{@$list->document_followup->priority}}">
                                                        @if(@$list->document_followup->priority == 'High')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Low')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Moderate')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                        @endif							
                                                    </span>
                                                </td>
                                                <td> 
                                                    <?php
                                                    $file_type = explode('.', $list->filename);
                                                    $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
                                                    ?>{{ $file_type[1] }}</td>
                                                <td class="">

                                                    <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download">| </i></a></span>	

                                                    <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"> </i></a></span>
													@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
														|
                                                    <span class="document-delete">
                                                        <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                                    </span>
													@endif

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    No Document Found
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Other Document Accordion Group -->
                    <div class="accordion-group">
                        <div class="accordion-heading js_accordion_header">
                            <a class="accordion-toggle">
                                <h4 class="@if(count($other_document) == 0) med-gray  @endif margin-t-0 no-bottom font16"><i class="fa fa-plus-circle margin-r-10"></i> Other Documents : <span class="normal-font font13 med-darkgray"><span class="font-italic">Scan Files, Fax Documents...</span></span><span class="text-right pull-right font600 normal-font font14 margin-r-10 js-count">{{ count($other_document) }}</span></h4>
                            </a>
                        </div> 
                        <div class="accordion-body js_accordion_content collapse">
                            <div class="accordion-inner margin-t-m-8"> 
                                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">
                                    <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                                       class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>
                                </div> 
                                <div class="table-responsive">                                
                                    @if(count($other_document) > 0)
                                    <table id="other-doc" class="table table-separate no-bottom popup-table-wo-border">
                                        <thead>
                                            <tr>
                                                <th class="med-green" style="background: #e7f5f4">Created On</th> 
                                                <th class="med-green" style="background: #e7f5f4">User</th> 
                                                <th class="med-green" style="background: #e7f5f4">Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Sub Category</th>
                                                <th class="med-green" style="background: #e7f5f4">Title</th>           
                                                <th class="med-green" style="background: #e7f5f4">Patient Name</th>           
                                                <th class="med-green" style="background: #e7f5f4">Claim No</th>
                                                <th class="med-green" style="background: #e7f5f4">Assigned To</th>
                                                <th class="med-green" style="background: #e7f5f4">Follow up Date</th>
                                                <th class="med-green" style="background: #e7f5f4">Status</th>
                                                <th class="med-green" style="background: #e7f5f4">Pages</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>
                                                <th class="med-green" style="background: #e7f5f4">File Type</th>
                                                <th class="med-green" style="background: #e7f5f4"></th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($other_document as $list)
                                            <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}">
                                                <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                                <td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
                                                <td>{{ @$list->document_categories->category_value }}</td>
                                                <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                                <td>{{ $list->patients->last_name." ".$list->patients->first_name}}</td>
                                                <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                                <td>{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                                <td>
                                                    <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                    @if(date("m/d/y") == $fllowup_date)
                                                    <span class="med-orange">{{$fllowup_date}}</span>
                                                    @elseif(date("m/d/y") >= $fllowup_date)
                                                    <span class="med-red">{{$fllowup_date}}</span>
                                                    @else
                                                    <span class="med-gray">{{$fllowup_date}}</span>
                                                    @endif
                                                </td>
                                                <td><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                                <td>{{ $list->page }}</td>
                                                <td>
                                                    <span class="{{@$list->document_followup->priority}}">
                                                        @if(@$list->document_followup->priority == 'High')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Low')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                        @elseif(@$list->document_followup->priority == 'Moderate')
                                                        <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                        @endif							
                                                    </span>
                                                </td>
                                                </td>
                                                <td> 
                                                    <?php
                                                    $file_type = explode('.', $list->filename);
                                                    $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
                                                    ?>{{ $file_type[1] }}</td>
                                                <td class="">

                                                    <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download">| </i></a></span>	

                                                    <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"> </i></a></span>
													@if(Auth::user()->user_type=="practice_admin" || Auth::user()->user_type=="customer" || Auth::user()->role_id == 1)
														|
                                                    <span class="document-delete">
                                                        <a class="js-common-delete-document" data-doc-id="{{$list->id}}" data-type = "doc"><i class="fa  {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>                                                       
                                                    </span>		
													@endif

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    No Document Found
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Patient Note Alert Window Starts  -->
<div id="document_attachment" class="js_common_modal_popup modal fade">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Attachment</h4>
            </div>
            <div class="modal-body">
                <img style="width: 100%" class="js-document">
                <input type="hidden" id="redirect_url" value="">
                <ul class="nav nav-list line-height-26">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 modal-desc text-center"></div>
                    </div>
                </ul>                   
                <div class="modal-footer">
                    <button class="js_note_confirm btn btn-medcubics-small js_common_modal_popup_save close_popup" id="true" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- New Document Popup -->
<div id="create_document" class="js_common_modal_popup modal fade">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close js_common_modal_popup_cancel" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Document</h4>
            </div>
            <div class="modal-body">
                <div class="box-body no-bottom no-padding"><!--Background color for Inner Content Starts -->
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >
                        {!! Form::open(['url'=>'patients/document/add/'.$patients->id,'id'=>'js-bootstrap-validator','files'=>true,'method'=>'POST','name'=>'medcubicsform','class'=>'popupmedcubicsform  medcubicsform' ]) !!}
                        <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.documents") }}' />
                        <div class="box no-shadow no-bottom">

                            <!-- form start -->
                            <div class="box-body form-horizontal no-bottom">                        
                                <div class="form-group">
                                    {!! Form::label('title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('title')) error @endif">
                                        {!! Form::text('title',null,['class'=>'form-control','maxlength'=> 120,'id'=>'title']) !!} 
                                        {!! $errors->first('title', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('category', 'Category', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('category')) error @endif">
                                        {!! Form::select('category', array('' => '-- Select --') + (array)$category_list,null,['class'=>'select2 form-control','id'=>'category']) !!}
                                        {!! $errors->first('category', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 
                                <div class="form-group">
<?php $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(Request::segment(2), 'decode'); ?>
                                    {!! Form::label('Claim Number', 'Claim No', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('category')) error @endif">
                                        {!! Form::select('claim_number[]',  array('' => '-- Select --') +(array)$claim_number,null,['class'=>'select2 form-control', 'id'=>'jsclaimnumber']) !!}                                        
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>

                                <div class="form-group show_payer_details hide">
                                    {!! Form::label('payer', 'Payer', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::select('payer',  array('' => '-- Select --') + (array)App\Http\Helpers\Helpers::getPatientInsurance($patient_id),null,['class'=>'select2 form-control payer-validation','id'=>'payer']) !!}                                        
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="form-group show_payer_details payer_appeal hide">
                                    {!! Form::label('checkno', 'Check No', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::text('checkno',null,['class'=>'form-control payer-validation','autocomplete'=>'off','id'=>'checkno']) !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 
                                <div class="form-group show_payer_details payer_appeal hide">
                                    {!! Form::label('checkdate', 'Check Date', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::text('checkdate',null,['class'=>'form-control payer-validation dm-date','autocomplete'=>'off','id'=>'checkdate']) !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 
                                <div class="form-group show_payer_details payer_appeal hide">
                                    {!! Form::label('checkamt', 'Check Amount', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::text('checkamt',null,['class'=>'form-control payer-validation','autocomplete'=>'off','id'=>'checkamt']) !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('assigned', 'Assigned To', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('category')) error @endif">
                                        {!! Form::select('assigned', array('' => '-- Select --') + (array)$user_list,null,['class'=>'select2 form-control','id'=>'assigned']) !!}
                                        {!! $errors->first('assigned', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 
                                <div class="form-group">
                                    {!! Form::label('priority', 'Priority', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('priority')) error @endif">
                                        {!! Form::select('priority', array('' => '-- Select --') + (array)$priority,null,['class'=>'select2 form-control','id'=>'priority']) !!}
                                        {!! $errors->first('priority', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('followup', 'Followup Date', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 p-r-0 control-label star ']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::text('followup',null,['class'=>'form-control dm-date','id'=>'follow_up_date','autocomplete'=>'off']) !!}
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('category')) error @endif">
                                        {!! Form::select('status', array('' => '-- Select --') + (array)array('Assigned'=>'Assigned','Inprocess'=>'Inprocess','Pending'=>'Pending','Review'=>'Review','Completed'=>'Completed'),null,['class'=>'select2 form-control','id'=>'status']) !!}
                                        {!! $errors->first('status', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('page', 'Pages', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::text('page',null,['class'=>'form-control js_numeric','autocomplete'=>'off','maxlength'=> 7]) !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('notes', 'Notes', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label ']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::textarea('notes',null,['class'=>'form-control']) !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="form-group">
                                    <?php $webcam = App\Http\Helpers\Helpers::getDocumentUpload('webcam'); ?>
<?php $scanner = App\Http\Helpers\Helpers::getDocumentUpload('scanner'); ?>
                                    @if($webcam || $scanner)  
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                        {!! Form::label('attachment', 'Attachment', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                            {!! Form::radio('upload_type', 'browse',true,['class'=>'flat-red js-upload-type']) !!} Upload &emsp;
                                            @if($webcam){!! Form::radio('upload_type', 'webcam',null, ['class'=>'flat-red js-upload-type']) !!} Picture &emsp;@endif
                                            @if($scanner){!! Form::radio('upload_type', 'scanner',null,['class'=>'flat-red js-upload-type']) !!} Scanner @endif
                                        </div>
                                        <div class="col-sm-1"></div>
                                    </div> 
                                    @endif
                                    <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-upload margin-t-10">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                           <div class="dropdown pull-right">
                                               <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="fa fa-question-circle margin-t-3 med-green form-icon-billing pull-right"  data-placement="top" data-toggle="tooltip" data-original-title="Info"></i>
                                               </a>
                                               <div class="dropdown-menu1">
                                                   <p class="font12 padding-4">pdf, jpeg, jpg, png, gif, doc, xls, csv, docx, xlsx, txt</p>
                                               </div>
                                           </div>
                                       </div>
                                        
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 no-padding">
                                            <span class="fileContainer " style="padding:1px 16px;"> 
                                                <input class="form-control form-cursor uploadFile" name="filefield[]" type="file" id="filefield1" multiple="multiple">Upload  </span>
                                                
                                            {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                                            <div>&emsp;<p class="js-display-error" style="display: inline;"></p>
                                                <span><i class="fa fa-times-circle cur-pointer removeFile margin-l-10 med-red" data-placement="bottom" data-toggle="tooltip" title="Remove" data-original-title="Tooltip on bottom" style="display:none;"></i></span>
                                            </div>
                                        </div>                                        
                                        
                                    </div>							 

                                    <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-photo margin-t-10" style="display:none">
                                        {!! Form::label('', '', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 no-padding">
                                            <span class="fileContainer js-webcam-class" style="padding:1px 20px;">
                                                <input type="hidden" class="js_err_webcam" /> Webcam</span>
                                            {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                                            &emsp;<span class="js-display-error"></span>
                                        </div>
                                        <div class="col-sm-1"></div>
                                    </div>
                                    <div class="box-footer js-scanner" style="display:none"> 
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                            <button type="button" class="btn btn-medcubics" onclick="scan();">Scan</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="upload_type" value="browse">
                                    <input type="hidden" name="scanner_filename" id="scanner_filename">
                                    <input type="hidden" name="scanner_image" id="scanner_image">
                                    @if($errors->first('filefield'))
                                    <div class="form-group">
                                        {!! Form::label('', '', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
                                        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('filefield')) error @endif">
                                            {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                                        </div>                                                          
                                        <div class="col-sm-1"></div>
                                    </div>
                                    @endif
                                </div><!-- /.box-body -->
                            </div><!-- /.box-body -->
                            <div class="box-footer no-padding">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                                    {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small form-group','accesskey'=>'s']) !!}
                                    <a href="javascript:void(0)" data-url="{{ url('patients/'.$patients->id.'/documents')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small close_popup', 'data-label'=>'close']) !!}</a>
                                </div>
                            </div><!-- /.box-footer -->
                        </div><!-- /.box -->
                        <div style="display:none" id="js-show-webcam">
                            <?php $document_type = "patients"; ?>  
                            @if($document_type=='patients')
                            @include ('layouts/webcam', ['type' => 'patient_document'])
                            @endif
                        </div>
                        {!! Form::close() !!}
                    </div><!--/.col (left) -->
                </div><!--Background color for Inner Content Ends -->	                                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<!-- Show Problem list start-->
<div id="show_document_assigned_list" class="modal fade in js_model_show_document_assigned_list"></div><!-- /.modal-dialog -->
<!-- Show Problem list end-->

@stop   
@push('view.scripts1')  
<script type="text/javascript">
    $('input[type="text"]').attr('autocomplete','off');
	$(document).on('click',"a[data-target=#document_add_modal]", function(e){ 
		setTimeout(function(){  $("#follow_up_date").datepicker({minDate: 0}); $("#checkdate").datepicker(); }, 1000);
	});
	$(document).on('change','#checkdate',function(){ 
		$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'checkdate');
	});

	$(document).on('change', 'input[type="file"]', function () {
		var get_form_id = $(this).parents("form").attr("id");
		var element = $(this);
		// var file = $('#' + get_form_id).find('input[name="filefield[]"]').val();
		var file  = [];
		var filelist = document.getElementById("filefield1").files || [];
		for (var i = 0; i < filelist.length; i++) {
			file.push((filelist[i].name).replace(/C:\\fakepath\\/i, ''));
		}
		if(filelist.length > 5) {

		} 
		// Added queries
		// Revision 1 - Ref: MR-2666 08 Augest 2019: Pugazh
		else if (filelist.length == 0){
			element.parent("span").closest("div").children("div").not(':first').remove();
			element.parent("span").closest("div").find('.js-display-error').html("");
		}else {
		$.each(file, function(i, val) {
			var file_name = val.substring(0, 30);
			var file = (val.length > 30) ? file_name + ".." : val; // changed due to insufficient space
			if(i != 0) {
				var ele = element.parent("span").next("div");
				var parent = element.parent("span").closest("div").find("small").first();
				ele.clone().insertBefore(parent).find('.js-display-error').html(file);      
				// $(parent+"> div:last-child > .js-display-error").html(file);
				// parent.children().last().find('.js-dispaly-error').html(file);
				// .find('.js-display-error').html(file);
				// $(this).parents("span").closest("div").find('.js-display-error').html(file);
				// console.log(ele);
			} else {
				var parent_child = element.parent("span").closest("div").children("div").size();
				if(parent_child > 1) {
					element.parent("span").closest("div").children("div").not(':first').remove();
					element.parent("span").closest("div").find('.js-display-error').html(file);
				//     var ele = element.parents("span").next("div");
				//     var parent = element.parents("span").closest("div");
				//     ele.clone().appendTo(parent).find('.js-display-error').html(file);
					// $(parent+"> div:last-child > .js-display-error").html(file);
					// parent.children().last().find('.js-dispaly-error').html(file);
				} 
				else {
					element.parent("span").closest("div").find('.js-display-error').html(file);
				}
			}
				if (file != '')
				$(".removeFile").hide();
			});
	}

	});

    $(document).ready(function () {
        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            excluded: [':disabled'],
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                title: {
                    message: '',
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: title_lang_err_msg
                        },
                        maxlength: {
                            maxlength : 120,
                            message: "Maxmimum Character Exceeded"
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 ]+$/,
                            message: alphanumericspace_lang_err_msg
                        },
						remote: {
							message: 'Title already taken in the selected category',
							url: api_site_url+'/documentTitle',
							data:{'title':$('input[name="title"]').val(),'_token':$('input[name="_token"]').val(),'category_id':function() { return $('#category').val(); }},
							type: 'POST'
						},
                        callback: {
                            message: '',
                            callback: function(value, validator, $field) {
                                var count = $("#title").val().length;
                                if(count >= 120){
                                    return {
                                        valid: false,
                                        message: "Maxmimum Character Exceeded"
                                    };
                                } else {
                                    return true;
                                }
                            }
                        }
                    }
                },
                category: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: category_lang_err_msg
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
								$('form#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'jsclaimnumber');
								$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'payer');
                                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'checkno');
                                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'checkdate');
                                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'checkamt');
								$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'title');
								$("#checkdate").datepicker();
                                return true;
                            }
                        }
                    }
                },
				assigned: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Assigned To'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				priority: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Priority'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				'followup': {
					  trigger: 'change keyup',
						validators: {
							notEmpty: {
								message: 'Select followup Date'
							},
							date:{
								format:'MM/DD/YYYY',
								message: 'Invalid date format'
							},
							callback: {
								message: '',
								callback: function(value, validator, $field) {
									var fllowup_date = $('#follow_up_date').val();
									var current_date=new Date(fllowup_date);
									var d=new Date();	
									if(fllowup_date != '' && ( d.getTime()-96000000 ) > current_date.getTime()){
										return {
											valid: false,
											message: "Please give future date"
										};
									} else {
										return true;
									}
								}
							}
						}
					},
				status: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Status'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				notes: {
                    message: '',
                    validators: {
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				// page: {
    //                 message: '',
    //                 validators: {
				// 		integer: {
				// 			message: 'The value is not an integer'
				// 		},
    //                     notEmpty: {
    //                         message: 'Enter Pages'
    //                     },
    //                     callback: {
    //                         message: attachment_lang_err_msg,
    //                         callback: function (value, validator) {
    //                             return true;
    //                         }
    //                     }
    //                 }
    //             },
                'jsclaimnumber': {
                    message: '',
                    selector: '#jsclaimnumber',
                    validators: {
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                category_value = $('#category').val();
                                value = $('#jsclaimnumber').val();
                                
								var need_claim_catg = ["Eligibility_Benefits_Eligibility_Reports", "Eligibility_Benefits_Benefit_Verification", "Authorization_Documents_Pre_Authorization_Letter", "Authorization_Documents_Referral_Letter","Clinical_Documents_Progress_Notes","Clinical_Documents_CTMRI_Reports","Clinical_Documents_X_ray_Reports","Clinical_Documents_Lab_Results","Clinical_Documents_Consult_Notes","Clinical_Documents_Admit_Discharge_Summary","Procedure_Documents_Superbills","Procedure_Documents_Surgery_Reports","Procedure_Documents_Procedure_reports","EDI_Reports_Clearinghouse_Reports","EDI_Reports_Payer_Acknowledgements","EDI_Reports_Rejections","Payer_Reports_ERA_EOB","Payer_Reports_Correspondence_Letter","Payer_Reports_Appeal_Letters"];
								var a = need_claim_catg.indexOf(category_value);
                                if ((value == '' || value == null) && a != -1) {
                                    return {
                                        valid: false,
                                        message: "Select claim no"
                                    }
                                } else {
                                    return true;
                                }
                            }
                        }
                    }
                },
				'payer':{
				message: '',
				selector: '#payer',
				validators: {
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							category_value = $('#category').val();
							var need_claim_catg = ["Payer_Reports_ERA_EOB","Payer_Reports_Correspondence_Letter","Payer_Reports_Appeal_Letters"];
							var a = need_claim_catg.indexOf(category_value);
							if ((value == '' || value == null) && a != -1) {
								return {
									valid: false,
									message: "Select payer"
								}
							} else {
								return true;
							}
						}
					}
				}
			},
			'checkno':{
				message: '',
				selector: '#checkno',
				validators: {
					regexp:{
						regexp: /^[A-Za-z0-9 \t]*$/i,
						message: 'Special characters are not allowed'
					}, 
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							category_value = $('#category').val();
							var need_claim_catg = ["Payer_Reports_ERA_EOB","Payer_Reports_Correspondence_Letter"];
							var a = need_claim_catg.indexOf(category_value);
							if(category_value == 'Payer_Reports_ERA_EOB'){
								if ((value == '' || value == null) && a != -1) {
									return {
										valid: false,
										message: "Check No is needed"
									}
								} else {
									return true;
								}
							}else{
								return true;
							}
						}
					}
				}
			},
		'checkdate':{
				message: '',
				selector: '#checkdate',
				validators: {
					date:{
							format:'MM/DD/YYYY',
							message: 'Invalid date format'
						},
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							category_value = $('#category').val();
							var need_claim_catg = ["Payer_Reports_ERA_EOB","Payer_Reports_Correspondence_Letter"];
							var a = need_claim_catg.indexOf(category_value);
							if(category_value == 'Payer_Reports_ERA_EOB'){
								if ((value == '' || value == null) && a != -1) {
									return {
										valid: false,
										message: "Check Date is needed"
									}
								} else {
									return true;
								}
							}else{
								return true;
							}
						}
					}
				}
			},
		'checkamt':{
				message: '',
				selector: '#checkamt',
				validators: {
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							category_value = $('#category').val();
							var need_claim_catg = ["Payer_Reports_ERA_EOB","Payer_Reports_Correspondence_Letter"];
							var a = need_claim_catg.indexOf(category_value);
							if(category_value == 'Payer_Reports_ERA_EOB'){
								if ((value == '' || value == null) && a != -1) {
									return {
										valid: false,
										message: "Check Amount is needed"
									}
								} else {
									return true;
								}
							}else{
								return true;
							}
						}
					}
				}
			},
                js_err_webcam: {
                    message: '',
                    selector: '.js_err_webcam',
                    validators: {
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                var get_checked_val = $('input[name="upload_type"]:checked').val();
                                var err_msg = $('#error-cam').val();
                                if ((err_msg == '' || err_msg == null || err_msg == 1) && get_checked_val == "webcam") {
                                    if (value == '' || value == null)
                                        return false;
                                    else
                                        return true;
                                }
                                return true;
                            }
                        }
                    }
                },
                "filefield[]": {
                    message: '',
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: attachment_lang_err_msg
                        },
                        file: {
                            maxSize: 1024 * 32000,
                            message: "Maximum allowed only 32MB per file"
                            // message: attachment_length_lang_err_msg
                        },
                        callback: {
                            message: attachment_valid_lang_err_msg,
                            callback: function (value, validator) {
                                var file  = [];
                                var filelist = document.getElementById("filefield1").files || [];
                                for (var i = 0; i < filelist.length; i++) {
                                    file.push((filelist[i].name).replace(/C:\\fakepath\\/i, ''));
                                }
                                if(filelist.length > 5) {
                                    return {
                                        valid: false,
                                        message: "Please upload only five files"
                                    };
                                } else {
                                    if (filelist != "") {
                                        var extension_Arr 	= ['pdf','jpeg','jpg','png','gif','doc','xls','csv','docx','xlsx','txt','PDF','JPEG','JPG','PNG','GIF','DOC','XLS','CSV','DOCX','XLSX','TXT'];
                                        var validation = [];
                                        $.each(file, function(i, val) {
                                        var file_name 		= val;
                                        var temp			= file_name.split(".");
                                        filename_length = ((temp.length) - 1);
                                        if(extension_Arr.indexOf(temp[filename_length]) == -1){
                                            validation.push(false);
                                            // return false;
                                        }else{
                                            validation.push(true);
                                            // return true;
                                        }
                                        });
                                        if(jQuery.inArray(false, validation) == -1) {
                                            return true;   
                                        }
                                        else {
                                            return {
                                                valid: false,
                                                message: "Please ensure all the file is of correct format"
                                                };                                           
                                        }
                                    }
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        });
    });
 $(document).on('change','#category',function(){
	if($(this).val() == 'Payer_Reports_ERA_EOB' || $(this).val() == 'Payer_Reports_Correspondence_Letter' || $(this).val() == 'Payer_Reports_Appeal_Letters'){
		$('.show_payer_details').removeClass('hide');
		if($(this).val() == 'Payer_Reports_Appeal_Letters'){
			$('.payer_appeal').addClass('hide');
		}
	}else{
		$('.show_payer_details').addClass('hide');
	}	
});

$(document).ready(function(){
	
	window.click_count = 0;
	<?php if($errors->first('title')){
	 ?> 
		$('.js-new-dcoument').trigger('click');
		setTimeout(function(){	  			
			$("#category").trigger("change");
				click_count = 1;
	 		},	  		
	  600);
	<?php } ?>
})

// Only numeric allow to enter
    $(document).on('keypress keyup blur','.js_numeric',function(event){
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    }); 
    
</script>
@endpush