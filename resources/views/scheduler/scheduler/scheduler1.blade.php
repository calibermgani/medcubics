@extends('admin_stat_view')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="home"></i> Home </small> <span class="font14 med-orange">|</span> <small class="toolbar-heading"> <i class="livicon med-breadcrum" data-name="calendar"></i> Calendar </small> <span class="font14 med-orange">|</span> <small class="toolbar-heading"> <i class="livicon med-breadcrum" data-name="user"></i> Patient List </small> <span class="font14 med-orange">|</span> <small class="toolbar-heading"> <i class="livicon med-breadcrum" data-name="calendar"></i> Scheduler </small>
        </h1>
        <ol class="breadcrumb">          
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href=""><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="box no-shadow no-border margin-t-m-20">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
        
    
        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 no-padding  margin-t-m-20 bg-white border-radius-4" style="border:1px solid #85E2E6;">      
			<div class="box-body no-padding">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<div class="box no-shadow no-border">
						<div class="box-body no-padding">
							<!--The calendar -->
							<div id="scheduler_calendar" class="bg-white"></div>
						</div><!-- /.box-body -->
					</div><!-- /.box -->
				</div>
			</div>
			<div class="box-body margin-t-m-20">
				
				<div class=" col-lg-12 col-md-12 col-sm-6 col-xs-12 margin-b-20"> 
			   
					<div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<input type="radio" name="provider" class="flat-red"> Provider       &emsp;<input type="radio" name="provider" class="flat-red"> Facility
					</div>
				</div>
				<div class=" col-lg-12 col-md-12 col-sm-6 col-xs-12 no-padding">                            
					<div class="box no-shadow no-border" style="background: transparent" >
						<div class="box-header-view no-border-radius" style="border-bottom: 1px solid #00877f; background: transparent">
							<i class="livicon" data-name="users-add"></i>
							<h3 class="box-title">Facilities</h3>
							<div class="box-tools pull-right">
								<button class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
							</div>
						</div>

						<div class="box-body chat" id="chat-scheduler">
							<ul class="list-group list-group-unbordered no-bottom" style="line-height: 14px;">
								<?php $i = 1; ?>
								@foreach($facilities as $keys=>$facility)
								<?php
									$facility_class = null;
									if ($facility->id == $facility_id) {
										$facility_class = true;
									}
								?>
								<li class="list-group-item" style="background: transparent; margin-bottom:  -12px;">
									{!! Form::radio('facility', $facility->id,$facility_class,['class'=>'js-scheduler_calendar js-scheduler_calendar_facility','id'=>'sc'.$keys]) !!} 
									<label class="med-darkgray form-cursor" for="sc{{$keys}}">{{substr($facility->facility_name, 0, 20)}}</label>
								</li>

								@endforeach    

							</ul>
						</div><!-- /.chat -->
					</div><!-- /.box (chat box) -->
				</div>
					   
			</div><!-- /.box-body -->   
        
			<div class="box-body  margin-t-m-20">
				<div class=" col-lg-12 col-md-12 col-sm-6 col-xs-12 no-padding">
					<div class="box no-shadow no-border" style="background: transparent">
						<div class="box-header-view no-border-radius" style="border-bottom: 1px solid #00877f;  background: transparent">
							<i class="livicon" data-name="home"></i>
							<h3 class="box-title">Providers</h3>
							<div class="box-tools pull-right">
								<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>

						<div class="box-body chat chat-facility" id="js-provider-listing">
							@include('scheduler/scheduler/scheduler_provider_listing')

						</div><!-- /.chat -->
					</div><!-- /.box (chat box) -->
				</div>
			</div>
			
			 <div class="box-body margin-t-m-20">

				<div class=" col-lg-12 col-md-12 col-sm-6 col-xs-12 no-padding">
					<div class="box no-shadow no-border" style="background: transparent">
						<div class="box-header-view no-border-radius" style="border-bottom: 1px solid #00877f;  background: transparent">
							<i class="livicon" data-name="retweet"></i>
							<h3 class="box-title">Appointments</h3>
							<div class="box-tools pull-right">
								<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>
						<div class="box-body chat" id="chat-resource">

							<p style="margin-top:10px;"><span class="font600" style="color:#CAAA26"><i class="fa fa-calendar" style="color: #CAAA26;"></i> Scheduled </span> <span class="pull-right font12">{{$index_stats_count->scheduled}}</span></p>
							<p style="margin-top:-2px;"><span class="font600" style="color: #019e98"><i class="fa fa-calendar-check-o" style="color:#019e98"></i> Check In </span> <span class="pull-right font12">{{$index_stats_count->checkin}}</span></p>
							<p style="margin-top:-2px;"><span class="font600" style="color:#2EB143"><i class="fa fa-calendar-o" style="color:#2EB143"></i> Completed </span> <span class="pull-right font12">{{$index_stats_count->completed}}</span></p>
							<p style="margin-top:-2px; margin-bottom: -15px;"><span class="font600" style="color:#DF7A89;"><i class="fa fa-calendar-times-o" style="color:#DF7A89"></i> Canceled </span> <span class="pull-right font12">{{$index_stats_count->canceled}}</span></p>           
						</div><!-- /.chat -->
					</div><!-- /.box (chat box) -->
				</div>
			</div><!-- /.box-body -->
		</div>        
        
    
		<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 margin-t-m-20">
			
			<div class="box no-border no-shadow">  
				
				<div class="box-body " >
					<!--The calendar -->
					<div id="calendar"></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div> 
    
	</div><!-- /.box -->
</div>

<!--End-->
@stop

@push('view.scripts')
<script type="text/javascript">
    // $(document).ready(function () {
    // $(function () { alert('ffff');
    $("#scheduler_calendar").datepicker({
        dateFormat: "yy-mm-dd",
        onSelect: function (dateText)
        {
            current_date = $(this).val();
            triggerCalendar();
        }
    });
    // });      
    //});

    var current_date = '{{date("Y-m-d")}}';
    triggerCalendar();
</script>
@endpush