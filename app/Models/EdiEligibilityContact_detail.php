<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EdiEligibilityContact_detail extends Model {

	protected $table = 'edi_eligibility_contact_details';
		
	protected $fillable=['details_for','details_for_id','entity_code','entity_code_label','last_name','first_name','identification_type','identification_code','address1','address2','city','state','zip5','zip4'];

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