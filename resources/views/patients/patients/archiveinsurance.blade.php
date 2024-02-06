@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-user font14"></i> Patients <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Insurance Archive</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a style="cursor:pointer;" onClick="window.open('{{url('/patients/create')}}', '_blank')"> <i class="fa {{Config::get('cssconfigs.common.plus_circle')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add Patient"></i></a></li>    
            <?php $uniquepatientid = $patientid = $id; ?>	

            @include ('patients/layouts/swith_patien_icon')	

            @if(count($archiveinsurance)>0)
            <li class="dropdown messages-menu">
                @include('layouts.practice_module_stream_export', ['url' => 'patients/api/archiveinsurancereport/'.$patientid.'/'])
            </li>
            @endif

            <li><a href="#js-help-modal" data-url="{{url('help/patients')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patientid,'needdecode'=>'yes'])
@stop


@section('practice')

<div class="col-md-12 margin-t-m-13">
    <div class="med-tab nav-tabs-custom no-bottom">
        <ul class="nav nav-tabs">
            @if($checkpermission->check_url_permission('patients/{id}') == 1)
            <li><a class= "js_next_process" href="{{ url('patients/'.@$patientid.'/edit') }}" accesskey="g"><i class="fa fa-info-circle i-font-tabs"></i> Demo<span class="text-underline">g</span>raphic</a></li>
            <li><a class= "js_next_process" href="{{ url('patients/'.@$patientid.'/edit/insurance') }}" accesskey="i"><i class="fa fa-institution i-font-tabs"></i> <span class="text-underline">I</span>nsurance</a></li>
            <li><a class= "js_next_process" href="{{ url('patients/'.@$patientid.'/edit/contact') }}" accesskey="c"><i class="fa fa-book i-font-tabs"></i> <span class="text-underline">C</span>ontacts</a></li>
            <li><a class= "js_next_process" href="{{ url('patients/'.@$patientid.'/edit/authorization') }}" accesskey="z"><i class="fa fa-shield i-font-tabs"></i> Authori<span class="text-underline">z</span>ation</a></li>
            @endif	

            @if($checkpermission->check_url_permission('patients/{id}/archiveinsurance') == 1)
            <li class="active"><a href="{{ url('patients/'.@$patientid.'/archiveinsurance') }}" accesskey="e"><i class="fa fa-institution i-font-tabs"></i> Insurance Archive</a></li>
            @endif

        </ul>
    </div>
</div> 
<?php $status = ($patientdetails->is_self_pay == 'Yes') ? 'disabled' : 'enabled'; ?>	
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
    <div class="box box-info no-shadow">

        <div class="box-block-header margin-b-10">
            <i class="fa fa-bars font14"></i><h3 class="box-title">Insurance Archive List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->

        <div class="box-body  form-horizontal margin-l-10">

            <div class="table-responsive">
                <table id="list_noorder" class="table table-bordered table-separate l-green-b ">         
                    <thead>
                        <tr>
                            <th class="med-green font600" style="background: #96dcd8;color: #00877f !important">Insurance</th>
                            <th class="med-green font600" style="background: #96dcd8;color: #00877f !important">Category</th>            
                            <th class="med-green font600" style="background: #96dcd8;color: #00877f !important">Insured</th>
                            <th class="med-green font600" style="background: #96dcd8;color: #00877f !important">Policy ID</th>
                            <th class="med-green font600" style="background: #96dcd8;color: #00877f !important">From / To</th>
                            <th class="med-green font600" style="background: #96dcd8;color: #00877f !important">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archiveinsurance as $archiveinsurance_val)
                        <?php 
						$ins_arc_from = App\Http\Helpers\Helpers::dateFormat(@$archiveinsurance_val->active_from,'date');
						$ins_arc_to   = App\Http\Helpers\Helpers::dateFormat(@$archiveinsurance_val->active_to,'date');  
						@$archiveinsurance_val->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$archiveinsurance_val->id,'encode');
						?>
                        <tr style="cursor:default">
                            <td>{{ @$archiveinsurance_val->insurance_details->insurance_name }}</td>        
                            <td>{{ @$archiveinsurance_val->category }}</td>
                            <td>{{ @$archiveinsurance_val->relationship }}</td>
                            <td>{{ @$archiveinsurance_val->policy_id }}</td>
                            <td class="med-green">@if(@$archiveinsurance_val->active_from !='0000-00-00 00:00:00')
                                [ {{ @$ins_arc_from }} To {{ @$ins_arc_to }} ] @else -  @endif </td>

                            <td class="{{ $status }}"><button type="button" class="btn btn-medcubics-small js_move_archiveins" data-toggle="modal" data-target="#js_move_insurance_model" style="margin:2px;" data-url = "{{ url('patients/'.@$patientid.'/movearchiveinsurance/'.@$archiveinsurance_val->id) }}"> Move</button> </td>

                        </tr>
                        @endforeach      
                    </tbody>
                </table>
            </div>       
        </div><!-- Box Body Ends --> 
    </div><!-- Box Ends -->
</div>
@stop
@push('view.scripts') 
<script type="text/javascript">
    $(document).ready( function () {
        $("#js_move_insurance_model").hide();
        var table = $('#list_noorder').DataTable(); 
        table.on( 'draw', function () {
            var body = $( table.table().body() ); 
            body.unhighlight();
            body.highlight( table.search() );  
        });
    });
</script>
@endpush