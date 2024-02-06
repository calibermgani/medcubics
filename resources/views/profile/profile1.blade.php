@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="user"></i> Profile </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')

<div class="col-lg-9 col-md-9 col-xs-8 col-xs-12" style="margin-top:-10px;"><!-- Left side Outer body starts -->
    
    <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12"><!-- Profile Header Starts -->
        <div class="box box-info no-shadow" style="border: 1px solid #85E2E6">
            <div class="box-body" style="margin-bottom: -7px;">


                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 tab-border-bottom">
                    {!! HTML::image('img/profile-pic.jpg',null,['class'=>'  margin-r-20 space10','style'=>'width:90px; margin-top:12px; border-radius:50%; border:3px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                    -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                    box-shadow: 0 0 5px rgba(0,0,0,0.5);   height:90px; float:left;']) !!}

                    <h5 class="med-green no-margin">Sophia, Mortaza</h5>
                    <h5 class=""><span class="med-orange sm-size"><i class="med-orange med-gender fa fa-female margin-r-5 "></i> Feb 12,1975, 41 years</span></h5>                
                    <h5 class="space20" style="margin-top: 30px;">1001 W Fayette St, Suite 400,</h5>
                    <h5>Syracuse - NY, 13204 - 2859</h5>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 med-left-border">
                    <ul class="icons push no-padding">					
                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green">Phone : </span>(315) 472-1488</li>

                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green">Mobile : </span>(415) 682-4234</li>

                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green">Email : </span><a href="" class="med-orange">sophiya@gmail.com</a></li>

                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12 space10">
                            <a href=""><i class="fa fa-facebook-square font20 facebook"></i></a> 
                            <a href=""><i class="fa fa-twitter-square font20 margin-l-5 twitter"></i></a> 
                            <a href=""><i class="fa fa-linkedin-square font20 margin-l-5 linkedin"></i></a>
                            <a href=""><i class="fa fa-google-plus-square font20 margin-l-5 gplus"></i></a>
                        </li>					                    
                    </ul>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- Profile Header Ends -->
    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
		<div class="timeline-profile">
			<div class="label-month">January 2016</div>
			<div class="box-feed">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="box-post left">                           
						<h5 style="background:#f07d08; color:#fff; padding: 2px 6px 5px;">Top 10 tools for writing blog</h5>
						<p style="padding:0px 6px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam posuere lacus quis dolor. Integer imperdiet lectus quis justo. Ut enim ad minima veniam.</p>                            
						<p>
							<img src="img/profile-pic.jpg" style="width:40px; margin-right: 10px; height:40px; float: left; border-radius: 50%; margin-left:20px; border:2px solid #00877f;">
						<h4 class="no-padding no-margin med-green font16">Sophia, Mortaza</h4>
						<h6 class="no-margin" style="color:#ccc; padding-bottom:10px;">Monday, Jan 22</h6>
						</p>
					</div>                       
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="box-post right">                          
						<h5 style="background:#f07d08; color:#fff; padding: 2px 6px 5px;">Top 10 tools for writing blog</h5>
						<p style="padding:6px;">Etiam posuere lacus quis dolor. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integer imperdiet lectus quis justo. Ut enim ad minima veniam.</p>                            
						<p>
							<img src="img/profile-pic.jpg" style="width:40px; margin-right: 10px; height:40px; float: left; border-radius: 50%; margin-left:20px; border:2px solid #00877f;">
						<h4 class="no-padding no-margin med-green font16">Sophia, Mortaza</h4>
						<h6 class="no-margin" style="color:#ccc; padding-bottom:10px;">Monday, Jan 22</h6>
						</p>
					
					</div>                      
				</div>
			</div>
			<div class="box-feed">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="box-post left">
						<h5 style="background:#f07d08; color:#fff; padding: 2px 6px 5px;">Top 10 tools for writing blog</h5>
						<p style="padding:6px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam posuere lacus quis dolor. Integer imperdiet lectus quis justo. Ut enim ad minima veniam.</p>                           
						<p>
							<img src="img/profile-pic.jpg" style="width:40px; margin-right: 10px; height:40px; float: left; border-radius: 50%; margin-left:20px; border:2px solid #00877f;">
						<h4 class="no-padding no-margin med-green font16">Sophia, Mortaza</h4>
						<h6 class="no-margin" style="color:#ccc; padding-bottom:10px;">Monday, Jan 22</h6>
						</p>
					</div>                        
				</div>
				
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="box-post right">
						<h5 style="background:#f07d08; color:#fff; padding: 2px 6px 5px;">Top 10 tools for writing blog</h5>
						<p style="padding:6px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam posuere lacus quis dolor. Integer imperdiet lectus quis justo. Ut enim ad minima veniam.</p>                            
						<p>
							<img src="img/profile-pic.jpg" style="width:40px; margin-right: 10px; height:40px; float: left; border-radius: 50%; margin-left:20px; border:2px solid #00877f;">
						<h4 class="no-padding no-margin med-green font16">Sophia, Mortaza</h4>
						<h6 class="no-margin" style="color:#ccc; padding-bottom:10px;">Monday, Jan 22</h6>
						</p>
					</div>                       
				</div>                    
			</div>
			
			<p class="lg-subtitle text-center">&emsp;</p>
			<div class="label-month">December 2015</div>
			<div class="box-feed">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="box-post left">                           
						<h5 style="background:#f07d08; color:#fff; padding: 2px 6px 5px;">Sophia, Mortaza <span class="pull-right" style="color:#673d13">Monday, Jan 22</span></h5>
						<p style="padding:6px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam posuere lacus quis dolor. Integer imperdiet lectus quis justo. Ut enim ad minima veniam.</p>                            
						
					</div>                       
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="box-post right">                          
						<h5 style="background:#f07d08; color:#fff; padding: 2px 6px 5px;">Annabelle Violet <span class="pull-right" style="color:#673d13">Monday, Jan 22</span></h5>
						<p style="padding:6px;">Etiam posuere lacus quis dolor. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integer imperdiet lectus quis justo. Ut enim ad minima veniam.</p>                                                       
					</div>                      
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="box-post left">                           
						<h5 style="background:#f07d08; color:#fff; padding: 2px 6px 5px;">Melanie Andrews <span class="pull-right" style="color:#673d13">Monday, Jan 22</span></h5>
						<p style="padding:6px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam posuere lacus quis dolor. Integer imperdiet lectus quis justo. Ut enim ad minima veniam.</p>                            
						
					</div>                       
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="box-post right">                          
						<h5 style="background:#f07d08; color:#fff; padding: 2px 6px 5px;">Isabelle Joseph <span class="pull-right" style="color:#673d13">Monday, Jan 22</span></h5>
						<p style="padding:6px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam posuere lacus quis dolor. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integer imperdiet lectus quis justo. Ut enim ad minima veniam.</p>                                                       
					</div>                      
				</div>
			</div>
			
			<p class="lg-subtitle text-center">&emsp;</p>
			<div class="label-month">Novenber 2015</div>
			<div class="row box-feed">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="box-post left">                           
						<h5 style="background:#f07d08; color:#fff; padding: 2px 6px 5px;">Top 10 tools for writing blog</h5>
						<p style="padding:6px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam posuere lacus quis dolor. Integer imperdiet lectus quis justo. Ut enim ad minima veniam.</p>                            
						<p>
							<img src="img/profile-pic.jpg" style="width:40px; margin-right: 10px; height:40px; float: left; border-radius: 50%; margin-left:20px; border:2px solid #00877f;">
						<h4 class="no-padding no-margin med-green font16">Sophia, Mortaza</h4>
						<h6 class="no-margin" style="color:#ccc; padding-bottom:10px;">Monday, Jan 22</h6>
						</p>
					</div>                       
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="box-post right">                          
						<h5 style="background:#f07d08; color:#fff; padding: 2px 6px 5px;">Top 10 tools for writing blog</h5>
						<p style="padding:6px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam posuere lacus quis dolor. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integer imperdiet lectus quis justo. Ut enim ad minima veniam.</p>                            
						<p>
							<img src="img/profile-pic.jpg" style="width:40px; margin-right: 10px; height:40px; float: left; border-radius: 50%; margin-left:20px; border:2px solid #00877f;">
						<h4 class="no-padding no-margin med-green font16">Sophia, Mortaza</h4>
						<h6 class="no-margin" style="color:#ccc; padding-bottom:10px;">Monday, Jan 22</h6>
						</p>
					</div>                      
				</div>
			</div>

		</div>        
    </div>
