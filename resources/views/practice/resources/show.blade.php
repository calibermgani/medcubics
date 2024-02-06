@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.resources')}} font14"></i> Resources</small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('resources')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="{{ url('resources/'.$resources->id.'/edit')}}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/resources')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-md-6 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">                    
                    <tbody>
                    <tr>
                        <td>Resource Name</td>
                        <td>{{ $resources->resource_name }}</td>
                    </tr>
                     <tr>
                        <td>Resource Facility</td>
                        <td>{{ @$resources->facility->facility_name}}</td>
                    </tr>
                     <tr>
                        <td>Resource Code</td>
                        <td>{{ $resources->resource_code }}</td>
                    </tr>
                    <tr>
                        <td>Phone Number</td>
                        <td>{{ $resources->phone_number }}</td>
                    </tr>
                    <tr>
                        <td>Default Provider</td>
                        <td>{{ @$resources->provider->provider_name }}</td>
                    </tr>
                    </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

@stop 