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
            @if($checkpermission->check_adminurl_permission('admin/adminuser') == 1)
                <li @if($selected_tab == 'admin/adminuser') class="active" @endif>
    				 <a href="{{ url('admin/adminuser') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font16"></i> 
                        <span class="@if($selected_tab == 'admin/adminuser') selected @endif"></span>  Users
                    </a>
                </li> 
            @endif

    		@if($checkpermission->check_adminurl_permission('admin/userpassword') == 1)
                <li @if($selected_tab == 'admin/adminpassword') class="active" @endif>
    				 <a href="{{ url('admin/userpassword') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font16"></i> 
                        <span class="@if($selected_tab == 'admin/userpassword') selected @endif"></span> Change Password
                    </a>
                </li> 
            @endif
        </ul>
		
    </section>
    <!-- /.sidebar -->
</aside>