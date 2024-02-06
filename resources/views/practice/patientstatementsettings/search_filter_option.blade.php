{!! Form::open(['url'=>'statementhistoryfilter','id'=>'js_common_search_form','name'=>'search_form']) !!}
<div id="js_claim_search_option">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
        <div class="no-shadow form-horizontal">
            <div class="col-lg-6 col-md-4 col-sm-12 col-xs-12 p-l-0 ">
            <div class="form-group-billing">
                {!! Form::label('Patient', 'Patient',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-7 col-sm-6 col-xs-10 select2-white-popup">
					 {!! Form::select('patient_search',[''=>'-- Select --','1'=>'Patient Name','2'=>'Acc No','3'=>'DOB','4'=>'SSN','5'=>'Gender'],'-- Select --',['class'=>'select2 form-control js_']) !!}
                </div>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                    {!! Form::text('patient_text',null,['maxlength'=>'26','class'=>'form-control input-sm-header-billing']) !!}
                </div>
            </div>
          		  
			<div class="form-group-billing">
                {!! Form::label('Patient Balance', 'Patient Balance',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-3 col-sm-3 col-xs-10 select2-white-popup">
                    {!! Form::select('billed_option', array(''=>'-- Select --','lessthan'=>'<','lessequal'=>'<=','equal'=>'=','greaterthan'=>'>','greaterequal'=>'>='),  null,['class'=>'form-control input-view-border1 select2 js-billed_amount','id'=>'billed_option']) !!}
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                    {!! Form::text('billed',null,['maxlength'=>'26','id'=>'billed','class'=>'form-control dm-copay-amount input-sm-header-billing js-billed_amount','placeholder'=>'Amount']) !!}
                </div>
            </div>
			
			  <div class="form-group-billing">
                {!! Form::label('Type', 'Type',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10 select2-white-popup">
                    {!! Form::select('type', array(''=>'-- Select --','Electronic'=>'Electronic','Paper'=>'Paper'),  null,['class'=>'form-control input-view-border1 select2','id'=>'type']) !!}
                </div>
            </div>
			
		  </div>
		   <div class="col-lg-5 col-md-4 col-sm-12 col-xs-12 p-l-0 ">
			
			 <div id="js_search_date_adj" class="js_date_validation form-group-billing">
                {!! Form::label('Send Statement Date', 'Send Statement Date',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-5 col-md-3 col-sm-3 col-xs-10">
                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing med-gray"></i> 
                    {!! Form::text('sendstatement_from',null,['id'=>'sendstatement_from','class'=>'form-control input-sm-header-billing dm-date datepicker search_start_date ','placeholder'=>'From']) !!}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-3 col-xs-10">
                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing med-gray"></i>
                    {!! Form::text('sendstatement_to',null,['id'=>'sendstatement_to','class'=>'form-control input-sm-header-billing dm-date datepicker search_end_date ','placeholder'=>'To']) !!}
                </div>
            </div>
			
			 <div id="js_search_date_adj" class="js_date_validation form-group-billing">
                {!! Form::label('Pay by Date', 'Pay by Date',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-5 col-md-3 col-sm-3 col-xs-10">
                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing med-gray"></i> 
                    {!! Form::text('paybydate_from',null,['id'=>'paybydate_from','class'=>'form-control input-sm-header-billing dm-date datepicker payby_start_date ','placeholder'=>'From']) !!}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-3 col-xs-10">
                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing med-gray"></i>
                    {!! Form::text('paybydate_to',null,['id'=>'paybydate_to','class'=>'form-control input-sm-header-billing dm-date datepicker payby_end_date ','placeholder'=>'To']) !!}
                </div>
            </div>
			
		   </div>
		    
            <div class="col-lg-1 col-md-4 col-sm-12 col-xs-12 p-l-0">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right p-r-0">
                    <input class="btn btn-medcubics-small" value="Search" type="submit">
                    {!! Form::reset('Reset',["class"=>"btn btn-medcubics-small js_search_reset"]) !!}
                </div>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}