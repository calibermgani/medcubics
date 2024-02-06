<header class="main-header"><!-- Header Starts Here -->
	<a href="{{url('/')}}" class="logo"><!-- Logo -->                    
		<span class="logo-mini">{!! HTML::image('img/cube.png',null,['alt' => 'medcubics', 'title' => 'medcubics']) !!}</span><!-- mini logo for sidebar mini 50x50 pixels -->                    
		<span class="logo-lg">{!! HTML::image('img/logo.png',null,['alt' => 'medcubics', 'title' => 'medcubics']) !!}</span><!-- logo for regular state and mobile devices -->
	</a>

	<nav class="navbar navbar-static-top" role="navigation"><!-- Header Navbar -->
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				<li class="hidden-xs hidden-sm"><a href="{{ url('/auth/login')}}"><img src="{{ url('/')}}/img/login.png" alt="Login" title="Login"> Login</a></li>
			</ul>
		</div>
	</nav>
</header><!-- Header Ends -->