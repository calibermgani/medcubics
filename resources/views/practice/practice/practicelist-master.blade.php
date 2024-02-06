<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
	<title>Medcubics PMS</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
 
	<link href='https://fonts.googleapis.com/css?family=Maven+Pro:400,500' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600,700' rel='stylesheet' type='text/css'>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  
	<link rel="stylesheet" href="<?php echo asset('assets/bootstrap/css/bootstrap-practice.min.css')?>" type="text/css">
	<link rel="stylesheet" href="<?php echo asset('assets/bootstrap/css/bootstrap-responsive.min.css')?>" type="text/css"> 
	<link rel="stylesheet" href="<?php echo asset('css/style-practicelist.css')?>" type="text/css"> 
	<link rel="stylesheet" href="<?php echo asset('css/style_responsive.css')?>" type="text/css"> 
	<link rel="stylesheet" href="<?php echo asset('css/style_default.css')?>" type="text/css">
	<link rel="stylesheet" href="<?php echo asset('assets/font-awesome/css/font-awesome.css')?>" type="text/css"> 
  
	<script>jQuery(document).ready(function(){App.init()});</script>
 
  
	<script type="text/javascript" src="{{ URL::asset('js/jquery-1.8.3.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/scripts.js') }}"></script>
  <!-- Animated Icons -->
	<script type="text/javascript" src="{{ URL::asset('js/animated-icons/livicons-1.4.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/animated-icons/json2.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/animated-icons/raphael.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/animated-icons/livicon.js') }}"></script>
</head>

<body  class="fixed-top1" style="background: #F5F4F5 url(img/body-bg.png); border-left:10px solid #007C76; border-right:10px solid #007C76;border-bottom:10px solid #007C76; " >
    <!-- Header Content Starts Here -->
	<div id="header" class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">			
			<div class="container-fluid"> 				
				<div class="col-lg-2 col-md-4 col-sm-4  ">
					<a class="logo" href="{{ url('/') }}">{!! HTML::image('img/logo-dash.png',null,['alt' => 'medcubics', 'title' => 'medcubics']) !!}</a>                           
				</div>
				
				<div class="col-lg-2 col-md-4 col-sm-4  ">
					<h4 class="slogan" style="text-align: center; margin-left: -90px;">&emsp;<br>RevExp, LLC</h4>
				</div>
							   
				<div class="span3 pull-right" style="text-align: right">
					<img src="img/dashboard-doctor.png" style="position: absolute; margin-left: -3%;">
					<ul style="list-style-type: none; line-height: 10px; font-size:13px; margin-right: 20%; color:#fff;">
						<li style="margin-bottom: -6px;">{!! Auth::user()->name !!}</li>
						<li>{!! Auth::user()->email !!}</li>
						<li><a href="{{ url('/auth/logout') }}" style="background: #fff; padding: 2px 6px; font-size:11px; border-radius: 4px; text-decoration: none;"> Logout </a></li>
					</ul>  					
				</div>
				   				
			</div>
		</div>
	</div>
	<script>jQuery(document).ready(function(){App.init()});</script>           
    <script type="text/javascript" src="{{ URL::asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
     
	<!-- Header content Ends Here -->        
       
	<div id="container">
		<center ><div class="span12" style="margin-top: 20px;">                   
				<h3 class="sloganhead" style="color:#00837C; margin-left: 100px;"><b>Successful</b> Practice Happy Patients</h3>
					</div></center>
		<div id="" >        
              <!-- Sub-header -->
			<div class="row-fluid circle-state-overview">
			   
				
				<div class="span2 responsive clearfix" data-tablet="span3" data-desktop="span2" >
					<div class="circle-wrap" >
					   <div class="module-menu-icon turquoise-color box-index" >
						 {!! HTML::image('img/content-icon1.png') !!}
					   </div>
					   
					   <h4 style="color:#8a8a8a;">32 </h4>
					   <h4>Appointment's</h4>
					</div>
				 </div>
				 <div class="span2 responsive" data-tablet="span3" data-desktop="span2" >
					<div class="circle-wrap" >
						<div class="module-menu-icon red-color box-index">
							{!! HTML::image('img/content-icon2.png') !!}
						</div>
					   
					   <h4 style="color:#8a8a8a;">47 </h4>
					   <h4>Unbilled</h4>
					</div>
				 </div>
				 <div class="span2 responsive" data-tablet="span3" data-desktop="span2" >
					<div class="circle-wrap">
						<div class="module-menu-icon green-color box-index">
							{!! HTML::image('img/content-icon3.png') !!}
						</div>
					   
					   <h4 style="color:#8a8a8a;">14 </h4>
					   <h4>Rejections</h4>
					</div>
				 </div>
				 <div class="span2 responsive" data-tablet="span3" data-desktop="span2" >
					<div class="circle-wrap">
					   <div class="module-menu-icon gray-color box-index">
							{!! HTML::image('img/content-icon4.png') !!}
					   </div>
					   
					   <h4 style="color:#8a8a8a;">18 </h4>
					   <h4>Charges</h4>
					</div>
				 </div>
				 <div class="span2 responsive" data-tablet="span3" data-desktop="span2" >
					<div class="circle-wrap">
					   <div class="module-menu-icon blue-color box-index">
						  {!! HTML::image('img/content-icon5.png') !!}
					   </div>
					   
					   <h4 style="color:#8a8a8a;">144 </h4>
					   <h4>Collections</h4>
					</div>
				 </div>
				
				<div class="span2 responsive" data-tablet="span3" data-desktop="span2" >
					<div class="circle-wrap">
					   <div class="module-menu-icon blue-color box-index">
						  {!! HTML::image('img/content-icon2.png') !!}
					   </div>
					   
					   <h4 style="color:#8a8a8a;">79.5% </h4>
					   <h4>Outstanding AR</h4>
					</div>
				</div>
					
			</div>
              <!-- MAin Modules Icon Starts -->
                
              
			<center>
				<div class="span5 offset4 ">
					<input type="text" class="form-control" placeholder="Search Practice" style="margin-left: -12px;" />
				</div>
			</center>
            <!-- Main Modules Ends -->
                          
            <!-- End Sub-header -->               
			<div class="container-fluid" >
				<!--Moodle 1 -->
				@yield('practice-info')
				<!--End Moodle 1 -->
				<!-- Body content Starts -->
				<div class="row-fluid ">
					<div class="span12">
						<div class="widget-main">
							<div class="widget-body">
								@yield('practice')
							</div>
						</div>
					</div>
				</div>
			</div>
		  
		  <!-- End Body Content -->            
             
		</div>
	</div>
	
	<script type="text/javascript" src="{{ URL::asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
 
	<!-- Footer Starts Here -->
	<div id="footer" style="color:#222;">
		2017 &copy; Medcubics Dashboard.         
		<div class="span pull-right">
			<span class="go-top">
				<i class="livicon"  data-name="chevron-up" data-color="#fff" data-size="18"></i>
			</span>
		</div>
	</div>
	<!-- Footer Ends Here -->
</body>

<script>
	$('tr').click( function() {
      window.location = $(this).find('a').attr('href');
	}).hover( function() {
      $(this).toggleClass('tablepointer');
	});
</script> 
</html>