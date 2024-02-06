@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> {{ucfirst($selected_tab)}}</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/templatereports/General/'])
            </li>

            <li><a href="#js-help-modal" data-url="{{url('help/templates')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
    
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">

    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="fa fa-bars font14"></i><h3 class="box-title">Templates List</h3>
            <div class="box-tools pull-right margin-t-2">
                <a class="font600 font14" href="{{ url('templates/create') }}"> <i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Template</a>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">

            <div class="accordion">
				@if(count($templates)>0)		
					<?php $user_names = []; ?>		
                    @foreach($templates as $templatestypes)
                        <div class="accordion-group">
                            <div class="accordion-heading js_accordion_header">
                                <a class="accordion-toggle">
                                    <span class="font600 no-background med-green">{{ $templatestypes->templatetypes }}</span> <span class="accordion-badge"> {{count($templatestypes->template)}}</span>
                                </a>
                            </div>
                            <div class="accordion-body js_accordion_content collapse">
                                <div class="accordion-inner"> 
                                    
                                    <table class="table margin-b-10">
                                        <thead style="border:none;">
                                            <tr>
                                                <th style="width:50%; font-weight: 600;" class="font600 med-green bg-white">Name</th>
                                                <th style="width:20%; font-weight: 600;" class="font600 med-green bg-white">Status</th>	
                                                <th style="width:20%; font-weight: 600;" class="font600 med-green bg-white">Created By</th>	
                                                <th style="width:10%; font-weight: 600;" class="font600 med-green bg-white">Created On</th>
                                            </tr>
                                        </thead>
										<tbody>
                                        @foreach($templatestypes->template as $templates)
                                        <?php 
											$templates->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($templates->id,'encode'); 
											if(!isset($user_names[$templates->created_by])) {
												$user_names[$templates->created_by] = App\Http\Helpers\Helpers::shortname($templates->created_by);
											}
											$created_name =$user_names[$templates->created_by];											
										?>
                                            <tr data-url="{{ url('templates/'.$templates->id) }}" class="js-table-click cur-pointer">
                                                <td>{{ $templates->name }}</td>
                                                <td>{{ @$templates->status}}</td>
                                                <td>{{ $created_name }}</td>
                                                <?php /*
                                                <td><span class="bg-date">{{ App\Http\Helpers\Helpers::dateFormat($templates->created_at,'date')}}</span></td>
                                                */ ?>
                                                <td><span class="bg-date">{{ App\Http\Helpers\Helpers::dateFormat($templates->created_at, 'date') }}</span></td>
                                            </tr>
                                        @endforeach
										</tbody>
                                    </table>
                                    
                                </div>
                            </div>
                        </div>
                    @endforeach 
				@else
					<div class="thumbnail col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green" style="padding: 10px 15px;"><h5>No Records Found !!!</h5></div>
				@endif
            </div>
        </div><!-- /.box-body --> 
    </div><!-- /.box -->
</div>
<!--End-->
@stop   