<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <div class="user-panel">
        <div class="pull-left image bottom-space-10">
            <i class="fa {{@$heading_icon}} font26 med-white"></i>
        </div>
        <div class="pull-left info" style="padding-top:5px;">
            <p>{{ @$heading }}</p>
        </div>
    </div>
    <section class="sidebar">
        <!-- Sidebar user panel -->

        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            
            
            

            @if($checkpermission->check_url_permission('provider') == 1)
                <li @if($selected_tab == 'provider') class="active" @endif>
                    <a href="{{ url('provider') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} font16"></i>
                        <span class="@if($selected_tab == 'provider') selected @endif"></span> Provider
                    </a>
                </li>
            @endif	

            

            
            <li class="header"> </li>
            <li class="header"> </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>