@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Medical History</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <?php $uniquepatientid = $patientid = $id; ?>	
            <?php /*@include ('patients/layouts/patientstatement_icon')	*/?>

            @include ('patients/layouts/swith_patien_icon')	

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

            @if(count((array)$questionaries)>0)
            <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'patients/api/questionnairesreport/'.$patientid.'/'])
            </li>
            @endif

            <li><a href="#js-help-modal" data-url="{{url('help/medical_history')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patientid,'needdecode'=>'yes'])
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
    <div class="box box-info no-shadow">

        <div class="box-header">
            <i class="fa {{Config::get('cssconfigs.common.nav')}} font14"></i><h3 class="box-title">Medical History List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>	
        </div><!-- /.box-header -->

        <!-- form start -->
        <div class="box-body  form-horizontal margin-l-10">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped ">         
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Answer Type</th>            
                            <th>Answer</th>
                            <th>User</th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questionaries as $answer)
                        <tr style="cursor:default">
                            <td>{{ $answer->questionnaries_template->question }}</td>        
                            <td>{{ $answer->questionnaries_template->answer_type }}</td>
                            @if($answer->questionnaries_template->answer_type == 'checkbox' or $answer->questionnaries_template->answer_type == 'radio')
                            <td>{{ App\Http\Controllers\Patients\Api\PatientApiController::getQuestionnariesOption(@$answer->questionnaries_option_id)  }}</td>
                            @else
                            <td>{{ $answer->answer }}</td>   
                            @endif
                            <td>{{ App\Http\Helpers\Helpers::shortname($answer->created_by) }}</td> 	
                            <td>{{ App\Http\Helpers\Helpers::timezone(@$answer->created_at, 'm/d/y') }}</td> 
                        </tr>
                        @endforeach      
                    </tbody>
                </table>
            </div>       
        </div><!-- Box Body Ends --> 
    </div><!-- Box Ends -->
</div>	
@stop