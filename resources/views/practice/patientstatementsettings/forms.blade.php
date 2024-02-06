<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-12 col-md-12 col-xs-12 margin-t-20 no-padding"><!--  Col-12 Starts -->
        <div class="box no-shadow "><!--  Left side Content Starts -->
            <div class="box-header-view margin-b-10">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body form-horizontal p-b-8 margin-l-10">                
                <div class="form-group">
                    {!! Form::label('Pay by Day', 'Pay by Day', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-5 control-label star']) !!} 
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-5 @if($errors->first('address_1')) error @endif">
                        {!! Form::text('paybydate',null,['id'=>'paybydate','class'=>'form-control dm-agelimit','autocomplete'=>'nope']) !!}
                        {!! $errors->first('paybydate', '<p> :message</p>') !!}
                    </div>      
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 line-height-26"> day(s)</div> 
                </div> 
                <div class="form-group bottom-space-10">
                    {!! Form::label('Service Location', 'Service Location', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-5 control-label']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 control-group">
                        {!! Form::radio('servicelocation', 'Facility',true,['class'=>'','id'=>'c-facility']) !!} {!! Form::label('c-facility', 'Facility',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('servicelocation', 'Practice',null,['class'=>'','id'=>'c-practice']) !!} {!! Form::label('c-practice', 'Practice',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>                
                </div>

                <div class=" js-address-class" id="js-address-general-address"><!-- Address Div Starts -->
                    {!! Form::hidden('general_address_type','employer',['class'=>'js-address-type']) !!}
                    {!! Form::hidden('general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                    {!! Form::hidden('general_address_type_category','general_information',['class'=>'js-address-type-category']) !!}
                    {!! Form::hidden('general_address1',$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                    {!! Form::hidden('general_city',$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                    {!! Form::hidden('general_state',$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                    {!! Form::hidden('general_zip5',$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                    {!! Form::hidden('general_zip4',$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                    {!! Form::hidden('general_is_address_match',$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                    {!! Form::hidden('general_error_message',$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}
                    <div class="form-group">
                        {!! Form::label('Check Payable Address', 'Check Payable Address', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 @if($errors->first('address_1')) error @endif">
                            {!! Form::text('check_add_1',null,['id'=>'check_add_1','class'=>'form-control js-address-check dm-address','placeholder'=>'Address 1','autocomplete'=>'nope']) !!} 
                            {!! $errors->first('check_add_1', '<p> :message</p>') !!}
                        </div>

                        {!! Form::hidden('check_payable_address_id',null,['id'=>'check_payable_address_id','class'=>'form-control']) !!}
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-5 p-r-0 p-l-0"><a href="#" class="line-height-26 text-underline font600" data-url="{{ url('patientstatement/getaddress') }}" data-toggle="modal" data-target="#patient_statement_modal">Get Address</a></div>
                    </div> 

                    <div class="form-group">
                        {!! Form::label('', '', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 @if($errors->first('address_2')) error @endif">
                            {!! Form::text('check_add_2',null,['id'=>'check_add_2','class'=>'form-control js-address2-tab dm-address','placeholder'=>'Address 2','autocomplete'=>'nope']) !!}
                            {!! $errors->first('check_add_2', '<p> :message</p>') !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div> 

                    <div class="form-group">
                        {!! Form::label('', '', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                            {!! Form::text('check_city',null,['class'=>'form-control js-address-check dm-address','id'=>'check_city','placeholder'=>'City','autocomplete'=>'nope']) !!}
                        </div>                 
                        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 p-l-0"> 
                            {!! Form::text('check_state',null,['class'=>'form-control dm-state js-address-check js-state-tab','id'=>'check_state','placeholder'=>'ST','autocomplete'=>'nope']) !!}
                        </div>
                    </div>   
                    <div class="form-group bottom-space-10">
                        {!! Form::label('', '', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">                             
                            {!! Form::text('check_zip5',null,['class'=>'form-control dm-zip5  js-address-check','id'=>'check_zip5','placeholder'=>'Zip 5','autocomplete'=>'nope']) !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-4">                             
                            {!! Form::text('check_zip4',null,['class'=>'form-control dm-zip4  js-address-check','id'=>'check_zip4','placeholder'=>'Zip 4','autocomplete'=>'nope']) !!}
                        </div>
                        <div class="col-md-1 col-sm-1 col-xs-2">
                            <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                            <span class="js-address-success @if($address_flag['general']['is_address_match'] != 'Yes') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                            <span class="js-address-error @if($address_flag['general']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                            <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>
                            <?php echo $value; ?> 
                        </div>
                    </div> 
                </div>

                <div class="form-group bottom-space-10">
                    {!! Form::label('Call Back Phone', 'Call Back Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-5 control-label star']) !!}      
                    <div class="col-lg-5 col-md-5 col-sm-4 col-xs-6">
                        {!! Form::text('callbackphone',null,['class'=>'form-control dm-phone','autocomplete'=>'nope']) !!} 
                    </div>               
                </div>

                <div class="form-group bottom-space-10">
                    {!! Form::label('Next Billing Cycle', 'Next Billing Cycle', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-5 control-label star']) !!}      
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                        {!! Form::text('statementsentdays',null,['class'=>'form-control dm-agelimit','autocomplete'=>'nope']) !!}
                    </div>               
                </div>

                <div class="form-group bottom-space-10">
                    {!! Form::label('Minimum Patient Balance', 'Min Patient Balance', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-5 control-label']) !!}      
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                        {!! Form::text('minimumpatientbalance',null,['class'=>'form-control js_amount_separation','maxlength'=>'10','autocomplete'=>'nope']) !!} 
                    </div>             
                </div>

                <div class="form-group p-b-5 hidden-sm hidden-xs">
                    &ensp;   
                </div>
                <div class="form-group p-b-4 hidden-sm hidden-xs">
                    &ensp;   
                </div>
				
				<div class="form-group p-b-4 hidden-sm hidden-xs">
                    &ensp;   
                </div>
                <div class="form-group p-b-4 hidden-sm hidden-xs">
                    &ensp;   
                </div>
				<div class="form-group p-b-4 hidden-sm hidden-xs">
                    &ensp;   
                </div>
            </div>
        </div><!--  Left side Content Ends -->

    </div><!--Background color for Inner Content Ends -->

    <div class="col-lg-12 col-md-12 col-xs-12 no-padding"><!--  Col-12 Starts -->
        <div class="box no-shadow "><!--  Left side Content Starts -->
            <div class="box-header-view margin-b-10">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">Bulk Statement</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body form-horizontal p-b-20 margin-l-10">

                <div class="form-group bottom-space-10">
                    {!! Form::label('Bulk Statements', 'Bulk Statements', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}
                    <div class="control-group col-lg-6 col-md-6 col-sm-6 col-xs-7">
                        {!! Form::radio('bulkstatement', '1',true,['class'=>'','id'=>'c-bulk-y']) !!}{!! Form::label('c-bulk-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('bulkstatement', '0',null,['class'=>'','id'=>'c-bulk-n']) !!}{!! Form::label('c-bulk-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>  
                    
                </div>

                <div class="form-group bottom-space-10 js_cycle @if(isset($psettings) && @$psettings->bulkstatement == 0) hide @endif">
                    {!! Form::label('Statement Cycle', 'Statement Cycle', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                        {!! Form::select('statementcycle', array('All' => 'All','Billcycle' => 'Bill Cycle','Account'=>'Acc No', 'Category' => 'Category'),null,['class'=>'form-control select2']) !!}
						<!-- 'Facility' => 'Facility','Provider'=>'Provider' -->
                    </div>                
                </div>

                <?php $week1billcycle = $week2billcycle = $week3billcycle = $week4billcycle = $week5billcycle = '';  ?>
                @if(@$psettings->statementcycle == 'Billcycle')
                <?php 	
					$week1billcycle = explode(",", $psettings->week_1_billcycle);	  
					$week2billcycle = explode(",", $psettings->week_2_billcycle);
					$week3billcycle = explode(",", $psettings->week_3_billcycle);
					$week4billcycle = explode(",", $psettings->week_4_billcycle);
					$week5billcycle = explode(",", $psettings->week_5_billcycle);
				?>
                @endif

                <div class="js_billcycle_box js_statmentcycle hide">

                    <div class="form-group">
                        {!! Form::label('Week 1', 'Week 1', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('billcycleweek1[]', array('A - G' => 'A - G','H - M' => 'H - M','N - S' => 'N - S','T - Z'=>'T - Z'),$week1billcycle,['multiple'=>'multiple','class'=>'form-control select2 billcycleweek1']) !!}
                        </div>                    
                    </div>

                    <div class="form-group">
                        {!! Form::label('Week 2', 'Week 2', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('billcycleweek2[]', array('A - G' => 'A - G','H - M' => 'H - M','N - S' => 'N - S','T - Z'=>'T - Z'),$week2billcycle,['multiple'=>'multiple','class'=>'form-control select2 billcycleweek2']) !!}
                        </div>                    
                    </div>

                    <div class="form-group">
                        {!! Form::label('Week 3', 'Week 3', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('billcycleweek3[]', array('A - G' => 'A - G','H - M' => 'H - M','N - S' => 'N - S','T - Z'=>'T - Z'),$week3billcycle,['multiple'=>'multiple','class'=>'form-control select2 billcycleweek3']) !!}
                        </div>                   
                    </div>

                    <div class="form-group">
                        {!! Form::label('Week 4', 'Week 4', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('billcycleweek4[]', array('A - G' => 'A - G','H - M' => 'H - M','N - S' => 'N - S','T - Z'=>'T - Z'),$week4billcycle,['multiple'=>'multiple','class'=>'form-control select2 billcycleweek4']) !!}
                        </div>                    
                    </div>

                    <div class="form-group">
                        {!! Form::label('Week 5', 'Week 5', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('billcycleweek5[]', array('A - G' => 'A - G','H - M' => 'H - M','N - S' => 'N - S','T - Z'=>'T - Z'),$week5billcycle,['multiple'=>'multiple','class'=>'form-control select2 billcycleweek5']) !!}
                        </div>                    
                    </div>
                </div>

                <?php $week1facility = $week2facility = $week3facility = $week4facility = $week5facility = '';  ?>
                @if(@$psettings->statementcycle == 'Facility')
                <?php 	
					$week1facility = explode(",", $psettings->week_1_facility);	  
					$week2facility = explode(",", $psettings->week_2_facility);
					$week3facility = explode(",", $psettings->week_3_facility);
					$week4facility = explode(",", $psettings->week_4_facility);
					$week5facility = explode(",", $psettings->week_5_facility);
				?>
                @endif

                <div class="js_facility_box js_statmentcycle hide">

                    <div class="form-group">
                        {!! Form::label('Week 1', 'Week 1', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('facilityweek1[]',(array)$facility,$week1facility,['multiple'=>'multiple','class'=>'form-control select2 facilityweek1']) !!}
                        </div>                    
                    </div>

                    <div class="form-group">
                        {!! Form::label('Week 2', 'Week 2', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('facilityweek2[]', (array)$facility,$week2facility,['multiple'=>'multiple','class'=>'form-control select2 facilityweek2']) !!}
                        </div>                   
                    </div>

                    <div class="form-group">
                        {!! Form::label('Week 3', 'Week 3', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('facilityweek3[]', (array)$facility,$week3facility,['multiple'=>'multiple','class'=>'form-control select2 facilityweek3']) !!}
                        </div>                    
                    </div>

                    <div class="form-group">
                        {!! Form::label('Week 4', 'Week 4', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('facilityweek4[]', (array)$facility,$week4facility,['multiple'=>'multiple','class'=>'form-control select2 facilityweek4']) !!}
                        </div>                    
                    </div>

                    <div class="form-group">
                        {!! Form::label('Week 5', 'Week 5', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('facilityweek5[]', (array)$facility,$week5facility,['multiple'=>'multiple','class'=>'form-control select2 facilityweek5']) !!}
                        </div>                    
                    </div>
                </div>
                <?php $week1provider = $week2provider = $week3provider = $week4provider = $week5provider = '';  ?>
                @if(@$psettings->statementcycle == 'Provider')
                <?php 	
					$week1provider = explode(",", $psettings->week_1_provider);	  
					$week2provider = explode(",", $psettings->week_2_provider);
					$week3provider = explode(",", $psettings->week_3_provider);
					$week4provider = explode(",", $psettings->week_4_provider);
					$week5provider = explode(",", $psettings->week_5_provider);
				?>
                @endif

                <div class="js_provider_box js_statmentcycle hide">

                    <div class="form-group">
                        {!! Form::label('Week 1', 'Week 1', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('providerweek1[]', (array)$provider,$week1provider,['multiple'=>'multiple','class'=>'form-control select2 providerweek1']) !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group ">
                        {!! Form::label('Week 2', 'Week 2', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('providerweek2[]', (array)$provider,$week2provider,['multiple'=>'multiple','class'=>'form-control select2 providerweek2']) !!}
                        </div>                    
                    </div>

                    <div class="form-group">
                        {!! Form::label('Week 3', 'Week 3', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('providerweek3[]', (array)$provider,$week3provider,['multiple'=>'multiple','class'=>'form-control select2 providerweek3']) !!}
                        </div>                    
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('Week 4', 'Week 4', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('providerweek4[]', (array)$provider,$week4provider,['multiple'=>'multiple','class'=>'form-control select2 providerweek4']) !!}
                        </div>                    
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('Week 5', 'Week 5', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('providerweek5[]',(array)$provider,$week5provider,['multiple'=>'multiple','class'=>'form-control select2 providerweek5']) !!}
                        </div>                    
                    </div>
                </div>

                <?php 
					$week1account[0] = $week2account[0] = $week3account[0]=$week4account[0]=$week5account[0]= $week1account[1] = $week2account[1] = $week3account[1]=$week4account[1]=$week5account[1]= '';
				?>
                @if(@$psettings->statementcycle == 'Account')
                <?php 	
					$week1account = explode(",", $psettings->week_1_account);	  
					$week2account = explode(",", $psettings->week_2_account);
					$week3account = explode(",", $psettings->week_3_account);
					$week4account = explode(",", $psettings->week_4_account);
					$week5account = explode(",", $psettings->week_5_account);
				?>
                @endif

                <div class="js_account_box js_statmentcycle form-horizontal hide">

                    <div class="form-group bottom-space-10">
                        {!! Form::label('Week 1', 'Week 1', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}      
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('fromaccountweek1',$week1account[0],['maxlength'=>'30','class'=>'js-all-caps-letter-format form-control fromaccountweek1 dm-pat-accno','placeholder'=>'From']) !!} 
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('toaccountweek1',$week1account[1],['maxlength'=>'30','class'=>'js-all-caps-letter-format form-control toaccountweek1 dm-pat-accno','placeholder'=>'To']) !!} 
                        </div>
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('Week 2', 'Week 2', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}      
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('fromaccountweek2',$week2account[0],['maxlength'=>'30','class'=>'js-all-caps-letter-format form-control fromaccountweek2 dm-pat-accno','placeholder'=>'From']) !!} 
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('toaccountweek2',$week2account[1],['maxlength'=>'30','class'=>'js-all-caps-letter-format form-control toaccountweek2 dm-pat-accno','placeholder'=>'To']) !!} 
                        </div>
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('Week 3', 'Week 3', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}      
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('fromaccountweek3',$week3account[0],['maxlength'=>'30','class'=>'js-all-caps-letter-format form-control fromaccountweek3 dm-pat-accno','placeholder'=>'From']) !!} 
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('toaccountweek3',$week3account[1],['maxlength'=>'30','class'=>'js-all-caps-letter-format form-control toaccountweek3 dm-pat-accno','placeholder'=>'To']) !!} 
                        </div>
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('Week 4', 'Week 4', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}      
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('fromaccountweek4',$week4account[0],['maxlength'=>'30','class'=>'js-all-caps-letter-format form-control fromaccountweek4 dm-pat-accno','placeholder'=>'From']) !!} 
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('toaccountweek4',$week4account[1],['maxlength'=>'30','class'=>'js-all-caps-letter-format form-control toaccountweek4 dm-pat-accno','placeholder'=>'To']) !!} 
                        </div>
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('Week 5', 'Week 5', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}      
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('fromaccountweek5',$week5account[0],['maxlength'=>'30','class'=>'js-all-caps-letter-format  form-control fromaccountweek5 dm-pat-accno','placeholder'=>'From']) !!} 
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            {!! Form::text('toaccountweek5',$week5account[1],['maxlength'=>'30','class'=>'js-all-caps-letter-format form-control toaccountweek5 dm-pat-accno ','placeholder'=>'To']) !!} 
                        </div>
                    </div> 
                </div>
				
				<?php $week1category = $week2category = $week3category = $week4category = $week5category = '';  ?>
                @if(@$psettings->statementcycle == 'Category')
                <?php 	
					$week1category = explode(",", $psettings->week_1_category);	  
					$week2category = explode(",", $psettings->week_2_category);
					$week3category = explode(",", $psettings->week_3_category);
					$week4category = explode(",", $psettings->week_4_category);
					$week5category = explode(",", $psettings->week_5_category);
				?>
                @endif

                <div class="js_category_box js_statmentcycle hide">

                    <div class="form-group">
                        {!! Form::label('Week 1', 'Week 1', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('categoryweek1[]', (array)$category,$week1category,['multiple'=>'multiple','class'=>'form-control select2 categoryweek1']) !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group ">
                        {!! Form::label('Week 2', 'Week 2', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('categoryweek2[]', (array)$category,$week2category,['multiple'=>'multiple','class'=>'form-control select2 categoryweek2']) !!}
                        </div>                    
                    </div>

                    <div class="form-group">
                        {!! Form::label('Week 3', 'Week 3', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('categoryweek3[]', (array)$category,$week3category,['multiple'=>'multiple','class'=>'form-control select2 categoryweek3']) !!}
                        </div>                    
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('Week 4', 'Week 4', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('categoryweek4[]', (array)$category,$week4category,['multiple'=>'multiple','class'=>'form-control select2 categoryweek4']) !!}
                        </div>                    
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('Week 5', 'Week 5', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            {!! Form::select('categoryweek5[]',(array)$category,$week5category,['multiple'=>'multiple','class'=>'form-control select2 categoryweek5']) !!}
                        </div>                    
                    </div>
					
                </div>
				

            </div>
        </div><!--  Left side Content Ends -->        
    </div><!--Background color for Inner Content Ends -->
</div>
<!-- Patient id Hidden used in Account no in Bill cycle label -->
<?php $patient_id = App\Models\Patients\Patient::patientSettings(); ?>
<input type="hidden" class="js_patientSettings" value=<?php echo $patient_id; ?>>
<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-12 col-md-12 col-xs-12 margin-t-20 no-padding"><!--  Col-12 Starts -->
        <div class="box no-shadow "><!--  Left side Content Starts -->
            <div class="box-header-view margin-b-10">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">Additional Info</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body form-horizontal p-b-20 margin-l-10">

                <div class="form-group bottom-space-10 ">
                    {!! Form::label('Rendering Provider', 'Rendering Provider', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-7">
                        {!! Form::radio('rendering_provider', '1',true,['class'=>'','id'=>'c-r-y']) !!} {!! Form::label('c-r-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('rendering_provider', '0',null,['class'=>'','id'=>'c-r-n']) !!} {!! Form::label('c-r-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>                
                </div>

                <div class="form-group bottom-space-10 margin-t-20">
                    {!! Form::label('Payment Format', 'Payment Format', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::radio('displaypayment', 'Payments',true,['class'=>'','id'=>'c-payments']) !!} {!! Form::label('c-payments', 'Payments',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('displaypayment', 'InsPatient',null,['class'=>'','id'=>'c-insurance']) !!} {!! Form::label('c-insurance', 'Insurance/Patients',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>               
                </div>

                <div class="form-group bottom-space-10 margin-t-20">
                    {!! Form::label('Show Latest Payment Info', 'Latest Payment Info', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::radio('latestpaymentinfo', '1',true,['class'=>'','id'=>'c-l-y']) !!} {!! Form::label('c-l-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('latestpaymentinfo', '0',null,['class'=>'','id'=>'c-l-n']) !!} {!! Form::label('c-l-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>               
                </div>

                <div class="form-group bottom-space-10 margin-t-20">
                    {!! Form::label('CPT with Short Description', 'CPT with Short Description', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                    <div class="control-group col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::radio('cpt_shortdesc', 'Claim',true,['class'=>'','id'=>'c-claim']) !!} {!! Form::label('c-claim', 'Claim',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('cpt_shortdesc', 'Lineitem',null,['class'=>'','id'=>'c-lineitem']) !!} {!! Form::label('c-lineitem', 'Line Item',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>                        
                </div>

                <div class="form-group bottom-space-10 margin-t-20">
                    {!! Form::label('Primary ICD for that DOS', 'Primary ICD for that DOS', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                    <div class="control-group col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::radio('primary_dx', '1',null,['class'=>'','id'=>'c-p-y']) !!} {!! Form::label('c-p-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('primary_dx', '0',true,['class'=>'','id'=>'c-p-n']) !!} {!! Form::label('c-p-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>                        
                </div>

                <div class="form-group bottom-space-10 margin-t-20">
                    {!! Form::label('$0 Ins Balance Service Line', '$0 Ins Balance Service Line', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                    <div class="control-group col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::radio('insserviceline', '1',true,['class'=>'','id'=>'c-bal-y']) !!} {!! Form::label('c-bal-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('insserviceline', '0',null,['class'=>'','id'=>'c-bal-n']) !!} {!! Form::label('c-bal-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>                        
                </div>

                <div class="form-group bottom-space-10 margin-t-20">
                    {!! Form::label('$0 Pat Balance Service Line', '$0 Pat Balance Service Line', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                    <div class="control-group col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::radio('patserviceline', '1',true,['class'=>'','id'=>'c-pat-y']) !!} {!! Form::label('c-pat-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('patserviceline', '0',null,['class'=>'','id'=>'c-pat-n']) !!} {!! Form::label('c-pat-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>                       
                </div>

                <div class="form-group bottom-space-10 margin-t-20 hide">
                    {!! Form::label('Financial Charges', 'Financial Charges', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}
                    <div class="control-group col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::radio('financial_charge', '1',null,['class'=>'','disabled','id'=>'c-financial-y']) !!} {!! Form::label('c-financial-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('financial_charge', '0',null,['class'=>'','disabled','id'=>'c-financial-n']) !!} {!! Form::label('c-financial-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>                       
                </div>

                <div class="form-group bottom-space-10 margin-t-20">
                    {!! Form::label('Alert', 'Alert', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                    <div class="control-group col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::radio('alert', '1',true,['class'=>'','id'=>'c-alert-y']) !!} {!! Form::label('c-alert-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('alert', '0',null,['class'=>'','id'=>'c-alert-n']) !!} {!! Form::label('c-alert-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>                       
                </div>
                <?php /* Add card type  */?>
                <div class="form-group bottom-space-10 margin-t-20">
                    {!! Form::label('Card Type', 'Card Type', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                    <div class="control-group col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::checkbox('visa_card', null, (@$psettings->visa_card=='1'?true:null), ['class'=>" med-green",'id'=>'c-visa']) !!} &nbsp; <label for="c-visa" class="med-green font600">Visa</label> &nbsp;
                        {!! Form::checkbox('mc_card', null, (@$psettings->mc_card=='1'?true:null), ['class'=>" med-green",'id'=>'c-master']) !!} &nbsp; <label for="c-master" class="med-green font600">Master</label>  &nbsp;
                        {!! Form::checkbox('maestro_card', null, (@$psettings->maestro_card=='1'?true:null), ['class'=>" med-green",'id'=>'c-maestro']) !!} &nbsp; <label for="c-maestro" class="med-green font600">Maestro</label>  &nbsp;
                        {!! Form::checkbox('gift_card', null, (@$psettings->gift_card=='1'?true:null), ['class'=>" med-green",'id'=>'c-gift']) !!} &nbsp; <label for="c-gift" class="med-green font600">Gift</label>  &nbsp;
                    </div>                       
                </div>
				
				<div class="form-group bottom-space-10 margin-t-20">
                    {!! Form::label('Insurance Balance ', 'Insurance Balance ', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}
                    <div class="control-group col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::radio('insurance_balance', '1',true,['class'=>'','id'=>'c-insbal-y']) !!} 
						{!! Form::label('c-insbal-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('insurance_balance', '0',null,['class'=>'','id'=>'c-insbal-n']) !!} 
						{!! Form::label('c-insbal-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>                       
                </div>
				
				<div class="form-group bottom-space-10 margin-t-20">
                    {!! Form::label('Aging Bucket', 'Aging Bucket', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}      
                    <div class="control-group col-lg-7 col-md-7 col-sm-7 col-xs-7">
                        {!! Form::radio('aging_bucket', '1',true,['class'=>'','id'=>'c-aging-y']) !!} {!! Form::label('c-aging-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('aging_bucket', '0',null,['class'=>'','id'=>'c-aging-n']) !!} {!! Form::label('c-aging-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>                       
                </div>
				
                <div class="col-lg-12 col-md-12 hidden-sm hidden-xs margin-b-10">

                </div>

            </div>
        </div><!--  Left side Content Ends -->        
    </div><!--Background color for Inner Content Ends -->

    <div class="col-lg-12 col-md-12 col-xs-12 no-padding"><!--  Col-12 Starts -->
        <div class="box no-shadow "><!--  Left side Content Starts -->
            <div class="box-header-view margin-b-10">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">Payment Message</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body form-horizontal p-b-20 margin-l-10">                
                <div>
                    <div class="form-group bottom-space-10">
                        {!! Form::label('Default Message 1', 'Default Message', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label star']) !!}      
                        <div class="control-group col-lg-6 col-md-6 col-sm-6">
                            {!! Form::textarea('paymentmessage_1', null, ['class' => 'form-control','size' => '30x5']) !!}
                        </div>                           
                    </div>
                </div>
            </div>
             <div class="box-body form-horizontal p-b-20 margin-l-10">

                <div class="form-group bottom-space-10">
                    {!! Form::label('Additional Message', 'Additional Message', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-5 control-label']) !!}
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-7">
                        {!! Form::radio('paymentmessage', '1',true,['class'=>'','id'=>'c-message-y']) !!} {!! Form::label('c-message-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('paymentmessage', '0',null,['class'=>'','id'=>'c-message-n']) !!} {!! Form::label('c-message-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>         
                </div>

                <?php /* Special message add in new feature max length set in 500 */?>
                <div class="js_message_box @if(isset($psettings) && @$psettings->paymentmessage == 0) hide @endif">
                    <div class="form-group bottom-space-10">
                        {!! Form::label(' ', ' ', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                        <div class="control-group col-lg-6 col-md-6 col-sm-6">
                            {!! Form::textarea('spacial_message_1', null, ['class' => 'form-control','size' => '30x5','maxlength'=>500]) !!}
                        </div>                           
                    </div>
                </div>

            </div>
        </div><!--  Left side Content Ends -->

    </div><!--Background color for Inner Content Ends -->
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"><!--Full width Content Starts -->
    {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
    <a href="{{ url('patientstatementsettings')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>

</div><!-- Col 12 Ends -->
<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- Modal Light Box Ends --> 

@push('view.scripts')
<script type="text/javascript">
$(".js-address-check").trigger("blur");
//Account no From
    $(document).on('change click ifToggled', ".toaccountweek1 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="fromaccountweek1"]'));
    });
	
    $(document).on('change click ifToggled', ".toaccountweek2 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="fromaccountweek2"]'));
    });
	
    $(document).on('change click ifToggled', ".toaccountweek3 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="fromaccountweek3"]'));
    });
	
    $(document).on('change click ifToggled', ".toaccountweek4 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="fromaccountweek4"]'));
    });
	
	//Account no To revaildation
    $(document).on('change click ifToggled', ".fromaccountweek1 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="toaccountweek1"]'));
    });
    
	$(document).on('change click ifToggled', ".fromaccountweek2 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="toaccountweek2"]'));
    });
    
	$(document).on('change click ifToggled', ".fromaccountweek3 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="toaccountweek3"]'));
    });
    
	$(document).on('change click ifToggled', ".fromaccountweek4 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="toaccountweek4"]'));
    });
    // Revalidation for Statement Cycle
    $(document).on('change click', ".billcycleweek1, .facilityweek1, .providerweek1, .toaccountweek1, .fromaccountweek1 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('select[name="statementcycle"]'));
    });
	
    $(document).on('change click', ".billcycleweek2, .facilityweek2, .providerweek2, .toaccountweek2, .fromaccountweek2 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('select[name="statementcycle"]'));
    });
	
    $(document).on('change click', ".billcycleweek3, .facilityweek3,.providerweek3, .toaccountweek3, .fromaccountweek3 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('select[name="statementcycle"]'));
    });
	
    $(document).on('change click', ".billcycleweek4, .facilityweek4, .providerweek4, .toaccountweek4, .fromaccountweek4 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('select[name="statementcycle"]'));
    });
	
	$(document).on('change click', ".billcycleweek5, .facilityweek5, .providerweek5, .toaccountweek5, .fromaccountweek5 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('select[name="statementcycle"]'));
    });
	
	$(document).on('change click', "#c-message-n, #c-message-y", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('textarea[name="spacial_message_1"]'));
    });
	
	//Category revaildation
    $(document).on('change click ifToggled', ".categoryweek1, .categoryweek2, .categoryweek3, .categoryweek4, .categoryweek5 ", function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('select[name="statementcycle"]'));
    });
    
    //Bootstrap validation starting
    $(document).ready(function () {
        $('#js-bootstrap-validator')
			.bootstrapValidator({
				message: 'This value is not valid',
				excluded: ':disabled',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					paybydate: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.paybydate") }}'
							},
							callback: {
								message: '',
								callback: function (value, validator, element) {
									if (value == '0' || value == '00')
									{
										return {
											valid: false,
											message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.invalidday") }}'
										};
									}
									return true;
								}
							}
						}
					},
					check_add_1: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var msg = addressValidation(value, "required");
									if (msg != true) {
										return {
											valid: false,
											message: msg
										};
									}
									return true;
								}
							}
						}
					},
					check_add_2: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var msg = addressValidation(value);
									if (msg != true) {
										return {
											valid: false,
											message: msg
										};
									}
									return true;
								}
							}
						}
					},
					check_city: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var msg = cityValidation(value, "required");
									if (msg != true) {
										return {
											valid: false,
											message: msg
										};
									}
									return true;
								}
							}
						}
					},
					check_state: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var msg = stateValidation(value, "required");
									if (msg != true) {
										return {
											valid: false,
											message: msg
										};
									}
									return true;
								}
							}
						}
					},
					check_zip5: {
						message: '',
						trigger: 'change keyup',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var msg = zip5Validation(value, "required");
									if (msg != true) {
										return {
											valid: false,
											message: msg
										};
									}
									return true;
								}
							}
						}
					},
					check_zip4: {
						message: '',
						trigger: 'change keyup',
						validators: {
							message: '',
							callback: {
								message: '',
								callback: function (value, validator) {
									var msg = zip4Validation(value);
									if (msg != true) {
										return {
											valid: false,
											message: msg
										};
									}
									return true;
								}
							}
						}
					},
					callbackphone: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.callbackphone") }}'
							},
							callback: {
								message: '',
								callback: function (value, validator, $field) {
									var phone_msg = '{{ trans("common.validation.phone_limit") }}';
									var response = phoneValidation(value, phone_msg);
									if (response != true) {
										return {
											valid: false,
											message: response
										};
									}
									return true;
								}
							}
						}
					},
					statementsentdays: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.statementsentdays") }}'
							},
							callback: {
								message: '',
								callback: function (value, validator, element) {
									if (value == '0' || value == '00')
									{
										return {
											valid: false,
											message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.invalidday") }}'
										};
									}
									return true;
								}
							}
						}
					},
					minimumpatientbalance: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									if (value == '0' || value == '00')
									{
										return {
											valid: false,
											message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.invalidamount") }}'
										};
									}
									if (value.length > 7) {
										return {
											valid: false,
											message: 'Enter vaild amount'
										};
									}
									return true;
								}
							}
						}
					},
					paymentmessage_1: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var paymentmessage = $('input[name="paymentmessage"]:checked').val();
									//if (paymentmessage == '1') // default message - mandatory condition
									{
										if (value == '' || value == null)
										{
											return {
												valid: false,
												message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.paymentmessage") }}'
											};
										}
									}
									return true;
								}
							}
						}
					},
					spacial_message_1: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var paymentmessage = $('input[name="paymentmessage"]:checked').val();
									if (paymentmessage == '1')
									{
										if (value == '' || value == null)
										{
											return {
												valid: false,
												message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.paymentmessage") }}'
											};
										}
									}
									return true;
								}
							}
						}
					},
					statementcycle: {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var statementcycle = $('select[name="statementcycle"]').val();
									//Statement cycle Account number select
									if (statementcycle == 'Account')
									{
										//Acount No to 
										toaccountweek1 = $(".toaccountweek1").val();
										toaccountweek2 = $(".toaccountweek2").val();
										toaccountweek3 = $(".toaccountweek3").val();
										toaccountweek4 = $(".toaccountweek4").val();
										toaccountweek5 = $(".toaccountweek5").val();

										//Account no From 
										fromaccountweek1 = $(".fromaccountweek1").val();
										fromaccountweek2 = $(".fromaccountweek2").val();
										fromaccountweek3 = $(".fromaccountweek3").val();
										fromaccountweek4 = $(".fromaccountweek4").val();
										fromaccountweek5 = $(".fromaccountweek5").val();
										if (((fromaccountweek5 == "") &&(fromaccountweek4 == "") && (fromaccountweek3 == '') && (fromaccountweek2 == '') && (fromaccountweek1 == '')) || ((toaccountweek5 == "") && (toaccountweek4 == "") && (toaccountweek3 == '') && (toaccountweek2 == '') && (toaccountweek1 == '')))
										{
											return {
												valid: false,
												message: 'Please select at least one week Account no'
											};
										}
									}
									//Statement  Bill cycle select
									if (statementcycle == 'Billcycle')
									{
										var billcycleweek1 = $(".billcycleweek1").select2("val");
										var billcycleweek2 = $(".billcycleweek2").select2("val");
										var billcycleweek3 = $(".billcycleweek3").select2("val");
										var billcycleweek4 = $(".billcycleweek4").select2("val");
										var billcycleweek5 = $(".billcycleweek5").select2("val");
										console.log(billcycleweek1+"##"+billcycleweek2+"##"+billcycleweek3+"##"+billcycleweek4+"##"+billcycleweek5);
										if ((billcycleweek5 == "") && (billcycleweek4 == "") && (billcycleweek3 == '') && (billcycleweek2 == '') && (billcycleweek1 == ''))
										{
											return {
												valid: false,
												message: 'Please select at least one week Billcycle'
											};
										}
									}
									//Statement cycle Facility  select
									if (statementcycle == 'Facility')
									{
										facilityweek1 = $(".facilityweek1").select2("val");
										facilityweek2 = $(".facilityweek2").select2("val");
										facilityweek3 = $(".facilityweek3").select2("val");
										facilityweek4 = $(".facilityweek4").select2("val");
										facilityweek5 = $(".facilityweek5").select2("val");
										if ((facilityweek5 == "") && (facilityweek4 == "") && (facilityweek3 == '') && (facilityweek2 == '') && (facilityweek1 == ''))
										{
											return {
												valid: false,
												message: 'Please select at least one week Facility'
											};
										}
									}
									//Statement cycle Provider select
									if (statementcycle == 'Provider')
									{
										providerweek1 = $(".providerweek1").select2("val");
										providerweek2 = $(".providerweek2").select2("val");
										providerweek3 = $(".providerweek3").select2("val");
										providerweek4 = $(".providerweek4").select2("val");
										providerweek5 = $(".providerweek5").select2("val");
										if ((providerweek5 == "") && (providerweek4 == "") && (providerweek3 == '') && (providerweek2 == '') && (providerweek1 == ''))
										{
											return {
												valid: false,
												message: 'Please select at least one week Provider'
											};
										}
									}
									
									//Statement cycle Category select
									if (statementcycle == 'Category')
									{
										var categoryweek1 = $(".categoryweek1").select2("val");
										var categoryweek2 = $(".categoryweek2").select2("val");
										var categoryweek3 = $(".categoryweek3").select2("val");
										var categoryweek4 = $(".categoryweek4").select2("val");
										var categoryweek5 = $(".categoryweek5").select2("val");
										if ((categoryweek5 == "") &&(categoryweek4 == "") && (categoryweek3 == '') && (categoryweek2 == '') && (categoryweek1 == ''))
										{
											return {
												valid: false,
												message: 'Please select at least one week Category'
											};
										}
									}									
									return true;
								}
							}
						}
					},					
					'providerweek1[]': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var statementcycle = $('select[name="statementcycle"]').val();
									if (statementcycle == 'Provider') {
										if (value == '' || value == null) {
											return {
												valid: false,
												message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.providercycle") }}'
											};
										}
									}
									return true;
								}
							}
						}
					},					
					'fromaccountweek1': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var last_acc_no = $('.js_patientSettings').val();
									var toAccNo = $('.toaccountweek1').val();
									var statementcycle = $('select[name="statementcycle"]').val();
									//if ((statementcycle == 'Account') && ((last_acc_no <= value) || (value.length < 5) && (value != '')))
									if ((statementcycle == 'Account') && ((value.length < 5) && (value != '')))	
									{
										return {
											vaild: false,
											message: 'Enter vaild Account No1'
										}
									}
									return true;
								}
							}
						}
					},
					'fromaccountweek2': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var last_acc_no = $('.js_patientSettings').val();
									var toAccNo = $('.toaccountweek2').val();
									var statementcycle = $('select[name="statementcycle"]').val();
									//if ((statementcycle == 'Account') && ((last_acc_no <= value) || (value.length < 5) && (value != '')))
									if ((statementcycle == 'Account') && ((value.length < 5) && (value != '')))	
									{
										return {
											vaild: false,
											message: 'Enter vaild Account No2'
										}
									}
									return true;
								}
							}
						}
					},
					'fromaccountweek3': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var last_acc_no = $('.js_patientSettings').val();
									var toAccNo = $('.toaccountweek3').val();
									var statementcycle = $('select[name="statementcycle"]').val();
									 
									//if ((statementcycle == 'Account') && ((last_acc_no <= value) || (value.length < 5) && (value != '')))
									if ((statementcycle == 'Account') && ((value.length < 5) && (value != '')))	
									{
										return {
											vaild: false,
											message: 'Enter vaild Account No3'
										}
									}
									return true;
								}
							}
						}
					},
					'fromaccountweek4': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var last_acc_no = $('.js_patientSettings').val();
									var toAccNo = $('.toaccountweek4').val();
									var statementcycle = $('select[name="statementcycle"]').val();
									//if ((statementcycle == 'Account') && ((last_acc_no <= value) || (value.length < 5) && (value != '')))
									if ((statementcycle == 'Account') && ((value.length < 5) && (value != '')))	
									{
										return {
											vaild: false,
											message: 'Enter vaild Account No4'
										}
									}
									return true;
								}
							}
						}
					},
					'fromaccountweek5': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var last_acc_no = $('.js_patientSettings').val();
									var toAccNo = $('.toaccountweek5').val();
									var statementcycle = $('select[name="statementcycle"]').val();
									//if ((statementcycle == 'Account') && ((last_acc_no <= value) || (value.length < 5) && (value != '')))
									if ((statementcycle == 'Account') && ((value.length < 5) && (value != '')))	
									{
										return {
											vaild: false,
											message: 'Enter vaild Account No4'
										}
									}
									return true;
								}
							}
						}
					},
					'toaccountweek1': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var last_acc_no = $('.js_patientSettings').val();
									var AccNo = $('.fromaccountweek1').val();
									 var statementcycle = $('select[name="statementcycle"]').val();
									if ((statementcycle == 'Account') && ((AccNo != '') && (value == ''))) {
										return {
											vaild: false,
											message: 'Enter To Account No'
										}
									}
									/*
									if ((statementcycle == 'Account') && ((value < AccNo) || (last_acc_no < value) && (AccNo != '')))
									{
										return {
											vaild: false,
											message: 'Enter Vaild Account No'
										}
									}
									*/
									if ((statementcycle == 'Account') && ((value.length < 5) && (value != '')))
									{
										return {
											vaild: false,
											message: 'Check the Account No'
										}
									}
									if ((statementcycle == 'Account') && ((AccNo == '') && (value != ''))) {
										return {
											vaild: false,
											message: 'Enter From Account No'
										}
									}
									/* var statementcycle = $('select[name="statementcycle"]').val();
									 
									 var get_detail = check_accountno(value, statementcycle, '2');
									 if (get_detail != true)
									 {
									 return {
									 valid: false,
									 message: get_detail
									 };
									 } */
									return true;
								}
							}							
						}
					},
					'toaccountweek2': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var last_acc_no = $('.js_patientSettings').val();
									var AccNo = $('.fromaccountweek2').val();
									 var statementcycle = $('select[name="statementcycle"]').val();
									if ((statementcycle == 'Account') && ((AccNo != '') && (value == ''))) {
										return {
											vaild: false,
											message: 'Enter To Account No'
										}
									}
									/*
									if ((statementcycle == 'Account') && ((value < AccNo) || (last_acc_no < value) && (AccNo != '')))
									{
										return {
											vaild: false,
											message: 'Enter Vaild Account No'
										}
									}
									*/
									if ((statementcycle == 'Account') && ((value.length < 5) && (value != '')))
									{
										return {
											vaild: false,
											message: 'Check the Account No'
										}
									}
									if ((statementcycle == 'Account') && ((AccNo == '') && (value != ''))) {
										return {
											vaild: false,
											message: 'Enter From Account No'
										}
									}
									return true;
								}
							}
						}
					},
					'toaccountweek3': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var last_acc_no = $('.js_patientSettings').val();
									var AccNo = $('.fromaccountweek3').val();
									 var statementcycle = $('select[name="statementcycle"]').val();
									if ((statementcycle == 'Account') && ((AccNo != '') && (value == ''))) {
										return {
											vaild: false,
											message: 'Enter To Account No'
										}
									}
									/*
									if ((statementcycle == 'Account') && ((value < AccNo) || (last_acc_no < value) && (AccNo != '')))
									{
										return {
											vaild: false,
											message: 'Enter Vaild Account No'
										}
									}
									*/
									if ((statementcycle == 'Account') && ((value.length < 5) && (value != '')))
									{
										return {
											vaild: false,
											message: 'Check the Account No'
										}
									}
									if ((statementcycle == 'Account') && ((AccNo == '') && (value != ''))) {
										return {
											vaild: false,
											message: 'Enter From Account No'
										}
									}
									return true;
								}
							}
						}
					},
					'toaccountweek4': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var last_acc_no = $('.js_patientSettings').val();
									var AccNo = $('.fromaccountweek4').val();
									 var statementcycle = $('select[name="statementcycle"]').val();
									if ((statementcycle == 'Account') && ((AccNo != '') && (value == ''))) {
										return {
											vaild: false,
											message: 'Enter To Account No'
										}
									}
									/*
									if ((statementcycle == 'Account') && ((value < AccNo) || (last_acc_no < value) && (AccNo != '')))
									{
										return {
											vaild: false,
											message: 'Enter Vaild Account No'
										}
									}
									*/
									if ((statementcycle == 'Account') && ((value.length < 5) && (value != '')))
									{
										return {
											vaild: false,
											message: 'Check the Account No'
										}
									}
									if ((statementcycle == 'Account') && ((AccNo == '') && (value != ''))) {
										return {
											vaild: false,
											message: 'Enter From Account No'
										}
									}
									return true;
								}
							}
						}
					},
					'toaccountweek5': {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator, element) {
									var last_acc_no = $('.js_patientSettings').val();
									var AccNo = $('.fromaccountweek5').val();
									 var statementcycle = $('select[name="statementcycle"]').val();
									if ((statementcycle == 'Account') && ((AccNo != '') && (value == ''))) {
										return {
											vaild: false,
											message: 'Enter To Account No'
										}
									}
									/*
									if ((statementcycle == 'Account') && ((value < AccNo) || (last_acc_no < value) && (AccNo != '')))
									{
										return {
											vaild: false,
											message: 'Enter Vaild Account No'
										}
									}
									*/
									if ((statementcycle == 'Account') && ((value.length < 5) && (value != '')))
									{
										return {
											vaild: false,
											message: 'Check the Account No'
										}
									}
									if ((statementcycle == 'Account') && ((AccNo == '') && (value != ''))) {
										return {
											vaild: false,
											message: 'Enter From Account No'
										}
									}
									return true;
								}
							}
						}
					},					
				}
			});

        function check_accountno(getvalue, cycle, type) {
            if (cycle == 'Account') {
                if (getvalue == '' || getvalue == null) {
                    if (type == '2')
                        return '{{ trans("practice/practicemaster/patientstatementsettings.validation.toaccountno") }}';
                    else
                        return '{{ trans("practice/practicemaster/patientstatementsettings.validation.fromaccountno") }}';
                } else if (/^[a-zA-Z0-9- ]*$/.test(getvalue) == false) {
                    return '{{ trans("common.validation.alphanumeric") }}';
                }
            }
            return true;
        }
    });
</script>
@endpush