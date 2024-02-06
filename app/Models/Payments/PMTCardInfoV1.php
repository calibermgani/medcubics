<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PMTCardInfoV1 extends Model {
  use SoftDeletes;
  protected $table = "pmt_card_info_v1";
  protected $fillable = ['card_type','card_first_4','card_center','card_last_4','name_on_card','expiry_date','created_by'];
  
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
