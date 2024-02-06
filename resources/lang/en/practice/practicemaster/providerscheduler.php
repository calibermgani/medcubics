<?php
return [
	"validation"	=> [
			"facility_id"			=>	"Select facility",
			"startdate"			=> 	"Start date must be before end date",
			"startdate_required"            => 	"Enter start date",
			"stopdate_required"		=> 	"Enter end date",
			"enddate"			=> 	"End date must be after start date",
			"time_slot"			=>	"Select time",
			"from_to_time"			=>	"Select From and To time",
			"enter_occurence"		=>	"Enter no. of occurrence",
			
			//Controller related msg
			"start_end_date"		=>	"Kindly select valid start/end date",
			"mismatch_time"			=>	"Kindly select a time or reset if not selected",
			"facility_wont_work"            =>	"Sorry we are dont have working days for selected facility",
			"conflict_error"		=>	"There has been conflicts in date and timing. Please try again.",
			"days_timings"			=>	"We unable to add since facility will not be available on these days",
			"select_one_day"		=>	"Kindly select atleast one day timings",
			"select_time_day"		=>	"Kindly select timings for selected day",
			"unable_process"		=>	"Unable to add since facility will not be available on these days",
			"select_missing_time"           =>	"Kindly select",
			"slt_another_str_date"          =>	"Facility will not be available on selected date(day). So kindly select another start date",
			"slt_another_date"		=>	"Invalid start date. Select another date.",
			"select_timing_days"            =>	"Kindly select timings for following days",
			"slt_one_day_schedule"          =>	"Kindly select atleast one day to schedule.",
			"select_selcted_days"           =>	"Make sure you have selected days",
			"facility_closed_day"           =>	"Unable to add since facility will not be available on those days ",
			"monthly_visit_type"            =>	"Kindly select visit type and visit frequency.",
			"facility_not_working"          =>	"Split your schedule depending upon dates",
			"day_timing"			=>	"days timings",
			"start_end_date_work"   =>  "Please Check the Dates"
		]
];
