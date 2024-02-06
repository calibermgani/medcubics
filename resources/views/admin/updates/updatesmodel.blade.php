<?php
    $commentarray = json_decode(json_encode($commentcountarray), true);
    $blogpartarray = json_decode(json_encode($blogpartcountarray), true);
    $checkcommantarray = array_keys($commentarray);
    $checkblogpartarray = array_keys($blogpartarray);
?>
@foreach($blogs as $blog)
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding blog-border ">
		<div class="col-md-12 no-bottom">
			<?php 
				$blog_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($blog->id,'encode'); 
				if(isset($blog->attachment) && $blog->attachment!='')
					$ext = substr(strrchr($blog->attachment, "."), 1);
				else 
					$ext = '';
			?>
			<h4 class="med-green space10"><a href="#">{{ ucwords($blog->title) }}</a></h4>
			<h4 class="font12 med-gray margin-t-m-5"><i class="fa fa-user"></i> {{ ucwords(@$blog->user->name) }} <i class="fa fa-calendar-o margin-l-10"></i> {{ App\Http\Helpers\Helpers::dateFormat($blog->updated_at,'datetime') }} </h4>
			@if(isset($blog->url) && $blog->url != '')
				<p class="blog-text"><a href="{{ $blog->url }}" target="_blank"> {{ $blog->url }} </a></p>
			@endif
			@if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif')
				<?php
					$img_details = [];
					$img_details['module_name']='blog';
					$img_details['file_name']=$blog->attachment;
					$img_details['practice_name']="admin";
					$img_details['style']='width: 200px; height: 200px;';
					$blog_image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>
				<p class="blog-text">{!! @$blog_image_tag !!} </p>
			@endif
			<p class="blog-text"> <?php echo $blog->description; ?></p>
		</div>
	</div>
@endforeach