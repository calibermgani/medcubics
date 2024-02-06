@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1><small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Communication Info</span></small></h1>
        <ol class="breadcrumb">
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- col-12 starts -->
    <div class="box box-info no-shadow"><!-- Box Starts -->
        <div class="box-header margin-b-10">
            <i class="fa fa-bars"></i><h3 class="box-title">Communication Info</h3>
            <div class="box-tools pull-right margin-t-2 hide">

            </div>
        </div><!-- /.box-header -->
        <div class="box-body"><!-- Box body starts -->
            <div class="table-responsive"> 
                <table id="communicationhistory_example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>Date</th>
                            <th>Duration</th>                            
                            <th>Direction</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($communicationhistory as $communicationhistory)
                        @if($communicationhistory !='')
                        <tr class="default-cursor">                            
                            <?php
                                $patientAccountNum  = App\Models\Patients\Patient::singlePatientData($communicationhistory->patient_id)['account_no'];
                            ?>
                            <td>{{ $patientAccountNum }}</td>
                            <td>{{ App\Models\Patients\Patient::getPatientname($communicationhistory->patient_id)}}</td>
                            <td>{{$communicationhistory->created_at}}</td>
                            <td>{{$communicationhistory->duration}}</td>
                            <td>{{$communicationhistory->direction}}</td>
                            <td>{{$communicationhistory->from}}</td>
                            <td>{{$communicationhistory->to}}</td>
                            <td>{{$communicationhistory->com_type}}</td>
                            <td>{{$communicationhistory->status}}</td>
                            <td>{{$communicationhistory->created_by}}</td>
                            <td>{{$communicationhistory->cost}}</td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>

                </table>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!-- col-12 ends -->
<!--End-->
@stop

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $("#communicationhistory_example").DataTable(
                {
                    "columnDefs": [{"orderable": false, "targets": 10}]
                });
    });
</script>
@endpush