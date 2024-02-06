@extends('admin')
@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> APP Settings <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Questionnaires</span></small>
        </h1>
        <ol class="breadcrumb">

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			@if(count(@$questionnaries) > 0)
            <li class="dropdown messages-menu">
                <!--<a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->
                @include('layouts.practice_module_export', ['url' => 'api/questionnaire/templateexport/'])
            </li>
			 @endif 
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/questionnaire_template')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop
@section('practice-info')
@include ('practice/questionnaires/tabs') 
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" ><!-- Col-12 starts -->
    <div class="box no-border no-shadow">

        <div class="box-header">
            <i class="fa fa-bars"></i><h3 class="box-title">Questionnaires List</h3>
            <div class="box-tools pull-right margin-t-2">                            
                @if($checkpermission->check_url_permission('questionnaire/template/create') == 1)
                <a href="{{ url('questionnaire/template/create') }}" class="font14 font600"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Template</a>
                @endif 

            </div>
        </div><!-- /.box-header -->

        <div class="box-body">	<!-- Box Body Starts -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                @if(Session::get('message')!== null) 
                <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
                @endif
            </div>		
            <div class="table-responsive margin-t-20">
                <table id="example1" class="table table-bordered table-striped ">         
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Created By</th>
                            <th>Created On</th>
                            <th>Updated By</th>
                            <th>Updated On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questionnaries as $questionnaries)
                        <?php $questionnaries->template_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($questionnaries->template_id,'encode'); ?>
                        <tr data-url="{{ url('questionnaire/template/'.$questionnaries->template_id) }}" class="js-table-click clsCursor" >
                              <td>{{ @str_limit($questionnaries->title,25) }}</td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($questionnaries->created_by) }}</td>
                            <td> @if($questionnaries->created_at !='' && $questionnaries->created_at !='-0001-11-30 00:00:00' && $questionnaries->created_at !='0000-00-00 00:00:00')
                                {{ App\Http\Helpers\Helpers::dateFormat($questionnaries->created_at,'date') }}
                                @endif</td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($questionnaries->updated_by) }}</td>
                            <td>
                                @if($questionnaries->updated_at !='' && $questionnaries->updated_at !='-0001-11-30 00:00:00' && $questionnaries->updated_at !='0000-00-00 00:00:00')
                                {{ App\Http\Helpers\Helpers::timezone($questionnaries->updated_at, 'm/d/y') }}
                                @endif
                            </td>
                        </tr>
                        @endforeach      
                    </tbody>
                </table>
            </div>                                
        </div><!-- /.box-body ends -->
    </div>
</div><!-- Col-12 Ends -->
<!--End-->
@stop    