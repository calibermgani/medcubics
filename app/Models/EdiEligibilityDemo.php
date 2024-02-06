<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdiEligibilityDemo extends Model {
	protected $table = 'edi_eligibility_demo';
	protected $fillable=['edi_eligibility_id','demo_type','gender','member_id','first_name','last_name','middle_name','group_id','group_name','address1','address2','city','state','zip5','zip4','dob','relationship'];

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