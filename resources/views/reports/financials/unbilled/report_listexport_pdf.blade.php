<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important; padding: 10px;
            }
            .summary-table table tbody tr:nth-of-type(odd) td{
                border-bottom: 1px solid #d7f4f2; border-top: 1px solid #d7f4f2;
            }
            th {
                text-align:left !important;
                font-size:10px !important;
                font-weight: 600 !important;
                border-bottom: 1px solid #ccc;
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 13px !important;}
            .table-summary tbody tr{line-height: 22px !important;} 
            .table-summary tbody tr td{font-size:11px !important;} 
            @page { margin: 100px -20px 100px 0px; }
            body { 
                margin:0;
                padding-top:30px;
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right !important;padding-right:5px;}
            .text-left{text-align: left;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .text-center{text-align: center;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
            .box-border{border: 1px solid #ccc !important;border-top: 0px solid #fff !important;}
            .box-border:first-child{border-top: 1px solid #ccc !important;}
            .new-border:first-child{border-bottom: 1px solid #ccc !important;}
            .med-red {color: #ff0000 !important;}
        </style>
    </head>	
    <body>
        <?php 
            $unbilled_claim_details = $result['unbilled_claim_details'];
            $total_charges = $result['total_charges'];            
            $search_by  = $result['search_by']; 
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
            
            foreach ($unbilled_claim_details as $key => $value) {
                    $abb_billing[] = @$value->billing_provider->short_name." - ".@$value->billing_provider->provider_name;
                    $abb_rendering[] = @$value->rendering_provider->short_name." - ".@$value->rendering_provider->provider_name;
                    $abb_facility[] = @$value->facility->short_name." - ".@$value->facility->facility_name;
                    $abb_insurance[] = @$value->insurance_details->short_name." - ".@$value->insurance_details->insurance_name;
                    $abb_user[] = @$value->user->short_name." - ".@$value->user->name;
                }
                $abb_billing = array_unique($abb_billing);
                foreach (array_keys($abb_billing, ' - ') as $key) {
                    unset($abb_billing[$key]);
                }
                $abb_rendering = array_unique($abb_rendering);
                foreach (array_keys($abb_rendering, ' - ') as $key) {
                    unset($abb_rendering[$key]);
                }
                $abb_facility = array_unique($abb_facility);
                foreach (array_keys($abb_facility, ' - ') as $key) {
                    unset($abb_facility[$key]);
                }
                $abb_insurance = array_unique($abb_insurance);
                foreach (array_keys($abb_insurance, ' - ') as $key) {
                    unset($abb_insurance[$key]);
                }
                $abb_user = array_unique($abb_user);
                foreach (array_keys($abb_user, ' - ') as $key) {
                    unset($abb_user[$key]);
                }
                $user_imp = implode(':', $abb_user);
                $billing_imp = implode(':', $abb_billing);
                $rendering_imp = implode(':', $abb_rendering);
                $insurance_imp = implode(':', $abb_insurance);
                $facility_imp = implode(':', $abb_facility);
         ?>        
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Unbilled Claims Analysis</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 0; ?>
                            @foreach($search_by as $key=>$val)
                            @if($i > 0){{' | '}}@endif
                                <span>{!! $key !!}: </span>{{ @$val }}                           
                                <?php $i++; ?>
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="6" style="border:none"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="5" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>              
            </table>
        </div>        
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="">
            <div>
                <div>
                    <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                        <thead>
                            <tr>
                                <th>Acc No</th>
                                <th>Patient Name</th>
                                <th>DOS</th>  
                                <th>Claim No</th>
                                <th>Payer</th>
                                <th>Facility</th>
                                <th>Rendering </th>
                                <th>Billing </th>  
                                <th>Created Date</th>  		
                                <th>Days Since Created</th>  		
                                <th class="text-right">Charges($)</th>  		
                            </tr>
                        </thead>
                        <tbody>
                            <?php $grand_total = 0; ?>
                            @if(!empty($unbilled_claim_details))
                            @foreach($unbilled_claim_details as $lists)
                            <tr>
                                <td>{!! @$lists->patient->account_no !!}</td>	{{--*/$name=App\Http\Helpers\Helpers::getNameformat(@$lists->patient->last_name,@$lists->patient->first_name,@$lists->patient->middle_name);
								/*--}}
                                <td>{!! @$name !!}</td>
                                <td>{!! App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$lists->date_of_service, '','-')  !!}</td>
                                <td>{!! @$lists->claim_number !!}</td>		
                                <?php $insurance_name = App\Http\Helpers\Helpers::getInsuranceName(@$lists->insurance_id) ?>
                                <td>{!! $insurance_name !!}</td>
                                <td>{{ @$lists->facility->short_name }}</td>
                                <td>{!! @$lists->rendering_provider->short_name !!}</td>					
                                <td>{!! @$lists->billing_provider->short_name !!}</td>
                                <td>{{ App\Http\Helpers\Helpers::timezone(@$lists->created_at, 'm/d/y') }}</td>
                                <td>{!!  App\Http\Helpers\Helpers::daysSinceCreatedCount(date('Y-m-d',strtotime(@$lists->created_at))) !!}</td>
                                <td class="text-right" data-format="0.00">{!! @$lists->total_charge !!}</td>
                                <?php $grand_total = $grand_total + $lists->total_charge; ?>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>		
                </div>
                @else
                <div><h5>No Records Found !!</h5></div>
                @endif
            </div>
            <ul style="line-height:20px;">
                <li>{{$user_imp}}</li>
                <li>{{$billing_imp}}</li>
                <li>{{$rendering_imp}}</li>
                <li>{{$insurance_imp}}</li>
                <li>{{$facility_imp}}</li>
            </ul>
        </div>
    </body>
</html>