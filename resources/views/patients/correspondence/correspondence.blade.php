@extends('admin')
@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Templates  <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> History </span> </small>
        </h1>
        <ol class="breadcrumb">
            <?php 
				$id = Route::current()->parameters['id'];
            	$uniquepatientid = $id; 
			?>
            <li><a href="{{ url('patients/'.$patient_id.'/correspondence')}}"> <i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>	

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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" ><!-- Col-12 starts -->
    <div class="box box-info no-shadow margin-t-m-10"><!-- Box Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-15">
            <h4 class="med-green"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> List</h4>
        </div>
        
        <div class="box-body">	<!-- Box Body Starts -->
            <div class="table-responsive">
                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4" style="position: absolute; z-index:999; left:0px; margin-top: 50px; margin-left: 100px;">                                       
            <!-- <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
               class="js-show-patientsearch js-insurance-popup claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a> -->
				</div> 
                <table id="example1" class="table table-bordered table-striped ">         
                    <thead>
                        <tr>
                            <!--th class="td-c-2"></th-->
                            <th>Created On</th>
                            <th>Claim No</th>
                            <th>DOS</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Insurance</th>
                            <th>User</th>
                            <th class="td-c-2"></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($patient_correspondence as $keys=>$patient_correspondence)
                        <?php 
                            $claim_number = '';
                            $claim_number = explode(',', $patient_correspondence->claim_number);
                            $display_data = "";
                            $claim_class = "";
                             if(!empty($claim_number) && count($claim_number)>1){
                                $claim_number = "Multiple";
                                $claim_class = "js_hove_claim";
                                $display_data = $patient_correspondence->claim_number;
                             } else if(!empty($claim_number)){
                                $claim_number = $claim_number[0];
                             }
                        ?>
                        <?php $patient_correspondence->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_correspondence->id,'encode'); ?>
                        <tr data-url="{{url('patients/'.@$id.'/correspondencehistory/'.@$patient_correspondence->id)}}" class="js-table-click clsCursor">
                            <!--td><input type="checkbox" class="" id="{{$keys}}"><label for="{{$keys}}" class="no-bottom">&nbsp;</label></td-->
                            <td>{{ App\Http\Helpers\Helpers::timezone($patient_correspondence->created_at,'m/d/y')}}</td>
                            <td class = "{{$claim_class}}" data-val="{{$display_data}}">{{ $claim_number == "" ? "Null" : $claim_number }}</td>
                            <td>{{App\Http\Helpers\Helpers::dateFormat($patient_correspondence->dos,'date') == "" ? "Null" :  App\Http\Helpers\Helpers::dateFormat($patient_correspondence->dos,'date') }}</td>
                            <td>{{ str_limit(@$patient_correspondence->subject,25,'...') }}</td>
                            <td> {{ @$patient_correspondence->template_detail->templatetype->templatetypes }}</td>
                            <td>{{ @$patient_correspondence->insurance->short_name == "" ? "Null" :  @$patient_correspondence->insurance->short_name }}</td>                                                       
                            <td>{{ App\Http\Helpers\Helpers::shortname(@$patient_correspondence->creator->id) }}</td>
                            <td><i class="fa fa-fax"></i></td>

                        </tr>
                        <span class="js_hove_claim_show" style="display:none;"></span>
                        @endforeach
                    </tbody>
                </table>

            </div>                                
        </div><!-- /.box-body ends -->
    </div><!-- /.box  ends -->
</div><!-- Col-12 Ends -->

<!--End-->
@stop