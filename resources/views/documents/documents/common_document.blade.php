{!! Form::open(['url' => '/documents/dynamic/filter','onsubmit'=>"event.preventDefault();",'id'=>'document_search_form','class'=>'popupmedcubicsform']) !!}
	<div class="box-body no-border no-padding"> 
        <h4 class="med-green margin-b-15 med-orange">Document Search</h4>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part js_date_validation"  id="js_search_date_adj">
			<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
				<div class="form-group">
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
						{!! Form::label('Document Date', 'Document Date', ['class'=>'control-label']) !!}
						{!! Form::select('date_option', ['' => '-- Select -- ','enter_date' => 'Choose Date','daily' => 'Today','current_month'=>'Current Month','previous_month'=>'Previous Month','current_year'=>'Current Year','previous_year'=>'Previous Year'],null,['class'=>'select2 form-control js_change_date_option']) !!}
					</div>                                                        
				</div> 

				<div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
					<div class="form-group">
						<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
							{!! Form::label('From', 'From', ['class'=>'control-label']) !!}
							{!! Form::text('from_date', null,['class'=>'search_start_date form-control datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format'), 'autocomplete'=> 'off'])  !!}
						</div>   
						
						<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
							{!! Form::label('To', 'To', ['class'=>'control-label']) !!}
							{!! Form::text('to_date', null,['class'=>'search_end_date form-control datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format'), 'autocomplete'=>'off'])  !!}
						</div>  
					</div>
					
					<div class="form-group">
						<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
							<?php $users = (array)$users;?>
							{!! Form::label('User', 'User', ['class'=>'control-label']) !!}
							{!! Form::select('user', [''=>"-- Select --"]+ $users, "", ['class'=>'form-control select2'])  !!}
						</div> 
						
						@if(isset($categories) && !empty($categories))
					
							<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
								{!! Form::label('Category', 'Category', ['class'=>'control-label']) !!} 
								{!! Form::select('category', [''=>"-- Select --"]+(array)$categories, "", ['class'=>'form-control select2'])  !!}
							</div>                        
						@else                        
							<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
								{!! Form::label('module', 'Module', ['class'=>'control-label']) !!}  
								{!! Form::select('document_type', array('' => '-- Select --','facility' => 'Facility','provider' => 'Provider','patients' => 'Patient','group' => 'Group'),null,['class'=>'select2 form-control js_select_module', 'id' => "js-main-module"]) !!} 
							</div>            
						   
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 margin-t-10">
								{!! Form::label('category', 'Category', ['class'=>'control-label']) !!}  
								{!! Form::select('category', array('' => '-- Select --'),null,['class'=>'select2 form-control js_select_category']) !!} 
							</div>
						@endif
					</div>                      
				</div>
			</div>
			<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
					 <div class="form-group">
						<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
							{!! Form::label('Check No', 'Check No', ['class'=>'control-label']) !!}
							{!! Form::text('check_number', null,['class'=>'form-control', 'autocomplete'=>'off'])  !!}
						</div>                                                        
					</div>   
					<div class="form-group">
						<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
							{!! Form::label('Start', 'Check Start Date', ['class'=>'control-label']) !!}
							{!! Form::text('checkdate_start', null,['class'=>'checkdate_start form-control datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
						</div>
						<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
							{!! Form::label('End', 'Check End Date', ['class'=>'control-label']) !!}
							{!! Form::text('checkdate_end', null,['class'=>'checkdate_end form-control datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
						</div>  
					</div>
					
					<div class="form-group">
						<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
							{!! Form::label('Start', 'Check Amount Start', ['class'=>'control-label']) !!}
							{!! Form::text('check_amt_start', null,['class'=>'form-control'])  !!}
						</div>   
						<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
							{!! Form::label('End', 'Check Amount End', ['class'=>'control-label']) !!}
							{!! Form::text('check_amt_end', null,['class'=>'form-control'])  !!}
						</div>    
					</div>
					
					<div class="form-group">
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
						{!! Form::label('File Type', 'File Type', ['class'=>'control-label']) !!}
						{!! Form::select('file_type[]',['','jpg'=>'jpg','pdf'=>'pdf', 'png' => 'png', 'docx' => 'docx', 'xlsx' => 'xlsx', 'xls' => 'xls', 'jpeg'=>'jpeg', 'gif' => 'gif', 'doc' => 'doc', 'csv' => 'csv', 'txt' => 'txt'],null,['class'=>'select2 form-control  ', 'multiple'=>"multiple"]) !!}
					</div>                                                        
				</div> 
				
			</div>
			  <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
				<div class="form-group">
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
						{!! Form::label('Payer', 'Insurance', ['class'=>'control-label']) !!}
						{!! Form::select('insurance', [''=> '-- Select -- ','all'=>'All']+(array)@$insurances,null,['class'=>'select2 form-control  ']) !!}
					</div>                                                        
				</div>
				 <div class="form-group">
					<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
						{!! Form::label('Status', 'Status', ['class'=>'control-label']) !!}
						{!! Form::select('status', [''=> '-- Select -- ','Assigned' => 'Assigned', 'Inprocess' => 'Inprocess', 'Completed' => 'Completed', 'Pending' => 'Pending', 'Review' =>'Review'], "",['class'=>'form-control select2'])  !!}
					</div>  
					 <div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
						{!! Form::label('Assigned to', 'Assigned To', ['class'=>'control-label']) !!}
						{!! Form::select('assigned_to', [''=>"-- Select --"]+$users, "", ['class'=>'form-control select2'])  !!}
					</div>    
				</div> 
				
				@if(!isset($from))
				<div class="form-group">
					 <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
						{!! Form::label('Patient', 'Patient Name', ['class'=>'control-label']) !!}
						{!! Form::select('patient', [''=>"-- Select --"]+(array)$patients, "", ['class'=>'form-control select2'])  !!}
					</div>
				</div>
				@endif
					
				 <div class="form-group">
					<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
						{!! Form::label('Start', 'Followup Date', ['class'=>'control-label']) !!}
						{!! Form::text('followup_start', null,['class'=>'followup_start form-control datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
					</div>   
				 
					<div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
						{!! Form::label('End', 'To', ['class'=>'control-label']) !!}
						{!! Form::text('followup_end', null,['class'=>'followup_end form-control datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
					</div>    
				</div>                          
			 </div>
			 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding text-center">
					<!-- <input class="btn btn-medcubics-small js_document_filter pull-right margin-r-10" value="Search" type="submit"> -->
				 {!! Form::submit('Search', ['class'=>'btn btn-medcubics js-submit-btn margin-t-20','id'=>'frmsubmit']) !!}
			</div>
		</div>
	</div>
 {!!Form::close()!!}