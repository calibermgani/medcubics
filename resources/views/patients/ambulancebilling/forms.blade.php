<div class="box box-view no-shadow no-border"><!--  Box Starts -->
    <div class="box-body form-horizontal no-padding">
        
        <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
        @if(strpos($currnet_page, 'create') !== false)
        {!!Form::hidden('claim_id',null,['class' => 'js-popclaim_id'])!!}
        @endif
		
		<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.ambulance_billing") }}' />
		
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding">
                <div class="form-group-billing"> 
                    <?php $patient_id = Route::getCurrentRoute()->parameter('patient_id'); ?>
                    {!!Form::hidden('patient_id', $patient_id)!!}                            
                    {!! Form::label('patient_weight', 'Emergency', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                        {!!Form::checkbox('is_emergency')!!}                        
                    </div>                                            
                </div>
            </div>
        </div>
            
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">
            <div class="form-group-billing">                             
                {!! Form::label('patient_weight', 'Patient Weight', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                    {!! Form::text('patient_weight',null,['class'=>'form-control input-sm-modal-billing']) !!} 

                </div>                                        
            </div>

            <div class="form-group-billing">                             
                {!! Form::label('transport_distance', 'Transport Distance', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                    {!! Form::text('tr_distance',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                </div>                                        
            </div>

            <div class="form-group-billing">                             
                {!! Form::label('transport_code', 'Transport Code', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                    {!! Form::text('tr_code',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                </div>                                        
            </div>

            <div class="form-group-billing">                             
                {!! Form::label('transport_reason_code', 'Transport Reason Code', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                    {!! Form::text('tr_reason_code',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                </div>                                        
            </div>
        </div>
        <div class="js-address-class" id="js-address-general-address">
			{!! Form::hidden('pick_address_type','practice',['class'=>'js-address-type']) !!}
            {!! Form::hidden('pick1_address_type_id',@$practice->id,['class'=>'js-address-type-id']) !!}
            {!! Form::hidden('pick1_address_type_category','pay_to_address',['class'=>'js-address-type-category']) !!}
            {!! Form::hidden('pick1_address1',@$address_flag['pta']['address1'],['class'=>'js-address-address1']) !!}
            {!! Form::hidden('pick1_city',@$address_flag['pta']['city'],['class'=>'js-address-city']) !!}
            {!! Form::hidden('pick1_state',@$address_flag['pta']['state'],['class'=>'js-address-state']) !!}
            {!! Form::hidden('pick1_zip5',@$address_flag['pta']['zip5'],['class'=>'js-address-zip5']) !!}
            {!! Form::hidden('pick1_zip4',@$address_flag['pta']['zip4'],['class'=>'js-address-zip4']) !!}
            {!! Form::hidden('pick1_is_address_match',@$address_flag['pta']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
            {!! Form::hidden('pick1_error_message',@$address_flag['pta']['error_message'],['class'=>'js-address-error-message']) !!}
        
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="form-group-billing">                             
                {!! Form::label('pick_addr1', 'Pickup Address 1', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                    {!! Form::text('pick_addr1',null,['maxlength'=>'26','id'=>'address1','class'=>'form-control js-address-check']) !!}   
                </div>                                        
            </div>

            <div class="form-group-billing">                             
                {!! Form::label('pick_addr2', ' Address 2', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                    {!! Form::text('pick_addr2',null,['id'=>'address2','class'=>'form-control js-address2-tab dm-address']) !!} 
                </div>                                        
            </div>
            <div class="form-group-billing">                             
                {!! Form::label('pick_city', 'City / State', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                     {!! Form::text('pick_city',null,['maxlength'=>'19','class'=>'form-control js-address-check','id'=>'city']) !!}
                </div>                        
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-10">
                    {!! Form::text('pick_state',null,['class'=>'form-control js-address-check js-state-tab dm-state','id'=>'state']) !!}
                </div>
            </div>
            <div class="form-group-billing">                             
                {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                     {!! Form::text('zipcode5',(isset($claimambulancedetail) && $claimambulancedetail->pick_zip5 != 0?$claimambulancedetail->pick_zip5:''),['class'=>'form-control dm-zip5 js-address-check','id'=>'pay_zip5']) !!}
                </div>                        
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4">                    
                    {!! Form::text('zipcode4',(isset($claimambulancedetail) && $claimambulancedetail->pick_zip4 != 0?$claimambulancedetail->pick_zip4:''),['class'=>'form-control dm-zip4 js-address-check','id'=>'pay_zip4']) !!}
                </div>
                <div class="col-md-1 col-sm-2 col-xs-2 p-l-0">            
                    <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                    <?php echo $value; ?> 
                </div> 
            </div>
                   
        </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding  bg-brown">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-t-5 js-address-class" id="js-address-drop-address">
                {!! Form::hidden('drop_address_type','practice',['class'=>'js-address-type']) !!}
                {!! Form::hidden('drop_address_type_id',@$practice->id,['class'=>'js-address-type-id']) !!}
                {!! Form::hidden('drop_address_type_category','pay_to_address',['class'=>'js-address-type-category']) !!}
                {!! Form::hidden('drop_address1',@$address_flag['pta']['address1'],['class'=>'js-address-address1']) !!}
                {!! Form::hidden('drop_city',@$address_flag['pta']['city'],['class'=>'js-address-city']) !!}
                {!! Form::hidden('drop_state',@$address_flag['pta']['state'],['class'=>'js-address-state']) !!}
                {!! Form::hidden('drop_zip5',@$address_flag['pta']['zip5'],['class'=>'js-address-zip5']) !!}
                {!! Form::hidden('drop_zip4',@$address_flag['pta']['zip4'],['class'=>'js-address-zip4']) !!}
                {!! Form::hidden('drop_is_address_match',@$address_flag['pta']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                {!! Form::hidden('drop_error_message',@$address_flag['pta']['error_message'],['class'=>'js-address-error-message']) !!}
                <div class="form-group-billing">                             
                    {!! Form::label('drop_location', ' Drop off Location', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                        {!! Form::text('drop_location',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                    </div>                                        
                </div>

                <div class="form-group-billing">                             
                    {!! Form::label('drop_addr1', 'Drop Off Address 1', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">                   
                         {!! Form::text('drop_addr1',null,['id'=>'drop_addr1','class'=>'form-control dm-address  js-address-check']) !!}
                    </div>                                        
                </div>

                <div class="form-group-billing">                             
                    {!! Form::label('drop_addr2', ' Address 2', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                        {!! Form::text('drop_addr2',null,['id'=>'drop_addr2','class'=>'form-control dm-address  js-address2-tab']) !!}
                    </div>                                        
                </div>

                <div class="form-group-billing">                             
                    {!! Form::label('city/state', 'City / State', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                        {!! Form::text('drop_city',null,['class'=>'form-control js-letters-caps-format js-address-check ','id'=>'drop_city']) !!}
                    </div>                        
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-10">
                        {!! Form::text('drop_state',null,['class'=>'form-control dm-state js-all-caps-letter-format js-address-check js-state-tab','id'=>'drop_state']) !!}                    
                    </div>
                </div>
                <div class="form-group-billing">                             
                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">                  
                        {!! Form::text('drop_zip5',(isset($claimambulancedetail) && $claimambulancedetail->drop_zip4 != 0?$claimambulancedetail->drop_zip4:''),['class'=>'form-control   dm-zip5 js-address-check','id'=>'drop_zip5']) !!}
                    </div>                        
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
                        {!! Form::text('drop_zip4',(isset($claimambulancedetail) && $claimambulancedetail->drop_zip5 != 0?$claimambulancedetail->drop_zip5:''),['class'=>'form-control   dm-zip4 js-address-check','id'=>'drop_zip4']) !!}
                    </div>
                     <div class="col-md-1 col-sm-2 col-xs-2 p-l-0">            
                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>   
                        <?php echo $value; ?> 
                    </div> 
                </div>            
			</div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-t-5 "> 
            <div class="form-group-billing">                                                                        
                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                    {!! Form::textarea('medical_note',null,['class'=>'form-control input-sm-modal-billing minheight-130','placeholder'=>'Medical Office Notes']) !!} 
                </div>                                                 
            </div>
        </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding">  
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  space10">                                                                                        
            <div class="form-group-billing">                                                                        
                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                    {!! Form::textarea('round_trip',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Round Trip Description']) !!} 
                </div>                                                 
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  space10">                                                                                        
            <div class="form-group-billing">                                                                        
                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                    {!! Form::textarea('strecher_purpose',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Stretcher Purpose']) !!} 
                </div>                                                 
            </div>
        </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  space10">                                                                                        
            <div class="form-group-billing">                                                                        
                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                    {!! Form::textarea('business_note',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Business Office Notes']) !!} 
                </div>                                                
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  space10">                                                                                        
            <div class="form-group-billing">                                                                        
                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                    {!! Form::textarea('ambulance_cert',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Ambulance Certification']) !!} 
                </div>                                                 
            </div>
        </div>

    </div>

    <div class="modal-footer">
        {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group js-submit-popup', 'id' => 'claimbilling']) !!}              
        <button class="btn btn-medcubics close_popup" type="button">Cancel</button>
    </div>

</div><!-- /.box-body -->