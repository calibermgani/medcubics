<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class SuperbillTemplate extends Model 
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = "superbill_template";
	
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
    
	public function provider()
	{
		return $this->belongsTo('App\Models\Provider','provider_id','id');		
	}

	public function creator()
	{
		return $this->belongsTo('App\User', 'created_by', 'id');
	}
	
	public function modifier()
	{
		return $this->belongsTo('App\User', 'updated_by', 'id');
	}

	protected $fillable=[
		'template_name','provider_id','header_list','status','get_list_order','order_header','header_style','office_visit','office_procedures','laboratory','well_visit','medicare_preventive_services','skin_procedures','consultation_preop_clearance','vaccines','medications','other_services','skin_procedures_units','medications_units','created_by','updated_by'
	];

	public static function messages(){
		return [
			'template_name.required' => Lang::get("practice/practicemaster/superbill.validation.template_name"),
			'template_name.chk_name_exists' => Lang::get("practice/practicemaster/superbill.validation.template_unique"),
			'provider_id.required' => Lang::get("common.validation.provider_required"),
			'header_list.required' => Lang::get("practice/practicemaster/superbill.validation.header_list"),
		];
	}
		
}