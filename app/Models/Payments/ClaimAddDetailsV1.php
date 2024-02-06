<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimAddDetailsV1 extends Model {

    use SoftDeletes;
    protected  $table = "claim_add_details_v1";

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

    protected $fillable = [
		'attorney_id',
		'facility_mrn',
		'provider_id',
		'is_provider_employed',
		'reserved_nucc_box8',
		'reserved_nucc_box9b',
		'reserved_nucc_box9c',
		'is_employment',
		'is_autoaccident',
		'autoaccident_state',
		'is_otheraccident',
		'otherclaimid',
		'print_signature_onfile_box12',
		'print_signature_onfile_box13',
		'illness_box14',
		'other_date',
		'unable_to_work_from',
		'unable_to_work_to',
		'additional_claim_info',
		'resubmission_code',
		'emergency',
		'outside_lab',
		'accept_assignment',
		'reserved_nucc_box30',
		'created_by',
		'updated_by',
		'claim_code',
		'patient_id',
		'original_ref_no',
		'box23_type',
		'box_23',
		'other_date_qualifier',
		'provider_qualifier',
		'provider_otherid',
		'lab_charge',
		'service_facility_qual',
		'facility_otherid',
		'billing_provider_qualifier',
		'billing_provider_otherid',
		'claim_id',
		'epsdt',
		'rendering_provider_qualifier',
		'rendering_provider_otherid',
		'otherclaimid_qual'
	];

    public function provider_details(){
        return $this->belongsTo('App\Models\Provider', 'provider_id', 'id')->select('id','provider_name','npi');
    }

    public function claim_info(){
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id');
    }

    public function facility_detail(){
        return $this->belongsTo('App\Models\Facility', 'facility_id', 'id')->select('id','facility_name')
        		->with(array('facility_address'=>function($query){ 
        				$query->select('id','facilityid','address1','city','pay_zip5','pay_zip4');
        			}));
    }

}