</div><!-- Left side Outer body Ends -->

<div class="col-lg-3 col-md-3 col-sm-6  col-xs-12" style="padding-left: 0px;">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-7 no-padding">
    <div id="dob" style="margin-top:-3px;" ><div class="ui-datepicker-inline ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" style="display: block; background: #fff; border-radius: 4px; border:1px solid #85E2E6 !important"><div class="ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all" style="background: #f0f0f0;"><a class="ui-datepicker-prev ui-corner-all" data-handler="prev" data-event="click" title="Prev"><span class="ui-icon ui-icon-circle-triangle-w">Prev</span></a><a class="ui-datepicker-next ui-corner-all" data-handler="next" data-event="click" title="Next"><span class="ui-icon ui-icon-circle-triangle-e">Next</span></a><div class="ui-datepicker-title" style="color:#00877f"><span class="ui-datepicker-month">January</span>&nbsp;<span class="ui-datepicker-year">2016</span></div></div><table class="ui-datepicker-calendar"><thead><tr><th scope="col" class="ui-datepicker-week-end"><span title="Sunday">Sun</span></th><th scope="col"><span title="Monday">Mon</span></th><th scope="col"><span title="Tuesday">Tue</span></th><th scope="col"><span title="Wednesday">Wed</span></th><th scope="col"><span title="Thursday">Thu</span></th><th scope="col"><span title="Friday">Fri</span></th><th scope="col" class="ui-datepicker-week-end"><span title="Saturday">Sat</span></th></tr></thead><tbody><tr><td class=" ui-datepicker-week-end ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">1</a></td><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">2</a></td></tr><tr><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">3</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">4</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">5</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">6</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">7</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">8</a></td><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">9</a></td></tr><tr><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">10</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">11</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">12</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">13</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">14</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">15</a></td><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">16</a></td></tr><tr><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">17</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">18</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">19</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">20</a></td><td class=" ui-datepicker-days-cell-over  ui-datepicker-current-day ui-datepicker-today" data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default ui-state-highlight ui-state-active ui-state-hover" href="#">21</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">22</a></td><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">23</a></td></tr><tr><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">24</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">25</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">26</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">27</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">28</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">29</a></td><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">30</a></td></tr><tr><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">31</a></td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-week-end ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td></tr></tbody></table></div></div>
