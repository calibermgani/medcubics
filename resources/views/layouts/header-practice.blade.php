<header class="main-header"><!-- Header Starts Here -->
    <a href="{{url('/analytics/practice')}}" class="logo"><!-- Logo -->                    
        <span class="logo-mini">{!! HTML::image('img/cube.png',null,['alt' => 'medcubics', 'title' => 'medcubics']) !!}</span><!-- mini logo for sidebar mini 50x50 pixels -->                    
        <span class="logo-lg">{!! HTML::image('img/logo.png',null,['alt' => 'medcubics', 'title' => 'medcubics']) !!}</span><!-- logo for regular state and mobile devices -->
    </a>
	<?php
		if (Auth::check()) {
			$user_details = Auth::user();         
			if ($user_details->user_type == 'Practice' || ($user_details->user_type == 'Medcubics' && Session::get('practice_dbid') != '')) {
				$practice_details = App\Models\Practice::getPracticeDetails();
				$heading_name = $practice_details['practice_name'];
				$get_practiceimg = $practice_details['practice_image'];
				$get_default_timezone = App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m/d/Y');
			} else {
				$get_practiceimg = [];
				$heading_name = 'Admin';
				$get_default_timezone = '';
			}      
		}
    ?> 
    <nav class="navbar navbar-static-top" role="navigation"><!-- Header Navbar -->
        @if($routex[0] == 'patients' && empty($routex[1]) || $currnet_page =="patients"|| $currnet_page =="charges" || $currnet_page =="charges/{status?}" || $currnet_page =="payments" || $currnet_page == 'payments/get-e-remittance' || $currnet_page =="documents" || $currnet_page =="uploadedpatients")
        @else       
        <a href="#" class="sidebar-toggle js-sidebar-toggle" data-toggle="offcanvas" role="button"><!-- Sidebar toggle button-->
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span> 
        </a>
        @endif
        <span class="practice-name col-lg-4 col-lg-offset-3 col-sm-5 col-sm-offset-2 col-xs-8 col-xs-offset-0 hidden-xs">             
            @if(count($get_practiceimg)==0)
            {!! HTML::image('img/hospital-icon.png') !!} 	
            @else
				<?php
					$filename = $get_practiceimg[0] . '.' . $get_practiceimg[1];
					$img_details = [];
					$img_details['module_name']='practice';
					$img_details['file_name'] = $filename;
					$img_details['practice_name']= "";				
					$img_details['style']='width: 21px; height: 21px; border-radius: 50%;';
					$img_details['alt']='practice-image';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>
            {!! $image_tag !!}   
            @endif
            {{$heading_name}} 
        </span>
        <span class="practice-name col-lg-4 col-lg-offset-3 col-sm-5 col-sm-offset-2 col-xs-7 visible-xs">{!! HTML::image('img/hospital-icon.png',null,['class'=>'hidden-xs']) !!} {!! str_limit($heading_name,20,'...') !!}</span>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php
					$current_page = Route::getFacadeRoot()->current()->uri(); 
					$user_type = Auth::user()->user_type;
					//$reportDetails = App\Http\Helpers\Helpers::getReportNotification();
					//$reportNotification = $reportDetails['ExportInfo'];
				//	$pendingExportCount = App\Http\Helpers\Helpers::getReportNotificationCount();
                ?>                         

				<?php
					$user = Auth::user ()->id;
                    /*$get_module_counts = App\Models\Profile\PersonalNotes::where('deleted_at',Null)->where('user_id',$user_id)->where('date',date('Y-m-d'))->count();
                    dd($get_module_counts);*/
					//$get_module_count = App\Http\Controllers\Profile\Api\ProfileApiController::getProfileModuleCount($user);
                ?>

                <li class="dropdown user user-menu "  style="border-right:1px solid #058185;">
                    <a href="#" class="dropdown-toggle med-white" id="notification-practice-user" data-toggle="dropdown" >
                        <i class="fa fa-bell faa-ring animated "></i>
                    </a>
               
                     <div class="dropdown-menu" style="box-shadow: 0 0 1em gold ;" id="dropdown-menu-notification">       
                    </div>
                

             
                </li>
                <input type="hidden" id="current_page" value="{{$current_page}}">
                <input type="hidden" id="heading" value="{{$heading}}">
                <input type="hidden" id="selected_tab" value="{{$selected_tab}}">
                <input type="hidden" id="token" value="{{ csrf_token() }}">
                    <?php 
					//	$list = App\Http\Helpers\Helpers::wishList(Auth::user()->id,$current_page);

						$url =Request::url('/'); 
					?>
                <li class="dropdown user user-menu" style="border-right:1px solid #058185;">
                
                    <a class="dropdown-toggle med-white" id="wishlist-practice-user" data-toggle="dropdown">
                        <i class="fa fa-heart-o "></i>
                    </a>
               
                  <!--   <a class="dropdown-toggle med-white" id="wishlist-practice-user" data-toggle="dropdown">
                        <i class="fa fa-heart "></i>
                    </a> -->
           
                <div class="dropdown-menu page-fav-shadow" style="box-shadow: 0 0 5px #888 !important;" id="dropdown-menu-wishlist">
                 
                                           
                        <!---End Profile Details -->
                        <!--- Switch User -->

                        <!--- End Switch User -->
                    </div>
                </li>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle med-white" data-toggle="dropdown">
						<?php
							$filename = @Auth::user ()->avatar_name.'.'.@Auth::user ()->avatar_ext;
							$img_details = [];
							$img_details['module_name']=(Auth::user ()->practice_user_type == "customer") ? 'customers' : 'user';
							$img_details['file_name']=$filename;
							$img_details['practice_name']="admin";
							$img_details['class']='user-image';
							$img_details['style']='';
							$img_details['alt']='user-image';
							$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
						?>
                        {!! $image_tag !!} 
                        <span class="hidden-xs med-white">{!! Auth::user()->short_name !!}</span>
                        <b class="caret"></b>
                    </a>

                    <div class="dropdown-menu" style="box-shadow: 0 0 1em gold ;">                        
                       
                        <div class="profile-picture">
							<?php
                                $user = Auth::user ()->id;
                               // $get_module_count = App\Http\Controllers\Profile\Api\ProfileApiController::getProfileModuleCount($user);
							?>                        
                            <!--- Profile Details -->
                            <div class="profile-user-name-right">
                                <ul style="list-style:none;margin-left: -30px;">
                                    <?php
										$id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(Auth::user()->id,'encode'); 
									?>
                                    @if($checkpermission->check_url_permission('profile') == 1 )
                                    <li class="dropdown">
                                        <a href="{{ url('profile/personaldetailsview/'.$id) }}" class="js_next_process setcolor">
                                            <button class="btn btn-app btn-medcubics-small" type="button">
                                                <i class="livicon" data-name="user" data-size="16" data-color="#ccc" ></i> Profile
                                            </button>
                                        </a>
                                    </li>
                                    @else
                                    <li class="dropdown">&emsp;</li><li class="dropdown">&emsp;</li>
                                    @endif

                             
                                    <li class="dropdown">
                                        <a href="{{ url('support') }}" class="js_next_process setcolor">
                                            <button class="btn btn-app btn-medcubics-small" type="button">
                                                <i class="livicon" data-name="timer" data-size="16" data-color="#ccc" ></i> Support
                                            </button>
                                        </a>
                                    </li>
                          

                                    @if($checkpermission->check_url_permission('profile/blogs/{order?}/{keyword?}') == 1)	
                                    <li class="dropdown">
                                        <a href="{{ url('profile/blogs') }}" class="js_next_process setcolor"> 
                                            <button class="btn btn-app btn-medcubics-small" type="button">
                                                <i class="livicon" data-name="pen" data-size="16" data-color="#ccc" ></i>Blog
                                            </button>
                                        </a>
                                    </li>
                                    @else
                                    <li class="dropdown">&emsp;</li>
                                    @endif

                                </ul>
                            </div>
                          
                        </div>

                     

                        <!---End Profile Details -->
                        <!--- Switch User -->
                        <div class="switch-user yes-border no-b-t">
                            <p>
                              
                                <a href="{{url('practice/switchuser')}}" class="js_next_process logout-btn"> 
                                    <i class="livicon margin-t-2" data-name="medkit" data-size="13" data-color="#fff"></i>Switch Practice
                                </a>
                             
                            </p>
                            <p class="margin-t-10">                                 
                                <a href="{{ url('/auth/logout') }}" class="js_next_process logout-btn-orange"> 
                                    <i class="livicon margin-t-2" data-name="sign-out" data-size="13" data-color="#fff" data-hovercolor="#fff"></i>Logout
                                </a>                            
                            </p>
                        </div>
                        <!--- End Switch User -->
                    </div>
                </li>
                <!-- Control Sidebar Toggle Button -->
            </ul>
        </div>
    </nav>
</header><!-- Header Ends -->

