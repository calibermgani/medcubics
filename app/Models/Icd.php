<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Auth;
use DB;
use Config;
use Session;
use Lang;

class Icd extends Model 
{
	protected $table = 'icd_10';

	/*public function __construct() {
		$db = new DBConnectionController();
		$admin_database = getenv('DB_DATABASE');
		$db->configureConnectionByName($admin_database);
		Config::set('database.default',$admin_database);
	}
    */
	
	/* public function __construct(){
		$db = new DBConnectionController();
		$db->connectPracticeDB(Session::get('practice_dbid'));
	} */
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
	
	public function connecttopracticedb() {
		$db = new DBConnectionController();
		$db->connectPracticeDB(Session::get('practice_dbid'));
	} 	

	protected $fillable=[
		'order','statement_description','icdid','header','icd_code','icd_type','sex','age_limit_lower','age_limit_upper','effectivedate','inactivedate','map_to_icd9','map_to_icd10','short_description','long_description','medium_description'
	];		
	
	public static $rules = [
		'age_limit_lower'		=> 'nullable|max:2',
		'age_limit_upper'		=> 'nullable|max:3',
		'icdid'					=> 'nullable|max:15',
		'map_to_icd9'			=> 'nullable|max:6',
		'inactivedate' 			=> 'nullable|after:effectivedate',
	];

	public static function messages(){
		return [
			'icdid.max' 			=> Lang::get("common.validation.alphanumeric"),
			'age_limit_lower.max' 	=> Lang::get("practice/practicemaster/icd.validation.lower_age_limit"),
			'age_limit_upper.max' 	=> Lang::get("practice/practicemaster/icd.validation.upper_age_limit"),
			'map_to_icd9.max' 		=> Lang::get("practice/practicemaster/icd.validation.map_to_icd9_limit"),
			'inactivedate.after' 	=> Lang::get("common.validation.inactivedate"),
		];
	}
	/*public static $messages = [
		'order.max' 				=> 'Enter proper Order!',
		'icdid.max' 				=> 'Enter proper ID!',
		'icd_code.unique' 			=> 'The ICD code has already been taken!',
		'icd_code.max' 				=> 'Enter proper code!',
		'age_limit_lower.max' 		=> 'Enter proper age limit lower!',
		'age_limit_upper.max' 		=> 'Enter proper age limit upper!',
		'inactivedate.after' 		=> 'Inactive date should be after effective date!',
	];*/
	public static function getIcdValues($icd_lists, $is_claim_format = 'no')
	{
		if($is_claim_format == 'yes')
			$icd_pointer_key_arr = ['1'=>'A','2'=>'B','3'=>'C','4'=>'D','5'=>'E','6'=>'F','7'=>'G','8'=>'H','9'=>'I','10'=>'J','11'=>'K','12'=>'L'];
		//Icd::connecttoadmindatabase();
		$icd_codes = explode(',',$icd_lists); 
		$j = 1;
		for($i=0;$i<count($icd_codes);$i++) 
		{
			if($is_claim_format == 'yes')
				//$icd[$icd_pointer_key_arr[$j]] = Icd::on("responsive")->where('id',$icd_codes[$i])->pluck('icd_code');
				$icd[$icd_pointer_key_arr[$j]] = Icd::where('id',$icd_codes[$i])->pluck('icd_code')->first();
			else
				//$icd[$j] = Icd::on("responsive")->where('id',$icd_codes[$i])->pluck('icd_code');
				$icd[$j] = Icd::where('id',$icd_codes[$i])->pluck('icd_code')->first();
			$j++;
		}
       return $icd;
	}

	public static function getIcdIds($icd_code)
	{
		//Icd::connecttoadmindatabase();
		//return  Icd::on('responsive')->where('icd_code',$icd_code)->pluck('id');
		return  Icd::where('icd_code',$icd_code)->pluck('id')->first();
	} 

	public static function getIcdDescription($icd_code)
	{
		//Icd::connecttoadmindatabase();
		//return  Icd::on("responsive")->where('icd_code',$icd_code)->pluck('short_description');
		return  Icd::where('icd_code',$icd_code)->pluck('short_description')->first();
	}

	public static function connecttoadmindatabase() {
		$db = new DBConnectionController();
		$admin_database = getenv('DB_DATABASE');
		$db->configureConnectionByName($admin_database);
		Config::set('database.default',$admin_database);
	}
	
	public static function getIcdValuelists($icd_lists)
	{
		$admin_database = getenv('DB_DATABASE');
		$icd_codes = explode(',',$icd_lists); 
		$j = 1;
		for($i=0;$i<count($icd_codes);$i++) 
		{
			//$icd[$j] = Icd::on("responsive")->where('id',$icd_codes[$i])->pluck('icd_code');
			$icd[$j] = Icd::where('id',$icd_codes[$i])->pluck('icd_code')->first();
			$j++;
		}
       return $icd;
	}
	/*Patient statement used*/
	public static function icd_shot_desc($icd_code)
	{
		$icd_code = ICD::where('id',$icd_code)->pluck('short_description')->first();
		return $icd_code;
	}

	public static function icd_code($icd_id)
	{
		$icd_code = ICD::where('id',$icd_id)->pluck('icd_code')->first();
		return $icd_code;
	}

	public static function getIcdCodeAndDesc($icd_id){
		$icd_code = ICD::where('id',$icd_id)->select('icd_code', 'short_description')->first();
		if(!empty($icd_code)){
			$resp['icd_code'] =  $icd_code->icd_code;
			$resp['short_description'] =  $icd_code->short_description;
			return $resp; 
		}	
		return [];
	}
	
	public static function icd_short($icd_id)
	{
		$icd_code = ICD::where('id',$icd_id)->value('short_description');
		return $icd_code;
	}
	
}