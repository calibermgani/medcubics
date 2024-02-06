<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NpiFlag extends Model {
	protected $table = 'npiflag';
	protected $fillable=['company_name','type','type_id','type_category',
           'location_address_1','location_address_2','location_address_type','location_city', 'location_state','location_country_code','location_country_name','location_postal_code','location_telephone_number','location_fax_number','mailling_address_1','mailling_address_2','mailling_address_type','mailling_city','mailling_state','mailling_country_code','mailling_country_name','mailling_postal_code','mailling_telephone_number',
          'mailling_fax_number','basic_credential','basic_enumeration_date','basic_first_name','basic_last_name','basic_middle_name','basic_gender','basic_last_updated','basic_name_prefix','basic_sole_proprietor','basic_status','created_epoch','enumeration_type','identifiers_code','identifiers_desc','identifiers_identifier','identifiers_issuer','identifiers_state','last_updated_epoch','number','taxonomies_code','taxonomies_desc',
            'taxonomies_license','taxonomies_primary','taxonomies_state','is_valid_npi','npi_error_message','basic_authorized_official_credential',
            'basic_authorized_official_first_name','basic_authorized_official_last_name','basic_authorized_official_name_prefix','basic_authorized_official_telephone_number', 'basic_authorized_official_title_or_position','basic_organization_name','basic_organizational_subpart'
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
	
	public static function checkAndInsertNpiFlag($request)
	{
		$npi_flag_array = NpiFlag::getNpiFlag($request['type'],$request['type_id'],$request['type_category']);

		if(!$npi_flag_array) {
			NpiFlag::create($request);
		} else {
			$npi_flag_array->update($request);
		}
	}

	public static function getNpiFlag($type,$type_id,$type_category)
	{
		return NpiFlag::where('type',$type)->where('type_id',$type_id)->where('type_category',$type_category)->first();
	}

}