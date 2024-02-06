<div class="dropdown-menu" aria-labelledby="dropdownMenu2" style="left: -840%;top: -5%;font-size:12px;">                     
  {!! Form::open(['onsubmit'=>"event.preventDefault();", 'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform', 'class'=>'medcubicsform', 'method'=>'POST']) !!}
	<div class="col-md-12 no-padding" style="width:400px;"><!-- Inner Content for full width Starts -->
		<div class="box-body-block med-bg-f0f0f0"><!--Background color for Inner Content Starts -->
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- General Details Full width Starts -->
				<div class="box no-border no-shadow" ><!-- Box Starts -->
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding med-bg-f0f0f0"><!--  1st Content Starts -->
						
						<div class="col-lg-12 col-md-12 col-sm-12 no-padding margin-t-10">
							<div class="col-lg-10 col-md-4 col-sm-8 col-xs-3 no-padding">
								<input type="text" name="filter_search_keyword" autofocus class="form-control input-sm-modal js_datatable_serach" placeholder="Search..." />
								<input type="hidden" name="current_action" value="{{ @$modal_file_path }}"/>
								<input type="hidden" name="view_file" value="{{ Route::currentRouteAction() }}"/>
								<input type="hidden" name="variable_name" value="employers"/>
							</div> 
							<div class="col-lg-2 col-md-4 col-sm-8 col-xs-3">
								<input class="btn btn-flat btn-medgreen js_filter_search_submit" value="Refine" type="submit" style="font-size: 12px;padding: 4px 8px;border: none;border-radius: 2px;">
							</div>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 no-padding margin-t-10">
							<div class="col-lg-10 col-md-4 col-sm-8 col-xs-3 no-padding">
								<label for="From" class="col-lg-2 col-md-2 col-sm-4 col-xs-1 no-padding control-label">From</label> 
								<div class="col-lg-4 col-md-8 col-sm-8 col-xs-3">
									<i class="fa fa-calendar-o form-icon-billing"></i> 
									<input id="search_start_date" class="form-control input-sm-header-billing datepicker dm-date" type="text">
								</div> 
								<label for="To" class="col-lg-1 col-md-2 col-sm-2 col-xs-1 no-padding control-label">To&emsp;</label>
								<div class="col-lg-4 col-md-8 col-sm-8 col-xs-3">
									<i class="fa fa-calendar-o form-icon-billing"></i> 
									<input id="search_end_date" class="form-control input-sm-header-billing dm-date datepicker no-padding" type="text">
								</div> 
							</div>                                                        
						</div>
						<label for="Rendering Provider" class="control-label-billing med-green font600">Filter By</label>
						<div class="box-block">
							<div class="box box-body" style="margin: 0px;padding: 0px 0px 10px 10px;">
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="employer_name" class="flat-red" />Employer Name</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="contact_person" class="flat-red" />Contact Person</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="description" class="flat-red" />Description</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="address" class="flat-red" />Address</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="contact_number" class="flat-red" />Phone/Fax</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="email" class="flat-red" />Email</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
								<input type="checkbox" name="column_name[]" value="designation" class="flat-red" />Designation</div>
							</div> 
						</div><!-- /.box-body -->
						<label for="User" class="control-label-billing med-green font600">Users</label>
						<?php 
							$user_list = App\Models\Medcubics\Users::getActiveUsers();
						?>
						<div class="box-block">
							<div class="box box-body" style="margin: 0px;padding: 0px 0px 10px 10px;">
							@foreach($user_list as $user_id => $username)
								<?php  $class = (strlen($username)> 10) ? "col-lg-5" : "col-lg-3"; ?>
								<div class=" {{ $class}} col-md-4 col-sm-4 col-xs-12 no-padding margin-t-10">
									<input type="checkbox" name="user_id[]" class="flat-red" value="{{ $user_id}}" />{{$username}}
								</div>
							@endforeach
							</div><!-- /.box-body -->
						</div><!-- /.box-body -->
						
					</div><!-- /.box -->
				</div><!-- /.box -->
			</div><!-- /.box -->
		</div><!-- /.box -->
	</div><!-- /.box -->
	<input type="hidden" name="listing_id" class="js_set_listing_id" value="all" />
	{!! Form::close() !!}
</div>