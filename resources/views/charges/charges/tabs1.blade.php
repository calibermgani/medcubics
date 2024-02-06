<div class="col-md-12 margin-t-m-20 margin-b-5">
     <div class="box box-info no-shadow orange-border">
        <div class="box-body textbox-bg-orange border-radius-4 p-b-0">
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 no-bottom form-horizontal m-b-m-10 margin-t-m-4">                               
                <div class="form-group-billing">                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 billing-select2-orange">
                        {!! Form::select('employer_id',array('name' => 'Name','last_name'=>'Last Name','first_name'=>'First Name','account_no'=>'Acc no','policy_id'=>'Policy ID', 'dob' => 'DOB', 'ssn' => 'SSN'),null,['class'=>'form-control select2', 'id' => 'PatientDetail']) !!}
                    </div>                                                     
                </div>                                    
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 no-bottom form-horizontal margin-t-m-4 m-b-m-8">
                <div class="form-group-billing">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                       {!! Form::text('search','',['maxlength'=>'25','class'=>'form-control js-search-text input-sm-modal-billing', 'placeholder'=>'Search', 'style'=>'border:1px solid #ccc;']) !!}
                    </div>
                </div>                  
            </div>
                                  
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2 no-bottom form-horizontal m-b-m-8 margin-t-m-4">
                <div class="form-group-billing">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                       {!! Form::submit('Search', ['class'=>'btn btn-medcubics-small','style'=>'margin-top:1px;', 'id' => 'js-search-patient']) !!}
                    </div>
                </div>                  
            </div> 
           {!! Form::hidden('facility_id',@$charge_session_value->facility_id, ['id' => 'facilityy_id']) !!}
           {!! Form::hidden('rendering_id',@$charge_session_value->rendering_provider_id, ['id' => 'rendering_id']) !!}
           {!! Form::hidden('billing_id',@$charge_session_value->billing_provider_id, ['id' => 'billing_id']) !!}
           {!! Form::hidden('reference',@$charge_session_value->reference, ['id' => 'js_reference']) !!}

           {!! Form::hidden('pos_val',@$charge_session_value->pos, ['id' => 'pos_val']) !!}
		   {!! Form::hidden('poscode_val',@$charge_session_value->pos_code, ['id' => 'pos_val']) !!}
           {!! Form::hidden('dos_from_val',@$charge_session_value->dos_from, ['id' => 'dos_from_val']) !!}
           {!! Form::hidden('dos_to_val',@$charge_session_value->dos_to, ['id' => 'dos_to_val']) !!}

           {!! Form::hidden('query',@$charge_session_value->query, ['id' => 'js_query']) !!}
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>