<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <div class="user-panel">
        <div class="pull-left image bottom-space-10">
            <i class="fa {{@$heading_icon}} font26 med-white"></i>
        </div>
        <div class="pull-left info" style="padding-top:5px;">
            <p>{{ @$heading }}</p>
        </div>
    </div>
    <section class="sidebar">
        <!-- Sidebar user panel -->

        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            @if(@$checkpermission->check_url_permission('practice/{practice}') == 1) 
                <li @if($selected_tab == 'practice') class="active" @endif>
                    @if(Session::has('practice_dbid'))
                        <?php 
							$id = Session::get('practice_dbid');
							$practice_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($id,'encode');
							$url = 'practice/'.$practice_id;
						?>
                    @else
                        <?php $url = 'practice/4';?>
                    @endif

                    <a href="{{ url($url) }}" class="js_next_process faa-parent animated-hover">
                        <i class="fa font16 {{Config::get('cssconfigs.Practicesmaster.practice')}}  faa-falling "></i>
                        <span class="@if($selected_tab == 'practice') selected @endif"></span> Practice
                    </a>
                </li>
            @endif	
            
            @if($checkpermission->check_url_permission('facility') == 1) 
                <li @if($selected_tab == 'facility') class="active" @endif>
                    <a href="{{ url('facility') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.facility')}} font16"></i> 
                        <span class="@if($selected_tab == 'facility') selected @endif"></span> Facility            
                    </a>              
                </li>
            @endif	

            @if($checkpermission->check_url_permission('provider') == 1)
                <li @if($selected_tab == 'provider') class="active" @endif>
                    <a href="{{ url('provider') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} font16"></i>
                        <span class="@if($selected_tab == 'provider') selected @endif"></span> Provider
                    </a>
                </li>
            @endif	

            @if($checkpermission->check_url_permission('insurance') == 1)
                <li @if($selected_tab == 'insurance' || $selected_tab == 'insurancemaster') class="active" @endif>
                    <a href="{{ url('insurance') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.common.insurance')}} font16"></i> 
                        <span class="@if($selected_tab == 'insurance' || $selected_tab == 'insurancemaster') selected @endif"></span> Insurance
                    </a>
                </li>
            @endif

            @if($checkpermission->check_url_permission('icd') == 1)
                <li @if($selected_tab == 'icd') class="active" @endif>
                    <a href="{{ url('icd') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.common.icd')}} font16"></i>
                        <span class="@if($selected_tab == 'icd') selected @endif"></span> ICD 10
                    </a>
                </li>
            @endif

            @if($checkpermission->check_url_permission('cpt') == 1)
                <li @if($selected_tab == 'cpt') class="active" @endif>
                    <a href="{{ url('listfavourites') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} font16"></i>
                        <span class="@if($selected_tab == 'cpt') selected @endif"></span> CPT / HCPCS
                    </a>
                </li>
            @endif	

            @if($checkpermission->check_url_permission('modifierlevel1') == 1)
                <li @if($selected_tab == 'modifiers') class="active" @endif>
                    <a href="{{ url('modifierlevel1') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.common.modifiers')}} font16"></i>
                        <span class="@if($selected_tab == 'modifiers') selected @endif"></span> Modifiers
                    </a>
                </li>
            @endif	

            @if($checkpermission->check_url_permission('code') == 1)
                <li @if($selected_tab == 'code') class="active" @endif>
                    <a href="{{ url('code') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.common.codes')}} font16"></i>
                        <span class="@if($selected_tab == 'code') selected @endif"></span> Remittance Codes
                    </a>
                </li>
                <?php //$path = Route::getCurrentRoute()->getPath(); ?>
            @endif	

            @if($checkpermission->check_url_permission('employer') == 1)
                <li @if($selected_tab == 'employer') class="active" @endif>
                    <a href="{{ url('employer') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font16"></i>
                        <span class="@if($selected_tab == 'employer') selected @endif"></span> Employers
                    </a>
                </li>
            @endif	

            @if($checkpermission->check_url_permission('templates') == 1)
                <li @if($selected_tab == 'templates') class="active" @endif>
                    <a href="{{ url('templates') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.common.templates')}} font16"></i>
                        <span class="@if($selected_tab == 'templates') selected @endif"></span> Templates
                    </a>
                </li>		
            @endif	
			
			<li @if($selected_tab == 'followup') class="active" @endif>
				<a href="{{ url('followup/category') }}" class="js_next_process">
					<i class="fa {{Config::get('cssconfigs.common.calendar')}} font16"></i>
					<span class="@if($selected_tab == 'followup') selected @endif"></span> Follow-up Template
				</a>
			</li>
			

            <li class="header"> </li>
