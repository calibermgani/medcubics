@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}} font14"></i> Clinical Notes</small>
        </h1>
        <ol class="breadcrumb">
		
			 <?php $uniquepatientid = @$patient_id; ?>	
			 <li><a href={{App\Http\Helpers\Helpers::patientBackButton($patient_id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			@include ('patients/layouts/swith_patien_icon')	
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => '/patients/'.@$patient_id.'/clinicalnotes/export/'])
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/clinical_notes')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop
@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'yes'])
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    @if(Session::get('message')!== null) 
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >

    <div class="box box-info no-shadow">
        <div class="box-header">
            <i class="fa fa-bars font14"></i><h3 class="box-title">Clinical Notes List</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_url_permission('patients/{id}/clinicalnotes/create') == 1)
				<a class="font600 font14 hidden-print" href="{{ url('patients/'.@$patient_id.'/clinicalnotes/create') }}"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Clinical Notes</a>
				@endif
            </div>
        </div><!-- /.box-header -->

        <div class="box-body">
            <div class="table-responsive">                                
                <table id="documents" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>DOS</th>
                            <th>Title</th>
                            <th>Claim No</th>
                            <th>Facility</th>
                            <th>Rendering</th>
                            <th>Category</th>
                            <th>User</th>
                            <th>Created On</th> 
                            <th style="width: 7%">Action</th>                                    
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(@$clinical_notes as $clinical_notes)
                        <?php
							$provider = @$clinical_notes->rendering_provider;
							$provider->id = 'p_'.@$provider->id.$clinical_notes->id;
							$facility = $clinical_notes->facility_detail;
							$facility->id = 'f_'.@$facility->id.$clinical_notes->id;
							@$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$clinical_notes->type_id,'encode');
							@$clinical_notes->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$clinical_notes->id,'encode');
						?>
                        <tr class="form-cursor js_table_click" data-url="{{ url('patients/'.$patient_id.'/clinicalnotes/'.@$clinical_notes->filename) }}" target="_blank">
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$clinical_notes->dos,'claimdate')}} </td>
                            <td>{{str_limit(@$clinical_notes->title,25,'...') }}</td>
                            <td>{{@$clinical_notes->claim->claim_number}}</td>
                            <td>
								<span class="js-display-detail"> </span>
								@include('layouts.facilitypop', array('data' => @$facility, 'from' =>'facility'))
									<a id="someelem{{hash('sha256',@$facility->id)}}" class="someelem" data-id="{{hash('sha256',@$facility->id)}}" href="javascript:void(0);"> {{$facility->short_name }}</a>
                                @include ('layouts/facility_hover')
                            </td>
                            <td><a id="someelem{{hash('sha256',@$provider->id)}}" class="someelem" data-id="{{hash('sha256',@$provider->id)}}" href="javascript:void(0);"> {{ @$provider->short_name }}</a> 
									@include ('layouts/provider_hover')</td>
                            <td>{{@$clinical_notes->category_type->category_value}} </td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($clinical_notes->created_by) }}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$clinical_notes->created_at,'date')}}</td>
                            <td class="td-c-5 text-center js-prevent-show">

                                <span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <a href="{{ url('patients/'.$patient_id.'/clinicalnotes/'.$clinical_notes->id.'/edit')}}" class="font14 font600 margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a>
                                </span>
                                <span class="col-lg-6 col-md-6 col-sm-6 col-xs-6 document-delete">	
								<!-- Dont use bootstrap tooltip option if we use href appended url -->
                                    {!! Form::open(array('method'=> 'DELETE', 'route' =>array('document.destroy', @$clinical_notes->id))) !!}
                                    <a class="js-delete-confirm" data-text="Are you sure to delete the entry?" href="{{ url('patients/'.@$patient_id.'/clinicalnotes/delete/'.@$clinical_notes->id) }}" ><i class="fa {{Config::get('cssconfigs.common.delete')}}" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></center>
                                    {!! Form::close() !!}
                                </span>
                            </td>
                        </tr>
                        @endforeach	
                    </tbody>
                </table>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
@stop    