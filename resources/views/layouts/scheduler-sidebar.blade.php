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
            <!--<li @if($selected_tab == 'dashboard') class="active" @endif>
                <a href="{{ url('scheduler') }}" class="js_next_process">
                    <i class="fa fa-dashboard font16"></i>
                    <span class="@if($selected_tab == 'dashboard') selected @endif"></span>Dashboard
                </a>
            </li>	-->		
            <li @if($selected_tab == 'scheduler') class="active" @endif>
                <a href="{{ url('scheduler/scheduler') }}" class="js_next_process">
                    <i class="fa fa-calendar font16"></i>
                    <span class="@if($selected_tab == 'scheduler') selected @endif"></span>Scheduler
                </a>
            </li>
            @if($checkpermission->check_url_permission('scheduler/appointmentlist') == 1)
                <li @if($selected_tab == 'Reports') class="active" @endif>
                     <a href="{{ url('scheduler/appointmentlist') }}" class="js_next_process">
                        <i class="fa fa-line-chart font16"></i> 
                        <span class="@if($selected_tab == 'Reports') selected @endif"></span>Appointment List
                    </a>
                </li>
            @endif                      
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>