<?php

namespace App\Http\Controllers\Patients;

use Auth;
use View;
use Input;
use Session;
use Request;
use Redirect;
use Validator;
use App\Http\Controllers\Api\PatientInsuranceApiController as PatientInsuranceApiController;
use App\Models\Patient;

class PatientInsuranceController extends Api\PatientInsuranceApiController {

    public function __construct() {
        View::share('heading', 'Patient');
        View::share('selected_tab', 'patients');
        View::share('heading_icon', 'users');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        $api_response = $this->getCreateApi();
        $api_response_data = $api_response->getData();

        $providers = $api_response_data->data->providers;
        $rendering_providers = $api_response_data->data->rendering_providers;
        $referring_providers = $api_response_data->data->referring_providers;
        $patients = $api_response_data->data->patients;
        $facilities = $api_response_data->data->facilities;
        $insurances = $api_response_data->data->insurances;
        $patient_language = $api_response_data->data->patient_language;
        $ethnicity = $api_response_data->data->ethnicity;
        $country = $api_response_data->data->country;
        // dd($country);
        $religion = $api_response_data->data->religion;
        $state = $api_response_data->data->state;


        $facility_id = '';
        $insurance_id = '';
        $provider_id = '';
        $rendering_provider_id = '';
        $referring_provider_id = '';
        $language_id = '';
        $ethnicity_id = '';
        $country_id = '';
        $religion_id = '';
        $state_id = '';

      
        return view('patients/patients/create', compact('patients', 'facilities', 'insurances', 'facility_id', 'providers', 'provider_id', 'insurance_id', 'rendering_providers', 'rendering_provider_id', 'referring_providers', 'referring_provider_id', 'patient_language', 'language_id', 'ethnicity', 'ethnicity_id', 'country', 'country_id', 'religion', 'religion_id', 'state', 'state_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($id, Request $request) {
        $api_response = $this->getStoreApi($request::all());

        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            
            return Redirect::to('patients/' . $id . '/create#insurance-info')->with('success', $api_response_data->message);
        } else {
            
            return Redirect::to('patients/create')->withInput()->withErrors($api_response_data->message);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {

        $api_response = $this->getShowApi($id);
        $api_response_data = $api_response->getData();
        $patients = $api_response_data->data->patients;

        $language = $api_response_data->data->language;
        $language_id = $api_response_data->data->language_id;
        $ethnicity = $api_response_data->data->ethnicity;
        $ethnicity_id = $api_response_data->data->ethnicity_id;
        $country = $api_response_data->data->country;
        $country_id = $api_response_data->data->country_id;
        $religion = $api_response_data->data->religion;
        $religion_id = $api_response_data->data->religion_id;
        $facility = $api_response_data->data->facility;
        $facility_id = $api_response_data->data->facility_id;
        $insurance = $api_response_data->data->insurance;
        $insurance_id = $api_response_data->data->insurance_id;

        $insuranceclass = $api_response_data->data->insuranceclass;
        $insuranceclass_id = $api_response_data->data->insuranceclass_id;

        $insurancetype = $api_response_data->data->insurancetype;
        $insurancetype_id = $api_response_data->data->insurancetype_id;

        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['general'] = (array) $address_flags['general'];

        return view('patients/patients/show', compact('patients', 'address_flag', 'language', 'language_id', 'ethnicity', 'ethnicity_id', 'country', 'country_id', 'religion', 'religion_id', 'facility', 'facility_id', 'insurance', 'insurance_id', 'insuranceclass', 'insuranceclass_id', 'insurancetype', 'insurancetype_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {

        $api_response = $this->getEditApi($id);
        $api_response_data = $api_response->getData();

        $providers = $api_response_data->data->providers;
        $rendering_providers = $api_response_data->data->rendering_providers;
        $referring_providers = $api_response_data->data->referring_providers;
        $patients = $api_response_data->data->patients;

        $facilities = $api_response_data->data->facilities;
        $insurances = $api_response_data->data->insurances;
        $insuranceclass = $api_response_data->data->insuranceclass;
        $insurancetype = $api_response_data->data->insurancetype;
        $patient_language = $api_response_data->data->patient_language;
        $ethnicity = $api_response_data->data->ethnicity;
        $country = $api_response_data->data->country;

        $religion = $api_response_data->data->religion;
        $pos = $api_response_data->data->pos;
        $pos_id = $api_response_data->data->pos_id;

        $facility_id = $patients->facility_id;
        $insurance_id = $patients->insurance_id;
        $insurance_class_id = $patients->insurance_class_id;
        $insurance_type_id = $patients->insurance_type_id;
        $provider_id = $patients->provider_id;
        $rendering_provider_id = $patients->rendering_provider_id;
        $referring_provider_id = $patients->referring_provider_id;
        $language_id = $patients->language_id;
        $ethnicity_id = $patients->ethnicity_id;
        $country_id = $patients->country_id;
        $religion_id = $patients->religion_id;
        $state = $api_response_data->data->state;
        $state_id = $patients->state_id;
        $zipcode = $api_response_data->data->zipcode;
        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['general'] = (array) $address_flags['general'];



        return view('patients/patients/edit', compact('patients', 'facilities', 'address_flag', 'facility_id', 'insurances', 'insurance_id', 'insuranceclass', 'insurancetype', 'insurance_class_id', 'insurance_type_id', 'providers', 'provider_id', 'rendering_providers', 'rendering_provider_id', 'referring_providers', 'referring_provider_id', 'patient_language', 'language_id', 'ethnicity', 'ethnicity_id', 'country', 'country_id', 'religion', 'religion_id', 'zipcode', 'address_flag', 'state', 'state_id', 'pos_id', 'pos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $api_response = $this->getUpdateApi($id);
        $api_response_data = $api_response->getData();

        if ($api_response_data->status == 'success') {
            return Redirect::to('patients/' . $id)->with('success', $api_response_data->message);
        } else {
            return redirect()->back()->withInput()->withErrors($api_response_data->message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $api_response = $this->getDeleteApi($id);
        $api_response_data = $api_response->getData();

        if ($api_response_data->status == 'success') {
            return Redirect::to('patient')->with('success', $api_response_data->message);
        } else {
            return redirect()->back()->with('error', $api_response_data->message);
        }
    }

    public function patientsprofile($id) {
        $api_response = $this->getPatientsprofile($id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            echo "success~~" . Request::input('next_tab_name');
            exit;
        } else {
            echo "error";
            exit;
        }
    }

}
