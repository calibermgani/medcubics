<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
use Config;
use Auth;
use App\Models\Insurance as Insurance;

class Eras extends Model 
{
	use SoftDeletes;
	protected $table	= 'eras';
	protected $fillable = ['receive_date','check_no','check_date','check_paid_amount','check_amount','insurance_id','provider_npi_id','provider_npi_id','pdf_name','total_claims','claim_nos','insurance_name','org_check_no','archive_status'];
	
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
    
	public function insurance_details() 
	{
        return $this->belongsTo('App\Models\Insurance', 'insurance_id', 'id')->select('id','insurance_name','short_name');
    }
	
	public function check_details(){
		return $this->belongsTo('App\Models\Payments\PMTCheckInfoV1', 'check_no', 'check_no')->with('pmt_details');
	}
	
	public function eft_details(){
		return $this->belongsTo('App\Models\Payments\PMTEFTInfoV1', 'check_no', 'eft_no')->with('pmt_details')->select('id','eft_no','eft_date');
	}
}