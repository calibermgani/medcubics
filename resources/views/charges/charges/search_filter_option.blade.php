{!! Form::open(['url'=>'charges/search','id'=>'js_common_search_form','name'=>'search_form', 'data_divid' => 'search_charge']) !!}
<?php 
    $value =  App\Models\Payments\ClaimInfoV1::getSearchCriteria();
    $response = $value->getData();
    $rendering_provider = $response->rendering_providers;
    $referring_provider = $response->referring_providers;
    $billing_provider = $response->billing_providers;
    $facility = $response->facilities;
    $insurances = $response->insurance;
    $patients = $response->patients;
    $claimnumber = $response->claimnumber;
?>
<div id="js_claim_search_option">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">
        <div class="no-shadow form-horizontal">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="form-group">
                    {!! Form::label('Billing', 'Billing',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-10 select2-white-popup">
                        {!! Form::select('billing_provider_id', array(''=>'-- Select --')+(array)$billing_provider, null,['class'=>'form-control input-view-border1 select2','id'=>'billing_provider_id']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('Rendering', 'Rendering',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-10 select2-white-popup">
                        {!! Form::select('rendering_provider_id', array(''=>'-- Select --')+(array)$rendering_provider, null,['class'=>'form-control input-view-border1 select2','id'=>'rendering_provider_id']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('Referring', 'Referring',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-10 select2-white-popup">
                        {!! Form::select('referring_provider_id', array(''=>'-- Select --')+(array)$referring_provider, null,['class'=>'form-control input-view-border1 select2','id'=>'referring_provider_id']) !!}
                    </div>
                </div>
                            
                <div class="form-group">
                    {!! Form::label('Billed To', 'Billed To',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-10 select2-white-popup">
                        {!! Form::select('insurance_id', array(''=>'-- Select --')+(array)$insurances, null,['class'=>'form-control input-view-border1 select2','id'=>'insurance_id']) !!}
                    </div>
                </div>
                
                <div class="form-group">
                    {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-10 select2-white-popup">
                        {!! Form::select('facility_id', array(''=>'-- Select --')+(array)$facility,  null,['class'=>'form-control input-view-border1 select2','id'=>'facility_id']) !!}
                    </div>
                </div>
                  <div class="form-group">
                    {!! Form::label('Claim No', 'Claim No', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-10 select2-white-popup">
                        {!! Form::select('claim_number', array(''=>'-- Select --')+(array)$claimnumber,  null,['class'=>'form-control input-view-border1 select2','id'=>'facility_id']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('Patient', 'Patient', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-10 select2-white-popup">
                        {!! Form::select('patient_id', array(''=>'-- Select --')+(array)$patients,  null,['class'=>'form-control input-view-border1 select2','id'=>'patient_id']) !!}
                    </div>
                </div>
                   
                 <div class="form-group js-select-div" id = "js-select-main">
                    {!! Form::label('Billed Amount', 'Billed Amount',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-4 col-md-3 col-sm-3 col-xs-10 select2-white-popup">
                        {!! Form::select('billed_option', array(''=>'-- Select --','lessthan'=>'<','lessequal'=>'<=','equal'=>'=','greaterthan'=>'>','greaterequal'=>'>='),  null,['class'=>'form-control input-view-border1 select2 js_main_select','id'=>'billed_option']) !!}
                    </div>               
                    <div class="col-lg-4 col-md-3 col-sm-3 col-xs-10">
                        {!! Form::text('billed',null,['maxlength'=>'26','class'=>'form-control dm-copay-amount input-sm-header-billing js_sub_select','placeholder'=>'Amount', 'id'=>'billed']) !!}
                    </div>
                </div>
                <div class="form-group js-select-div" id = "js-select-main-balance">
                    {!! Form::label('Balance Amount', 'Balance Amount',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label p-r-0']) !!} 
                    <div class="col-lg-4 col-md-3 col-sm-3 col-xs-10 select2-white-popup">
                        {!! Form::select('balance_option', array(''=>'-- Select --','lessthan'=>'<','lessequal'=>'<=','equal'=>'=','greaterthan'=>'>','greaterequal'=>'>='),  null,['class'=>'form-control input-view-border1 select2 js_main_select', 'id' => 'balance_option']) !!}
                    </div>
                   
                    <div class="col-lg-4 col-md-3 col-sm-3 col-xs-10">
                        {!! Form::text('balance',null,['maxlength'=>'26','class'=>'form-control dm-copay-amount input-sm-header-billing js_sub_select','placeholder'=>'Amount', 'id' => 'balance']) !!}
                    </div>
                </div>
                <div id="js_search_date_adj" class="js_date_validation form-group-billing">
                    {!! Form::label('DOS', 'DOS',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-4 col-md-3 col-sm-3 col-xs-10">
                        <i onclick="iconclick('dos_from')" class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i> 
                        {!! Form::text('dos_from',null,['id'=>'dos_from','class'=>'form-control input-sm-header-billing dm-date datepicker search_start_date','placeholder'=>'From']) !!}
                    </div>
                    <div class="col-lg-4 col-md-3 col-sm-3 col-xs-10">
                        <i onclick="iconclick('dos_to')" class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                        {!! Form::text('dos_to',null,['id'=>'dos_to','class'=>'form-control input-sm-header-billing dm-date datepicker search_end_date','placeholder'=>'To']) !!}
                    </div>
                </div>
               
            </div>            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right p-r-0">
                <input class="btn btn-medcubics-small" value="Search" type="submit">
                {!! Form::reset('Reset',["class"=>"btn btn-medcubics-small js_search_reset"]) !!}
            </div>
            
        </div>
    </div>
</div>
{!! Form::close() !!}