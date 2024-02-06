@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
	<?php $customer->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($customer->id,'encode'); ?>
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum"></i><span>View</span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/customer') }}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/customer')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	@include ('admin/customer/tabs')
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">
		 @if($checkpermission->check_adminurl_permission('admin/customer/{customer}/edit') == 1)
			<a href="{{ url('admin/customer/'.$customer->id.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
		 @endif
	</div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="user"></i> <h3 class="box-title">Customer Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table-responsive table-striped-view table">
                        <tbody>
                            <tr>
                                <td>Type</td>
                                <td>{{ @$customer->customer_type }}</td>
                            </tr>
                            <tr>
                                <td>Contact person</td>
                                <td>{{ @$customer->contact_person }}</td>
                            </tr>
                            <tr>
                                <td>Designation</td>
                                <td>{{ @$customer->designation }}</td>
                            </tr>
							 <tr>
                                <td>Gender</td>
                                <td>{{ @$customer->gender }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td><span class="patient-status-bg-form @if(@$customer->status == 'Active')label-success @else label-warning @endif">{{ @$customer->status }}</span></td>
                            </tr>

                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="address-book"></i> <h3 class="box-title">Mailing Address</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table-responsive table-striped-view table">
                        <tbody>
                            <tr>
                                <td>User Name</td>
                                <td>{{ @$customer->lastname }} {{ @$customer->firstname }}</td>
                            </tr>                            <tr>
                                <td>Address Line 1</td>
                                <td>{{ @$customer->addressline1 }}</td>
                            </tr>
                            <tr>
                                <td>Address Line 2</td>
                                <td>{{ @$customer->addressline2 }}</td>
                            </tr>
                            <tr colspan="4">
                                <td>City</td>
                                <td>{{ @$customer->city }} @if(@$customer->state != '') - <span class=" bg-state ">{{ @$customer->state }}</span>@endif</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Zip Code</td>
                                <td>{{ @$customer->zipcode5 }} @if(@$customer->zipcode4 != '')- {{ @$customer->zipcode4 }}@endif</td>
                                <td>
                                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match'],'show'); ?>   
                                   <?php echo $value;?> 
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->
<!-- Modal Light Box starts -->
<div id="form-address-modal" class="modal fade in">
  @include('practice/layouts/usps_form_modal') 
</div><!-- /.modal  Ends-->
@stop
