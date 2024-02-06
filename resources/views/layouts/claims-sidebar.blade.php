<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image bottom-space-10">
                <i class="fa fa-cart-arrow-down font26 med-white"></i>              
            </div>

            <div class="pull-left info" style="padding-top:5px;">
                <p> Claims </p>
            </div>
        </div>
        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
		<?php $notificationCount = App\Http\Helpers\Helpers::claimsNotificationNo(); ?>
        <ul class="sidebar-menu">           
            
            <!--<li @if($selected_tab == 'claimsummary') class="active" @endif>
                <a href="{{url('claims') }}">
                    <i class="fa fa-tachometer font16"></i> 
                    <span class="@if($selected_tab == 'claimsummary') selected @endif"></span> Summary            
                </a>              
            </li>-->                     

            <li @if($selected_tab == 'tosubmit') class="active" @endif class="hide">
                 <a href="{{url('claims') }}">
                    <i class="fa fa-cart-plus  font16"></i> 
                    <span class="@if($selected_tab == 'tosubmit') selected @endif"></span> Ready to Submit            
                </a>     
            </li> 
			
			<li @if($selected_tab == 'electronic') class="active" @endif>
                 <a href="{{url('claims/status/electronic') }}">
                    <i class="fa fa-tv  font16"></i> 
                    <span class="@if($selected_tab == 'electronic') selected @endif"></span> Electronic Claims            
                </a>     
            </li>
			<li @if($selected_tab == 'paper') class="active" @endif>
                 <a href="{{url('claims/status/paper') }}">
                    <i class="fa fa-file-text  font16"></i> 
                    <span class="@if($selected_tab == 'paper') selected @endif"></span> Paper Claims            
                </a>     
            </li>
			
			<li @if($selected_tab == 'error') class="active" @endif>
                 <a href="{{url('claims/status/error') }}">
                    <i class="fa fa-pencil-square-o font16"></i> 
                    <span class="@if($selected_tab == 'error') selected @endif"></span> Claim Edits 
					 @if($notificationCount['claimEdits'] > 0)
                    <small class="label pull-right bg-yellow main_ar_workbench" style="font-weight:400">
                        {{(@$notificationCount['claimEdits'])}}</small>
                    @endif
                </a>     
            </li> 
			
			<li @if($selected_tab == 'submitted') class="active" @endif>
                 <a href="{{url('claims/status/submitted') }}">
                    <i class="fa fa-check font16"></i> 
                    <span class="@if($selected_tab == 'submitted') selected @endif"></span> Submitted    
					@if($notificationCount['Submitted'] > 0)
                    <small class="label pull-right bg-yellow main_ar_workbench" style="font-weight:400">
                        {{(@$notificationCount['Submitted'])}}</small>
                    @endif
                </a>
            </li> 

            <li @if($selected_tab == 'hold') class="active" @endif style="display:none">
                 <a href="{{url('claims/hold') }}">
                    <i class="fa fa-lock font16"></i> 
                    <span class="@if($selected_tab == 'hold') selected @endif"></span> Claims on Hold            
                </a>
            </li>

            <li @if($selected_tab == 'pending') class="active" @endif style="display:none">
                 <a href="{{url('claims/pending') }}">
                    <i class="fa fa-exclamation-triangle font16"></i> 
                    <span class="@if($selected_tab == 'pending') selected @endif"></span> Pending Claims            
                </a>
            </li> 

            <li @if($selected_tab == 'rejected') class="active" @endif>
                 <a href="{{url('claims/status/rejected') }}">
                    <i class="fa fa-ban font16"></i> 
                    <span class="@if($selected_tab == 'rejected') selected @endif"></span> EDI Rejections  
					 @if($notificationCount['EdiRejection'] > 0)
                    <small class="label pull-right bg-yellow main_ar_workbench" style="font-weight:400">
                        {{(@$notificationCount['EdiRejection'])}}</small>
                    @endif
                </a>
            </li> 

            

            <li @if($selected_tab == 'edireports') class="active" @endif>
                 <a href="{{url('claims/edireports') }}">
                    <i class="fa fa-bar-chart font16"></i> 
                    <span class="@if($selected_tab == 'edireports') selected @endif"></span> EDI Reports            
                </a>
            </li> 

            <li class="hide" @if($selected_tab == 'transmission') class="active" @endif>
                <a href="{{url('claims/transmission ') }}">
                    <i class="fa fa-exchange font16"></i> 
                    <span class="@if($selected_tab == 'transmission') selected @endif"></span> Claim Transmission            
                </a>
            </li>         
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>