@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Tickets <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Updates</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#js-help-modal" data-url="{{url('help/blog')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-13"><!-- Left side Outer body starts -->
   
    
    <?php
		$favarray = json_decode(json_encode($favcountarray), true);
		$commentarray = json_decode(json_encode($commentcountarray), true);
		$blogpartarray = json_decode(json_encode($blogpartcountarray), true);
		$favblogarray = json_decode(json_encode($favblogarray), true);
		$blog_vote = json_decode(json_encode($blogvotearray), true);

		$checkfavarray = array_keys($favarray);
		$checkcommantarray = array_keys($commentarray);
		$checkblogpartarray = array_keys($blogpartarray);
    ?>

    <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12 no-padding"  ><!-- Blog Header Starts -->
        <div class="med-tab nav-tabs-custom no-bottom">
            <ul class="nav nav-tabs">                      	          
                <li class=" active"><a href="" style="background:#fff !important;"><i class="fa fa-comments i-font-tabs"></i> Updates</a></li>
              
                @if($checkpermission->check_url_permission('admin/updates/create') == 1)
                <li class="pull-right">                
                    <a href="{{ url('admin/updates/create') }}" ><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Updates</a>             
                </li>
                @endif
            </ul>
        </div>
        
        <div class="box box-view no-shadow no-border no-border-radius">
            <div class="box-body padding-t-20">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-aqua padding-10">
                    <div class="col-lg-5 col-md-6 col-sm-4 xol-xs-5 no-padding">                
                        <span class="input-group input-group-sm">
                            <input type="text" placeholder="Search blog using key words" class="form-control js_search_blog_list" name="search_blog_keyword">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-flat btn-medgreen js_search_update_button">Search</button>
                            </span>
                        </span>
                        <span class="error" id='error_keyword' style="display:none;"><p>Enter the keyword<p></span>
                    </div>	
                    <div class="col-lg-7 col-md-6 col-sm-8 col-xs-12 no-padding">  

                        <select id="js-updates-sort" class="dev-sort" style="color:black;float:right;padding:4px">
					<option value="">Sort By :</option>
					<option class="demo" value="newest" data-value="newest">Newest</option>
					<option class="demo" value="oldest" data-value="oldest">Oldest</option>
					<option class="demo" value="highcomment" data-value="highcomment">High Comment</option>
					<option class="demo" value="lowcomment" data-value="lowcomment">Low Comment</option>
					</select>                                                    

                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tab-border-bottom margin-t-20 js_blog_order min-height-profile-blogs"> 
                    <i class="fa fa-spinner fa-spin coverloadingimg" id="listingimg" style="display:none" ></i>

                    @if(count($blogs) == '0')
                    <p class="text-center">No Blogs Found</p>
                    @endif     
                    @foreach($blogs as $blog)  

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding blog-border ">
                        <div class="col-lg-02 col-md-2 col-sm-3 col-xs-3">
                            <?php
							$filename = $blog->user->avatar_name.'.'.$blog->user->avatar_ext;
							$img_details = [];
							$img_details['module_name']='user';
							$img_details['file_name']=$filename;
							$img_details['practice_name']="admin";
							
							$img_details['class']='margin-r-20 space10 img-responsive blogs-img';
							$img_details['alt']='blog-image';
							$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
						?>
                            {!! $image_tag !!}
                            <h4 style="margin-left:30%;"><span class=""><a href="javascript:void(0);" data-id="{{ $blog->id }}" class="blog_favourite" id="blog_fav{{ $blog->id }}"><?php echo ($favblogarray[$blog->id] == 1) ? '<i class="fa tooltips fa-star font16 med-orange font600" data-toggle="tooltip" data-title="Remove from favourite"></i>' : '<i class="fa tooltips fa-star-o font16 med-orange font600" data-toggle="tooltip" data-original-title="Add to favourite"></i>' ?></a> @if(in_array($blog->id,$checkfavarray)) <span class="favourite_count{{$blog->id}} "> {{ @$favarray[$blog->id] }} </span> @else <span class="favourite_count{{$blog->id}}">0</span> @endif</span></h4>

                        </div>

                        <div class="col-lg-10 col-md-10 col-sm-9 col-xs-9 no-bottom">

                            <?php $blog_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($blog->id,'encode'); ?>

                            <h4 class="med-green space10"><a href="{{ url('admin/updates/'.$blog_id) }}">{{ ucwords($blog->title)  }}</a></h4>
                            <h4 class="font12 med-gray margin-t-m-5"><i class="fa fa-user"></i> {{ ucwords(@$blog->user->name) }} <i class="fa fa-calendar-o margin-l-10"></i> {{ App\Http\Helpers\Helpers::dateFormat($blog->updated_at,'datetime') }} </h4>


                            <p class="blog-text">{{ str_limit(strip_tags($blog->description), 500)  }}</p>

                            <span class="med-gray font12"><i class="fa fa-comments"></i> @if(in_array($blog->id,$checkcommantarray)){{$commentarray[$blog->id]}}@else 0 @endif Comments</span> |

                            <span class="med-gray"><i class="fa fa-user"></i> @if(in_array($blog->id,$checkblogpartarray)){{ @$blogpartarray[$blog->id] }}@else 0 @endif Participants</span> |


                            <span> <a class="js-vote cur-pointer" name="up" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="up{{ $blog->id }} fa fa-thumbs-o-up  font14 {{ (@$blog_vote[$blog->id]['up']==1)? 'med-gray' : 'med-gray' }} " ></i> 
                                    <span class="up{{ $blog->id }} vote_up{{ $blog->id }}  {{ (@$blog_vote[$blog->id]['up']==1)? 'med-gray' : 'med-gray' }}">{{ $blog->up_count }} Votes</span></a> </span> |
                            <span><a class="js-vote cur-pointer" name="down" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="down{{ $blog->id }} fa fa-thumbs-o-down  font14 {{ (@$blog_vote[$blog->id]['down']==1)? 'med-gray' : 'med-gray' }}"  ></i>
                                    <span class="down{{ $blog->id }} vote_down{{ $blog->id }}  {{ (@$blog_vote[$blog->id]['down']==1)? 'med-gray' : 'med-gray' }}">{{ $blog->down_count }} Votes</span></a>
                            </span>

                            @if($checkpermission->check_url_permission('admin/updates/{blog}') == 1)
                            <a href="{{ url('admin/updates/'.$blog_id) }}" class="pull-right font13 margin-b-6 cur-pointer">Read More ...</a>
                            @endif 
                        </div>
                    </div>
                    @endforeach
                      
                    <div id="results"></div>
                </div>
               

            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- Blog Header Ends -->
</div>

<style>
    .pull-right #js-blogorder {float:left;}
</style>
<!--End-->
@stop