<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Lang;

class Cpt extends Model {
	use SoftDeletes;
	protected $dates 	= ['deleted_at'];
	protected $connection = "responsive";
	
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
		return $this->belongsTo('App\Models\Medcubics\Pos','pos_id','id');
	}
	public function qualifier()
	{
		return $this->belongsTo('App\Models\Medcubics\IdQualifier','service_id_qualifier','id');
	}

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	public function modifier_name()
	{
		return $this->belongsTo('App\Models\Medcubics\Modifier','modifier_id','id');
	}
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	
	protected $fillable=[
		'long_description','medium_description','short_description',
		'cpt_hcpcs', 'code_type','type_of_service','pos_id','applicable_sex','referring_provider','age_limit','modifier_id','revenue_code','drug_name','ndc_number','min_units','max_units',
		'anesthesia_unit','service_id_qualifier','allowed_amount','billed_amount','medicare_global_period','required_clia_id','clia_id','icd',
		'work_rvu','facility_practice_rvu','nonfacility_practice_rvu','pli_rvu','total_facility_rvu','total_nonfacility_rvu','effectivedate','terminationdate', 'status'
	];
	public static $rules = [
		'short_description'		=> 'nullable|max:28',	
		'modifier_id'			=> 'nullable|max:4',	
		'medium_description' 	=> 'required|max:100',
		'revenue_code' 			=> 'nullable|alpha_num|max:5',
		//'cpt_hcpcs'				=> 'required',
		///'anesthesia_unit'		=> 'integer',
		'ndc_number' 			=> 'nullable|alpha_num',
		'clia_id' 				=> 'nullable|alpha_num|max:15'
		];
	public static function messages(){
		return [
			'medium_description.required' 	=> Lang::get("admin/cpt.validation.medium_des"),
			'cpt_hcpcs.required' 			=> Lang::get("admin/cpt.validation.cpt_hcpcs"),
			'cpt_hcpcs.unique' 				=> Lang::get("admin/cpt.validation.cpt_hcpcs_unique"),
			'revenue_code.digits' 			=> Lang::get("practice/practicemaster/cpt.validation.revenue_code"),
			'modifier_id.max' 				=> Lang::get("admin/cpt.validation.modifier_max_length"),
			'ndc_number.alpha_num' 		=> Lang::get("common.validation.alphanumeric"),
			'clia_id.alpha_num' 		=> Lang::get("common.validation.alphanumeric"),
			'clia_id.max' 				=> Lang::get("practice/practicemaster/cpt.validation.clia_id"),
			'terminationdate.after' 	=> Lang::get("common.validation.terminationdate")
		];
	}
	
	public static function Cptshortdescription($cpt_code)
    {
		$result = Cpt::where('cpt_hcpcs',$cpt_code)->pluck("short_description")->first();
		return $result;
    }
}
