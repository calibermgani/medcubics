@extends('admin')
@section('toolbar')
<?php
try {
    ?>
    <?php 
		$id = Route::current()->parameters['id'];
    	$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patients->id,'encode');  
	?>
    <div class="row toolbar-header"><!-- Toolbar Starts -->
        <section class="content-header">
            <h1><small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Visits <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Appointments </span> </small></h1>
            <ol class="breadcrumb">

                <?php $uniquepatientid = $patients->id; ?>
                
                @include ('patients/layouts/swith_patien_icon')

                <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

                @if(count(@$patient_appointment) > 0 )
                <li class="dropdown messages-menu">
                    <!--<a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->

                    @include('layouts.practice_module_stream_export', ['url' => 'api/patientappointmentreports/'.$patient_id.'/'])
                </li>
                @endif
                <li><a href="{{App\Http\Helpers\Helpers::patientBackButton($patient_id)}}" accesskey="b" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
              
                <li><a href="" data-target="javascript:void(0)" data-url="{{url('help/appointments')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            </ol>
        </section>
    </div><!-- Toolbar Ends -->
    <?php
} catch (Exception $e) {
    \Log::info( "Error" . $e->getMessage());
}
?>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patients->id,'needdecode'=>'no'])
@stop

@section('practice')

@include ('patients/billing/model-inc')
<?php 
	$activetab = 'patientappointments'; 
	$routex = explode('.',Route::currentRouteName());
	$bill_cycle = @$patients->bill_cycle;
	$statement_type = ( isset($patients->statements) && $patients->statements == 'Yes' ) ?  "Paper": "-";
?>  

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Full width Starts -->
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
           
            @if($checkpermission->check_url_permission('scheduler/scheduler') == 1)
                @if($checkpermission->check_url_permission('patients/{id}/appointments') == 1)
					<li class="@if($activetab == 'patientappointments') active @endif"><a href="{{ url('patients/'.$id.'/appointments') }}" ><i class="fa fa-bars i-font-tabs"></i> Appo<span class="text-underline">i</span>ntments</a></li>
                @endif
            @endif
            
            <li class="@if($activetab == 'charges_list') active @endif"><a href="{{ url('patients/'.$id.'/billing') }}" accesskey="m"><i class="fa fa-bars i-font-tabs"></i> Clai<span class="text-underline">m</span>s</a></li>
        </ul>
    </div> 

    <div class="box-info no-shadow margin-t-10">

        <div class="box-body form-horizontal  bg-white">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-13">
                    <span class=" med-orange margin-l-10 font13 padding-0-4 font600">&emsp;</span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">                                                         
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive margin-b-5">
                        <table class="popup-table-wo-border table margin-b-1">                    
                            <tbody>
                                <tr>
                                    <td class="font600" style="width:50%">Total Appointments</td>
                                    <td><span class="font600"> {!! @$patients->stats->total_appointment !!}</span></td> 
                                </tr>                            
                                <tr>
                                    <td class="font600">Scheduled</td>
                                    <td><span class="font600">{!! @$patients->stats->scheduled !!}</span></td>
                                </tr>
                                <tr>
                                    <td class="font600">Canceled</td>
                                    <td><span class="font600">{!! @$patients->stats->canceled !!}</span></td>
                                </tr>  
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive tab-l-b-1 p-l-0  md-display tabs-lightgreen-border">
                        <table class="popup-table-wo-border table margin-b-1">                    
                            <tbody>      
                                <tr>
                                    <td class="font600">No Show</td>
                                    <td class="font600">{!! @$patients->stats->no_show !!}</td>
                                </tr>
                                <tr>                                               
                                    <td class="font600">Encounters</td>
                                    <td class="med-orange font600">{!! @$patients->stats->encounter !!} </td>
                                </tr>
                                <tr>
                                    <td class="font600">Complete</td>
                                    <td class="font600">{!! @$patients->stats->complete !!} </td>                                              
                                </tr>                                                 
                            </tbody>
                        </table>
                    </div> 
					<?php /*	
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive p-l-0 tab-l-b-1  md-display tabs-lightgreen-border">
                        <table class="popup-table-wo-border table margin-b-1">                    
                            <tbody>
                                <tr>
                                    <td class="font600">Payment Type</td>
                                    <td class="med-orange font600 text-right"> {!! @$patients->stats->payment_type !!}</td>
                                </tr>
                                <tr>
                                    <td class="font600">Statement Type</td>
                                    <td class="text-right">{!! @$statement_type !!} </td>                                              
                                </tr>
                                <tr>
                                    <td class="font600">Bill Cycle</td>
                                    <td class="text-right">{!! @$patients->bill_cycle !!}</td>                                              
                                </tr>

                            </tbody>
                        </table>
                    </div> 
					*/ ?>					
                </div>
            </div>
        </div>

    </div>
    <div class="box-info no-shadow margin-t-10"><!-- Box Starts here -->
        <div class="box-header">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">List</h3> 
            <div class="box-tools pull-right margin-t-4">
                @if($checkpermission->check_url_permission('scheduler') == 1)          
					<a class="font600 font13 js_scheduler_arg" accesskey="a" data-url="{{url('scheduler/scheduler?popup&id='.@$patients->id)}}" href="{{url('scheduler/scheduler?popup&id='.@$patients->id)}}" target="_blank"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Appointment</a> 
                @endif      
            </div>         
        </div>


        <div class="box-body bg-white p-b-0">
            <div class="table-responsive" id="js_table_search_listing">
                @include('patients/appointments/appointments_list')
            </div>
        </div>
    </div><!-- Box Ends Here --> 
</div><!-- Full width Ends -->
@stop

@push('view.scripts')
<script>
    
</script>
@endpush