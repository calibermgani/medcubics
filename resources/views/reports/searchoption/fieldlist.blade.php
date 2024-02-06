<div class="dropdown-menu" aria-labelledby="dropdownMenu2" style="left: -840%;top: -5%;font-size:12px;">                     
  {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator','name'=>'medcubicsform','class'=>'medcubicsform','method'=>'POST']) !!}
	<div class="col-md-12 no-padding js_search_part" style="width:400px;"><!-- Inner Content for full width Starts -->
		<div class="box-body-block med-bg-f0f0f0"><!--Background color for Inner Content Starts -->
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- General Details Full width Starts -->
				<div class="box no-border  no-shadow" ><!-- Box Starts -->
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding med-bg-f0f0f0"><!--  1st Content Starts -->
						
						<div class="col-lg-12 col-md-12 col-sm-12 no-padding margin-t-10">
							<div class="col-lg-10 col-md-4 col-sm-8 col-xs-3 no-padding">
								<input type="text" name="filter_search_keyword" autofocus class="form-control input-sm-modal js_datatable_serach" placeholder="Search..." />
								<input type="hidden" name="current_action" value="{{ $modal_file_path }}"/>
								<input type="hidden" name="view_file" value="{{ Route::currentRouteAction() }}"/>
								<input type="hidden" name="variable_name" value="claims"/>
								<input type="hidden" name="with_table" value="patient,provider_details,rendering_provider,facility_detail,insurance_details,billing_provider"/>
							</div> 
							<div class="col-lg-2 col-md-4 col-sm-8 col-xs-3">
								<input class="btn btn-flat btn-medgreen js_filter_search_submit" value="Refine" type="submit" style="font-size: 12px;padding: 4px 8px;border: none;border-radius: 2px;">
							</div>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 no-padding margin-t-10">
							<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6" style="padding-left:0px;">
								{!! Form::select('patient_search',[''=>'-- Select --','billed_date'=>'Original Billed Date','date_of_service'=>'DOS','paid_date'=>'Paid Date','claim_created_date'=>'Created Date','last_submission'=>'Last Submission'],"claim_created_date",['class'=>'select2 form-control','id'=>"filter_option"]) !!}
							</div>
							<div class="col-lg-4 col-md-4 col-sm-3 col-xs-3 no-padding">
								<label for="From" class="col-lg-5 col-md-2 col-sm-4 col-xs-1 no-padding control-label">From</label>
								<div class="col-lg-7 col-md-4 col-sm-3 col-xs-3 no-padding">
									<i class="fa fa-calendar-o form-icon-billing"></i> 
									<input id="search_start_date" class="form-control input-sm-header-billing datepicker dm-date" type="text">
								</div> 
							</div> 
							<div class="col-lg-4 col-md-4 col-sm-3 col-xs-3 no-padding">
								<label for="To" class="col-lg-5 col-md-2 col-sm-2 col-xs-1 no-padding control-label">&emsp;To</label>
								<div class="col-lg-7 col-md-4 col-sm-3 col-xs-3 no-padding">
									<i class="fa fa-calendar-o form-icon-billing"></i> 
									<input id="search_end_date" class="form-control input-sm-header-billing dm-date datepicker no-padding" type="text">
								</div>                                                    
							</div>                                                    
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 no-padding margin-t-10">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 no-padding">
								{!! Form::select('patient_search',[''=>'-- Facility --','billed_date'=>'Original Billed Date','date_of_service'=>'DOS','paid_date'=>'Paid Date','claim_created_date'=>'Claim Created Date','last_submission'=>'Last Submission'],NULL,['class'=>'select2 form-control','id'=>"filter_option"]) !!}
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
								{!! Form::select('patient_search',[''=>'-- Provider --','billed_date'=>'Original Billed Date','date_of_service'=>'DOS','paid_date'=>'Paid Date','claim_created_date'=>'Claim Created Date','last_submission'=>'Last Submission'],NULL,['class'=>'select2 form-control','id'=>"filter_option"]) !!}
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 no-padding">
								{!! Form::select('patient_search',[''=>'-- Insurance --','billed_date'=>'Original Billed Date','date_of_service'=>'DOS','paid_date'=>'Paid Date','created_date'=>'Created Date','last_submission'=>'Last Submission'],NULL,['class'=>'select2 form-control','id'=>"filter_option"]) !!}
							</div>
						</div>
						<input type="checkbox" id="js_prov_option" name="column_name[]" value="patient_name" class="js_menu flat-red margin-t-10" />
						<label for="Rendering Provider" class="control-label med-green font600 margin-t-10">Provider</label>
						<div class="box-block">
							<div class="box box-body" style="margin: 0px;padding: 0px 0px 10px 10px;">
								<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_prov_option flat-red js_submenu" />Billing</div>
								<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_prov_option flat-red js_submenu" />Rendering </div>
								<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_prov_option flat-red js_submenu" />Referring </div>
								<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_prov_option flat-red js_submenu" />Ordering </div>
								
							</div> 
						</div><!-- /.box-body -->
						<input type="checkbox" id="js_ins_option" name="column_name[]" value="patient_name" class="js_menu flat-red margin-t-10" />
						<label for="Rendering Provider" class="control-label med-green font600 margin-t-10">Insurance</label>
						<div class="box-block">
							<div class="box box-body" style="margin: 0px;padding: 0px 0px 10px 10px;">
								<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_ins_option flat-red js_submenu" />Primary</div>
								<div class="col-lg-5 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_ins_option flat-red js_submenu" />Tertiary</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_ins_option flat-red js_submenu" />Auto Accident</div>
								<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_ins_option flat-red js_submenu" />Secondary</div>
								<div class="col-lg-5 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_ins_option flat-red js_submenu" />Workers compensation</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_ins_option flat-red js_submenu" />Liability</div>
							</div> 
						</div><!-- /.box-body -->
						<input type="checkbox" id="js_bal_option" name="column_name[]" value="patient_name" class="flat-red margin-t-10 js_menu" />
						<label for="Rendering Provider" class="control-label med-green font600 margin-t-10">Balance Option</label>
						<div class="box-block">
							<div class="box box-body" style="margin: 0px;padding: 0px 0px 10px 10px;">
								<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_submenu flat-red js_bal_option" />Paid</div>
								<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_bal_option flat-red js_submenu" />Ins Bal</div>
								<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_bal_option flat-red js_submenu" />Pat Bal </div>
								<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_bal_option flat-red js_submenu" />Partial Paid</div>
								<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="js_bal_option flat-red js_submenu" />Pending</div>
							</div> 
						</div><!-- /.box-body -->
						
						<!--div class="box-block">
							<div class="box box-body" style="margin: 0px;padding: 0px 0px 10px 10px;">
								<!--div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="account_no" class="flat-red" />Acc No</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="patient_name" class="flat-red" />Patient Name</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="claim_number" class="flat-red" />Claim No</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="date_of_service" class="flat-red" />DOS</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="insurance_name" class="flat-red" />Insurance</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="billing_provider_name" class="flat-red" />Billing</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="rendering_provider_name" class="flat-red" />Rendering</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="facility_name" class="flat-red" />Facility</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="total_charge" class="flat-red" />Charges</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="insurance_paid" class="flat-red" />Ins Pmt</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="pateint_paid" class="flat-red" />Pat Pmt</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="total_adjusted" class="flat-red" />Adjustment</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="balance_amt" class="flat-red" />Total Bal</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="status" class="flat-red" />Status
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								{!! Form::select('patient_search',[''=>'-- Select --','billed_date'=>'Original Billed Date','date_of_service'=>'DOS','paid_date'=>'Paid Date','claim_created_date'=>'Claim Created Date','last_submission'=>'Last Submission'],NULL,['class'=>'select2 form-control','id'=>"filter_option"]) !!}</div>
							</div> 
						</div--><!-- /.box-body -->
						<!--label for="User" class="control-label-billing med-green font600">Users</label>
						@php 
							$user_list = App\Models\Medcubics\Users::getActiveUsers();
						@endphp 
						<div class="box-block">
							<div class="box box-body" style="margin: 0px;padding: 0px 0px 10px 10px;">
							@foreach($user_list as $user_id => $username)
								@if(strlen($username)> 10)
									@php  $class = "col-lg-5"; @endphp 
								@else
									@php  $class = "col-lg-3"; @endphp 
								@endif
								<div class=" {{ $class}} col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="user_id[]" class="flat-red" value="{{ $user_id}}" />{{$username}}</div>
							@endforeach
							</div><!-- /.box-body >
						</div><!-- /.box-body -->
						
					</div><!-- /.box -->
				</div><!-- /.box -->
			</div><!-- /.box -->
		</div><!-- /.box -->
	</div><!-- /.box -->
	<input type="hidden" name="listing_id" class="js_set_listing_id" value="all" />
	{!! Form::close() !!}
</div>