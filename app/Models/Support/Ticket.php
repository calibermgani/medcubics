<?php namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Lang;
//use Illuminate\Database\Eloquent\SoftDeletes;
class Ticket extends Model
{
//use SoftDeletes;

	//protected $dates = ['deleted_at'];
	protected $table = 'ticket';
	protected $fillable = array('name','ticket_id','email_id','title','type','status','created_by','updated_by');
	protected $connection = 'responsive';			
	public static $rules	= [
		  'name' => 'required',
		  'email_id' => 'required|email',
		  'title' => 'required',
		  'description' => 'required',
		  'filefield'	=> 'mimes:pdf,jpeg,jpg,png,gif,doc,xls,csv,docx,xlsx,txt'
	];
	
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
	
	
	public static $adminticketrules	= [
		  'title' => 'required',
		  'description' => 'required',
		  'filefield'	=> 'nullable|mimes:pdf,jpeg,jpg,png,gif,doc,xls,csv,docx,xlsx,txt',
		  'assigneduser_id' => 'required'
	];
	
	public static $admin_rules	= [
		  'description' => 'required'
	];
	
	public static function messages()
	{
		return [
			'name.required' 	=> Lang::get("admin/adminuser.validation.name"),
			'name.regex'		=> Lang::get("common.validation.alpha_regex"),
			'title.required' 	=> Lang::get("common.validation.title"),
			'type.required' 	=> Lang::get("support/ticket.validation.type"),
			'description.required' 	=> Lang::get("common.validation.description"),
			'email_id.required' 		=> Lang::get("common.validation.email"),
			'email_id.email' 			=> Lang::get("common.validation.email_valid"),
			'filefield.mimes'			=> Lang::get("common.validation.upload_valid"),
			'assigneduser_id.required'	=> Lang::get("support/ticket.validation.selectuser")
		];
	}
	
	public function get_assignee()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','assigned','id');
	}
	
	public function get_assignedby()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','assignedby','id');
	}

	public function ticket_detail()
	{
		return $this->belongsTo('App\Models\Support\TicketDetail','ticket_id','ticket_id');
	}
}