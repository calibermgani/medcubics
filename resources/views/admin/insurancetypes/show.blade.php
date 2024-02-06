@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php $insurancetypes->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($insurancetypes->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.list_ul')}} font14"></i> Insurance Types <i class="fa fa-angle-double-right med-breadcrum"></i><span>View</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{url('admin/insurancetypes')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/insurance_types')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">    
	@if($checkpermission->check_adminurl_permission('admin/insurancetypes/{insurancetypes}/edit') == 1)
		<a href="{{ url('admin/insurancetypes/'.$insurancetypes->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
	@endif
</div>

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title"> General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
        <table class="table-responsive table-striped-view table">
			<tbody>
				<tr>
					<td class="td-c-30">Insurance Types</td>
					<td>{!! $insurancetypes->type_name !!}</td>
					<td colspan="2"></td>
				</tr>
				
				<tr>
					<td class="td-c-30">CMS Type</td>
					<td>{!! @$insurancetypes->cms_type !!}</td>
					<td colspan="2"></td>
				</tr>
				<tr>
					<td class="td-c-30">POS Code</td>
					<td>{!! @$insurancetypes->code !!}</td>
					<td colspan="2"></td>
				</tr>
				<tr>
					<td>Created On</td>
					<td>
					@if($insurancetypes->created_at !='' && $insurancetypes->created_at !='-0001-11-30 00:00:00' && $insurancetypes->created_at !='0000-00-00 00:00:00')
						<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($insurancetypes->created_at,'date')}} @else <span class="nill"> -Nil-</span> 
					@endif
					</td>
				</tr>
				 <tr>
					<td>Created By</td>
					<td>@if(@$insurancetypes->created_by != ''){{ App\Http\Helpers\Helpers::shortname($insurancetypes->created_by) }}@else <span class="nill"> -Nil-</span> @endif</td>
				</tr>
				<tr>
					<td>Updated On</td>
					<td>
						@if($insurancetypes->updated_at !='' && $insurancetypes->updated_at !='-0001-11-30 00:00:00' && $insurancetypes->updated_at !='0000-00-00 00:00:00')<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($insurancetypes->updated_at,'date')}}</span>  @endif
					</td>
				</tr>
				<tr>
					<td>Updated By</td>
					<td>@if(@$insurancetypes->updated_by != '')<span>{{ App\Http\Helpers\Helpers::shortname($insurancetypes->updated_by) }}</span> @endif</td>
				</tr>
		   </tbody>
		</table>		 
		</div><!-- /.box-body -->
	</div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
@stop