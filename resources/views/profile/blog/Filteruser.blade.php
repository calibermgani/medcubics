<i class="fa fa-spinner fa-spin coverloadingimg" id="user_listing" style="margin-left:120px;"></i>
@foreach($users as $user)
@if($user->id != 1)
<?php
	$filename = @$user->avatar_name.'.'.@$user->avatar_ext;
	$img_details = [];
	$img_details['module_name']=($user->practice_user_type == "customer") ? 'customers' : 'user';
	$img_details['file_name']=$filename;
	$img_details['practice_name']="admin";
	$img_details['class']='margin-r-10 no-padding';
	$img_details['style']='width:25px; border-radius:50%; margin-top:-8px; margin-bottom:-15px; height:25px;';
	$img_details['alt']='user-image';
	$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
?>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border-bottom:1px dashed #e2e9f1;">			
	<div class="col-lg-02 col-md-2 col-sm-4 col-xs-12 p-l-0 p-r-0" style="">{!! $image_tag !!} </div>
	<div class="col-lg-10 col-md-8 col-sm-8 col-xs-12" style="margin-bottom:-3px;">
		<p class="" style="padding-top: 7px"><span class="med-darkgray  font14">{{substr($user->name, 0, 20)}}</span> <i class="fa fa-circle med-gray @if($user->is_logged_in =='1')med-green-o @endif pull-right margin-t-5" style="font-size:9px;"></i></p>
	</div>
</div>
@endif

@endforeach
@if(count($users) == 0)
	No user found....
@endif