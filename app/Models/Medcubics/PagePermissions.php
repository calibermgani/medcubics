<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class PagePermissions extends Model {

	protected $fillable = ['menu', 'submenu','title','title_url'];
    protected $connection = 'responsive';
	
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
	
        public static function getMenusList($fied_name, $field_value, $main_menu = '', $module)
	{ 
            //$query = SetPagepermissions::where($fied_name,$field_value);
            if($fied_name == 'menu')
                $query = PagePermissions::where('menu',$field_value)->where('module', $module)->groupBy('submenu')->orderBy('submenu','ASC')->get();
            else
                $query = PagePermissions::where('menu',$main_menu)->where('module', $module)->where('submenu',$field_value)->orderBy('title','ASC')->get();
            return $query;
	}
     public static function getModuleMenusList($module)
    { 
                $query = PagePermissions::where('module', $module)->groupBy('menu')->orderBy('menu','ASC')->get();           
            return $query;
    }
}
