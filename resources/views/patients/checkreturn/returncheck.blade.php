@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
		<?php  
			$uniquepatientid = $patient_id;	 
		?>
        <h1>
           <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.patient.history')}}"></i> Wallet History <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Return Check</span> </small>
		   
        </h1>
        <ol class="breadcrumb">
			<li><a href={{ url('patients/'.$patient_id.'/patientpayment')}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			

			@include ('patients/layouts/swith_patien_icon')	
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
				@include('layouts.practice_module_export', ['url' => '/patients/'.@$patient_id.'/returncheck/export/'])
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/return_check')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
	 
</div>
@stop

@section('practice-info')
	<?php  $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id,'decode'); ?>
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'no'])
@stop

@section('practice')
	<?php 
		$activetab = 'return check';
 		$id = Route::current()->parameters['id'];
	?>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<!-- Tab Starts  -->	   
		<div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
		<?php
			$countPatientPayment = App\Models\Payments\ClaimInfoV1::countPatientPayment($patient_id);
		?>
		<?php  $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id,'encode'); ?>
			@include ('patients/checkreturn/tab')
		</div>
		<!-- Tab Ends -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding space20">
			<div class="box no-border no-shadow">				
				<div class="box-header">
					<i class="fa fa-bars font14"></i><h3 class="box-title">Return Check</h3>
					<div class="box-tools pull-right margin-t-4">
                @if($checkpermission->check_url_permission('returncheck') == 1)          
					@if($countPatientPayment <> 0)
						<a class="font600 font13 " href="returncheck/create"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Return Check</a> 
					@endif
				@endif      
            </div> 
				</div><!-- /.box-header -->
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-striped ">
						<thead>
							<tr>
								<th>Check No</th>  
								<th>Check Date</th>
								<th>Financial Charges</th>
							</tr>
						</thead>               
						<tbody> 
							 @foreach($returncheck as $returncheck)
								<?php $returncheck->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($returncheck->id,'encode'); ?>
								<tr  @if($checkpermission->check_url_permission('returncheck/{returncheck}') == 1) data-url="{{ url('patients/'.$patient_id.'/returncheck/'.$returncheck->id) }}" @endif class="js-table-click clsCursor">                        
									<td>{{$returncheck->check_no}}</td>
									<td>{{ App\Http\Helpers\Helpers::dateFormat($returncheck->check_date,'date')}}</td>
									<td>{{$returncheck->financial_charges}}</td>
									
								</tr>
								@endforeach                                               
							</tbody>
						</tbody>
					</table>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>
@stop