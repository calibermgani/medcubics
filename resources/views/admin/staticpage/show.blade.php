@extends('admin')



@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.help')}} font14"></i> Help <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('admin/staticpage') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>		
		<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
        </ol>
    </section>

</div>
@stop




@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-m-18">    				
			<a href="{{ url('admin/staticpage/'.$staticpages->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>		
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
                        <td>Module Name</td>
                        <td><?php if(strlen($staticpages->slug)== 3) echo (strtoupper($staticpages->slug)); else echo (ucfirst($staticpages->slug)); ?></td>
                    </tr>
                    
                     <tr>
                        <td>Title</td>
                        <td>{{ $staticpages->title}}</td>
                    </tr>
                   
                    <tr>
                        <td>Status</td>
                        <td>{{ $staticpages->status }}</td>
                    </tr> 
					<tr>
						<td>Created By</td>
						<td>{{ App\Http\Helpers\Helpers::shortname($staticpages->created_by) }}</td>
					</tr>
					 <tr>
						<td>Created On</td>
						<td>@if(App\Http\Helpers\Helpers::dateFormat($staticpages->created_at,'date'))<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($staticpages->created_at,'date')}}</span>@endif</td>
					</tr>
					<tr>
						<td>Updated By</td>
						<td>{{ App\Http\Helpers\Helpers::shortname($staticpages->updated_by) }}</td>
					</tr>
					<tr>
						<td>Updated On</td>
						<td>@if($staticpages->updated_at  >='12-11-2015')<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($staticpages->updated_at,'date')}}</span>@endif</td>
					</tr>
				</tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->


<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right Side content Starts -->
    <div class="box box-view no-shadow">
        <div class="box-header-view">
            <i class="livicon" data-name="question"></i> <h3 class="box-title">Help Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            
                        <p>{!! $staticpages->content !!}</p>
                    
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Right Side content Ends -->
<!--End-->
@stop 