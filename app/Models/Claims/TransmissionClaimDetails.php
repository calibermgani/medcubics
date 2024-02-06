<?php

namespace App\Models\Claims;

use Illuminate\Database\Eloquent\Model;

class TransmissionClaimDetails extends Model {

    //protected $table = 'edi_eligibility';

    protected $fillable = ['edi_transmission_id', 'claim_id', 'claim_type', 'insurance_id', 'icd', 'referring_provider_id', 'total_billed_amount'];

    public function claims() {
        //return $this->belongsTo('App\Models\Patients\Claims', 'claim_id', 'id');
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id');
    }

    public function insurance() {
        return $this->belongsto('App\Models\Insurance', 'insurance_id', 'id');
    }

    public function cpt_transmission() {
        return $this->hasMany('App\Models\Claims\TransmissionCptDetails', 'transmission_claim_id', 'id');
    }

    public function edi_transmission() {
        return $this->belongsTo('App\Models\Claims\EdiTransmission', 'edi_transmission_id', 'id');
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

    /* public function patient() 
      {
      return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id');
      }

      public function provider()
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
      } */
}
