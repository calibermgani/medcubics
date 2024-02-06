<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image" style="padding-bottom:20px;">
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
            
            <li @if($selected_tab == 'financial-report') class="active" @endif>
                 <a href="{{ url('reports/financials/list') }}" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.charges.charges') }} font16"></i> 
                    <span class="@if($selected_tab == 'financial-report') selected @endif"></span>Billing Reports
                </a>              
            </li>
            
            <li @if($selected_tab == 'collection-report') class="active" @endif <?php if(Auth::user()->isProvider()) echo 'class="hide"'; ?>>
                 <a href="{{ url('reports/collections/list') }}" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.payments.payments') }} font16"></i> 
                    <span class="@if($selected_tab == 'collection-report') selected @endif "></span>Collection Reports
                </a>              
            </li>
            
            <li @if($selected_tab == 'appointment-report') class="active" @endif <?php if(Auth::user()->isProvider()) echo 'class="hide"'; ?>>
                 <a href="{{ url('reports/appointments/list') }}" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.common.calendar') }} font16"></i> 
                    <span class="@if($selected_tab == 'appointment-report') selected @endif"></span>Appointment Reports
                </a>              
            </li>

            <!--li @if($selected_tab == 'financial-report') class="active" @endif>
                 <a href="{{ url('reports/financials/list') }}" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.payments.payments') }} font16"></i> 
                    <span class="@if($selected_tab == 'financial-report') selected @endif"></span>Financial Reports
                </a>              
            </li-->
            
            <li @if($selected_tab == 'patients-report') class="active " @endif <?php if(Auth::user()->isProvider()) echo 'class="hide"'; ?>>
                 <a href="{{ url('reports/patients/list') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.common.user')}} font16"></i> 
                    <span class="@if($selected_tab == 'patients-report') selected @endif"></span>Patient Reports
                </a>              
            </li>
            
            <li @if($selected_tab == 'ar-report') class="active" @endif>
                 <a href="{{ url('reports/ar/list') }}" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.Practicesmaster.ar') }} font16"></i> 
                    <span class="@if($selected_tab == 'ar-report') selected @endif"></span>AR Reports           
                </a>              
            </li>
            
            <!--li @if($selected_tab == 'scheduling-report') class="active" @endif>
                 <a href="{{ url('reports/scheduling/list') }}" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.common.calendar') }} font16"></i> 
                    <span class="@if($selected_tab == 'scheduling-report') selected @endif"></span>Scheduling Reports
                </a>              
            </li-->
			<!-- MR-2714 - Provider login based reports showing issues fixed and removed practice indicator issues fixed -->
			<!-- Revision 1 : MR-2749 : 26 Aug 2019 : Selva -->
            <li @if($selected_tab == 'practice-report') class="active" @endif <?php if(Auth::user()->isProvider()) echo 'class="hide"'; ?>>
                 <a href="{{ url('reports/practicesettings/list') }}" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.Practicesmaster.facility') }} font16"></i> 
                    <span class="@if($selected_tab == 'practice-report') selected @endif"></span>Practice Indicators
                </a>              
            </li>
            
            <li @if($selected_tab == 'demo-report') class="active" @endif >
                 <a href="{{ url('reports/performance/list') }}" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.Practicesmaster.superbills') }} font16"></i> 
                    <span class="@if($selected_tab == 'demo-report') selected @endif"></span>Performance Reports
                </a>              
            </li>
            
            <li class="hidden" @if($selected_tab == 'misc-report') class="active " @endif>
                 <a href="#" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.Practicesmaster.blogs') }} font16"></i> 
                    <span class="@if($selected_tab == 'misc-report') selected @endif"></span>Miscellaneous
                </a>              
            </li>
            <li @if($selected_tab == 'generated-report') class="active" @endif >
                 <a href="{{ url('reports/generated_reports') }}" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.Practicesmaster.apisettings') }} font16"></i>
                    <span class="@if($selected_tab == 'generated-report') selected @endif"></span>Generated Reports
                </a>
            </li>
			<!--
            <li @if($selected_tab == 'management-report') class="active" @endif>
                 <a href="{{ url('reports/management/list') }}" class="js_next_process">
                    <i class="fa {{ Config::get('cssconfigs.common.management-report') }} font16"></i> 
                    <span class="@if($selected_tab == 'management-report') selected @endif"></span>Upcoming Reports
                </a>              
            </li>

            <li @if($selected_tab == 'financial_report') class="active" @endif>
                 <a>
                    <i class="fa {{ Config::get('cssconfigs.common.custom-report') }} font16"></i> 
                    <span class="@if($selected_tab == 'financial_report') selected @endif"></span>Custom Reports
                </a>
            </li> 


            <li @if($selected_tab == 'edi_report') class="active" @endif>
                 <a>
                    <i class="fa {{ Config::get('cssconfigs.admin.users') }} font16"></i> 
                    <span class="@if($selected_tab == 'edi_report') selected @endif"></span>User Reports
                </a>              
            </li>

            <li @if($selected_tab == 'miscellenous_report') class="active" @endif>
                 <a>
                    <i class="fa {{ Config::get('cssconfigs.common.save') }} font16"></i> 
                    <span class="@if($selected_tab == 'miscellenous_report') selected @endif"></span>Saved Reports
                </a>              
            </li>

            <li @if($selected_tab == 'edi-report') class="active" @endif>
                 <a>
                    <i class="fa {{Config::get('cssconfigs.common.edi')}} font16"></i> 
                    <span class="@if($selected_tab == 'edi-report') selected @endif"></span>EDI Reports           
                </a>              
            </li>
			-->
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>