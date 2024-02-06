<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Profile </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#js-help-modal" data-url="{{url('help/profile')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')

<div class="col-lg-9 col-md-9 col-xs-8 col-xs-12 margin-t-m-10"><!-- Left side Outer body starts -->
    <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12"><!-- Profile Header Starts -->
        <div class="box box-info no-shadow l-green-b">
            <div class="box-body">


                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                    <div class="text-center">
                        <div class="safari_rounded">
                            <?php
								$filename = @Auth::user ()->avatar_name.'.'.@Auth::user ()->avatar_ext;
								$img_details = [];
								$img_details['module_name']=(Auth::user ()->practice_user_type == "customer") ? 'customers' : 'user';
								$img_details['file_name']=$filename;
								$img_details['practice_name']="admin";
								$img_details['class']='';
								$img_details['style']='';
								$img_details['alt']='user-image';
								$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
							?>
                            {!! $image_tag !!} 							 
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 tab-border-bottom">



                    @if(Auth::user()->firstname!=''|| Auth::user()->lastname !='')
                    <h5 class="med-green no-margin">{{Auth::user()->lastname}}, {{Auth::user()->firstname}}</h5><span style="background: #fff;">{{Auth::user()->short_name}}</span>
                    @endif
					
                    <h5 class=""><span class="med-orange sm-size">
						<?php
						$originalDate = Auth::user()->dob;
						$dob = date("M d,Y", strtotime($originalDate));
						$from = new DateTime($dob);
						$to = new DateTime('today');
						$years = $from->diff($to)->y;
						?>
						@if(Auth::user()->gender != '')
								
						<i class="med-orange med-gender hide fa @if(Auth::user()->gender == 'Male') fa-male @else fa-female @endif margin-r-5 "></i> {!! Auth::user()->gender !!}   @if(Auth::user()->dob !== "0000-00-00"){{$dob}}, {{$years}} years @endif</span>
						@endif	
					</h5> 
                    <h5 class="space20" style="margin-top: 30px;">
                        @if(Auth::user()->addressline1 != '')
                        {{@Auth::user()->addressline1.','}} 
                        @endif
                        @if(Auth::user()->addressline2 != '')
                        {{Auth::user()->addresline2.','}}</h5>
                    @endif	
                    <h5>@if(Auth::user()->city != ''){{Auth::user()->city.'-'}}@endif @if(Auth::user()->state != ''){{Auth::user()->state.', '}}@endif @if(Auth::user()->zipcode5 != '') {{Auth::user()->zipcode5.'-'}} @endif @if(Auth::user()->zipcode4 != ''){{Auth::user()->zipcode4}}@endif</h5>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 med-left-border">
                    <ul class="icons push no-padding">					
                        
                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green">Phone</span>
                            @if(Auth::user()->phone != '')<span class="pull-right"> {{Auth::user()->phone}} @else <p class="nill pull-right">- Nil -</p></span> @endif</li>
                        
                        
                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green">Fax</span>				
                            @if(Auth::user()->fax != '')   <span class="pull-right">{{Auth::user()->fax}} @else <p class="nill pull-right">- Nil -</p></span> @endif</li>
                       
                        @if(Auth::user()->email != '')
                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green">Email</span> <span class="pull-right">{{Auth::user()->email}}</span></li>
                        @endif
                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12 space10">
                            @if(Auth::user()->facebook_ac != '')
                            <a href="{{@Auth::user()->facebook_ac}}" target="_blank"><i class="fa fa-facebook-square font20 facebook"></i></a> 
                            @endif	
                            @if(Auth::user()->twitter != '')		
                            <a href="{{Auth::user()->twitter}}" target="_blank"><i class="fa fa-twitter-square font20 margin-l-5 twitter"></i></a> 
                            @endif	
                            @if(Auth::user()->linkedin != '')	
                            <a href="{{Auth::user()->linkedin}}" target="_blank"><i class="fa fa-linkedin-square font20 margin-l-5 linkedin"></i></a>
                            @endif	
                            @if(Auth::user()->googleplus != '')	
                            <a href="{{Auth::user()->googleplus}}" target="_blank"><i class="fa fa-google-plus-square font20 margin-l-5 gplus"></i></a>
                            @endif	
                        </li>					                    
                    </ul>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- Profile Header Ends -->

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-20 margin-b-15">

        @if($checkpermission->check_url_permission('profile/calendar') == 1)
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 ">
            <div class="med-profile">
                <a href="{{ url('profile/calendar') }}"><i class="fa fa-calendar"></i>
                    <h5 class="profile-heading">Calendar</h5></a>
            </div>                        
        </div>
        @endif


        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
            <div class="med-profile">
                <i class="fa fa-tasks"></i>
                <h4 class="profile-heading">Tasks</h4>
            </div>            

        </div>


	@if($checkpermission->check_url_permission('profile/maillist') == 1)
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
		<a href="{{ url('profile/message')}}">
			<div class="med-profile">
				<i class="fa fa-envelope"></i>
				<h4 class="profile-heading">Messages</h4></a>
		</div>                       
    </div>
    @endif

    @if($checkpermission->check_url_permission('profile/blogs') == 1)
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
        <a href="{{ url('profile/blogs')}}">
            <div class="med-profile">               
                <i class="fa fa-comments-o"></i>
                <h4 class="profile-heading">Blogs</h4></a>
    </div>                         
</div>
@endif

</div>