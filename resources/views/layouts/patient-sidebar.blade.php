<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image bottom-space-10">
                <i class="fa {{@$heading_icon}} font26 med-white"></i>			  
            </div>
            <div class="pull-left info" style="padding-top:5px;">
                <p>{{ $heading }}</p>
            </div>
        </div>
        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
			<?php
				if($selected_tab == 'patients' || $selected_tab == 'patientstatements' || $selected_tab == 'billing' || $selected_tab == 'correspondence' || $selected_tab == 'payments' || $selected_tab == 'patientpayment' || $selected_tab == 'documents'|| $selected_tab == 'appointments'|| $selected_tab == 'eligibility' || $selected_tab == 'superbill' || $selected_tab == 'problemlist' || $selected_tab == 'armanagement' || $selected_tab == 'ledger' || $selected_tab == 'clinicalnotes' || $selected_tab == 'tasklist' || $selected_tab == 'reports'|| $selected_tab == 'reports1'|| $selected_tab == 'patientbudget' || $selected_tab == 'budgetplan' || $selected_tab == 'patientnote' || $selected_tab == 'patientshistory') {				
					$id = Route::current()->parameter('id');
					$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($id,'decode');
				} else {
					$patient_id = Route::current()->parameter('id');
					$id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id,'encode');
				}		
			?>
            @if($patient_id != '' || (strpos($currnet_page, 'patientstatements') !== false))

            @if($checkpermission->check_url_permission('patients/{id}/ledger') == 1)
            <li @if($selected_tab == 'ledger') class="active" @endif>
                 <a href="{{ url('patients/'.$id.'/ledger') }}" class="js_next_process" accesskey="l">
                    <i class="fa fa-newspaper-o font16"></i> 
                    <span class="@if($selected_tab == 'ledger') selected @endif"></span> <span class="text-underline">L</span>edger
                </a>
            </li>
            @endif

            @if($checkpermission->check_url_permission('patients/{id}') == 1)
            <li @if($selected_tab == 'patients') class="active" @endif>
                 <a href="{{ url('patients/'.$id.'/edit') }}" class="js_next_process" accesskey="g">
                    <i class="fa fa-user font16"></i> 
                    <span class="@if($selected_tab == 'patients') selected @endif"></span> Re<span class="text-underline">g</span>istration
                </a>
            </li>
            @endif

            @if($checkpermission->check_url_permission('patients/{id}/billing') == 1)
            <li @if($selected_tab == 'billing') class="active" @endif>
                 <a href="{{ url('patients/'.$id.'/appointments') }}" class="js_next_process" accesskey="v">
                    <i class="fa fa-pencil font16"></i> 
                    <span class="@if($selected_tab == 'billing') selected @endif"></span> <span class="text-underline">V</span>isits
                </a>
            </li>
            @endif

            @if($checkpermission->check_url_permission('patients/{id}/payments') == 1)
            <li @if($selected_tab == 'payments') class="active" @endif>                
                 <a href="{{ url('patients/'.$id.'/payments') }}" class="js_next_process" accesskey="y">				 
                    <i class="fa fa-money font16"></i> 					
                    <span class="@if($selected_tab == 'payments') selected @endif"></span> Pa<span class="text-underline">y</span>ments
                </a>            
            </li>
            @endif	 

            @if($checkpermission->check_url_permission('patients/{id}/armanagement/list') == 1)
            <li @if($selected_tab == 'armanagement' && Request::segment(4) != 'followup') class="active" @endif>
                 <a href="{{ url('patients/'.$id.'/armanagement/arsummary') }}" class="js_next_process" accesskey="">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.ar')}} font16"></i> 
                    <span class="@if($selected_tab == 'armanagement') selected @endif"></span> AR Management
                </a>
            </li>
            @endif

            @if($checkpermission->check_url_permission('patients/{id}/problemlist') == 1)	
            <li @if($selected_tab == 'problemlist') class="active" @endif>
                 <a href="{{ url('patients/'.$id.'/problemlist') }}" class="js_next_process" accesskey="">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.questionnaires')}} font16"></i>
                    <span class="@if($selected_tab == 'problemlist') selected @endif"></span> Workbench
                    <?php $problem_list_count = App\Models\Patients\ProblemList::getProblemListCount($id); ?>				
                    @if($problem_list_count > 0)
                    <small class="label pull-right bg-yellow" style="font-weight:400">
                        {{(@$problem_list_count)}}</small>
                    @endif
                </a>
            </li>
            @endif

            <li @if(Request::segment(4) == 'followup' && Request::segment(5) == 'list') class="active" @endif style="display:none">
                 <a href="{{ url('patients/'.$id.'/armanagement/followup/list') }}" accesskey="">
                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} font16"></i> 
                    <span class="@if(Request::segment(4) == 'followup' && Request::segment(5) == 'list') selected @endif"></span> Followup List
                </a>    
            </li>

            <!-- Hided this menu because we not using it in version 1. Check with practice-sidebar blade file --> 
            <!--
                  @if($checkpermission->check_url_permission('patients/{id}/superbill/create') == 1)
                <li @if($selected_tab == 'superbill') class="active" @endif>
                     <a href="{{ url('patients/'.$id.'/superbill/create') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.patient.superbill')}} font16"></i> 
                        <span class="@if($selected_tab == 'superbill') selected @endif"></span> E-Superbills
                    </a>
                </li>
                @endif      
            -->

            <li class="header"> </li>

            @if($checkpermission->check_url_permission('patients/{id}/notes') == 1)
            <li @if($selected_tab == 'patientnote') class="active" @endif><a href="{{ url('patients/'.@$id.'/notes') }}" accesskey="t"><i class="fa fa-sticky-note i-font-tabs"></i>  <span class="@if($selected_tab == 'patientnote') selected @endif"></span> No<span class="text-underline">t</span>es</a></li>
            @endif

            @if($checkpermission->check_url_permission('patients/{id}/eligibility') == 1)
            <li @if($selected_tab == 'eligibility') class="active" @endif>
                 @if(Session::has('patient_id')) 
                 <?php  $patient_id =  Session::get('patient_id');?>
                 @endif
                 <a href="{{ url('patients/'.$id.'/eligibility') }}" class="js_next_process" accesskey="">
                    <i class="fa {{Config::get('cssconfigs.admin.speciality')}} font16"></i> 
                    <span class="@if($selected_tab == 'eligibility') selected @endif"></span> Eligibility
                </a>
            </li>
            @endif

            @if($checkpermission->check_url_permission('patients/{id}/correspondence') == 1)
            <li @if($selected_tab == 'correspondence') class="active" @endif>
                 <a href="{{ url('patients/'.$id.'/correspondencehistory') }}" class="js_next_process" accesskey="">
                    <i class="fa {{Config::get('cssconfigs.patient.file_text')}} font16"></i> 
                    <span class="@if($selected_tab == 'correspondence') selected @endif"></span> Templates
                </a>
            </li>
            @endif

            <!--
            <li @if($selected_tab == 'patientstatements') class="active" @endif >
                 <a href="{{ url('patients/'.@$id.'/patientstatements') }}"><i class="fa fa-file i-font-tabs"></i> 
                    <span class="@if($selected_tab == 'patientstatements') selected @endif"></span> Statements

                </a>
            </li>
            -->
             <!-- Patient payment -->
            @if($checkpermission->check_url_permission('patients/{id}/patientpayment') == 1)
            <li @if($selected_tab == 'patientpayment') class="active" @endif>                
                 <a href="{{ url('patients/'.$id.'/patientpayment') }}" class="js_next_process" accesskey="">				 
                    <i class="fa {{Config::get('cssconfigs.patient.history')}} font16"></i> 					
                    <span class="@if($selected_tab == 'patientpayment') selected @endif"></span>Statements
                </a>            
            </li>
            @endif	
            
            @if($checkpermission->check_url_permission('patients/{id}/documents') == 1)
            <li @if($selected_tab == 'documents') class="active" @endif>
                 <a href="{{ url('patients/'.@$id.'/documentsummary') }}" class="js_next_process" accesskey="">
                    <i class="fa {{Config::get('cssconfigs.patient.file_open')}} font16"></i> 
                    <span class="@if($selected_tab == 'documents') selected @endif"></span> Documents
					<?php $document_list_count = App\Models\Patients\DocumentFollowupList::getDocumentAssignedCount($id); ?>				
                    @if($document_list_count > 0)
                    <small class="label pull-right bg-yellow" style="font-weight:400">
                        {{(@$document_list_count)}}</small>
                    @endif
                </a>
            </li>
            @endif
            <!-- Hided for 1st version --> 
            <!-- 
            @if($checkpermission->check_url_permission('patients/{id}/reports') == 1)
            <li @if($selected_tab == 'reports') class="active" @endif>
                 <a href="{{ url('patients/'.@$id.'/reports') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.resources')}} font16"></i> 
                    <span class="@if($selected_tab == 'reports') selected @endif"></span>Patient Reports
                </a>
            </li> -->

            <!--
               @if($checkpermission->check_url_permission('scheduler/scheduler') == 1)
                   @if($checkpermission->check_url_permission('patients/{id}/appointments') == 1)
                       <li @if($selected_tab == 'appointments') class="active" @endif>
                            <a href="{{ url('patients/'.$id.'/appointments') }}" class="js_next_process">
                               <i class="fa {{Config::get('cssconfigs.patient.calendar')}} font16"></i>
                               <span class="@if($selected_tab == 'appointments') selected @endif"></span> Appointments              
                           </a>              
                       </li>
                   @endif
               @endif   
   -->
  
               @if($checkpermission->check_url_permission('patients/{id}/budgetplan') == 1)
                   <li @if($selected_tab == 'patientstatements') class="active" @endif >
                       <a href="{{ url('patients/'.@$id.'/budgetplan') }}"><i class="fa fa-file-text-o i-font-tabs"></i> 
                           <span class="@if($selected_tab == 'patientstatements') selected @endif"></span> Budget Plan
                       </a>
                   </li>
               @endif
                 
            
            @if($checkpermission->check_url_permission('patients/{id}/medicalhistory') == 1)
                <li @if($selected_tab == 'patientshistory') class="active" @endif >
                     <a href="{{ url('patients/'.@$id.'/medicalhistory') }}" accesskey=""><i class="fa {{Config::get('cssconfigs.common.medicalhistory')}} i-font-tabs"></i> <span class="@if($selected_tab == 'patientshistory') selected @endif"></span> Medical History
                    <small class="label pull-right bg-yellow" style="font-weight:400;margin-right: -7px;"> App</small>
                    </a>
                </li>
            @endif
          
            <!-- Hided this menu because we not using it in version 1 --> 
            <!--    @if($checkpermission->check_url_permission('patients/{id}/ledger') == 1)
                <li @if($selected_tab == 'clinicalnotes') class="active" @endif>
                     <a href="{{ url('patients/'.$id.'/clinicalnotes') }}">
                        <i class="fa fa-sticky-note font16"></i> 
                        <span class="@if($selected_tab == 'clinicalnotes') selected @endif"></span> Clinical Notes
                    </a>
                </li>
                @endif -->
            <!-- Hided this menu because we not using it in version 1 --> 

            <!-- Hided this menu because we not using it in version 1 --> 
            <!-- @endif -->

            @else
            <li @if($selected_tab == 'patients') class="active" @endif>
                 <a href="{{ url('patients/create') }}" class="js_next_process" accesskey="g">
                    <i class="fa fa-user font16"></i> 
                    <span class="@if($selected_tab == 'patients') selected @endif"></span> Re<span class="text-underline">g</span>istration
                </a>
            </li>
            @endif   
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>