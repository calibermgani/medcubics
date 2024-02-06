<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowupCategory extends Model {
	
	use SoftDeletes;
	
	protected $table = 'followup_category';

	protected $dates = ['deleted_at'];
	
	public $timestamps = true;

	protected $fillable=['name', 'label_name', 'created_at', 'status', 'deleted_at'];
	
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
    
	public function question(){
		return $this->hasMany('App\Models\FollowupQuestion','category_id','id')->with('user');
	}
	
}