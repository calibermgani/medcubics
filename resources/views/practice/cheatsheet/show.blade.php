@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading">Cheatsheet - <span>{{ $cheatsheet->id }}</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('cheatsheet/'.$cheatsheet->id.'/edit')}}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/cheatsheet')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-md-6 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="hand-right"></i> <h3 class="box-title">General</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body"><!-- Start box-body -->
            <table class="table-responsive table-striped-view table">                    
                <tbody>
                    <tr>
                        <td>Resource</td>
                        <td>{{ $cheatsheet->resources->resource_name }}</td>
                    </tr>
                     <tr>
                        <td>Facility</td>
                        <td>{{ $cheatsheet->facility->facility_name }}</td>
                    </tr>
                    <tr>
                        <td>Provider</td>
                        <td>{{ $cheatsheet->provider->first_name.' '.$cheatsheet->provider->last_name }}</td>
                    </tr>
                    <tr>
                        <td>Visit Type</td>
                        <td>{{ $cheatsheet->visit_type_id }}</td>
                    </tr>
                    <tr>
                        <td>CPT</td>
                        <td>{{ $cheatsheet->cpt }}</td>
                    </tr>
                    <tr>
                        <td>ICD</td>
                        <td>{{ $cheatsheet->icd }}</td>
                    </tr>
                    <tr>
                        <td>Claimstatus</td>
                        <td>{{ $cheatsheet->claimstatus }}</td>
                    </tr>
                    <tr>
                        <td>Feeschedules</td>
                        <td>{{ $cheatsheet->feeschedules }}</td>
                    </tr>
                    
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

@stop 