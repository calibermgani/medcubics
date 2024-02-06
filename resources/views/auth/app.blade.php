<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Medcubics PMS</title>
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link href="<?php echo asset('/css/login-main.css') ?>" rel="stylesheet">

        <!-- Fonts -->
        <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/modal_style.css') }}">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
        <style>
            @import url(https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600,700);
            .testimonial-section {
                width: 100%;
                height: auto;
                padding: 18px;
                color:#00877f;
                text-align: center;
                position: relative;              
                font-size:15px;
                font-family: 'Open Sans', sans-serif;
            }
            .testimonial-section:after {
                top: 100%;
                left: 10%;
                border: solid transparent;
                content: " ";
                position: absolute;
                border-top-color: #fff;
                border-width: 15px;
                margin-left: -15px;
            }
            .testimonial-section-name {
                margin-top: 30px;
                margin-left: 60px;
                text-align:left;
                color:#000;
            }
            .testimonial-section-name img {
                max-width:40px;
                border: 2px solid #fff;
            }
            .carousel-indicators-set {
                position:static;
                margin-left:0px;
                width:100%;
            }
            .carousel {
                position: relative;
            }
            .carousel-inner {
                position: relative;
                width: 100%;
                overflow: hidden;
            }
            .carousel-inner > .item {
                position: relative;
                display: none;
                -webkit-transition: 1s ease-in-out left;
                -o-transition: 1s ease-in-out left;
                transition: 1s ease-in-out left;
            }
            .carousel-inner > .item > img,
            .carousel-inner > .item > a > img {
                line-height: 1;
            }
            @media all and (transform-3d), (-webkit-transform-3d) {
                .carousel-inner > .item {
                    -webkit-transition: -webkit-transform 1s ease-in-out;
                    -o-transition:      -o-transform 1s ease-in-out;
                    transition:         transform 1s ease-in-out;

                    -webkit-backface-visibility: hidden;
                    backface-visibility: hidden;
                    -webkit-perspective: 1000;
                    perspective: 1000;
                }
                .carousel-inner > .item.next,
                .carousel-inner > .item.active.right {
                    left: 0;
                    -webkit-transform: translate3d(100%, 0, 0);
                    transform: translate3d(100%, 0, 0);
                }
                .carousel-inner > .item.prev,
                .carousel-inner > .item.active.left {
                    left: 0;
                    -webkit-transform: translate3d(-100%, 0, 0);
                    transform: translate3d(-100%, 0, 0);
                }
                .carousel-inner > .item.next.left,
                .carousel-inner > .item.prev.right,
                .carousel-inner > .item.active {
                    left: 0;
                    -webkit-transform: translate3d(0, 0, 0);
                    transform: translate3d(0, 0, 0);
                }
            }
            .carousel-inner > .active,
            .carousel-inner > .next,
            .carousel-inner > .prev {
                display: block;
            }
            .carousel-inner > .active {
                left: 0;
            }
            .carousel-inner > .next,
            .carousel-inner > .prev {
                position: absolute;
                top: 0;
                width: 100%;
            }
            .carousel-inner > .next {
                left: 100%;
            }
            .carousel-inner > .prev {
                left: -100%;
            }
            .carousel-inner > .next.left,
            .carousel-inner > .prev.right {
                left: 0;
            }
            .carousel-inner > .active.left {
                left: -100%;
            }
            .carousel-inner > .active.right {
                left: 100%;
            }                     
            .carousel-fade .carousel-inner .item {
                opacity: 0;
                -webkit-transition-property: opacity;
                -moz-transition-property: opacity;
                -o-transition-property: opacity;
                transition-property: opacity;
            }
            .carousel-fade .carousel-inner .active {
                opacity: 1;
            }
            .carousel-fade .carousel-inner .active.left,
            .carousel-fade .carousel-inner .active.right {
                left: 0;
                opacity: 0;
                z-index: 1;
            }
            .carousel-fade .carousel-inner .next.left,
            .carousel-fade .carousel-inner .prev.right {
                opacity: 1;
            }
            .carousel-fade .carousel-control {
                z-index: 2;
            }

            .help-block {
                color : red;
            }  
        </style>
    </head>
    <body>
        <ul class="cb-slideshow">
            <li><span>Image 01</span></li>
            <li><span>Image 02</span></li>
            <li><span>Image 03</span></li>
            <li><span>Image 04</span></li> 
            <li><span>Image 05</span></li> 
            <li><span>Image 06</span></li> 
        </ul>
        <div class="container">
            <!-- Codrops top bar -->
            <div class="medcubic-top" style="border-bottom:1px solid #ccc;">
                <a href="">{!! HTML::image('images/logo.png', null,['alt' => 'medcubics', 'title' => 'medcubics','style'=>'width:251px;']) !!}</a>
                <span class="right line-height-24">                  
                    <!-- <a href="" target="_blank">{!! HTML::image('images/chat.png') !!} Live Chat</a> -->
                    <a href="{{ url('support') }}"><i class="fa fa-user"></i><span class="margin-l-5">Support</span></a><a style="color:#424c55;">|</a><a><i class="fa fa-phone-square"></i><span class="margin-l-5">(732) 795-5646</span></a>
                    <!--a href="" target="_blank">{!! HTML::image('images/settings.png') !!} Setting</a-->
                </span>
                <div class="clr"></div>
            </div><!-- Codrops top bar -->

            <!--         <div id="carousel-example" class="carousel slide carousel-fade" data-ride="carousel">
                         <div class="carousel-inner">
                             <div class="item active">
                                 <div class="testimonial-section">
                                     <p style="margin-bottom:0px; font-size: 36px; text-align: center; font-weight: 600; text-transform: uppercase"> <span style="color:#eeeeec">Innovative Technology</span>
                                         <span style="color:#eeeeec">Superior Performance</p>
                                 </div>                                    
                             </div>
                             <div class="item">
                                 <div class="testimonial-section">
                                     <p style="margin-bottom:0px; font-size: 36px; text-align: center; font-weight: 600; text-transform: uppercase"> <span style="color:#91a6bf">Anywhere,</span> 
                                         <span style="color:#91a6bf">Anytime Access</span> </p>
                                 </div>                                   
                             </div>
                             <div class="item">
                                 <div class="testimonial-section">
                                     <p style="margin-bottom:0px; font-size: 36px; text-align: center; font-weight: 600; text-transform: uppercase"> <span style="color:#8ba44c">Custom Tailored </span> 
                                         <span style="color:#8ba44c">Metrics</span></p>
                                 </div>                                    
                             </div>
                             <div class="item">
                                 <div class="testimonial-section">
                                     <p style="margin-bottom:0px; font-size: 36px; text-align: center;font-weight: 600;text-transform: uppercase"> <span style="color:#4367a8">Secured Data,</span> 
                                         <span style="color:#4367a8">HIPAA Compliance</span></p>
                                 </div>                                    
                             </div>
                             <div class="item">
                                 <div class="testimonial-section">
                                     <p style="margin-bottom:0px; font-size: 36px; text-align: center;font-weight: 600;text-transform: uppercase"> <span style="color:#4c758f">CARRY MEDCUBICS IN YOUR POCKET!</span>                                 
                                 </div>                                    
                             </div>
            -->                 
            <!-- INDICATORS -->
            <!--
                            </div>
                        </div>
            -->
                       <!-- <h1 style="margin-left: 40px;margin-top:20px;">Successful <span>Practice</span></h1>
                        <h1 style="margin-left:180px;"><span>Happy</span> Patients</h1>  -->

            <p class="alert alert-danger-message margin-t-10" style="display:none;">Error Message Comes Here ...</p>

            <header class="right" style="margin-top: 30px;">
                <p class="medcubic-login">
                    @yield('content')
                </p>         

                <h1 style="width: 100%;">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</h1>
            </header>
            <footer >
                <h3><a href="#js-help-modal" data-url="{{url('static/help/disclaimer')}}" class="js-help hide" data-toggle="modal">Disclaimer</a> | <a href="#js-help-modal" data-url="{{url('static/help/privacy_policy')}}" class="js-help hide" data-toggle="modal">Privacy Policy</a> | <a href="#js-help-modal" data-url="{{url('static/help/terms_and_conditions')}}" class="js-help hide" data-toggle="modal">Terms and Condition</a> | © <?php echo date('Y') ?> Medcubics. All Rights Reserved</h3>			
            </footer>
        </div>
        <div style="float:right; text-align: right;position:fixed; bottom:1px; right:0; margin-right: 20px;">
            
            <a style="display:none" href="https://twitter.com/clouddesigner1" target="_blank">{!! HTML::image('images/twitter.png') !!}</a>
            <a style="display:none" href="https://www.facebook.com/Cloud-Designers-1564492920529693/" target="_blank">{!! HTML::image('images/facebook.png') !!}</a>
            <a style="display:none" href="https://www.linkedin.com/sales/accounts/insights?companyId=10075700" target="_blank">{!! HTML::image('images/linkedin.png') !!}</a>
            
        </div>
        <!-- Help Modal Light Box starts -->  
        <div id="js-help-modal" class="modal fade in">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="js-help-modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <p id="js-help-modal-msg" style="word-wrap:break-word"></p>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal  Ends-->
        <!-- Scripts -->
        <?php
			App\Http\Helpers\CssMinify::minifyJs('common_js');
			App\Http\Helpers\CssMinify::minifyJs('form_js');
        ?>

        {!! HTML::script('js/'.md5("common_js").'.js') !!}
        {!! HTML::script('js/'.md5("form_js").'.js') !!}
        @stack('view.scripts')
        <script>
            $(".js-help").click(function () {
                url = $(this).attr('data-url');
                if (url != '')
                {
                    $.ajax({
                        url: url,
                        type: 'get',
                        data: '',
                        success: function (data, textStatus, jQxhr) {
                            split_result = data.split('~~');
                            $('#js-help-modal-msg').html(split_result[1]);
                            $('#js-help-modal-title').html(split_result[0]);
                        },
                        error: function (jqXhr, textStatus, errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                }
            });

            $('#carousel-example').carousel({
                interval: 6000 //TIME IN MILLI SECONDS
            });
            $("#div3").fadeIn(3000);
        </script>

    </body>
</html>
