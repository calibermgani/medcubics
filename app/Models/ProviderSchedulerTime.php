<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;

class ProviderSchedulerTime extends Model
{
    protected $table = 'provider_scheduler_time';
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public static function boot() {
       parent::boot();
       // create a event to happen on saving
       static::saving(function($table)  {
            foreach ($table->toArray() as $name => $value) {
                if (empty($value) && $name <> 'deleted_at') {
                    $table->{$name} = '';
                }
            }
            return true;
       });
    }

    public function providerscheduler()
    {
        return $this->belongsTo('App\Models\ProviderScheduler','provider_scheduler_id','id');
    }

	public function facility()
    {
        return $this->belongsTo('App\Models\Facility','facility_id','id');
    }

	public function provider()
    {
        return $this->belongsTo('App\Models\Provider','provider_id','id');
    }
    
	public function provider_scheduler()
    {
        return $this->belongsTo('App\Models\ProviderScheduler','provider_scheduler_id','id');
    }
    
	/*public function provider_scheduler()
    {
        return $this->hasMany('App\Models\ProviderScheduler');
    }*/

    protected $fillable = ['provider_scheduler_id', 'provider_id', 'facility_id', 'schedule_date', 'day', 
                            'from_time', 'to_time', 'schedule_type', 'created_by','updated_by', 'sms_reminder_status', 'phone_reminder_status','email_reminder_status'];
    
    public static function getProviderScheduledTimings($provider_id,$dates_array, $scheduler_id = '')
    { 
		$query = ProviderSchedulerTime::where('provider_id',$provider_id)->whereIn('schedule_date',$dates_array);
		
		if($scheduler_id != '')
			$query->where('provider_scheduler_id','!=',$scheduler_id);
		
		$facility_timings = $query->get();
        
        $scheduled_timings = [];
        $scheduled_timings['monday'] = [];
        $scheduled_timings['tuesday'] = [];
        $scheduled_timings['wednesday'] = [];
        $scheduled_timings['thursday'] = [];
        $scheduled_timings['friday'] = [];
        $scheduled_timings['saturday'] = [];
        $scheduled_timings['sunday'] = [];
        
        foreach($facility_timings as $facility_time)
        {
            if($facility_time->day == 'monday')
                $scheduled_timings['monday'] = App\Http\Helpers\Helpers::splitAndGetTimingsByDay($scheduled_timings['monday'],$facility_time->from_time,$facility_time->to_time); 
            
            if($facility_time->day == 'tuesday')
                $scheduled_timings['tuesday'] = App\Http\Helpers\Helpers::splitAndGetTimingsByDay($scheduled_timings['tuesday'],$facility_time->from_time,$facility_time->to_time); 
            
            if($facility_time->day == 'wednesday')
                $scheduled_timings['wednesday'] = App\Http\Helpers\Helpers::splitAndGetTimingsByDay($scheduled_timings['wednesday'],$facility_time->from_time,$facility_time->to_time); 
            
            if($facility_time->day == 'thursday')
                $scheduled_timings['thursday'] = App\Http\Helpers\Helpers::splitAndGetTimingsByDay($scheduled_timings['thursday'],$facility_time->from_time,$facility_time->to_time);
            
            if($facility_time->day == 'friday')
                $scheduled_timings['friday'] = App\Http\Helpers\Helpers::splitAndGetTimingsByDay($scheduled_timings['friday'],$facility_time->from_time,$facility_time->to_time);
            
            if($facility_time->day == 'saturday')
                $scheduled_timings['saturday'] = App\Http\Helpers\Helpers::splitAndGetTimingsByDay($scheduled_timings['saturday'],$facility_time->from_time,$facility_time->to_time);
            
            if($facility_time->day == 'sunday')
                $scheduled_timings['sunday'] = App\Http\Helpers\Helpers::splitAndGetTimingsByDay($scheduled_timings['sunday'],$facility_time->from_time,$facility_time->to_time);
        }
        return $scheduled_timings;
    }
	
    public function scopeGetScheduleDatesByProviderAndFacilityId($query, $facility_id, $provider_id, $cur_date='')
    {
        $query->has('providerscheduler')
                ->whereHas('providerscheduler', function($q) {$q->where('status', 'active');})
                ->where('facility_id',$facility_id)->where('provider_id',$provider_id);
                
        if($cur_date != '')
            $query->where('schedule_date','>=',$cur_date);
        
        return $query;
    }
	
}