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
       $filename = $blog->title; 
       $avatar_url = App\Http\Helpers\Helpers::checkAndGetAvatar('user', $filename);
?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-blogs">
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">

            {!! HTML::image($avatar_url,null,['class'=>'  margin-r-20 space10','style'=>'width:50px; margin-top:12px; border-radius:50%; border:2px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
            -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);
            box-shadow: 0 0 5px rgba(0,0,0,0.5);   height:50px;']) !!}

            <h4 style="margin-top: 20px; margin-left: 5px;"><span class="font600" ><a href="javascript:void(0);" data-id="{{ $blog->id }}" class="blog_favourite" id="blog_fav{{ $blog->id }}"><?php echo ($favblogarray[$blog->id] == 1) ? '<i class="fa tooltips fa-star font16 med-orange font600" data-toggle="tooltip" data-title="Remove from favourite"></i>' : '<i class="fa tooltips fa-star-o font16 med-orange font600" data-toggle="tooltip" data-original-title="Add to favourite"></i>' ?></a> @if(in_array($blog->id,$checkfavarray)) <span class="favourite_count{{$blog->id}} font600"> {{ @$favarray[$blog->id] }} </span> @else <span class="favourite_count{{$blog->id}} font600">0</span> @endif</span></h4>

        </div>
        <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">
            <h4 class="med-green space10"><a href="{{ url('profile/blog/'.$blog->id) }}">{{ ucwords($blog->title)  }}</a></h4>
            <h4 class="font14" style="margin-top: -5px;">{{ ucwords(@$blog->user->name) }} - <span class="med-orange font13">{{ App\Http\Helpers\Helpers::dateFormat($blog->updated_at,'datetime') }}</span>
            @if($check_page == 2) 
             <span class="pull-right"><a href="{{ url('profile/blog/'.$blog->id.'/edit') }}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> </a> | <a class="js-delete-confirm" data-text="Are you sure would you like to delete this blog?" href="{{ url('profile/blog/delete/'.$blog->id) }}"><i class="fa fa-trash" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i> </a> </span>
            @endif
            </h4>    
            <p style='margin-bottom: 20px;'>{{ str_limit(strip_tags($blog->description), 350)  }}</p>

            <span class=" font600">Comments (@if(in_array($blog->id,$checkcommantarray)){{$commentarray[$blog->id]}}@else 0 @endif)</span> |
            <span class=" font600">Participants (@if(in_array($blog->id,$checkblogpartarray)){{ @$blogpartarray[$blog->id] }}@else 0 @endif)</span> |
             <span><a class="js-vote cur-pointer" name="up" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="up{{$blog->id}} fa fa-thumbs-o-up font600 font16 {{ (@$blog_vote[$blog->id]['up']==1)? 'med-orange' : 'med-green' }} " ></i> 
                     <span class="up{{$blog->id}} vote_up{{$blog->id}} font600 {{ (@$blog_vote[$blog->id]['up']==1)? 'med-orange' : 'med-green' }}">{{ $blog->up_count }} Votes</span></a> |</span>
            <span><a class="js-vote cur-pointer" name="down" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="down{{$blog->id}} fa fa-thumbs-o-down font600 font16 {{ (@$blog_vote[$blog->id]['down']==1)? 'med-orange' : 'med-green' }}"  ></i>
                <span class="down{{$blog->id}} vote_down{{$blog->id}} font600 {{ (@$blog_vote[$blog->id]['down']==1)? 'med-orange' : 'med-green' }}">{{ $blog->down_count }} Votes</span>
                    </span>
            <a href="{{ url('profile/blog/'.$blog->id) }}" class="pull-right" style="font-size: 13px;margin-bottom: 7px; border-radius: 4px; border:1px solid #00877f; padding: 0px 10px 0px 10px; cursor: pointer;" >Read More</a>
        </div>
    </div>
    <?php } ?>