<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonMailApiController as CommonMailApiController;
use Response;
use Request;
use View;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Models\Taxanomy as Taxanomy;
use App\Models\Speciality as Speciality;
use App\Models\Provider as Provider;
use App\Models\Insurance as Insurance;
use App\Models\EdiEligibilityDemo as EdiEligibilityDemo;
use App\Models\EdiEligibilityMedicare as EdiEligibilityMedicare;
use App\Models\EdiEligibilityInsurance as EdiEligibilityInsurance;
use App\Models\EdiEligibilityContact_detail as EdiEligibilityContact_detail;
use App\Models\EdiEligibilityInsuranceSpPhysician as EdiEligibilityInsuranceSpPhysician;
use App\Models\EdiEligibility as EdiEligibility;
use App\Models\PatientEligibilityWaystar as PatientEligibilityWaystar;
use App\Models\Patients\PatientEligibility as PatientEligibility;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Patients\Patient;
use App\Http\Helpers\Helpers as Helpers;
use App;
use DB;
use Log;
use PDF;
use Auth;
use Config;
use Twilio;
use Lang;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Medcubics\UserIp as UserIp;
use App\Models\Medcubics\Customer as Customer;
use App\Http\Controllers\Medcubics\Api\CustomerApiController;
use Session;
use App\Models\Medcubics\Practice;
use Redirect;
class CommonApiController extends Controller {

