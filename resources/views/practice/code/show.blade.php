@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Remittance Codes <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <?php $code->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($code->id,'encode'); ?>
        <ol class="breadcrumb">
            <li><a href="{{ url('code') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="javascript:void(0);" data-url="{{url('help/codes')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-15 margin-b-10">
    @if($checkpermission->check_url_permission('code/{code}/edit') == 1)
    <a href="{{ url('code/'.$code->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif	
</div>

<div class="col-md-12 col-md-12 col-sm-12 col-xs-12" >
    <div class="box box-info no-shadow">
        <div class="box-block-header margin-b-10">
            <h3 class="box-title"><i class="livicon" data-name="code" data-color='#008e97' data-size='16'></i> Remittance Code Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body  form-horizontal margin-t-10 margin-l-10">
            <div class="form-group">
                {!! Form::label('CodeCategory', 'Code Category', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$code->codecategories->codecategory }}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('TransactionCode', 'Transaction Code', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$code->transactioncode_id }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">
                {!! Form::label('Description', 'Description', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$code->description}}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">
                {!! Form::label('start date', 'Start Date', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">@if($code->start_date!='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat($code->start_date,'date') }} @endif</p>
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group">
                {!! Form::label('last modified date', 'Last Modified Date', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">@if($code->last_modified_date!='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat($code->last_modified_date,'date') }}@endif</p>
                </div>
                <div class="col-sm-1"></div>
            </div>           
            
            <div class="form-group">
                {!! Form::label('created by', 'Created By', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::shortname($code->created_by) }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>
            
            <div class="form-group">
                {!! Form::label('created on', 'Created On', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::dateFormat($code->created_at,'date') }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>
            
            @if(@$code->userupdate->name !='')
            <div class="form-group">
                {!! Form::label('Updated by', 'Updated By', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$code->userupdate->name }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>
            @endif
            
            @if($code->updated_at !='' && $code->updated_at !='-0001-11-30 00:00:00' && $code->updated_at !='0000-00-00 00:00:00')
            <div class="form-group">
                {!! Form::label('Updated on', 'Updated On', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">@if($code->updated_at !='' && $code->updated_at !='-0001-11-30 00:00:00' && $code->updated_at !='0000-00-00 00:00:00') {{ App\Http\Helpers\Helpers::dateFormat($code->updated_at,'date')}}@endif</p>
                </div>
                <div class="col-sm-1"></div>
            </div>
            @endif

            <div class="form-group">
                {!! Form::label('status', 'Status',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label']) !!}
                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-6 @if($errors->first('status')) error @endif">
                 @if($code->status == 'Active')  
					{!! Form::radio('status', 'Active',true,['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','disabled']) !!} Inactive
				@else	
                    {!! Form::radio('status', 'Active',null,['class'=>'flat-red','disabled']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',true,['class'=>'flat-red']) !!} Inactive
				@endif	
                    {!! $errors->first('status', '<p> :message</p>')  !!}
                </div>						
            </div>
        </div><!-- /.box-body -->									

    </div><!-- /.box -->
</div><!--/.col (left) -->

<!--End-->
@stop 