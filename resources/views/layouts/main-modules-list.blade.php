<div class="box-body hidden-print" style="margin-top: -35px;">
    <?php
		$currnet_arr = explode('/', $currnet_page);
		if ($currnet_arr[0] == 'patients') {
			$patient_current_page = 'patients';
		} elseif ($currnet_arr[0] == 'dashboard') {
			$patient_current_page = 'dashboard';
		} elseif ($currnet_arr[0] == 'charges') {
			$patient_current_page = 'charges';
		}
    ?>  
    <div class="row">
        @if($checkpermission->check_url_permission('payments') == 1)
        <div class="col-lg-0 col-sm-1 col-md-0  col-xs-2 col-md-offset-3 col-sm-offset-1 col-xs-offset-1">
            <div class="module-menu-icon @if($routex[0] == 'dashboard' || $patient_current_page == 'dashboard') dash-module-active @else dash-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Dashboard">                              
                <a href="{{ url('analytics/practice') }}" class="js_next_process"><i class="livicon left-pad" data-name="dashboard" data-size="30" data-color="@if($routex[0] == 'dashboard' || $patient_current_page == 'dashboard') #fff @else #429b9b @endif" data-hovercolor="#fff"></i></a>
            </div>
        </div>
        @endif
        <!-- /.col -->  
        @if($checkpermission->check_url_permission('scheduler') == 1)                                  
        <div class="col-sm-1 col-md-0 col-xs-2">
            <div class="module-menu-icon @if($routex[0] == 'scheduler') scheduler-module-active @else scheduler-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Scheduler">
                <a href="{{ url('scheduler/scheduler') }}" class="js_next_process"><i class="livicon left-pad" data-name="calendar" data-size="30" data-color="@if($routex[0] == 'scheduler') #fff @else #4685f9 @endif"  data-hovercolor="#fff"></i></a>
            </div>
        </div>
        @endif
        <!-- /.col --> 
        @if($checkpermission->check_url_permission('patients') == 1)                                      
        <div class="col-sm-1 col-md-0 col-xs-2">						
            <div class="module-menu-icon @if($routex[0] == 'patients' || $currnet_arr[0] == 'uploadedpatients' || $patient_current_page == 'patients') patient-module-active @else patient-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Patients">                                
                <a href="{{ url('patients') }}" class="js_next_process"><i class="livicon left-pad" data-name="users" data-size="30" data-color="@if($routex[0] == 'patients' || $currnet_arr[0] == 'uploadedpatients' || $patient_current_page == 'patients') #fff @else #DB5C87 @endif"  data-hovercolor="#fff"></i></a>
            </div>
        </div>
        @endif
        <!-- /.col --> 
        @if($checkpermission->check_url_permission('charges') == 1)                                    
        <div class="col-sm-1 col-md-0 col-xs-2">
            <div class="module-menu-icon @if($routex[0] == 'charges' || $patient_current_page == 'charges') charge-module-active @else charge-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Charges">
                <a href="{{ url('charges') }}" class="js_next_process"><i class="livicon left-pad" data-name="list" data-size="30" data-color="@if($routex[0] == 'charges' || $patient_current_page == 'charges') #fff @else #46cdf9 @endif"  data-hovercolor="#fff"></i></a>
            </div>
        </div>
        @endif
        <!-- /.col --> 
        @if($checkpermission->check_url_permission('payments') == 1)
        <div class="col-sm-1 col-md-0 col-xs-2 ">
            <div class="module-menu-icon @if($routex[0] == 'payments' || $patient_current_page == 'payments' || $currnet_page == 'payments/get-e-remittance' || $currnet_page == 'payments') payments-module-active @else payments-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Payments">
                <a href="{{ url('payments') }}" class="js_next_process"><i class="livicon left-pad" data-name="money" data-size="30" data-color="@if($routex[0] == 'payments' || $patient_current_page == 'payments' || $currnet_page == 'payments/get-e-remittance' || $currnet_page == 'payments' ) #fff @else #8cc503 @endif" data-hovercolor="#fff"></i></a>
            </div>
        </div>
        @endif
        <!-- /.col -->  

        <div class="col-sm-1 col-md-0 col-xs-2 col-xs-offset-1 col-lg-offset-0 col-md-offset-0 col-sm-offset-0 ">
            @if($checkpermission->check_url_permission('armanagement') == 1)
            <div class="padding-t-xs-5">
                <div class="module-menu-icon @if($routex[0] == 'armanagement' && $ar_main_page == 'armanagement') ar-module-active @else ar-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="AR Management">
                    <a href="{{ url('armanagement/armanagementlist') }}" class="js_next_process"><i class="livicon left-pad" data-name="laptop" data-size="30" data-color="#b7375c"  data-hovercolor="#fff"></i></a>
                </div>
            </div>
            @endif
        </div>
        <!-- /.col -->                                    
        <div class="col-sm-1 col-md-0 col-xs-2">
            @if($checkpermission->check_url_permission('claims') == 1)        
            <div class="padding-t-xs-5">
                <div class="module-menu-icon @if($routex[0] == 'claims' || $patient_current_page == 'claims') claims-module-active @else claims-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Claims">
                    <a href="{{ url('claims/status/electronic') }}" class="js_next_process"><i class="livicon left-pad" data-name="shopping-cart" data-size="30" data-color="@if($routex[0] == 'claims' || $patient_current_page == 'claims') #fff @else #c8ae02 @endif" data-hovercolor="#fff"></i></a>
                </div>
            </div>
            @endif
        </div>
        <!-- /.col -->                                    
        <div class="col-sm-1 col-md-0 col-xs-2">
            @if($checkpermission->check_url_permission('reports') == 1)      
            <div class="padding-t-xs-5">
                <div class="module-menu-icon @if($currnet_arr[0] == 'reports' || $patient_current_page == 'reports') report-module-active @else report-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Reports">
                    <a href="{{ url('reports/financials/list') }}" class="js_next_process"> <i class="livicon left-pad" data-name="barchart" data-size="30" data-color="@if($currnet_arr[0] == 'reports' || $patient_current_page == 'reports') #fff @else #a967aa @endif" data-hovercolor="#fff"></i></a>
                </div>
            </div>
            @endif
        </div>
        <!-- /.col --> 
        <?php 
			$practice_id = (Session::has('practice_dbid')) ? Session::get('practice_dbid') : "4";
			$id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice_id,'encode'); 
		?>
		@if($checkpermission->checkAllowToAccess('practice'))
	        <div class="col-sm-1 col-md-0 col-xs-2">            
	            <div class="padding-t-xs-5">
	                <div class="module-menu-icon @if($routex[0] == 'patients' || $patient_current_page == 'patients' || $patient_current_page == 'patients' || $routex[0] == 'scheduler' || $routex[0] == 'dashboard' || $patient_current_page == 'dashboard' || $routex[0] == 'charges' || $patient_current_page == 'charges'  || $routex[0] == 'payments' || $patient_current_page == 'payments' || $currnet_page == 'payments/get-e-remittance' || $routex[0] == 'claims' || $patient_current_page == 'claims' || $currnet_arr[0] == 'reports' || $routex[0] == 'documents' || $patient_current_page == 'documents' || $ar_main_page == 'armanagement' || $routex[0] == 'armanagement' || $currnet_page == 'payments' || $currnet_arr[0] == 'uploadedpatients' ) practice-module @else practice-module-active  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Practice">                                
	                    <a href="{{ url('practice/'.$id) }}" class="js_next_process"><i class="livicon left-pad" data-name="medkit" data-size="30" data-color="@if($routex[0] == 'patients' || $patient_current_page == 'patients' || $routex[0] == 'scheduler' || $routex[0] == 'dashboard' || $patient_current_page == 'dashboard' || $routex[0] == 'charges' || $patient_current_page == 'charges' || $routex[0] == 'payments' || $currnet_page == 'payments/get-e-remittance' || $patient_current_page == 'payments' || $routex[0] == 'claims' || $patient_current_page == 'claims' || $currnet_arr[0] == 'reports' || $routex[0] == 'documents' || $patient_current_page == 'documents' || $ar_main_page == 'armanagement' || $routex[0] == 'armanagement' || $currnet_page == 'payments' || $currnet_arr[0] == 'uploadedpatients' ) #E30303 @else #fff @endif "  data-hovercolor="#fff"></i></a>
	                </div>
	            </div>            
	        </div>
		@endif
        <!-- /.col -->                                    
        <div class="col-sm-1 col-md-0 col-xs-2">
            @if($checkpermission->check_url_permission('documents') == 1)      
            <div class="padding-t-xs-5">
                <div class="module-menu-icon @if($routex[0] == 'documents' || $patient_current_page == 'documents') document-module-active @else document-module  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Documents">
                    <a href="{{ url('documents') }}" class="js_next_process"> <i class="livicon left-pad" data-name="folders" data-size="30" data-color="@if($routex[0] == 'documents' || $patient_current_page == 'documents') #fff @else #045a70 @endif" data-hovercolor="#fff"></i> </a>
                </div>
            </div>
            @endif                    
        </div>
        <!-- /.col -->                                                                                                                                                             
    </div>
    <!-- /.row -->
    @yield('toolbar')
</div>
<!-- /.box-body -->