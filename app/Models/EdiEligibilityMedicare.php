<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdiEligibilityMedicare extends Model {
	protected $table = 'edi_eligibility_medicare';
	
	protected $fillable = ['plan_type','plan_type_label', 'active', 'deductible', 'deductible_remaining', 'coinsurance_percent', 'copayment', 'payer_name', 'policy_number', 'contact_details', 'insurance_type', 'insurance_type_label', 'mco_bill_option_code', 'mco_bill_option_label', 'locked', 'info_valid_till', 'start_date', 'end_date', 'effective_date', 'termination_date'];
	
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

	public function contact_details() 
	{
        return $this->belongsTo('App\Models\EdiEligibilityContact_detail', 'contact_details', 'id');
  }	   
}