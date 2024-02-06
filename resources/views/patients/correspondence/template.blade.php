@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Templates <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Search / Add Templates </span></small>

        </h1>
        <ol class="breadcrumb">
            <?php 
				$id = Route::current()->parameters['id'];
				$uniquepatientid = $id; 
			?>	
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            @include ('patients/layouts/swith_patien_icon')


<!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/correspondence')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$id,'needdecode'=>'yes'])
@include ('patients/correspondence/tabs')
@stop


@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

    <div class="box box-info no-shadow margin-t-m-10">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-15">
            <h4 class="med-green"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> List</h4>
        </div>

        

        <div class="box-body">	<!-- Box Body Starts -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4" style="position: absolute; z-index:999; left:0px; margin-top: 2px; margin-left: 100px;">                                       
            <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
               class="js-show-patientsearch js-insurance-popup claimdetail form-cursor font600 p-l-10 p-r-10 hide" style=""> Tab View</a>
        </div> 
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped ">         
                        <thead>
                            <tr>
                                <!--th class="td-c-2"></th-->
                                <th>Created On</th>
                                <th>Category</th>
                                <th>Name</th>    
                                <th>User</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $keys=>$templates)
                            <?php $templates->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($templates->id,'encode'); ?>
                            <tr class="cur-pointer js-table-click" data-url="{{ url('patients/'.$patient_id.'/correspondence/'.$templates->id.'/edit') }}" >
                                <!--td><input type="checkbox" class="" id="{{$keys}}"><label for="{{$keys}}" class="no-bottom">&nbsp;</label></td-->
                                <td>{{ App\Http\Helpers\Helpers::timezone($templates->created_at,'m/d/y')}}</td>
                                <td>{{  str_limit($templates->templatetype->templatetypes, 25) }}</td>
                                <td> {{  str_limit($templates->name, 35) }}</td>   
                                <td>{{ App\Http\Helpers\Helpers::shortname($templates->created_by) }}</td>
                            </tr>
                            @endforeach      
                        </tbody>
                    </table>
                </div>       
            </div>

            
        </div><!-- /.box-body ends -->

    </div><!-- /.box -->
</div>

<!--End-->
@stop   