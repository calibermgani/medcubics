<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
    <div class="box box-info no-shadow" style="border: 1px solid #e0dfdb">

        <div class="box-body">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding">
                <div class="col-lg-3 col-md-3 col-sm-2 col-xs-12 no-padding">
                    <div class="text-center">
                        <div class="safari_rounded">
                            <?php
								$filename = $login_user[0]->avatar_name.'.'.$login_user[0]->avatar_ext;
								$img_details = [];
								$img_details['module_name']=($login_user[0]->practice_user_type == "customer") ? 'customers' : 'user';
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
                <div class="col-lg-8 col-md-8 col-sm-9 col-xs-12 ">
                    <p class="margin-b-5 font14 font600 margin-t-10 med-green">{!! $login_user[0]->name !!} , {!! $login_user[0]->short_name !!}</p>
                    <p class="">
                        {!! $login_user[0]->addressline1 !!}, {!! $login_user[0]->city !!} - {!! $login_user[0]->state !!}<br>{!! $login_user[0]->zipcode5 !!} - {!! $login_user[0]->zipcode4 !!}</p>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12 no-padding">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 margin-t-5 p-l-0 p-r-0 med-right-border">
                    <h3 class="margin-t-15 no-bottom" id="total_message" style="color: #637c96;margin-left: -10px">{!! @$total_messages; !!}</h3>
                    <p class="margin-t-0 font14 med-green font600"  style="margin-left:-10px;"><i class="fa fa-envelope"></i> Messages</p>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3 margin-t-5 p-r-0 med-right-border">
                    <h3 class="margin-t-15 no-bottom" id="notes_count" style="color: #637c96">{!! @$today_notes !!}</h3>
                    <p class="margin-t-0 font14 med-green font600"><i class="fa fa-file-text"></i> Notes</p>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 margin-t-5 p-r-0 med-right-border hide"><!-- Hided -->
                    <h3 class="margin-t-15 no-bottom" style="color: #637c96">0</h3>
                    <p class="margin-t-0 font14 med-green font600"><i class="fa fa-gift"></i> Events</p>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3 margin-t-5 p-r-0">
                    <h3 class="margin-t-15 no-bottom" style="color: #637c96">{!! @$total_record; !!}</h3>
                    <p class="margin-t-0 font14 med-green font600"><i class="fa fa-commenting"></i> Blogs</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 no-padding text-center hide"><!-- Hided -->
                <a href="{{url('auth/logout')}}" class="btn btn-medcubics margin-t-13">Logout</a><br>
                <a href="" class="btn btn-outline-primary">Settings</a>
            </div>


        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!-- Profile Header Ends -->