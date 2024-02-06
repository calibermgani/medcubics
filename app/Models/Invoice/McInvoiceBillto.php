<?php namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Lang;

class McInvoiceBillto extends Model 
{
	use SoftDeletes;
	protected $table 	= "mc_invoice_billto";
	protected $dates 	= ['deleted_at'];
	protected $fillable	= ['practice_id','contact_name','street_1','street_2','city','state','zip_5','zip_4','contact_no','mobile_no','created_by','updated_by'];
	// public static $rules = ['procedure_category' => 'required'];
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