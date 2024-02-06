<?php namespace App\Models\Claims;

use Illuminate\Database\Eloquent\Model;

class EdiTransmission extends Model {
	//protected $table = 'edi_eligibility';
	
	protected $fillable = ['transmission_type','total_claims','total_billed_amount','file_path','is_transmitted','created_by'];
		   
	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}	

	public function claim_transmission() 
	{
		return $this->hasMany('App\Models\Claims\TransmissionClaimDetails', 'edi_transmission_id', 'id');
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

	
	/*public function provider() 
	{
		return $this->belongsTo('App\Models\Provider', 'provider_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}

	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}	
	public function contact_details() 
	{
        return $this->belongsTo('App\Models\EdiEligibilityDemo', 'contact_detail', 'id');
    }	*/   
}
