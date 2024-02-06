<?php

namespace App\Models\Payments;

use App\Http\Controllers\Charges\Api\ChargeV1ApiController;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use App\Http\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payments\PMTClaimTXV1;
use DB;
use Carbon\Carbon;
use Response;
use App\Traits\CommonUtil;

class PMTUnpostedNotesV1 extends Model
{

    use SoftDeletes;
    use CommonUtil;

    protected $table = "pmt_unposted_notes";
    protected $fillable = ['pmt_id', 'user_id', 'notes', 'updated_at', 'created_at', 'deleted_at'];
	
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