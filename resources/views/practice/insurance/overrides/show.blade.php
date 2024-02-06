@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.insurance')}} font14"></i> Insurance <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Overrides <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('insurance/'.$insurance->id.'/insuranceoverrides') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('practice/insurance/insurance_tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
    @if($checkpermission->check_url_permission('insurance/{insurance_id}/insuranceoverrides/{insuranceoverrides}/edit') == 1)
    <a href="{{ url('insurance/'.$insurance->id.'/insuranceoverrides/'.$overrides->id.'/edit') }}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
@endif
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
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
                        <td>Facility</td>
                        <td>{{ @$overrides->facility->facility_name}}</td>                      
                        <td></td>
                    </tr>
                    <tr>
                        <td>Provider Name</td>
                        <td>{{ @$overrides->provider->provider_name }} {{ @$overrides->provider->degrees->degree_name }}</td>                       
                        <td></td>
                    </tr>
                    <tr>
                        <td>Tax ID</td>
                        <td><span class="bg-number">{{ @$overrides->provider->etin_type_number }}</span></td>                       
                        <td></td>
                    </tr>
                    <tr>
                        <td>NPI</td>
                        <td><span class="bg-number">{{ @$overrides->provider->npi }}</span></td>                        
                        <td></td>
                    </tr>
                    <tr>
                        <td>Provider ID</td>
                        <td>@if($overrides->provider_id !='')<span class="bg-number">{{ $overrides->provider_id }}</span> @else &nbsp; @endif <!--<a id="document_add_modal_link_provider_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/insurance::overrides::'.$insurance->id.'/'.$overrides->id.'/provider_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('cssconfigs.common.attachment_view')}} margin-l-10"></i></a>--></td>   
                        <td></td>
                    </tr>
                    <tr>
                        <td>ID Type</td>
                        <td>{{ @$overrides->id_qualifier->id_qualifier_name }}</td>                        
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
   
@stop 