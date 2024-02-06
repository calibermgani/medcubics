<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Roles extends Model 
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable=['role_name','role_type','status','created_at','updated_at','created_by','updated_by','deleted_at'];
    
    public function created_user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
        
    public function updated_user()
    {
        return $this->belongsTo('App\User','updated_by','id');
    }

    public function SetPermissions()
    {
        return $this->belongsTo('App\Models\Medcubics\SetAdminPagePermissions','id','role_id');
    }   
	
    public function SetpracticePagePermissions()
    {
        return $this->belongsTo('App\Models\Medcubics\SetPagepermissions','id','role_id');
    }   
	
    public static $rules = [
                            'role_type' 		=> 'required|min:6',
							];
    public static function messages () {
						return [
                            'role_name.min' 	=> Lang::get("admin/role.validation.role"),
                            'role_type.required' 	=> Lang::get("admin/role.validation.role_type"),
							];
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
