<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->		

    <div class="box-header-view">
        <i class="fa fa-user" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>


    <div class="box-body  bg-white"><!-- Box Body Starts -->


        
        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-b-20 med-orange">Patient - Itemized Bill</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-0 text-center">
               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-0 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">
                        </script> 
<?php $i=1; ?>
                        @if(isset($header) && !empty($header))
                        @foreach($header as $header_name => $header_val)
                            <span class="med-green">
                                <?php $hn = $header_name; ?>
                                {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i<count((array)$header)) | @endif </script> 
<?php $i++; ?> 
                        @endforeach
                        @endif
                    </div>                    
                </div>
              
            </div>
        </div>
        
        @if(count($patient) > 0)
        <div class="table-responsive col-lg-12">
            @foreach($patient as $r)
                <table class="table table-striped table-bordered">
                    <tbody>
                <?php
                $ins_overpayment = App\Models\Payments\ClaimInfoV1::InsuranceOverPayment($r['claim']['id']);
                $insurance_refund =  App\Models\Payments\ClaimInfoV1::getRefund($r['claim']['id'], 'insurance_paid_amt');
                $patient_refund = @App\Models\Payments\ClaimInfoV1::getRefund($r['claim']['id'], 'patient_paid_amt');
                ?>
                <tr>
                    <td style="border-top: 1px solid #00837C !important">Acc No</td>
                    <td colspan="7" style="border-top: 1px solid #00837C !important">{!! $r['claim']['account_no'] !!}</td>
                </tr>
                <tr>
                    <td>Patient Name</td>
                    <td colspan="7">
                        <?php
                        $patient_name = App\Http\Helpers\Helpers::getNameformat(@$r['claim']['last_name'], @$r['claim']['first_name'], @$r['claim']['middle_name']);
                        $age = App\Http\Helpers\Helpers::dob_age($r['claim']['dob']);
                        echo $patient_name." (".$r['claim']['dob'],", ".$age." - ".$r['claim']['gender'].")";
                    ?>
                    </td>
                </tr>
                <tr>
                    <td>SSN</td>
                    <td colspan="7">{{ !empty($r['claim']['ssn'])?$r['claim']['ssn']:'Nil' }}</td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td colspan="7">{{ $r['claim']['address1'] }}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>{{ $r['claim']['status'] }}</td>
                    <td>Billed To</td>
                    <td>
                        @if(!empty($r['claim']['insurance_id']))
                            {{ \App\Http\Helpers\Helpers::getInsuranceName($r['claim']['insurance_id']) }} - {{ \App\Http\Helpers\Helpers::getInsuranceFullName($r['claim']['insurance_id']) }}
                        @else
                            Patient
                        @endif
                    </td>
                    <td>Total Charge</td>
                    <td colspan="3">{!!App\Http\Helpers\Helpers::priceFormat($r['claim']['total_charge'])!!}</td>
                </tr>
                <tr>
                    <td>DOS</td>
                    <td>{{App\Http\Helpers\Helpers::dateFormat($r['claim']['date_of_service'],'dob')}}</td>
                    <td>Rendering Provider</td>
                    <td>{{ @$r['claim']['rendering_short_name']}} - {{ @$r['claim']['rendering_name'] }}</td>
                    <td>Ins Overpayment</td>
                    <td colspan="3">{!!App\Http\Helpers\Helpers::priceFormat($ins_overpayment)!!}</td>
                </tr>
                <tr>
                    <td>Claim No</td>
                    <td>{{ $r['claim']['claim_number'] }}</td>
                    <td>Billing Provider</td>
                    <td>{{ @$r['claim']['billing_short_name']}} - {{ @$r['claim']['billing_name'] }}</td>
                    <td>Insurance Refund</td>
                    <td colspan="3">{!!App\Http\Helpers\Helpers::priceFormat($insurance_refund)!!}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Facility</td>
                    <td>{{ @$r['claim']['facility_short_name']}} - {{ @$r['claim']['facility_name'] }}</td>
                    <td>Patient Refund</td>
                    <td colspan="3">{!!App\Http\Helpers\Helpers::priceFormat($patient_refund)!!}</td>
                </tr>
                @foreach($r['CPT'] as $key=>$cpt)
                    <tr>
                        <td>CPT</td>
                        <td colspan="3">
                            <?php 
                                $code = \DB::table('claim_cpt_info_v1')->select('cpt_code','charge')->where('id',$key)->first(); 
                                $icd = \DB::table('icd_10')->selectRaw('group_concat(icd_code) as icd')->whereRaw("id in (".$r['claim']['icd_codes'].")")->first(); 
                                echo $code->cpt_code." (".$icd->icd.")"; 
                                ?>
                        </td>
                        <td>CPT Amount</td>
                        <td>{!!App\Http\Helpers\Helpers::priceFormat($code->charge)!!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <th style="text-align: left !important;">Trans Date</th>
                        <th style="text-align: left !important;">Responsibility</th>
                        <th style="text-align: left !important;">Description</th>
                        <th style="text-align: left !important;">Payment Type</th>
                        <th style="text-align: right !important;">Payments</th>
                        <th style="text-align: right !important;">Adj</th>
                        <th style="text-align: right !important;">Pat Bal</th>
                        <th style="text-align: right !important;">Ins Bal</th>
                    </tr>
                    @foreach($cpt as $c)
                    <tr>
                        <td>{{App\Http\Helpers\Helpers::dateFormat($c['claim_cpt_created_at'])}}</td>
                        <td>
                        @if($c['claim_cpt_responsibility']==0)
                            Patient
                        @else
                            {{ \App\Http\Helpers\Helpers::getInsuranceName($c['claim_cpt_responsibility']) }}
                            @if(isset($c['respCat']) && $c['respCat'] != '')
                                <span class="{{@$c['resp_bg_class']}}">{{ substr(@$c['respCat'], 0, 1) }}</span>
                            @endif
                        @endif
                        </td>
                        <td>{!! nl2br($c['desc']) !!}</td>
                        <td>{{$c['pmt_type']}}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($c['pmts'])!!}</td>
                        <td class="text-right">
                                    {!!App\Http\Helpers\Helpers::priceFormat($c['adj'])!!}
                        </td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($c['claim_cpt_pat_bal'])!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($c['claim_cpt_ins_bal'])!!}</td>
                    </tr>
                    @endforeach
                @endforeach
                </tbody>
                </table>    
            @endforeach
        </div>
         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-10 no-padding dataTables_info">
                Showing {{@$pagination['from']}} to {{@$pagination['to']}} of {{@$pagination['total']}} entries
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding margin-t-m-10">{!! $pagination['pagination_prt'] !!}</div>
        </div>
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->
