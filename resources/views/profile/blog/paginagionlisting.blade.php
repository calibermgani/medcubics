<?php 
    $favblogarray = json_decode(json_encode($favblogarray),true);
    $favarray = json_decode(json_encode($favcountarray),true); 
    $commentarray = json_decode(json_encode($commentcountarray),true); 
    $blogpartarray = json_decode(json_encode($blogpartcountarray),true); 
    $blog_vote     = json_decode(json_encode($blogvotearray),true); 

    $checkfavarray = array_keys($favarray);
    $checkcommantarray = array_keys($commentarray);
    $checkblogpartarray = array_keys($blogpartarray);

    foreach($blogs as $blog) {

		$filename = $blog->user->avatar_name.'.'.$blog->user->avatar_ext;
		$img_details = [];
		$img_details['module_name']='user';
		$img_details['file_name']=$filename;
		$img_details['practice_name']="admin";
		$img_details['class']='margin-r-20 space10 img-responsive blogs-img';
		$img_details['alt']='blog-image';
		$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
?>
<i class="fa fa-spinner fa-spin coverloadingimg" id="listingimg" style="display:none" ></i>
 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 blog-border">
	<div class="col-lg-2 col-md-1 col-sm-3 col-xs-3">
		{!! $image_tag !!}
		<h4 style="margin-left:30%;"><span class=""><a href="javascript:void(0);" data-id="{{ $blog->id }}" class="blog_favourite" id="blog_fav{{ $blog->id }}"><?php echo ($favblogarray[$blog->id] == 1) ? '<i class="fa tooltips fa-star font16 med-orange font600" data-toggle="tooltip" data-title="Remove from favourite"></i>' : '<i class="fa tooltips fa-star-o font16 med-orange font600" data-toggle="tooltip" data-original-title="Add to favourite"></i>' ?></a> @if(in_array($blog->id,$checkfavarray)) <span class="favourite_count{{$blog->id}} "> {{ @$favarray[$blog->id] }} </span> @else <span class="favourite_count{{$blog->id}}">0</span> @endif</span></h4>

	</div>

	<div class="col-lg-10 col-md-11 col-sm-9 col-xs-9 no-bottom">
		<?php $blog_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($blog->id,'encode'); ?>
		<h4 class="med-green space10"><a href="{{ url('profile/blog/'.$blog_id) }}">{{ ucwords($blog->title)  }}</a></h4>
		<h4 class="font12 med-gray margin-t-m-5"><i class="fa fa-user"></i> {{ ucwords(@$blog->user->name) }} <i class="fa fa-calendar-o margin-l-10"></i> {{ App\Http\Helpers\Helpers::dateFormat($blog->updated_at,'datetime') }} </h4>
		
		
		@if($check_page == 2) 
		 <span class="pull-right"><a href="{{ url('profile/blog/'.$blog_id.'/edit') }}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> </a> | <a class="js-delete-confirm" data-text="Are you sure would you like to delete this blog?" href="{{ url('profile/blog/delete/'.$blog_id) }}"><i class="fa fa-trash" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i> </a> </span>
		@endif

		<p class="blog-text">{{ str_limit(strip_tags($blog->description), 500)  }}</p>

		<span class="med-gray font12"><i class="fa fa-comments"></i> @if(in_array($blog->id,$checkcommantarray)){{$commentarray[$blog->id]}}@else 0 @endif Comments</span> |

		<span class="med-gray"><i class="fa fa-user"></i> @if(in_array($blog->id,$checkblogpartarray)){{ @$blogpartarray[$blog->id] }}@else 0 @endif Participants</span> |


		<span> <a class="js-vote cur-pointer" name="up" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="up{{ $blog->id }} fa fa-thumbs-o-up  font14 {{ (@$blog_vote[$blog->id]['up']==1)? 'med-gray' : 'med-gray' }} " ></i> 
				<span class="up{{ $blog->id }} vote_up{{ $blog->id }}  {{ (@$blog_vote[$blog->id]['up']==1)? 'med-gray' : 'med-gray' }}">{{ $blog->up_count }} Votes</span></a> </span> |
		<span><a class="js-vote cur-pointer" name="down" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="down{{ $blog->id }} fa fa-thumbs-o-down  font14 {{ (@$blog_vote[$blog->id]['down']==1)? 'med-gray' : 'med-gray' }}"  ></i>
				<span class="down{{ $blog->id }} vote_down{{ $blog->id }}  {{ (@$blog_vote[$blog->id]['down']==1)? 'med-gray' : 'med-gray' }}">{{ $blog->down_count }} Votes</span></a>
		</span>

		@if($checkpermission->check_url_permission('profile/blog/{blog}') == 1)
		<a href="{{ url('profile/blog/'.$blog_id) }}" class="pull-right font13 margin-b-6 cur-pointer">Read More ...</a>
		@endif 
	</div>
</div>
<?php } ?>
<div id="results"></div>	