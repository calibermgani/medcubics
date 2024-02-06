@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Appeal Address</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @if(count($appealaddress)>0)
                @include('layouts.practice_module_export', ['url' => 'api/insuranceappealaddress/'.$insurance->id.'/'])
            @endif
            <li><a href="{{ url('insurance/'.$insurance->id) }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')

    @include ('practice/insurance/insurance_tabs')  
    @stop

@section('practice')

        <div class="col-lg-12">
        @if(Session::get('message')!== null) 
        <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
        @endif
        </div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
           <i class="fa fa-bars"></i><h3 class="box-title">Appeal Address List</h3>
           <div class="box-tools pull-right margin-t-2">
               @if($checkpermission->check_url_permission('insurance/{insuranceid}/insuranceappealaddress/create') == 1)
               <a href="{{url('insurance/'.$insurance->id.'/insuranceappealaddress/create')}}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Appeal Address</a>  
               @endif	
           </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Address Line 1</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Zipcode</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appealaddress as $appealaddress)
				<?php $appealaddress->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($appealaddress->id,'encode'); ?>
                <tr data-url="{{ url('insurance/'.$insurance->id.'/insuranceappealaddress/'.$appealaddress->id) }}" class="js-table-click clsCursor">
                    <td>{{ $appealaddress->address_1 }}</td>
                    <td>{{ $appealaddress->city }}</td>
                    <td>{{ $appealaddress->state }}</td>
                    <td>{{ $appealaddress->zipcode5.'-'.$appealaddress->zipcode4 }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
  
<!--End-->
@stop   