@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Employers </span></small>
        </h1>
        <ol class="breadcrumb">
			<!--li><a href="{{ url('employer') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li-->
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
			<li class="dropdown messages-menu hide"><a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_stream_export', ['url' => 'api/employerreports/'])
            </li>
            
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/employers')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice')
<div class="col-md-12"><!-- Col-12 Starts -->
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="fa fa-bars"></i><h3 class="box-title">Employers List</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_url_permission('employer/create') == 1)
                <a href="{{ url('employer/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Employer</a>
                @endif	
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
			
            <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>                        
                        <th>Employer Name</th>
                        <th>Phone1</th>
						<th>Phone2</th>
                    </tr>
                </thead>
                <tbody>
					@foreach($employers as $employer)
					<?php 
						$employer->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($employer->id,'encode'); 
					?>
                    <tr  class="js-table-click clsCursor" >                        
                      
                        <td  @if($checkpermission->check_url_permission('employer/{employer}') == 1) data-url="{{ action('EmployerController@show',[$employer->id]) }}" @endif >{{ @$employer->employer_name}}</td>
                        <?php $phone_class = (isset($employer->work_phone) && !empty($employer->work_phone))? "js-callmsg-clas cur-pointer": ""?>
                        <td class="js-table-click hidden-print js-prevent-redirect">
                            <span class="{{$phone_class}} js-prevent-redirect" data-phone= "{{@$employer->work_phone}}" data-user_id="{{$employer->id}}" data-user_type="employer">
                            {{ $employer->work_phone }}@if(@$employer->work_phone_ext){{-@$employer->work_phone_ext }}
                            <span class="fa fa-phone-square margin-l-4 med-green" data-placement="bottom"  data-toggle="tooltip" data-original-title="Click"></span>
                            @endif
                            </span>
                        </td>
                        <td>{{ $employer->work_phone1 }}@if(@$employer->work_phone_ext1){{-@$employer->work_phone_ext1 }}@endif</td>
                       
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!-- Col-12 Ends -->
<div id="export_csv_div"></div>
@stop   

@push('view.scripts')
<script type="text/javascript">
    $(document).on('click', '.js-prevent-redirect', function (e) { 
        e.stopImmediatePropagation();
    });
    
    /* Export Excel for Charges list*/
    /*$('.js_search_export_csv').click(function(){
	var baseurl = '{{url('/')}}';
	var url = baseurl+"/reports/streamcsv/export/employers";
        form = $('form').serializeArray();
        var data_arr = [];
        data_arr.push({
            name : "controller_name", 
            value:  "EmployerController"
        });
        data_arr.push({
            name : "function_name", 
            value:  "getEmployerExport"
        });
        data_arr.push({
            name : "report_name",
            value:  "Employers_List"
        });
        form_data = "<form id='export_csv' method='POST' action='"+url+"'>";
        $.each(data_arr,function(index,value){
            if($.isArray(value.value)) {
                if(value.value.length > 0) {
                    var avoid ="[]"
                    form_data += "<input type='text' name='"+value.name.replace(avoid, '')+"' value='"+value.value+"'>";
                }
            } else {
                if(value.value.length > 0) {
                    form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
                }
            }
        });
        form_data  += "<input type='hidden' name='export' value = 'yes'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
        form_data += "</form>";
        $("#export_csv_div").html(form_data);
        $("#export_csv").submit();
        $("#export_csv").empty();
    });*/
</script>
@endpush