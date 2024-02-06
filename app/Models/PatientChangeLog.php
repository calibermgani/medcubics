<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;
use Config;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\Patients\Patient as Patient;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\Patients\PatientInsurance;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Customer as Customer;
use Auth;
use Lang;
use DB;
use Cache;

class PatientChangeLog extends Model {   

  
    protected $table = 'patient_change_log';

   
    // public static function boot() {
    //    parent::boot();
    //    // create a event to happen on saving
    //    static::saving(function($table)  {
    //         foreach ($table->toArray() as $name => $value) {
    //             if (empty($value) && $name <> 'created_at') {
    //                 $table->{$name} = '';
    //             }
    //         }
    //         return true;
    //    });
    // }
    public function users_details() {
        return $this->belongsTo('App\Models\Medcubics\Users', 'changed_by', 'id')->withTrashed();
    }
    public function patient_details() {
         return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id')->withTrashed();
    }
/*    public function patient_insurance() {
        return $this->hasMany('App\Models\Patients\PatientInsurance', 'patient_id', 'id')->with('insurance_details');
    }
    public function contact_details() {
        return $this->hasMany('App\Models\Patients\PatientContact', 'patient_id', 'id');
    }*/
}
