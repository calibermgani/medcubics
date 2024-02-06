<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;

class IdQualifier extends Model 
{
	protected $table = 'id_qualifiers';
	
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
	
	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	protected $fillable=[
						'id_qualifier_name'
						];
	public static $rules = [
							'id_qualifier_name' => 'required|unique:id_qualifiers'
							];
	public static $messages = [
							'id_qualifier_name.required'=> 'Enter your ID Qualifier name',
							'id_qualifier_name.unique' 	=> 'ID Qualifier name must be unique'
							];
}
