@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
     <?php 
        $practice_id = $practice->id;
        $practice->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice->id,'encode'); 
    ?>    
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>Practice</span></small>
        </h1>
        <ol class="breadcrumb">
            
            <li><a href="{{ url('admin/customer/'.$customer_id.'/customerpractices') }}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			
			@if($checkpermission->check_adminurl_permission('admin/customer/setpractice/{id}') == 1)	
			<li><a href="javascript:void(0);" id="security-code-generate"><i class="fa {{Config::get('cssconfigs.Practicesmaster.practice')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Go to Practice"></i></a></li>
			@endif

          
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>

            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif

        </ol>
    </section>

</div>
@stop

@section('practice-info')
 {!! Form::model($practice, ['method'=>'PATCH','id'=>'kl','name'=>'myform','files'=>true,'url'=>'practice/'.$practice->id]) !!}
@include ('admin/customer/customerpractices/tabs')
@stop


@section('practice')
<!--1st Data-->

<?php
    $provider_count = App\Models\Practice::getProviderCount($practice->id);
    $facility_count = App\Models\Practice::getFacilityCount($practice->id);
?>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
    @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerpractices/{customerpractices}/edit') == 1)
        <a href="{{ url('admin/customer/'.$customer_id.'/customerpractices/'.$practice->id.'/edit') }}"  class=" pull-right font14 font600 margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
    @endif
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
           <i class="livicon" data-name="briefcase"></i> <h3 class="box-title">Business Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
                    <tbody>
                       
                        <tr>
                            <td>Doing Business As</td>
                            <td>{{ $practice->doing_business_s }}</td>
                        </tr>

                        <tr>
                            <td>Specialty</td>
                            <td>{{ @$practice->speciality_details->speciality }}</td>
                        </tr>

                        <tr>
                            <td>Taxonomy</td>
                            <td><span class="bg-number"/>{{ @$practice->taxanomy_details->code }}</td>
                        </tr>

                        <tr>
                            <td>Billing Entity</td>
                            <td><span class="patient-status-bg-form @if($practice->billing_entity == 'Yes')label-success @else label-danger @endif">{{ $practice->billing_entity }}</span></td>
                        </tr>
						<tr>
                            <td>Entity Type</td>
                            <td>{{ $practice->entity_type }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->


    <div class="box box-view no-shadow js-address-class" id="js-address-pay-to-address"><!--  Box Starts -->
        {!! Form::hidden('pta_address_type','practice',['class'=>'js-address-type']) !!}
        {!! Form::hidden('pta_address_type_id',$practice->id,['class'=>'js-address-type-id']) !!}
        {!! Form::hidden('pta_address_type_category','pay_to_address',['class'=>'js-address-type-category']) !!}
        {!! Form::hidden('pta_address1',$address_flag['pta']['address1'],['class'=>'js-address-address1']) !!}
		
        {!! Form::hidden('pta_city',$address_flag['pta']['city'],['class'=>'js-address-city']) !!}
        {!! Form::hidden('pta_state',$address_flag['pta']['state'],['class'=>'js-address-state']) !!}
        {!! Form::hidden('pta_zip5',$address_flag['pta']['zip5'],['class'=>'js-address-zip5']) !!}
        {!! Form::hidden('pta_zip4',$address_flag['pta']['zip4'],['class'=>'js-address-zip4']) !!}
        {!! Form::hidden('pta_is_address_match',$address_flag['pta']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
        {!! Form::hidden('pta_error_message',$address_flag['pta']['error_message'],['class'=>'js-address-error-message']) !!}
        <div class="box-header-view">
           <i class="livicon" data-name="message-out"></i> <h3 class="box-title">Pay to Address</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
                <tbody>
                    <tr>
                        <td>Address Line 1</td>
                        <td>{{ $practice->pay_add_1 }}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Address Line 2</td>
                        <td>{{ $practice->pay_add_2 }}</td>
                        <td></td>
                    </tr>

					<tr>
                        <td>City</td>
                        <td>{{ $practice->pay_city }} @if($practice->pay_state != '') - <span class=" bg-state ">{{ $practice->pay_state }}</span>@endif</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Zip Code</td>
                        <td>{{ $practice->pay_zip5 }}  @if($practice->pay_zip4 != '') - {{ $practice->pay_zip4 }}@endif</td>
                        <td>
                             <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['pta']['is_address_match'], 'show'); ?>
                             <?php echo $value;?>                                
                        </td>
                    </tr>

                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->


    <div class="box box-view no-shadow js-address-class" id="js-address-primary-address"><!--  Box Starts -->
        {!! Form::hidden('pa_address_type','practice',['class'=>'js-address-type']) !!}
		{!! Form::hidden('pa_address_type_id',$practice->id,['class'=>'js-address-type-id']) !!}
		{!! Form::hidden('pa_address_type_category','primary_address',['class'=>'js-address-type-category']) !!}
		{!! Form::hidden('pa_address1',$address_flag['pa']['address1'],['class'=>'js-address-address1']) !!}
		{!! Form::hidden('pa_city',$address_flag['pa']['city'],['class'=>'js-address-city']) !!}
		{!! Form::hidden('pa_state',$address_flag['pa']['state'],['class'=>'js-address-state']) !!}
		{!! Form::hidden('pa_zip5',$address_flag['pa']['zip5'],['class'=>'js-address-zip5']) !!}
		{!! Form::hidden('pa_zip4',$address_flag['pa']['zip4'],['class'=>'js-address-zip4']) !!}
		{!! Form::hidden('pa_is_address_match',$address_flag['pa']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
		{!! Form::hidden('pa_error_message',$address_flag['pa']['error_message'],['class'=>'js-address-error-message']) !!}
        <div class="box-header-view">
           <i class="livicon" data-name="mail"></i> <h3 class="box-title">Primary Location</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
                    <tbody>
                        <tr>
                            <td>Address Line 1</td>
                            <td>{{ $practice->primary_add_1 }}</td>
                            <td colspan="2"></td>
                        </tr>

                        <tr>
                            <td>Address Line 2</td>
                            <td>{{ $practice->primary_add_2 }}</td>
                            <td colspan="2"></td>
                        </tr>

						<tr>
                            <td>City</td>
                            <td>{{ $practice->primary_city }} @if($practice->primary_state != '') - <span class=" bg-state ">{{ $practice->primary_state }}</span>@endif</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>Zip Code</td>
                            <td>{{ $practice->primary_zip5 }} @if($practice->primary_zip4 != '') - {{ $practice->primary_zip4 }} @endif</td>
                            <td>
                                 <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['pa']['is_address_match'], 'show'); ?>
                                 <?php echo $value;?>
                            </td>
                        </tr>

                    </tbody>
                </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->



    <div class="box box-view no-shadow"><!--  Box Starts -->
               <div class="box-header-view">
           <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
                    <tbody>
                        <tr>
                            <td>Practice Start Date</td>
                            <td><span class="bg-date"/>{{ App\Http\Helpers\Helpers::dateFormat($practice->created_at,'date')}}</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>Primary Language</td>
                            <td>{{ @$practice->languages_details->language }}</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>Providers</td>
                            <td><span class="bg-number">{{$provider_count}}</td>
                            <td class="med-green">Locations: <span>{{ $facility_count }}</span></td>
                        </tr>
                        <tr>
                            <td>Time Zone</td>
                            <td>{{ $practice->timezone }}</td>
                            <td></td>
                        </tr>
						
                    </tbody>
                </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->

</div><!--  Left side Content Ends -->


<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
           <i class="livicon" data-name="inbox"></i> <h3 class="box-title">Credentials</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
                <tbody>
                    @if($practice->entity_type == 'Individual')
                    <tr>
                        <td>Tax ID</td>
                        <td><span class="bg-number">{{ $practice->tax_id }}</span></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>NPI</td>
                        <td><span class="bg-number">{{ $practice->npi }}</span></td>
                        <td>
                             <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi'] , 'induvidual'); ?>
                             <?php echo $value;?>                             
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td>Group Tax ID</td>
                        <td><span @if($practice->group_tax_id != "")class="bg-number" @endif>{{ $practice->group_tax_id }}</span></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Group NPI</td>
                        <td><span @if($practice->group_npi != "")class="bg-number" @endif>{{ $practice->group_npi }}</span></td>
                        <td>
                          <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi']); ?>
                          <?php echo $value;?>                            
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td>Medicare PTAN</td>
                        <td>{{ $practice->medicare_ptan }}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Medicaid ID</td>
                        <td><span @if($practice->medicaid != "")class="bg-number" @endif>{{ $practice->medicaid }}</span></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>BCBS ID</td>
                        <td><span @if($practice->bcbs_id != "")class="bg-number" @endif>{{ $practice->bcbs_id }}</span></td>
                        <td></td>
                    </tr>

                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->


    <div class="box box-view no-shadow js-address-class" id="js-address-mailling-address"><!--  Box Starts -->
      {!! Form::hidden('ma_address_type','practice',['class'=>'js-address-type']) !!}
        {!! Form::hidden('ma_address_type_id',$practice->id,['class'=>'js-address-type-id']) !!}
        {!! Form::hidden('ma_address_type_category','mailling_address',['class'=>'js-address-type-category']) !!}
        {!! Form::hidden('ma_address1',$address_flag['ma']['address1'],['class'=>'js-address-address1']) !!}
        {!! Form::hidden('ma_city',$address_flag['ma']['city'],['class'=>'js-address-city']) !!}
        {!! Form::hidden('ma_state',$address_flag['ma']['state'],['class'=>'js-address-state']) !!}
        {!! Form::hidden('ma_zip5',$address_flag['ma']['zip5'],['class'=>'js-address-zip5']) !!}
        {!! Form::hidden('ma_zip4',$address_flag['ma']['zip4'],['class'=>'js-address-zip4']) !!}
        {!! Form::hidden('ma_is_address_match',$address_flag['ma']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
        {!! Form::hidden('ma_error_message',$address_flag['ma']['error_message'],['class'=>'js-address-error-message']) !!}
        <div class="box-header-view">
           <i class="livicon" data-name="mail"></i> <h3 class="box-title">Mailing Address</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table-responsive table-striped-view table">
                    <tbody>
                        <tr>
                            <td>Address Line 1</td>
                            <td>{{ $practice->mail_add_1 }}</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>Address Line 2</td>
                            <td>{{ $practice->mail_add_2 }}</td>
                            <td></td>
                        </tr>

						<tr>
                            <td>City</td>
                            <td>{{ $practice->mail_city }} @if($practice->mail_state != '') - <span class=" bg-state ">{{ $practice->mail_state }}</span>@endif</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>Zip Code</td>
                            <td>{{ $practice->mail_zip5 }} @if($practice->mail_zip4 != '') - {{ $practice->mail_zip4 }} @endif</td>
                            <td>
                                <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['ma']['is_address_match'], 'show'); ?>
                                <?php echo $value;?>
                            </td>

                        </tr>

                    </tbody>

                </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
	<div class="box box-view no-shadow"><!--  Box Starts -->
               <div class="box-header-view">
           <i class="livicon" data-name="globe"></i> <h3 class="box-title">Host credentials</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
                <tbody>
                    <tr>
                        <td>Host Username</td>
                        <td>{{@$practice->hostname}}</td>
                        <td></td>
                    </tr> 
					<tr>
                        <td>Host IP Address</td>
                        <td>{{@$practice->ipaddress}}</td>
                        <td></td>
                    </tr>
					<tr>
                        <td>Host IP Password</td>
                        <td>{{@$practice->hostpassword}}</td>
                        <td></td>
                    </tr>                    
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->	

 
    @include ('practice/layouts/npi_form_fields')
@include ('practice/layouts/npi_form_modal')
</div><!-- Right side Content Ends -->

  


<!-- Modal Light Box starts -->
<div id="form-pta-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div>
<!-- Modal Light Box Ends -->
<div id="security-code" class="modal fade in ">
    <div class="modal-sm-usps">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close js_usps_add_modal_close_btn" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Security Code Check</h4>
            </div>
            <div class="modal-body">
                    <div class="form-group">                        
                        <input type="text" name="code" class="form-control" placeholder="Enter Security code" />
                    </div>
                    <div class="form-group">
                        <p class="btn btn-medcubics">Submit</p>
                    </div>
            </div>

        </div><!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@stop
@push('view.scripts')
<script type="text/javascript">
    $('#security-code-generate').on('click', function(){
        $.ajax({
            data:{customer_id:"{{$practice->customer_id}}"},
            url:"{{url('api/practice/security_code/'.$practice->id)}}",
            method:'POST',
            success:function(result){
                if(result=="Yes"){
                    $("#security-code").modal();
                    js_sidebar_notification('success','Security code successfully sent to email');
                }
                else{
                    js_sidebar_notification('error','Internal server error');
                }
            }
        })
    });
    $('#security-code .btn').on('click', function(){
        $.ajax({
            data:{security_code:$("input[name='code']").val(),practice_id:"{{$practice->id}}"},
            url:"{{ url('api/set_practice') }}",
            method:'POST',
            success:function(result){
                console.log(result);
                if(result=="Yes"){
                    window.location.href="{{url('analytics/practice')}}";
                } else {
                    js_sidebar_notification('error','Invalid security code');
                }
            }
        })
    });
</script>
@endpush