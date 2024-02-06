@extends('admin')
@section('toolbar')
@include('profile/tabs')
<div class="col-lg-12 col-md-12 col-xs-12 col-xs-12" ><!-- Profile Header Starts -->
    <div class="box box-info no-shadow" style="border: 1px solid #a967aa">
        <div class="box-body bg-white border-radius-4">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom">     
                <h4 class="no-margin" style="border-bottom: 1px dotted #a967aa; color:#a967aa; padding-bottom: 5px;">Calendar <span class="pull-right" style="font-size: 13px;">{{count($events)}} Events</span></h4>
                @if(count($events) > 0)
                @foreach($events as $reminders)
                <?php $time  = strtotime($reminders->start_date);
							$time_in_12_hr_format  = date("g:i A", strtotime($reminders->start_time));
							$day = ltrim(date('d',$time), '0');
							$month = ltrim(date('m',$time), '0');
							$month_orgin = ltrim(date('M',$time), '0');
							$year  = date('Y',$time);
							$today_day =ltrim(date("d"), '0');
							$today_month =ltrim(date("m"), '0');
							$today_year =date("Y");
							$date=date('M j', strtotime($reminders->start_date));
							 ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10 js_count_list" id="event_list_{{$reminders->id}}">
                    <div class="col-lg-1">
                        <h4 class="no-bottom med-orange " style="text-align: center; font-size: 12px; padding-top: 2px; padding-bottom: 2px; border-radius: 4px 4px 0px 0px; background: #f0f0f0; margin-top: 0px;">{{$month_orgin}}</h4>
                        <h4 class="no-margin med-orange font16" style=" padding-bottom: 3px; padding-top: 2px; background: #fbf0cb; border-radius: 0px 0px 4px 4px; text-align: center">{{$day}}</h4>
                    </div>
                    <div class="col-lg-9">
                        <p id="title" class="med-green">{{$reminders->title}}</p>
                        <p id="content">{{$reminders->description}}</p>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 med-orange" style="font-weight:600;text-align:right;">
                        <p>{{$time_in_12_hr_format}}</p>
                    </div>
                </div>
                @endforeach
                @else

                <p class="text-center margin-t-15">No Events Found</p>

                @endif
            </div>

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!-- Profile Header Ends -->
<div class="col-lg-12 col-md-12 col-xs-12 col-xs-12" style="margin-top:-13px;" ><!-- Message Header Starts -->
    <div class="box box-info no-shadow no-border" style="background: #fff;">
        <div class="box-body table-responsive profile" style="border: 1px solid #abd250; border-radius: 4px;">
            <h4 class="no-margin" style="border-bottom: 1px dotted #abd250; color:#abd250; padding-bottom: 5px;">Message <span class="pull-right" style="font-size: 13px;">{{count($PrivateMessageDetails)}} Messages</span></h4>            

            @if(count($PrivateMessageDetails) > 0)
            <table id="example2" class="table">	
                <tbody>

                    @foreach($PrivateMessageDetails as $message_inbox_list_val)
                    <tr><?php
                        $d = $message_inbox_list_val->created_at;
                        $d = explode(" ", $d);
                        ?>
                        <?php	$date_f = App\Http\Helpers\Helpers::dateFormat(substr($d[0], 0, 25)); ?>
                        <td class="mailbox-subject"><b>{!! $date_f !!}</b>  {!! @$message_inbox_list_val->messagecontent_list !!}		</td>

                        <td class="mailbox-subject"><b>{!! substr($message_inbox_list_val->message_detail->subject, 0, 25) !!}</b>  {!! @$message_inbox_list_val->messagecontent_list !!}</td>
                        <td class="mailbox-subject"><b>{!! substr($message_inbox_list_val->message_detail->message_body, 0, 25) !!}</b>  {!! @$message_inbox_list_val->messagecontent_list !!}</td>

                        <td class="mailbox-date">{!! $d[1] !!}</td>
                    </tr>
                    @endforeach


                </tbody>
            </table><!-- /.table -->
            @else
            <p class="text-center margin-t-15">No Messages Found</p>
            @endif
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!-- Message Header Ends -->
<div class="col-lg-12 col-md-12 col-xs-12 col-xs-12" style="margin-top:-13px;"><!-- Tasks Header Starts -->
    <div class="box box-info no-shadow " style="border: 1px solid #118ab1">
        <div class="box-body" style="background: #fff; border-radius: 4px;">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom">                   
                <h4 class="no-margin" style="border-bottom: 1px dotted #118ab1; color:#118ab1; padding-bottom: 5px;">Tasks <span class="pull-right" style="font-size: 13px">4 Tasks</span></h4>                                    
                <h5 style="padding-bottom:2px; margin-left: 20px;  "> <i class="fa fa-plus font10"></i> Send last bill to the insurance company Cigna</h5>
                <h5 style="padding-bottom:2px; margin-left: 20px; "> <i class="fa fa-plus font10"></i>  Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has</h5>
                <h5 style="padding-bottom:2px; margin-left: 20px; "> <i class="fa fa-plus font10"></i>  Get a book on how to create a new practice from Medcubics</h5>
                <h5 style="padding-bottom:2px; margin-left: 20px; "> <i class="fa fa-plus font10"></i>  Send statement to Sophiya</h5>
            </div>

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!-- Tasks Header Ends -->

