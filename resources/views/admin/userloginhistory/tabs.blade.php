
<div class="col-md-12 space-m-t-22">
    <!-- Sub Menu -->
    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
                  
			<li class="@if($current_tab == 'pendingApproval') active @endif"><a href="{{ url('admin/userLoginHistory/pendingApproval') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.reason')}} i-font-tabs"></i> Pending Approval</a></li>
			
            <li class="@if($current_tab == 'approvedIp') active @endif"><a href="{{ url('admin/userLoginHistory/approvedIp') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}} i-font-tabs"></i> Approved IP </a></li>

            <li class="@if($current_tab == 'settings') active @endif"><a href="{{ url('admin/userLoginHistory/settings') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.settings')}} i-font-tabs"></i> Settings </a></li>
        </ul>
    </div>
</div><!-- /.box -->