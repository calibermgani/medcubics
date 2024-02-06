@extends('admin')

@section('toolbar')
<style type="text/css">
    .text-right{
        text-align: right !important;
    }
</style>
<div class="row toolbar-header"><!-- Toolbar Row Starts -->
    <section class="content-header">
        <h1><small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> CPT / HCPCS <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> CPT / HCPCS Master</span></small></h1>
        <ol class="breadcrumb">
            <?php /*
            <!--li><a href="{{ url('cpt/create') }}"><i class="fa fa-plus-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></a></li>-->
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <!--li class="dropdown messages-menu"><a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/cptreports/'])
            </li-->
            */ ?>
            <li><a href="{{ url('listfavourites') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="" data-target="#js-help-modal"  data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Row Ends -->
@stop

@section('practice-info')
@include ('practice/claimsintegrity/tabs', array('data' => 'active'))
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20"><!-- Col starts -->
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="fa fa-bars"></i><h3 class="box-title">Claims Number</h3>  
            <div class="box-tools pull-right margin-t-2"> 
          
            </div>
        </div><!-- /.box-header -->
          <div class="box-body table-responsive"><!-- Box Body Starts -->        
         @if(!empty(@$mismatchedclaims))
            @foreach($mismatchedclaims as $result)
            <?php if($result->claimTXType != '0') $edit = 'Edit Charge'; else $edit= '';?>
            <p class="med-red"> {{$result->claim_number }}  <span class="med-green">{{ @$edit }}</span></p> 
            @endforeach
        @endif
        </div><!-- /.box-body ends-->
   
    </div><!-- /.box Ends -->
</div><!-- Col Ends -->

@include('practice/layouts/favourite_modal')
@stop
@push('view.scripts')

@endpush