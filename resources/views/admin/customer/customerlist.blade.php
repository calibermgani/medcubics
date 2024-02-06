@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers</small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>

				@if($checkpermission->check_adminurl_permission('api/admin/customermedcubics/{export}') == 1)
					<li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/customermedcubics/'])
					</li>
				@endif

				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/customer')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop
@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Customer List</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_adminurl_permission('admin/customer/create') == 1)
                <a href="{{ url('admin/customer/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New</a>
            @endif
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box-body table-responsive">
                <div style="border: 1px solid #008E97;border-radius: 4px;">
                <div class="box-header med-bg-green no-padding" style="border-radius: 4px 4px 0px 0px;">
                    <div class="col-lg-3 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Customer Name</h3>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-1 hidden-xs" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Customer Type</h3>
                    </div>  

                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Contact Person</h3>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-0 med-white">Designation</h3>
                    </div> 
                    <div class="col-lg-2 col-md-1 col-sm-2 col-xs-3" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-0 med-white">Phone</h3>
                    </div>                    
                    <div class="col-lg-2 col-md-1 col-sm-1 col-xs-3" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-0 med-white">Cell Phone</h3>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-5"><!--  Left side Content Starts -->              
                        @foreach(@$customers as $customer)
                        <div class="box collapsed-box" style="box-shadow:none;margin:0px;"><!--  Box Starts -->
                            <div class="box-header-view-white no-padding" style="color: #fff;border-bottom: 1px solid #CDF7FC;">
                                <div class="col-lg-3 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <h3 class="box-title font12 font-normal">
                                         <button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i></button></h3>
                                         @php  $customer->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($customer->id,'encode'); @endphp
                                    <a href="{{ url('admin/customer/'.$customer->id) }}"><span class="med-green">{{ $customer->customer_name }}</span></a>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{ $customer->customer_type }}</span>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{ $customer->contact_person }}</span>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{ $customer->designation }}</span>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{ $customer->phone }}</span>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{ $customer->mobile }}</span>
                                </div><!-- /.box-header -->
                            </div>
                            <div class="box-body form-horizontal">
                                <div class="col-md-12 col-sm-12 col-xs-12 no-padding border-radius-4 yes-border border-b4f7f7">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                        <span class="med-orange margin-l-10 font13 font600 padding-0-4 bg-white">Practice Details</span>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10" >   
                                        <table class="table margin-t-5 margin-b-10 no-sm-bottom">
                                            <thead>
                                                   <tr>    
                                                       <th style="border-right: 1px solid #fff;" class="col-lg-2 col-md-1 col-sm-1 col-xs-2">Name</th>
                                                       <th style="border-right: 1px solid #fff;" class="col-lg-2 col-md-1 col-sm-1 col-xs-2">Description</th>
                                                       <th style="border-right: 1px solid #fff;" class="col-lg-2 col-md-1 col-sm-1 col-xs-2">Email</th>
                                                       <th style="border-right: 1px solid #fff;" class="col-lg-2 col-md-1 col-sm-1 col-xs-2">Phone</th>
                                                       <th style="border-right: 1px solid #fff;" class="col-lg-2 col-md-1 col-sm-1 col-xs-2">Fax</th>
                                                       <th style="border-right: 1px solid #fff;" class="col-lg-2 col-md-1 col-sm-1 col-xs-2">Doing Business as</th>
                                                   </tr>
                                               </thead>
                                               <tbody>
                                                   @foreach(@$customer->practice as $list)
                                                   @php  $practice = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id,'encode'); @endphp
                                                   <tr data-url="{{ url('admin/customer/'.$customer->id.'/customerpractices/'.$practice) }}" class="js-table-click clsCursor">
                                                       <td class="margin-l-10"><a href="{{ url('admin/customer/'.$customer->id.'/customerpractices/'.$practice) }}"><span class="med-green margin-l-10">{{ @$list->practice_name }}</span></a></td>
                                                       <td>{{ @$list->practice_description }}</td>
                                                       <td>{{ @$list->email }}</td>
                                                       <td>{{ @$list->phone }}</td>
                                                       <td>{{ @$list->fax }}</td>
                                                       <td>{{ @$list->doing_business_s }}</td>
                                                   </tr>
                                                   @endforeach
                                               </tbody>
                                        </table>                         
                                    </div>
                                </div>
                            </div><!-- /.box Ends-->
                        </div>
                        @endforeach
                    </div>    
                </div>
            </div>
        </div><!-- /.box -->
        </div>
        </div>
    </div><!-- /.box -->
</div>
@endsection
@push('view.scripts')
<script type="text/javascript">
    // $(document).ready(function(){
    //     $('#p_list').on('click',function(){
    //         alert('success');
    //     });
    // });

</script>
@endpush