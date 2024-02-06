<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
    <div class="box box-info no-shadow">
        <div class="box-body form-horizontal">
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('type')) error @endif " style="float:left; width: 200px;">
                    {!! Form::label('practice', 'Select Practice', ['class'=>'control-label font600']) !!} 
                    {!! Form::select('practice_id', array('' => '-- Select --')+(array)$practicelist,null,['class'=>'select2 form-control js_select_user_id']) !!} 
                <div class="col-sm-1 col-xs-2"></div>
            </div> 
        </div><!-- /.box-body -->
		
    </div><!-- /.box -->
</div><!--/.col (left) -->