<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class AdminPagePermissions extends Model {
    
        protected $table = 'adminpage_permission';
		
		protected $connection = 'responsive';	

	protected $fillable = ['menu', 'submenu','title','title_url'];
	 public static function getAdminMenusList($fied_name, $field_value, $main_menu = '')
	{ 
            //$query = SetPagepermissions::where($fied_name,$field_value);
            if($fied_name == 'menu')
                $query = AdminPagePermissions::where('menu',$field_value)->groupBy('submenu')->orderBy('submenu','ASC')->get();
            else
                $query = AdminPagePermissions::where('menu',$main_menu)->where('submenu',$field_value)->orderBy('title','ASC')->get();
            return $query;
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
