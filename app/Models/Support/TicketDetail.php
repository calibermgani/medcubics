<?php namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use App\Models\Support\Ticket as Ticket;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use Lang;
//use Illuminate\Database\Eloquent\SoftDeletes;
class TicketDetail extends Model {
//use SoftDeletes;

	public function ticket()
	{
		return $this->belongsTo('App\Models\Support\Ticket','ticket_id','ticket_id');
	}
	public static function getLastTicket($ticket_id,$date)
	{
		foreach ($ticket_id as $key => $value)
		{
			$ticket = TicketDetail::with("ticket")->where("ticket_id",$value)->orderBy("created_at","<=",$date)->first()->toArray();
			$msg = "Hello ".$ticket['ticket']['name'].","."\n".Lang::get("common.validation.ticket_reminder_msg");
			$email =$ticket['ticket']['email_id'];
			$res = array('email'	=>	$email,
						'subject'	=>	"Reminder mail",
						'msg'		=>	$msg,
						'name'		=>	$ticket['ticket']['name']
						);
			$msg_status = CommonApiController::connectEmailApi($res);	
			$ticket_id = Ticket::findOrFail($value);
			$ticket_id->notification_sent = "Yes";
			$ticket_id->save ();
		}
		
	}
	//protected $dates = ['deleted_at'];
	protected $table = 'ticket_details';
	protected $connection = 'responsive';			
	protected $fillable = array('ticket_id','description','attach_details','image_type','posted_by','postedby_id','created_by','updated_by');
	
	public static $rules	= [
		  'ticketno' => 'required|numeric'
	];
	
	public static $messages = [
			'ticketno.required' 	=> 'Enter ticket no',
			'name.numeric'		=> 'Number Only allowed'
	];
	
	public function posted_user()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','postedby_id','id');
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
