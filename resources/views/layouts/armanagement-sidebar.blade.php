<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image" style="padding-bottom:20px;">
               <i class="fa {{@$heading_icon}} font26 med-white"></i>
            </div>
            <div class="pull-left info" style="padding-top:5px;">
                <p>{{ $heading }}</p>               
            </div>
        </div>
        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
		<?php $segment2 =  Request::segment(2);  ?>
        <ul class="sidebar-menu">    
			<?php /*
            <!--    
            <li @if($selected_tab == 'summary') class="active" @endif>
                 <a href="{{ url('armanagement') }}">
                    <i class="fa fa-file-text font16"></i> 
                    <span class="@if($selected_tab == 'summary') selected @endif"></span>   Dashboard            
                </a>              
            </li>
            
            <li @if($selected_tab == 'insurancewise') class="active" @endif>
                 <a href="{{ url('armanagement/insurancewise') }}">
                    <i class="fa fa-bank font16"></i> 
                    <span class="@if($selected_tab == 'insurancewise') selected @endif"></span>   Insurance Wise            
                </a>              
            </li>
            
            <li @if($selected_tab == 'statuswise') class="active" @endif>
                 <a href="{{ url('armanagement/statuswise') }}">
                    <i class="fa fa-file-text font16"></i> 
                    <span class="@if($selected_tab == 'statuswise') selected @endif"></span>   Status Wise            
                </a>              
            </li>
            
            <li>
                <a href="#">
                    <i class="fa fa-exchange font16"></i> 
                    <span class="@if($selected_tab == 'userwise') selected @endif"></span>   Reports  
                </a>     
            </li> 
            
            <li>
                <a href="#">
                    <i class="fa fa-exchange font16"></i> 
                    <span class="@if($selected_tab == 'problemlist') selected @endif"></span> Workbench  
                </a>     
            </li> 
            
            <li>
                <a href="#">
                    <i class="fa fa-exchange font16"></i> 
                    <span class="@if($selected_tab == 'reports') selected @endif"></span>  Followup  
                </a>     
            </li> 
            
            <li>
                <a href="#">
                    <i class="fa fa-exchange font16"></i> 
                    <span class="@if($selected_tab == 'reports') selected @endif"></span>  Patient AR  
                </a>     
            </li> 
			-->
			*/ ?>	
            <li @if($selected_tab == 'list'  || $selected_tab == 'armanagementlist') class="active" @endif>
                 <a href="{{ url('armanagement/armanagementlist') }}">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.ar')}} font16"></i> 
                    <span class="@if($selected_tab == 'summary' || $selected_tab == 'armanagementlist') selected @endif"></span> AR Management            
                </a>              
            </li>
            
          <!--  <li @if($selected_tab == 'armanagementlist') class="active" @endif>
                 <a href="{{ url('armanagement/armanagement') }}">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.ar')}} font16"></i> 
                    <span class="@if($selected_tab == 'armanagementlist') selected @endif"></span> AR Old            
                </a>              
            </li> -->
            
            <?php $problem_list_count = App\Models\Patients\ProblemList::getMainProblemListCount(); //@todo - check and remove this if not going to use it. ?>
            <li @if($selected_tab == 'myproblemlist' || $selected_tab == 'problemlist') class="active" @endif>
                <a href="{{ url('armanagement/myproblemlist') }}">
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}} font16"></i> 
                    <span class="@if($selected_tab == 'myproblemlist' || $selected_tab == 'problemlist') selected @endif"></span> Workbench  
					<?php  $problem_list_count = App\Models\Patients\ProblemList::getProblemListCount(); ?>
                    @if($problem_list_count > 0)
                    <small class="label pull-right bg-yellow main_ar_workbench" style="font-weight:400">
                        {{(@$problem_list_count)}}</small>
                    @endif
                </a>     
            </li>
			
			<li @if($segment2 == 'myfollowup' || $segment2 == 'otherfollowup') class="active" @endif style="display:none">
                <a href="{{ url('armanagement/myfollowup') }}">
                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} font16"></i> 
                    <span class="@if($segment2 == 'myfollowup' || $segment2 == 'otherfollowup') selected @endif"></span> Followup List    
                </a>    
            </li>
			
			<li @if($selected_tab == 'deniallist') class="active" @endif>
                <a href="{{ url('armanagement/summary') }}">
                    <i class="fa {{Config::get('cssconfigs.common.denials')}} font16"></i> 
                    <span class="@if($selected_tab == 'denials' ) selected @endif"></span> Denials					
					<?php  $denials_count = App\Models\Payments\ClaimInfoV1::getDeniedClaimCount(); ?>
                    @if($denials_count > 0)
                    <small class="label pull-right bg-yellow main_ar_workbench" style="font-weight:400">{{( @$denials_count) }}</small>
                    @endif
                </a>     
            </li>
            
            <!--<li @if($selected_tab == 'workorder') class="active" @endif>
                 <a>
                    <i class="fa {{Config::get('cssconfigs.Practicesmaster.ar')}} font16"></i> 
                    <span class="@if($selected_tab == 'workorder') selected @endif"></span> Work Order            
                </a>              
            </li>-->
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>