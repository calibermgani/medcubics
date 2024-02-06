<div class="col-lg-12 col-md-12 col-xs-12 col-xs-12 p-r-0"  ><!-- Blog Header Starts -->
	<div class="box-header no-border">
		<i class="fa fa-navicon i-font-tabs"></i>  <h3 class="box-title">Favorites</h3>
		
	</div><!-- /.box-header -->

	<div class="box box-view no-shadow no-border no-border-radius">
		<div class="box-body padding-t-15 min-height-profile-blogs-left">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<?php 
				$Favourite = App\Http\Controllers\Profile\BlogController::getFavourite();
				$favourcount = count($Favourite); 
			?>
			@if($favourcount == '0')
			 <p class="text-center">No Blogs Found</p>
			@else    
				@foreach($Favourite as $getfav)  
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 blog_{{ $getfav->blog->id }} no-padding blog-fav blog-border">
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 no-padding margin-b-10">
					
					<?php
						$filename = @$getfav->user->avatar_name.'.'.@$getfav->user->avatar_ext;
						$img_details = [];
						$img_details['module_name']='user';
						$img_details['file_name']=$filename;
						$img_details['practice_name']="admin";
						
						$img_details['class']='ideal blog-user-list';
						$img_details['data-placement']='bottom';
						$img_details['data-toggle']='tooltip';
						$img_details['alt']='blog-image';
						$img_details['data-original-title']= @$getfav->user->name;
						$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
					?>
					{!! $image_tag !!}
					
					</div>
					<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
						<?php $blog_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($getfav->blog->id,'encode'); ?>
						<p class="med-green font600 no-bottom"><a href="{{ url('profile/blog/'.$blog_id) }}">{{ ucwords($getfav->blog->title)  }}</a></p>
						<p>{{ str_limit(strip_tags($getfav->blog->description), 75)  }}</p>
					</div>
				</div>
				@endforeach
			@endif  	
			</div>                         
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</div>