<?php namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Lang;

class McInvoiceProd extends Model 
{
	use SoftDeletes;
	protected $table 	= "mc_invoice_prod";
	protected $dates 	= ['deleted_at'];
	protected $fillable	= ['invoice_id','product_start_date','product_end_date','description','unit_price','quantity','total_price','created_by','updated_by'];
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