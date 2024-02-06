@extends('admin')

@section('toolbar')
<?php  
	if(!isset($get_default_timezone)){
        $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
}?> 
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Eligibility <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Verify Eligibility </span></small>
        </h1>

        <?php 	
			$uniquepatientid = @$patient_id;
			$patientid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id,'decode');		
			$id = $uniquepatientid; 
		?>

        <ol class="breadcrumb">
            <li><a href="{{App\Http\Helpers\Helpers::patientBackButton($patient_id)}}" class="js_next_process">
                    <i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>	

            @include ('patients/layouts/swith_patien_icon')  

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/eligibility')}}"  class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patientid,'needdecode'=>'no'])
@include ('patients/eligibility/tabs')

@stop

@section('practice')

<div class="box box-info no-shadow" >
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 2px solid #f0f0f0;">
        @if(Session::get('message')!== null) 
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">            
            <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>            
        </div>
        @endif
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-15 margin-t-10">
            <h4 class="med-darkgray"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Eligibility Verification</h4>
        </div>
        <div class="box-body bg-white no-bottom">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                {!! Form::open(['name'=>'edi_form','id'=>'js-bootstrap-validator']) !!}


                <div class="box-body form-horizontal margin-b-15">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-address-class" id="js-address-general-address">
                        <input type="hidden" name="patient_id" class="js_edi_patient" value={{$patient_id}} >
                        {!! Form::token() !!}
                        <div class="form-group">
                            {!! Form::label('Insurance name', 'Insurance Name', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label star']) !!} 
                            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10 @if($errors->first('insurance_name')) error @endif">
                                {!! Form::select('insurance_id', array(''=>'-- Select --')+(array)@$patient_insurances,null,['class'=>'select2 form-control edi_insurance_id']) !!}       
                                <span id="insurance_error"></span>
                                {!!Form::hidden('category')!!}
                                {!!Form::hidden('insid')!!}
                            </div>                                
                        </div>


                        <div class="form-group">
                            {!! Form::label('DOS From', 'DOS From', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label star']) !!} 
                            <div class="col-lg-2 col-md-3 col-sm-5 col-xs-10">
                                <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i> 
                                {!! Form::text('dos_from',null,['id'=>'elig_dos_from','class'=>'datepicker dm-date form-control input-sm-modal-billing form-cursor','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}  
                            </div>                                
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('DOS To', 'DOS To', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label star']) !!} 
                            <div class="col-lg-2 col-md-3 col-sm-5 col-xs-10">
                                 <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i> 
                                {!! Form::text('dos_to',null,['id'=>'elig_dos_to','class'=>'datepicker dm-date form-control input-sm-modal-billing form-cursor','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}  
                            </div>                                
                        </div>

                      

                        <div class="form-group">
                            {!! Form::label('', '', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!} 

                            <div class="col-lg-2" id="insurance-info-footer">
                                {!! Form::submit("Verify", ['class'=>'btn btn-medcubics']) !!}
                                <a  class="btn btn-medcubics-small js-patient-eligibility_check margin-t-m-2 hide" type="submit" value="" data-page="eligibility" data-patientid ="{{$patient_id}}">Verify</a>
                            </div>
                        </div>
                    </div>
                </div><!-- /.box-body -->   


                {!! Form::close() !!}
            </div>

        </div><!-- /.box-body --> 
    </div><!-- Box Ends -->
    <!-- SIVA -->





    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide"><!-- Hided for First Version -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-15 margin-t-15">
            <h4 class="med-darkgray"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Benefits Verification</h4>
        </div>

        <div class="box-body bg-white">
            <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4" style="position: absolute; z-index:999; left:0px; margin-top: 64px; margin-left: 110px;">                                       

                <!--     <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                        class="js-show-patientsearch js-insurance-popup claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>
                -->

            </div> 
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <table id="example1" class="table table-bordered table-striped ">         
                    <thead>
                        <tr>
                           <!-- <th class="td-c-2"></th> -->
                            <th>Created On</th>
                            <th>Category</th>
                            <th>Template</th>
                            <th>User</th>

                        </tr>
                    </thead>
                    <tbody>

                        @foreach($benifit_templates as $keys=>$benifit)
                        <?php  $template_id = @$benifit->id;
                                $template_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$template_id,'encode');
                                $url = url('patients/'.$patient_id.'/eligibility/create/'.$template_id);
                                ?>  
                        <tr class="js-table-click" data-url = "{{$url}}">
                            <td><a href="#">{{ App\Http\Helpers\Helpers::dateFormat($benifit->created_at,'date')}}</a></td>
                            <td>Benefits Verification</td>
                            <td>{{$benifit->name}}</td>
                            <td>{{@$benifit->creator->short_name}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div><!-- /.box-body --> 
    </div><!-- Box Ends -->
    <!-- SIVA -->

</div>


@stop

@push('view.scripts')                           
<script type="text/javascript">
    $(document).ready(function () {
        $('input[type="text"]').attr('autocomplete', 'off');
        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            excluded: ':disabled',
            feedbackIcons: {
                valid: '',
                invalid: '',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                'insurance_id': {
                    validators: {
                        notEmpty: {
                            message: 'Select Insurance Name'
                        }
                    }
                },
                'dos_from': {
                    trigger: 'keyup change',
                    validators: {
                        notEmpty: {
                            message: 'Select Dos From'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            message: date_valid_lang_err_msg
                        },
                    }
                },
                'dos_to': {
                    trigger: 'keyup change',
                    validators: {
                        notEmpty: {
                            message: 'Select Dos To'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            message: date_valid_lang_err_msg
                        },
                    }
                },
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("a.js-patient-eligibility_check").click();
        });
    });
<?php if(isset($get_default_timezone)){?>
     var get_default_timezone = '<?php echo $get_default_timezone;?>';    
<?php }?>
</script>
@endpush