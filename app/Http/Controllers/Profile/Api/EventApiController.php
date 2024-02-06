<?php 
namespace App\Http\Controllers\Profile\Api;
use Auth;
use Request;
use Response;
use Input;
use Validator;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as User;
use App\Models\Profile\ProfileEvents as ProfileEvents;
use App\Http\Helpers\Helpers as Helpers;
use App;
use Config;
use DB;

class EventApiController extends Controller {
    public function getindexApi()
	{
		if(ProfileEvents::get()->count()) {
			$reminder = ProfileEvents::orderBy('start_date', 'ASC')->get();
			for ($i=0; $i<count($reminder); $i++) {
				$reminder[$i] = json_decode(json_encode($reminder[$i]), true);
			}
			return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('reminder')));
		}
		else{
			return Response::json(array('status' => 'error', 'message' => null, 'data' => ''));
		}
	}
		
	//create and update using same function
	public function getEventCreateApi($request,$id=""){
		if($request !='') {
			if($id){
				$edit_event_id = ProfileEvents::where('id',$id)->pluck('event_id')->first();
				if($edit_event_id == $request['event_id'])ProfileEvents::where('event_id',$edit_event_id)->delete();
			} else {
				$request['event_id']  = ProfileEvents::orderBy('event_id', 'DESC')->take(1)->pluck('event_id')->first()+1;
			}
			$user = Auth::user ()->id;
			$request['start_date']  = date("Y-m-d",strtotime($request['start_date']));
			$request['start_time']  = date("H:i", strtotime($request['start_time']));
			$request['end_date']  = date("Y-m-d",strtotime($request['end_date']));
			$request['end_time']  = date("H:i", strtotime($request['end_time']));
			$request['participants']  = (count(@$request['participants'][0]) >1)?implode(",",@$request['participants'][0]):@$request['participants'][0];
			$request['reminder_days']  = rtrim($request['reminder_days'], ",");
			
			//To get week days list Global variables 
			$startDate = strtotime($request['start_date']);
			$endDate = strtotime($request['end_date']);
			$year1 = date('Y', $startDate);
			$year2 = date('Y', $endDate);
			$month1 = date('m', $startDate);
			$month2 = date('m', $endDate);
			$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
			$request['created_by'] =$user;
			if($request['reminder_type']=="one-time"){
				$request['reminder_type_repeat']="";
				$request['repeated_by']="";
				$data = ProfileEvents::create($request);
			} else {
				if($request['reminder_type_repeat']=="never" || $request['repeated_by']=="Daily"){
					$request['repeated_by']="Daily";
					$dates=$this->date_range($startDate, $endDate);
					foreach ($dates as $key => $value){
						$request['start_date'] =$value;
						$data = ProfileEvents::create($request);
					}
				} else {
					$start_date = $request['start_date'];
					$dateArr = array();
					$getDate = array();
					$allDate = array();
					$weekdays =explode(",",$request['reminder_days']);
					if($request['repeated_by']=="Weekly"){
						for ($i=0; $i<count($weekdays); $i++) {
							$weekdayNumber = $weekdays[$i];
							$temp_end_date = date("Y-m-d", strtotime("+7 days", strtotime($start_date)));
							$days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
							$request['repeated_day']=$days[$weekdayNumber];
							if($request['end_date'] >= $temp_end_date){
								$alldates=$this->swith_case($startDate, $endDate, $weekdayNumber);
								foreach ($alldates as $key => $value){
									$request['start_date'] =$value;
									$data = ProfileEvents::create($request);
								}
							}
						}
					}
					if(($request['reminder_type_repeat']=="on") && ($request['repeated_by']=="Monthly" || $request['repeated_by']=="Yearly")){
						$request['reminder_date']  = date("Y-m-d",strtotime($request['reminder_date']));
						if($request['repeated_by']=="Monthly")$step ="+1 month"; 
						if($request['repeated_by']=="Yearly")$step ="+1 year";
						$startDate = strtotime($request['reminder_date']);
						$month_dates=$this->date_ranges($startDate, $endDate,$step);
						foreach ($month_dates as $key => $value){
							$request['start_date'] =$value;
							$data = ProfileEvents::create($request);
						}
					}
				}
			}
			return Response::json(array('status' => 'success', 'message' => 'Reminder added successfully','data' =>''));
		} else {
			return Response::json(array('status' => 'error', 'message' => 'No data available', 'data' => ''));
		}
    }
	
	//calculate All dates in two dates
	public function date_range($startDate, $endDate, $step = '+1 day', $output_format = 'Y-m-d' ) {
		$dates = array();
		while( $startDate <= $endDate ) {
			$dates[] = date($output_format, $startDate);
			$startDate = strtotime($step, $startDate);
		}
		return $dates;
	}

	//calculate All Month diff in two dates
	public function date_ranges($startDate, $endDate, $step, $output_format = 'Y-m-d' ) {
		$month_dates = array();
		while( $startDate <= $endDate ) {
			$month_dates[] = date($output_format, $startDate);
			$startDate = strtotime($step, $startDate);
		}
		return $month_dates;
	}

	//calculate All week dates diff in two dates
	public function swith_case($startDate, $endDate, $weekdayNumber){
		switch ($weekdayNumber) {
			case 0:
				$month_dates=$this->getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber);
				break;
			case 1:
				$month_dates=$this->getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber);
				break;
			case 2:
				$month_dates=$this->getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber);
				break;
			case 3:
				$month_dates=$this->getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber);
				break;
			case 4:
				$month_dates=$this->getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber);
				break;
			case 5:
				$month_dates=$this->getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber);
				break;
			case 6:
				$month_dates=$this->getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber);
				break;
		}
		return $month_dates;
	}
	
	//calculate Selected week dates in two dates
	public function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
	{
		//echo $startDate.":".$endDate.":".$weekdayNumber;
		do
		{
			if(date("w", $startDate) != $weekdayNumber)
			{
				$startDate += (24 * 3600); // add 1 day
			}
		} while(date("w", $startDate) != $weekdayNumber);
		
		while($startDate <= $endDate)
		{
			$dateArr[] = date('Y-m-d', $startDate);
			$startDate += (7 * 24 * 3600); // add 7 days
			
		}
		return($dateArr);
	}
			
	public function getCalendarshowApi(){
		$total_date = ProfileEvents::orderBy('start_date', 'ASC')->pluck('start_date')->all();
		$start_date = date("Y-m-d");
		$end_date = date("Y-m-d", strtotime("+7 days", strtotime($start_date)));
		$user = Auth::user ()->id;
		$today_events = ProfileEvents::where('start_date', '=', $start_date)->get()->toArray();
		$reminders = ProfileEvents::where('start_date', '>', $start_date)->where('start_date', '<=', $end_date)->groupBy('start_date')->orderBy('start_date', 'ASC')->get()->toArray();
		$reminder = array();
		
		foreach($reminders as $reminders) {
			$reminder[] = ProfileEvents::where('start_date', '=', $reminders['start_date'])->get()->toArray();
		}		
		//dd($today_events);
	   return Response::json(array('status' => 'success', 'message' => 'Reminder added successfully', 'data' =>compact('today_events','total_date','reminder')));
    }

	public function getCalendarAddApi(){
		$select_list = User::orderBy('name', 'ASC')->pluck('name','id')->all();
		//dd($select_list);
		return Response::json(array('status' => 'success', 'message' => 'Reminder added successfully', 'data' =>compact('select_list')));
    }

	public function getCalendareditApi($id){
		$select_list = User::orderBy('name', 'ASC')->pluck('name','id')->all();
		$reminder = ProfileEvents::where('id',$id)->get();
		$reminder= json_decode(json_encode($reminder[0]), true);
		return Response::json(array('status' => 'success', 'message' => 'Reminder added successfully', 'data' =>compact('reminder','select_list')));
    }

	public function getCalendarshowTimestampApi($timestamp){
		if($timestamp !='') {
			$coming_date = date("Y-m-d",$timestamp);
			$start_date = date("Y-m-d",strtotime("+1 days", strtotime($coming_date)));
			$yesterday_date = date("Y-m-d",strtotime("-1 days"));
			$current_date = date("Y-m-d");
			$end_date = date("Y-m-d", strtotime("+7 days", strtotime($start_date)));
			$user = Auth::user ()->id;
			$reminders = ProfileEvents::where('start_date', '>=', $start_date)->where('start_date', '<=', $end_date)->groupBy('start_date')->orderBy('start_date', 'ASC')->get()->toArray();
			$reminder = array();
				$i=0;
				foreach($reminders as $reminders){
					$reminder_tmp = ProfileEvents::where('start_date', '=', $reminders['start_date'])->get()->toArray();
					if($reminders['start_date'] < $yesterday_date){$name=0;}
					if($reminders['start_date'] == $yesterday_date){$name=1;}
					if($reminders['start_date'] == $current_date){$name=2;}
					if($reminders['start_date'] > $current_date){$name=3;}
					for($i=0;$i<count($reminder_tmp);$i++){
						$reminder_tmp[$i]['arrange']=$name;
					}
					$reminder[] = $reminder_tmp;
					unset($reminder_tmp);
				}
			//dd($reminder);
			return Response::json(array('status' => 'success', 'message' => 'Reminder added successfully', 'data' => $reminder));
		} else {
			return Response::json(array('status' => 'error', 'message' => 'No data available', 'data' => ''));
		}
    }
	
	public function getEventDeleteApi($id) {
		if (ProfileEvents::where('id', $id)->count()) {
			$start_date = ProfileEvents::where('id', $id)->pluck('start_date')->first();
			$result = ProfileEvents::find($id)->delete();
			$count = ProfileEvents::where('start_date',$start_date)->count();
			if ($result == 1) {
                return Response::json(array('status' => 'success', 'message' => $start_date, 'data' =>$count));
            } else {
                return Response::json(array('status' => 'error', 'message' => 'Unable to delete the patient.', 'data' => ''));
            }
        } else {
            return Response::json(array('status' => 'error', 'message' => 'Invalid Patient Details.', 'data' => 'null'));
        }
    }
}