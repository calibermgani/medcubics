@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> APP Settings <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Set Questionnaires <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('questionnaires')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>			
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/questionnaire')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop
@section('practice-info')
	@include ('practice/questionnaires/tabs') 
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">
    @if($checkpermission->check_url_permission('questionnaires/{questionnaires}/edit') == 1)
	   <a href="{{ url('questionnaires/'.$id.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
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
						<td>Provider</td>
						<td>{{ @$questionnaries->provider->provider_name }} {{ @$questionnaries->provider->degrees->degree_name }}</td>
					</tr>
					<tr>
						<td>Facility</td>
						<td>{{ @$questionnaries->facility->facility_name}}</td>
					</tr>
					<tr>
						<td>Questionnaires</td>
						<td>{{ @$questionnaries->questionnaries_option->title}}</td>
					</tr>
					<tr>
						<td>Created By</td>
						<td>{{ App\Http\Helpers\Helpers::shortname($questionnaries->created_by) }}</td>
					</tr>
					<tr>
						<td>Created On</td>
                        <td><span class="bg-date">
                               {{ App\Http\Helpers\Helpers::timezone($questionnaries->created_at, 'm/d/y') }}
                        </span></td>
					</tr>
					<tr>
						<td>Updated By</td>
						<td>{{ App\Http\Helpers\Helpers::shortname($questionnaries->updated_by) }}</td>
					</tr>
					<tr>
						<td>Updated On</td>
						<td>
							@if($questionnaries->updated_at !='' && $questionnaries->updated_at !='-0001-11-30 00:00:00' && $questionnaries->updated_at !='0000-00-00 00:00:00')
	                            <span class="bg-date">
                               {{ App\Http\Helpers\Helpers::timezone($questionnaries->updated_at, 'm/d/y') }}
	                            </span>
							@endif
						</td>
					</tr>
				</tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
@stop            