<!--
            @if($checkpermission->check_url_permission('edi') == 1)
            <li @if($selected_tab == 'edi') class="active" @endif>
                 <a href="{{ url('edi') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.common.edi')}} font16"></i>
                    <span class="@if(@$selected_tab == 'edi') selected @endif"></span>EDI
                </a>
            </li>
            @endif
-->         
            @if($checkpermission->check_url_permission('comhistory') == 1)
                <li @if($selected_tab == 'comhistory') class="active" @endif>
                    <a href="{{ url('comhistory') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.communication')}} font16"></i>
                        <span class="@if(@$selected_tab == 'comhistory') selected @endif"></span> Communication Info
                    </a>
                </li>
            @endif
            
            @if($checkpermission->check_url_permission('feeschedule') == 1)  
                <li @if($selected_tab == 'feeschedule') class="active" @endif>
                    <a href="{{ url('feeschedule') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.inbox')}} font16"></i>
                        <span class="@if(@$selected_tab == 'feeschedule') selected @endif"></span> Fee Schedule
                    </a>
                </li>
            @endif

            @if($checkpermission->check_url_permission('practiceproviderschedulerlist') == 1)
                <li @if(@$selected_tab == 'scheduler') class="active" @endif>
                    <a href="{{ url('practiceproviderschedulerlist') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.scheduler')}} font16"></i>
                        <span class="@if(@$selected_tab == 'scheduler') selected @endif"></span> Scheduler Preference
                    </a>
                </li>
            @endif

            @if($checkpermission->check_url_permission('usersactivity') == 1 && $checkpermission->checkAllowToAccess('practice')) 
                <li @if($selected_tab == 'usersactivity') class="active" @endif>
                    <a href="{{ url('usersactivity') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.facility')}} font16"></i> 
                        <span class="@if($selected_tab == 'usersactivity') selected @endif"></span> Users Activity            
                    </a>              
                </li>
            @endif

            @if($checkpermission->check_url_permission('patientstatementsettings') == 1)
                <li @if(@$selected_tab == 'patientstatementsettings' || @$selected_tab == 'patientbulkstatement' || @$selected_tab == 'patientindividualstatement' || @$selected_tab == 'patientstatementhistory') class="active" @endif>
                    <a href="{{ url('patientstatementsettings') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.patientstatement')}} font16"></i>
                        <span class="@if(@$selected_tab == 'patientstatementsettings' || @$selected_tab == 'patientbulkstatement' || @$selected_tab == 'patientindividualstatement' || @$selected_tab == 'patientstatementhistory') selected @endif"></span> Patient Statement
                    </a>
                </li>
            @endif

            <!-- Hided this menu because of UI changes in Demographics Page --> 
            <!-- @if($checkpermission->check_url_permission('registration') == 1)
             <li @if(@$selected_tab == 'registration') class="active" @endif>
                  <a href="{{ url('registration') }}" class="js_next_process">
                     <i class="fa {{Config::get('cssconfigs.Practicesmaster.registration')}} font16"></i>
                     <span class="@if(@$selected_tab == 'registration') selected @endif"></span>Registration
                 </a>
             </li>
             @endif -->
            <!-- Hided this menu because of UI changes in Demographics Page -->

            <!-- Hided this menu because we not using it in version 1. Check with patient-sidebar blade file --> 
            <!--  @if($checkpermission->check_url_permission('superbills') == 1)
            <li @if(@$selected_tab == 'superbills') class="active" @endif>
                  <a href="{{ url('superbills') }}" class="js_next_process">
                     <i class="fa {{Config::get('cssconfigs.Practicesmaster.superbills')}} font16"></i>
                     <span class="@if(@$selected_tab == 'superbills') selected @endif"></span>Superbills
                 </a>
             </li>
             @endif -->
            <!-- Hided this menu because we not using it in version 1 --> 

            <!--- API settings start -->

            <li @if($selected_tab == 'holdoption' || $selected_tab == 'reason_for_visit' || $selected_tab == 'adjustmentreason' || $selected_tab == 'emailtemplate'|| $selected_tab == 'clinicalcategories'|| $selected_tab == 'insurance_type' || $selected_tab=='statementholdreason' || $selected_tab=='statementcategory' || $selected_tab=='procedurecategory' || $selected_tab == 'claimsubstatus')  class="active" @endif>
                 <a href="{{ url('reason') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.apisettings')}} font14"></i>
                    <span class="@if($selected_tab == 'holdoption' || $selected_tab == 'reason_for_visit' || $selected_tab == 'adjustmentreason' || $selected_tab == 'emailtemplate' || $selected_tab == 'clinicalcategories'|| $selected_tab == 'insurance_type' || $selected_tab == 'claimsubstatus') selected @endif"></span> Account Preference
                </a>
            </li>

            <li @if($selected_tab == 'apisettings' || $selected_tab == 'userapisettings')  class="active" @endif>
                 <a href="{{ url('apisettings') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.api')}} font14"></i>
                    <span class="@if($selected_tab == 'apisettings' || $selected_tab == 'userapisettings') selected @endif"></span> API Settings
                </a>
            </li>
            
            @if(Auth::user()->role_id == 1)  
                <?php $url_segment = Request::segment(2);?>      
                <li @if($selected_tab == 'changedate' && $url_segment != 'payments') class="active" @endif>
                     <a href="{{ url('changedate/charges') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.charges.charges')}} font14"></i>
                        <span class="@if($selected_tab == 'changedate' && $url_segment != 'payments') selected @endif"></span> Charges
                    </a>
                </li>

                <li @if($selected_tab == 'changedate' && $url_segment == 'payments') class="active" @endif>
                     <a href="{{ url('changedate/payments') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.payments.payments')}} font14"></i>
                        <span class="@if($url_segment == 'payments') selected @endif"></span> Payments
                    </a>
                </li>
            @endif
            <!--  App settings   -->
            @if($checkpermission->check_url_permission('questionnaire/template') == 1)
                <li @if($selected_tab == 'questionnaire/template' || $selected_tab == 'apptemplate' || $selected_tab == 'questionnaires')  class="active" @endif>
                     <a href="{{ url('questionnaire/template') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.app')}} font14"></i>
                        <span class="@if($selected_tab == 'questionnaire/template'||$selected_tab == 'questionnaires'|| $selected_tab == 'apptemplate') selected @endif"></span> APP Settings
                    </a>
                </li>
            @endif
            <!--- Email Tamplate start -->

            <!-- Hided this menu because we having doubt in listing this in this page -->
            <!--<li @if($selected_tab == 'users' || $selected_tab == 'practice/useractivity' || $selected_tab == 'practice/userhistory')  class="active" @endif>
                 <a href="{{ url('users') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.common.user')}} font14"></i>
                    <span class="@if($selected_tab == 'users'||$selected_tab == 'practice/useractivity'|| $selected_tab == 'practice/userhistory') selected @endif"></span>Users
                </a>
            </li>-->
            <!-- Hided this menu because we having doubt in listing this in this page -->
			
			<li @if(@$selected_tab == 'userLoginHistory') class="active margin-b-20" @endif>
                 <a href="{{ url('userLoginHistory/pendingApproval') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.security')}} font16"></i>
                    <span class="@if(@$selected_tab == 'userLoginHistory') selected @endif"></span> Security Code
                </a>
            </li>
            <li @if(@$selected_tab == 'Charge Delete') class="active margin-b-20" @endif>
                 <a href="{{ url('practice/charge/delete') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.common.delete')}} font16"></i>
                    <span class="@if(@$selected_tab == 'Charge Delete') selected @endif"></span> Charge Delete
                </a>
            </li>
			
			
            <li @if(@$selected_tab == 'staticpage') class="active margin-b-20" @endif style="margin-bottom:40px;">
                 <a href="{{ url('staticpage') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.common.help')}} font16"></i>
                    <span class="@if(@$selected_tab == 'staticpage') selected @endif"></span> Help
                </a>
            </li>

            <!--- Email Tamplate end ---->

            <!--- API settings end -->
            <!--
            <li @if(@$selected_tab == 'payments') class="active" @endif>
                                <a href="" class="js_next_process">
                <i class="fa fa-money font16 "></i>
                <span class="@if(@$selected_tab == 'payments') selected @endif"></span>Payments
                </a>
            </li>
            
            <li><a href="#"><i class="livicon" data-color="#b8c7ce" data-hover="#fff" data-size="19" data-name="shopping-cart"></i> <span>Claims</span></a></li>
            <li><a href="#"><i class="livicon" data-color="#b8c7ce" data-hover="#fff" data-size="19" data-name="folders"></i> <span>Documents</span></a></li>
            <li><a href="#"><i class="livicon" data-color="#b8c7ce" data-hover="#fff" data-size="19" data-name="barchart"></i> <span>Reports</span></a></li>
            <li><a href="#"><i class="livicon" data-color="#b8c7ce" data-hover="#fff" data-size="19" data-name="mail"></i> <span>Messages</span></a></li>
            -->
            <li class="header"> </li>
            <li class="header"> </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>