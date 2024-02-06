@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php $insurance->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($insurance->id,'encode'); ?>
<?php $appealaddress->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($appealaddress->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.insurance')}}" data-name="bank"></i>Insurance <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Appeal Address <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('admin/insurance/'.$insurance->id.'/insuranceappealaddress/') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            
           
            
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop


@section('practice-info')
@include ('admin/insurance/insurance_tabs')
@stop

@section('practice')



<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">    
              
 @if($checkpermission->check_adminurl_permission('admin/insurance/{insurance_id}/insuranceappealaddress/{insuranceappealaddress}/edit') == 1)
           <a href="{{ url('admin/insurance/'.$insurance->id.'/insuranceappealaddress/'.$appealaddress->id.'/edit') }}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
            @endif
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Appeal Address</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table-responsive table-striped-view table">                    
                <tbody>
                    <tr>
                        <td>Address Line 1</td>
                        <td>{{ $appealaddress->address_1 }}</td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>Address Line 2</td>
                        <td>{{ $appealaddress->address_2 }}</td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td>{{ $appealaddress->city }} @if($appealaddress->state !='') - <span class="bg-state"> {{ strtoupper($appealaddress->state) }}</span>@endif</td>
                        <td></td> 
                        <td></td>
                    </tr>                    
                    <tr>
                        <td>Zipcode</td>
                        <td>{{ $appealaddress->zipcode5}} @if($appealaddress->zipcode4!=''){{' - '.$appealaddress->zipcode4 }} @endif</td>
                        <td></td> 
                        <td>
                             <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match'],'show'); ?>   
                                <?php echo $value;?>    
                        </td>
                    </tr>
                    <tr>
                        <td>Work Phone</td>
                        <td>{{ $appealaddress->phone }} <span class="@if($appealaddress->phoneext == '')  @else bg-ext @endif">{{ $appealaddress->phoneext }}</span></td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>Fax</td>
                        <td>{{ $appealaddress->fax }}</td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><a href="mailto:{{ $appealaddress->email }}">{{ $appealaddress->email }}</a></td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>Created By</td>
                        <td>{{ App\Http\Helpers\Helpers::shortname($appealaddress->created_by) }}</td>
                        <td></td> 
                        <td></td>
                    </tr>
                    <tr>
                        <td>Updated By</td>
                        <td>{{ App\Http\Helpers\Helpers::shortname($appealaddress->updated_by) }}</td>
                        <td></td> 
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
     @include('practice/layouts/usps_form_modal') 
</div><!-- Modal Light Box Ends --> 

@include('practice/layouts/favourite_modal')   
@stop 