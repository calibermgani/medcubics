{!! Form::open(['url'=>'filterticket','id'=>'js_common_search_form','name'=>'search_form']) !!}
<div id="js_claim_search_option">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
        <div class="no-shadow form-horizontal">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 ">

                <div class="form-group-billing">
                    {!! Form::label('Ticket ID', 'Ticket ID',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 select2-white-popup">
                        {!! Form::text('ticket_id',null,['id'=>'ticket_id','class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 ">

                <div class="form-group-billing">
                    {!! Form::label('Title', 'Title',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 select2-white-popup">
                        {!! Form::text('title',null,['id'=>'title','class'=>'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0">
                <div class="form-group-billing">
                    {!! Form::label('Status', 'Status',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
                        {!! Form::select('status', array(''=>'-- Select --','Open'=>'Open','Closed'=>'Closed'), null, ['id' => 'taxanomies-list','class'=>'select2 form-control']) !!}
                    </div>                    
                </div>
            </div>

            <div class="col-lg-7 col-md-7 col-sm-8 col-xs-12 text-right">
                <input class="btn btn-medcubics-small" value="Search" type="submit">
                {!! Form::reset('Reset',["class"=>"btn btn-medcubics-small js_search_reset"]) !!}
            </div>

        </div>
    </div>
</div>
{!! Form::close() !!}