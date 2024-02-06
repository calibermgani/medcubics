<!DOCTYPE html>
<html lang="en">
    <head>
        <title>CPT-HCPCS - Favorites</title>
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
            @$cpt_favorites = $result['cpt_favorites'];
            @$practice_details = App\Models\Practice::getPracticeDetails();
            @$heading_name = $practice_details['practice_name'];
        ?>
        <table>
            <tr>                   
                <td colspan="7" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="7" style="text-align:center;">CPT/HCPCS - Favorites List</td>
            </tr>
            <tr>
                <td valign="center" colspan="7" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->short_name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td valign="top" colspan="14" style="text-align:center;">
                    @if($result['header'] !='' && count((array)$result['header']) > 0)
                    <?php $i = 1; ?>
                    @foreach($result['header'] as $header_name => $header_val)
                    <span>
                        <?php $hn = $header_name; ?>
                        {{ @$header_name }}
                    </span> : {{str_replace('-','/', @$header_val)}}
                    @if($i < count((array)$result['header'])) | @endif 
                    <?php $i++; ?>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">CPT / HCPCS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Short Description</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billed Amount($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Allowed Amount($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">POS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Modifier</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Type of Service</th>
                </tr>
            </thead>    
            <tbody>
                @foreach($cpt_favorites as $cpt_favorite)
                <?php
                    $pos = (isset($cpt_favorite->cpt->pos_id) && ($cpt_favorite->cpt->pos_id != 0)) ? $cpt_favorite->cpt->pos_id : 'Nil';
                    $modifier = (isset($cpt_favorite->cpt->modifier_id) && $cpt_favorite->cpt->modifier_id != 0 || $cpt_favorite->cpt->modifier_id != '') ? $cpt_favorite->cpt->modifier_id : 'Nil';
                ?>
                <tr>
                    <td class="text-left" style='text-align: left;'>{{ $cpt_favorite->cpt->cpt_hcpcs }}</td>
                    <td>{{ $cpt_favorite->cpt->short_description }}</td>
                    <td class="text-right <?php echo(@$cpt_favorite->cpt->billed_amount) < 0 ? 'med-red' : ''; ?>" data-format="#,##0.00">{!! @$cpt_favorite->cpt->billed_amount !!}</td>
                    <td class="text-right <?php echo(@$cpt_favorite->cpt->allowed_amount) < 0 ? 'med-red' : ''; ?>" data-format="#,##0.00">{!! @$cpt_favorite->cpt->allowed_amount !!}</td>
                    <td style="text-align: left;">{{ $pos }}</td>
                    <td style='text-align: left;'>{{ $modifier }}</td>
                    <td>{{ $cpt_favorite->cpt->type_of_service }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="7">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>