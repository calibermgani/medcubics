<?php namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use Lang;
//use Illuminate\Database\Eloquent\SoftDeletes;
class AssignTicketHistory extends Model {
	
	//protected $dates = ['deleted_at'];
	protected $table = 'assign_tickethistory';
	protected $connection = 'responsive';			
	protected $fillable = array('ticket_id','assigned','assigned_by','created_at','updated_at');
	
	public function get_assignedto()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','assigned','id');
	}
	
	public function get_assignedby()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','assigned_by','id');
	}
	
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

}