<div class="col-lg-12 col-md-12 col-xs-12 col-xs-12"  style="margin-top:-13px;"><!-- Blog Header Starts -->
    <div class="box box-info no-shadow" style="border:1px solid #00877f;">
        <div class="box-body" style="background: #fff; border-radius: 4px;">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom"> 
                <h4 class="no-margin med-green" style="padding-bottom: 5px; border-bottom: 1px dotted #00877f;">Blogs <span class="pull-right" style="font-size: 13px; color:#00877f !important">{{count($blogs)}} Blogs</span></h4>
                @if(count($blogs)>0)
                @foreach($blogs as $blog)  
                <?php $blog_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($blog->id,'encode'); ?>
                <div class="col-lg-1 col-md-1 col-sm-3 col-xs-3">
                    <?php
						$filename = $blog->title;
						$img_details = [];
						$img_details['module_name']='user';
						$img_details['file_name']=$filename;
						$img_details['practice_name']="";
						$img_details['style']="height:auto";
						$img_details['class']='margin-r-20 space10 img-responsive blogs-img';
						$img_details['alt']='blog-image';
						$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
					?>
                    {!! $image_tag !!}
                </div>
                <div class="col-lg-11 col-md-11 col-sm-9 col-xs-9">
                    @if(count($blogs) == '0')
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-bottom" style="border-bottom: 1px dotted #00877f;">
                        No Blogs Found
                    </div>
                    @else
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-bottom" style="border-bottom: 1px dotted #00877f;">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-bottom">
                            <h4 class="med-green"><a href="{{ url('profile/blog/'.$blog_id) }}">{{ ucwords($blog->title)  }}</a> <h6 class="space-m-t-7 med-orange"><i class="fa fa-clock-o"></i> {{ App\Http\Helpers\Helpers::dateFormat($blog->updated_at,'datetime') }}</h6></h4>
                            <p>{{ str_limit(strip_tags($blog->description), 220)  }}
                            <a href="{{ url('profile/blog/'.$blog_id) }}" class="pull-right font600">Know More</a>
                            </p>
                        </div>
                        @endif
                        
                    </div>
                </div> 
                @endforeach
                @else
                <tr>
                    <td align="center" style="font-weight: bold;">No Blogs Available</td>
                </tr>
                @endif
            </div>
        </div>
    </div><!-- /.box-body -->
</div><!-- /.box -->
</div><!-- Blog Header Ends -->
</div><!-- Left side Outer body Ends -->
<div class="col-lg-3 col-md-3 col-sm-6  col-xs-12" style="padding-left: 0px;">
    @include('profile/rightside-tabs')
</div>
<!--End-->
@stop