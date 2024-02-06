<!doctype html>
<html>
    <?php 
        $routex = explode('.',Route::currentRouteName());  
        $currnet_page = Route::getFacadeRoot()->current()->uri();       
        $patient_current_page = '';
        $profile_current_page = '';
        $patient_charges_page = '';
                $ar_main_page = '';
    ?>

    <head>
		<meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8" />
        <title>@yield('pageTitle', App\Http\Helpers\Helpers::getPageTitle())</title>  
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="google" content="notranslate">
        <meta content="" name="description" />
        <meta content="" name="author" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <?php App\Http\Helpers\CssMinify::minifyCss(); ?>
        {!! HTML::style('css/'.md5("css_cache").'.css') !!}
        @if(strpos($currnet_page, 'scheduler1') !== false || strpos($currnet_page, 'scheduler2') !== false)
        {!! HTML::style('js/fullcalendar/fullcalendar.min.css') !!}
        {!! HTML::style('js/fullcalendar/scheduler.min.css') !!}
        @endif

        {!! HTML::style('https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600,700') !!} 
        {!! HTML::style('https://fonts.googleapis.com/css?family=Maven+Pro:400,500') !!}         
        {!! HTML::style('plugins/iCheck/all.css') !!}
        {!! HTML::style('css/pace.css') !!}

        <style type="text/css">            
            .highlight {background: #faefaf;color: #94852e; }
            #filefield1{overflow:hidden;}
            .js_wait_alert_confirm{
                z-index: 9999;
                position: absolute;
                width: 100%;
                height: 100%;
                text-align: center;
                background-color: white;
                background: -webkit-gradient(linear, left top, right top, color-stop(0%, rgba(255,255,255,0)), color-stop(10%, rgba(255,255,255,0.9)), color-stop(90%, rgba(255,255,255,0.9)), color-stop(100%, rgba(255,255,255,0)));
                background: -webkit-linear-gradient(left, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 10%, rgba(255,255,255,0.9) 90%, rgba(255,255,255,0) 100%);
                background: -moz-linear-gradient(left, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 10%, rgba(255,255,255,0.9) 90%, rgba(255,255,255,0) 100%);
                background: -ms-linear-gradient(left, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 10%, rgba(255,255,255,0.9) 90%, rgba(255,255,255,0) 100%);
                background: -o-linear-gradient(left, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 10%, rgba(255,255,255,0.9) 90%, rgba(255,255,255,0) 100%); 
                background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 10%, rgba(255,255,255,0.9) 90%, rgba(255,255,255,0) 100%);
            }
            .js_processing_image{ top: 50%; }

        </style>
       
    </head>

    <?php
		if (!Cache::has('sidebar_class')) {
			Cache::forever('sidebar_class', '');
			$sidebar_class = '';
		} else {
			$sidebar_class = Cache::get('sidebar_class');
		}

		$currnet_arr = explode('/', $currnet_page);
		if ($currnet_arr[0] == 'patients') {
			$patient_current_page = 'patients';
		}
    ?>

    <body class="skin-blue collapsed sidebar-collapse fixed" style="border-left: 10px solid #00877f; border-right: 10px solid #00877f;">        
        <div id="selLoading" class="loader" style="display: none;">
            <?php /* Loader Image temporarily commented. 
			{!! HTML::image('img/load.gif',null,['class'=>'loaderImg']) !!}
			*/ ?>
        </div>
        <div class="wrapper"><!-- Site wrapper -->
             @if(Auth::user()->user_type == 'Practice')
                 @include('layouts/header-practice')
            @elseif(Auth::user()->user_type == 'Medcubics')
                @include('layouts/header-medcubics')           
            @else
                @include('layouts/header')   
            @endif
            <div class="content-wrapper space20"><!-- Content Wrapper. Contains page content -->
                @if($routex[0] == 'admin')
                @include('layouts/admin-main-modules') <!-- Content Header (Page header) -->
                @else
                @include('layouts/main-modules-list') <!-- Content Header (Page header) -->
                @endif

                <section class="content" ><!--Stats  Inner Body Content Starts -->
                    <div class="row">                                                 
                        @if($currnet_page == 'charges' || strpos($currnet_page, "charges") !== false)
                        <?php $module = 'charges' ?>
                        @elseif($routex[0] == 'charges1')
                        <?php $module = 'charges1' ?>                    
                        @elseif($routex[0] == 'payments')
                        <?php $module = 'payments' ?>                        
                        @elseif($routex[0] == 'claims')
                        <?php $module = 'claims' ?>                       
                        @elseif($routex[0] == 'reports')
                        <?php $module = 'report' ?>
                        @elseif($routex[0] == 'documents')
                        <?php $module = 'documents' ?> 
                        @include('documents/documents/stats')
                        @elseif($currnet_page == 'patients' || $currnet_page == 'uploadedpatients')
                        <?php $module = 'patients' ?>
                        @endif

                        @if($currnet_page == 'payments/get-e-remittance')
                        <?php $module = 'payments' ?>
                        @endif
                        <!--Stats Document list  -->	
                        @if($routex[0] != 'documents')
                        @include('reports/reports/stats')    
                        @endif		
                        <section class="content margin-b-20" ><!-- Inner Body Content Starts -->
                            <div class="row">
                                <div class="col-lg-12">                            

                                </div>
                                <div id="js-print-main-div">
                                    @yield('practice-info')
                                    @yield('practice')
                                    <!-- Only for print preview screen starts-->
                                    <div class="col-lg-12 hidden-lg hidden-md hidden-sm hidden-xs hidden-phone visible-print pull-right"><a href="https://medcubics.com/" target="blank">{!! HTML::image('img/logo.png',null,['class'=>'pull-right', 'alt' => 'medcubics', 'title' => 'medcubics']) !!}</a></div>
                                    <!-- Only for print preview screen ends -->
                                </div>  
                            </div>   
                        </section><!-- Inner Body Content Ends -->                        
                    </div>
                </section><!--Stats Inner Body Content Ends -->
            </div><!-- content-wrapper Ends -->

        </div><!-- Site wrapper Ends -->

        <!-- Sidebar Notification Start  -->
        <div class="snackbar-div">
            <h3><span id="show_error_type">Error</span> <i class="fa fa-close pull-right font12 form-cursor med-gray m-r-m-10 margin-t-2"></i></h3>
            <p id="show_error_msg">Your data updated successfully.</p>
        </div>
        <!-- Sidebar Notification End  -->
        @include('layouts/popupmodal') 
        @include('layouts/script_lang_msg') 
        @include('layouts/notification')  
        @include('layouts/stats_footer')
        <!-- Footer Section Ends -->
		
		<!-- Pace loading js -->
		
		{!! HTML::script('js/pace.min.js') !!}
		
		<script>
			$(function () {
				// Disable links and submit button before page getting loaded.
				// $('section.content input:submit').prop('disabled',true);				
				// $("section.content a").css("pointer-events", "none");
			});
			$(document).ajaxStart(function() { 				
				//displayLoadingImage();
				// $('input:submit').prop('disabled',true);				
				// $("section.content a:not('.js-search-patient')").css("pointer-events", "none");
			});
            paceOptions = {
                ajax: true,
                document: true,
                eventLag: false
            };
            Pace.on('done', function() {
                $('#preloader').delay(500).fadeOut(800);
				//hideLoadingImage();
				// $("section.content a").css("pointer-events", "");
				// $('input:submit').prop('disabled',false);
            });
            
        </script>
		
		<!-- Pace loading js -->
		
		
    </body>
    <!-- Footer Section Starts -->  
</html>