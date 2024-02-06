<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ClaimEDIInfoV1 extends Model {

	use SoftDeletes;

    protected $table = "claim_edi_info_v1";
    protected $fillable = ['claim_id', 'edi_notes', 'denial_codes', 'response_file_path', 'rejected_date','updated_by', 'created_by'];
	
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
