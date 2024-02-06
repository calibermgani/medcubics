@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{ Config::get('cssconfigs.payments.payments') }}" data-name="users"></i> Invoice</small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>

				@if($checkpermission->check_adminurl_permission('api/admin/invoice/{export}') == 1)
					<li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/invoice/'])
					</li>
				@endif

				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/invoice')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop
@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Invoice List</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_adminurl_permission('admin/invoice/create') == 1)
                <a href="{{ url('admin/invoice/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New Invoice</a>
            @endif
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped ">
                    <thead>
                        <tr>
                            <th class="text-left">Invoice Number </th>
                            <th class="text-left">Invoice Date</th>
                            <th class="text-left">Invoice Start Date</th>
                            <th class="text-left">Invoice End Date</th>
                            <th class="text-left">Invoice Amount ($)</th>
                            <th class="text-left">Tax (%)</th>
                            <th class="text-left">Previous amount due ($)</th>
                            <th class="text-left">Total Amount ($)</th>
                            <th class="text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($invoicelists) > 0)
                        @foreach($invoicelists as $invoicelist)
                        <tr style="text-align:center;" data-url="" class="js-table-click clsCursor">
                            <td class="text-left">{{ $invoicelist->invoice_no }}</td>
                            <td class="text-left">{{ \App\Http\Helpers\Helpers::checkAndDisplayDateInInput($invoicelist->invoice_date,'','','m/d/y') }}</td>
                            <td class="text-left">{{ \App\Http\Helpers\Helpers::checkAndDisplayDateInInput($invoicelist->invoice_start_date,'','','m/d/y') }}</td>
                            <td class="text-left">{{ \App\Http\Helpers\Helpers::checkAndDisplayDateInInput($invoicelist->invoice_end_date,'','','m/d/y') }}</td>
                            <td class="text-right">{{ $invoicelist->invoice_amt }}</td>
                            <td class="text-left">{{ $invoicelist->tax }}</td>
                            <td class="text-right">{{ $invoicelist->previous_amt_due }}</td>
                            <td class="text-right">{{ $invoicelist->total_amt }}</td>
                            <td class="td-c-5 text-left">
                                <span>
                                    <a href="{{url('admin/invoice/report/'.@$invoicelist->id )}}" class="font14 font600 margin-r-5"><i class="fa fa-clipboard" data-placement="bottom"  data-toggle="tooltip" data-original-title="Download"></i></a>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

@stop

@push('view.scripts')
{!! HTML::script('js/invoice.js') !!}
@endpush