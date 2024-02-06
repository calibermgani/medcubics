<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image" style="padding-bottom:20px;">
                 <i class="fa fa-clock-o font26 med-white"></i>
            </div>
            <div class="pull-left info" style="padding-top:5px;">
               <p> Support </p>
            </div>
        </div>
        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li  @if($selected_tab == 'faq') class="active" @endif>
                <a class="js_next_process" href=" {{ url('support/faq') }}">
                    <i class="fa fa-question" data-color="#b8c7ce" data-hovercolor="#fff" data-size="19" data-name="user"></i> 
                    <span class="@if($selected_tab == 'faq') selected @endif"></span> FAQ 
                </a>
            </li>
            
			<li @if($selected_tab == 'Ticket') class="active" @endif>
				<a href="{{ url('searchticket') }}">
					<i class="fa {{Config::get('cssconfigs.admin.ticket')}} font16"></i> 
					<span class="@if($selected_tab == 'Ticket') selected @endif"></span> Tickets
				</a>
			</li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>