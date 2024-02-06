{!! Form::open(['url'=>'patients/'.$patient_id.'/appointments/type','id'=>'js_common_search_form','name'=>'search_form']) !!}
<div id="js_claim_search_option">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
        <div class="no-shadow form-horizontal">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0 ">
				<div class="form-group-billing">
					{!! Form::label('Provider', 'Provider',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::select('provider_id', array(''=>'-- Select --')+(array)@$rendering_provider, null,['class'=>'form-control input-view-border1 select2','id'=>'provider_id']) !!}
					</div>
				</div>
				<div class="form-group-billing">
					{!! Form::label('Facility', 'Facility', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
						<i class="fa fa-calendar-o form-icon-billing"></i> 
						  {!! Form::select('facility_id',['all'=>'All']+(array)$facility,"all",['class'=>'select2 form-control']) !!}
						<div class="col-lg-01 col-md-01 col-sm-3 col-xs-3"></div>
					</div>                        
				</div>
				<div class="form-group-billing">
					{!! Form::label('status', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::select('status', ['all' => 'All','scheduled' => 'Scheduled','confirmed' => 'Confirmed','not_confirmed'=>'Not Confirmed','arrived'=>'Arrived','in_session'=>'In Session','complete'=>'Complete','rescheduled'=>'Rescheduled','cancelled'=>'Cancelled','no_show'=>'No Show'],null,['class'=>'select2 form-control js_change_date_option']) !!}
					</div>                        
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0">
				<div class="form-group-billing">
					{!! Form::label('adjustment reason', 'Reason for Visit', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}        
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::select('reason_id',['Patient'=>'All']+(array)@$reason,null,['class'=>'select2 form-control']) !!}
					</div>                        
				</div>           
				<div class="form-group-billing">
					{!! Form::label('Time', 'Time dur from', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 bootstrap-timepicker">
						<i class="fa {{Config::get('cssconfigs.common.clock')}} form-icon-billing" onclick= "iconclick('check_in_time')"></i> 
						{!! Form::text('check_in_time',null,['id'=>'check_in_time','class'=>'form-control input-sm-header-billing timepicker1 dm-time',]) !!}   
						<small class="help-block hide" id="js-error-check_in_time"></small>      
					</div>
				</div>
				<div class="form-group-billing">
					{!! Form::label('to', 'To', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 bootstrap-timepicker">
						<i class="fa {{Config::get('cssconfigs.common.clock')}} form-icon-billing" onclick= "iconclick('check_out_time')"></i> 
						{!! Form::text('check_out_time',null,['id'=>'check_out_time','class'=>'form-control input-sm-header-billing timepicker1 dm-time']) !!}   
						<small class="help-block hide" id="js-error-check_out_time"></small>    
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 p-l-0">
				<div class="form-group-billing">
					{!! Form::label('Date', 'App date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::select('date_option', ['enter_date' => 'Enter Date','daily' => 'Daily','current_month'=>'Current Month','previous_month'=>'Previous Month','current_year'=>'Current Year','previous_year'=>'Previous Year'],null,['class'=>'select2 form-control js_change_date_option']) !!}
					</div>                        
				</div>       
				<div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
					<div class="form-group-billing">
						{!! Form::label('', 'From', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}  
					   <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
							<i class="fa fa-calendar-o form-icon-billing"></i> 
							{!! Form::text('from_date', null,['class'=>'search_start_date form-control input-sm-modal-billing datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
						</div>                        
					</div>                                        
					<div class="form-group-billing">
						  {!! Form::label('', 'To', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
							<i class="fa fa-calendar-o form-icon-billing"></i> 
							{!! Form::text('to_date', null,['class'=>'search_end_date form-control input-sm-modal-billing datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
						</div>                        
					</div>
				</div> 
			</div>
		</div>
	</div>
	   
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right p-r-0">
		<input class="btn btn-medcubics-small" value="Search" type="submit">
		{!! Form::reset('Reset',["class"=>"btn btn-medcubics-small js_search_reset"]) !!}
	</div>
</div>
{!! Form::close() !!}
<style>
.bootstrap-timepicker-widget table td input{
	width:40px !important;
}
</style>

@push('view.scripts1')
<script type="text/javascript">
	$(document).on('focus', '.timepicker1', function () {
		$(".timepicker1").timepicker();
	});

	$(document).on('change', '.js_change_date_option', function (e) {
		console.log('5297');
		var current_val = $(this).val();
		if (current_val == "enter_date" || current_val == "" || typeof current_val == "undefined") {
			var str_date = '';
			var end_date = '';
		} else {
			var str_date = getStartDate(current_val);
			var end_date = getEndDate(current_val);
		}
		if ($(".search_start_date").length > 0) {
			$(".search_start_date").val(str_date);
			$(".search_end_date").val(end_date);
			$(".search_start_date,.search_end_date").trigger("keyup");
			if (current_val != "enter_date" && current_val != "" && typeof current_val != "undefined") {
				$(".search_start_date,.search_end_date").attr("disabled", 'disabled').removeClass("datepicker dm-date hasDatepicker");
			} else {
				$(".search_start_date,.search_end_date").removeAttr("disabled").addClass("datepicker dm-date hasDatepicker");
			}
			$('#js_common_search_form').bootstrapValidator('revalidateField', $('.search_start_date'));
			$('#js_common_search_form').bootstrapValidator('revalidateField', $('.search_end_date'));
		}
	});

	function getStartDate(date_option) {
		var d = new Date();
		switch (date_option) {
			case "daily":
				var strDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
				break;

			case "current_month":
				var date = new Date(d.getFullYear(), (d.getMonth()), 1);
				var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
				break;

			case "previous_month":
				var date = new Date(d.getFullYear(), (d.getMonth() - 1), 1);
				var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
				break;

			case "current_year":
				var date = new Date(d.getFullYear(), 0, 1);
				var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
				break;

			case "previous_year":
				var date = new Date(d.getFullYear() - 1, 0, 1);
				var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
				break;

			default:
				var strDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
		}
		return MakeDate(strDate);
	}

	function getEndDate(date_option) {
		var d = new Date();
		switch (date_option) {
			case "daily":
				var endDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
				break;

			case "current_month":
				var endDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
				break;

			case "previous_month":
				var date = new Date(d.getFullYear(), (d.getMonth()), 0);
				var endDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
				break;

			case "current_year":
				var endDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
				break;

			case "previous_year":
				var date = new Date(d.getFullYear(), 0, 0);
				var endDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
				break;

			default:
				var endDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
		}
		return MakeDate(endDate);
	}

	function MakeDate(date_value) {
		var date = date_value.split("/");
		date[0] = ((date[0]) < 10) ? "0" + date[0] : date[0];
		date[1] = ((date[1]) < 10) ? "0" + date[1] : date[1];
		var return_date = date.join("/");
		return return_date;
	}
</script>
@endpush