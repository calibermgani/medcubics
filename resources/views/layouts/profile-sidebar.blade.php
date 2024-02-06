<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->

    <div class="user-panel">
        <div class="pull-left image bottom-space-10">
            <i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font26 med-white"></i>
        </div>
        <div class="pull-left info" style="padding-top:5px;">
            <p> Profile </p>
        </div>
    </div>

    <section class="sidebar">
        <!-- Sidebar user panel -->

        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">

            <?php $id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(Auth::user()->id,'encode'); ?>
            @if($checkpermission->check_url_permission('profile/personaldetails/'.$id) == 1)
            <li @if($selected_tab == 'personaldetails')  class="active" @endif >
                 <a href="{{ url('profile/personaldetailsview/'.$id) }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font16"></i>
                    @if($selected_tab == 'personaldetails')<span class=" @if($selected_tab == 'personaldetails') selected @endif"></span>@endif Profile
                </a>
            </li>
            @endif 

            <?php /*
			@if($checkpermission->check_url_permission('profile') == 1)
			<li @if($selected_tab == 'profile')  class="active" @endif>
				 <a href="{{ url('profile') }}" class="js_next_process">
					<i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font16"></i> 
					@if($selected_tab == 'profile')<span class=" @if($selected_tab == 'profile') selected @endif"></span>@endif Profile 
				</a>
			</li>
			@endif

			<li>
				<a href="">
					<i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="19" data-name="dashboard"></i> User Access
				</a>
			</li>
            */ ?>
            @if($checkpermission->check_url_permission('profile/changepassword') == 1)
            <li @if($selected_tab == 'changepassword') class="active" @endif>
                 <a href="{{ url('profile/changepassword') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.change_password')}} font16"></i> 
                    @if($selected_tab == 'changepassword')<span class=" @if($selected_tab == 'changepassword') selected @endif"></span>@endif Change Password
                </a>
            </li>
            @endif

            <!--li>
                <a href="">
                    <i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="19" data-name="dashboard"></i> 
                    Personal Detials
                </a>
            </li--> 

            <li @if($selected_tab == 'blog_listing')  class="active" @elseif($selected_tab == 'blogs')  class="active" @endif >
                 <a href="{{ url('profile/blogs') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.blogs')}} font16"></i>
                    @if($selected_tab == 'blog_listing')<span class=" @if($selected_tab == 'blog_listing') selected @endif"></span> @elseif($selected_tab == 'blogs') <span class=" @if($selected_tab == 'blogs') selected @endif"></span> @endif Blogs
                </a>
            </li>

            <li @if($selected_tab == 'notes')  class="active" @endif >
                 <a href="{{ url('profile/personal-notes') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}} font16"></i>
                    @if($selected_tab == 'notes')<span class=" @if($selected_tab == 'notes') selected @endif"></span>@endif Notes
                </a>
            </li>  
			<?php /*
            <li @if($selected_tab == 'task')  class="active" @endif >
                 <a href="{{ url('profile/task') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.patient.file_text')}} font16"></i>
                    @if($selected_tab == 'task')<span class=" @if($selected_tab == 'task') selected @endif"></span>@endif Tasks
                </a>
            </li>  
			*/ ?>
            <li @if($selected_tab == 'message')  class="active" @endif >
                 <a href="{{ url('profile/message') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.common.message')}} font16"></i>
                    @if($selected_tab == 'message')<span class=" @if($selected_tab == 'message') selected @endif"></span>@endif Messages
                </a>
            </li>  

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>