<?php namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Lang;

class McInvoice extends Model 
{
	use SoftDeletes;
	protected $table 	= "mc_invoice";
	protected $dates 	= ['deleted_at'];
	protected $fillable	= ['invoice_no','header', 'practice_id','invoice_date','invoice_start_date','invoice_end_date','invoice_amt','tax','previous_amt_due','total_amt','notes','created_by','updated_by'];
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