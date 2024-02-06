<div class="box-body margin-t-m-18">
  <div class="row">
    <div class="col-sm-1 col-md-0  col-xs-2 col-md-offset-3 col-sm-offset-2 col-xs-offset-1">
      <div class="module-menu-icon @if(isset($currnet_page) && $currnet_page == 'admin/dashboard') dash-module-active @else dash-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Dashboard">                               
        <a href="{{ url('admin/dashboard') }}" class="js_next_process"><i class="livicon left-pad" data-name="dashboard" data-size="30" data-color="@if(isset($currnet_page) && $currnet_page == 'admin/dashboard') #fff @else #429b9b @endif" data-hovercolor="#fff"></i></a>
      </div>
    </div>
    <!-- /.col -->   

  @if($currnet_page == 'admin/dashboard')
    <?php $routex[1] = ''; ?>
  @endif

    <div class="col-sm-1 col-md-0 col-xs-2">
		<div class="module-menu-icon @if(isset($routex[0]) && $routex[0] == 'admin' && $routex[1] != 'adminuser' && $routex[1] != 'faq' && $routex[1] != 'manageticket' && $routex[1] != 'managemyticket' && $routex[1] != 'errorlog' && $routex[1] != 'errorlog') admin-customer-module-active @else admin-customer-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Customers">
			<a href="{{ url('admin/customer') }}" class="js_next_process"><i class="livicon left-pad" data-name="users" data-size="30" data-color="@if(isset($routex[0]) && $routex[0] == 'admin' && $routex[1] != 'adminuser' && $routex[1] != 'faq' && $routex[1] != 'manageticket' && $routex[1] != 'managemyticket' && $routex[1] != 'errorlog' && $routex[1] != 'errorlog') #fff @else #6e9cf1 @endif"  data-hovercolor="#fff"></i></a>
		</div>		 
    </div>
    <!-- /.col -->                                    

    <div class="col-sm-1 col-md-0 col-xs-2">
		<div class="module-menu-icon @if(isset($routex[1]) && $routex[1] == 'adminuser') admin-user-module-active @else admin-user-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Admin">    
			<a href="{{ url('admin/adminuser') }}" class="js_next_process"><i class="livicon left-pad" data-name="user" data-size="30" data-color="@if(isset($routex[1]) && $routex[1] == 'adminuser' ) #fff @else #db5c87 @endif"  data-hovercolor="#fff"></i></a>	
		</div>
    </div>
    <!-- /.col -->                                    

    <div class="col-sm-1 col-md-0 col-xs-2">
		<div class="module-menu-icon @if(isset($routex[1]) && ($routex[1] == 'faq' || $routex[1] == 'manageticket' || $routex[1] == 'managemyticket' || $routex[1] == 'createnewticket' || $routex[1] == 'errorlog' || $routex[1] == 'errorlog')) admin-tickets-module-active @else admin-tickets-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Tickets">
			<a href="{{ url('admin/faq') }}" class="js_next_process"><i class="livicon left-pad" data-name="message-new" data-size="30" data-color="@if(isset($routex[1]) && ($routex[1] == 'faq' || $routex[1] == 'manageticket' || $routex[1] == 'managemyticket' || $routex[1] == 'createnewticket' || $routex[1] == 'errorlog' || $routex[1] == 'errorlog')) #fff @else #46cdf9 @endif"  data-hovercolor="#fff"></i></a>	 
		</div>
    </div>
    <!-- /.col -->     

    <div class="col-sm-1 col-md-0 col-xs-2">
		<div class="module-menu-icon @if(isset($routex[1]) &&$routex[1] == 'customer/metrics') admin-metrics-module-active @else admin-metrics-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Metrics">
        <a href="{{ url('admin/metrics') }}" class="js_next_process"><i class="livicon left-pad" data-name="shopping-cart" data-size="30" data-color="@if(isset($routex[1]) && $routex[1] == 'customer/tickets') #fff @else #8cc503 @endif"  data-hovercolor="#fff"></i></a>
		</div>
    </div>
    <!-- /.col -->                                    

    <div class="col-sm-1 col-md-0 col-xs-2 col-xs-offset-1 col-lg-offset-0 col-md-offset-0 col-sm-offset-0">
		<div class="padding-t-xs-5">
			<div class="module-menu-icon @if(isset($routex[0]) || $routex[1] == 'maintenance-sql') admin-backup-module-active @else admin-backup-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Maintenance"> <a href="{{ url('admin/maintenance-sql') }}" class="js_next_process">
				<i class="livicon left-pad" data-name="servers" data-size="30" data-color="@if(isset($routex[0]) || $routex[0] == 'maintenance-sql') #fff @else #ed5949 @endif"  data-hovercolor="#fff"></i></a>	
			</div>
		</div>
    </div>
    <!-- /.col -->  

	<div class="col-sm-1 col-md-0 col-xs-2">
      <div class="padding-t-xs-5">
			<div class="module-menu-icon @if(isset($routex[1]) &&$routex[1] == 'customer/reports') admin-reports-module-active @else admin-reports-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Reports">
				<a href="{{ url('admin/reports') }}" class="js_next_process"><i class="livicon left-pad" data-name="barchart" data-size="30" data-color="@if(isset($routex[1]) && $routex[1] == 'customer/reports') #fff @else #af4773 @endif"  data-hovercolor="#fff"></i></a>
			</div>
      </div>
    </div>
    <!-- /.col -->    

    <div class="col-sm-1 col-md-0 col-xs-2">
		<div class="padding-t-xs-5">
			<div class="module-menu-icon @if(isset($routex[1]) &&$routex[1] == 'customer/payments') admin-payments-module-active @else admin-payments-module @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Payments"> <i class="livicon left-pad" data-name="money" data-size="30" data-color="@if(isset($routex[1]) && $routex[1] == 'customer/payments') #fff @else #47af71 @endif"  data-hovercolor="#fff"></i> 
			</div>
		</div>
    </div>                                                                                                                                          
  </div>
<!-- /.row -->
@yield('toolbar')
</div>
<!-- /.box-body -->