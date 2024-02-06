<?php
namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Insuranceclass extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];
	protected $fillable = [ 
					'insurance_class' 
						  ];
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
