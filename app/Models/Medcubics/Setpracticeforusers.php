<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Setpracticeforusers extends Model {

    use SoftDeletes;
    protected $connection = 'responsive';
	public function user()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','user_id','id');
	}
	public function createdBy()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','created_by','id');
	}
	public function updatedBy()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','updated_by','id');
	}
	public function practice()
	{
		return $this->belongsTo('App\Models\Medcubics\Practice','practice_id','id');
	}
    protected $dates = ['deleted_at']; 
    
	protected $fillable=['user_id','role_id','practice_id','page_permission_ids','created_by','updated_by'];

	public static $rules = [];
	
	public static $messages = [];	

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
