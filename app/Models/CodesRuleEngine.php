<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Lang;

class CodesRuleEngine extends Model 
{
	use SoftDeletes;
	protected $table 	= 'codes_rule_engine';
	protected $dates 	= ['deleted_at'];
	protected $fillable	=['reason_type','claim_status','next_resp','priority','transactioncode_id','code_type'];
	
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