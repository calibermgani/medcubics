{!! Form::open(['url'=>$type.'/search','id'=>'js_common_search_form','name'=>'search_form']) !!}
<div id="js_claim_search_option">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
        <div class="no-shadow form-horizontal">
			<div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
				<div class="form-group-billing">
					{!! Form::label('Code', 'Code',['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
					{!! Form::text('code',null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>
				</div>
			</div>
			<div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
				<div class="form-group-billing">
					{!! Form::label('name', 'Name',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
					{!! Form::text('name',null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
				<div class="form-group-billing">
					{!! Form::label('description', 'Description',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
					{!! Form::text('description',null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">            
				<div class="form-group-billing">
					{!! Form::label('status', 'Status',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-6 col-md-8 col-sm-8 col-xs-10 select2-white-popup">
					{!! Form::select('status', array(''=>'-- Select --','Active'=>'Active','Inactive'=>'Inactive'), null,['class'=>'form-control input-view-border1 select2']) !!}
					</div>
				</div>
			</div>
			 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right p-r-0">
                <input class="btn btn-medcubics-small" value="Search" type="submit">
                {!! Form::reset('Reset',["class"=>"btn btn-medcubics-small js_search_reset"]) !!}
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}