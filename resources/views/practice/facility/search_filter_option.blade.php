{!! Form::open(['url'=>'facility/searchfilter','id'=>'js_common_search_form','name'=>'search_form']) !!}
<div id="js_claim_search_option">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
        <div class="no-shadow form-horizontal">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0 ">
			
			<div class="form-group-billing">
                {!! Form::label('Short Name', 'Short Name',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10 select2-white-popup">
					{!! Form::text('short_name',null,['id'=>'short_name','class'=>'form-control']) !!}
                </div>
            </div>
			
			<div class="form-group-billing">
                {!! Form::label('Facility Name', 'Facility Name',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10 select2-white-popup">
					{!! Form::text('facility_name',null,['id'=>'facility_name','class'=>'form-control']) !!}
                </div>
            </div>
		  
          </div>
		   <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0 ">
			 <div class="form-group-billing">
                {!! Form::label('POS', 'POS',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10 select2-white-popup">
                    {!! Form::select('pos', array(''=>'-- Select --')+(array)$pos, null,['class'=>'form-control input-view-border1 select2','id'=>'billing_provider_id']) !!}
                </div>
            </div>
			<div class="form-group-billing">
                {!! Form::label('Specialty', 'Specialty',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10 select2-white-popup">
                    {!! Form::select('speciality', array(''=>'-- Select --')+(array)$speciality, null,['class'=>'form-control input-view-border1 select2','id'=>'rendering_provider_id']) !!}
                </div>
            </div>				
			
		   </div>
            
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0">
            <div class="form-group-billing">
				{!! Form::label('Status', 'Status',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
				<div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
					{!! Form::select('status', array(''=>'-- Select --','Active'=>'Active','Inactive'=>'Inactive'), null, ['id' => 'taxanomies-list','class'=>'select2 form-control']) !!}
				</div>
				<div class="col-sm-1 col-xs-2"></div>
			</div>
         
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right p-r-0">
                <input class="btn btn-medcubics-small" value="Search" type="submit">
                {!! Form::reset('Reset',["class"=>"btn btn-medcubics-small js_search_reset"]) !!}
            </div>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}