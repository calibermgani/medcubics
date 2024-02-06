<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image bottom-space-10">
                <i class="fa {{@$heading_icon}} font26 med-white"></i>              
            </div>
            <div class="pull-left info" style="padding-top:5px;">
                <p>{{ @$heading }}</p>              
            </div>
        </div>
        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="active">
                <a href="{{url('charges')}}" class="js_next_process">
                    <i class="fa {{@$heading_icon}}"></i> 
					<span class=" selected"></span>Charges 
                </a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>