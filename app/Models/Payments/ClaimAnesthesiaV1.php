<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimAnesthesiaV1 extends Model {
    
    use SoftDeletes;
    protected  $table = "claim_anesthesia_v1";
    protected $fillable = ['anesthesia_start','anesthesia_stop','anesthesia_minute','created_by','updated_by'];
     // below function used for tabs to check whether paer id available for the selected insurance
	 
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
