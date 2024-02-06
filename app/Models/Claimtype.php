<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claimtype extends Model
{
	protected $fillable = [ 'claim_type' ];
	
	public static $rules = [];

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