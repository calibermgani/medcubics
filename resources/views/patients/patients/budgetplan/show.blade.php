@extends('admin')


@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-file-text-o font14"></i> Budget Plan </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($patient_id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <?php $uniquepatientid = $patient_id; ?>
            

            @include ('patients/layouts/swith_patien_icon')	

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

            <li><a href="#js-help-modal" data-url="{{url('help/budgetplan')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop


@section('practice-info')
<?php 
$activetab = 'budget plan';
 ?>
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'yes'])

@stop

@section('practice')

<?php  $plantype = ['Weekly'=>'Weekly','Biweekly'=>'Bi-Weekly','Monthly'=>'Monthly','Bimonthly'=>'Bi-Monthly'];  ?>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10 margin-b-10">
    @if($checkpermission->check_url_permission('patients/{id}/budgetplan/{budgetplan}/edit') == 1) 
    <a href="{{ url('patients/'.$patient_id.'/budgetplan/'.$patientBudget_id.'/edit') }}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
    @endif
</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >

    <div class="box box-info no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="fa fa-file-text-o font14"></i>  <h3 class="box-title">Budget Plan</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->

        <div class="box-body  form-horizontal margin-l-10">
            <div class="form-group">
                {!! Form::label('Patient Balance', 'Patient Balance', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-6 control-label']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 med-orange font600">
                    <p class="show-border no-bottom">{!! App\Http\Helpers\Helpers::priceFormat($get_patientbalance) !!}</p>
                </div>
            </div>  
			
			<div class="form-group">
                {!! Form::label('Budget Total', 'Budget Total', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-6 control-label']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 med-orange font600">
                    <p class="show-border no-bottom">{{ $patientBudget->budget_total }}</p>
                </div>
            </div>  
            <div class="form-group">
                {!! Form::label('Budget Plan', 'Budget Plan', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">
                    <p class="show-border no-bottom">{{ $plantype[$patientBudget->plan] }}</p>
                </div>
            </div>  
            <div class="form-group">
                {!! Form::label('Budget Amount', 'Budget Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">
                    <p class="show-border no-bottom">{{ $patientBudget->budget_amt }}</p>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('Statement Start Date', 'Statement Start Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">
                    <p class="show-border no-bottom">{{ $patientBudget->statement_start_date }}</p>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('Budget Balance', 'Budget Balance', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">
                    <p class="show-border no-bottom">{!! App\Http\Helpers\Helpers::priceFormat(App\Http\Helpers\Helpers::getPatientBudgetTotalBalence($patientBudget->patient_id)) !!}</p>
                </div>
            </div>
            <?php 
							$Pass_budget = ['balance'=>$get_patientbalance,'plan'=>$patientBudget->plan,'amount'=>$patientBudget->budget_amt,'start_date'=>$patientBudget->statement_start_date ];
							$getbudgetperiod = App\Http\Helpers\Helpers::getPatientBudgetPeriod($Pass_budget);
							?>
            <div class="form-group hide">
                {!! Form::label('Budget Period', 'Budget Period', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">
                    <p class="show-border no-bottom">{{ @$getbudgetperiod['budget_period'] }} ({{ @$getbudgetperiod['budget_date'] }})</p>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('Last Statement Sent Date', 'Last Statement Sent Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">
                    <p class="show-border no-bottom">{{ ($patientBudget->last_statement_sent_date=='0000-00-00' || $patientBudget->last_statement_sent_date==Null || $patientBudget->last_statement_sent_date=='01/01/70' )?'-':App\Http\Helpers\Helpers::dateFormat($patientBudget->last_statement_sent_date,'date') }}</p>
                </div>
            </div>  
			<div class="form-group">
                {!! Form::label('Status', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10">
                    <p class="show-border no-bottom">{{ $patientBudget->status }}</p>
                </div>
            </div>


        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!--/.col (left) -->
@stop            