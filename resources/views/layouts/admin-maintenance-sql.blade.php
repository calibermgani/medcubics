<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image" style="padding-bottom:20px;">
                <i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="25" data-name="{{$heading_icon}}" ></i>
            </div>            
            <div class="pull-left info" style="padding-top:5px;">
                <p>{{ $heading }}</p>             
            </div>
        </div>
        <!-- search form -->
 
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
			@if($checkpermission->check_adminurl_permission('admin/maintenance-sql') == 1)
				<li @if($selected_tab == 'maintenance') class="active" @endif>
					<a href="{{ url('admin/maintenance-sql') }}" class="js_next_process">
						<i class="fa {{Config::get('cssconfigs.maintenance.sql')}} font16"></i>  
						<span class="@if($selected_tab == 'maintenance') selected @endif"></span> Maintenance SQL
					</a>
				</li> 
			@endif
        </ul>
        <ul class="sidebar-menu">
            @if($checkpermission->check_adminurl_permission('admin/apiconfig') == 1)
                <li @if($selected_tab == 'apiconfig') class="active" @endif>
                    <a href="{{ url('admin/apiconfig') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.maintenance.sql')}} font16"></i>  
                        <span class="@if($selected_tab == 'apiconfig') selected @endif"></span> API Config
                    </a>
                </li> 
            @endif
        </ul>
        <ul class="sidebar-menu">
            @if($checkpermission->check_adminurl_permission('admin/claimsintegrity') == 1)
                <li @if($selected_tab == 'claimsintegrity') class="active" @endif>
                    <a href="{{ url('admin/claimsintegrity') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.maintenance.sql')}} font16"></i>  
                        <span class="@if($selected_tab == 'claimsintegrity') selected @endif"></span> Data Integrity Test
                    </a>
                </li> 
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>