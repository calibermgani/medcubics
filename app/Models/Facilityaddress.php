<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facilityaddress extends Model 
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable=[
		'facilityid',
		'address1',
		'address2',
		'city',
		'state',
		'pay_zip5',
		'pay_zip4',
		'phone',
		'phoneext',
		'fax',
		'email'
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
	  
	public function setStateAttribute($value)
    {
        $this->attributes['state'] = strtoupper($value);
    } 
}