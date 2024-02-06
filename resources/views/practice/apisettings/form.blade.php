<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.apisettings") }}' />
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" ><!-- Col Starts -->
    <div class="box box-info no-shadow"><!-- Box General Information Starts -->
        <div class="box-block-header with-border">
            <h3 class="box-title"><i class="fa {{Config::get('cssconfigs.Practicesmaster.api')}} font14"></i> API Settings</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->
        {!! Form::hidden('removed_api') !!}

        @if(count($practiceApiList)>0) 
        <div class="box-body  form-horizontal"><!-- Box Body Starts -->
            <div class="col-lg-10 col-md-12 col-sm-12">
                {!! Form::label('API List','API List', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                    @foreach($practiceApiList as $keys=>$api_value) 

                    @if(in_array(@$apilist_arr[$api_value->api_id]['api_name'],$maincat_api))
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-bottom">
                        <ul style="list-style-type: none;line-height: 15px;margin-bottom:10px;">
                            <li>
                                {!! Form::checkbox('practice_api[]', @$api_value->api_id,(@$api_value->status=='Active')?true:false, ["class" => "js_api_check","data-api"=>App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$api_value->api_id,'encode'),'id'=>$keys]) !!}&nbsp;
                                {!! Form::label($keys,ucwords(@$apilist_arr[$api_value->api_id]['category']),ucwords(str_replace(@$apilist_arr[$api_value->api_id]['api_name'],' ',str_replace('_',' ',@$apilist_arr[$api_value->api_id]['category']))), ['class'=>'control-label font600']) !!} &emsp;&emsp;	
                            </li>
                        </ul>

                    </div>
                    @else
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                        <ul style="list-style-type: none;">
                            <li>
                                {!! Form::checkbox('practice_api[]', $api_value->api_id,($api_value->status=='Active')?true:false, ["class" => "js_api_check","data-api"=>App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($api_value->api_id,'encode'),'id'=>$keys]) !!}&nbsp;
                                {!! Form::label($keys,ucwords(@$apilist_arr[$api_value->api_id]['category']),ucwords(str_replace('_',' ',@$apilist_arr[$api_value->api_id]['category'])), ['class'=>'control-label font600']) !!} &emsp;&emsp;
                            </li>
                        </ul>
                    </div>
                    @endif	

                    @endforeach	
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                {!! Form::button('Update', ['name'=>'sample','class'=>'btn btn-medcubics js_updateapisettings']) !!}
            </div>

        </div>
        
        @else
        <div class="box-body"><!-- Box Body Starts -->
            <p class="med-gray text-center no-bottom">No API settings available</p>
        </div>
        @endif
    </div><!-- Box General Information Ends -->
</div><!--/.col ends -->