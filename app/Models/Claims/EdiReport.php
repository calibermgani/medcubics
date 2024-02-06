<?php namespace App\Models\Claims;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EdiReport extends Model {
	//protected $table = 'edi_eligibility';
	
	protected $fillable = ['file_name','file_path','created_by','file_created_date','file_type','file_size'];
	use SoftDeletes;
	public function user()  
	{
		return $this->belongsTo('App\User','created_by','id');
	}	
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
