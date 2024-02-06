{!! Form::open(['url'=>'practicefacilityschedulerlist','id'=>'js_common_search_form','name'=>'search_form']) !!}
<div id="js_claim_search_option">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
        <div class="no-shadow form-horizontal">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0 ">
				<div class="form-group-billing">
					{!! Form::label('Short_name_label', 'Short Name',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::text('sch_shortname',null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>
				</div>
				<div class="form-group-billing">
					{!! Form::label('facility_label', 'Facility',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::text('sch_facility',null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0 ">
				<div class="form-group-billing">
					{!! Form::label('specialty_label', 'Specialty',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::select('sch_speciality', array(''=>'-- Select --')+(array)@$all_speciality, null,['class'=>'form-control input-view-border1 select2','id'=>'sch_speciality']) !!}
					</div>
				</div>
				<div class="form-group-billing">
					{!! Form::label('pos_label', 'POS',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
					<div class="col-lg-7 col-md-7 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::select('sch_pos', array(''=>'-- Select --')+(array)@$all_pos, null,['class'=>'form-control input-view-border1 select2','id'=>'sch_pos']) !!}
					</div>
				</div>
			</div>
            
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0">
            
			<div class="form-group-billing">
				{!! Form::label('Scheduled_label', 'Scheduled',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
				<div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
					{!! Form::select('sch_scheduled', array(''=>'-- Select --','Yes'=>'Yes','No'=>'No'), null, ['id' => 'sch_scheduled','class'=>'select2 form-control']) !!}
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