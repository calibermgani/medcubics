@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.patient.file_text')}} font14"></i> Templates <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> History</span> </small>
        </h1>
		<ol class="breadcrumb">
		 
            <li><a href="{{url('patients/'.@$patient_id.'/correspondencehistory')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>  
			<?php $uniquepatientid = @$patient_id; ?>	
			@include ('patients/layouts/swith_patien_icon')
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/correspondence')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice-info')
<div class="col-md-12 col-md-12 space-m-t-22 margin-b-20"><p class="alert alert-success hide" id="mail_success_alert">Email sent successfully</p></div>
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'yes'])
@include ('patients/correspondence/tabs')
@stop

@section('practice')

@foreach($content as $content)
<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->


        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10"><!--  Left side Content Starts -->
            <div class="table-responsive">
                <table class="table-responsive table-striped-view table ">   

                    <tbody>
                        <tr class="bg-white">
                            <td><span  class="font600">To : </span> <span class="med-gray-dark">{{ @$content->email_id }}</span></td>                                                
                            <td><span  class="med-green font600">From : </span> <span class="med-gray-dark">{{ @$content->creator->name }}</span></td>                                                
                            <td><span class="med-green font600">Subject : </span> <span class="med-gray-dark">{{ @$content->subject }}</span></td>                 
                            <td><span class="med-green font600">Sent Date : </span> <span class="med-gray-dark">{{ App\Http\Helpers\Helpers::timezone(@$content->created_at, 'm/d/y') }}</span></td>
                        </tr>
                       

                    </tbody>
                </table>


            </div>
            <div class="box box-view no-shadow margin-t-m-13"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">Content</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">

                    <p>
                        {!! @$content->message !!}
                    </p>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->
    </div>
</div>
@endforeach
@stop 