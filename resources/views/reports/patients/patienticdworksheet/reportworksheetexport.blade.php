<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - ICD Worksheet</title>
        <style>
            table tbody tr  td{
                font-size: 9px !important;
                border: none !important;
            }
            table tbody tr th {
                text-align:center !important;
                font-size:10px !important;                
                color:#000 !important;
                border:none !important;
                border-radius: 0px !important;
            }
            table thead tr th{border-bottom: 5px solid #000 !important;font-size:10px !important}
            .text-right{text-align: right;}
            .text-left{text-align: left;}
            .text-center{text-align: center;}
            .med-green{color: #00877f;}
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head>	
    <body>
        <?php 
        @$icd_result = $result['icd_result'];
        @$start_date = $result['start_date'];
        @$end_date = $result['end_date'];
        @$createdBy = $result['createdBy'];
        @$practice_id = $result['practice_id'];
        @$search_by = $result['search_by'];
        @$heading_name = App\Models\Practice::getPracticeDetails(); @$icd_val = array(); ?>
        @foreach($icd_result as $list)
        <?php $icd_values = App\Models\Icd::getIcdValues(@$list->patient_icd); @$icd_val[]=count($icd_values); ?>	
        @endforeach
        <?php @$maxval=max($icd_val); ?>
        <table>
            <tr>                   
                <td colspan="6" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>                    
            </tr>
            <tr>
                <td colspan="6" style="text-align:center;">ICD Worksheet</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align:center;"><span>User :</span><span>@if(Auth::check() && isset(Auth::user()->name)) {{ Auth::user()->name }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span>{!! $key !!} :  </span>{{ @$val[0] }}
                        <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>         			
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOB</th>        
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">SSN</th>        
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payer</th>   		
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">ICD10</th> 
                </tr>
            </thead>
            <tbody>
                @if(count($icd_result) > 0)  
                <?php @$total_adj = 0; @$patient_total = 0; @$insurance_total = 0; ?>
                @foreach($icd_result as $list)
                <?php 
                    $icd_values = App\Models\Icd::getIcdValues(@$list->patient_icd); @$icd_val=count($icd_values); 
                    $name=App\Http\Helpers\Helpers::getNameformat(@$list->last_name,@$list->first_name,@$list->middle_name);
                ?>
                <tr>
                    <td>{!! @$list->account_no !!}</td>
                    <td>{!! @$name !!}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$list->dob,'dob') }}</td>
                    <td style="text-align:left;width:50px;">@if(@$list->ssn != ''){{@$list->ssn}} @else -Nil- @endif</td>
                    <?php /* SP  */
                        if(isset($list->short_name) && $list->short_name != ''){
                    ?>
                    <td>{!! @$list->short_name !!}</td>
                    <?php 
                        } else {
                    ?>
                    <td>
                        @if(isset($list->patient_insurance[0]))
                            {{ (!empty($list->patient_insurance[0]->insurance_details->short_name))?$list->patient_insurance[0]->insurance_details->insurance_name:'-Nil-'}}
                        @else
                            Self
                        @endif
                    </td>
                    <?php } 
                        @$icd_values = App\Models\Icd::getIcdValues(@$list->patient_icd);
                    ?>
                    <td style="text-align: left;width:50px;"> {{implode(', ', $icd_values)}} </td>	
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        <td colspan="6">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>