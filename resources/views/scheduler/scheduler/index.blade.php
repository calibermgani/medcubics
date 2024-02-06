@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-calendar-o font14"></i> Scheduler</small>
        </h1>
        <ol class="breadcrumb">
            <?php /*?>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <?php */?>
            <li><a href="#js-help-modal" data-url="{{url('help/scheduler')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>

@stop

@section('practice')

<div class="box no-shadow no-border">
    <div class="no-padding"><!-- Removed box-header med-bg-f0f0f0 -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_datepicker_scroll" style="margin-top: -16px; margin-bottom: -4px;">
            <div class="box-tools pull-right margin-t-0">
                <a class="cur-pointer" data-widget="collapse"><i class="fa fa-minus yes-border padding-0-4 border-green"></i></a>
            </div>

            <div class="pull-right margin-r-20">
                <a class="js-new_appointment form-cursor font13 font600" accesskey="a"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Appointment</a>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 font600">
                <span class="med-orange">View By </span>&emsp; 
                {!! Form::radio('default_view', Config::get('siteconfigs.scheduler.default_view_facility'),(Cache::get('default_view') != Config::get('siteconfigs.scheduler.default_view_provider'))?true:null,['class'=>' js-default_view_option','id'=>'c-facility']) !!} {!! Form::label('c-facility', 'Facility',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                {!! Form::radio('default_view', Config::get('siteconfigs.scheduler.default_view_provider'),(Cache::get('default_view') == Config::get('siteconfigs.scheduler.default_view_provider'))?true:null,['class'=>' js-default_view_option','id'=>'c-provider']) !!} {!! Form::label('c-provider', 'Provider',['class'=>'med-darkgray font600 form-cursor']) !!}
            </div>
        </div>
    </div><!-- /.box-header -->

    <div class="box-body box-body-bg margin-t-0">
        <div class="col-md-3 m-b-m-20">
            <div class="box no-shadow no-border">
                <div class="box-body no-padding">
                    <!--The calendar -->
                    <div id="scheduler_calendar"></div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
        <div class="col-md-9 p-r-0">
            <div class="box-block">
                <div class="box-body m-b-m-5 js-default-view-div">
                    @include('scheduler/scheduler/default_view_form')
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div><!-- /.box-body -->              
</div><!-- /.box -->

<div class="col-md-12" >
    <div class="box no-border no-shadow no-padding">                
        <div class="box-body no-padding" >
            {!! Form::hidden('check_selected_time','',['id'=>'check_selected_time']) !!} 
			<!--The calendar -->
            <div id="calendar"></div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!--End-->

<div id="fullCalendarModal1" class="modal fade in">
    <div class="modal-md-scheduler">
        <div class="modal-content">           
            <div class="modal-body no-padding med-green-border">
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="fullCalendarModal" class="modal fade in">
    
</div>

<div id="fullCalendarModal_schedular" class="modal fade in">
    <div class="modal-md-scheduler">
        <div class="modal-content">           
            <div class="modal-body no-padding yes-border med-border-color">
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
<div id="auth" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="false">&times;</span></button>
                <h4 class="modal-title">Authorization</h4>
            </div>
            <div class="modal-body">

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="appointment_cancel_delete_modal" class="modal fade in">
    <div class="modal-md-scheduler">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close js-app_appointment_operation_cancel" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                {!! Form::hidden('cancel_delete_option','',['class'=>'form-control']) !!} 
                <div class="form-group">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        {!! Form::text('reason',null,['class'=>'form-control js-appointment_cancel_delete_reason','maxlength'=>'150','placeholder'=>'Reason']) !!} 
                        <small id='reason_err' class='hide help-block med-red' data-bv-validator='notEmpty' data-bv-for='reason_err' data-bv-result='INVALID'>Enter the reason!</small>
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer text-right margin-r-5">
                    {!! Form::button('Save', ['class'=>'btn btn-medcubics-small margin-t-8 js-app_cancel_del_submit']) !!}
                    {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small margin-t-8 js-app_appointment_operation_cancel']) !!}
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div id="eligibility_content_popup" class="modal fade in">
    @include ('layouts/eligibility_modal_popup')
</div>

<?php /* Scheduler module facility empty */
    $facility_count = 0;
    $facility_count = App\Models\Facility::facilityCount();
    if($facility_count == 0)
    { ?>
        @push('view.scripts1')  
            <script type="text/javascript">
                $(document).ready(function () {                     
                    $('#popup_facility_msg').modal('show');
                });
            </script>
        @endpush  
    <?php }
    /* Scheduler module provider empty */
    $provider_count = App\Models\Provider::providerCount();
     if($provider_count == 0)
    { 
?>
        @push('view.scripts1')  
            <script type="text/javascript">
                $(document).ready(function () {                     
                    $('#popup_provider_msg').modal('show');
                });
            </script>
        @endpush  
<?php }
    /* Scheduler module practice provider scheduler empty,  if provider schedule time not available.  */
    $provider_scheduler_count = App\Models\ProviderScheduler::providerSchedulerCount();
    if($provider_scheduler_count == 0 || count($resource_listing) < 1)
    {        
?>
    @push('view.scripts1')  
		<script type="text/javascript">
			var providerSchCnt = 0;
			$(document).ready(function () { 
				$('#popup_provider_scheduler_msg').modal('show');
			});
		</script>
	@endpush  
<?php } else { ?>
	@push('view.scripts1')  
		<script type="text/javascript">
			var providerSchCnt = 1;
		</script>
	@endpush  	
<?php } ?>
<!-- /.modal-dialog -->
<style>
.modal-open .modal {
  overflow-x: hidden;
  overflow-y: auto;
}
</style>
@stop   

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $(function () {
            $("#scheduler_calendar").datepicker({
                dateFormat: "yy-mm-dd",
                onSelect: function (dateText) {
                    current_date = $(this).val();
                    triggerCalendar();
					appointmentStatsdynamic('def');
                }
            });
        });
    });

    var current_date = '{{date("Y-m-d")}}';
    var current_date_time = '{{date("Y-m-d H:i")}}';
    triggerCalendar();
	/*
	$('.modal').on('hidden.bs.modal', function (e) {
		if($('.modal').hasClass('in')) {
			$('body').addClass('modal-open');
		}    
	});
	
	$(document).on('click','.js_recentform',function() {
		setTimeout(function(){
			$('body').removeClass('modal-open');
		}, 500);
	});*/
</script>
@endpush