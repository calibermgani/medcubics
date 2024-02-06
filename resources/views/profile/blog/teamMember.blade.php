<div class="box-header no-border">
	<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Team Members</h3>          
</div><!-- /.box-header -->
<div class="box-body" style="line-height: 50px;">
<?php $user = App\Http\Controllers\Profile\BlogController::teamMemberController();  ?> 
@foreach($user as $user)
	<?php $filename = $user->avatar_name.'.'.$user->avatar_ext;$img_details = []; ?>	
	@if($user->is_logged_in == 1 && $user->user_type == "Practice")
		<?php 
			$img_details['class']='online blog-user-list';
		?>	
	@elseif($user->is_logged_in ==0 && $user->user_type == "Practice")
		<?php 
			$img_details['class']='ideal blog-user-list';
		?>
	@else
		
	@endif
	@if(($user->is_logged_in == 1 || $user->is_logged_in == 0) && $user->user_type === "Practice")
	<?php 
		$img_details['module_name']= ($user->practice_user_type == "customer") ? 'customers' : 'user';'user';
		$img_details['file_name']=$filename;
		$img_details['practice_name']="admin";
		$img_details['data-placement']='bottom';
		$img_details['data-toggle']='tooltip';
		$img_details['alt']='blog-image';
		$img_details['data-original-title']=$user->name;
		$image_tag = App\Http\Helpers\Helpers::checkandgetavatar($img_details);
	?>	
	{!! $image_tag !!}	
	@endif			
@endforeach
</div><!-- /.box-body -->       