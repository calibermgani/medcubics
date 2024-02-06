<div class="box-body hidden-print" style="margin-top: -18px;">
    <?php 
        $currnet_arr = explode('/', $currnet_page);
        if ($currnet_arr[0] == 'patients') {
            $patient_current_page = 'patients';
        } elseif ($currnet_arr[0] == 'dashboard') {
            $patient_current_page = 'dashboard';
        } elseif ($currnet_arr[0] == 'charges') {
            $patient_current_page = 'charges';
        } elseif ($currnet_arr[0] == 'claims') {
            $patient_current_page = 'claims';
        } elseif ($currnet_arr[0] == 'payments') {
            $patient_current_page = 'payments';
        }

        if($currnet_arr[0] == 'practiceproviderschedulerlist' || $currnet_arr[0] == 'practicefacilityschedulerlist' || $currnet_arr[0] == 'facilityscheduler' || $currnet_arr[0] == 'practicescheduler' ){
        	$currnet_page = 'practice';
        }
    ?> 
    @if(Auth::user())
		<div class="row">
			<div class="col-sm-1 col-md-0  col-xs-2 col-md-offset-3 col-sm-offset-1 col-xs-offset-1">
				<div class="module-menu-icon @if($routex[0] == 'dashboard' || $patient_current_page == 'dashboard' || $currnet_arr[0] == 'analytics') dash-module-active @else dash-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Dashboard">                               
					<a href="@if(Auth::user()->isProvider()) {{url('analytics/providers')}} @else {{url('analytics/practice')}} @endif" class="js_next_process"><i class="livicon left-pad" data-name="dashboard" data-size="30" data-color="@if($routex[0] == 'dashboard' || $patient_current_page == 'dashboard' || $currnet_arr[0] == 'analytics') #fff @else #429b9b @endif" data-hovercolor="#fff"></i></a>
				</div>
			</div>
			<!-- /.col -->                                    
			<div class="col-sm-1 col-md-0 col-xs-2 <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
				<div class="module-menu-icon @if(strpos($currnet_page, 'scheduler') !== false) scheduler-module-active @else scheduler-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Scheduler">
					<a href="{{ url('scheduler/scheduler') }}" class="js_next_process"><i class="livicon left-pad" data-name="calendar" data-size="30" data-color="@if(strpos($currnet_page, 'scheduler') !== false) #fff @else #4685f9 @endif"  data-hovercolor="#fff"></i></a>
				</div>
			</div>
			<!-- /.col -->                                    
			<div class="col-sm-1 col-md-0 col-xs-2 <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">						
				<div class="module-menu-icon @if($routex[0] == 'patients' || $patient_current_page == 'patients') patient-module-active @else patient-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Patients">                                
					<a href="{{ url('patients') }}" class="js_next_process"><i class="livicon left-pad" data-name="users" data-size="30" data-color="@if($routex[0] == 'patients' || $patient_current_page == 'patients') #fff @else #DB5C87 @endif"  data-hovercolor="#fff"></i></a>
				</div>
			</div>
			<!-- /.col -->                                    
			<div class="col-sm-1 col-md-0 col-xs-2 <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
				<div class="module-menu-icon @if($routex[0] == 'charges' || $patient_current_page == 'charges') charge-module-active @else charge-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Charges">
					<a href="{{ url('charges') }}" class="js_next_process"><i class="livicon left-pad" data-name="list" data-size="30" data-color="@if($routex[0] == 'charges' || $patient_current_page == 'charges') #fff @else #46cdf9 @endif"  data-hovercolor="#fff"></i></a>
				</div>
			</div>
			<!-- /.col -->                                    
			<div class="col-sm-1 col-md-0 col-xs-2 <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
				<div class="module-menu-icon @if($routex[0] == 'payments' || $patient_current_page == 'payments') payments-module-active @else payments-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Payments">
					<a href="{{ url('payments') }}" class="js_next_process"><i class="livicon left-pad" data-name="money" data-size="30" data-color="@if($routex[0] == 'payments' || $patient_current_page == 'payments') #fff @else #8cc503 @endif" data-hovercolor="#fff"></i></a>
				</div>
			</div>
			<!-- /.col -->                                    
			<div class="col-sm-1 col-md-0 col-xs-2 col-xs-offset-1 col-lg-offset-0 col-md-offset-0 col-sm-offset-0 <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
				<div class="padding-t-xs-5">
					<div class="module-menu-icon @if($routex[0] == 'armanagement' || $ar_main_page == 'armanagement') ar-module-active @else ar-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="AR Management">
						<a href="{{ url('armanagement/armanagementlist') }}" class="js_next_process"><i class="livicon left-pad" data-name="laptop" data-size="30" data-color="@if($routex[0] == 'armanagement' || $ar_main_page == 'armanagement') #fff @else #b7375c @endif"  data-hovercolor="#fff"></i></a>
					</div>
				</div>            
			</div>
			<!-- /.col -->                                    
			<div class="col-sm-1 col-md-0 col-xs-2 <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
				<div class="padding-t-xs-5">
					<div class="module-menu-icon @if($routex[0] == 'claims' || $patient_current_page == 'claims') claims-module-active @else claims-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Claims">
						<a href="{{ url('claims/status/electronic') }}" class="js_next_process"><i class="livicon left-pad" data-name="shopping-cart" data-size="30" data-color="@if($routex[0] == 'claims' || $patient_current_page == 'claims') #fff @else #c8ae02 @endif" data-hovercolor="#fff"></i></a>
					</div>
				</div>            
			</div>
			<!-- /.col -->                                    
			<div class="col-sm-1 col-md-0 col-xs-2 <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
				<div class="padding-t-xs-5">
					<div class="module-menu-icon @if($currnet_arr[0] == 'reports' || $patient_current_page == 'reports') report-module-active @else report-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Reports">
						<a href="{{ url('reports/financials/list') }}" class="js_next_process"><i class="livicon left-pad" data-name="barchart" data-size="30" data-color="@if($currnet_arr[0] == 'reports' || $patient_current_page == 'reports') #fff @else #a967aa @endif" data-hovercolor="#fff"></i></a>
					</div>
				</div>
			</div> 
			<!-- Provider Login based header icon changed -->
			<!-- Selva : 20 Aug 2019 -->
			<!-- Changed select icon for sub menu -->
			<!-- Revision 1 MR-2714 21 Aug 2019 : Selva -->
			<?php if(Auth::user()->isProvider()){ ?>
				
			<div class="col-sm-1 col-md-0 col-xs-2">
				<div class="padding-t-xs-5">
					<div class="module-menu-icon @if($currnet_arr[0] == 'reports' && $currnet_arr[1] == 'financials') scheduler-module-active @else scheduler-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Billing Reports">
						<a href="{{ url('reports/financials/list') }}" class="js_next_process"><i class="livicon left-pad" data-name="pen" data-size="30" data-color="@if($currnet_arr[1] == 'financials') #fff @else #4685f9 @endif" data-hovercolor="#fff"></i></a>
					</div>
				</div>
			</div> 
			
			
			
			<div class="col-sm-1 col-md-0 col-xs-2">
				<div class="padding-t-xs-5">
					<div class="module-menu-icon @if($currnet_arr[0] == 'reports' && $currnet_arr[1] == 'ar') payments-module-active @else payments-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="AR Reports">
						<a href="{{ url('reports/ar/list') }}" class="js_next_process"><i class="livicon left-pad" data-name="desktop" data-size="30" data-color="@if($currnet_arr[1] == 'ar') #fff @else #8cc503 @endif" data-hovercolor="#fff"></i></a>
					</div>
				</div>
			</div> 
			<!-- MR-2714 - provider login icon highlighted issues fixed -->
			<!-- Revision 1 : 22 Aug 2019 : Selva -->
			<!-- MR-2714 - Provider login based reports showing issues fixed and removed practice indicator issues fixed -->
			<!-- Revision 1 : MR-2749 : 26 Aug 2019 : Selva -->
			<div class="col-sm-1 col-md-0 col-xs-2">
				<div class="padding-t-xs-5">
					<div class="module-menu-icon @if($currnet_arr[0] == 'reports' && $currnet_arr[1] == 'performance') claims-module-active @else claims-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Performance Reports">
						<a href="{{ url('reports/performance/list') }}" class="js_next_process"><i class="livicon left-pad" data-name="briefcase" data-size="30" data-color="@if($currnet_arr[1] == 'performance') #fff @else #c8ae02 @endif" data-hovercolor="#fff"></i></a>
					</div>
				</div>
			</div>
			
			<div class="col-sm-1 col-md-0 col-xs-2">
				<div class="padding-t-xs-5">
					<div class="module-menu-icon @if($currnet_arr[0] == 'reports' && $currnet_arr[1] == 'generated_reports') report-module-active @else report-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Generated Reports">
						<a href="{{ url('reports/generated_reports') }}" class="js_next_process"><i class="livicon left-pad" data-name="gears" data-size="30" data-color="@if($currnet_arr[1] == 'generated_reports') #fff @else #a967aa @endif" data-hovercolor="#fff"></i></a>
					</div>
				</div>
			</div> 
			
			
			
			<?php } ?>
			
			
			
			<!-- /.col -->    
			<?php
				if(Session::has('practice_dbid')) {				
					$id = Session::get('practice_dbid');
					$practice_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($id,'encode');
					$url = 'practice/'.$practice_id;
				} else {
					$url = 'practice/4';
				}
			?>
			@if($checkpermission->check_url_permission('practice') == 1 && $checkpermission->checkAllowToAccess('practice'))	
				<div class="col-sm-1 col-md-0 col-xs-2 <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
					<div class="padding-t-xs-5">
						<div class="module-menu-icon @if($routex[0] == 'patients' || $patient_current_page == 'patients' ||  (strpos($currnet_page, 'scheduler') !== false) == 'scheduler' || $routex[0] == 'dashboard' || $patient_current_page == 'dashboard' || $routex[0] == 'charges' || $patient_current_page == 'charges'  || $routex[0] == 'payments' || $patient_current_page == 'payments' || $routex[0] == 'claims' || $patient_current_page == 'claims' || $currnet_arr[0] == 'reports' || $patient_current_page == 'reports' || $routex[0] == 'documents' || $patient_current_page == 'documents' || $routex[0] == 'armanagement' || $ar_main_page == 'armanagement' || $currnet_arr[0] == 'analytics') practice-module @else practice-module-active  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Practice">                                
							<a href="{{ url($url) }}" class="js_next_process"><i class="livicon left-pad" data-name="medkit" data-size="30" data-color="@if($routex[0] == 'patients' || $patient_current_page == 'patients' || (strpos($currnet_page, 'scheduler') !== false) || $routex[0] == 'dashboard' || $patient_current_page == 'dashboard' || $routex[0] == 'charges' || $patient_current_page == 'charges' || $routex[0] == 'payments' || $patient_current_page == 'payments' || $routex[0] == 'claims' || $patient_current_page == 'claims' || $currnet_arr[0] == 'reports' || $patient_current_page == 'reports' || $routex[0] == 'documents' || $patient_current_page == 'documents' || $routex[0] == 'armanagement' || $ar_main_page == 'armanagement' || $currnet_arr[0] == 'analytics') #E30303 @else #fff @endif "  data-hovercolor="#fff"></i></a>
						</div>
					</div>
				</div>
			@endif
			<!-- /.col -->                                    
			<div class="col-sm-1 col-md-0 col-xs-2 <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
				<div class="padding-t-xs-5">
					<div class="module-menu-icon @if($routex[0] == 'documents' || $patient_current_page == 'documents') document-module-active @else document-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Documents">
						<a href="{{ url('documents') }}" class="js_next_process"><i class="livicon left-pad" data-name="folders" data-size="30" data-color="@if($routex[0] == 'documents' || $patient_current_page == 'documents') #fff @else #045a70 @endif" data-hovercolor="#fff"></i> </a>
					</div>                                 
				</div>
			</div>
			<!-- /.col -->           
		</div>
    @endif 
    <!-- /.row -->	
	
    @yield('toolbar')
</div>
<!-- /.box-body -->