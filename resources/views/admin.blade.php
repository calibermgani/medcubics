 <!DOCTYPE html>
<html>
    <?php 
        $routex = explode('/',Route::current()->uri());    
        $currnet_page = Route::getFacadeRoot()->current()->uri();		
        $patient_current_page = '';
		$profile_current_page = '';
		$patient_charges_page = '';
                $ar_main_page = '';
    ?>
    <head> 
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8" /> 
        <title>@yield('pageTitle', App\Http\Helpers\Helpers::getPageTitle() )</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="google" content="notranslate">
        <meta content="Medcubics Medical Billing Software" name="description" />
        <meta content="Medcubics" name="author" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <?php App\Http\Helpers\CssMinify::minifyCss(); ?>
        {!! HTML::style('css/'.md5("css_cache").'.css') !!}
        
        <script>
            window.dataLayer = window.dataLayer || [];
            window.history.forward();
            function noBack() {
                window.history.forward();
            }
        </script>
        @if(strpos($currnet_page, 'edit') !== false || strpos($currnet_page, 'create') !== false || (strpos($currnet_page, 'event') !== false) || (strpos($currnet_page, 'profile') !== false))
			{!! HTML::style('js/eventCalendar/style-simplecalendar.css') !!}
			{!! HTML::style('js/eventCalendar/timepicker.css') !!}
        @endif
        {!! HTML::style('plugins/timepicker/bootstrap-timepicker.css') !!}
        {!! HTML::style('https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600,700') !!} 
        {!! HTML::style('https://fonts.googleapis.com/css?family=Maven+Pro:400,500') !!}

        @if(strpos($currnet_page, 'scheduler') !== false)
			{!! HTML::style('js/fullcalendar/fullcalendar.min.css') !!}
			{!! HTML::style('js/fullcalendar/scheduler.min.css') !!}
        @endif
        {!! HTML::style('plugins/iCheck/all.css') !!}   
        {!! HTML::style('css/pace.css') !!}
        

        @if(strpos($currnet_page, 'reports') !== false || strpos($currnet_page, 'armanagement') !== false || strpos($ar_main_page, 'armanagement') !== false )
			<!-- This is for report chart Diagrams -->
			{!! HTML::style('css/morris.css') !!} 
        @endif

        @if(strpos($currnet_page, 'maillist') !== false)
			{!! HTML::style('css/bootstrap-colorpicker-plus.css') !!}
        @endif
		
        @if(strpos($currnet_page, 'message') !== false)
			{!! HTML::style('css/bootstrap-colorpicker-plus.css') !!}
        @endif
      
		<?php
			if(!Cache::has('sidebar_class')) {
				Cache::forever('sidebar_class', '');
				$sidebar_class = '';
			} else {
				$sidebar_class = Cache::get('sidebar_class');
			}

			$currnet_arr = explode('/', $currnet_page);
			if($currnet_arr[0] == 'patients') {
				$patient_current_page = 'patients';
			} elseif ($currnet_arr[0] == 'charges') {
				$patient_charges_page = 'charges';
			} elseif($currnet_arr[0] == 'profile') {
				$profile_current_page = 'profile';
			} elseif ($currnet_arr[0] == 'armanagement') {
				$ar_main_page = 'armanagement';
			}
		?>
    </head>
	    
    <body class="skin-blue sidebar-mini fixed {{$sidebar_class}}" onload="noBack();" onpageshow="if (event.persisted) noBack();" onunload="">    	           
        <div id="selLoading" class="loader" style="display: block;">            
			<?php /* Loader Image temporarily commented. 
			{!! HTML::image('img/load.gif',null,['class'=>'loaderImg']) !!}
			*/ ?>
        </div>
        <div class="wrapper"><!-- Site wrapper --> 
            @if( (strpos($currnet_page, 'support') !== false || $currnet_arr[0] == 'ticket' || $currnet_arr[0] == 'searchticket') && @Auth::user()->id=='')
				@include('layouts/front_header')<!-- Header Section logo practice name Starts -->	
            @else
                @if(Auth::user()->user_type == 'Practice')
                    @include('layouts/header-practice')
                @elseif(Auth::user()->user_type == 'Medcubics')
                    @include('layouts/header-medcubics')           
                @else
                @include('layouts/header')   
				 <!-- Header Section logo practice name Starts -->	
                @endif
            @endif	

            @if($currnet_page == 'admin/dashboard')
				@include('layouts/admin-dashboard-sidebar') <!-- Admin sidebar -->
            @elseif(strpos($currnet_page, 'adminuser') !== false || strpos($currnet_page, 'admin/userpassword') !== false)
				@include('layouts/admin-user-sidebar')
            @elseif($currnet_arr[0]== 'admin' && strpos($currnet_page, 'faq') !== false || strpos($currnet_page, 'errorlog') !== false || strpos($currnet_page, 'manageticket') !== false || strpos($currnet_page, 'managemyticket') !== false || strpos($currnet_page, 'createnewticket') !== false || strpos($currnet_page, 'admin/updates') !== false)
				@include('layouts/admin-ticket-sidebar')
            @elseif( $routex[0] == 'admin' && ($currnet_page == "admin/maintenance-sql" || $currnet_page == "admin/claimsintegrity"))
                @include('layouts/admin-maintenance-sql')	
            @elseif($currnet_arr[0] == 'admin' && $currnet_arr[1] == "apiconfig")
                @include('layouts/admin-maintenance-sql')    
            @elseif($routex[0] == 'admin' || ($currnet_page == 'admin/searchicd') ||($currnet_page == 'admin/searchcpt') || (strpos($currnet_page, 'customerusers/setpracticeforusers') !== false) || (strpos($currnet_page, 'practiceusers')  !== false ))
			@include('layouts/admin-sidebar') <!-- Maintenance sidebar -->
            @elseif($routex[0]=='scheduler' || (strpos($currnet_page, 'scheduler/list') !== false) || (strpos($currnet_page, 'scheduler/appointmentlist') !== false))<!--Main Scheduler sidebar -->
				@include('layouts/scheduler-sidebar') <!--Main Scheduler sidebar -->
            @elseif($routex[0]=='patients' || $patient_current_page =='patients'  )            
				@include('layouts/patient-sidebar') <!-- Patient sidebar -->
            @elseif($currnet_page=='analytics/practice' || strpos($currnet_page, 'dashboard') !== false || $currnet_page=='scheduling' || $currnet_page=='analytics/financials' || $currnet_page=='analytics/providers'|| $currnet_page=='ardashboard' || $currnet_page=='analytics/armanagement' || $currnet_page=='analytics/claims')
				@include('layouts/dashboard-sidebar') <!-- Patient sidebar -->				
            @elseif($currnet_page=='profile' || $profile_current_page =='profile')			
				@include('layouts/profile-sidebar') <!-- Patient sidebar -->
            @elseif($routex[0]=='charges')
				@include('layouts/charges-sidebar') <!-- Charges sidebar -->
            @elseif($currnet_arr[0] == 'reports')
				@include('layouts/reports-sidebar')
            @elseif($routex[0]=='payments')
				@include('layouts/payments-sidebar') <!-- Main payments sidebar -->				
            @elseif(strpos($currnet_page, 'charges') !== false)
				@include('layouts/charges-sidebar')
            @elseif(strpos($currnet_page, 'payments') !== false)
				@include('layouts/payments-sidebar') <!-- Main payments sidebar -->                       
            @elseif($currnet_arr[0] == 'claims')
				@include('layouts/claims-sidebar')
            @elseif($routex[0]=='armanagement' || $ar_main_page == 'armanagement')
				@include('layouts/armanagement-sidebar') <!-- Charges sidebar -->
            @elseif($currnet_arr[0] == 'support' || $currnet_arr[0] == 'myticket' || $currnet_arr[0] == 'ticket' || $currnet_arr[0] == 'searchticket')	
				@include('layouts/support_sidebar') <!-- support sidebar -->
            @elseif($currnet_page == 'admin/metrics')
				@include('layouts/admin-metrics-sidebar') <!-- Metrics sidebar -->
            @elseif($currnet_page == 'admin/reports' || strpos($currnet_page, 'invoice') !== false)
				@include('layouts/admin-reports-sidebar') <!-- Reports sidebar -->         
            @elseif($currnet_arr[0] == 'admin' && $currnet_arr[1] == "userLoginHistory")
                @include('layouts/admin-sidebar') <!-- Maintenance sidebar -->
             <!-- Maintenance sidebar -->
			 @elseif($currnet_page == 'trail/provider/create')
				@include('layouts/practice-sidebar-trail')
            @else
				@include('layouts/practice-sidebar') <!-- Practice sidebar -->
            @endif
            <div class="content-wrapper"><!-- Content Wrapper. Contains page content -->
                @if(! Auth::user())  
                <div class="box-body margin-t-m-18">
                    @yield('toolbar')
                </div>
                @endif
                
                <?php $user_details = Auth::user(); ?>
                @if(strpos($currnet_page, 'charges/create/') !== 0 && $user_details != '')
					@if(Session::get('practice_dbid') == "" && $user_details->user_type == 'Medcubics')
						@include('layouts/admin-main-modules') <!-- Content Header (Page header) -->
					@else
						@include('layouts/main-modules') 	<!-- Content Header (Page header) -->
					@endif
                @endif
                <section class="content margin-b-20" ><!-- Inner Body Content Starts -->
                    <div class="row">
                        <div class="col-lg-12">                            

                        </div>
                        <div id="js-print-main-div">
                            @yield('practice-info')
                            @yield('practice')
                            <!-- Only for print preview screen starts-->
                            <div class="col-lg-12 hidden-lg hidden-md hidden-sm hidden-xs hidden-phone visible-print pull-right"><a target="blank">{!! HTML::image('img/logo.png',null,['class'=>'pull-right', 'alt' => 'medcubics', 'title' => 'medcubics' ]) !!}</a></div>
                            
                            <!-- Only for print preview screen ends -->
                        </div>                        
                    </div>                    
                </section><!-- Inner Body Content Ends -->            
            </div><!-- content-wrapper Ends -->

            <!-- Add the sidebar's background. This div must be placed
                 immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
            <div class="snackbar-div">
                <h3><span id="show_error_type">Success</span> <i class="fa fa-close pull-right font12 form-cursor med-gray m-r-m-10 margin-t-2"></i></h3>
                <p id="show_error_msg">Your data updated successfully.</p>
            </div>
        </div><!-- Site wrapper Ends -->

        <!-- Popup modal file start [admin.blade file split] -->
        @include('layouts/popupmodal') 
        <!-- Popup modal file end -->

        <!-- Common error message start [admin.blade file split] -->
        @include('layouts/script_lang_msg') 
        @include('layouts/notification')    
        @include('layouts/footer')
        <!-- Footer Section Ends -->
        @stack('transaction_date_scripts')
		<!-- Pace loading js -->
		{!! HTML::script('js/pace.min.js') !!}
		@if($currnet_page == 'admin/claimsintegrity')
        {!! HTML::script('js/documents_module.js') !!}
        @endif
        @if($currnet_page == 'myticket')
        {!! HTML::script('js/function.js') !!}
        @endif
		<script>
			$(function () {
				// Disable links and submit button before page getting loaded.
				// $('section.content input:submit').prop('disabled',true);				
				// $("section.content a").css("pointer-events", "none");
			});
			$(document).ajaxStart(function() { 
				//displayLoadingImage();
				Pace.restart();
			});
            paceOptions = {
                ajax: {trackMethods: ['GET', 'POST', 'PUT', 'DELETE', 'REMOVE']},
                document: true,
                restart: true,
                eventLag: false
            };

            Pace.on('done', function() {
                $('#preloader').delay(500).fadeOut(800);
				hideLoadingImage();
				//$("section.content a").css("pointer-events", "");
				//$('input:submit').prop('disabled',false);
            });
          
        </script>
		<!-- Pace loading js -->		
    </body>
    <!-- Footer Section Starts -->	
</html>