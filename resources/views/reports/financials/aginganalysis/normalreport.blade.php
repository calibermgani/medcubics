
<div class="box box-view no-shadow"><!--  Box Starts -->

    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>


<div class="box-body no-padding"><!-- Box Body Starts -->
    @if($headers !='' && !empty($headers))
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-b-20 med-orange" >
                <div class="margin-b-10">Aging Summary</div>
            </h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding text-center">
                    </script> 
<?php $i=1; ?>
                        @foreach($headers as $header_name => $header_val)
                            <span class="med-green">
                                <?php $hn = $header_name; ?>
                                {{ @$header_name }}</span> : {{ @$header_val}}@if($i<count((array)$headers)) | @endif </script> 
<?php $i++; ?> 
                        @endforeach
                </div>                
            </div>
        </div>
        @endif
        <style type="text/css">
            .table-bordered > thead > tr > th, .table-bordered > thead > tr > td{
                border-bottom-width: 0px !important;
            }
        </style>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-info no-shadow no-border no-bottom">
            <div class="box-body margin-t-10">
                <div class="table-responsive monitor-scroll">
                    @if(isset($aging_report_list) && !empty($aging_report_list) && count($aging_report_list->total)>1)
                        <table class="table table-bordered table-striped dataTable">
                            <thead>
                                <tr>
                                    <?php $count_r = 1; ?> 
                                    @foreach($header as $header_name => $header_val)

                                    @if($count_r ==1)
                                    <th style="cursor:default;" class="text-right">{{ @$header_val }}</th>
                                    @elseif($count_r % 2 == 0)
                                    <th style="cursor:default;" colspan="2" class="text-center">{{ @$header_val }}</th>
                                    @endif
                                    <?php $count_r++; ?>
                                    @endforeach
                                </tr>
                            </thead>                
                            <tbody>
                                @if(isset($aging_report_list) && !empty($aging_report_list) && count($aging_report_list->total)>1)
                                    <tr style="cursor:default;" role="row">                     
                                        @foreach($aging_report_list->name as $key => $name)    
                                                <!-- This condition added for displaying price format-->
                                            @if(@$name=='Claims')
                                                <td class="text-left font600" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green">{!! @$name !!}</span></td>
                                            @else
                                            @if($key > 0)
                                                <td class="text-right font600" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green">{!! @$name !!}($)</span></td>
                                            @else
                                                <td class="text-right" >{!! @$name !!}</td>
                                            @endif
                                            @endif                                  
                                        @endforeach
                                    </tr>
                                    <tr style="cursor:default;" role="row">  
                                    @if(isset($aging_report_list->patient))                
                                        @foreach($aging_report_list->patient as $key => $val)    
                                                <!-- This condition added for displaying price format-->
                                            @if(@$key==0)
                                                <td class="text-left font600" style="border-right: 1px solid #CDF7FC"><span class="med-green">{!! @$val !!}</span></td>
                                            @elseif(@$key%2==0)
                                                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$val) !!}</td>
                                            @else
                                                <td class="text-left">{!! @$val !!}</td>
                                            @endif                                  
                                        @endforeach
                                    @endif
                                    </tr>
                                    <?php 
                                    $insurance_provider = array_except((array)$aging_report_list,['name','patient','total','total_percentage']);
                                    ?>
                                    @if(isset($insurance_provider) && !empty($insurance_provider))             
                                        @foreach($insurance_provider as $list)  
                                        <tr style="cursor:default;" role="row">        
                                            @foreach($list as $key => $val)  
                                                <!-- This condition added for displaying price format-->
                                            @if(@$key==0)
                                                <td class="font600 text-left" style="border-right: 1px solid #CDF7FC"><span class="med-green">{!! @$val !!}</span></td>
                                            @elseif(@$key%2==0)
                                                <td class="text-right" style="border-right: 1px solid #CDF7FC">{!! App\Http\Helpers\Helpers::priceFormat(@$val) !!}</td>
                                            @else
                                                <td class="text-left">{!! @$val !!}</td>
                                            @endif    
                                            @endforeach                              
                                        </tr>
                                        @endforeach
                                    @endif
                                    <tr style="cursor:default;" role="row">                     
                                        @foreach($aging_report_list->total as $key => $val)    
                                                <!-- This condition added for displaying price format-->
                                            @if(@$key==0)
                                                <td class="font600 med-orange text-left" style="border-right: 1px solid #CDF7FC">{!! @$val !!}</td>
                                            @elseif(@$key%2==0)
                                                <td class="font600 med-orange text-right" style="border-right: 1px solid #CDF7FC">{!! App\Http\Helpers\Helpers::priceFormat(@$val) !!}</td>
                                            @else
                                                <td class="font600 med-orange text-left">{!! @$val !!}</td>
                                            @endif                                  
                                        @endforeach
                                    </tr>
                                    <tr style="cursor:default;" role="row">                     
                                        @foreach($aging_report_list->total_percentage as $key => $val)    
                                                <!-- This condition added for displaying price format-->
                                            @if(@$key==0)
                                                <td class="font600 med-orange text-left" style="border-right: 1px solid #CDF7FC">{!! @$val !!}</td>
                                            @elseif(@$key%2==0)
                                                <td class="font600 med-orange text-right" style="border-right: 1px solid #CDF7FC">{!! @$val !!}</td>
                                            @else
                                                <td class="font600 med-orange text-left">{!! @$val !!}</td>
                                            @endif                                  
                                        @endforeach
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    @else
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
                    @endif
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.box -->
    </div>
</div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->