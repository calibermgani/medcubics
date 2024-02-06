<div class="table-responsive">
    @if(!empty($claims) && $type == "charges")
        <table id="example1" class="table table-bordered table-striped ">         
            <thead>     
                <tr>
                    <th>Dos</th>
                    <th>Created Date</th>
                    <th>Claim No</th>
                    <th>Change Date</th>                                   
                </tr>
            </thead>
            <tbody>
                @foreach($claims as $claim)
                <?php $charge_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id,'encode'); ?>     
                    <tr>
                    <td>{!!date("d/m/Y",strtotime($claim->date_of_service))!!}</td>
                    <td>{!!date("d/m/Y",strtotime($claim->created_at))!!}</td>
                    <td>{!!$claim->claim_number!!}</td>
                    <td> <div class="col-lg-4 col-md-4 col-sm-3 col-xs-5 ">
                    	<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('admit_date')"></i>  
                        {!! Form::text('change_date',date('m/d/Y',strtotime($claim->created_at)),['class'=>'form-control dm-date input-sm-header-billing dm-date p-r-0 js-change-date', 'data-type' => "charges", 'data-id' => $charge_id]) !!}
                        </div>       
                    </td>
                </tr>
                @endforeach      
            </tbody>
        </table>
    @else
        <table id="example1" class="table table-bordered table-striped ">         
            <thead>
                <tr>
                    <th>PaymentID</th>
                    <th>Created Date</th>
                    <th>Check Date</th>
                    <th>Change Date</th>                                   
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <?php $payment_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment->id,'encode'); ?>
                <tr>
                    <td>{!!$payment->paymentnumber!!}</td>
                    <td>{!!date('m/d/Y', strtotime($payment->created_at))!!}</td>
                    <td>{!!(isset($payment->check_date) && $payment->check_date != '1970-01-01' && $payment->check_date != '0000-00-00')?@date('m/d/Y',strtotime($payment->check_date)):''!!}</td>
                    <td> <div class="col-lg-4 col-md-4 col-sm-3 col-xs-5 ">
                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('admit_date')"></i>  
                     {!! Form::text('change_date',date('m/d/Y',strtotime($payment->created_at)),['class'=>'form-control dm-date input-sm-header-billing dm-date p-r-0 js-change-date',  'data-type' => "payments", 'data-id' => $payment_id]) !!}  
                    </div>       
                </td>
            </tr>
                @endforeach      
            </tbody>
        </table>
    @endif

</div>                                