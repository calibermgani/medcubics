<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Lang;

class Cpt extends Model 
{
	use SoftDeletes;
	protected $dates 	= ['deleted_at'];
	protected $fillable	=[
		'long_description','short_description','medium_description','cpt_hcpcs', 'code_type','type_of_service','pos_id','applicable_sex','referring_provider','age_limit','revenue_code','drug_name','ndc_number','min_units','max_units','anesthesia_unit','service_id_qualifier','allowed_amount','billed_amount','required_clia_id','clia_id','modifier_id','icd','effectivedate','terminationdate', 'work_rvu','facility_practice_rvu','nonfacility_practice_rvu','pli_rvu','total_facility_rvu','total_nonfacility_rvu'
	,'status','procedure_category','unit_code','unit_cpt','unit_ndc','unit_value'];	

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

	public function pos()
	{
		return $this->belongsTo('App\Models\Pos','pos_id','id');
	}	

	public function favourite()
	{
		return $this->hasOne('App\Models\Favouritecpts', 'cpt_id','id');
		//->where('user_id',Auth::user()->id); => Favorite cpt will show based on the practice not based on user.
	}

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}

	public function qualifier()
	{
		return $this->belongsTo('App\Models\Medcubics\IdQualifier','service_id_qualifier','id');
	}

	public function modifier_name()
	{
		return $this->belongsTo('App\Models\Modifier','modifier_id','id');
	}

	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	
	public function multifeeSchedule()
	{
		return $this->belongsTo('App\Models\MultiFeeschedule','id','cpt_id');
	}

	public function pro_category()
	{
		return $this->belongsTo('App\Models\ProcedureCategory','procedure_category','id');
	}

	public static $rules = [
		'revenue_code' 			=> 'nullable|alpha_num|max:5',
		'modifier_id'			=> 'nullable|max:4',	
		'ndc_number' 			=> 'nullable|alpha_num',
		'clia_id' 				=> 'nullable|alpha_num|max:15'
	];

	public static function messages(){
		return [
			'revenue_code.alpha_num'=> Lang::get("practice/practicemaster/cpt.validation.revenue_code"),
			'revenue_code.max' 		=> Lang::get("practice/practicemaster/cpt.validation.revenue_code"),
			'ndc_number.alpha_num' 	=> Lang::get("common.validation.alphanumeric"),
			'clia_id.alpha_num' 	=> Lang::get("common.validation.alphanumeric"),
			'modifier_id.max' 		=> Lang::get("practice/practicemaster/cpt.validation.modifier_max_length"),
			'clia_id.max' 			=> Lang::get("practice/practicemaster/cpt.validation.clia_id"),
			'terminationdate.after' => Lang::get("common.validation.terminationdate")
		];
	}
	
	public static function cpt_shot_desc($cpt_code)
	{
		//$cpt_code = CPT::on('responsive')->where('cpt_hcpcs',$cpt_code)->pluck('short_description');
		$cpt_code = CPT::where('cpt_hcpcs',$cpt_code)->pluck('short_description')->first();
		return $cpt_code;
	}
}