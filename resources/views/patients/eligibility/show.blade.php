@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.speciality')}}" data-name="checked-on"></i> Eligibility <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Show  Benefit verification</span></small>
        </h1>
        <ol class="breadcrumb">
			<li><a href="{{url('patients/'.$patient_id.'/eligibility')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> 
			<?php $uniquepatientid = $patient_id; ?>	
			
			@include ('patients/layouts/swith_patien_icon')
			 
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/eligibility')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$eligibility->patients_id,'needdecode'=>'no'])
@stop

@section('practice')

@if($eligibility->is_manual_atatched == 1)
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">
	   @if($checkpermission->check_url_permission('patients/{patientid}/eligibility/{eligibility}/edit') == 1)
		<a href="{{url('patients/'.$patient_id.'/eligibility/'.$eligibility->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
		@endif	
	</div>
@endif


<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
	<div class="box box-view no-shadow"><!--  Box Starts -->
		<div class="box-header-view">
			<i class="livicon" data-name="anchor"></i> <h3 class="box-title">Template Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body"><!-- Box Body Starts -->
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<span class="med-green font600"> DOS From</span>
				</div>

				<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
					<p>@if($eligibility->dos_from !="0000-00-00") {{ App\Http\Helpers\Helpers::dateFormat($eligibility->dos_from,'dob') }} @endif</p>
				</div>                               
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<span class="med-green font600"> DOS To</span>
				</div>

				<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
					<p>@if($eligibility->dos_to !="0000-00-00") {{ App\Http\Helpers\Helpers::dateFormat($eligibility->dos_to,'dob') }} @endif</p>
				</div>                               
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<span class="med-green font600"> Insurance</span>
				</div>

				<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
					<p>{{ @$eligibility->insurance_details->insurance_name }}</p>
				</div>                               
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<span class="med-green font600"> Provider</span>
				</div>

				<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
					<p>{{ @$eligibility->provider->provider_name.' '.@$eligibility->provider->degrees->degree_name }} <span class="med-orange">{{ @$eligibility->provider->short_name }} </span></p>
				</div>                               
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<span class="med-green font600"> Facility</span>
				</div>

				<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
					<p>{{ @$eligibility->facility->facility_name }}</p>
				</div>                               
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<span class="med-green font600"> Document</span>
				</div>

				<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
					<a target = "_blank" href= "{{ url(@$eligibility->bv_file_path) }}"><i class="fa {{Config::get('cssconfigs.patient.file_text')}} cur-pointer" data-toggle="tooltip" data-original-title="View more" data-placement="bottom"></i></a>
				</div>                               
			</div>
		</div><!-- Box Body Ends --> 
	</div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
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
                        <td>Created On</td>
						<td>@if($eligibility->created_at !='' && $eligibility->created_at !='-0001-11-30 00:00:00' && $eligibility->created_at !='0000-00-00 00:00:00')<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($eligibility->created_at,'date')}}</span>@endif</td>
                    </tr>
                    <tr>
                        <td>Created By</td>
                        <td>@if($eligibility->created_by != ''){{ App\Http\Helpers\Helpers::shortname($eligibility->created_by) }}@endif</td>
                    </tr>
                    <tr>
                        <td>Updated On</td>
                        <td>@if($eligibility->updated_at !='' && $eligibility->updated_at !='-0001-11-30 00:00:00' && $eligibility->updated_at !='0000-00-00 00:00:00' && !empty($eligibility->userupdate))<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($eligibility->updated_at,'date')}}</span>@endif</td>
                    </tr>
                    <tr>
                        <td>Updated By</td>
                        <td>@if(@$eligibility->updated_by != ''){{ App\Http\Helpers\Helpers::shortname($eligibility->updated_by) }}@endif</td>
                    </tr>
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

@stop