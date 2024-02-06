<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientStatementSettings extends Model {
	protected $table 	= 'patientstatement_settings';
	protected $fillable	= [
		'paybydate','alert', 'servicelocation','check_add_1','check_add_2','check_city','check_state','check_zip5','check_zip4','rendering_provider','callbackphone','statementsentdays','bulkstatement','statementcycle','week_1_billcycle','week_2_billcycle','week_3_billcycle','week_4_billcycle','week_5_billcycle','week_1_facility','week_2_facility','week_3_facility','week_4_facility','week_5_facility','week_1_provider','week_2_provider','week_3_provider','week_4_provider','week_5_provider','week_1_account','week_2_account','week_3_account','week_4_account','week_5_account','week_1_category', 'week_2_category', 'week_3_category', 'week_4_category','week_5_category',	'minimumpatientbalance','displaypayment','latestpaymentinfo','paymentmessage','paymentmessage_1','paymentmessage_2','paymentmessage_3','cpt_shortdesc','primary_dx','insserviceline','patserviceline','financial_charge','updated_by','spacial_message_1','insurance_balance','aging_bucket'
	];

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

}