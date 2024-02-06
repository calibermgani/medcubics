{!! Form::open(['url'=>'provider/search','id'=>'js_common_search_form','name'=>'search_form']) !!}
<div id="js_claim_search_option">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">
        <div class="no-shadow form-horizontal">
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
				<div class="form-group-billing">
					{!! Form::label('short name', 'Short Name',['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
					{!! Form::text('short_name',null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>
				</div>
				<div class="form-group-billing">
					{!! Form::label('Provider name', 'Prov Name',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10ol-xs-10">
					{!! Form::text('provider_name',null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
				<div class="form-group-billing">
					{!! Form::label('etin type', 'ETIN Type',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">
					{!! Form::select('etin_type', array(''=>'-- Select --','SSN'=>'SSN','TAX ID'=>'TAX ID'), null,['class'=>'form-control input-view-border1 select2']) !!}
					</div>
				</div>
				<div class="form-group-billing">
					{!! Form::label('Providertype', 'Type',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">
					{!! Form::select('provider_type[]', (array)$provider_type, null, ['class'=>'form-control select2 js_choose_header','multiple'=>'multiple','autocomplete'=>'off']) !!}
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
				<div class="form-group-billing">
					{!! Form::label('tax id', 'Tax ID/SSN',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
					{!! Form::text('tax_id',null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>
				</div>
				<div class="form-group-billing">
					{!! Form::label('npi', 'NPI',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
					{!! Form::text('npi',null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">            
				<div class="form-group-billing">
					{!! Form::label('specialty', 'Specialty',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
					{!! Form::text('speciality',null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>
				</div>
				<div class="form-group-billing">
					{!! Form::label('status', 'Status',  ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label']) !!} 
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
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