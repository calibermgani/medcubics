@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="bank"></i>Insurance <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Favourite</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('listinsurancefavourites') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>  
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			
			@if(count($favourites)>0)
				<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/insfavouritereports/'])
				</li>
			@endif
			
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/insurance/tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info  no-shadow">
        <div class="box-header margin-b-10">
           <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Favourite List</h3>            
        </div><!-- /.box-header -->
        <div class="box-body js_insurances_favourites">
            @include('practice/insurance/favourites_insurance_list')
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
  
<!--End-->
@include('practice/layouts/favourite_modal')
@stop     