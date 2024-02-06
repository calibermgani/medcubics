@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php $insurance->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($insurance->id,'encode'); ?>
<?php $overrides->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($overrides->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.insurance')}}" data-name="bank"></i>Insurance <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Overrides <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/insurance/'.$insurance->id.'/insuranceoverrides') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            
           
            
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
            
        </ol>
    </section>

</div>
@stop


@section('practice-info')
@include ('admin/insurance/insurance_tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">                  
 
     @if($checkpermission->check_adminurl_permission('admin/insurance/{insurance_id}/insuranceoverrides/{insuranceoverrides}/edit') == 1)
            <a href="{{ url('admin/insurance/'.$insurance->id.'/insuranceoverrides/'.$overrides->id.'/edit') }}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
            @endif        
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
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
                        <td>Facility</td>
                        <td>{{ @$overrides->facility->facility_name}}</td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>Provider Name</td>
                        <td>{{ $overrides->provider->provider_name }} {{ $overrides->provider->degrees->degree_name }}</td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>Tax ID</td>
                        <td><span @if(@$overrides->provider->etin_type_number != "") class="bg-number" @endif>{{ $overrides->provider->etin_type_number }}</span></td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>NPI</td>
                        <td><span @if(@$overrides->provider->npi != "") class="bg-number" @endif>{{ $overrides->provider->npi }}</span></td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>Provider ID</td>
                        <td><span @if(@$overrides->provider_id  != "") class="bg-number" @endif>{{ $overrides->provider_id }}</td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>ID Type</td>
                        <td>{{ $overrides->id_qualifier->id_qualifier_name }}</td>
                        <td></td> 
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

@stop 