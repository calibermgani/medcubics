<div class="modal-sm-usps">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close js_usps_add_modal_close_btn" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">USPS Address Information</h4>
        </div>
        <div class="modal-body">
            <ul class="nav nav-list" style="line-height:24px;">                   
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"  @if(@$address_flag['general']['is_address_match'] != 'Yes') hide @endif" id="modal_show_success_message">
                   
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 med-green font600">Address </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7"><span id="modal_address">{{@$address_flag['general']['address1']}}</span></div>
                    </div>
                     
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"> 
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 med-green font600">City </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7"><span id="modal_city">{{@$address_flag['general']['city']}}</span></div>
                    </div>
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 med-green font600">State </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7"><span id="modal_state">{{@$address_flag['general']['state']}}</span></div>
                    </div>
                     
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 med-green font600">Zip Code </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7"><span id="modal_zip5">{{@$address_flag['general']['zip5']}} - {{@$address_flag['general']['zip4']}}</span></div>                    
                    </div>
                    </div>
            </ul>                   
             <p id="modal_show_error_message" @if(@$address_flag['general']['is_address_match'] != 'No') class="hide" @endif>{{@$address_flag['general']['error_message']}}</p>	
        </div>

    </div><!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->