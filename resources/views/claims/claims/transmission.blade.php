@extends('admin')
@section('toolbar')    
    <div class="row toolbar-header">
        <section class="content-header">
           <h1>
                <small class="toolbar-heading">
                    <i class="fa font14 {{$heading_icon}}"></i> Claim Transmission
                </small>
            </h1>
			<ol class="breadcrumb">                
                <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom" data-toggle="tooltip" data-original-title="Print"></i></a></li-->
                <li class="dropdown messages-menu js_claim_export hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'claims/transmission/search/export/'])
                     {!! Form::hidden('js_search_option_url',url('claims/transmission/search/'),['id'=>'js_search_option_url']) !!}
				</li>
                <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            </ol>
            
        </section>
    </div>
@stop
@section('practice')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		{!! Form::open(['id'=>'js_claim_search','url'=>'claims/transmission/search', 'class'=>'js_search_basis_export']) !!}
			<input class="js_filter_search_submit" type="hidden"> 
			 <!--div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding pull-right">
				<input class="btn btn-flat btn-medcubics-small js_filter_search_submit pull-right" value="Search" type="submit">
			</div-->
		{!! Form::close() !!}
        <div class="box box-info no-shadow">
            <div class="box-header with-border">
                <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">List</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">                
                <table id="example1" class="table table-bordered table-striped">	
                    <thead>
                        <tr>                                                               
                            <th>Transmission Type</th>
                            <th>No of Claims</th>                                
                            <th>Billed Amt</th>                                
                            <th>Transmited By</th>
                            <th>Transmited On</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>  
                        @foreach($claim_transmission as $key => $transmission)
                            <?php $transmission_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($transmission->id,'encode'); ?>
                            <tr data-url="{{ url('claims/transmission/'.$transmission_id) }}" class="js-table-click clsCursor">
                                <td>{{$transmission->transmission_type}}</td>
                                <td>{{$transmission->total_claims}}</td>                                    
                                <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($transmission->total_billed_amount)!!}</td>
                                <td>{{@$transmission->user->name}}</td>
                                <td>{{App\Http\Helpers\Helpers::dateFormat($transmission->created_at,'date')}}</td>
                                <td>@if($transmission->transmission_type == 'Electronic')<a href="{{ url('claims/download/837/'.$transmission_id) }}"><i class="fa fa-paperclip" data-placement="bottom"  data-toggle="tooltip" data-original-title="Download"></i></a>@else N/A @endif</td>
                            </tr>
                        @endforeach                                                 
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
    <!--End-->   
@stop