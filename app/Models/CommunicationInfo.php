<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunicationInfo extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = "communication_info";

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
        'sid',
        'com_provider',
        'from',
        'to',
        'direction',
        'patient_id',
        'claim_id',
        'com_type',
        'start_time',
        'duration',
        'status',
        'cost','created_by'];
}
