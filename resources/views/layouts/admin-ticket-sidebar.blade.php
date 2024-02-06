<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
             
			<div class="pull-left image bottom-space-10">
                <i class="fa {{@$heading_icon}} font26 med-white"></i>			  
            </div>
            <div class="pull-left info margin-t-5">
                <p>{{ $heading }}</p>
            </div>
        </div>
        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        
        <ul class="sidebar-menu">  
       
    		@if($checkpermission->check_adminurl_permission('admin/faq') == 1)   
                <li @if($selected_tab == 'admin/faq') class="active" @endif>
    				 <a href="{{ url('admin/faq') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.admin.faq')}} font16"></i>
                        <span class="@if($selected_tab == 'admin/faq') selected @endif"></span> FAQ
                    </a>
                </li>            
            @endif

    		@if($checkpermission->check_adminurl_permission('admin/manageticket') == 1) 
    			<?php
					$get_ticketcount = App\Http\Helpers\Helpers::getnewticket(); 
    				$ticketcount = '('.$get_ticketcount.')';
    			?>
                <li @if($selected_tab == 'admin/manageticket') class="active" @endif>
    				 <a href="{{ url('admin/manageticket') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.admin.ticket')}} font16"></i>
                        <span class="@if($selected_tab == 'admin/manageticket') selected @endif"></span> Manage Tickets {{ ($get_ticketcount=='')?'':$ticketcount }}
                    </a>
                </li>            
            @endif
           @if($checkpermission->check_adminurl_permission('admin/updates') == 1) 
	        <li @if($selected_tab == 'admin/updates') class="active" @endif>
                <a href="{{ url('admin/updates') }}" class="js_next_process">
                    <i class="fa fa-upload font16"></i> 
                     <span class="@if($selected_tab == 'admin/updates') selected @endif"></span> Updates
                </a>
            </li> 
           @endif
			<li @if($selected_tab == 'admin/log') class="active" @endif>
				<a href="{{ url('admin/errorlog') }}">
					<i class="fa fa-exclamation font16"></i> 
					 <span class="@if($selected_tab == 'admin/log') selected @endif"></span> Error Log
				</a>
			</li>
		
        </ul>		
    </section>
    <!-- /.sidebar -->
</aside>