<?php namespace App\Models;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Illuminate\Database\Eloquent\Model;
use Auth;

class AddressFlag extends Model {
	protected $table = 'addressflag';
	
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
    
	public static function checkAndInsertAddressFlag($addressFlag)
	{
		$get_practiceAPI = DBConnectionController::getUserAPIIds('address');
		if($get_practiceAPI ==1)
		{
			$address_flag_array = AddressFlag::where('type',$addressFlag['type'])->where('type_id',$addressFlag['type_id'])->where('type_category',$addressFlag['type_category'])->first();

			if(!$address_flag_array)
				$address_flag_array = new AddressFlag;

			$address_flag_array->type = $addressFlag['type'];
			$address_flag_array->type_id = $addressFlag['type_id'];
			$address_flag_array->type_category = $addressFlag['type_category'];
			$address_flag_array->address2 = isset($addressFlag['address2']) ? $addressFlag['address2'] : '';
			$address_flag_array->city = $addressFlag['city'];
			$address_flag_array->state = $addressFlag['state'];					
			$address_flag_array->zip5 = $addressFlag['zip5'];
			$address_flag_array->zip4 = $addressFlag['zip4'];
			$address_flag_array->is_address_match = $addressFlag['is_address_match'];
			$address_flag_array->error_message = $addressFlag['error_message'];
			$address_flag_array->save();
		}
	}

	public static function getAddressFlag($type,$type_id,$type_category)
	{
        $address_arr = AddressFlag::where('type',$type)->where('type_id',$type_id)->where('type_category',$type_category)
        				->select('address2', 'city', 'state', 'zip5', 'zip4', 'is_address_match', 'error_message', 'address2')->first();
        if($address_arr) {
            $address['address1'] = $address_arr->address2;
            $address['city'] = $address_arr->city;
            $address['state'] = $address_arr->state;
            $address['zip5'] = $address_arr->zip5;
            $address['zip4'] = $address_arr->zip4;
            $address['is_address_match'] = $address_arr->is_address_match;
            $address['error_message'] = $address_arr->error_message;
            $address['address1'] = $address_arr->address2;
        } else {
            $address['address1'] = '';
            $address['city'] = '';
            $address['state'] = '';
            $address['zip5'] = '';
            $address['zip4'] = '';
            $address['is_address_match'] = '';
            $address['error_message'] = '';
            $address['address1'] = '';
        }
        return $address;
	}
 }
