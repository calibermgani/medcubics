<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image" style="padding-bottom:20px;">
                @if($heading_icon == "fa-laptop")
                    <i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="25" data-name="dashboard" ></i>
                @else
                    <i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="25" data-name="{{$heading_icon}}" ></i>
                @endif
            </div>
            <div class="pull-left info" style="padding-top:5px;">
                
                @if( $heading == "AR"  )
                    <p>Dashboard</p>
                @else
                    <p>{{$heading}}</p>
                @endif
            </div>
        </div>
        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <?php if(!Auth::user()->isProvider()){ ?>
            <li @if($selected_tab == 'dashboard') class="active" @endif >
                 <a href="{{ url('analytics/practice') }}" class="js_next_process">
                    <i class="fa font16 {{Config::get('cssconfigs.Practicesmaster.practice')}}"></i>
                    <span class="@if($selected_tab == 'dashboard') selected @endif"></span>Practice Analytics
                </a>
            </li>
            <!-- Hided for 1st version -->
            <!--
            <li @if($selected_tab == 'scheduling-dashboard') class="active" @endif>
                 <a href="{{ url('scheduling') }}" class="js_next_process">
                    <i class="fa font16 {{Config::get('cssconfigs.common.calendar')}}"></i>
                    <span class="@if($selected_tab == 'scheduling-dashboard') selected @endif"></span>Scheduler
                </a>
            </li> -->
            
			<!-- Charge Analytics page hide -->
			
            <!--<li @if($selected_tab == 'charge-analysis') class="active" @endif>
                 <a href="{{ url('dashboard/charges') }}" class="js_next_process">
                    <i class="fa font16 {{Config::get('cssconfigs.charges.charges')}}"></i>
                    <span class="@if($selected_tab == 'charge-analysis') selected @endif"></span>Charge Analytics
                </a>
            </li>-->
			
            <!-- Hided for 1st version -->
			
            <li @if($selected_tab == 'payment-dashboard') class="active" @endif class="hide">
                 <a href="{{ url('analytics/financials') }}" class="js_next_process">
                    <i class="fa font16 {{Config::get('cssconfigs.payments.payments')}}"></i>
                    <span class="@if($selected_tab == 'payment-dashboard') selected @endif"></span>Payment Analytics
                </a>
            </li>
			
			 <li @if($selected_tab == 'aranalytics-dashboard') class="active" @endif>
                <a href="{{ url('analytics/armanagement') }}" class="js_next_process">
                    <i class="fa font16 {{Config::get('cssconfigs.Practicesmaster.ar')}}"></i>
                    <span class="@if($selected_tab == 'summary' || $selected_tab == 'armanagementlist') selected @endif"></span>AR Analytics
                </a>
            </li>  

			<li @if($selected_tab == 'claimsummary') class="active" @endif>
                <a href="{{url('analytics/claims') }}">
                    <i class="fa fa-tachometer font16"></i> 
                    <span class="@if($selected_tab == 'claimsummary') selected @endif"></span> Claims Analytics            
                </a>              
            </li>
			
			<?php } if(Auth::user()->isProvider()){ ?>
             <li @if($selected_tab == 'provider-dashboard') class="active" @endif>
                 <a href="{{ url('analytics/providers') }}" class="js_next_process">
                    <i class="fa font16 {{Config::get('cssconfigs.payments.payments')}}"></i>
                    <span class="@if($selected_tab == 'provider-dashboard') selected @endif"></span>Provider Analytics
                </a>
            </li>
			
			
            <?php }
            
            /*<li @if($selected_tab == 'ar-dashboard') class="active" @endif>
                 <a href="{{ url('ardashboard') }}" class="js_next_process">
                    <i class="fa font16 {{Config::get('cssconfigs.Practicesmaster.ar')}}"></i>
                    <span class="@if($selected_tab == 'ar-dashboard') selected @endif"></span>AR Management
                </a>
            </li>  -->
            */ ?>            
            <!--       
            <li>
                <a href="javascript:void(0);" class="js_next_process">
                    <i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="19" data-name="vector-circle"></i> Insurance Analytics
                </a>
            </li>

            <li>
                <a href="javascript:void(0);" class="js_next_process">
                    <i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="19" data-name="notebook"></i> Patient Analytics
                </a>
            </li>            
           
            <li>
                <a href="javascript:void(0);" class="js_next_process">
                    <i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="19" data-name="money"></i> Financial
                </a>
            </li>

            <li>
                <a href="javascript:void(0);" class="js_next_process">
                    <i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="19" data-name="notebook"></i> AR Reports
                </a>
            </li>

            <li>
                <a href="javascript:void(0);" class="js_next_process">
                    <i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="19" data-name="notebook"></i> Custom Reports
                </a>
            </li>
            -->    

           
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>