</div>     


    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10" style="background: #fff;">
        <h4 style="border-bottom: 1px solid #00877f;">Event's of this week<span class="pull-right med-orange" style="font-size:13px; line-height: 22px;"><i class="fa fa-newspaper-o"></i> 6</span></h4>
    
    <h6 class="space20" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 4px;">January 13 - Friday</h6>
    <h5 style="background: #fef8f0; padding: 4px;border: 1px solid #ebeaea; border-radius: 4px; border-left: 2px solid #f07d08;">Meeting Dr.Crono <span class="pull-right med-orange" style="font-size:11px; line-height: 15px;"><i class="fa fa-clock-o"></i> 12:45</span></h5>
    <h5 style="background: #fef8f0; padding: 4px;border: 1px solid #ebeaea; border-radius: 4px; border-left: 2px solid #f07d08;">Meeting Dr.Williams <span class="pull-right med-orange" style="font-size:11px; line-height: 15px;"><i class="fa fa-clock-o"></i> 12:45</span></h5>
     
    <h6 class="space20" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 4px;">January 16 - Monday</h6>
    
    <h5 style="background: #fef8f0; padding: 4px;border: 1px solid #ebeaea; border-radius: 4px; border-left: 2px solid #f07d08;">Meeting Dr.Williams <span class="pull-right med-orange" style="font-size:11px; line-height: 15px;"><i class="fa fa-clock-o"></i> 12:45</span></h5>
      <h5 style="background: #fef8f0; padding: 4px;border: 1px solid #ebeaea; border-radius: 4px; border-left: 2px solid #f07d08;">Meeting Dr.Williams <span class="pull-right med-orange" style="font-size:11px; line-height: 15px;"><i class="fa fa-clock-o"></i> 17:35</span></h5>
       <h5 style="background: #fef8f0; padding: 4px;border: 1px solid #ebeaea; border-radius: 4px; border-left: 2px solid #f07d08;">Meeting Dr.Williams <span class="pull-right med-orange" style="font-size:11px; line-height: 15px;"><i class="fa fa-clock-o"></i> 09:20</span></h5>
      
       <h6 class="space20" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 4px;">January 17 - Tuesday</h6>
       <h5 style="background: #fef8f0; padding: 4px;border: 1px solid #ebeaea; border-radius: 4px; border-left: 2px solid #f07d08;">Meeting Dr.Crono <span class="pull-right med-green" style="font-size:11px;"><i class="fa fa-clock-o"></i> 12:45</span></h5>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-7 no-padding">

    <div class="box box-info no-shadow space20">
        <div class="box-header with-border">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Team Members</h3>
            <div class="box-tools pull-right" style="margin-top: 3px;">
                  
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <ul class="no-padding" style="list-style-type: none; line-height: 45px;">
                <li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'online','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Melanie Willams <span class="pull-right" style="font-size: 11px; color:#ccc">Admin</span></li>
                <li>{!! HTML::image('img/del.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Mackenzie George <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
                <li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Melanie Andrews <span class="pull-right" style="font-size: 11px; color:#ccc">Front Desk</span></li>
                <li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'online','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Isabelle Joseph <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
                <li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'online','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Annabelle Violet <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
                <li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'online','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Brooklyn <span class="pull-right" style="font-size: 11px; color:#ccc">Admin</span></li>
                <li>{!! HTML::image('img/del.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Gabriella <span class="pull-right" style="font-size: 11px; color:#ccc">Front Desk</span></li>
                <li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Mackenzie John <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
                <li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Penelope <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
                <li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Melanie Andrews <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
            </ul>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
</div>
<!--End-->
@stop