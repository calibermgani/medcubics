<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimCPTShadedInfoV1 extends Model {

    use SoftDeletes;

    protected $table = "claim_cpt_shaded_info_v1";
    protected $fillable = [
        'claim_cpt_info_v1_id',
        'box_24_AToG',
        'created_by',
        'updated_by'
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
