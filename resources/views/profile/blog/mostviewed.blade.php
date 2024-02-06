<div class="col-lg-12 col-md-12 col-xs-12 col-xs-12 p-r-0"  ><!-- Blog Header Starts -->
	<div class="box-header no-border">
		<i class="fa fa-navicon i-font-tabs"></i>  <h3 class="box-title">Most Discussed</h3>
		
	</div><!-- /.box-header -->

	<div class="box box-view no-shadow no-border no-border-radius">
		<div class="box-body padding-t-15 min-height-profile-blogs-left">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			   <?php	
					$mostviewed =  App\Http\Controllers\Profile\BlogController::getMostViewed(); 
					$mostviewedcount = count($mostviewed); 
				?>
				@if($mostviewedcount == '0')
					<p class="text-center margin-t-10">No blogs found</p>
				@else    
					@foreach($mostviewed as $getviewed)  
				
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding blog-fav blog-border">
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 no-padding margin-b-10">
							<?php
								$filename = @$getviewed->user->avatar_name.'.'.@$getviewed->user->avatar_ext;
								$img_details = [];
								$img_details['module_name']='user';
								$img_details['file_name']=$filename;
								$img_details['practice_name']="admin";
								
								$img_details['class']='ideal blog-user-list';
								$img_details['data-placement']='bottom';
								$img_details['data-toggle']='tooltip';
								$img_details['alt']='blog-image';
								$img_details['data-original-title']=$getviewed->user->name;
								$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
							?>
							{!! $image_tag !!}
						</div>
						<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
							<?php $blog_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($getviewed->id,'encode'); ?>
							<p class="med-green font600 no-bottom"><a href="{{ url('profile/blog/'.$blog_id) }}">{{ ucwords($getviewed->title)  }}</a></p>
							<p>{{ str_limit(strip_tags($getviewed->description), 75)  }}</p>
						</div>
					</div>			
					@endforeach
				@endif 
						  
			</div>

		</div>                         
	</div><!-- /.box-body -->
</div><!-- /.box -->