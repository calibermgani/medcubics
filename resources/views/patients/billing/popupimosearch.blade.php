<div class="form-group-billing margin-b-5">                            
    {!! Form::label('icd6', 'Search ICD from IMO',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label']) !!} 
    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-10">
        {!! Form::text('imosearch',null,['class'=>'form-control input-sm-modal-billing']) !!}
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
        <i class="fa {{Config::get('cssconfigs.common.info')}} med-green form-icon-billing"></i>
    </div>
</div>