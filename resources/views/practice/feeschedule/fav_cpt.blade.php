<!DOCTYPE HTML>
<html lang="en">
    <head>
        <style type="text/css">
            .text-left{text-align: left !important;}
            .text-right{text-align: right;}
        </style>
    </head> 
    <body>
        <div>            
            <div>   
                <table>
                    <thead>
                        <tr>
                            @for ($i=0; $i < count($heading) ; $i++)
                            <th>{!! $heading[$i] !!}</th>
                            @endfor
                        </tr>
                    </thead>
                        <tbody>
                        @foreach($feeschedules as $list)
                        <tr>
                            <td style="text-align: left;">{!! @$list->cpt_hcpcs !!}</td>
                            <td class="text-right" data-format="0.00">{!! @$list->billed_amount !!}</td>
                            <td class="text-right" data-format="0.00">{!! @$list->allowed_amount !!}</td>
                            @if(count($heading) > 3)
                            <td style="text-align: left;">{!! @$list->modifier_1 !!}</td>
                            <td style="text-align: left;">{!! @$list->modifier_2 !!}</td>
                            <td style="text-align: left;">{!! @$list->modifier_3 !!}</td>
                            <td style="text-align: left;">{!! @$list->modifier_4 !!}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>                            
            </div>
        </div>
    </body>
</html>