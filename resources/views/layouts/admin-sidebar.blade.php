<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
             
			<div class="pull-left image bottom-space-10">
                <i class="fa {{@$heading_icon}} font26 med-white"></i>			  
            </div>
            <div class="pull-left info margin-t-5">
                 <p>{{ @$heading }}</p> 
            </div>
        </div>
        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">    
    		@if($checkpermission->check_adminurl_permission('admin/customer') == 1)
                <li @if($selected_tab == 'customer' || $selected_tab == 'admin/customerpractices' || $selected_tab == 'provider_details' || $selected_tab == 'admin/customernotes' || $selected_tab == 'admin/customerusers'|| $selected_tab == 'users')  class="active" @endif>
                     <a href="{{ url('admin/customer') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.admin.users')}} font16"></i>
                        <span class="@if($selected_tab == 'customer' || $selected_tab == 'admin/customerpractices' || $selected_tab == 'provider_details' || $selected_tab == 'admin/customernotes' || $selected_tab == 'admin/customerusers') selected @endif"></span> Customers
                    </a>                 
                </li>
            @endif

            @if($checkpermission->check_adminurl_permission('admin/insurance') == 1)
                <li @if($selected_tab == 'admin/insurance' || $selected_tab == 'insurance') class="active" @endif>
    				 <a href="{{ url('admin/insurance') }}" class="js_next_process">
                       <i class="fa {{Config::get('cssconfigs.common.insurance')}} font16"></i>
                        <span class="@if($selected_tab == 'admin/insurance' || $selected_tab == 'insurance') selected @endif"></span> Insurance 
                    </a>
                </li>          
            @endif

            @if($checkpermission->check_adminurl_permission('admin/insurancetypes') == 1)
                <li @if($selected_tab == 'admin/insurancetypes') class="active" @endif>
    			    <a href="{{ url('admin/insurancetypes') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.common.list_ul')}} font16"></i> <span class="@if($selected_tab == 'admin/insurancetypes') selected @endif"></span> Insurance Types
                    </a>
                </li>
            @endif
             
            @if($checkpermission->check_adminurl_permission('admin/modifiers') == 1)
                <li @if($selected_tab == 'admin/modifiers') class="active" @endif>
    				 <a href="{{ url('admin/modifierlevel1') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.common.modifiers')}} font16"></i> 
                        <span class="@if($selected_tab == 'admin/modifiers') selected @endif"></span> Modifiers
                    </a>
                </li>
            @endif
             
    		 <!--
             @if($checkpermission->check_adminurl_permission('admin/feeschedule') == 1)
                <li @if($selected_tab == 'admin/feeschedule') class="active" @endif>
                     <a href="{{ url('admin/feeschedule') }}">
                        <i class="livicon" data-color="#b8c7ce" data-hovercolor="#fff" data-size="19" data-name="inbox-empty"></i> 
                        <span class="@if($selected_tab == 'admin/feeschedule') selected @endif"></span> Fee Schedule
                    </a>
                </li>
             @endif
             -->
    		 
            @if($checkpermission->check_adminurl_permission('admin/code') == 1)
                <li @if($selected_tab == 'admin/code') class="active" @endif>
    				<a href="{{ url('admin/code') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.common.codes')}} font16"></i>
                        <span class="@if($selected_tab == 'admin/code') selected @endif"></span> Codes 
                    </a>
                </li> 
            @endif
              
            @if($checkpermission->check_adminurl_permission('admin/cpt') == 1)
                <li @if($selected_tab == 'admin/cpt') class="active" @endif>
    				<a href="{{ url('admin/cpt') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} font16"></i>
                        <span class="@if($selected_tab == 'admin/cpt') selected @endif"></span> CPT
                    </a>
                </li>
            @endif
              
            @if($checkpermission->check_adminurl_permission('admin/icd') == 1)
                <li @if($selected_tab == 'admin/icd') class="active" @endif>
    				<a href="{{ url('admin/icd') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.common.icd')}} font16"></i>
                        <span class="@if($selected_tab == 'admin/icd') selected @endif"></span> ICD
                    </a>
                </li>
            @endif

            @if($checkpermission->check_adminurl_permission('admin/edi') == 1)   
                <li @if($selected_tab == 'admin/edi') class="active" @endif>
                    <a href="{{ url('admin/edi') }}" class="js_next_process">
                        <i class="fa fa-cart-plus font16"></i>
                        <span class="@if($selected_tab == 'admin/edi') selected @endif"></span> EDI
                    </a>
                </li>            
            @endif

            @if($checkpermission->check_adminurl_permission('admin/speciality') == 1)   
                <li @if($selected_tab == 'admin/speciality') class="active" @endif>
    				<a href="{{ url('admin/speciality') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.admin.speciality')}} font16"></i>
                        <span class="@if($selected_tab == 'admin/speciality') selected @endif"></span> Specialty
                    </a>
                </li>            
            @endif

            @if($checkpermission->check_adminurl_permission('admin/taxanomy') == 1)   
                <li @if($selected_tab == 'admin/taxanomy') class="active" @endif>
    				<a href="{{ url('admin/taxanomy') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.admin.taxanomy')}} font16"></i>
                        <span class="@if($selected_tab == 'admin/taxanomy') selected @endif"></span> Taxonomy
                    </a>
                </li>            
            @endif
             
            @if($checkpermission->check_adminurl_permission('admin/placeofservice') == 1)
                <li @if($selected_tab == 'admin/placeofservice') class="active" @endif>
    				<a href="{{ url('admin/placeofservice') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.admin.pos')}} font16"></i> 
                        <span class="@if($selected_tab == 'admin/placeofservice') selected @endif"></span> POS
                    </a>
                </li>    
            @endif   

            @if($checkpermission->check_adminurl_permission('admin/qualifiers') == 1)
                <li @if($selected_tab == 'admin/qualifiers') class="active" @endif>
    				<a href="{{ url('admin/qualifiers') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.admin.qualifier')}} font16"></i> 
                        <span class="@if($selected_tab == 'admin/qualifiers') selected @endif"></span> ID Qualifiers
                    </a>
                </li>
            @endif
            
            @if($checkpermission->check_adminurl_permission('admin/providerdegree') == 1)
                <li @if($selected_tab == 'admin/providerdegree') class="active" @endif>
    				<a href="{{ url('admin/providerdegree') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.admin.providerdegree')}} font16"></i> 
                        <span class="@if($selected_tab == 'admin/providerdegree') selected @endif"></span> Provider Degree
                    </a>
                </li>  
            @endif
                
            @if($checkpermission->check_adminurl_permission('admin/medcubicsrole') == 1)
                <li @if($selected_tab == 'admin/medcubicsrole') class="active" @endif>
    				<a href="{{ url('admin/medcubicsrole') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.admin.role')}} font16"></i> 
                        <span class="@if($selected_tab == 'admin/medcubicsrole') selected @endif"></span> Roles
                    </a>
                </li>  
            @endif
    		
            @if($checkpermission->check_adminurl_permission('admin/useractivity') == 1)
                <li @if($selected_tab == 'admin/useractivity') class="active" @endif>
    				<a href="{{ url('admin/useractivity') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.Practicesmaster.userapisettings')}} font16"></i>  
                        <span class="@if($selected_tab == 'admin/useractivity') selected @endif"></span> User Activity
                    </a>
                </li> 
            @endif
			
			<li @if(@$selected_tab == 'userLoginHistory') class="active margin-b-20" @endif>
                 <a href="{{ url('admin/userLoginHistory/pendingApproval') }}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.common.help')}} font16"></i>
                    <span class="@if(@$selected_tab == 'userLoginHistory') selected @endif"></span> Security Code
                </a>
            </li>

    		@if($checkpermission->check_adminurl_permission('admin/userhistory') == 1)
                <li @if($selected_tab == 'admin/userhistory') class="active" @endif>
    				<a href="{{ url('admin/userhistory') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.admin.users')}} font16"></i>  
                        <span class="@if($selected_tab == 'admin/userhistory') selected @endif"></span> User History
                    </a>
                </li> 
            @endif
   		
            @if($checkpermission->check_adminurl_permission('admin/staticpage') == 1)
    			<li @if($selected_tab == 'admin/staticpage') class="active" @endif>
    				<a href="{{ url('admin/staticpage') }}" class="js_next_process">
                        <i class="fa {{Config::get('cssconfigs.common.help')}} font16"></i>
    					<span class="@if($selected_tab == 'admin/staticpage') selected @endif"></span> Help
                    </a>
                </li>
    		@endif	
		
        </ul>
		
    </section>
    <!-- /.sidebar -->
</aside>