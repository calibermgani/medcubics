<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Medcubics PMS</title>
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('signup/assets/css/materialdesignicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('signup/assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('signup/assets/css/login.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            .help-block {
                color : red;
            }

            .scrollbar {                
                height: 435px;                
                overflow-x:hidden;
            }
        </style>
    </head>
    <body>
        <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
            <div class="container">
                <div class="card login-card">
                    <div class="row no-gutters">
                        <div class="col-md-5">
                            {!! HTML::image('signup/assets/images/login.jpg', null,['alt' => 'medcubics', 'class'=>'login-card-img', 'title' => '','style'=>'']) !!}
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <div class="brand-wrapper">
                                    {!! HTML::image('signup/assets/images/logo.png', null,['alt' => 'medcubics', 'title' => 'medcubics','style'=>'']) !!}
                                </div>
                                <h3 class="login-card-description">Signup for a free trial</h3>
                                <p>Complete engagement and better healthcare for your patients</p>
                                <div class="scrollbar">
                                    @yield('content')
                                </div>
                                <!-- forgot -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Scripts -->
        {!! HTML::script('signup/js/bootstrap.min.js')!!}
        {!! HTML::script('signup/js/jquery.min.js')!!}
        {!! HTML::script('signup/js/popper.min.js')!!}
        <?php
        App\Http\Helpers\CssMinify::minifyJs('common_js');
        App\Http\Helpers\CssMinify::minifyJs('form_js');
        ?>

        {!! HTML::script('js/'.md5("common_js").'.js') !!}
        {!! HTML::script('js/'.md5("form_js").'.js') !!}
        {!! HTML::script('plugins/input-mask/jquery.inputmask.js?'.mt_rand())!!}
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