    public function checkUSPSAddressCheck(Request $request) {
        $get_practiceAPI = DBConnectionController::getUserAPIIds('address');
        $address_api = ApiConfig::where('api_for', 'address')->where('api_status', 'Active')->first();

        if ($address_api) {
            if ($address_api->api_name == 'usps') {
                $address2 = $request::input('address1');
                $city = $request::input('city');
                $state = $request::input('state');
                $zip5 = trim($request::input('zip5'));
                $zip4 = $request::input('zip4');
                $check_either = $request::input('current_hit');

                $url = $address_api->url;
                $msg = '<AddressValidateRequest USERID="' . $address_api->usps_user_id . '">';
                $msg .= '<IncludeOptionalElements>true</IncludeOptionalElements>';
                $msg .= '<ReturnCarrierRoute>true</ReturnCarrierRoute>';
                $msg .= '<Address ID="0">';
                $msg .= '<FirmName />';
                $msg .= '<Address1 />';
                $msg .= '<Address2>' . $address2 . '</Address2>';

                if ($check_either == 'city' || $check_either == 'state') {
                    $msg .= '<City>' . $city . '</City>';
                    $msg .= '<State>' . $state . '</State>';
                    $msg .= '<Zip5></Zip5>';
                }

                if ($check_either == 'zip5' || $check_either == 'zip4') {
                    $msg .= '<City></City>';
                    $msg .= '<State></State>';
                    $msg .= '<Zip5>' . $zip5 . '</Zip5>';
                }
                if ($check_either == 'address') {
                    $msg .= '<City>' . $city . '</City>';
                    $msg .= '<State>' . $state . '</State>';
                    $msg .= '<Zip5>' . $zip5 . '</Zip5>';
                }

                $msg .= '<Zip4></Zip4>';
                $msg .= '</Address>';
                $msg .= '</AddressValidateRequest>';
                $contextOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    )
                );
                $newurl = $url . urlencode($msg);
                $xml = $newurl;
                $parser = xml_parser_create();
                $xmldata = file_get_contents($newurl, false, stream_context_create($contextOptions));
                xml_parse_into_struct($parser, $xmldata, $values);
                xml_parser_free($parser);
                $xml = simplexml_load_string($xmldata);

                if (!$xml->Address->Error && !$xml->Number) {
                    $data['address1'] = $xml->Address->Address2;
                    $data['address2'] = $xml->Address->Address1;
                    $data['city'] = $xml->Address->City;
                    $data['state'] = $xml->Address->State;
                    $data['zip5'] = $xml->Address->Zip5;
                    $data['zip4'] = $xml->Address->Zip4;

                    return Response::json(array('status' => 'success', 'message' => '', 'data' => $data));
                }
                else {
                    $message = 'Erro found';
                    if ($xml->Address->Error)
                        $message = $xml->Address->Error->Description;
                    elseif ($xml->Number)
                        $message = $xml->Description;

                    return Response::json(array('status' => 'error', 'message' => $message, 'data' => ''));
                }
            }
        } else {
            return Response::json(array('status' => 'error', 'message' => 'no_validation', 'data' => ''));
        }
    }

    public function npiCheck(Request $request) {
        $get_practiceAPI = DBConnectionController::getUserAPIIds('npi');
        $address_api = ApiConfig::where('api_for', 'npi')->where('api_status', 'Active')->first();
        if ($address_api) {
            try {
                $npi = $request::input('npi');
                $is_provider = $request::input('is_provider');
                $url = $address_api->url . $npi . '&taxonomy_description=&first_name=&last_name=&organization_name=&address_purpose=&city=&state=&postal_code=&country_code=&limit=&skip=&version=2.0&pretty=true';
                // Get cURL resource
                $curl = curl_init();

                // Set some options - we are passing in a useragent too here
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_SSL_VERIFYPEER => false
                ));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                // Send the request & save response to $resp
                $resp = curl_exec($curl);

                if (!curl_exec($curl)) {
                    die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
                }

                // Close request to clear up some resources
                curl_close($curl);

                $result_array = json_decode($resp);
                // dd($result_array);
                if (isset($result_array->Errors)) {
                    $data['npi_details']['is_valid_npi'] = 'No';
                    $data['npi_details']['npi_error_message'] = isset($result_array->Errors->number) ?  $result_array->Errors->number : '';
                    return Response::json(array('status' => 'error', 'message' => $data['npi_details']['npi_error_message'], 'data' => $data));
                }
                elseif (isset($result_array) && $result_array->result_count == 1) {
                    $data['npi_details']['location_address_1'] = @$result_array->results[0]->addresses[0]->address_1;
                    $data['npi_details']['location_address_2'] = @$result_array->results[0]->addresses[0]->address_2;
                    $data['npi_details']['location_address_type'] = @$result_array->results[0]->addresses[0]->address_type;
                    $data['npi_details']['location_city'] = @$result_array->results[0]->addresses[0]->city;
                    $data['npi_details']['location_state'] = @$result_array->results[0]->addresses[0]->state;
                    $data['npi_details']['location_country_code'] = @$result_array->results[0]->addresses[0]->country_code;
                    $data['npi_details']['location_country_name'] = @$result_array->results[0]->addresses[0]->country_name;
                    $data['npi_details']['location_postal_code'] = @$result_array->results[0]->addresses[0]->postal_code;
                    $data['npi_details']['location_telephone_number	'] = @$result_array->results[0]->addresses[0]->telephone_number;
                    $data['npi_details']['location_fax_number'] = @$result_array->results[0]->addresses[0]->fax_number;

                    $data['npi_details']['mailling_address_1'] = @$result_array->results[0]->addresses[1]->address_1;
                    $data['npi_details']['mailling_address_2'] = @$result_array->results[0]->addresses[1]->address_2;
                    $data['npi_details']['mailling_address_type'] = @$result_array->results[0]->addresses[1]->address_type;
                    $data['npi_details']['mailling_city'] = @$result_array->results[0]->addresses[1]->city;
                    $data['npi_details']['mailling_state'] = @$result_array->results[0]->addresses[1]->state;
                    $data['npi_details']['mailling_country_code'] = @$result_array->results[0]->addresses[1]->country_code;
                    $data['npi_details']['mailling_country_name'] = @$result_array->results[0]->addresses[1]->country_name;
                    $data['npi_details']['mailling_postal_code'] = @$result_array->results[0]->addresses[1]->postal_code;
                    $data['npi_details']['mailling_telephone_number	'] = @$result_array->results[0]->addresses[1]->telephone_number;
                    $data['npi_details']['mailling_fax_number'] = @$result_array->results[0]->addresses[1]->fax_number;
                    if (@$result_array->results[0]->enumeration_type == 'NPI-2') {
                        $data['npi_details']['basic_authorized_official_credential'] = @$result_array->results[0]->basic->authorized_official_credential;
                        $data['npi_details']['basic_authorized_official_first_name'] = @$result_array->results[0]->basic->authorized_official_first_name;
                        $data['npi_details']['basic_authorized_official_last_name'] = @$result_array->results[0]->basic->authorized_official_last_name;
                        $data['npi_details']['basic_authorized_official_name_prefix'] = @$result_array->results[0]->basic->authorized_official_name_prefix;
                        $data['npi_details']['basic_authorized_official_telephone_number'] = @$result_array->results[0]->basic->authorized_official_telephone_number;
                        $data['npi_details']['basic_authorized_official_title_or_position'] = @$result_array->results[0]->basic->authorized_official_title_or_position;
                        $data['npi_details']['basic_organization_name'] = @$result_array->results[0]->basic->organization_name;
                        $data['npi_details']['basic_organizational_subpart'] = @$result_array->results[0]->basic->organizational_subpart;

                        $data['npi_details']['basic_status'] = @$result_array->results[0]->basic->status;
                        $data['npi_details']['basic_enumeration_date'] = @$result_array->results[0]->basic->enumeration_date;
                        $data['npi_details']['basic_last_updated'] = @$result_array->results[0]->basic->last_updated;

                        $data['npi_details']['basic_credential'] = '';
                        $data['npi_details']['basic_first_name'] = '';
                        $data['npi_details']['basic_last_name'] = '';
                        $data['npi_details']['basic_middle_name'] = '';
                        $data['npi_details']['basic_gender'] = '';
                        $data['npi_details']['basic_name_prefix'] = '';
                        $data['npi_details']['basic_sole_proprietor'] = '';
                    }
                    else {
                        $data['npi_details']['basic_credential'] = @$result_array->results[0]->basic->credential;
                        $data['npi_details']['basic_first_name'] = @$result_array->results[0]->basic->first_name;
                        $data['npi_details']['basic_last_name'] = @$result_array->results[0]->basic->last_name;
                        $data['npi_details']['basic_middle_name'] = @$result_array->results[0]->basic->middle_name;
                        $data['npi_details']['basic_gender'] = @$result_array->results[0]->basic->gender;
                        $data['npi_details']['basic_name_prefix'] = @$result_array->results[0]->basic->name_prefix;
                        $data['npi_details']['basic_sole_proprietor'] = @$result_array->results[0]->basic->sole_proprietor;

                        $data['npi_details']['basic_status'] = @$result_array->results[0]->basic->status;
                        $data['npi_details']['basic_enumeration_date'] = @$result_array->results[0]->basic->enumeration_date;
                        $data['npi_details']['basic_last_updated'] = @$result_array->results[0]->basic->last_updated;

                        $data['npi_details']['basic_authorized_official_credential'] = '';
                        $data['npi_details']['basic_authorized_official_first_name'] = '';
                        $data['npi_details']['basic_authorized_official_last_name'] = '';
                        $data['npi_details']['basic_authorized_official_name_prefix'] = '';
                        $data['npi_details']['basic_authorized_official_telephone_number'] = '';
                        $data['npi_details']['basic_authorized_official_title_or_position'] = '';
                        $data['npi_details']['basic_organization_name'] = '';
                        $data['npi_details']['basic_organizational_subpart'] = '';
                    }

                    if ($result_array->results[0]->identifiers) {
                        $data['npi_details']['identifiers_code'] = @$result_array->results[0]->identifiers[0]->code;
                        $data['npi_details']['identifiers_desc'] = @$result_array->results[0]->identifiers[0]->desc;
                        $data['npi_details']['identifiers_identifier'] = @$result_array->results[0]->identifiers[0]->identifier;
                        $data['npi_details']['identifiers_issuer'] = @$result_array->results[0]->identifiers[0]->issuer;
                        $data['npi_details']['identifiers_state'] = @$result_array->results[0]->identifiers[0]->state;
                    }
                    else {
                        $data['npi_details']['identifiers_code'] = '';
                        $data['npi_details']['identifiers_desc'] = '';
                        $data['npi_details']['identifiers_identifier'] = '';
                        $data['npi_details']['identifiers_issuer'] = '';
                        $data['npi_details']['identifiers_state'] = '';
                    }

                    $data['npi_details']['created_epoch'] = @$result_array->results[0]->created_epoch;
                    $data['npi_details']['enumeration_type'] = @$result_array->results[0]->enumeration_type;
                    $data['npi_details']['last_updated_epoch'] = @$result_array->results[0]->last_updated_epoch;
                    $data['npi_details']['number'] = @$result_array->results[0]->number;

                    if ($result_array->results[0]->taxonomies) {
                        $data['npi_details']['taxonomies_code'] = @$result_array->results[0]->taxonomies[0]->code;
                        $data['npi_details']['taxonomies_desc'] = @$result_array->results[0]->taxonomies[0]->desc;
                        $data['npi_details']['taxonomies_license'] = @$result_array->results[0]->taxonomies[0]->license;
                        $data['npi_details']['taxonomies_primary'] = @$result_array->results[0]->taxonomies[0]->primary;
                        $data['npi_details']['taxonomies_state'] = @$result_array->results[0]->taxonomies[0]->state;
                    }
                    else {
                        $data['npi_details']['taxonomies_code'] = '';
                        $data['npi_details']['taxonomies_desc'] = '';
                        $data['npi_details']['taxonomies_license'] = '';
                        $data['npi_details']['taxonomies_primary'] = '';
                        $data['npi_details']['taxonomies_state'] = '';
                    }
                    $data['npi_details']['is_valid_npi'] = 'Yes';
                    $data['npi_details']['npi_error_message'] = '';

                    if ($is_provider == 'yes') {
                        $data['provider']['enumeration_type'] = @$result_array->results[0]->enumeration_type;
                        /* if(@$result_array->results[0]->enumeration_type == 'NPI-2')
                          $data['provider']['provider_types_id'] = 5;
                          else
                          $data['provider']['provider_types_id'] = 5; */
                        $data['provider']['address_1'] = @$result_array->results[0]->addresses[0]->address_1;
                        $data['provider']['address_2'] = @$result_array->results[0]->addresses[0]->address_2;
                        $data['provider']['city'] = @$result_array->results[0]->addresses[0]->city;
                        $data['provider']['state'] = @$result_array->results[0]->addresses[0]->state;
                        $data['provider']['zipcode5'] = substr(@$result_array->results[0]->addresses[0]->postal_code, 0, 5);
                        $data['provider']['zipcode4'] = substr(@$result_array->results[0]->addresses[0]->postal_code, 5, 4);
                        $data['provider']['phone'] = '(' . preg_replace('/-/', ') ', @$result_array->results[0]->addresses[0]->telephone_number, 1);
                        $data['provider']['fax'] = '(' . preg_replace('/-/', ') ', @$result_array->results[0]->addresses[0]->fax_number, 1);
                        if (@$result_array->results[0]->enumeration_type == 'NPI-2')
                            $data['provider']['provider_degrees_id'] = @$result_array->results[0]->basic->authorized_official_credential;
                        else
                            $data['provider']['provider_degrees_id'] = @$result_array->results[0]->basic->credential;

                        $data['provider']['first_name'] = @$result_array->results[0]->basic->first_name;
                        $data['provider']['last_name'] = @$result_array->results[0]->basic->last_name;
                        $data['provider']['middle_name'] = @$result_array->results[0]->basic->middle_name;
                        $data['provider']['organization_name'] = @$result_array->results[0]->basic->organization_name;

                        if (@$result_array->results[0]->basic->gender == 'M')
                            $data['provider']['gender_m'] = 'Male';
                        else
                            $data['provider']['gender_m'] = '';
                        if (@$result_array->results[0]->basic->gender == 'F')
                            $data['provider']['gender_f'] = 'Female';
                        else
                            $data['provider']['gender_f'] = '';

                        if (@$result_array->results[0]->identifiers)
                            $data['provider']['medicareptan'] = @$result_array->results[0]->identifiers[0]->identifier;
                        else
                            $data['provider']['medicareptan'] = '';
                        /* if(@$result_array->results[0]->taxonomies){
                          $data['provider']['taxanomies_id'] = Taxanomy::getTaxanomyId($result_array->results[0]->taxonomies[0]->code);
                          $data['provider']['specialities_id'] = Speciality::getSpecialityId($result_array->results[0]->taxonomies[0]->desc);
                          } else {
                          $data['provider']['taxanomies_id'] = '';
                          $data['provider']['specialities_id'] = '';
                          } */
                    }
                    $npi_value = [];
                    foreach ($data as $key_data => $data_value) {
                        $npi_value[$key_data] = array_map(array($this, 'ucFirst'), $data_value);
                    }
                    $data = $npi_value;
                    $data['npi_details']['location_address_type'] = strtoupper($data['npi_details']['location_address_type']);
                    $data['npi_details']['location_state'] = strtoupper($data['npi_details']['location_state']);
                    $data['npi_details']['location_country_code'] = strtoupper($data['npi_details']['location_country_code']);
                    $data['npi_details']['location_country_name'] = ucwords($data['npi_details']['location_country_name']);
                    $data['npi_details']['location_city'] = ucwords($data['npi_details']['location_city']);
                    $data['npi_details']['mailling_city'] = ucwords($data['npi_details']['mailling_city']);
                    $data['npi_details']['mailling_address_type'] = strtoupper($data['npi_details']['mailling_address_type']);
                    $data['npi_details']['mailling_state'] = strtoupper($data['npi_details']['mailling_state']);
                    $data['npi_details']['mailling_country_code'] = strtoupper($data['npi_details']['mailling_country_code']);
                    $data['npi_details']['mailling_country_name'] = ucwords($data['npi_details']['mailling_country_name']);
                    $data['npi_details']['basic_credential'] = strtoupper($data['npi_details']['basic_credential']);
                    $data['npi_details']['basic_name_prefix'] = strtoupper($data['npi_details']['basic_name_prefix']);
                    $data['npi_details']['basic_sole_proprietor'] = strtoupper($data['npi_details']['basic_sole_proprietor']);
                    $data['npi_details']['identifiers_identifier'] = strtoupper($data['npi_details']['identifiers_identifier']);
                    $data['npi_details']['enumeration_type'] = strtoupper($data['npi_details']['enumeration_type']);
                    $data['npi_details']['taxonomies_code'] = strtoupper($data['npi_details']['taxonomies_code']);
                    $data['npi_details']['taxonomies_desc'] = ucwords($data['npi_details']['taxonomies_desc']);
                    @$data['provider']['city'] = ucwords($data['provider']['city']);
                    $data['npi_details']['taxonomies_state'] = strtoupper($data['npi_details']['taxonomies_state']);
                    @$data['provider']['enumeration_type'] = strtoupper($data['npi_details']['enumeration_type']);
                    @$data['provider']['state'] = strtoupper($data['provider']['state']);
                    @$data['provider']['provider_degrees_id'] = strtoupper($data['provider']['provider_degrees_id']);
                    @$data['npi_details']['basic_last_updated'] = Helpers::dateFormat($data['npi_details']['basic_last_updated'], 'dob');
                    @$data['npi_details']['basic_enumeration_date'] = Helpers::dateFormat($data['npi_details']['basic_enumeration_date'], 'dob');
                    return Response::json(array('status' => 'success', 'message' => '', 'data' => $data));
                }
                else {
                    $data['npi_details']['is_valid_npi'] = 'No';
                    $data['npi_details']['npi_error_message'] = 'Not found';
                    return Response::json(array('status' => 'error', 'message' => 'not_found', 'data' => $data));
                }
            } catch(Exception $e) {
                \Log::info("Error occured on NPI check ".$e->getMessage() );
                $data['npi_details']['is_valid_npi'] = 'No';
                $data['npi_details']['npi_error_message'] = 'Not found';
                return Response::json(array('status' => 'error', 'message' => 'not_found', 'data' => $data));
            }
        }
        else {
            return Response::json(array('status' => 'error', 'message' => 'no_validation', 'data' => ''));
        }
    }

    /*     * * Uc first character change start ** */

    function ucFirst($value) {
        return ucfirst(strtolower($value));
    }

    /*     * * Uc first character change end ** */

    public function searchAndGetICDAndCPTFromIMO(Request $request) {
        $keyword = $request['search_keyword'];
        $search_for = $request['search_for'];
        $get_cpt_API = DBConnectionController::getUserAPIIds('imo_cpt');
        $get_icd_API = DBConnectionController::getUserAPIIds('imo_icd');
        $imo_api = ApiConfig::where('api_for', $search_for)->where('api_status', 'Active')->first();
        if (isset($imo_api) && ($get_cpt_API == 1 or $get_icd_API == 1)) {
            $url = $imo_api->url;
            $user_credential = $imo_api->usps_user_id;
            $host = $imo_api->host;
            $port = $imo_api->port;
            //"http://sandbox-wps.e-imo.com/IMOTPWS.asmx?op=Execute";
            $soap_request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                            <Execute xmlns="http://www.e-imo.com/">
                            <Value>search^100|5|1|2^' . $keyword . '^' . $user_credential . '</Value>
                            <Host>' . $host . '</Host>
                            <Port>' . $port . '</Port>
                            </Execute>
                            </soap:Body>
                            </soap:Envelope>';
            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, $url);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_request);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
            $result = curl_exec($soap_do);
            $result = html_entity_decode($result);
            $result = str_replace('</ExecuteResult></ExecuteResponse></soap:Body></soap:Envelope>', '', $result);
            $result = str_replace('<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><ExecuteResponse xmlns="http://www.e-imo.com/"><ExecuteResult>', '', $result);
            $result = simplexml_load_string($result);
            return $result;
        }
    }

    /*
     * This Function For Patient Eliglibility In Pverify API
     * Author		: Selvakumar.V
     * Created on	: 04Oct2017
     */

    public function checkEligibility($elibility_arr) {
		if(Session::get('practice_dbid') == 40)
			$eligibility_api = ApiConfig::where('api_for', 'insurance_eligibility')->where('api_name','waystar')->where('api_status', 'Active')->first();
		else
			$eligibility_api = ApiConfig::where('api_for', 'insurance_eligibility')->where('api_name','!=','waystar')->where('api_status', 'Active')->first();
		
		
        if ($eligibility_api->api_name == 'eligible') {
            $provider_id = $elibility_arr['provider_id'];
            $patient_id = $elibility_arr['patient_id'];
            $insurance_id = $elibility_arr['insurance_id'];
            $category = $elibility_arr['category'];
            $policy_id = $elibility_arr['member_id'];

            $date = $elibility_arr['date'];
            unset($elibility_arr['provider_id']);
            unset($elibility_arr['patient_id']);
            unset($elibility_arr['insurance_id']);
            unset($elibility_arr['date']);
            unset($elibility_arr['category']);
            unset($elibility_arr['medicare']);

            $return_arr['temp_id'] = "";
            $temp_patient_id = "";
            if ($patient_id == '') {
                $temp_patient_id = rand();
                $return_arr['temp_id'] = $temp_patient_id;
            }

            if ($provider_id > 0) {
                //echo 'hi';
                //echo $provider_id;
                $provider_details = Provider::where('id', $provider_id)->first();
            }
            else {
                //echo 'hi1';
                $provider_details = Provider::where('first_name', '!=', '')->where('last_name', '!=', '')->where('status', 'Active')->first();
            }

            //print_r($provider_details);			
            if (count((array)$provider_details) != '0') {
                $elibility_arr['provider_npi'] = $provider_details->npi;

                if ($provider_details->organization_name != '')
                    $elibility_arr['service_provider_organization_name'] = $provider_details->organization_name;
                else {
                    $elibility_arr['provider_last_name'] = $provider_details->last_name;
                    $elibility_arr['provider_first_name'] = $provider_details->first_name;
                }
            }

            $chk_env_site = getenv('APP_ENV');
            if ($chk_env_site != Config::get('siteconfigs.production.defult_production')) {
                $elibility_arr['member_last_name'] = 'Andreassi';
                $elibility_arr['member_first_name'] = 'Tiffany';
                $elibility_arr['payer_id'] = 60054;
                $elibility_arr['member_id'] = 'BCBSMASS_PPO';
                $elibility_arr['provider_npi'] = '1245319599';
                $elibility_arr['service_provider_organization_name'] = 'FAITH HOPE & CHARITY RESIDENTIAL CARE CENTER LLC';
            }

            $api_url = $eligibility_api->url;
            $elibility_arr['api_key'] = $eligibility_api->usps_user_id;

            //print_r($elibility_arr); exit;

            $par = 'test=true';
            foreach ($elibility_arr as $key => $value) {
                if ($value != '') {
                    if ($par != '')
                        $par .= '&';

                    $par .= $key . '=' . urlencode($value);
                }
            }

            $url = $api_url . '?' . $par;

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_SSL_VERIFYPEER => false
            ));
            $resp = curl_exec($curl);
            if (!curl_exec($curl)) {
                die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
            }
            curl_close($curl);
            $result_array = json_decode($resp);

            if (@$result_array->error) {
                $return_arr['error'] = ($result_array->error->details == '') ? $result_array->error->reject_reason_description : $result_array->error->details;
                $return_arr['status'] = 'error';
                $return_arr['temp_id'] = '1';

                $eligibility_request = new EdiEligibility();
                $eligibility_request->edi_eligibility_created = $result_array->created_at;
                $eligibility_request->patient_id = $patient_id;
                $eligibility_request->error_message = ($result_array->error->details == '') ? $result_array->error->reject_reason_description : $result_array->error->details;
                //$eligibility_request->category = $category;	
                $eligibility_request->policy_id = $policy_id;
                $eligibility_request->temp_patient_id = $temp_patient_id;
                $eligibility_request->save();

                // Update primary insurance status in patient table.
                if ($category == 'Primary' && $patient_id != '') {
                    $patient = Patient::find($patient_id);
                    $patient->eligibility_verification = 'Error';
                    $patient->save();
                }

                $PatientInsurance = PatientInsurance::where('patient_id', '=', $patient_id)->where('insurance_id', $insurance_id)->where('policy_id', $policy_id)->update(['eligibility_verification' => 'Error']);
                return $return_arr;
            }
            else {
                if (@$result_array->eligible_id) {
                    $patient_eligibility = new PatientEligibility();
                    $patient_eligibility->patient_insurance_id = $insurance_id;
                    $patient_eligibility->patients_id = $patient_id;
                    $patient_eligibility->is_edi_atatched = 1;
                    $patient_eligibility->is_manual_atatched = 0;
                    $patient_eligibility->dos = $date;
                    $patient_eligibility->temp_patient_id = $temp_patient_id;
                    $patient_eligibility->created_by = Auth::user()->id;
                    $patient_eligibility->save();

                    $patient_eligibility_id = $patient_eligibility->id;
                    $eligibility_request = new EdiEligibility();
                    $eligibility_request->patient_eligibility_id = $patient_eligibility_id;
                    $eligibility_request->edi_eligibility_id = $result_array->eligible_id;
                    $eligibility_request->edi_eligibility_created = $result_array->created_at;
                    //$eligibility_request->category = $category;	
                    $eligibility_request->policy_id = $policy_id;
                    $eligibility_request->patient_id = $patient_id;
                    if (!empty($result_array->plan->coverage_status_label))
                        @$eligibility_request->error_message = $result_array->plan->coverage_status_label;
                    $eligibility_request->temp_patient_id = $temp_patient_id;
                    $eligibility_request->provider_id = $provider_id;
                    $eligibility_request->provider_npi = $elibility_arr['provider_npi'];
                    $eligibility_request->insurance_id = $insurance_id;
                    $eligibility_request->dos = $date;
                    $plan[] = $result_array->plan;
                    foreach (@$plan as $key => $plan) {
                        $eligibility_request->type = $plan->type;
                        $eligibility_request->plan_type = ($plan->plan_type_label == '') ? '-' : $plan->plan_type_label;
                        $eligibility_request->plan_number = ($plan->plan_number == '') ? '-' : $plan->plan_number;
                        $eligibility_request->plan_name = ($plan->plan_name == '') ? '-' : $plan->plan_name;
                        $eligibility_request->coverage_status = ($plan->coverage_status_label == '') ? '-' : $plan->coverage_status_label;
                        $eligibility_request->group_name = ($plan->group_name == '') ? '-' : $plan->group_name;

                        if (@$plan->dates[0]->date_type == 'plan_begin_begin' || @$plan->dates[0]->date_type == 'plan_begin')
                            $eligibility_request->plan_begin_date = @$plan->dates[0]->date_value;

                        if (@$plan->dates[1]->date_type == 'plan_begin_end')
                            $eligibility_request->plan_end_date = @$plan->dates[1]->date_value;
                    }
                    $eligibility_request->created_by = Auth::user()->id;
                    $eligibility_request->save();
                    $edi_eligibility_id = $eligibility_request->id;

                    $return_arr['error'] = '';
                    if ($result_array->plan->coverage_status == 1) {
                        if ($category == 'Primary' && $patient_id != '') {
                            $patient = Patient::find($patient_id);
                            $patient->eligibility_verification = 'Active';
                            $patient->save();
                        }
                        PatientInsurance::where('patient_id', '=', $patient_id)->where('insurance_id', $insurance_id)->where('policy_id', $policy_id)->update(['eligibility_verification' => 'Active']);
                    }
                    else {
                        if ($category == 'Primary' && $patient_id != '') {
                            $patient = Patient::find($patient_id);
                            $patient->eligibility_verification = 'Inactive';
                            $patient->save();
                        }
                        PatientInsurance::where('patient_id', '=', $patient_id)->where('insurance_id', $insurance_id)->where('policy_id', $policy_id)->update(['eligibility_verification' => 'Inactive']);
                        $return_arr['error'] = 'Inactive';
                    }

                    if ($result_array->demographics->subscriber)
                        $this->InsertDemoEligibility($result_array->demographics->subscriber, 'subscriber', $edi_eligibility_id);

                    $dependent = (array) @$result_array->demographics->dependent;

                    if (!empty($dependent)) {
                        $this->InsertDemoEligibility($result_array->demographics->dependent, 'dependent', $edi_eligibility_id);
                    }

                    /** * Eligibilty Insurance check function called start *** */
                    if (@$result_array->insurance)
                        $this->InsertInsurEligibility($result_array->insurance, $edi_eligibility_id, $insurance_id);
                    /** * Eligibilty Insurance check function end *** */
                }

                $view = View::make('patients/eligibility/eligibility_check_view', compact('result_array'));
                $contents = $view->render();

                if ($patient_id != '') {
                    $encodepatient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
                }
                else {
                    $encodepatient_id = Helpers::getEncodeAndDecodeOfId($temp_patient_id, 'encode');
                }

                $filename = 'eligible_' . time() . '.pdf';

                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($contents);
                $output_pdf = $pdf->stream();
                Helpers::mediauploadpath('', 'patienteligibility', $output_pdf, '', $filename, '', $encodepatient_id);

                if ($filename != '') {
                    $patient_eligibility = new PatientEligibility();
                    $patient_eligibility->edi_filename = $filename;
                    $patient_eligibility->save();
                    $return_arr['status'] = 'success';
                    $return_arr['error'] = '';
                    return $return_arr;
                }
                else {
                    $return_arr['error'] = 'PDF Not created';
                    $return_arr['status'] = 'error';
                    return $return_arr;
                }
            }
        }
        elseif ($eligibility_api->api_name == 'Pverify') {
            $provider_id = $elibility_arr['provider_id'];
            $patient_id = $elibility_arr['patient_id'];
            $insurance_id = $elibility_arr['insurance_id'];
            $date = $elibility_arr['date'];
            $policy_id = @$elibility_arr['member_id'];
            $category = $elibility_arr['category'];
            $eligibility_payerid = $elibility_arr['eligibility_payerid'];
            $token_url = $eligibility_api->token;
            $username = $eligibility_api->api_username;
            $password = $eligibility_api->api_password;
            $response_url = $eligibility_api->url;

            $return_arr['temp_id'] = "";
            $temp_patient_id = "";
            if ($patient_id == '') {
                $temp_patient_id = rand();
                $return_arr['temp_id'] = $temp_patient_id;
            }
            if ($patient_id != '') {
                $encodepatient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
            }
            else {
                $encodepatient_id = Helpers::getEncodeAndDecodeOfId($temp_patient_id, 'encode');
            }

            if ($provider_id > 0) {
                $provider_details = Provider::where('id', $provider_id)->first();
            }
            else {
                $provider_details = Provider::where('npi', '=', '1023098076')->where('status', 'Active')->first();
            }

            if (count((array)$provider_details) != '0') {
                $provider['npi'] = $provider_details->npi;
                $provider['lastName'] = $provider_details->last_name;
                $provider['middleName'] = '';
                $provider['firstName'] = $provider_details->first_name;
            }
            else {
                $provider['npi'] = '1023098076';
                $provider['lastName'] = 'Santucci';
                $provider['middleName'] = 'C';
                $provider['firstName'] = 'Denise';
            }

            $payer_code = $eligibility_payerid;
            $isSubscriberPatient = 'False';
            if (!empty($elibility_arr['dos_from']) && !empty($elibility_arr['dos_to'])) {
                $doS_StartDate = base64_decode($elibility_arr['dos_from']);
                $doS_EndDate = base64_decode($elibility_arr['dos_to']);
            }
            else {
                $doS_StartDate = date("m/d/Y");
                $doS_EndDate = date("m/d/Y");
            }
            $requestSource = 'RestAPI';

            $subscriber['firstName'] = $elibility_arr['member_first_name'];
            $subscriber['middleName'] = '';
            $subscriber['lastName'] = $elibility_arr['member_last_name'];
            $subscriber['memberID'] = $elibility_arr['member_id'];

            $dependent['patient']['firstName'] = $elibility_arr['member_first_name'];
            $dependent['patient']['middleName'] = '';
            $dependent['patient']['lastName'] = $elibility_arr['member_last_name'];
            $dependent['patient']['dob'] = date('m/d/Y', strtotime($elibility_arr['dob']));
            $dependent['patient']['gender'] = '';
            $dependent['relationWithSubscriber'] = '';

            $serviceCodes[] = '';
            $Posting_data = array("payerCode" => $payer_code,
                "provider" => $provider,
                "subscriber" => $subscriber,
                "dependent" => $dependent,
                "isSubscriberPatient" => $isSubscriberPatient,
                "doS_StartDate" => $doS_StartDate,
                "doS_EndDate" => $doS_EndDate,
                "serviceCodes" => $serviceCodes,
                "requestSource" => $requestSource
            );


            $result = $this->GetAuthTokenPverify($token_url, $username, $password, $response_url, $Posting_data);
            Log::info('pverify result - ' . json_encode($result));
            //echo "<pre>";print_r($result);die;
            if (empty($result['EDIErrorMessage']) && empty($result['ExceptionNotes'])) {
                DB::beginTransaction();
                try {

                    $patient_eligibility = new PatientEligibility();
                    $patient_eligibility->patient_insurance_id = $insurance_id;
                    $patient_eligibility->patients_id = $patient_id;
                    $patient_eligibility->is_edi_atatched = 0;
                    $patient_eligibility->is_manual_atatched = 0;
                    $patient_eligibility->dos_from = $date;
                    $patient_eligibility->temp_patient_id = $temp_patient_id;
                    $patient_eligibility->created_by = Auth::user()->id;
                    $patient_eligibility->save();



                    $patient_eligibility_id = $patient_eligibility->id;
                    $eligibility_request = new EdiEligibility();
                    $eligibility_request->patient_eligibility_id = $patient_eligibility_id;
                    $eligibility_request->edi_eligibility_id = @$result['ElgRequestID'];
                    $eligibility_request->edi_eligibility_created = @$result['EligibilityPeriod']['EffectiveFromDate'];
                    $eligibility_request->policy_id = $policy_id;
                    $eligibility_request->patient_id = $patient_id;
                    if (!empty($result['Plan']))
                        @$eligibility_request->error_message = $result['Plan'];
                    $eligibility_request->temp_patient_id = $temp_patient_id;
                    if (count((array)$provider_details) != '0') {
                        $eligibility_request->provider_id = $provider_details->id;
                        $eligibility_request->provider_npi = $provider_details->npi;
                    }
                    else {
                        $eligibility_request->provider_id = 0;
                        $eligibility_request->provider_npi = '1023098076';
                    }
                    $eligibility_request->insurance_id = $insurance_id;
                    $eligibility_request->dos = $date;

                    $eligibility_request->created_by = Auth::user()->id;
                    $eligibility_request->save();

                    if (@$result['Status'] == 'Active') {
                        if ($category == 'Primary' && $patient_id != '') {
                            $patient = Patient::find($patient_id);
                            $patient->eligibility_verification = 'Active';
                            $patient->save();
                        }
                        PatientInsurance::where('patient_id', '=', $patient_id)->where('insurance_id', $insurance_id)->where('policy_id', $policy_id)->update(['eligibility_verification' => 'Active']);
                    }
                    else {
                        if ($category == 'Primary' && $patient_id != '') {
                            $patient = Patient::find($patient_id);
                            $patient->eligibility_verification = 'Inactive';
                            $patient->save();
                        }
                        PatientInsurance::where('patient_id', '=', $patient_id)->where('insurance_id', $insurance_id)->where('policy_id', $policy_id)->update(['eligibility_verification' => 'Inactive']);
                        $return_arr['error'] = 'Inactive';
                    }

                    if (!empty($result['DemographicInfo']['Subscriber'])) {
                        $demo_sub_request = new EdiEligibilityDemo();
                        $demo_sub_request->edi_eligibility_id = $eligibility_request->id;
                        $demo_sub_request->demo_type = 'subscriber';
                        $demo_sub_request->gender = $result['DemographicInfo']['Subscriber']['Gender_R'];
                        $demo_sub_request->member_id = $elibility_arr['member_id'];
                        $demo_sub_request->first_name = $result['DemographicInfo']['Subscriber']['Firstname'];
                        $demo_sub_request->last_name = $result['DemographicInfo']['Subscriber']['Lastname_R'];
                        $demo_sub_request->middle_name = @$result['DemographicInfo']['Subscriber']['Middlename'];
                        $demo_sub_request->address1 = $result['DemographicInfo']['Subscriber']['Address1'];
                        $demo_sub_request->address2 = $result['DemographicInfo']['Subscriber']['Address2'];
                        $demo_sub_request->city = $result['DemographicInfo']['Subscriber']['City'];
                        $demo_sub_request->state = @$result['DemographicInfo']['Subscriber']['State'];
                        $zip5 = substr($result['DemographicInfo']['Subscriber']['Zip'], 0, 5);
                        $zip4 = substr($result['DemographicInfo']['Subscriber']['Zip'], 5, 4);
                        $demo_sub_request->zip5 = $zip5;
                        $demo_sub_request->zip4 = $zip4;

                        $demo_sub_request->save();
                    }

                    if (!empty($result['DemographicInfo']['Dependent']['DependentInfo'])) {
                        $demo_sub_request = new EdiEligibilityDemo();
                        $demo_sub_request->edi_eligibility_id = $eligibility_request->id;
                        $demo_sub_request->demo_type = 'dependent';
                        $demo_sub_request->gender = $result['DemographicInfo']['Dependent']['DependentInfo']['Gender_R'];
                        $demo_sub_request->member_id = $elibility_arr['member_id'];
                        $demo_sub_request->first_name = $result['DemographicInfo']['Dependent']['DependentInfo']['Firstname'];
                        $demo_sub_request->last_name = $result['DemographicInfo']['Dependent']['DependentInfo']['Lastname_R'];
                        $demo_sub_request->middle_name = @$result['DemographicInfo']['Dependent']['DependentInfo']['Middlename'];
                        $demo_sub_request->address1 = $result['DemographicInfo']['Dependent']['DependentInfo']['Address1'];
                        $demo_sub_request->address2 = $result['DemographicInfo']['Dependent']['DependentInfo']['Address2'];
                        $demo_sub_request->city = $result['DemographicInfo']['Dependent']['DependentInfo']['City'];
                        $demo_sub_request->state = $result['DemographicInfo']['Dependent']['DependentInfo']['State'];
                        $zip5 = substr($result['DemographicInfo']['Dependent']['DependentInfo']['Zip'], 0, 5);
                        $zip4 = substr($result['DemographicInfo']['Dependent']['DependentInfo']['Zip'], 5, 4);
                        $demo_sub_request->zip5 = $zip5;
                        $demo_sub_request->zip4 = $zip4;
                        $demo_sub_request->dob = date('Y-m-d', strtotime($result['DemographicInfo']['Dependent']['DependentInfo']['DOB_R']));
                        $demo_sub_request->relationship = @$result['DemographicInfo']['Dependent']['Relationship'];

                        $demo_sub_request->save();
                    }

                    /** * Eligibilty Insurance check function called start *** */
                    if (!empty($result['ServicesTypes'])) {
                        $insu_sub_request = new EdiEligibilityInsurance();
                        $insu_sub_request->edi_eligibility_id = $eligibility_request->id;
                        $insu_sub_request->insurance_id = $insurance_id;
                        $insu_sub_request->name = isset($result['ServicesTypes'][0]['ServiceTypeSections'][1]['ServiceParameters'][0]['Value']) ? $result['ServicesTypes'][0]['ServiceTypeSections'][1]['ServiceParameters'][0]['Value'] : '' ;
                        $insu_sub_request->payer_type = "Payer";
                        $insu_sub_request->save();


                        $edi_insurance_sub_request = new EdiEligibilityInsuranceSpPhysician();
                        $edi_insurance_sub_request->edi_eligibility_insurance_id = $insu_sub_request->id;
                        $edi_insurance_sub_request->eligibility_code = "None";
                        $edi_insurance_sub_request->insurance_type = '';
                        $edi_insurance_sub_request->save();

                        $edi_contact_request = new EdiEligibilityContact_detail();
                        $edi_contact_request->details_for_id = $edi_insurance_sub_request->id;
                        $edi_contact_request->last_name = isset($result['ServicesTypes'][0]['ServiceTypeSections'][1]['ServiceParameters'][0]['Value']) ? $result['ServicesTypes'][0]['ServiceTypeSections'][1]['ServiceParameters'][0]['Value'] : '';
						
                        $edi_contact_request->address1 = isset($result['ServicesTypes'][0]['ServiceTypeSections'][1]['ServiceParameters'][1]['Value']) ? $result['ServicesTypes'][0]['ServiceTypeSections'][1]['ServiceParameters'][1]['Value'] : '';
						
						if(isset($result['ServicesTypes'][0]['ServiceTypeSections'][1]['ServiceParameters'][2]['Value'])){
							$city_details = explode(',', $result['ServicesTypes'][0]['ServiceTypeSections'][1]['ServiceParameters'][2]['Value']);
							$edi_contact_request->city = (!empty($city_details[0])) ? $city_details[0] : '';
							$edi_contact_request->state = (!empty($city_details[1])) ? $city_details[1] : '';
							$edi_contact_request->zip5 = (!empty($city_details[2])) ? $city_details[2] : '';
						}
                        $edi_contact_request->save();
                    }
                    /*                     * * Eligibilty Insurance check function end *** */
                    if (!empty($result['ReportURL'])) {
                        $url = $result['ReportURL'];
                           $contextOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false
                            )
                        );
                        $content = file_get_contents($url, false, stream_context_create($contextOptions));
                        //$content = file_get_contents($url);
                        $content = nl2br($content);

                        $content = strip_tags($content, '<br>');
                        $content = str_replace("&nbsp;", " ", $content);
                        $content = explode("<br />", $content);
                        $sizes = array();
                        foreach ($content as $key => $list) {
                            $sizes[$key] = strlen($list);
                        }
                        $value_key = array_keys($sizes, max($sizes));
                        $content = $content[$value_key[0]];
                        $view = View::make('patients/patients/patient_pdf_generation', compact('content'));
                        $contents = $view->render();

                        if ($patient_id != '') {
                            $encodepatient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
                        }
                        else {
                            $encodepatient_id = Helpers::getEncodeAndDecodeOfId($temp_patient_id, 'encode');
                        }
                        if (App::environment() == 'production')
                            $path_medcubic = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
                        else
                            $path_medcubic = public_path() . '/';
                        $path = $path_medcubic . '/media/patienteligibility/';

                        // Creating folder for eligibility
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }


                        $filename = 'eligible_' . time() . '.pdf';
                        $path_with_filename = $path . $filename;
                        $output_pdf = PDF::loadHTML($view, 'A4')->filename($path_with_filename)->output();

                        // $pdf = App::make('dompdf.wrapper');
                        // $pdf->loadHTML($contents);
                        // $output_pdf = $pdf->stream();
                        // $pdf->save($path . $filename);
                        Helpers::mediauploadpath('', 'patienteligibility', $output_pdf, '', $filename, '', $encodepatient_id);


                        $PatientEligibility = PatientEligibility::find($patient_eligibility_id);
                        $PatientEligibility->is_edi_atatched = 1;
                        $PatientEligibility->edi_filename = $filename;
                        $PatientEligibility->edi_file_path = $path . $filename;
                        $PatientEligibility->update();
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    $return_arr['status'] = 'error';
                    if (!empty($result['Message'])) {
                        $return_arr['error'] = $result['Message'];
                    }
                    elseif (!empty($result['errors'][0])) {
                        $return_arr['error'] = $result['errors'][0];
                    }
                    else {
                        $trace = $e->getTrace();
                        Log::info('pverify error  <=---=> ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() . ' called from ' . $trace[0]['file'] . ' on line ' . $trace[0]['line']);

                        $return_arr['error'] = 'Please try again after sometime';
                    }
                    return $return_arr;
                }

                $return_arr['status'] = 'success';
                $return_arr['error'] = '';
                return $return_arr;
            }
            else {
                $return_arr['error'] = ($result['EDIErrorMessage'] == null ) ? $result['ExceptionNotes'] : $result['EDIErrorMessage'];
                $return_arr['status'] = 'error';
                return $return_arr;
            }
        }else if($eligibility_api->api_name == 'waystar'){
			$provider_id = $elibility_arr['provider_id'];
            $patient_id = $elibility_arr['patient_id'];
            $insurance_id = $elibility_arr['insurance_id'];
            $date = $elibility_arr['date'];
            $policy_id = @$elibility_arr['member_id'];
            $category = $elibility_arr['category'];
            $eligibility_payerid = $elibility_arr['eligibility_payerid'];
            $username = $eligibility_api->api_username;
            $password = $eligibility_api->api_password;
            $response_url = $eligibility_api->url;
			$dataString = "";
			$insuranceInfo = Insurance::where('id',$insurance_id)->first();
            $return_arr['temp_id'] = "";
            $temp_patient_id = "";
            if ($patient_id == '') {
                $temp_patient_id = rand();
                $return_arr['temp_id'] = $temp_patient_id;
            }
            if ($patient_id != '') {
                $encodepatient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
            }
            else {
                $encodepatient_id = Helpers::getEncodeAndDecodeOfId($temp_patient_id, 'encode');
            }

            if ($provider_id > 0) {
                $provider_details = Provider::where('id', $provider_id)->first();
            }
            else {
                $provider_details = Provider::where('npi', '=', '1730682394')->where('status', 'Active')->first();
            }
			
			// Provider Name in this pipe
            if (count((array)$provider_details) != '0') {
                $provider['npi'] = $provider_details->npi;
				$provider['tax_id'] = $provider_details->etin_type_number;
				if(!empty($provider_details->organization_name))
					$dataString .= $provider_details->organization_name."|";
				else
					$dataString .= $provider_details->last_name." ".$provider_details->first_name."|";
            }
            else {
                $provider['npi'] = '1730682394';
                $provider['tax_id'] = '473323470';
				$dataString .= "Rural Physicians Group, PC"."|";
            }
			// Insurance Name pipe 
			if(isset($elibility_arr['insurance_id'])){
				$dataString .= $insuranceInfo->insurance_name."|";
			}
			
			// Member Id 
			if(isset($policy_id)){
				$dataString .= $policy_id."|";
			}
			
			// Patient FirstName
			if(isset($elibility_arr['member_first_name'])){
				$dataString .= $elibility_arr['member_first_name']."|";
			}
			// Patient LastName
			if(isset($elibility_arr['member_last_name'])){
				$dataString .= $elibility_arr['member_last_name']."|";
			}
			
			//Patient DOB
			if(isset($elibility_arr['dob'])){
				$dataString .= date('m/d/Y', strtotime($elibility_arr['dob']))."|";
			}
			
			//Patient Insurance Type
			
			$dataString .= "S"."|";
			
			
			//Other Provider Name
			$dataString .= ""."|";
			
			//Date-of-Service/Range
			$dataString .= Date('m/d/Y')."|";
			
			//Service Type
			$dataString .= "30"."|";
			
			//Provider Inquiry Reference Number
			$dataString .= ""."|";
			
			//Payer ID
			$dataString .= $insuranceInfo->eligibility_payerid."|";
			//$dataString .= "66666"."|";
			
			//Account Break-out Value
			$dataString .= ""."|";
			
			//Simple File Format Version
			$dataString .= "1"."|";
			
			//Inquiring Provider Entity ID	
			$dataString .= "1P"."|";
			
			//Inquiring Provider First Name
			$dataString .= ""."|";
			
			//Inquiring Provider Middle Name	
			$dataString .= ""."|";
			
			//Inquiring Provider Tax ID
			$dataString .= $provider['tax_id']."|";
			
			//Inquiring Provider Payer-Assigned Provider Number
			$dataString .= ""."|";
			
			//Inquiring Provider NPI
			$dataString .= "HPI-".$provider['npi'];
			

            $result = $this->GetWaystarResponse($username, $password, $response_url, $dataString);
           
			$dataArrr['patient_id'] = $patient_id;
			$dataArrr['insurance_id'] = $insurance_id;
			$dataArrr['content'] = $result;
			$dataArrr['created_by'] = Auth::user()->id;
			PatientEligibilityWaystar::create($dataArrr);
			
			Patient::where('id',$patient_id)->update(['eligibility_verification'=>'Active']);
			PatientInsurance::where('patient_id',$patient_id)->where('insurance_id',$insurance_id)->update(['eligibility_verification'=>'Active']);
			$return_arr['status'] = 'success';
			$return_arr['error'] = '';
			return $return_arr;
           
			
		}
    }
	
	/*
     * This Function Used For Getting waystar eligibility response
     * Author		: Selvakumar.V
     * Created on	: 22Jan2020
     */
	public function GetWaystarResponse($username = '', $password = '', $response_url = '', $dataString = ''){	
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $response_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "UserID=" . $username . "&Password=" . $password . "&DataFormat=" . "SF1" . "&ResponseType=" . "HTML" . "&Data=" .$dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $responseInfo = curl_exec($ch);
        curl_close($ch);
        
		return $responseInfo;
	}
	
    /*
     * This Function Used For Getting Pverify Token
     * Author		: Selvakumar.V
     * Created on	: 04Oct2017
     */

    Public function GetAuthTokenPverify($token_url = '', $username = '', $password = '', $response_url = '', $Posting_data = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=" . $username . "&password=" . $password . "&grant_type=password");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $Auth_details = curl_exec($ch);

        curl_close($ch);
        $Auth_Arr_Resp = json_decode($Auth_details, True);
        $result = [];
        if (!empty($Auth_Arr_Resp['error'])) {
            $result['error_msg'] = $Auth_Arr_Resp['error'];
            $result['status'] = 'Error';
        }
        elseif (!empty($Auth_Arr_Resp['access_token'])) {
            $result['error_msg'] = '';
            $result['status'] = 'Success';
            $result['username'] = $username;
            $result['password'] = $password;
            $result['response_url'] = $response_url;
            $result['access_token'] = $Auth_Arr_Resp['access_token'];
        }
        else {
            $result['error_msg'] = 'Something went wrong';
            $result['status'] = 'Error';
        }
        return $this->CheckPverifyEligibility($result, $Posting_data);
    }

    /*
     * This Function Used For Patient Eligibility Check
     * Author		: Selvakumar.V
     * Created on	: 04Oct2017
     */

    public function CheckPverifyEligibility($data, $postData) {
        $postData = json_encode($postData);
        if ($data['status'] == 'Error') {
            $result['EDIErrorMessage'] = $data['error_msg'];
            $result['status'] = 'Error';
            return $result;
        }
        elseif ($data['status'] == 'Success') {
            //\Log::info('Pverify' . $postData);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $data['response_url']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $data['access_token'],
                'Client-User-Name:' . $data['username'],
                'Client-Password:' . $data['password'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData)
            ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $patient_details = curl_exec($ch);
            curl_close($ch);
           // \Log::info('After Pverify Response' . $patient_details);
            $patient_response = json_decode($patient_details, True);

            return $patient_response;
        }
    }

    public function InsertDemoEligibility($demo_request, $demo_type = 'subscriber', $edi_eligibility_id) {
        $demo_sub_request = new EdiEligibilityDemo();
        $demo_sub_request->edi_eligibility_id = $edi_eligibility_id;
        $demo_sub_request->demo_type = $demo_type;
        $demo_sub_request->gender = $demo_request->gender;
        $demo_sub_request->member_id = $demo_request->member_id;
        $demo_sub_request->first_name = $demo_request->first_name;
        $demo_sub_request->last_name = $demo_request->last_name;
        $demo_sub_request->middle_name = @$demo_request->middle_name;
        $demo_sub_request->group_id = @$demo_request->group_id;
        $demo_sub_request->group_name = @$demo_request->group_name;
        $demo_sub_request->address1 = $demo_request->address->street_line_1;
        $demo_sub_request->address2 = $demo_request->address->street_line_2;
        $demo_sub_request->city = $demo_request->address->city;
        $demo_sub_request->state = $demo_request->address->state;
        $zip5 = substr($demo_request->address->zip, 0, 5);
        $zip4 = substr($demo_request->address->zip, 5, 4);
        $demo_sub_request->zip5 = $zip5;
        $demo_sub_request->zip4 = $zip4;
        $demo_sub_request->dob = $demo_request->dob;

        if ($demo_type == 'dependent')
            $demo_sub_request->relationship = @$demo_request->relationship;

        $demo_sub_request->save();
    }

    /*     * * Eligibilty Insurance check function calling start *** */

    public function InsertInsurEligibility($insurance_request, $edi_eligibility_id, $insurance_id) {
        $insu_sub_request = new EdiEligibilityInsurance();
        $insu_sub_request->edi_eligibility_id = $edi_eligibility_id;
        $insu_sub_request->insurance_id = $insurance_id;
        $insu_sub_request->name = $insurance_request->name;
        $insu_sub_request->payer_type = $insurance_request->payer_type_label;
        $insu_sub_request->save();
        foreach (@$insurance_request->service_providers->physicians as $service_providers_value) {
            $edi_insurance_sub_request = new EdiEligibilityInsuranceSpPhysician();
            $edi_insurance_sub_request->edi_eligibility_insurance_id = $insu_sub_request->id;
            $edi_insurance_sub_request->eligibility_code = $service_providers_value->eligibility_code_label;
            $edi_insurance_sub_request->insurance_type = ($service_providers_value->insurance_type_label) ? $service_providers_value->insurance_type_label : '';
            $edi_insurance_sub_request->primary_care = $service_providers_value->primary_care;
            $edi_insurance_sub_request->restricted = $service_providers_value->restricted;
            $edi_insurance_sub_request->save();

            if (count((array)$service_providers_value->contact_details) > 0) {
                foreach (@$service_providers_value->contact_details as $key => $contact_details) {
                    $edi_contact_request = new EdiEligibilityContact_detail();
                    $edi_contact_request->details_for_id = $edi_insurance_sub_request->id;
                    $edi_contact_request->entity_code = $contact_details->entity_code_label;
                    $edi_contact_request->last_name = $contact_details->last_name;
                    $edi_contact_request->first_name = ($contact_details->first_name) ? $contact_details->first_name : '';
                    $edi_contact_request->identification_type = ($contact_details->identification_type) ? $contact_details->identification_type : '';
                    $edi_contact_request->identification_code = ($contact_details->identification_code) ? $contact_details->identification_code : '';
                    $edi_contact_request->address1 = ($contact_details->address->street_line_1) ? $contact_details->address->street_line_1 : '';
                    $edi_contact_request->address2 = ($contact_details->address->street_line_2) ? $contact_details->address->street_line_2 : '';
                    $edi_contact_request->city = ($contact_details->address->city) ? $contact_details->address->city : '';
                    $edi_contact_request->state = ($contact_details->address->state) ? $contact_details->address->state : '';
                    $edi_contact_request->zip5 = ($contact_details->address->zip) ? $contact_details->address->zip : '';
                    $edi_contact_request->save();
                }
            }
        }
    }

    /*     * * Eligibilty Insurance check function end *** */

    public function checkMedicareEligibility($elibility_arr) {
        $provider_id = $elibility_arr['provider_id'];
        $patient_id = $elibility_arr['patient_id'];
        $insurance_id = $elibility_arr['insurance_id'];
        $category = $elibility_arr['category'];
        $date = $elibility_arr['date'];
        //Get Api details
        $eligibility_api = ApiConfig::where('api_for', 'medicare_eligibility')->where('api_status', 'Active')->first();

        /*         * * create new array for creating api req format ** */
        $result = [];
        $result['payer_id'] = $elibility_arr['payer_id'];
        $result['member_id'] = $elibility_arr['member_id'];
        $result['member_last_name'] = $elibility_arr['member_last_name'];
        $result['member_first_name'] = $elibility_arr['member_first_name'];
        $result['member_dob'] = $elibility_arr['dob'];

        $return_arr['temp_id'] = $temp_patient_id = "";
        if ($patient_id == '') {
            $temp_patient_id = rand();
            $return_arr['temp_id'] = $temp_patient_id;
        }

        if ($provider_id > 0)
            $provider_details = Provider::where('id', $provider_id)->first();
        else
            $provider_details = Provider::where('first_name', '!=', '')->where('last_name', '!=', '')->where('status', 'Active')->first();

        if (count((array)$provider_details) > 0) {
            $result['provider_npi'] = $provider_details->npi;
            $result['provider_last_name'] = $provider_details->last_name;
            $result['provider_first_name'] = $provider_details->first_name;
        }
        $api_url = $eligibility_api->url;
        $result['api_key'] = $eligibility_api->usps_user_id;

        $par = 'test=true';
        /*         * * creating api req format starts** */
        foreach ($result as $key => $value) {
            if ($value != '') {
                if ($par != '')
                    $par .= '&';

                $par .= $key . '=' . urlencode($value);
            }
        }

        $url = $api_url . '?' . $par;
        /*         * * creating api req format ends ** */
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false
                )
        );
        $resp = curl_exec($curl); //response 
        if (!curl_exec($curl)) {
            die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        curl_close($curl);
        $get_response = json_decode($resp); //Decoded response
        if (isset($get_response->error)) {
            $return_arr['error'] = ($get_response->error->details == '') ? $get_response->error->reject_reason_description : $get_response->error->details;
            $return_arr['status'] = 'error';
            $return_arr['temp_id'] = '1';
            return $return_arr;
        }
        else {
            if ($get_response->eligible_id) {
                /*                 * * Patient eligibility records Insert starts here ** */
                $patient_eligibility = new PatientEligibility();
                $patient_eligibility->patient_insurance_id = $insurance_id;
                $patient_eligibility->patients_id = $patient_id;
                $patient_eligibility->is_edi_atatched = 1;
                $patient_eligibility->is_manual_atatched = 0;
                $patient_eligibility->dos = $date;
                $patient_eligibility->temp_patient_id = $temp_patient_id;
                $patient_eligibility->created_by = Auth::user()->id;
                $patient_eligibility->save();
                /*                 * * Patient eligibility records Insert ends here ** */

                /*                 * * Medicare eligibility records Insert starts here ** */
                if (count((array)$get_response->plan_details) > 0) {
                    foreach ($get_response->plan_details as $plan_name_key => $plan_name_value) {
                        $eli_medicare = $address = [];
                        $eli_medicare['plan_type'] = $plan_name_key;
                        $eli_medicare['plan_type_label'] = $get_response->plan_types->$plan_name_key;
                        foreach ($plan_name_value as $key => $value) {
                            if (is_array($value) && count((array)$value) > 0) {
                                if ($key == "contacts") {
                                    foreach ($value as $arr_key => $arr_value) {
                                        $add[$arr_value->contact_type] = (isset($arr_value->contact_value)) ? $arr_value->contact_value : '';
                                    }
                                    $address['contacts'] = $add;
                                }
                            }
                            else {
                                if (is_object($value)) {

                                    if ($key == "address") {
                                        $add['address1'] = (isset($value->street_line_1)) ? $value->street_line_1 : '';
                                        $add['address2'] = (isset($value->street_line_2)) ? $value->street_line_2 : '';
                                        $add['city'] = (isset($value->city)) ? $value->city : '';
                                        $add['state'] = (isset($value->state)) ? $value->state : '';
                                        $add['zip5'] = (isset($value->zip)) ? substr($value->zip, 0, 5) : '';
                                        $add['zip4'] = (isset($value->zip)) ? substr($value->zip, 5, 9) : '';
                                        $address['contacts'] = $add;
                                    }
                                }
                                else {
                                    $str_value = (isset($value)) ? $value : '';
                                    if ($key == "active")
                                        $eli_medicare[$key] = ($str_value === false) ? 'false' : 'true';
                                    else
                                        $eli_medicare[$key] = $str_value;
                                }
                            }
                        }
                        if (count((array)$address) > 0) {
                            $address['contacts']['details_for'] = "medicare";
                            // Medicare eligibility contact details Insert starts here 
                            $edi_con_detail = EdiEligibilityContact_detail::create($address['contacts']);
                            $eli_medicare['contact_details'] = $edi_con_detail->id;
                            $eli_medicare['contacts'] = $address['contacts'];
                        }

                        $all_detail[$plan_name_key] = $eli_medicare;
                        $insert_arr = $all_detail[$plan_name_key];
                        unset($insert_arr['contacts']);
                        // Medicare eligibility details Inserts here 
                        $edi_medicare_detail = EdiEligibilityMedicare::create($insert_arr);
                        $edi_medicare_detail->created_by = Auth::user()->id;
                        $edi_medicare_detail->save();
                        $medicare_id[] = $edi_medicare_detail->id;
                    }
                }
                /*                 * * Medicare eligibility records Insert ends here ** */

                /*                 * * Edi eligibility records Insert starts here ** */
                $patient_eligibility_id = $patient_eligibility->id;
                $eligibility_request = new EdiEligibility();
                $eligibility_request->patient_eligibility_id = $patient_eligibility_id;
                $eligibility_request->edi_eligibility_id = $get_response->eligible_id;
                $eligibility_request->edi_eligibility_created = $get_response->created_at;
                //	$eligibility_request->category = $category;				
                $eligibility_request->patient_id = $patient_id;
                $eligibility_request->error_message = (isset($get_response->known_issues)) ? 1 : 0; //Getting array
                $eligibility_request->temp_patient_id = $temp_patient_id;
                $eligibility_request->provider_id = $provider_id;
                $eligibility_request->provider_npi = $result['provider_npi'];
                $eligibility_request->insurance_id = $insurance_id;
                $eligibility_request->dos = $date;
                $eligibility_request->plan_number = $get_response->plan_number;
                $eligibility_request->plan_type = implode(',', $medicare_id);
                $eligibility_request->insurance_type = "Medicare";
                $eligibility_request->created_by = Auth::user()->id;
                $eligibility_request->save();
                /*                 * * Edi eligibility records Insert ends here ** */
                /*                 * * General detail Insert starts here ** */
                $edi_eligibility_id = $eligibility_request->id;
                $demo_sub_request = new EdiEligibilityDemo();
                $demo_sub_request->edi_eligibility_id = $edi_eligibility_id;
                $demo_sub_request->demo_type = 'subscriber';
                $demo_sub_request->gender = (isset($get_response->gender)) ? $get_response->gender : '';
                $demo_sub_request->member_id = (isset($get_response->member_id)) ? $get_response->member_id : '';
                $demo_sub_request->first_name = (isset($get_response->first_name)) ? $get_response->first_name : '';
                $demo_sub_request->last_name = (isset($get_response->last_name)) ? $get_response->last_name : '';
                $demo_sub_request->group_id = (isset($get_response->group_id)) ? $get_response->group_id : '';
                $demo_sub_request->group_name = (isset($get_response->group_name)) ? $get_response->group_name : '';
                $demo_sub_request->address1 = (isset($get_response->address->street_line_1)) ? $get_response->address->street_line_1 : '';
                $demo_sub_request->address2 = (isset($get_response->address->street_line_2)) ? $get_response->address->street_line_2 : '';
                $demo_sub_request->city = (isset($get_response->address->city)) ? $get_response->address->city : '';
                $demo_sub_request->state = (isset($get_response->address->state)) ? $get_response->address->state : '';
                $zip5 = (isset($get_response->address->zip)) ? substr($get_response->address->zip, 0, 5) : '';
                $zip4 = (isset($get_response->address->zip)) ? substr($get_response->address->zip, 5, 4) : '';
                $demo_sub_request->zip5 = $zip5;
                $demo_sub_request->zip4 = $zip4;
                $demo_sub_request->dob = (isset($get_response->dob)) ? $get_response->dob : '';
                $demo_sub_request->save();
                $contact_id = $demo_sub_request->id;
                /*                 * * General detail Insert ends here ** */

                // Update Edi Eligibility contact detail Inserts
                EdiEligibility::where('patient_eligibility_id', $patient_eligibility_id)->where('id', $edi_eligibility_id)->update(['contact_detail' => $contact_id]);

                $return_arr['error'] = '';
                if (isset($get_response->known_issues) && count((array)$get_response->known_issues) > 0) {
                    if ($category == 'Primary' && $patient_id != '') {
                        $patient = Patient::find($patient_id);
                        $patient->eligibility_verification = 'Inactive';
                        $patient->save();
                    }
                    // Update Patient Insurance status
                    PatientInsurance::where('patient_id', '=', $patient_id)->where('category', '=', $category)->update(['eligibility_verification' => 'Inactive']);
                    $return_arr['error'] = 'Inactive';
                }
                else {
                    if ($category == 'Primary' && $patient_id != '') {
                        $patient = Patient::find($patient_id);
                        $patient->eligibility_verification = 'Active';
                        $patient->save();
                    }
                    // Update Patient Insurance status
                    PatientInsurance::where('patient_id', '=', $patient_id)->where('category', '=', $category)->update(['eligibility_verification' => 'Active']);
                }
                /*                 * * Making PDF file contents starts here ** */
                $view = View::make('patients/eligibility/medicare_eligibility_check_view', compact('all_detail', 'get_response'));
                $contents = $view->render();

                if ($patient_id != '') {
                    $encodepatient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
                }
                else {
                    $encodepatient_id = Helpers::getEncodeAndDecodeOfId($temp_patient_id, 'encode');
                }

                $filename = 'eligible_' . time() . '.pdf';

                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($contents);

                $output_pdf = $pdf->stream();
                Helpers::mediauploadpath('', 'patienteligibility', $output_pdf, '', $filename, '', $encodepatient_id);

                if ($filename != '') {
                    $patient_eligibility->edi_filename = $filename;
                    $patient_eligibility->save();
                    $return_arr['status'] = 'success';
                    return $return_arr;
                }
                else {
                    $return_arr['error'] = 'PDF Not created';
                    $return_arr['status'] = 'error';
                    return $return_arr;
                }
                /*                 * * Making PDF file contents ends here ** */
            }
        }
    }

    public static function connectSmsApi($msg, $phone_num) {
        self::setSmsCretential("twilio_sms");
        Twilio::sms('+918608331760', $msg);
        self::clearSmsCretential();
        return "Success";
    }

    public static function connectPhoneApi() {
        self::setSmsCretential("twilio_sms");
        //Twilio::call('+918608331760',"http://demo.twilio.com/docs/voice.xml");
        self::clearSmsCretential();
        return "Success";
        //Twilio::call('+918608331760',"http://demo.twilio.com/docs/voice.xml");
        //Twilio::call('+918608331760',"http://demo.twilio.com/docs/voice.xml", array("Method" => "GET","StatusCallback" => "https://www.myapp.com/events","StatusCallbackMethod" => "POST","StatusCallbackEvent" => array("initiated", "ringing", "answered", "completed"),));
    }

    public static function connectEmailApi($request) {
        return CommonMailApiController::common_send_mail($request);
    }

    public static function setSmsCretential($sms_api) {
        $sms_api = ApiConfig::where('api_for', $sms_api)->where('api_status', 'Active')->first();
        Config::set('twilio.connections.twilio.sid', $sms_api['usps_user_id']);
        Config::set('twilio.connections.twilio.token', $sms_api['token']);
        $from_num = "+" . $sms_api['host'];
        Config::set('twilio.connections.twilio.from', $from_num);
    }

    public function searchBasisExport($export_type) {
        $request = Request::All();
        $get_view_function = $request['view_file'];
        $result_arr = $this->getResult($request);
        $get_function_value = $result_arr["response"];
        $result['value'] = $get_function_value->get();
        $result['export_type'] = $export_type;
        $get_view_function = explode("@", $get_view_function);
        $view_function = new $get_view_function[0];
        //get_parent_class($view_function)
        $extn_fn = "getClaimListApi";
        $exportparam = $view_function->$extn_fn($result); //Get listing function
        //$file 			= file_get_contents($get_view_function[0],'r');


        $exportparam = array(
            'filename' => 'employers',
            'heading' => '',
            'fields' => array(
                'employer_name' => 'Employer Name',
                'employer_phone' => 'Employer Phone',
                'employer_fax' => 'Employer Fax',
                'contact_person' => 'Contact Person',
                'designation' => 'Designation',
                'contact_email' => 'Contact Email',
            )
        );
        $callexport = new CommonExportApiController();
        return $callexport->generatemultipleExports($exportparam, $result, $export_type);
    }

    public function advancedTableSearch(Request $request) {
        #### Getting variables from response ####
        $request = Request::All();
        $result_arr = $this->getResult($request);
        $get_function_value = $result_arr["response"];
        $table_content = $result_arr["content"];
        $view_path = $result_arr["view_path"];
        $result = $get_function_value->get();
        $result_get_id = $get_function_value->pluck('id')->all();
        $table_content = '<input type="hidden" class="js_get_id" value="' . implode(",", $result_get_id) . '" />' . $table_content;
        file_put_contents($view_path, '');
        file_put_contents($view_path, $table_content);
        return view('layouts/search_table_content', compact('result', 'variable_name'));
    }

    public function getResult($request) {
        #### Getting variables from response ####
        $keyword = str_replace(' ', '', $request['filter_search_keyword']);
        $use_function = $request['current_action'];
        $listing_id = $request['listing_id'];
        $get_id = ($listing_id == "all" || $listing_id == "") ? $listing_id : explode(",", $listing_id);
        $variable_name = $request['variable_name'];
        $with_param = $request['with_table'];
        //$with_param_list 	= implode(",",(explode(",",$with_param)));
        $with_param_list = explode(",", $with_param);
        $get_view_function = $request['view_file'];

        #### Convert variables from response ####
        $get_view_function = explode("@", $get_view_function);
        $view_function = new $get_view_function[0];
        $view_name = $view_function->$get_view_function[1](); //Get listing function
        $view_file = $view_name->getPath();
        $result_list = $view_name->getData()[$variable_name];

        /* $get_relation_table = $this->getRelationName($result_list);
          //eval('$b = ' . var_export($view_name, true) . ';');
          $root_path = $_SERVER['CONTEXT_DOCUMENT_ROOT'];
          $get_root = substr($root_path, 0, strrpos( $root_path, '\\'));
          $file 			= file_get_contents($get_root."/".$get_view_function[0].".php");
          $split_function 	= explode("<table",$file);
          $table 			= explode("</table>",$split_table[1]);
          dd($file); */
        #### Content getting and reset from current path ####
        $contextOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false
                            )
                        );
        $file = file_get_contents($view_file, false, stream_context_create($contextOptions));
        //$file = file_get_contents($view_file);
        $split_table = explode("<table", $file);
        $table = explode("</table>", $split_table[1]);
        $table_text = str_replace("@foreach($" . $variable_name, "@foreach($" . "result", $table[0]);
        $table_content = "<table " . $table_text . "</table>";
        $view_path = explode("views/", $view_file);
        $view_path = $view_path[0] . "views/layouts/search_table_content.blade.php";

        #### Getting response from current controller ####
        $use_function_name = new $use_function;
        $get_function_value = (count((array)$with_param_list) > 0) ? $use_function::with($with_param_list)->orderBy("id", "DESC") : $use_function::orderBy("id", "DESC");
        $get_all_column = $get_function_value->get();
        // Used for searching keys
        $get_column_list = (count((array)$get_all_column) > 0) ? array_keys($get_all_column[0]->getOriginal()) : array_keys($get_all_column->getOriginal());
        $count = 1;
        //dd($get_column_list);
        #### User Basis List ####
        if (isset($request['user_id'])) {
            $get_function_value->where(function($query)use($user_id) {
                return $query->whereIn('updated_by', $user_id)->orWhereIn('updated_by', $user_id);
            });
        }
        if ($keyword != '') {
            if (isset($request['column_name'])) {
                $search_column_list = $request['column_name'];
                foreach ($search_column_list as $search_key => $search_value) {
                    $get_function_value = $this->ColumnBasedList($get_function_value, $get_column_list, $get_id, $count, $keyword, $search_value);
                    $count++;
                }
            }
            else {
                foreach ($get_column_list as $column_key => $column_value) {
                    if ($column_value != "id" || $column_value != "created_at" || $column_value != "updated_at" || $column_value != "deleted_at") {
                        $get_function_value = $this->ConditionBasedList($get_function_value, $get_id, $count, $column_value, $keyword);
                        $count++;
                    }
                }
            }
        }
        $result["response"] = $get_function_value;
        $result["content"] = $table_content;
        $result["view_path"] = $view_path;
        return $result;
    }

    /*     * * Column Based search function start here ** */

    public function ColumnBasedList($get_function_value, $get_column_list, $get_id, $count, $keyword, $value) {
        foreach ($get_column_list as $column_key => $column_value) {
            if ($value == "address") {
                if ((strpos($column_value, 'address') !== false) || (strpos($column_value, 'city') !== false) || (strpos($column_value, 'state') !== false) || (strpos($column_value, 'zip') !== false)) {
                    $get_function_value = $this->ConditionBasedList($get_function_value, $get_id, $count, $column_value, $keyword);
                }
            }
            elseif ($value == "contact_number") {
                if ((strpos($column_value, 'ext') !== false) || (strpos($column_value, 'phone') !== false) || (strpos($column_value, 'fax') !== false)) {
                    $get_function_value = $this->ConditionBasedList($get_function_value, $get_id, $count, $column_value, $keyword);
                }
            }
            elseif ($value == $column_value) {
                $get_function_value = $this->ConditionBasedList($get_function_value, $get_id, $count, $column_value, $keyword);
            }
        }
        return $get_function_value;
    }

    /*     * * Column Based search function end here ** */

    /*     * * Conditional Based search function start here ** */

    public function ConditionBasedList($get_function_value, $get_id, $count, $column_value, $keyword) {
        if ($count == 1) {
            $get_function_value = ($get_id == "all" || $get_id == "") ? $get_function_value->where($column_value, 'like', '%' . $keyword . '%') : $get_function_value->whereIn('id', $get_id)->where($column_value, 'like', '%' . $keyword . '%');
        }
        else {
            $get_function_value = ($get_id == "all" || $get_id == "") ? $get_function_value->orWhere($column_value, 'like', '%' . $keyword . '%') : $get_function_value->whereIn('id', $get_id)->orWhere($column_value, 'like', '%' . $keyword . '%');
        }
        return $get_function_value;
    }

    /*     * * Conditional Based search function end here ** */

    /*     * * Relation Based search function start here *** /
      public function RelationBasedList($get_function_value,$get_id,$count,$column_value,$keyword)
      {
      if($count==1)
      {
      $get_function_value =  ($get_id =="all" || $get_id =="") ? $get_function_value->where($column_value, 'like', '%'.$keyword.'%') : $get_function_value->whereIn('id', $get_id)->where($column_value, 'like', '%'.$keyword.'%');
      }
      else
      {
      $get_function_value = ($get_id =="all" || $get_id =="") ? $get_function_value->orWhere($column_value, 'like', '%'.$keyword.'%') : $get_function_value->whereIn('id', $get_id)->orWhere($column_value, 'like', '%'.$keyword.'%');
      }
      return $get_function_value;
      }
      /*** Relation Based search function end here ** */

    /*     * * Conditional Based search function start here ** */

    public function getRelationName($result) {
        if (is_array($result) || is_object($result) || count((array)$result) > 0) {
            $column_name = [];
            foreach ($result as $column_key => $column_value) {
                foreach ($result as $column_key => $column_value) {
                    if (is_array($column_value)) {
                        $column_name[] = $column_value;
                    }
                    elseif (is_object($column_value)) {
                        $column_name[] = $column_value;
                    }
                }
            }
            return $column_name;
        }
        /*         * * Conditional Based search function end here ** */
    }
    // Adjustment reverse entry validation
    //Author: baskar
    public function adjustment_validation(Request $request)
    {
        $request = Request::All();
        try{
            $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
            $insurance_id = @$request['insurance_id'];
            $cpt_code = isset($request['cpt']) ? $request['cpt'] : '';
            $cpt_id = DB::table('claim_cpt_info_v1')->where('claim_id',$claim_id)->where('cpt_code',$cpt_code)->pluck('id')->first();
            if($request['name']=='adjustment[0]'){
                $amt = DB::table('pmt_claim_tx_v1 AS cl_tx')->selectRaw('cpt.writeoff')->join('pmt_claim_cpt_tx_v1 AS cpt','cpt.claim_id','=','cl_tx.claim_id')->where('cl_tx.payer_insurance_id',$insurance_id)->where('cpt.claim_id',$claim_id)->where('cl_tx.claim_id',$claim_id)->where('cpt.claim_cpt_info_id',$cpt_id)->groupBy('cpt.id')->get();
            }
            else{
                if($request['val']=='all')
                    $amt = DB::table('claim_cpt_others_adjustment_info_v1 AS other')->selectRaw('other.adjustment_amt')->join('pmt_claim_tx_v1 AS cl_tx','cl_tx.claim_id','=','other.claim_id')->where('cl_tx.payer_insurance_id',$insurance_id)->where('cl_tx.claim_id',$claim_id)->where('other.claim_id',$claim_id)->where('other.claim_cpt_id',$cpt_id)->groupBy('other.id')->get();
                else
                    $amt = DB::table('claim_cpt_others_adjustment_info_v1 AS other')->selectRaw('other.adjustment_amt')->join('pmt_claim_tx_v1 AS cl_tx','cl_tx.claim_id','=','other.claim_id')->where('cl_tx.payer_insurance_id',$insurance_id)->where('cl_tx.claim_id',$claim_id)->where('other.adjustment_id',$request['val'])->where('other.claim_id',$claim_id)->where('other.claim_cpt_id',$cpt_id)->groupBy('other.id')->get();
            }
            $amt = array_sum(array_flatten(json_decode(json_encode($amt), true)));
            $tot_amt = $amt+$request['amt'];
            return $tot_amt;
            //Log::info("total_charge".$total_charge.'-tot_days'.$tot_days.'-total_ar'.$total_ar);

        } catch (\Exception $e) {
            \Log::info("Error occured while adjustment validation".$e->getMessage() );
        }  
    }

    // Get last used of ICD for patient
    //Author: baskar
    public function lastIcd(Request $request)
    {
        $request = Request::All();
        try{
            // Decode to patient
            $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
            // Get icd ids from claim_info_v1 table
            $icd_ids = DB::table('claim_info_v1')->where('patient_id',@$patient_id)->orderBy('id','desc')->limit(1)->pluck('icd_codes')->first();            
            // Get icd codes from icd_10 table
            if(!empty($icd_ids))
                $icd_codes = DB::table('icd_10')->whereIn('id',explode(',',@$icd_ids))->select('icd_code')->orderByRaw('FIELD(id, '.@$icd_ids.')')->get();
           else  
                $icd_codes = '';
            return @$icd_codes;
        } catch (\Exception $e) {
            \Log::info("Error occured while last used of icd for patient".$e->getMessage() );
        } 
    }
    // Security code check before go to practice
    //Author: baskar
    public function security_code_generate($id){
        $request = Request::all();
        $error_msg = Lang::get("common.validation.security");
        $ip_lat_longitude = Helpers::GetIpAndLatAndLongitude();
        $digits = 4;
        $security_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
        $security_code_attempt = 1;
        $dataArr['user_id'] = Auth::id();
        $ipAddress = $ip_lat_longitude['ipaddress'];
        $dataArr['ip_address'] = $ipAddress;
        $dataArr['security_code'] = $security_code;
        $dataArr['security_code_attempt'] = $security_code_attempt;
        /* Get browser and device name - Anjukaselvan*/
        
        $browserAndDeviceName = Helpers::getBrowserAndDeviceName();
        $browser = $browserAndDeviceName['browser_name'];
        $device = $browserAndDeviceName['device_name'];
        $dataArr['browser_name'] = $browser;
        $dataArr['device_name'] = $device;
        $userip = UserIp::create($dataArr);

        // Start  Mail send for notification of security code
        $customers = Customer::where('id',$request['customer_id'])->first();
        $practice = Helpers::getPracticeNames('',Auth::id());                            
        $message = trans("email/email.security_email");         
        $oldPhrase = ["VAR_CUSTOMER_USER", "VAR_PRACTICE_NAME", "VAR_USER_NAME", "VAR_USER_EMAIL", "VAR_LOGIN_ATTEMPT", "VAR_IP_ADDRESS", "VAR_DATE", "VAR_SECURITY_CODE","VAR_SITE_NAME"];
        $newPhrase   = [Auth::user()->name,$practice,Auth::user()->name,Auth::user()->email, $security_code_attempt,$ipAddress,date("Y-m-d H:i:s"),$security_code, "Medcubics"];
        $newMessage = str_replace($oldPhrase, $newPhrase, $message);
        $Subject = "Security Code Notification";                    
        $deta = array('name'=> Auth::user()->name,'email'=> Auth::user()->email,'subject'=> $Subject,'msg' => $newMessage,'attachment'=>'');
        
        Helpers::sendMail($deta);                   
        // End Mail send for notification of security code
        if($userip && $customers)
            return 'Yes';
        else
            return "No";
    }

    public function setPractice()
    {
        $request = Request::all();
        $security_code_count = UserIp::where('security_code',$request['security_code'])->where('user_id',Auth::id())->where('approved','No')->orderBy('created_at','desc')->first();
        if(!empty($security_code_count)){
            UserIp::where('id',$security_code_count['id'])->update(['approved'=>'Yes','first_login'=>date("Y-m-d H:i:s")]);
            $api = new CustomerApiController();
            $api_response       = $api->setPracticeApi($request['practice_id']);
            $api_response_data  = $api_response->getData();
            Session::put('timezone',Practice::where('id',base64_decode($request['practice_id']))->value('timezone'));
            return 'Yes';
        }else{
            return 'No';
        }
    }

}
