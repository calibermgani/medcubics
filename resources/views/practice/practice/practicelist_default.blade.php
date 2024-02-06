<!doctype html>
<html>
    <head>
        <?php 
            $routex = explode('.',Route::currentRouteName());  
            $currnet_page = Route::getFacadeRoot()->current()->uri();       
            $patient_current_page = '';
            $profile_current_page = '';
            $patient_charges_page = '';
                    $ar_main_page = '';
        ?>
        <meta charset="utf-8" />
        <title>Medcubics PMS</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
		<style>            
            .highlight {background: #FDE47D !important;color:#007F78 !important; font-weight: bold;}
			.dataTables_filter{ display:none; }
        </style>
        <?php App\Http\Helpers\CssMinify::minifyCss(); ?>
        {!! HTML::style('css/'.md5("css_cache").'.css') !!}

        {!! HTML::style('https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600,700') !!} 
        {!! HTML::style('https://fonts.googleapis.com/css?family=Maven+Pro:400,500') !!}            
    </head>
    <body class="collapsed sidebar-collapse fixed p-page-border">        
        <div class="wrapper"><!-- Site wrapper -->
            <div class="content-wrapper">
                <section class="content margin-b-20" ><!-- Inner Body Content Starts -->
                    <div class="row">
                        <div class="navbar navbar-inverse navbar-fixed-top">
                            <div class="navbar-inner">
                                <div class="container-fluid"> 
                                    <div class="col-lg-4 col-md-5 col-sm-2 col-xs-3">
                                        <a href="{{ url('/') }}" class="p-logo-height">{!! HTML::image('img/logo-dash.png',null,['alt' => 'medcubics', 'title' => 'medcubics']) !!}</a>                           
                                    </div><span class="customer-img-width">

                                        <?php	
											$get_CustomerImg = App\Models\Practice::getCustomerImg();  
											$filename =@$get_CustomerImg[0] . '.' .@$get_CustomerImg[1];
											$img_details = [];
											$img_details['module_name']='customers';
											$img_details['file_name']=$filename;
											$img_details['practice_name']='admin';
											
											$img_details['style']='width: 21px; height: 21px; border-radius: 50%;';
											$img_details['alt']='customer-image';
											$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
										?>
                                    </span>

                                    <div class="col-lg-4 col-md-3 hidden-sm hidden-xs">

                                        <h4 class="customer-name">{!! @$image_tag !!} {{@$customer_name}}</h4>
                                    </div>
                                    <?php //dd(@$customer->avatar_name);?>

                                    <div class="col-lg-4 col-md-3 col-sm-4 pull-right p-text-right">
                                        <img src="img/dashboard-doctor.png" class="hidden-xs p-user-img">
                                        <ul class="practice-list-xs user-ul">
                                            <li class="m-b-m-5">{!! Auth::user()->short_name !!}</li>
                                            <li>{!! Auth::user()->designation !!} </li>
                                            <li><a href="{{ url('/auth/logout') }}" class="p-logout-btn"> Logout &nbsp;<i class="livicon m-r-0" data-name="sign-out" data-color="#fff" data-size="13" data-hovercolor="#F07D08"></i> </a></li>
                                        </ul>  
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 margin-b-20 p-stats">
                            
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 practice-statis">                    
                                <div class="practice-icons" >
                                    {!! HTML::image('img/stat-problem-list.png') !!}
                                </div>
                                <h4 class="med-orange">{{ $stats_details['month_prob_list'] }} </h4>
                                <h5 class="margin-t-m-5 med-green font16">Workbench</h5>
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 practice-statis">
                                <div class="practice-icons">
                                    {!! HTML::image('img/content-icon7.png') !!}
                                </div>
                                <h4 class="med-orange">{!! $stats_details['unbilled'] !!} </h4>
                                <h5 class="margin-t-m-5 med-green font16">Unbilled Charges</h5>                               
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 practice-statis">
                                <div class="practice-icons">
                                    {!! HTML::image('img/content-icon6.png') !!}
                                </div>
                                <h4 class="med-orange">${{ App\Http\Helpers\Helpers::priceFormat($stats_details['rejection']) }}</h4>
                                <h5 class="margin-t-m-5 med-green font16">EDI Rejections</h5>                                
                            </div>
							
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 practice-statis">                    
                                <div class="practice-icons">
                                    {!! HTML::image('img/content-icon5.png') !!}
                                </div>
                                <h4 class="med-orange">{!! $stats_details['charges'] !!}</h4>
                                <h5 class="margin-t-m-5 med-green font16">Billed Charges</h5>
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 practice-statis">                   
                                <div class="practice-icons med-green font16">
                                    {!! HTML::image('img/content-icon8.png') !!}
                                </div>
                                <h4 class="med-orange">{!! $stats_details['collection'] !!} </h4>
                                <h5 class="margin-t-m-5 med-green font16">Total Collections</h5>
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 practice-statis">
                                <div class="practice-icons">
                                    {!! HTML::image('img/content-icon2.png') !!}
                                </div>
                                <h4 class="med-orange">{!! $stats_details['outstanding'] !!}</h4>
                                <h5 class="margin-t-m-5 med-green font16">Outstanding AR</h5>                                
                            </div>                                                     

                        </div>

                        <div class="col-lg-12 margin-t-20">
                            <div class="col-lg-4 pull-right p-r-0">                   
                                <input type="text" name="" placeholder="Search Practice" class="form-control js-search-practice">
                            </div>
                        </div>
                        <!-- Inner Body Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            @if(Session::get('error')!== null) 
                            <p class="alert alert-error" id="success-alert">{{ Session::get('error') }}</p>
                            @endif
                        </div>

                        <div class="col-lg-12 padding-10">
                            @yield('practice-info')
                            @yield('practice')
                        </div>   
                    </div>
                </section>
            </div>
        </div><!-- Site wrapper Ends -->

        @include('layouts/script_lang_msg')         
    </body>
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <a href="https://medcubics.com" class="med-white" target="_blank">Medcubics</a>
        </div>
        Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.
    
        <script type="text/javascript">
            var api_site_url = '{{url('/')}}';
            var chk_env_site = '{{ getenv("APP_ENV") }}';
        </script>    
        <?php App\Http\Helpers\CssMinify::minifyJs('common_js'); ?>       
        {!! HTML::script('js/'.md5("common_js").'.js') !!}
        {!! HTML::script('plugins/timepicker/bootstrap-timepicker.min.js') !!}  
        <?php App\Http\Helpers\CssMinify::minifyJs('datatables_js'); ?>
        {!! HTML::script('js/'.md5("datatables_js").'.js') !!}      
        {!! HTML::script('js/datatables/datatable_search_highlight.js') !!}  

        <!-- Ends Here -->
        <script type="text/javascript">
            var api_site_url = '{{url('/')}}';
            $("#success-alert").hide();

            $("#success-alert").alert();
            $("#success-alert").fadeTo(3000, 500).slideUp(500, function () {
                $("#success-alert").alert('close');
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching"     : true,
                "oSearch": { "bSmart": false, "bRegex": true },
                "aoColumns": [ 
                    {"bSearchable": true},
                    {"bSearchable": false},
                    {"bSearchable": false},
                    {"bSearchable": false},
                    {"bSearchable": false},
                    {"bSearchable": false},
                    {"bSearchable": false},
                    {"bSearchable": false},
                    {"bSearchable": false}
                ],
                "ordering": true,
                "info": false,
                "searchHighlight": true,
                "fixedHeader": true,
                "responsive": true,
                "autoWidth": true
            });
            /*** Documents search with highlight function start ***/
            $('.js-search-practice').on('keyup',function(){
                $('#example2 tbody tr td').unhighlight();
                $('.dataTables_filter input[type="search"]').val($(this).val()).trigger("keyup");
                $("#example2 tbody tr td:not(.dataTables_empty)").highlight($(this).val());
            });
            /*** Documents search with highlight function end ***/   
        </script>
        <script>
        <?php if(Request::segment(1) != 'admin'){ ?>
        document.addEventListener('visibilitychange', function(){
            if(document.visibilityState == 'visible'){
                var prv_id = "<?php echo Session::get('practice_dbid'); ?>";
                var ajaxUrl = "<?php echo url('/get_practice_session_id'); ?>";
                $.ajax({
                    type: "GET",        
                    url: ajaxUrl,       
                    dataType: "json",   
                    success: function (json) {
                        var current_id = json.id;
                        var role_id = json.role_id;
						if(role_id == 0){
						  if(prv_id != current_id) {
								js_alert_popup("{{ trans('practice/practicemaster/practice.validation.practice_change') }}");
								$(".js_note_confirm").addClass('practice_change');  
							}   
						}                      
                    }
                });
            }
        });
         $(document).on('click',".practice_change",function(){
            location.reload();
        });
        </script>
        <?php
            App\Http\Helpers\CssMinify::minifyJs('app_js');
            App\Http\Helpers\CssMinify::minifyJs('function_js');
        ?>             
        {!! HTML::script('js/'.md5("app_js").'.js') !!}        
        {!! HTML::script('js/function.js') !!}  
        {!! HTML::script('js/'.md5("datatables_js").'.js') !!}
        {!! HTML::script('js/datatables/datatable_search_highlight.js') !!}
        
        <?php } ?>
        </script>
        <style>
            table.dataTable thead .sorting_asc:after{    color: #00837C !important;}
            table.dataTable thead .sorting_desc:after{    color: #00837C !important;}
        </style>
        
        @stack('view.scripts1')
        @stack('view.scripts')
    </footer>        
</html>