<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Patients\Patient as Patient;

use DB;
use Lang;

class STMTCategory extends Model {

	use SoftDeletes;
	
	protected $table = "stmt_category";

	protected $dates = ['deleted_at'];
	
	public $timestamps = false;

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

	public function creator(){
		return $this->belongsTo('App\User', 'created_by', 'id');
	}

	public function modifier(){
		return $this->belongsTo('App\User', 'updated_by', 'id');
	}

	public function holdreason(){
		return $this->belongsTo('App\Models\STMTHoldReason', 'hold_reason', 'id');
	}
	
	protected $fillable = [
        'stmt_option', 'category', 'hold_reason', 'hold_release_date', 'status','created_by','updated_by'
    ];	
    
    public static $rules = [
        'stmt_option' => 'required',
	];

	public static function messages(){
		return [
			'stmt_option.required'		=> "Please select statement option",
			'category.required' 		=> "Please select statement category"
		];
	}

	public static function getCategoryAppliedCount($category_id = 0) {
		$cnt = Patient::where('stmt_category', $category_id)->count();
		return $cnt;
	}


	public static function getStmtCategoryList() {
		$stmt_category = STMTCategory::where('status', 'Active')->pluck('category','id')->all();
		return $stmt_category;
	}	

	public static function updatePatientStmtRecords($category_id = 0, $request) {		
		try {
			if(isset($request['stmt_option'])){
				
				$update_arr = [];
				$update_arr['statements'] = $request['stmt_option'];

				switch ($request['stmt_option']) {				
					
					case 'Hold':
						// Update patients stmt_category to 'Hold', hold_reason, hold_release_date						
						$update_arr['hold_reason'] = isset($request['hold_reason']) ? $request['hold_reason'] : '';
						if (isset($request['hold_release_date']) && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
				            $update_arr['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
				        } else {
				            $update_arr['hold_release_date'] = "0000-00-00";
				        }
						break;

					case 'Yes':						
					case 'Insurance Only':
						$update_arr['hold_reason'] = '';
						$update_arr['hold_release_date'] = "0000-00-00";
						break;	
				}			
				Patient::where('stmt_category', $category_id)->update($update_arr);
			}

		} catch(Exception $e) {
			\Log::info("While update statement changes in patient record getting error ".$e->getMessage());
		} 
	}

}  