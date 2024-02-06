@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading">Cheat sheet</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('cheatsheet/create') }}"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="New"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="{{ url('api/cheatsheetreports/export') }}"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/cheatsheet')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>           
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-xs-12">
    <div class="box box-info no-shadow">
        <div class="box-header with-border">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Cheatsheet List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Resource</th>
                        <th>Facility</th>
                        <th>Provider</th>
                        <th>Visit Type</th>
                        <th>CPT</th>
                        <th>ICD</th>
                        <th>Claimstatus</th>
                        <th>Feeschedules</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cheatsheet as $cheatsheets)
                    <tr data-url="{{ url('cheatsheet/'.$cheatsheets->id) }}" class="js-table-click clsCursor">
                        <td>@if($cheatsheets->resources){{ $cheatsheets->resources->resource_name }}@endif</td>
                        <td>@if($cheatsheets->facility){{ $cheatsheets->facility->facility_name }}@endif</td>
                        <td>@if($cheatsheets->provider){{ $cheatsheets->provider->first_name.' '.$cheatsheets->provider->last_name }}@endif</td>      
                        <td>{{ $cheatsheets->visit_type_id }}</td>
                        <td>{{ $cheatsheets->cpt }}</td>      
                        <td>{{ $cheatsheets->icd }}</td>
                        <td>{{ $cheatsheets->claimstatus }}</td>
                        <td>{{ $cheatsheets->feeschedules }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!--End-->
@stop 