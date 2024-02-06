<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Insuranceappealaddress extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'insuranceappealaddress';

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

    public function user()
    {
		return $this->belongsTo('App\User','created_by','id');
    }
	
    public function userupdate()
    {
		return $this->belongsTo('App\User','updated_by','id');
    }

	protected $fillable = array (
		'insurance_id',
		'address_1',
		'address_2',
		'city',
		'state',
		'zipcode5', 
		'zipcode4',
		'phone',
		'phoneext',
		'fax',
		'email',
		'created_by',
		'updated_by',
		'created_at',
		'updated_at',
		'deleted_at'
	);
	
	public static $rules = [ 
                'address_1' 	=> 'required',
		//'address_1' 	=> 'required|regex:/^[A-Za-z0-9 ]+$/i',
		'city' 			=> 'required|regex:/^[A-Za-z0-9 ]+$/i',
		'state' 		=> 'required|regex:/^[A-Za-z]+$/i',
		'zipcode5' 		=> 'required|digits:5',
		'zipcode4' 		=> 'nullable|min:4|max:4',
		'email' 		=> 'nullable|email'
	];
	
	public static function messages(){
		return [
			'address_1.required' 		=> Lang::get("common.validation.address1_required"),
			'city.required'				=> Lang::get("common.validation.city_required"),
			'state.required'			=> Lang::get("common.validation.state_required"),
			'city.regex'				=> Lang::get("common.validation.alphanumericspac"),
			'state.regex'				=> Lang::get("common.validation.alpha"),
			'zipcode5.required'			=> Lang::get("common.validation.zipcode5_required"),
			'zipcode5.digits'			=> Lang::get("common.validation.zipcode5_limit"),
			'zipcode4.min'				=> Lang::get("common.validation.zipcode4_limit"),
			'zipcode4.max'				=> Lang::get("common.validation.zipcode4_limit"),
			'email.email'				=> Lang::get("common.validation.email_valid"),
		];
	}
}